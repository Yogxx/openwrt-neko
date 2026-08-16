<?php

include './cfg.php';
$tmp_dir = $neko_www . "/lib";

// Anti-SSRF: hanya izinkan http/https ke host publik.
// Blokir loopback, private range, link-local (termasuk 169.254.169.254 metadata endpoint),
// dan gagal-aman kalau hostname tidak bisa di-resolve.
function is_url_ssrf_safe($url) {
    $parts = parse_url($url);
    if (!$parts || empty($parts['scheme']) || empty($parts['host'])) return false;

    $scheme = strtolower($parts['scheme']);
    if (!in_array($scheme, ['http', 'https'])) return false;

    $host = $parts['host'];

    // Resolve ke IP (dukung host yang sudah berupa IP maupun domain)
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        $ips = [$host];
    } else {
        $records = @dns_get_record($host, DNS_A + DNS_AAAA) ?: [];
        $ips = array_merge(array_column($records, 'ip'), array_column($records, 'ipv6'));
        $ips = array_values(array_filter($ips));
        if (empty($ips)) {
            $single = gethostbyname($host);
            if ($single !== $host) $ips = [$single];
        }
    }

    if (empty($ips)) return false; // gagal resolve -> tolak, jangan lanjut ke curl

    foreach ($ips as $ip) {
        $is_private = !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
        if ($is_private) return false;
    }

    return true;
}

if(isset($_POST['url'])) {
    $dt = $_POST['url'];

    if (!is_url_ssrf_safe($dt)) {
        echo "ERROR: URL not allowed (invalid scheme or points to a private/internal address)";
        exit;
    }

    if (strpos($dt, '/sub/') !== false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $dt);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // cegah redirect ke IP internal
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200 && !empty($content)) {
            $decoded = base64_decode($content);
            if ($decoded) {
                $configs = explode("\n", $decoded);
                $outcfg = "";

                foreach ($configs as $config) {
                    if (empty(trim($config))) continue;

                    $basebuff = parse_url($config);
                    if (!$basebuff || !isset($basebuff['scheme'])) continue;

                    $tmpfile = tempnam($tmp_dir, 'neko_');

                    if ($basebuff['scheme'] == "vmess") {
                        parseVmess($basebuff, $tmpfile);
                    } else if (in_array($basebuff['scheme'], ["vless", "trojan", "ss"])) {
                        parseUrl($basebuff, $tmpfile);
                    }

                    if (file_exists($tmpfile)) {
                        $outcfg .= file_get_contents($tmpfile) . "\n";
                        unlink($tmpfile);
                    }
                }

                echo $outcfg;
                exit;
            }
        }

        echo "ERROR: Cannot fetch subscription content";
        exit;
    }

    $basebuff = parse_url($dt);
    if (!$basebuff || !isset($basebuff['scheme'])) {
        echo "ERROR: Invalid URL format!";
        exit;
    }

    $tmpdata = tempnam($tmp_dir, 'neko_');
    $tmp     = $basebuff['scheme']."://";

    if ($basebuff['scheme'] == "vless")       parseUrl($basebuff, $tmpdata);
    else if ($basebuff['scheme'] == "vmess")  parseVmess($basebuff, $tmpdata);
    else if ($basebuff['scheme'] == "trojan") parseUrl($basebuff, $tmpdata);
    else if ($basebuff['scheme'] == "ss")     parseUrl($basebuff, $tmpdata);
    else {
        $errmsg = "ERROR, PLEASE CHECK YOUR URL!\ntrojan://...\nvless://...\nss://...\nvmess://...\nYOU ENTERED : $tmp";
        file_put_contents($tmpdata, $errmsg);
    }

    if (file_exists($tmpdata)) {
        $out = file_get_contents($tmpdata);
        unlink($tmpdata);
        echo $out;
    } else {
        echo "Error: Could not create output file";
    }
    exit;
}

function parseVmess($base, $tmpdata) {
    $decoded = base64_decode($base['host']);
    $urlparsed = array();
    $arrjs = json_decode($decoded, true);
    if (!empty($arrjs['v'])) {
        $urlparsed['cfgtype']     = isset($base['scheme'])    ? $base['scheme']    : '';
        $urlparsed['name']        = isset($arrjs['ps'])       ? $arrjs['ps']       : '';
        $urlparsed['host']        = isset($arrjs['add'])      ? $arrjs['add']      : '';
        $urlparsed['port']        = isset($arrjs['port'])     ? $arrjs['port']     : '';
        $urlparsed['uuid']        = isset($arrjs['id'])       ? $arrjs['id']       : '';
        $urlparsed['alterId']     = isset($arrjs['aid'])      ? $arrjs['aid']      : '';
        $urlparsed['type']        = isset($arrjs['net'])      ? $arrjs['net']      : '';
        $urlparsed['path']        = isset($arrjs['path'])     ? $arrjs['path']     : '';
        $urlparsed['security']    = isset($arrjs['type'])     ? $arrjs['type']     : '';
        $urlparsed['sni']         = isset($arrjs['host'])     ? $arrjs['host']     : '';
        $urlparsed['tls']         = isset($arrjs['tls'])      ? $arrjs['tls']      : '';
        $urlparsed['serviceName'] = isset($arrjs['path'])     ? $arrjs['path']     : '';
        printcfg($urlparsed, $tmpdata);
    } else {
        file_put_contents($tmpdata, "DECODING FAILED!\nPLEASE CHECK YOUR URL!");
    }
}

