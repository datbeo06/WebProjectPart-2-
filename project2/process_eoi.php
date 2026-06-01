<?php
// process_eoi.php
// Server-side validation, sanitization, and insertion of applicant EOIs
// Group Nick-Thu-1030-G03

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Prevent direct GET access. Only accept POST submissions.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: apply.php');
    exit;
}

require_once 'settings.php';

// Helper function to sanitize user inputs
if (!function_exists('sanitise')) {
    function sanitise($input) {
        return htmlspecialchars(stripslashes(trim($input)));
    }
}

// Auto-create eoi table if it does not exist (Defensive Design)
$create_eoi_table_sql = "
CREATE TABLE IF NOT EXISTS eoi (
    EOInumber INT AUTO_INCREMENT PRIMARY KEY,
    job_ref VARCHAR(5) NOT NULL,
    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(20) NOT NULL,
    dob VARCHAR(10) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    street VARCHAR(40) NOT NULL,
    suburb VARCHAR(40) NOT NULL,
    state VARCHAR(3) NOT NULL,
    postcode VARCHAR(4) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(12) NOT NULL,
    skills TEXT,
    other_skills TEXT,
    status ENUM('New', 'Current', 'Final') DEFAULT 'New',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
@mysqli_query($conn, $create_eoi_table_sql);

// 2. Fetch and sanitize all inputs
$job_ref = isset($_POST['job_ref']) ? sanitise($_POST['job_ref']) : '';
$first_name = isset($_POST['first_name']) ? sanitise($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? sanitise($_POST['last_name']) : '';
$dob = isset($_POST['dob']) ? sanitise($_POST['dob']) : '';
$gender = isset($_POST['gender']) ? sanitise($_POST['gender']) : '';
$street = isset($_POST['street']) ? sanitise($_POST['street']) : '';
$suburb = isset($_POST['suburb']) ? sanitise($_POST['suburb']) : '';
$state = isset($_POST['state']) ? sanitise($_POST['state']) : '';
$postcode = isset($_POST['postcode']) ? sanitise($_POST['postcode']) : '';
$email = isset($_POST['email']) ? sanitise($_POST['email']) : '';
$phone = isset($_POST['phone']) ? sanitise($_POST['phone']) : '';

// Handle checkbox skills array
$skills_array = isset($_POST['skills']) && is_array($_POST['skills']) ? $_POST['skills'] : [];
$skills_sanitized = array_map('sanitise', $skills_array);
$skills_str = implode(', ', $skills_sanitized);

$other_skills = isset($_POST['other_skills']) ? sanitise($_POST['other_skills']) : '';

// 3. Validation Logic
$errors = [];

// Validate Job Reference: Exactly 5 alphanumeric characters
if ($job_ref === '') {
    $errors['job_ref'] = 'Job reference number is required.';
} else if (!preg_match('/^[A-Za-z0-9]{5}$/', $job_ref)) {
    $errors['job_ref'] = 'Job reference must be exactly 5 alphanumeric characters.';
} else {
    // Cross-validate that the job reference exists in the jobs database
    $job_check_stmt = mysqli_prepare($conn, "SELECT job_ref FROM jobs WHERE job_ref = ?");
    if ($job_check_stmt) {
        mysqli_stmt_bind_param($job_check_stmt, "s", $job_ref);
        mysqli_stmt_execute($job_check_stmt);
        mysqli_stmt_store_result($job_check_stmt);
        if (mysqli_stmt_num_rows($job_check_stmt) === 0) {
            $errors['job_ref'] = 'Job reference must match an active position on our Jobs page (e.g., WD001, SE002, SC001).';
        }
        mysqli_stmt_close($job_check_stmt);
    }
}

// Validate First Name: Max 20 alphabetical characters
if ($first_name === '') {
    $errors['first_name'] = 'First name is required.';
} else if (!preg_match('/^[A-Za-z]{1,20}$/', $first_name)) {
    $errors['first_name'] = 'First name must contain only letters (max 20 characters).';
}

// Validate Last Name: Max 20 alphabetical characters
if ($last_name === '') {
    $errors['last_name'] = 'Last name is required.';
} else if (!preg_match('/^[A-Za-z]{1,20}$/', $last_name)) {
    $errors['last_name'] = 'Last name must contain only letters (max 20 characters).';
}

// Validate DOB: dd/mm/yyyy format, valid calendar date, and age 15-80
if ($dob === '') {
    $errors['dob'] = 'Date of birth is required.';
} else if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dob)) {
    $errors['dob'] = 'Date of birth must be in dd/mm/yyyy format.';
} else {
    $dob_parts = explode('/', $dob);
    $day = (int)$dob_parts[0];
    $month = (int)$dob_parts[1];
    $year = (int)$dob_parts[2];
    
    if (!checkdate($month, $day, $year)) {
        $errors['dob'] = 'Please enter a valid calendar date.';
    } else {
        // Calculate age
        $dob_formatted = "$year-$month-$day";
        $dob_time = strtotime($dob_formatted);
        $current_time = time();
        
        $age = date('Y', $current_time) - date('Y', $dob_time);
        if (date('md', $current_time) < date('md', $dob_time)) {
            $age--;
        }
        
        if ($age < 15 || $age > 80) {
            $errors['dob'] = 'Applicant age must be between 15 and 80 years old.';
        }
    }
}

