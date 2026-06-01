<?php
// jobs.php
// Jobs directory displaying all active positions
// Group Nick-Thu-1030-G03

/* CSS */

$page_title = "Jobs — SolarCore Energy";

$page_style = '
    <style>

        /* Job card styling */
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

        .job-card h2 {
            color: #0F4C3A;
            margin-bottom: 0.6rem;
        }

        .job-card h3 {
            margin-top: 1.4rem;
            margin-bottom: 0.5rem;
            color: #0F4C3A;
        }

        .job-card p {
            line-height: 1.6;
            margin-bottom: 0.8rem;
        }

        .job-card li {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        /* Better layout for requirements + preferable skills */
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
            margin-bottom: 0.75rem;
            line-height: 1.5;
        }

        /* Apply button */
        .btn-apply {
            transition: all 0.2s ease;
        }

        .btn-apply:hover {
            background-color: #D97706 !important;
            color: white !important;
            transform: translateY(-2px);
        }

        /* Mobile responsive */
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
?>

<main class="content-container">

    <!-- INTRO -->
    <section class="jobs-intro">
        <h2>Current Opportunities at SolarCore Energy</h2>

        <p>
            Join our growing team of engineers, developers, and technicians as we power
            Australia\'s transition to clean energy. We deliver solar, wind, and hydro
            solutions across Victoria, New South Wales, and Queensland.
        </p>

        <p>
            Please review the current opportunities below and use the job reference number
            (e.g. <strong>WD001</strong>, <strong>SE002</strong>, <strong>SC001</strong>)
            when completing your application on the Apply page.
        </p>
    </section>

    <!-- SEARCH -->
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
                style="flex: 1; min-width: 200px; padding: 0.6rem 1rem;
                border: 1px solid #10B981; border-radius: 4px; font-size: 1rem;">

            <button type="submit"
                style="background-color: #0F4C3A; color: white; border: none;
                padding: 0.6rem 1.5rem; border-radius: 4px; cursor: pointer;
                font-weight: 600; font-size: 1rem;">
                Search
            </button>
        </form>
    </section>

    <!-- COMPANY BENEFITS -->
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

    <!-- JOBS -->
    <?php

    $query = "SELECT * FROM jobs ORDER BY job_ref ASC";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

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

                    <!-- FLEX LAYOUT -->
                    <div class="job-details-layout">

                        <!-- LEFT SIDE -->
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

                        <!-- RIGHT SIDE -->
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
                style="text-align: center; padding: 3rem 1rem;
                border-left: 4px solid #F59E0B;">

                <h2 style="color: #F59E0B;">No Positions Available</h2>

                <p style="color: #4B5563; font-size: 1.1rem; margin-top: 0.5rem;">
                    We currently have no open positions, but we encourage you
                    to check back regularly for new opportunities.
                </p>

            </section>

            <?php
        }

        mysqli_stmt_close($stmt);

    } else {

        echo "<p class=\'error\'>Failed to prepare the search statement.</p>";

    }
    ?>

    <!-- APPLY NOTE -->
    <section class="application-note"
        style="margin-top: 2.5rem; clear: both;">

        <h2>How to Apply</h2>

        <p>
            To apply for one of these roles, click the
            "Apply for this Position" button above, or visit the
            <a href="apply.php">Apply</a> page and enter the correct
            reference number.
        </p>

    </section>

</main>

<?php
include 'footer.inc';
?>