function parseUrl($basebuff, $tmpdata) {
    $urlparsed = array();
    $querybuff = array();
    $urlparsed['cfgtype']     = isset($basebuff['scheme'])   ? $basebuff['scheme']   : '';
    $urlparsed['name']        = isset($basebuff['fragment']) ? $basebuff['fragment'] : '';
    $urlparsed['host']        = isset($basebuff['host'])     ? $basebuff['host']     : '';
    $urlparsed['port']        = isset($basebuff['port'])     ? $basebuff['port']     : '';

    if ($urlparsed['cfgtype'] == "ss") {
        $urlparsed['uuid'] = isset($basebuff['user']) ? $basebuff['user'] : '';
        $basedata = explode(":", base64_decode($urlparsed['uuid']));
        $urlparsed['cipher'] = $basedata[0];
        $urlparsed['uuid']   = $basedata[1];
    } else {
        $urlparsed['uuid'] = isset($basebuff['user']) ? $basebuff['user'] : '';
    }

    if ($urlparsed['cfgtype'] == "ss") {
        $tmpbuff  = array();
        $tmpstr   = "";
        $tmpquery  = isset($basebuff['query']) ? $basebuff['query'] : '';
        $tmpquery2 = explode(";", $tmpquery);
        for ($x = 0; $x < count($tmpquery2); $x++) {
            $tmpstr .= $tmpquery2[$x] . "&";
        }
        parse_str($tmpstr, $querybuff);
        $urlparsed['mux']   = isset($querybuff['mux'])   ? $querybuff['mux']   : '';
        $urlparsed['host2'] = isset($querybuff['host2']) ? $querybuff['host2'] : '';
    } else {
        parse_str($basebuff['query'], $querybuff);
    }

    $urlparsed['type']        = isset($querybuff['type'])        ? $querybuff['type']        : '';
    $urlparsed['path']        = isset($querybuff['path'])        ? $querybuff['path']        : '';
    $urlparsed['mode']        = isset($querybuff['mode'])        ? $querybuff['mode']        : '';
    $urlparsed['plugin']      = isset($querybuff['plugin'])      ? $querybuff['plugin']      : '';
    $urlparsed['security']    = isset($querybuff['security'])    ? $querybuff['security']    : '';
    $urlparsed['encryption']  = isset($querybuff['encryption'])  ? $querybuff['encryption']  : '';
    $urlparsed['serviceName'] = isset($querybuff['serviceName']) ? $querybuff['serviceName'] : '';
    $urlparsed['sni']         = isset($querybuff['sni'])         ? $querybuff['sni']         : '';
    printcfg($urlparsed, $tmpdata);
}

