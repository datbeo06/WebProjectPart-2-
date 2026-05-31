<?php
// logout.php
// Ends the HR manager session and redirects back to the homepage
// Group Nick-Thu-1030-G03

session_start();

// Unset all of the session variables
$_SESSION = array();

// Remove the session cookie when sessions are configured to use cookies
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

// Redirect to home page
header('Location: index.php');
exit;
?>
