<?php
// test_management.php
// Automated Integration Tests for SolarCore Energy HR Dashboard (manage.php)
// Group Nick-Thu-1030-G03

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mock a logged-in HR session
$_SESSION['logged_in'] = true;
$_SESSION['user'] = 'admin';

// Include DB settings
require_once 'settings.php';

echo "========================================================\n";
echo "SOLARCORE MANAGEMENT PORTAL - AUTOMATED TEST SUITE\n";
echo "========================================================\n\n";

// Clear out and pre-seed eoi database table with mock candidates for filtering/sorting tests
mysqli_query($conn, "TRUNCATE TABLE eoi");
echo "1. Database truncated. Clean test environment prepared.\n";

$seed_data = [
    [
        'job_ref' => 'WD001',
        'first_name' => 'Alice',
        'last_name' => 'Zeta',
        'dob' => '10/10/1990',
        'gender' => 'Female',
        'street' => '1 Main St',
        'suburb' => 'Richmond',
        'state' => 'VIC',
        'postcode' => '3121',
        'email' => 'alice@test.com',
        'phone' => '0399998888',
        'skills' => 'PHP, MySQL',
        'other_skills' => '',
        'status' => 'New'
    ],
    [
        'job_ref' => 'SE002',
        'first_name' => 'Bob',
        'last_name' => 'Alpha',
        'dob' => '12/12/1988',
        'gender' => 'Male',
        'street' => '2 Broad St',
        'suburb' => 'Fitzroy',
        'state' => 'VIC',
        'postcode' => '3065',
        'email' => 'bob@test.com',
        'phone' => '0388887777',
        'skills' => 'Solar PV',
        'other_skills' => '',
        'status' => 'Current'
    ],
    [
        'job_ref' => 'WD001',
        'first_name' => 'Charlie',
        'last_name' => 'Beta',
        'dob' => '05/05/1995',
        'gender' => 'Other',
        'street' => '3 High St',
        'suburb' => 'St Kilda',
        'state' => 'VIC',
        'postcode' => '3182',
        'email' => 'charlie@test.com',
        'phone' => '0377776666',
        'skills' => 'HTML5/CSS3, JavaScript',
        'other_skills' => '',
        'status' => 'New'
    ]
];