function printcfg($data, $tmpdata) {
    $outcfg = "";

    if ($data['cfgtype'] == "vless") {
        $outcfg .= !empty($data['name']) ? "- name: ".$data['name']."\n" : "- name: VLESS\n";
        $outcfg .= "  type: ".$data['cfgtype']."\n";
        $outcfg .= "  server: ".$data['host']."\n";
        $outcfg .= "  port: ".$data['port']."\n";
        $outcfg .= "  uuid: ".$data['uuid']."\n";
        $outcfg .= "  cipher: auto\n";
        $outcfg .= "  tls: true\n";
        $outcfg .= "  alterId: 0\n";
        $outcfg .= "  flow: xtls-rprx-direct\n";
        $outcfg .= !empty($data['sni']) ? "  servername: ".$data['sni']."\n" : "  servername: ".$data['host']."\n";
        if ($data['type'] == "ws") {
            $outcfg .= "  network: ".$data['type']."\n";
            $outcfg .= "  ws-opts: \n";
            $outcfg .= "   path: ".$data['path']."\n";
            $outcfg .= "   Headers: \n";
            $outcfg .= "      Host: ".$data['host']."\n";
        } else if ($data['type'] == "grpc") {
            $outcfg .= "  network: ".$data['type']."\n";
            $outcfg .= "  grpc-opts: \n";
            $outcfg .= "   grpc-service-name: ".$data['serviceName']."\n";
        }
        $outcfg .= "  udp: true\n";
        $outcfg .= "  skip-cert-verify: true\n";
        file_put_contents($tmpdata, $outcfg);
    }

    else if ($data['cfgtype'] == "trojan") {
        $outcfg .= !empty($data['name']) ? "- name: ".$data['name']."\n" : "- name: TROJAN\n";
        $outcfg .= "  type: ".$data['cfgtype']."\n";
        $outcfg .= "  server: ".$data['host']."\n";
        $outcfg .= "  port: ".$data['port']."\n";
        $outcfg .= "  password: ".$data['uuid']."\n";
        $outcfg .= !empty($data['sni']) ? "  sni: ".$data['sni']."\n" : "  sni: ".$data['host']."\n";
        if ($data['type'] == "ws") {
            $outcfg .= "  network: ".$data['type']."\n";
            $outcfg .= "  ws-opts: \n";
            $outcfg .= "   path: ".$data['path']."\n";
            $outcfg .= "   Headers: \n";
            $outcfg .= "      Host: ".$data['host']."\n";
        } else if ($data['type'] == "grpc") {
            $outcfg .= "  network: ".$data['type']."\n";
            $outcfg .= "  grpc-opts: \n";
            $outcfg .= "   grpc-service-name: ".$data['serviceName']."\n";
        }
        $outcfg .= "  udp: true\n";
        $outcfg .= "  skip-cert-verify: true\n";
        file_put_contents($tmpdata, $outcfg);
    }

    else if ($data['cfgtype'] == "ss") {
        $outcfg .= !empty($data['name']) ? "- name: ".$data['name']."\n" : "- name: SHADOWSOCKS\n";
        $outcfg .= "  type: ".$data['cfgtype']."\n";
        $outcfg .= "  server: ".$data['host']."\n";
        $outcfg .= "  port: ".$data['port']."\n";
        $outcfg .= "  cipher: ".$data['cipher']."\n";
        $outcfg .= "  password: ".$data['uuid']."\n";
        if ($data['plugin'] == "v2ray-plugin" || $data['plugin'] == "xray-plugin") {
            $outcfg .= "  plugin: ".$data['plugin']."\n";
            $outcfg .= "  plugin-opts: \n";
            $outcfg .= "   mode: websocket\n";
            $outcfg .= "   # path: ".$data['path']."\n";
            $outcfg .= "   mux: ".$data['mux']."\n";
            $outcfg .= "   # tls: true \n";
            $outcfg .= "   # skip-cert-verify: true \n";
            $outcfg .= "   # headers: \n";
            $outcfg .= "   #    custom: value\n";
        } else if ($data['plugin'] == "obfs") {
            $outcfg .= "  plugin: ".$data['plugin']."\n";
            $outcfg .= "  plugin-opts: \n";
            $outcfg .= "   mode: tls\n";
            $outcfg .= "   # host: ".$data['host2']."\n";
        }
        $outcfg .= "  udp: true\n";
        $outcfg .= "  skip-cert-verify: true\n";
        file_put_contents($tmpdata, $outcfg);
    }

    else if ($data['cfgtype'] == "vmess") {
        $outcfg .= !empty($data['name']) ? "- name: ".$data['name']."\n" : "- name: VMESS\n";
        $outcfg .= "  type: ".$data['cfgtype']."\n";
        $outcfg .= "  server: ".$data['host']."\n";
        $outcfg .= "  port: ".$data['port']."\n";
        $outcfg .= "  uuid: ".$data['uuid']."\n";
        $outcfg .= "  alterId: ".$data['alterId']."\n";
        $outcfg .= "  cipher: auto\n";
        $outcfg .= ($data['tls'] == "tls") ? "  tls: true\n" : "  tls: false\n";
        $outcfg .= !empty($data['sni']) ? "  servername: ".$data['sni']."\n" : "  servername: ".$data['host']."\n";
        $outcfg .= "  network: ".$data['type']."\n";
        if ($data['type'] == "ws") {
            $outcfg .= "  ws-opts: \n";
            $outcfg .= "   path: ".$data['path']."\n";
            $outcfg .= "   Headers: \n";
            $outcfg .= "      Host: ".$data['sni']."\n";
        } else if ($data['type'] == "grpc") {
            $outcfg .= "  grpc-opts: \n";
            $outcfg .= "   grpc-service-name: ".$data['serviceName']."\n";
        } else if ($data['type'] == "h2") {
            $outcfg .= "  h2-opts: \n";
            $outcfg .= "   host: \n";
            $outcfg .= "     - google.com \n";
            $outcfg .= "     - bing.com \n";
            $outcfg .= "   path: ".$data['path']."\n";
        } else if ($data['type'] == "http") {
            $outcfg .= "  # http-opts: \n";
            $outcfg .= "  #   method: \"GET\"\n";
            $outcfg .= "  #   path: \n";
            $outcfg .= "  #     - '/'\n";
            $outcfg .= "  #   headers: \n";
            $outcfg .= "  #     Connection: \n";
            $outcfg .= "  #       - keep-alive\n";
        }
        $outcfg .= "  udp: true\n";
        $outcfg .= "  skip-cert-verify: true\n";
        file_put_contents($tmpdata, $outcfg);
    }
}

?>
