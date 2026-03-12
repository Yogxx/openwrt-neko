<?php

$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navtop navbar-expand-lg py-1">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center text-body" href="#">
            <i data-feather="slack" class="me-2 spin-slow"></i>
            <span>NekoClash</span>
        </a>

        <!-- Hamburger Toggle (mobile) -->
        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarContent"
            aria-controls="navbarContent"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Main Navigation -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">
                        <i data-feather="home" class="feather-sm me-lg-1"></i>
                        <span class="d-none d-lg-inline">Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="./dashboard.php">
                        <i data-feather="activity" class="feather-sm me-lg-1"></i>
                        <span class="d-none d-lg-inline">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'configs.php') ? 'active' : ''; ?>" href="./configs.php">
                        <i data-feather="settings" class="feather-sm me-lg-1"></i>
                        <span class="d-none d-lg-inline">Configs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>" href="./settings.php">
                        <i data-feather="tool" class="feather-sm me-lg-1"></i>
                        <span class="d-none d-lg-inline">Settings</span>
                    </a>
                </li>
            </ul>

            <!-- Theme Switcher -->
            <ul class="navbar-nav navbar-align">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme (auto)">
                        <span class="theme-icon-active" id="bd-theme-icon">
                            <i class="fa fa-sun"></i>
                        </span>
                        <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
                        <li>
                            <button class="dropdown-item d-flex align-items-center" type="button" data-bs-theme-value="light">
                                <i class="fa fa-sun opacity-50 me-2"></i>Light
                                <i class="pr-check fa fa-check ms-auto d-none"></i>
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item d-flex align-items-center" type="button" data-bs-theme-value="dark">
                                <i class="fa fa-moon opacity-50 me-2"></i>Dark
                                <i class="pr-check fa fa-check ms-auto d-none"></i>
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item d-flex align-items-center" type="button" data-bs-theme-value="auto">
                                <i class="fa fa-circle-half-stroke opacity-50 me-2"></i>Auto
                                <i class="pr-check fa fa-check ms-auto d-none"></i>
                            </button>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
