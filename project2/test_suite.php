<?php
// test_suite.php
// Automated Integration & Validation Tests for SolarCore Energy Recruitment Portal
// Group Nick-Thu-1030-G03

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper to run process_eoi.php with simulated POST data and capture output
function run_process_eoi($post_data) {
    global $conn;
    
    // Clear out standard input structures
    $_POST = $post_data;
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    // Capture output buffer to prevent rendering to console during test execution
    ob_start();
    try {
        include 'process_eoi.php';
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage();
    }
    $output = ob_get_clean();
    
    return $output;
}

// 1. Setup connection
require_once 'settings.php';
echo "========================================================\n";
echo "SOLARCORE RECRUITMENT PORTAL - AUTOMATED TEST SUITE\n";
echo "========================================================\n\n";

// Clear EOI table before running tests to have a clean slate
mysqli_query($conn, "TRUNCATE TABLE eoi");
echo "1. Database truncated. Clean test environment prepared.\n";

$tests_passed = 0;
$tests_failed = 0;

function assert_test($condition, $message) {
    global $tests_passed, $tests_failed;
    if ($condition) {
        echo "  [PASS] $message\n";
        $tests_passed++;
    } else {
        echo "  [FAIL] $message\n";
        $tests_failed++;
    }
}

// -------------------------------------------------------------------------
// TEST CASE 1: Empty Submission
// -------------------------------------------------------------------------
echo "\nTest Case 1: Empty Submission (Trigger All Errors)\n";
$empty_data = [];
$output1 = run_process_eoi($empty_data);

assert_test(strpos($output1, 'Job reference number is required.') !== false, "Error 'Job reference number is required' found.");
assert_test(strpos($output1, 'First name is required.') !== false, "Error 'First name is required' found.");
assert_test(strpos($output1, 'Last name is required.') !== false, "Error 'Last name is required' found.");
assert_test(strpos($output1, 'Date of birth is required.') !== false, "Error 'Date of birth is required' found.");
assert_test(strpos($output1, 'Please select a gender.') !== false, "Error 'Please select a gender' found.");
assert_test(strpos($output1, 'Street address is required.') !== false, "Error 'Street address is required' found.");
assert_test(strpos($output1, 'Suburb/town is required.') !== false, "Error 'Suburb/town is required' found.");
assert_test(strpos($output1, 'State selection is required.') !== false, "Error 'State selection is required' found.");
assert_test(strpos($output1, 'Postcode is required.') !== false, "Error 'Postcode is required' found.");
assert_test(strpos($output1, 'Email address is required.') !== false, "Error 'Email address is required' found.");
assert_test(strpos($output1, 'Phone number is required.') !== false, "Error 'Phone number is required' found.");
assert_test(strpos($output1, 'You must select at least one skill checkbox.') !== false, "Error 'Must select at least one skill' found.");

// -------------------------------------------------------------------------
// TEST CASE 2: Invalid Postcode/State Combination
// -------------------------------------------------------------------------
echo "\nTest Case 2: Postcode Cross-Validation (NSW State with VIC Postcode)\n";
$invalid_postcode_data = [
    'job_ref' => 'WD001',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'dob' => '15/05/1990',
    'gender' => 'Male',
    'street' => '123 Swanston St',
    'suburb' => 'Melbourne',
    'state' => 'NSW', // NSW state
    'postcode' => '3000', // VIC postcode starts with 3
    'email' => 'john.doe@test.com',
    'phone' => '0412345678',
    'skills' => ['PHP', 'MySQL'],
    'other_skills' => ''
];
$output2 = run_process_eoi($invalid_postcode_data);
assert_test(strpos($output2, 'NSW postcodes must start with 1 or 2.') !== false, "Postcode cross-validation correctly identified mismatch (NSW with 3000).");

// -------------------------------------------------------------------------
// TEST CASE 3: Underage / Overage Validation
// -------------------------------------------------------------------------
echo "\nTest Case 3: Age Restriction Check (Under 15 and Over 80)\n";
$underage_data = [
    'job_ref' => 'WD001',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'dob' => date('d/m/Y', strtotime('-14 years')), // 14 years old
    'gender' => 'Male',
    'street' => '123 Swanston St',
    'suburb' => 'Melbourne',
    'state' => 'VIC',
    'postcode' => '3000',
    'email' => 'john.doe@test.com',
    'phone' => '0412345678',
    'skills' => ['PHP'],
    'other_skills' => ''
];
$output3_under = run_process_eoi($underage_data);
assert_test(strpos($output3_under, 'Applicant age must be between 15 and 80 years old.') !== false, "Underage check correctly rejected 14-year-old.");

$overage_data = $underage_data;
$overage_data['dob'] = date('d/m/Y', strtotime('-81 years')); // 81 years old
$output3_over = run_process_eoi($overage_data);
assert_test(strpos($output3_over, 'Applicant age must be between 15 and 80 years old.') !== false, "Overage check correctly rejected 81-year-old.");

