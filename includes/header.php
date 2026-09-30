<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Site settings
|--------------------------------------------------------------------------
| Fetched once here so every page, and footer.php, can use $settings
| without each needing its own query. Falls back to safe defaults if
| the settings table hasn't been created yet, so the site never breaks.
*/

if (!isset($pdo)) {
    require_once __DIR__ . "/../config/database.php";
}

$settings = false;

try {
    $settings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();
} catch (PDOException $e) {
    $settings = false;
}

if (!$settings) {
    $settings = [
        "restaurant_name" => "Bite & Bliss",
        "address" => "",
        "phone" => "",
        "phone_secondary" => "",
        "email" => "",
        "email_secondary" => "",
        "hours_weekday" => "",
        "hours_weekend" => "",
        "facebook_url" => "#",
        "instagram_url" => "#",
        "twitter_url" => "#"
    ];
}

$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = $pageTitle ?? 'Bite & Bliss';

$projectRoot = realpath(dirname(__DIR__));
$currentScriptDirectory = realpath(
    dirname($_SERVER['SCRIPT_FILENAME'] ?? $projectRoot)
);
$sitePath = '';

if (
    $projectRoot &&
    $currentScriptDirectory &&
    str_starts_with($currentScriptDirectory, $projectRoot)
) {
    $relativeDirectory = trim(
        substr($currentScriptDirectory, strlen($projectRoot)),
        DIRECTORY_SEPARATOR
    );

    if ($relativeDirectory !== '') {
        $sitePath = str_repeat(
            '../',
            count(explode(DIRECTORY_SEPARATOR, $relativeDirectory))
        );
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="Bite & Bliss — delicious food, warm hospitality, and memorable dining experiences.">

    <title>
        <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>
    </title>

    <!-- Bootstrap 5.3.3 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons 1.11.3 -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= $sitePath ?>assets/css/style.css">
</head>

<body>

    <!-- Mouse Glow -->
    <div class="mouse-glow" aria-hidden="true"></div>

    <!-- =========================
         NAVBAR
    ========================== -->

    <nav class="navbar navbar-expand-lg" aria-label="Main navigation">

        <div class="container">

            <!-- Logo -->
            <a class="navbar-brand" href="<?= $sitePath ?>index.php">
                Bite <span>&</span> Bliss
            </a>

            <!-- Mobile Toggle -->
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
                aria-controls="mainNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation">

                <i class="bi bi-list"></i>

            </button>

            <!-- Navigation -->
            <div class="collapse navbar-collapse" id="mainNavbar">

                <ul class="navbar-nav mx-auto">

                    <!-- Home -->
                    <li class="nav-item">
                        <a
                            class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>"
                            href="<?= $sitePath ?>index.php"
                            <?= $currentPage === 'index.php'
                                ? 'aria-current="page"'
                                : '' ?>>

                            Home

                        </a>
                    </li>

                    <!-- Menu -->
                    <li class="nav-item">
                        <a
                            class="nav-link <?= $currentPage === 'menu.php' ? 'active' : '' ?>"
                            href="<?= $sitePath ?>menu.php"
                            <?= $currentPage === 'menu.php'
                                ? 'aria-current="page"'
                                : '' ?>>

                            Menu

                        </a>
                    </li>

                    <!-- About -->
                    <li class="nav-item">
                        <a
                            class="nav-link <?= $currentPage === 'about.php' ? 'active' : '' ?>"
                            href="<?= $sitePath ?>about.php"
                            <?= $currentPage === 'about.php'
                                ? 'aria-current="page"'
                                : '' ?>>

                            About

                        </a>
                    </li>

                    <!-- Reservations -->
                    <li class="nav-item">
                        <a
                            class="nav-link <?= $currentPage === 'reservations.php' ? 'active' : '' ?>"
                            href="<?= $sitePath ?>reservations.php"
                            <?= $currentPage === 'reservations.php'
                                ? 'aria-current="page"'
                                : '' ?>>

                            Reservations

                        </a>
                    </li>

                    <!-- Contact -->
                    <li class="nav-item">
                        <a
                            class="nav-link <?= $currentPage === 'contact.php' ? 'active' : '' ?>"
                            href="<?= $sitePath ?>contact.php"
                            <?= $currentPage === 'contact.php'
                                ? 'aria-current="page"'
                                : '' ?>>

                            Contact

                        </a>
                    </li>

                </ul>

                <!-- Navbar Actions -->
                <div class="navbar-actions">

                    <!-- Theme Toggle -->
                    <button
                        class="theme-toggle"
                        id="themeToggle"
                        type="button"
                        aria-label="Toggle light and dark mode">

                        <i
                            class="bi bi-sun-fill"
                            aria-hidden="true">
                        </i>

                    </button>

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>

                            <!-- Admin Dashboard -->
                            <a
                                href="<?= $sitePath ?>admin/dashboard.php"
                                class="btn-register">

                                Dashboard

                            </a>

                        <?php endif; ?>

                        <!-- Logout -->
                        <a
                                href="<?= $sitePath ?>auth/logout.php"
                            class="btn-login">

                            Logout

                        </a>

                    <?php else: ?>

                        <!-- Login -->
                        <a
                                href="<?= $sitePath ?>login.php"
                            class="btn-login">

                            Login

                        </a>

                        <!-- Register -->
                        <a
                                href="<?= $sitePath ?>register.php"
                            class="btn-register">

                            Register

                        </a>

                    <?php endif; ?>

                    <a href="<?= $sitePath ?>cart.php" class="nav-link">
                        <i class="bi bi-cart3"></i>
                        Cart
                        <?php
                        $cartCount = 0;

                        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                            $cartCount = array_sum($_SESSION['cart']);
                        }

                        if ($cartCount > 0):
                        ?>
                            <span class="badge bg-danger">
                                <?= $cartCount ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <?php if (isset($_SESSION["user_id"])): ?>

                        <a href="<?= $sitePath ?>orders/my_orders.php" class="nav-link">
                            <i class="bi bi-bag"></i>
                            My Orders
                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </nav>


    <!-- =========================
         PAGE CONTENT STARTS HERE
    ========================== -->