// Validate Gender: Must be Male, Female, or Other
if ($gender === '') {
    $errors['gender'] = 'Please select a gender.';
} else if (!in_array($gender, ['Male', 'Female', 'Other'])) {
    $errors['gender'] = 'Invalid gender selected.';
}

// Validate Street Address: Max 40 characters
if ($street === '') {
    $errors['street'] = 'Street address is required.';
} else if (strlen($street) > 40) {
    $errors['street'] = 'Street address cannot exceed 40 characters.';
} else if (preg_match('/^[0-9]+$/', $street)) {
    $errors['street'] = 'Street address cannot contain only numbers.';
}


// Validate Suburb/Town: Max 40 characters
if ($suburb === '') {
    $errors['suburb'] = 'Suburb/town is required.';
} else if (strlen($suburb) > 40) {
    $errors['suburb'] = 'Suburb/town cannot exceed 40 characters.';
} else if (preg_match('/^[0-9]+$/', $suburb)) {
    $errors['suburb'] = 'Suburb/town cannot contain only numbers.';
}

// Validate State: Must be VIC, NSW, QLD, WA, SA, TAS, ACT, NT
$valid_states = ['VIC', 'NSW', 'QLD', 'WA', 'SA', 'TAS', 'ACT', 'NT'];
if ($state === '') {
    $errors['state'] = 'State selection is required.';
} else if (!in_array($state, $valid_states)) {
    $errors['state'] = 'Invalid state selected.';
}

// Validate Postcode: Exactly 4 digits, cross-validated with state
if ($postcode === '') {
    $errors['postcode'] = 'Postcode is required.';
} else if (!preg_match('/^\d{4}$/', $postcode)) {
    $errors['postcode'] = 'Postcode must be exactly 4 digits.';
} else if ($state !== '') {
    // Cross-validation of postcode against Australian state rules
    $first_digit = substr($postcode, 0, 1);
    
    switch ($state) {
        case 'VIC':
            if ($first_digit !== '3' && $first_digit !== '8') {
                $errors['postcode'] = 'VIC postcodes must start with 3 or 8.';
            }
            break;
        case 'NSW':
            if ($first_digit !== '1' && $first_digit !== '2') {
                $errors['postcode'] = 'NSW postcodes must start with 1 or 2.';
            }
            break;
        case 'QLD':
            if ($first_digit !== '4' && $first_digit !== '9') {
                $errors['postcode'] = 'QLD postcodes must start with 4 or 9.';
            }
            break;
        case 'SA':
            if ($first_digit !== '5') {
                $errors['postcode'] = 'SA postcodes must start with 5.';
            }
            break;
        case 'WA':
            if ($first_digit !== '6') {
                $errors['postcode'] = 'WA postcodes must start with 6.';
            }
            break;
        case 'TAS':
            if ($first_digit !== '7') {
                $errors['postcode'] = 'TAS postcodes must start with 7.';
            }
            break;
        case 'NT':
            if ($first_digit !== '0') {
                $errors['postcode'] = 'NT postcodes must start with 0.';
            }
            break;
        case 'ACT':
            $prefix2 = substr($postcode, 0, 2);
            if ($first_digit !== '0' && $prefix2 !== '26' && $prefix2 !== '29') {
                $errors['postcode'] = 'ACT postcodes must start with 0, 26, or 29.';
            }
            break;
    }
}

