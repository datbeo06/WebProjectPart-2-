<?php
// jobs.php
// Jobs directory displaying active positions and handling keyword searches
// Group Nick-Thu-1030-G03

$page_title = "Jobs — SolarCore Energy";

$page_style = '
    <style>
        /* Salary highlight pill — page-specific decoration */
        .job-card p strong {
            color: #0F4C3A;
        }

        /* Job reference number badge */
        .job-reference {
            display: inline-block;
            background-color: #FEF3C7;
            color: #D97706;
            padding: 0.15rem 0.6rem;
            border-radius: 4px;
            font-family: \'Courier New\', monospace;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        /* Subtle style override for job cards */
        .job-card {
            position: relative;
        }
    </style>
';

include 'header.inc';
include 'nav.inc';

require_once 'settings.php';

// Retrieve and sanitize search term if provided
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
?>

    <main class="content-container">
        <section class="jobs-intro">
            <h2>Current Opportunities at SolarCore Energy</h2>
            <p>
                Join our growing team of engineers, developers, and technicians as we power
                Australia's transition to clean energy. We deliver solar, wind, and hydro
                solutions across Victoria, New South Wales, and Queensland.
            </p>
            <p>
                Please review the current opportunities below and use the job reference number
                (e.g. <strong>WD001</strong>, <strong>SE002</strong>, <strong>SC001</strong>) when completing your
                application on the Apply page.
            </p>
        </section>

        <!-- JOB SEARCH SECTION -->
        <section class="jobs-search-section" style="background-color: #ECFDF5; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid #F59E0B; box-shadow: 0 2px 10px rgba(15,76,58,0.05);">
            <form method="GET" action="jobs.php" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                <label for="search" style="font-weight: 700; color: #0F4C3A; font-size: 1.05rem;">Search Positions:</label>
                <input type="text" id="search" name="search" placeholder="e.g. Developer, Engineer, Analyst, SC001..." value="<?= htmlspecialchars($search_term) ?>" style="flex: 1; min-width: 200px; padding: 0.6rem 1rem; border: 1px solid #10B981; border-radius: 4px; font-size: 1rem;">
                <button type="submit" style="background-color: #0F4C3A; color: white; border: none; padding: 0.6rem 1.5rem; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 1rem; transition: background 0.2s;">Search</button>
                <?php if (!empty($search_term)): ?>
                    <a href="jobs.php" style="color: #D97706; text-decoration: none; font-weight: 600; margin-left: 0.5rem; transition: color 0.2s;">Clear Search</a>
                <?php endif; ?>
            </form>
        </section>

        <!-- WHY WORK AT SOLARCORE (ASIDE) -->
        <aside class="job-aside" aria-label="Why work at SolarCore">
            <h2>Why Work at SolarCore?</h2>
            <p>
                Build a meaningful career with a Melbourne-based renewable energy company that
                is making a real difference for the climate.
            </p>
            <ul>
                <li>Purpose-driven work in clean energy</li>
                <li>Hybrid &amp; flexible working arrangements</li>
                <li>Annual training &amp; certification budget</li>
                <li>Salary packaging &amp; novated leasing</li>
                <li>Discounted home solar &amp; battery installation</li>
                <li>Diverse, inclusive, team-focused culture</li>
            </ul>
        </aside>

        <!-- JOB CARDS — Dynamic database rendering -->
        <?php
        // Build search query safely with prepared statements
        if ($search_term !== '') {
            $query = "SELECT * FROM jobs WHERE title LIKE ? OR description LIKE ? OR job_ref = ?";
            $stmt = mysqli_prepare($conn, $query);
            $like_term = "%" . $search_term . "%";
            mysqli_stmt_bind_param($stmt, "sss", $like_term, $like_term, $search_term);
        } else {
            $query = "SELECT * FROM jobs ORDER BY job_ref ASC";
            $stmt = mysqli_prepare($conn, $query);
        }

        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <section class="job-card" id="job-<?= htmlspecialchars($row['job_ref']) ?>">
                        <h2><?= htmlspecialchars($row['title']) ?></h2>
                        <p><span class="job-reference">Reference Number:</span> <?= htmlspecialchars($row['job_ref']) ?></p>
                        <p><strong>Salary:</strong> <?= htmlspecialchars($row['salary']) ?></p>
                        <p><strong>Reporting Line:</strong> Reports to the <?= htmlspecialchars($row['reporting_to']) ?></p>

                        <h3>Short Description</h3>
                        <p><?= htmlspecialchars($row['description']) ?></p>

                        <h3>Key Responsibilities</h3>
                        <ol>
                            <?php
                            $resps = explode(';', $row['responsibilities']);
                            foreach ($resps as $resp) {
                                if (trim($resp) !== '') {
                                    echo "<li>" . htmlspecialchars(trim($resp)) . "</li>";
                                }
                            }
                            ?>
                        </ol>

                        <h3>Essential Requirements</h3>
                        <ul>
                            <?php
                            $ess = explode(';', $row['essential_requirements']);
                            foreach ($ess as $item) {
                                if (trim($item) !== '') {
                                    echo "<li>" . htmlspecialchars(trim($item)) . "</li>";
                                }
                            }
                            ?>
                        </ul>

                        <?php if (!empty($row['preferable_requirements'])): ?>
                            <!-- Aside element for preferable requirements — floats right (handled by style.css) -->
                            <aside class="job-aside-card" style="float: right; width: 25%; margin-left: 20px; background-color: #F9FAFB; padding: 1rem; border-left: 3px solid #10B981; border-radius: 4px; font-size: 0.9rem;" aria-label="Preferable skills for <?= htmlspecialchars($row['title']) ?>">
                                <h4 style="color: #0F4C3A; margin-top: 0; margin-bottom: 0.5rem; font-size: 0.95rem; font-weight: 700;">Preferable Skills</h4>
                                <ul style="margin: 0; padding-left: 1.2rem;">
                                    <?php
                                    $prefs = explode(';', $row['preferable_requirements']);
                                    foreach ($prefs as $pref) {
                                        if (trim($pref) !== '') {
                                            echo "<li>" . htmlspecialchars(trim($pref)) . "</li>";
                                        }
                                    }
                                    ?>
                                </ul>
                            </aside>
                        <?php endif; ?>

                        <div style="clear: both; margin-top: 1.5rem;">
                            <a href="apply.php?ref=<?= htmlspecialchars($row['job_ref']) ?>" style="display: inline-block; padding: 0.75rem 2rem; background-color: #F59E0B; color: #0F4C3A; text-decoration: none; border-radius: 6px; font-weight: 700; transition: background 0.2s; border: 1px solid #D97706;" class="btn-apply">
                                Apply for this Position
                            </a>
                        </div>
                    </section>
                    <?php
                }
            } else {
                ?>
                <section class="job-card" style="text-align: center; padding: 3rem 1rem; border-left: 4px solid #EF4444;">
                    <h2 style="color: #EF4444;">No Positions Found</h2>
                    <p style="color: #4B5563; font-size: 1.1rem; margin-top: 0.5rem;">We couldn't find any job matches for "<strong><?= htmlspecialchars($search_term) ?></strong>".</p>
                    <p style="margin-top: 1.5rem;">
                        <a href="jobs.php" style="display: inline-block; padding: 0.6rem 1.5rem; background-color: #0F4C3A; color: white; text-decoration: none; border-radius: 4px; font-weight: 600;">Browse All Jobs</a>
                    </p>
                </section>
                <?php
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "<p class='error'>Failed to prepare the search statement.</p>";
        }
        ?>

        <section class="application-note" style="margin-top: 2.5rem; clear: both;">
            <h2>How to Apply</h2>
            <p>
                To apply for one of these roles, click the "Apply for this Position" button above, or visit the <a href="apply.php">Apply</a> page
                and enter the correct reference number (e.g. <strong>WD001</strong>, <strong>SE002</strong>, <strong>SC001</strong>). We review applications
                on a rolling basis &mdash; early applicants are encouraged.
            </p>
        </section>
    </main>

<?php
include 'footer.inc';
?>