foreach ($seed_data as $data) {
    $stmt = mysqli_prepare($conn, "
        INSERT INTO eoi (job_ref, first_name, last_name, dob, gender, street, suburb, state, postcode, email, phone, skills, other_skills, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, "ssssssssssssss", 
        $data['job_ref'], $data['first_name'], $data['last_name'], $data['dob'], 
        $data['gender'], $data['street'], $data['suburb'], $data['state'], 
        $data['postcode'], $data['email'], $data['phone'], $data['skills'], 
        $data['other_skills'], $data['status']
    );
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
echo "2. Successfully seeded 3 test candidates (Alice Zeta, Bob Alpha, Charlie Beta).\n";

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

// Helper to run manage.php capturing output and checking effects
function run_manage_get($get_params) {
    $_GET = $get_params;
    $_POST = [];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    ob_start();
    include 'manage.php';
    return ob_get_clean();
}

function run_manage_post($post_params) {
    $_GET = [];
    $_POST = $post_params;
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    ob_start();
    include 'manage.php';
    return ob_get_clean();
}

// -------------------------------------------------------------------------
// TEST 1: List All EOIs (Empty Filters)
// -------------------------------------------------------------------------
echo "\nTest 1: List All EOIs (Empty Filters)\n";
$out1 = run_manage_get([]);
assert_test(strpos($out1, 'Found 3 application record(s)') !== false, "All 3 records retrieved when no filters are set.");
assert_test(strpos($out1, 'Alice Zeta') !== false, "Alice Zeta is listed.");
assert_test(strpos($out1, 'Bob Alpha') !== false, "Bob Alpha is listed.");
assert_test(strpos($out1, 'Charlie Beta') !== false, "Charlie Beta is listed.");

// -------------------------------------------------------------------------
// TEST 2: Filter by Job Reference
// -------------------------------------------------------------------------
echo "\nTest 2: Filter by Job Reference ('WD001')\n";
$out2 = run_manage_get(['job_ref' => 'WD001', 'action' => 'filter']);
assert_test(strpos($out2, 'Found 2 application record(s)') !== false, "Filtering by WD001 correctly returns 2 candidates.");
assert_test(strpos($out2, 'Alice Zeta') !== false, "Alice Zeta is listed for WD001.");
assert_test(strpos($out2, 'Charlie Beta') !== false, "Charlie Beta is listed for WD001.");
assert_test(strpos($out2, 'Bob Alpha') === false, "Bob Alpha (SE002) is correctly excluded.");

// -------------------------------------------------------------------------
// TEST 3: Search by Candidate Name
// -------------------------------------------------------------------------
echo "\nTest 3: Search by First/Last Name\n";
$out3_fname = run_manage_get(['first_name' => 'Bob', 'action' => 'filter']);
assert_test(strpos($out3_fname, 'Bob Alpha') !== false, "Search by first name 'Bob' returns Bob Alpha.");
assert_test(strpos($out3_fname, 'Alice Zeta') === false, "Search by first name 'Bob' excludes Alice Zeta.");

$out3_lname = run_manage_get(['last_name' => 'Beta', 'action' => 'filter']);
assert_test(strpos($out3_lname, 'Charlie Beta') !== false, "Search by last name 'Beta' returns Charlie Beta.");

// -------------------------------------------------------------------------
// TEST 4: Sort Results
// -------------------------------------------------------------------------
echo "\nTest 4: Sort Results by Last Name\n";
// Let's run query directly to see sorting order, as testing order inside HTML table requires parsing, 
// but we can also check their relative position in the HTML string.
$out4_sort = run_manage_get(['sort_by' => 'last_name', 'action' => 'filter']);
$pos_bob = strpos($out4_sort, 'Bob Alpha'); // last_name: Alpha
$pos_charlie = strpos($out4_sort, 'Charlie Beta'); // last_name: Beta
$pos_alice = strpos($out4_sort, 'Alice Zeta'); // last_name: Zeta

assert_test($pos_bob < $pos_charlie, "Bob Alpha (Alpha) appears before Charlie Beta (Beta).");
assert_test($pos_charlie < $pos_alice, "Charlie Beta (Beta) appears before Alice Zeta (Zeta).");
echo "  Sorted order verified: Alpha -> Beta -> Zeta.\n";

// -------------------------------------------------------------------------
// TEST 5: Change EOI Status (POST)
// -------------------------------------------------------------------------
echo "\nTest 5: Change EOI Status to 'Final'\n";
$out5 = run_manage_post([
    'change_status' => '1',
    'eoi_number' => '1', // Alice Zeta (currently 'New')
    'new_status' => 'Final'
]);

$db_status_check = mysqli_query($conn, "SELECT status FROM eoi WHERE EOInumber = 1");
$status_row = mysqli_fetch_assoc($db_status_check);
assert_test($status_row['status'] === 'Final', "Alice Zeta's status updated successfully to 'Final'.");
assert_test(strpos($out5, "Successfully updated EOI #1 status to &#039;Final&#039;.") !== false, "Success notification is displayed on dashboard.");

// -------------------------------------------------------------------------
// TEST 6: Bulk Delete by Job Reference
// -------------------------------------------------------------------------
echo "\nTest 6: Bulk Delete by Job Reference ('WD001')\n";
$out6 = run_manage_get([
    'action' => 'delete_by_ref',
    'job_ref' => 'WD001'
]);

$db_count_check = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM eoi");
$count_row = mysqli_fetch_assoc($db_count_check);
assert_test($count_row['cnt'] == 1, "Only 1 record remains in database (deleted 2 matching WD001).");

$db_remain_check = mysqli_query($conn, "SELECT * FROM eoi");
$remain_row = mysqli_fetch_assoc($db_remain_check);
assert_test($remain_row['job_ref'] === 'SE002' && $remain_row['first_name'] === 'Bob', "Remaining record is Bob Alpha (SE002).");
assert_test(strpos($out6, "Successfully deleted 2 application record(s)") !== false, "Deletion notification confirms 2 rows deleted.");

echo "\n========================================================\n";
echo "MANAGEMENT TESTS SUMMARY\n";
echo "========================================================\n";
echo "Total Tests Run: " . ($tests_passed + $tests_failed) . "\n";
echo "Passed: $tests_passed\n";
echo "Failed: $tests_failed\n";
echo "========================================================\n";
?>
