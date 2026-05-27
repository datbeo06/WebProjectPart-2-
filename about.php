<?php
// about.php
// About Us page dynamically loading contributions from database
// Group Nick-Thu-1030-G03

require_once 'settings.php';

$page_title = "About Us - SolarCore Energy";
$teamMembers = [];
$teamLoadError = false;

$funFacts = [
    [
        'name' => 'Duong Danh Dat',
        'member' => 'Member A',
        'student_id' => '105928000',
        'hidden_talent' => 'Japanese calligraphy',
        'favourite_tool' => 'CSS Grid + Figma',
        'fun_fact' => 'Enjoys AI and AI development',
    ],
    [
        'name' => 'James',
        'member' => 'Member B',
        'student_id' => '105901030',
        'hidden_talent' => 'Baking',
        'favourite_tool' => 'Jira + VS Code',
        'fun_fact' => 'Learning new languages',
    ],
    [
        'name' => 'Marksamuel',
        'member' => 'Member C',
        'student_id' => '105920006',
        'hidden_talent' => 'Playing guitar',
        'favourite_tool' => 'Chrome DevTools',
        'fun_fact' => 'Plays soccer on weekends',
    ],
    [
        'name' => 'Jose Leonardo',
        'member' => 'Member D',
        'student_id' => '103641855',
        'hidden_talent' => 'Playing drums',
        'favourite_tool' => 'Visual Studio Code',
        'fun_fact' => 'Bilingual (Spanish & English)',
    ],
];

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$result = mysqli_query($conn, "SELECT * FROM about ORDER BY member_id ASC");

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $teamMembers[] = $row;
    }

    mysqli_free_result($result);
} else {
    $teamLoadError = true;
}

include 'header.inc';
include 'nav.inc';
?>

    <main>
        <div class="section-container about-page-container">

            <!-- TEAM IDENTITY, GROUP NAME + CLASS DAY/TIME -->
            <section class="about-section">
                <div class="team-intro-card">
                    <h2 class="team-intro-title">Team Identity &mdash; Group G03</h2>
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

            <!-- MEMBER CONTRIBUTIONS & QUOTES - Dynamically Loaded from DB -->
            <section class="about-section">
                <h2>Meet the Team</h2>
                <p>Each member independently developed one complete page including its CSS. Below are individual contributions and personal quotes in native languages with English translation loaded dynamically from the database.</p>

                <?php if ($teamLoadError): ?>
                    <p class="error">Failed to load team data from database.</p>
                <?php else: ?>
                    <!-- Definition list (dl/dt/dd) loaded dynamically -->
                    <dl class="contrib-definition-list">
                        <?php foreach ($teamMembers as $member): ?>
                            <?php $memberLetter = chr(65 + ((int) $member['member_id'] - 1)); ?>
                            <dt>Member <?= e($memberLetter) ?> &ndash; <?= e($member['full_name']) ?> (<?= e($member['student_id']) ?>)</dt>
                            <dd>
                                <strong>Role:</strong> <?= e($member['role']) ?>.<br>
                                <strong>Part 1 Contribution:</strong> <?= e($member['part1_contribution']) ?><br>
                                <strong>Part 2 Contribution:</strong> <?= e($member['part2_contribution']) ?><br>
                                <span class="member-quote">"<?= e($member['quote_original']) ?>"</span>
                                <span class="quote-translation"><?= e($member['quote_language']) ?> &mdash; Translation: "<?= e($member['quote_translation']) ?>"</span>
                            </dd>
                        <?php endforeach; ?>
                    </dl>
                <?php endif; ?>
            </section>

            <!-- GROUP PHOTO -->
            <section class="about-section">
                <h2>Our Team Photo</h2>
                <div class="team-grid">
                    <figure class="group-photo-figure">
                        <img src="images/team-photo.jpg" alt="The four-member SolarCore Energy student team standing together at Swinburne University">
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
                            <?php foreach ($funFacts as $fact): ?>
                                <tr>
                                    <td><strong><?= e($fact['name']) ?> (<?= e($fact['member']) ?>)</strong><br>(<?= e($fact['student_id']) ?>)</td>
                                    <td><?= e($fact['hidden_talent']) ?></td>
                                    <td><?= e($fact['favourite_tool']) ?></td>
                                    <td><?= e($fact['fun_fact']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

<?php
include 'footer.inc';
?>
