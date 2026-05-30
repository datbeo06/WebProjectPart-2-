<?php
// manage.php
// HR Administrator Dashboard providing EOI listing, filtering, status updating, and record deletion
// Group Nick-Thu-1030-G03

// 1. Session verification. Protect dashboard from unauthenticated access.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'settings.php';
global $conn;

$msg_success = '';
$msg_error = '';

// 2. Handle POST action: Change status of an EOI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
    $eoi_id = isset($_POST['eoi_number']) ? (int)$_POST['eoi_number'] : 0;
    $new_status = isset($_POST['new_status']) ? trim($_POST['new_status']) : '';
    
    if ($eoi_id > 0 && in_array($new_status, ['New', 'Current', 'Final'])) {
        $update_stmt = mysqli_prepare($conn, "UPDATE eoi SET status = ? WHERE EOInumber = ?");
        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "si", $new_status, $eoi_id);
            if (mysqli_stmt_execute($update_stmt)) {
                $msg_success = "Successfully updated EOI #$eoi_id status to '$new_status'.";
            } else {
                $msg_error = "Failed to update EOI status in the database.";
            }
            mysqli_stmt_close($update_stmt);
        } else {
            $msg_error = "Database prepare error during status update.";
        }
    } else {
        $msg_error = "Invalid parameters provided for status update.";
    }
}

// 3. Handle GET action: Delete all EOIs by a given Job Reference
if (isset($_GET['action']) && $_GET['action'] === 'delete_by_ref') {
    $del_job_ref = isset($_GET['job_ref']) ? trim($_GET['job_ref']) : '';
    
    if ($del_job_ref === '') {
        $msg_error = "Please specify a Job Reference number in the filter card to delete records.";
    } else if (!preg_match('/^[A-Za-z0-9]{5}$/', $del_job_ref)) {
        $msg_error = "Invalid Job Reference format. Must be exactly 5 alphanumeric characters.";
    } else {
        $del_stmt = mysqli_prepare($conn, "DELETE FROM eoi WHERE job_ref = ?");
        if ($del_stmt) {
            mysqli_stmt_bind_param($del_stmt, "s", $del_job_ref);
            if (mysqli_stmt_execute($del_stmt)) {
                $deleted_count = mysqli_stmt_affected_rows($del_stmt);
                $msg_success = "Successfully deleted $deleted_count application record(s) matching Job Reference '$del_job_ref'.";
            } else {
                $msg_error = "Failed to delete application records from database.";
            }
            mysqli_stmt_close($del_stmt);
        } else {
            $msg_error = "Database prepare error during records deletion.";
        }
    }
}

// 4. Retrieve Filters from GET query parameters
$filter_job_ref = isset($_GET['job_ref']) ? trim($_GET['job_ref']) : '';
$filter_fname = isset($_GET['first_name']) ? trim($_GET['first_name']) : '';
$filter_lname = isset($_GET['last_name']) ? trim($_GET['last_name']) : '';
$sort_by = isset($_GET['sort_by']) ? trim($_GET['sort_by']) : 'EOInumber';

$allowed_sorts = ['EOInumber', 'last_name', 'status', 'submitted_at'];
if (!in_array($sort_by, $allowed_sorts)) {
    $sort_by = 'EOInumber';
}

// Build dynamic WHERE clause based on filters safely with prepared statements
$where_clauses = [];
$params = [];
$types = '';

if ($filter_job_ref !== '') {
    $where_clauses[] = 'job_ref = ?';
    $params[] = $filter_job_ref;
    $types .= 's';
}
if ($filter_fname !== '') {
    $where_clauses[] = 'first_name LIKE ?';
    $params[] = '%' . $filter_fname . '%';
    $types .= 's';
}
if ($filter_lname !== '') {
    $where_clauses[] = 'last_name LIKE ?';
    $params[] = '%' . $filter_lname . '%';
    $types .= 's';
}

$sql_query = "SELECT * FROM eoi";
if (!empty($where_clauses)) {
    $sql_query .= " WHERE " . implode(' AND ', $where_clauses);
}
$sql_query .= " ORDER BY $sort_by ASC";

// 5. Compile layout options
$page_title = "HR Dashboard — SolarCore Energy";


