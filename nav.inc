<?php
// nav.inc
// Shared navigation menu for SolarCore Energy website
// Group Nick-Thu-1030-G03

// Ensure session is started if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the current file name to highlight the active menu link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- =====================================================
     NAVIGATION — dynamically generated active links
     ===================================================== -->
<nav class="site-nav" aria-label="Main navigation">
    <ul>
        <li><a href="index.php" <?php if ($current_page === 'index.php') { echo 'class="active" aria-current="page"'; } ?>>Home</a></li>
        <li><a href="jobs.php" <?php if ($current_page === 'jobs.php') { echo 'class="active" aria-current="page"'; } ?>>Jobs</a></li>
        <li><a href="apply.php" <?php if ($current_page === 'apply.php') { echo 'class="active" aria-current="page"'; } ?>>Apply</a></li>
        <li><a href="about.php" <?php if ($current_page === 'about.php') { echo 'class="active" aria-current="page"'; } ?>>About Us</a></li>
        
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <li><a href="manage.php" <?php if ($current_page === 'manage.php') { echo 'class="active" aria-current="page"'; } ?>>Manage</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" <?php if ($current_page === 'login.php') { echo 'class="active" aria-current="page"'; } ?>>Login</a></li>
        <?php endif; ?>
    </ul>
</nav>
