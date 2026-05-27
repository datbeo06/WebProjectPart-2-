<?php
// login.php
// Manager authentication page protecting manage.php
// Group Nick-Thu-1030-G03

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to manage.php if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: manage.php');
    exit;
}

//loads database connection
require_once 'settings.php';

$error = '';
$username = '';

// Process login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        // Retrieve hashed password safely from database using prepared statements
        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE username = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) === 1) {
                mysqli_stmt_bind_result($stmt, $hashed_password);
                mysqli_stmt_fetch($stmt);
                
                // Securely verify password
                if (password_verify($password, $hashed_password)) {
                    $_SESSION['user'] = $username;
                    $_SESSION['logged_in'] = true;
                    
                    // Redirect to administration portal
                    header('Location: manage.php');
                    exit;
                }
            }
            // Generic security error message to prevent account enumeration
            $error = 'Invalid credentials. Please try again.';
            mysqli_stmt_close($stmt);
        } else {
            $error = 'System error. Please try again later.';
        }
    }
}

$page_title = "Manager Login — SolarCore Energy";

$page_style = '
    <style>
        .login-card {
            max-width: 400px;
            margin: 5rem auto;
            padding: 2.5rem;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(15, 76, 58, 0.08);
            border-top: 5px solid #0F4C3A;
        }

        .login-title {
            color: #0F4C3A;
            margin-top: 0;
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }

        .form-group input {
            width: 100%;
            padding: 0.65rem 0.8rem;
            border: 1px solid #D1D5DB;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #10B981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .error-message {
            background-color: #FEF2F2;
            color: #991B1B;
            border-left: 4px solid #EF4444;
            padding: 0.75rem 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background-color: #F59E0B;
            color: #0F4C3A;
            border: 1px solid #D97706;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .btn-login:hover {
            background-color: #0F4C3A;
            color: white;
            border-color: #0F4C3A;
        }
    </style>
';

include 'header.inc';
include 'nav.inc';
?>

    <main>
        <div class="section-container" style="padding: 1rem;">
            <div class="login-card">
                <h2 class="login-title">HR Portal Login</h2>
                
                <?php if ($error !== ''): ?>
                    <div class="error-message">
                        &#9888; <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php" novalidate>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" autocomplete="username">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" autocomplete="current-password">
                    </div>
                    
                    <button type="submit" class="btn-login">Log In</button>
                </form>
                
                <p style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem; color: #6B7280;">
                    Authorized HR Personnel Only.
                </p>
            </div>
        </div>
    </main>

<?php
include 'footer.inc';
?>
