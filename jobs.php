<?php
require_once("settings.php");

$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("<p>Database connection failed.</p>");
}

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

if ($search != "") {

    $query = "SELECT * FROM jobs
              WHERE job_ref LIKE ?
              OR title LIKE ?
              OR description LIKE ?
              OR salary LIKE ?
              OR reporting_to LIKE ?
              OR responsibilities LIKE ?
              OR essential_requirements LIKE ?
              OR preferable_requirements LIKE ?
              ORDER BY title ASC";

    $stmt = mysqli_prepare($conn, $query);

    $search_term = "%" . $search . "%";

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssss",
        $search_term,
        $search_term,
        $search_term,
        $search_term,
        $search_term,
        $search_term,
        $search_term,
        $search_term
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

} else {

    $query = "SELECT * FROM jobs ORDER BY title ASC";

    $result = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Jobs | SolarCore Energy</title>

    <link rel="stylesheet" href="styles/style.css">

    <style>

        .job-search-form {
            margin: 1.5rem 0;
        }

        .job-search-form input[type="text"] {
            padding: 0.6rem;
            width: 300px;
            max-width: 100%;
        }

        .job-search-form button,
        .job-search-form a {
            padding: 0.6rem 1rem;
            margin-left: 0.4rem;
            text-decoration: none;
        }

        .job-reference {
            display: inline-block;
            background-color: #FEF3C7;
            color: #D97706;
            padding: 0.15rem 0.6rem;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        .job-card {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
        }

    </style>
</head>

<body>

<?php include("header.inc"); ?>
<?php include("nav.inc"); ?>

<main class="content-container">

    <section class="jobs-intro">

        <h2>Current Opportunities at SolarCore Energy</h2>

        <p>
            Join our growing renewable energy team and help power Australia's clean future.
        </p>

        <form method="get" action="jobs.php" class="job-search-form">

            <label for="search">Search Jobs:</label>

            <input
                type="text"
                id="search"
                name="search"
                placeholder="Search by title, reference or keyword"
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit">Search</button>

            <a href="jobs.php">Reset</a>

        </form>

    </section>

    <aside class="job-aside">

        <h2>Why Work at SolarCore?</h2>

        <ul>
            <li>Flexible hybrid working</li>
            <li>Renewable energy projects</li>
            <li>Career growth opportunities</li>
            <li>Supportive team culture</li>
            <li>Training and certifications</li>
        </ul>

    </aside>

    <?php

    if ($result && mysqli_num_rows($result) > 0) {

        while ($job = mysqli_fetch_assoc($result)) {

            echo "<section class='job-card'>";

            echo "<h2>" . htmlspecialchars($job["title"]) . "</h2>";

            echo "<p>
                    <span class='job-reference'>Reference Number:</span>
                    " . htmlspecialchars($job["job_ref"]) . "
                  </p>";

            echo "<p><strong>Salary:</strong> "
                . htmlspecialchars($job["salary"]) .
                "</p>";

            echo "<p><strong>Reporting Line:</strong> Reports to "
                . htmlspecialchars($job["reporting_to"]) .
                "</p>";

            echo "<h3>Short Description</h3>";

            echo "<p>"
                . htmlspecialchars($job["description"]) .
                "</p>";

            echo "<h3>Key Responsibilities</h3>";

            echo "<ol>";

            $responsibilities = explode(";", $job["responsibilities"]);

            foreach ($responsibilities as $item) {

                echo "<li>" . htmlspecialchars(trim($item)) . "</li>";
            }

            echo "</ol>";

            echo "<h3>Essential Requirements</h3>";

            echo "<ul>";

            $essential = explode(";", $job["essential_requirements"]);

            foreach ($essential as $item) {

                echo "<li>" . htmlspecialchars(trim($item)) . "</li>";
            }

            echo "</ul>";

            echo "<h3>Preferable Requirements</h3>";

            echo "<ul>";

            $preferable = explode(";", $job["preferable_requirements"]);

            foreach ($preferable as $item) {

                echo "<li>" . htmlspecialchars(trim($item)) . "</li>";
            }

            echo "</ul>";

            echo "</section>";
        }

    } else {

        echo "
        <section class='job-card'>
            <h2>No Jobs Found</h2>
            <p>No jobs matched your search.</p>
        </section>";
    }

    ?>

    <section class="application-note">

        <h2>How to Apply</h2>

        <p>
            Visit the <a href="apply.php">Apply</a> page and enter the correct
            job reference number when submitting your application.
        </p>

    </section>

</main>

<?php

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

include("footer.inc");

?>

</body>
</html>