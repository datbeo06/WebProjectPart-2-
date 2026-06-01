<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// about.php
// About Us page dynamically loading contributions from database
// Group Nick-Thu-1030-G03

$page_title = "About Us — SolarCore Energy";

$page_style = '
    <style>
        .about-header-title {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 700;
            color: white;
            letter-spacing: -0.5px;
        }

        @media (max-width: 700px) {
            .about-header-title {
                font-size: 1.6rem;
            }
        }
    </style>
';

include 'header.inc';
include 'nav.inc';
?>

    <main>
        <div class="section-container" style="padding: 2rem 1.5rem 3rem;">

            <!-- TEAM IDENTITY, GROUP NAME + CLASS DAY/TIME -->
            <section class="about-section">
                <div class="team-intro-card">
                    <h2 style="margin-top: 0;">Team Identity &mdash; Group G03</h2>
                    <!-- Nested list requirement -->
                    <ul>
                        <li><strong>Class:</strong> Nick &mdash; Thursday 10:30 &mdash; Group G03</li>
                        <li><strong>Class day and time:</strong>
                            <ul>
                                <li>Thursday Lab: 10:30 &mdash; 13:30</li>
                                <li>Friday team sync: 11:00 (Discord)</li>
                            </ul>
                        </li>
                        <li><strong>Industry:</strong> Renewable Energy (Solar, Wind, Hydro)</li>
                        <li><strong>Topic:</strong> Sustainable Energy Solutions Company
                            <ul>
                                <li>Strengthening the technology team</li>
                                <li>Promoting clean energy solutions</li>
                                <li>Public engagement and project information</li>
                            </ul>
                        </li>
                        <li><strong>Project:</strong> Recruitment website for SolarCore Energy</li>
                    </ul>
                </div>
            </section>

            <!-- MEMBER CONTRIBUTIONS & QUOTES — Dynamically Loaded from DB -->
            <section class="about-section">
                <h2>Meet the Team</h2>
                <p>Each member independently developed one complete page including its CSS. Below are individual contributions and personal quotes in native languages with English translation loaded dynamically from the database.</p>

                <!-- Definition list (dl/dt/dd) loaded dynamically -->
                <dl class="contrib-definition-list">
                    <?php
                    require_once 'settings.php';
                    
                    $query = "SELECT * FROM about ORDER BY member_id ASC";
                    $result = mysqli_query($conn, $query);
                    
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $member_label = "Member " . chr(65 + ($row['member_id'] - 1)) . " &ndash; " . htmlspecialchars($row['full_name']) . " (" . htmlspecialchars($row['student_id']) . ")";
                            ?>
                            <dt><?= $member_label ?></dt>
                            <dd>
                                <strong>Role:</strong> <?= htmlspecialchars($row['role']) ?>.<br>
                                <strong>Part 1 Contribution:</strong> <?= htmlspecialchars($row['part1_contribution']) ?><br>
                                <strong>Part 2 Contribution:</strong> <?= htmlspecialchars($row['part2_contribution']) ?><br>
                                <span class="member-quote">"<?= htmlspecialchars($row['quote_original']) ?>"</span>
                                <span class="quote-translation"><?= htmlspecialchars($row['quote_language']) ?> &mdash; Translation: "<?= htmlspecialchars($row['quote_translation']) ?>"</span>
                            </dd>
                            <?php
                        }
                        mysqli_free_result($result);
                    } else {
                        echo "<p class='error'>Failed to load team data from database.</p>";
                    }
                    ?>
                </dl>
            </section>

            <!-- GROUP PHOTO -->
            <section class="about-section">
                <h2>Our Team Photo</h2>
                <div class="team-grid">
                    <figure class="group-photo-figure">
                        <img src="images/team-photo.png" alt="The four-member SolarCore Energy student team standing together at Swinburne University">
                        <figcaption>SolarCore Energy team &mdash; Swinburne University, May 2026</figcaption>
                    </figure>
                    <div>
                        <p><strong>About our company:</strong> SolarCore Energy is a Melbourne-based renewable energy company founded in 2018, delivering solar, wind, and hydro solutions across Australia.</p>
                        <p><strong>Collaboration:</strong> We use GitHub for version control with pull request reviews, Jira for sprint planning across two sprints, and Discord for daily team communication.</p>
                    </div>
                </div>
            </section>

            <!-- FUN FACTS TABLE -->
            <section class="about-section">
                <h2>Fun Facts About the Team</h2>
                <div class="table-responsive">
                    <table class="fun-facts-table">
                    <caption>Learn About Our SolarCore Team</caption>
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Hidden Talent</th>
                                <th>Favourite Tool</th>
                                <th>Fun Fact</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Duong Danh Dat (Member A)</strong><br>(105928000)</td>
                                <td>Japanese calligraphy</td>
                                <td>CSS Grid + Figma</td>
                                <td>Enjoys AI and AI development</td>
                            </tr>
                            <tr>
                                <td><strong>James (Member B)</strong><br>(105901030)</td>
                                <td>Baking</td>
                                <td>Jira + VS Code</td>
                                <td>Learning new languages</td>
                            </tr>
                            <tr>
                                <td><strong>Marksamuel (Member C)</strong><br>(105920006)</td>
                                <td>Playing guitar</td>
                                <td>Chrome DevTools</td>
                                <td>Plays soccer on weekends</td>
                            </tr>
                            <tr>
                                <td><strong>Jose Leonardo (Member D)</strong><br>(103641855)</td>
                                <td>Playing drums</td>
                                <td>Visual Studio Code</td>
                                <td>Bilingual (Spanish &amp; English)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

<?php
include 'footer.inc';
?>