// -------------------------------------------------------------------------
// TEST CASE 4: Valid Submission
// -------------------------------------------------------------------------
echo "\nTest Case 4: Valid EOI Submission\n";
$valid_data = [
    'job_ref' => 'WD001',
    'first_name' => 'James',
    'last_name' => 'Smith',
    'dob' => '20/10/1995',
    'gender' => 'Male',
    'street' => '456 George St',
    'suburb' => 'Sydney',
    'state' => 'NSW',
    'postcode' => '2000',
    'email' => 'james.smith@solarcore.com.au',
    'phone' => '0499888777',
    'skills' => ['PHP', 'MySQL', 'HTML5/CSS3'],
    'other_skills' => 'Highly passionate about solar panels.'
];
$output4 = run_process_eoi($valid_data);
assert_test(strpos($output4, 'Application Received Successfully') !== false, "Success screen displayed correctly.");
assert_test(strpos($output4, 'EOI #1') !== false, "Database generated and returned EOI #1.");

// -------------------------------------------------------------------------
// TEST CASE 5: Verify DB Record Integrity
// -------------------------------------------------------------------------
echo "\nTest Case 5: Verify Database Record Integrity\n";
$db_check = mysqli_query($conn, "SELECT * FROM eoi WHERE EOInumber = 1");
if ($row = mysqli_fetch_assoc($db_check)) {
    assert_test($row['job_ref'] === 'WD001', "Job reference stored correctly ('WD001').");
    assert_test($row['first_name'] === 'James', "First name stored correctly ('James').");
    assert_test($row['last_name'] === 'Smith', "Last name stored correctly ('Smith').");
    assert_test($row['dob'] === '20/10/1995', "DOB stored correctly ('20/10/1995').");
    assert_test($row['gender'] === 'Male', "Gender stored correctly ('Male').");
    assert_test($row['state'] === 'NSW', "State stored correctly ('NSW').");
    assert_test($row['postcode'] === '2000', "Postcode stored correctly ('2000').");
    assert_test($row['email'] === 'james.smith@solarcore.com.au', "Email stored correctly ('james.smith@solarcore.com.au').");
    assert_test($row['phone'] === '0499888777', "Phone stored correctly ('0499888777').");
    assert_test($row['skills'] === 'PHP, MySQL, HTML5/CSS3', "Skills array correctly joined and stored ('PHP, MySQL, HTML5/CSS3').");
    assert_test($row['other_skills'] === 'Highly passionate about solar panels.', "Other skills stored correctly.");
    assert_test($row['status'] === 'New', "Default status set to 'New'.");
} else {
    assert_test(false, "Failed to retrieve EOI #1 from database.");
}

// -------------------------------------------------------------------------
// TEST CASE 6: Security Verification (XSS and SQL Injection)
// -------------------------------------------------------------------------
echo "\nTest Case 6: Security and Sanitization Verification (XSS & SQL Injection)\n";
$malicious_data = [
    'job_ref' => 'WD001',
    'first_name' => "O'Connor", // Single quote testing SQL injection
    'last_name' => '<script>alert("xss")</script>', // XSS payload
    'dob' => '05/05/2000',
    'gender' => 'Other',
    'street' => '789 Queen St',
    'suburb' => 'Brisbane',
    'state' => 'QLD',
    'postcode' => '4000',
    'email' => 'malicious@hacker.com',
    'phone' => '0711223344',
    'skills' => ['JavaScript'],
    'other_skills' => "SQL' OR '1'='1"
];

// Re-enable real database saving (this should succeed because inputs are sanitized)
// Wait! Let's filter out letters-only checks first
$malicious_data['first_name'] = 'Connor'; // first name only allows letters in our regex
$malicious_data['last_name'] = 'Hacker'; // last name only allows letters in our regex
// We put a short XSS script in street address (which accepts letters/symbols) so it fits in 40 chars
$malicious_data['street'] = '789 <u> St';
$malicious_data['other_skills'] = "SQL' OR '1'='1' <script>alert(\"xss\")</script>";

$output6 = run_process_eoi($malicious_data);
$success_found = (strpos($output6, 'Application Received Successfully') !== false);
assert_test($success_found, "XSS and SQL injection payloads handled and saved safely.");

if (!$success_found) {
    echo "--- DEBUG OUTPUT FROM PROCESS_EOI ---\n";
    echo strip_tags($output6) . "\n";
    echo "-------------------------------------\n";
}

$db_sec_check = mysqli_query($conn, "SELECT * FROM eoi WHERE EOInumber = 2");
if ($sec_row = mysqli_fetch_assoc($db_sec_check)) {
    $stored_street = $sec_row['street'];
    $stored_other = $sec_row['other_skills'];
    
    // Check street escaping
    assert_test(strpos($stored_street, '<u>') === false, "HTML Tags (u) properly escaped in street address.");
    assert_test(strpos($stored_street, '&lt;u&gt;') !== false, "u tag safely stored as entity-encoded string in street address.");
    
    // Check other_skills escaping
    echo "  [DEBUG] Stored other_skills: " . $stored_other . "\n";
    assert_test(strpos($stored_other, '<script>') === false, "HTML Tags (script) properly escaped in other_skills.");
    assert_test(strpos($stored_other, '&lt;script&gt;') !== false, "script tag safely stored as entity-encoded string in other_skills.");
    assert_test(strpos($stored_other, 'SQL&#039; OR') !== false, "SQL quotes safely escaped to html entities in other_skills.");
} else {
    assert_test(false, "Failed to retrieve EOI #2 from database.");
}

echo "\n========================================================\n";
echo "TEST RESULTS SUMMARY\n";
echo "========================================================\n";
echo "Total Tests Run: " . ($tests_passed + $tests_failed) . "\n";
echo "Passed: $tests_passed\n";
echo "Failed: $tests_failed\n";
echo "========================================================\n";
?>
