CREATE TABLE IF NOT EXISTS jobs (
    job_id INT AUTO_INCREMENT PRIMARY KEY,
    job_reference VARCHAR(10) NOT NULL UNIQUE,
    job_title VARCHAR(100) NOT NULL,
    salary_range VARCHAR(100) NOT NULL,
    location VARCHAR(150) NOT NULL,
    job_type VARCHAR(50) NOT NULL,
    reports_to VARCHAR(100) NOT NULL,
    short_description TEXT NOT NULL,
    responsibilities TEXT NOT NULL,
    essential_requirements TEXT NOT NULL,
    desirable_requirements TEXT NOT NULL
);

INSERT INTO jobs (
    job_reference,
    job_title,
    salary_range,
    location,
    job_type,
    reports_to,
    short_description,
    responsibilities,
    essential_requirements,
    desirable_requirements
) VALUES

(
    'WD001',
    'Full-Stack Web Developer',
    '$75,000 – $95,000 per year',
    'Melbourne CBD (hybrid, 3 days in office)',
    'Full-time, permanent',
    'Head of Digital',

    'We are seeking a Full-Stack Web Developer to build and maintain SolarCore''s public-facing websites and customer portals. You will work across PHP, MySQL, HTML5, and CSS3 to deliver responsive, accessible, and secure web applications that help customers explore renewable energy products and track solar system performance.',

    'Develop responsive web pages and dynamic features using PHP and MySQL.
Design and maintain database schemas and secure SQL queries with prepared statements.
Build server-side validation and administrator dashboards.
Ensure WCAG 2.1 AA accessibility compliance.
Collaborate with UX designers and marketing teams.
Maintain Git version control and participate in peer code reviews.',

    'Strong proficiency in PHP 7+/8+ and MySQL.
Solid HTML5 and CSS3 skills including responsive design.
Understanding of web security including prepared statements, password hashing, and sessions.
Experience with Git workflows.
Strong communication and teamwork skills.',

    'Experience with XAMPP and Apache configuration.
Knowledge of renewable energy or sustainability industries.
Understanding of WCAG accessibility standards and SEO.
Experience integrating REST APIs.'
),

(
    'SE002',
    'Solar Systems Engineer',
    '$85,000 – $110,000 per year',
    'Melbourne (travel across Victoria)',
    'Full-time, permanent',
    'Engineering Manager',

    'We are looking for a Solar Systems Engineer to design and oversee residential and commercial solar PV installations. This role contributes directly to Australia''s clean energy transition.',

    'Design solar PV systems for residential and commercial clients.
Perform site assessments and energy yield modelling.
Prepare electrical drawings and compliance documentation.
Coordinate DNSP grid approvals.
Supervise installation crews and commissioning.
Mentor junior engineers and apprentices.',

    'Bachelor degree in Electrical Engineering or related discipline.
CEC accreditation for solar PV design and installation.
Minimum 3 years solar PV experience.
Experience with PVsyst, AutoCAD Electrical, and HelioScope.
Valid Australian driver licence.',

    'Experience with battery storage systems.
Knowledge of AS/NZS 5033 and AS/NZS 4777 standards.
Project management certification.
Experience with utility-scale commercial installations.'
),

(
    'CS003',
    'Customer Support Consultant',
    '$55,000 – $68,000 per year',
    'Melbourne VIC',
    'Part-time',
    'Customer Service Manager',

    'Provide support to SolarCore customers regarding products, installations, and energy monitoring services.',

    'Respond to customer enquiries.
Resolve complaints and technical issues.
Maintain CRM records.
Support sales and installation teams.',

    'Strong customer service skills.
Excellent communication abilities.
Basic computer literacy.',

    'Experience with CRM software.
Knowledge of renewable energy products.'
);