include 'header.inc';
include 'nav.inc';
?>

    <main>
        <div class="dashboard-container">
            
            <div class="dashboard-header">
                <h2>Recruitment Administration Portal</h2>
                <div class="user-badge">
                    Logged in: <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>
                </div>
            </div>

            <!-- Alerts for user actions -->
            <?php if ($msg_success !== ''): ?>
                <div class="alert alert-success">
                    &#10004; <?= htmlspecialchars($msg_success) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($msg_error !== ''): ?>
                <div class="alert alert-error">
                    &#9888; <?= htmlspecialchars($msg_error) ?>
                </div>
            <?php endif; ?>

            <!-- FILTER CARD -->
            <section class="admin-card" aria-labelledby="filter-heading">
                <h3 id="filter-heading">Filter &amp; Manage Applications</h3>
                
                <form method="GET" action="manage.php">
                    <div class="filter-grid">
                        <div class="filter-group">
                            <label for="job_ref">Job Reference:</label>
                            <input type="text" id="job_ref" name="job_ref" placeholder="e.g. WD001" value="<?= htmlspecialchars($filter_job_ref) ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first_name" name="first_name" placeholder="e.g. Duong" value="<?= htmlspecialchars($filter_fname) ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" placeholder="e.g. Nguyen" value="<?= htmlspecialchars($filter_lname) ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label for="sort_by">Sort Results By:</label>
                            <select id="sort_by" name="sort_by">
                                <option value="EOInumber" <?php if ($sort_by === 'EOInumber') echo 'selected'; ?>>EOI Number</option>
                                <option value="last_name" <?php if ($sort_by === 'last_name') echo 'selected'; ?>>Last Name</option>
                                <option value="status" <?php if ($sort_by === 'status') echo 'selected'; ?>>Recruitment Status</option>
                                <option value="submitted_at" <?php if ($sort_by === 'submitted_at') echo 'selected'; ?>>Submission Date</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" name="action" value="filter" class="btn-admin btn-filter">Apply Filters</button>
                        
                        <!-- Delete all by job reference button requires confirmation -->
                        <button type="submit" name="action" value="delete_by_ref" class="btn-admin btn-delete" 
                                onclick="return confirm('WARNING: Are you sure you want to permanently delete ALL applications matching the Job Reference \'<?= htmlspecialchars($filter_job_ref) ?>\'? This action cannot be undone.');">
                            Delete by Job Ref
                        </button>
                        
                        <a href="manage.php" class="btn-clear">Reset Dashboard</a>
                    </div>
                </form>
            </section>

            <!-- APPLICATIONS RESULTS TABLE -->
            <section class="admin-card" style="border-top-color: #10B981;" aria-labelledby="results-heading">
                <h3 id="results-heading">Expression of Interest (EOI) Records</h3>
                
                <?php
                // Execute dynamic filter query safely
                $stmt = mysqli_prepare($conn, $sql_query);
                if ($stmt) {
                    if ($types !== '') {
                        mysqli_stmt_bind_param($stmt, $types, ...$params);
                    }
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $total_records = mysqli_num_rows($result);
                    
                    echo "<p style='margin-top: 0; color: #4B5563; font-weight: 600; font-size: 0.9rem;'>Found $total_records application record(s) matching your criteria.</p>";
                    
                    if ($total_records > 0): ?>
                        <div class="table-responsive">
                            <table class="admin-table" aria-label="Recruitment expressions of interest list">
                                <thead>
                                    <tr>
                                        <th scope="col">EOI #</th>
                                        <th scope="col">Job Ref</th>
                                        <th scope="col">Candidate Name</th>
                                        <th scope="col">Date of Birth</th>
                                        <th scope="col">Gender</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Skills</th>
                                        <th scope="col">Other Skills</th>
                                        <th scope="col">Current Status</th>
                                        <th scope="col" style="text-align: center;">Modify Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($row['EOInumber']) ?></strong></td>
                                            <td><span style="font-family: monospace; font-weight: 700; background: #F3F4F6; padding: 0.15rem 0.4rem; border-radius: 4px;"><?= htmlspecialchars($row['job_ref']) ?></span></td>
                                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                            <td><?= htmlspecialchars($row['dob']) ?></td>
                                            <td><?= htmlspecialchars($row['gender']) ?></td>
                                            <td><?= htmlspecialchars($row['street'] . ', ' . $row['suburb'] . ' ' . $row['state'] . ' ' . $row['postcode']) ?></td>
                                            <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>" style="color: #0F4C3A; text-decoration: underline;"><?= htmlspecialchars($row['email']) ?></a></td>
                                            <td><?= htmlspecialchars($row['phone']) ?></td>
                                            <td>
                                                <span style="font-size: 0.78rem; display: block; max-width: 150px; line-height: 1.3;">
                                                    <?= htmlspecialchars($row['skills']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.78rem; max-width: 150px; max-height: 60px; overflow-y: auto; line-height: 1.3; font-style: italic;">
                                                    <?= htmlspecialchars($row['other_skills'] !== '' ? $row['other_skills'] : 'None') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?= htmlspecialchars($row['status']) ?>">
                                                    <?= htmlspecialchars($row['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <!-- Action form to modify status dynamically -->
                                                <form method="POST" action="manage.php" class="update-form">
                                                    <input type="hidden" name="eoi_number" value="<?= htmlspecialchars($row['EOInumber']) ?>">
                                                    <select name="new_status" aria-label="Select new status for EOI #<?= htmlspecialchars($row['EOInumber']) ?>">
                                                        <option value="New" <?php if ($row['status'] === 'New') echo 'selected'; ?>>New</option>
                                                        <option value="Current" <?php if ($row['status'] === 'Current') echo 'selected'; ?>>Current</option>
                                                        <option value="Final" <?php if ($row['status'] === 'Final') echo 'selected'; ?>>Final</option>
                                                    </select>
                                                    <button type="submit" name="change_status" class="btn-update">Update</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem 1rem; color: #6B7280; font-size: 1.1rem; background-color: #F9FAFB; border-radius: 6px; border: 1px dashed #D1D5DB;">
                            &#128065; No applications found matching the selected filters.
                        </div>
                    <?php endif;
                    mysqli_stmt_close($stmt);
                } else {
                    echo "<p class='error'>Failed to retrieve records from the database due to a query syntax error.</p>";
                }
                ?>
            </section>
        </div>
    </main>

<?php
include 'footer.inc';
?>
