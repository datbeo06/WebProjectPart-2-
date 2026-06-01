<?php
// header.inc
// Shared HTML head and top header section for SolarCore Energy website
// Group Nick-Thu-1030-G03
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SolarCore Energy is a Melbourne-based renewable energy company delivering solar, wind, and hydro solutions for a sustainable future.">
    <meta name="keywords" content="solar energy, wind energy, hydro power, renewable energy, sustainability, clean energy Australia, Melbourne renewable energy">
    <meta name="author" content="Group G03">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : "SolarCore Energy"; ?></title>

    <!-- External stylesheet (shared across all pages) -->
    <link rel="stylesheet" href="styles/style.css">

    <!-- Embedded CSS specific to individual pages if provided -->
    <?php if (isset($page_style)) { echo $page_style; } ?>
</head>
<body>

    <!-- =====================================================
         SITE HEADER — logo + company name + slogan
         ===================================================== -->
    <header class="site-header">
        <a href="index.php" class="logo-link" aria-label="SolarCore Energy home">
            <img src="images/logo.png" alt="SolarCore Energy logo — golden sun icon with company name">
        </a>
        <p style="color: rgba(255,255,255,0.85); font-size: 0.85rem; font-style: italic; margin: 0;">
            Powering tomorrow with clean energy.
        </p>
    </header>
