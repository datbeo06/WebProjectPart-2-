<?php
// search.php
// Job search results page handling keyword searches from jobs.php form
// Group Nick-Thu-1030-G03

$page_title = "Search Results — Jobs at SolarCore Energy";

$page_style = '
    <style>
        .job-card p strong {
            color: #0F4C3A;
            font-weight: 700;
        }

        .job-reference {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #FEF3C7, #FDE68A);
            color: #B45309;
            padding: 0.2rem 0.7rem;
            border-radius: 999px;
            font-family: \'Courier New\', monospace;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            border: 1px solid #FCD34D;
        }

        .job-card {
            position: relative;
            border: 1px solid #D1FAE5;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .job-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 18px rgba(15, 76, 58, 0.08);
        }

        .job-card h2,
        .job-card h3 {
            color: #0F4C3A;
        }

        .job-card p,
        .job-card li {
            line-height: 1.55;
        }

        .job-details-layout {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
            margin-top: 1rem;
        }

        .job-main-content {
            flex: 1;
            min-width: 0;
        }

        .job-aside-card {
            width: 280px;
            background-color: #F9FAFB;
            padding: 1rem;
            border-left: 3px solid #10B981;
            border-radius: 6px;
            font-size: 0.92rem;
            flex-shrink: 0;
            box-shadow: 0 2px 10px rgba(15,76,58,0.05);
        }

        .job-aside-card h4 {
            color: #0F4C3A;
            margin-top: 0;
            margin-bottom: 0.75rem;
            font-size: 1rem;
            font-weight: 700;
        }

        .job-aside-card ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        .job-aside-card li {
            margin-bottom: 0.7rem;
        }

        .btn-apply {
            transition: all 0.2s ease;
        }

        .btn-apply:hover {
            background-color: #D97706 !important;
            color: white !important;
            transform: translateY(-2px);
        }

        @media (max-width: 900px) {
            .job-details-layout {
                flex-direction: column;
            }

            .job-aside-card {
                width: 100%;
            }
        }
    </style>
';

include 'header.inc';
include 'nav.inc';

require_once 'settings.php';

$search_term = isset($_POST['search']) ? trim($_POST['search']) : (isset($_GET['search']) ? trim($_GET['search']) : '');
?>