// Validate Email: Standard validation pattern
if ($email === '') {
    $errors['email'] = 'Email address is required.';
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email address format.';
}

// Validate Phone Number: 8 to 12 digits (ignore spaces)
$phone_clean = preg_replace('/\s+/', '', $phone);
if ($phone === '') {
    $errors['phone'] = 'Phone number is required.';
} else if (!preg_match('/^\d{8,12}$/', $phone_clean)) {
    $errors['phone'] = 'Phone number must be between 8 and 12 digits.';
}

// Validate Skills: Must select at least one checkbox skill
if (empty($skills_sanitized)) {
    $errors['skills'] = 'You must select at least one skill checkbox.';
}

// Validate Other Skills length
if (strlen($other_skills) > 200) {
    $errors['other_skills'] = 'Other skills must not exceed 200 characters.';
} else if (preg_match('/^[0-9]+$/', $other_skills)) {
    $errors['other_skills'] = 'Other skills cannot contain only numbers.';
}

// Check for duplicate applications
if (empty($errors)) {

    $duplicate_stmt = mysqli_prepare(
        $conn,
        "SELECT EOInumber
         FROM eoi
         WHERE first_name = ?
         AND last_name = ?
         AND email = ?
         AND job_ref = ?"
    );

    if ($duplicate_stmt) {

        mysqli_stmt_bind_param(
            $duplicate_stmt,
            "ssss",
            $first_name,
            $last_name,
            $email,
            $job_ref
        );

        mysqli_stmt_execute($duplicate_stmt);
        mysqli_stmt_store_result($duplicate_stmt);

        if (mysqli_stmt_num_rows($duplicate_stmt) > 0) {
            $errors['duplicate'] =
                'You have already applied for this position. Duplicate applications are not permitted.';
        }

        mysqli_stmt_close($duplicate_stmt);
    }
}

// 4. Render HTML Results page (common styling and menus included)
$page_title = empty($errors) ? "Application Submitted Successfully" : "Application Errors Found";

