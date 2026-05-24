<?php
// apply.php
// Application EOI Form for SolarCore Energy
// Group Nick-Thu-1030-G03

$page_title = "Apply — SolarCore Energy";

$page_style = '
    <style>
        /* Required-field marker after each label */
        .application-form label::after {
            content: "";
        }

        /* Visual grouping for fieldset legends */
        fieldset {
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            padding: 0.75rem 1rem 1rem;
            margin-bottom: 1.2rem;
            background-color: #F9FAFB;
        }

        legend {
            font-weight: 600;
            color: #0F4C3A;
            padding: 0 0.5rem;
        }

        .radio-group,
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem 1.5rem;
        }

        .radio-group label,
        .checkbox-group label {
            font-weight: 400;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0;
        }

        /* Stack the two action buttons side-by-side on wider screens */
        @media (min-width: 540px) {
            .application-form button[type="submit"],
            .application-form button[type="reset"] {
                width: auto;
                min-width: 180px;
                margin-right: 0.75rem;
            }
        }
    </style>
';

include 'header.inc';
include 'nav.inc';

// Pre-populate job reference from URL GET parameter if redirecting from jobs page
$job_ref_prefill = isset($_GET['ref']) ? trim($_GET['ref']) : '';
?>

    <main>
        <section class="section-padded">
            <div class="section-container">

                <h1 style="text-align: center; margin-bottom: 1.5rem;">Job Application &mdash; Expression of Interest</h1>

                <section class="apply-intro">
                    <h2>Application Information</h2>
                    <p>
                        Please complete the application form below to apply for a position at SolarCore Energy.
                        We are looking for talented engineers, developers, and clean-energy professionals to
                        help us power Australia's transition to renewable energy.
                    </p>
                    <p>
                        Enter the job reference number from the <a href="jobs.php">Jobs page</a>
                        (<strong>WD001</strong> for Full-Stack Web Developer, <strong>SE002</strong> for Solar
                        Systems Engineer, or <strong>SC001</strong> for Solar Panel Engineer). All fields will be thoroughly verified on our servers.
                    </p>
                    <p>
                        Make sure your details are accurate. Invalid or incomplete submissions will not be accepted.
                    </p>
                </section>

                <!-- APPLICATION FORM
                     Note: All client-side validations (required, pattern) are removed
                     Email input has type="text"
                     Action redirects to process_eoi.php via POST method -->
                <form class="application-form" action="process_eoi.php" method="post" novalidate>

                    <div class="form-group">
                        <label for="job_ref">Job Reference Number:</label>
                        <input type="text" id="job_ref" name="job_ref" 
                               maxlength="5" placeholder="e.g. WD001 or SE002"
                               value="<?= htmlspecialchars($job_ref_prefill) ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first_name" name="first_name">
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dob">Date of Birth:</label>
                        <!-- Keep date picker input type -->
                        <input type="text" id="dob" name="dob" placeholder="dd/mm/yyyy">
                    </div>

                    <fieldset>
                        <legend>Gender</legend>
                        <div class="radio-group">
                            <label><input type="radio" name="gender" value="Male"> Male</label>
                            <label><input type="radio" name="gender" value="Female"> Female</label>
                            <label><input type="radio" name="gender" value="Other"> Other</label>
                        </div>
                    </fieldset>

                    <div class="form-group">
                        <label for="street">Street Address:</label>
                        <input type="text" id="street" name="street" >
                    </div>

                    <div class="form-group">
                        <label for="suburb">Suburb/Town:</label>
                        <input type="text" id="suburb" name="suburb">
                    </div>

                    <div class="form-group">
                        <label for="state">State:</label>
                        <select id="state" name="state">
                            <option value="">Select</option>
                            <option value="VIC">VIC</option>
                            <option value="NSW">NSW</option>
                            <option value="QLD">QLD</option>
                            <option value="WA">WA</option>
                            <option value="SA">SA</option>
                            <option value="TAS">TAS</option>
                            <option value="ACT">ACT</option>
                            <option value="NT">NT</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="postcode">Postcode:</label>
                        <input type="text" id="postcode" name="postcode">
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <!-- type="text" is strictly required by the specifications to demonstrate backend validation -->
                        <input type="text" id="email" name="email">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="text" id="phone" name="phone" placeholder="e.g. 0412 345 678">
                    </div>

                    <fieldset>
                        <legend>Skills (tick all that apply):</legend>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="skills[]" value="PHP"> PHP</label>
                            <label><input type="checkbox" name="skills[]" value="MySQL"> MySQL</label>
                            <label><input type="checkbox" name="skills[]" value="HTML5/CSS3"> HTML5 / CSS3</label>
                            <label><input type="checkbox" name="skills[]" value="JavaScript"> JavaScript</label>
                            <label><input type="checkbox" name="skills[]" value="Solar PV"> Solar PV Design</label>
                            <label><input type="checkbox" name="skills[]" value="Electrical Eng"> Electrical Engineering</label>
                            <label><input type="checkbox" name="skills[]" value="Project Management"> Project Management</label>
                            <label><input type="checkbox" name="skills[]" value="Customer Service"> Customer Service</label>
                        </div>
                    </fieldset>

                    <div class="form-group">
                        <label for="other_skills">Other Skills (optional):</label>
                        <textarea id="other_skills" name="other_skills"
                                  placeholder="Tell us about any additional relevant experience or qualifications."></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Submit Application</button>
                    <button type="reset" class="btn-submit">Clear Form</button>

                </form>

            </div>
        </section>
    </main>

<?php
include 'footer.inc';
?>