<main class="content-container">

    <section class="jobs-intro">
        <h2>Search Results</h2>

        <p>
            You searched for: <strong><?= htmlspecialchars($search_term) ?></strong>
        </p>

        <p>
            Below are the matching positions. Use the job reference number
            when completing your application on the Apply page.
        </p>
    </section>

    <section class="jobs-search-section"
        style="background-color: #ECFDF5; padding: 1.5rem; border-radius: 8px;
        margin-bottom: 2rem; border-left: 4px solid #F59E0B;
        box-shadow: 0 2px 10px rgba(15,76,58,0.05);">

        <form method="POST" action="search.php"
            style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">

            <label for="search"
                style="font-weight: 700; color: #0F4C3A; font-size: 1.05rem;">
                Search Positions:
            </label>

            <input type="text"
                id="search"
                name="search"
                placeholder="e.g. Developer, Engineer, Analyst, SC001..."
                value="<?= htmlspecialchars($search_term) ?>"
                style="flex: 1; min-width: 200px; padding: 0.6rem 1rem;
                border: 1px solid #10B981; border-radius: 4px; font-size: 1rem;">

            <button type="submit"
                style="background-color: #0F4C3A; color: white; border: none;
                padding: 0.6rem 1.5rem; border-radius: 4px; cursor: pointer;
                font-weight: 600; font-size: 1rem;">
                Search
            </button>

            <a href="jobs.php"
                style="color: #D97706; text-decoration: none; font-weight: 600;
                margin-left: 0.5rem;">
                View All Jobs
            </a>

        </form>
    </section>

    <?php
    if ($search_term === '') {
        ?>

        <section class="job-card"
            style="text-align: center; padding: 3rem 1rem; border-left: 4px solid #F59E0B;">

            <h2 style="color: #F59E0B;">Please Enter a Search Term</h2>

            <p style="color: #4B5563; font-size: 1.1rem; margin-top: 0.5rem;">
                Enter keywords, job title, or a job reference number to search available positions.
            </p>

            <p style="margin-top: 1.5rem;">
                <a href="jobs.php"
                    style="display: inline-block; padding: 0.6rem 1.5rem;
                    background-color: #0F4C3A; color: white; text-decoration: none;
                    border-radius: 4px; font-weight: 600;">
                    Browse All Jobs
                </a>
            </p>

        </section>

        <?php
    } else {

        $query = "SELECT * FROM jobs 
                  WHERE title LIKE ? 
                  OR description LIKE ? 
                  OR job_ref = ? 
                  ORDER BY job_ref ASC";

        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {

            $like_term = "%" . $search_term . "%";

            mysqli_stmt_bind_param($stmt, "sss", $like_term, $like_term, $search_term);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {

                echo "<p style='color: #4B5563; font-weight: 600; font-size: 0.95rem; margin-bottom: 2rem;'>Found " . mysqli_num_rows($result) . " matching position(s).</p>";

                while ($row = mysqli_fetch_assoc($result)) {
                    ?>

                    <section class="job-card" id="job-<?= htmlspecialchars($row['job_ref']) ?>">

                        <h2><?= htmlspecialchars($row['title']) ?></h2>

                        <p>
                            <span class="job-reference">Reference Number:</span>
                            <?= htmlspecialchars($row['job_ref']) ?>
                        </p>

                        <p>
                            <strong>Salary:</strong>
                            <?= htmlspecialchars($row['salary']) ?>
                        </p>

                        <p>
                            <strong>Reporting Line:</strong>
                            Reports to the <?= htmlspecialchars($row['reporting_to']) ?>
                        </p>

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

                        <div class="job-details-layout">

                            <div class="job-main-content">

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

                                <div style="margin-top: 1.5rem;">

                                    <a href="apply.php?ref=<?= htmlspecialchars($row['job_ref']) ?>"
                                        class="btn-apply"
                                        style="display: inline-block; padding: 0.75rem 2rem;
                                        background-color: #F59E0B; color: #0F4C3A;
                                        text-decoration: none; border-radius: 6px;
                                        font-weight: 700; border: 1px solid #D97706;">
                                        Apply for this Position
                                    </a>

                                </div>

                            </div>

                            <?php if (!empty($row['preferable_requirements'])): ?>

                                <aside class="job-aside-card"
                                    aria-label="Preferable skills for <?= htmlspecialchars($row['title']) ?>">

                                    <h4>Preferable Skills</h4>

                                    <ul>
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

                        </div>

                    </section>

                    <?php
                }

            } else {
                ?>

                <section class="job-card"
                    style="text-align: center; padding: 3rem 1rem; border-left: 4px solid #EF4444;">

                    <h2 style="color: #EF4444;">No Positions Found</h2>

                    <p style="color: #4B5563; font-size: 1.1rem; margin-top: 0.5rem;">
                        We couldn't find any job matches for
                        "<strong><?= htmlspecialchars($search_term) ?></strong>".
                    </p>

                    <p style="margin-top: 1.5rem;">
                        <a href="jobs.php"
                            style="display: inline-block; padding: 0.6rem 1.5rem;
                            background-color: #0F4C3A; color: white; text-decoration: none;
                            border-radius: 4px; font-weight: 600;">
                            Browse All Jobs
                        </a>
                    </p>

                </section>

                <?php
            }

            mysqli_stmt_close($stmt);

        } else {
            echo "<p class='error'>Failed to prepare the search statement.</p>";
        }
    }
    ?>

    <section class="application-note" style="margin-top: 2.5rem; clear: both;">
        <h2>How to Apply</h2>

        <p>
            To apply for one of these roles, click the "Apply for this Position"
            button above, or visit the <a href="apply.php">Apply</a> page and enter
            the correct reference number.
        </p>
    </section>

</main>

<?php
include 'footer.inc';
?>