$page_style = '
    <style>
        .result-container {
            max-width: 650px;
            margin: 3rem auto;
            padding: 2.5rem;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(15, 76, 58, 0.08);
            border-top: 5px solid #0F4C3A;
        }
        
        .result-container.has-errors {
            border-top-color: #EF4444;
        }

        .result-title {
            color: #0F4C3A;
            margin-top: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .result-title.error-color {
            color: #EF4444;
        }

        .error-list {
            margin: 1.5rem 0;
            padding-left: 1.5rem;
            color: #374151;
            line-height: 1.6;
        }

        .error-list li {
            margin-bottom: 0.75rem;
        }

        .error-list strong {
            color: #B91C1C;
        }

        .success-box {
            background-color: #ECFDF5;
            border-left: 4px solid #10B981;
            padding: 1.5rem;
            border-radius: 6px;
            margin: 1.5rem 0;
            color: #065F46;
        }

        .success-box h3 {
            margin-top: 0;
            color: #0F4C3A;
        }

        .eoi-badge {
            display: inline-block;
            background-color: #FEF3C7;
            color: #D97706;
            padding: 0.25rem 0.8rem;
            border-radius: 4px;
            font-family: \'Courier New\', monospace;
            font-weight: 700;
            font-size: 1.2rem;
            margin-top: 0.5rem;
            border: 1px solid #F59E0B;
        }

        .btn-action {
            display: inline-block;
            background-color: #F59E0B;
            color: #0F4C3A;
            text-decoration: none;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            font-weight: 700;
            border: 1px solid #D97706;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .btn-action:hover {
            background-color: #0F4C3A;
            color: #ffffff;
            border-color: #0F4C3A;
        }
    </style>
';

include 'header.inc';
include 'nav.inc';
?>

    <main>
        <div class="section-container">
            <?php if (!empty($errors)): ?>
                <!-- Render Errors Page -->
                <div class="result-container has-errors">
                    <h2 class="result-title error-color">&#9888; Application Errors Found</h2>
                    <p>We encountered some issues while validating your application. Please resolve the following errors:</p>
                    
                    <ul class="error-list">
                        <?php foreach ($errors as $field => $error_msg): ?>
                            <li><strong><?= htmlspecialchars(ucwords(str_replace('_', ' ', $field))) ?>:</strong> <?= htmlspecialchars($error_msg) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <p style="margin-top: 2rem;">
                        <!-- Back button using history back ensures their entered data is preserved in most browsers -->
                        <button onclick="window.history.back();" class="btn-action">Return to Apply Form</button>
                    </p>
                </div>
            <?php else: ?>
                <!-- Save in Database & Render Success Page -->
                <?php
                $stmt = mysqli_prepare($conn, "
                    INSERT INTO eoi (job_ref, first_name, last_name, dob, gender, street, suburb, state, postcode, email, phone, skills, other_skills)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $eoi_number = null;
                $db_error = false;
                
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssssssssssss", 
                        $job_ref, $first_name, $last_name, $dob, $gender, 
                        $street, $suburb, $state, $postcode, $email, $phone, 
                        $skills_str, $other_skills
                    );
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $eoi_number = mysqli_insert_id($conn);
                    } else {
                        $db_error = true;
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $db_error = true;
                }
                
                if ($db_error): ?>
                    <div class="result-container has-errors">
                        <h2 class="result-title error-color">&#9888; Database Error</h2>
                        <p>We apologize, but we could not store your application at this time due to a database connection problem. Please try again later.</p>
                        <p style="margin-top: 2rem;">
                            <a href="apply.php" class="btn-action">Back to Form</a>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="result-container">
                        <h2 class="result-title">&#10004; Application Received Successfully</h2>
                        <p>Thank you for expressing your interest in joining <strong>SolarCore Energy</strong>. Your application details have been safely stored in our central recruitment database.</p>
                        
                        <div class="success-box">
                            <h3>Record Information</h3>
                            <p>An administrator will review your profile shortly. Your unique application tracking number is:</p>
                            <span class="eoi-badge">EOI #<?= $eoi_number ?></span>
                        </div>

                        <table style="width: 100%; border-collapse: collapse; margin-top: 1.5rem; font-size: 0.9rem;">
                            <caption>Application Summary</caption>
                            <tr style="border-bottom: 1px solid #E5E7EB; text-align: left;"><th style="padding: 0.5rem 0; color: #0F4C3A;">Job Reference</th><td style="padding: 0.5rem 0;"><?= htmlspecialchars($job_ref) ?></td></tr>
                            <tr style="border-bottom: 1px solid #E5E7EB; text-align: left;"><th style="padding: 0.5rem 0; color: #0F4C3A;">Full Name</th><td style="padding: 0.5rem 0;"><?= htmlspecialchars($first_name . ' ' . $last_name) ?></td></tr>
                            <tr style="border-bottom: 1px solid #E5E7EB; text-align: left;"><th style="padding: 0.5rem 0; color: #0F4C3A;">Email</th><td style="padding: 0.5rem 0;"><?= htmlspecialchars($email) ?></td></tr>
                            <tr style="border-bottom: 1px solid #E5E7EB; text-align: left;"><th style="padding: 0.5rem 0; color: #0F4C3A;">Phone</th><td style="padding: 0.5rem 0;"><?= htmlspecialchars($phone) ?></td></tr>
                        </table>

                        <p style="margin-top: 2rem;">
                            <a href="index.php" class="btn-action">Return to Homepage</a>
                        </p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

<?php
include 'footer.inc';
?>
