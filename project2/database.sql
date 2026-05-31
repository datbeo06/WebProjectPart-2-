-- COS10026 Applied Web Project - Part 2 Database Schema & Seed Data
-- Group Nick-Thu-1030-G03
-- SolarCore Energy

CREATE DATABASE IF NOT EXISTS solarcore_db;
USE solarcore_db;

-- Drop tables if they exist to avoid schema conflicts
DROP TABLE IF EXISTS about;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS eoi;
DROP TABLE IF EXISTS jobs;

-- --------------------------------------------------------
-- Table structure for table `eoi` (Expression of Interest)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS eoi (
    EOInumber INT AUTO_INCREMENT PRIMARY KEY,
    job_ref VARCHAR(5) NOT NULL,
    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(20) NOT NULL,
    dob VARCHAR(10) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    street VARCHAR(40) NOT NULL,
    suburb VARCHAR(40) NOT NULL,
    state VARCHAR(3) NOT NULL,
    postcode VARCHAR(4) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(12) NOT NULL,
    skills TEXT,
    other_skills TEXT,
    status ENUM('New', 'Current', 'Final') DEFAULT 'New',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `jobs`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS jobs (
    job_ref VARCHAR(5) PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    salary VARCHAR(50) NOT NULL,
    reporting_to VARCHAR(100) NOT NULL,
    responsibilities TEXT NOT NULL,
    essential_requirements TEXT NOT NULL,
    preferable_requirements TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `users` (For manager login)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `about` (For team contributions)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS about (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    student_id VARCHAR(20) NOT NULL,
    role VARCHAR(100),
    part1_contribution TEXT,
    part2_contribution TEXT,
    quote_original TEXT,
    quote_translation TEXT,
    quote_language VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Seed Data for `jobs`
-- --------------------------------------------------------
INSERT INTO jobs (job_ref, title, description, salary, reporting_to, responsibilities, essential_requirements, preferable_requirements) VALUES
(
  'WD001',
  'Full-Stack Web Developer',
  'We are seeking a Full-Stack Web Developer to build and maintain SolarCore\'s public-facing websites and customer portals. You will work across PHP, MySQL, HTML5, and CSS3 to deliver responsive, accessible, and secure web applications that help customers explore our renewable energy products and track their solar system performance.',
  '$75,000 – $95,000 per year',
  'Head of Digital',
  'Develop responsive web pages and dynamic features using PHP and MySQL.;Design and maintain database schemas, write secure SQL queries with prepared statements.;Build server-side form validation and authenticated administrator dashboards.;Ensure WCAG 2.1 AA accessibility compliance across all customer-facing pages.;Collaborate with UX designers and the marketing team to launch new campaigns.;Maintain version control via Git and participate in peer code reviews.',
  'Strong proficiency in PHP 7+ / 8+ and MySQL.;Solid HTML5 and CSS3 skills, including responsive design and Flexbox/Grid.;Understanding of web security: prepared statements, password hashing, session handling.;Experience with Git-based collaborative workflows.;Excellent written communication and team collaboration skills.',
  'Experience with the LAMP/XAMPP stack and Apache configuration.;Familiarity with Australian renewable energy industry or sustainability sector.;Knowledge of accessibility standards (WCAG) and SEO best practice.;Experience integrating REST APIs (e.g. solar inverter monitoring data).'
),
(
  'SE002',
  'Solar Systems Engineer',
  'We are looking for a Solar Systems Engineer to design and oversee residential and commercial solar PV installations. You will be responsible for system sizing, electrical design, grid-connection paperwork, and on-site commissioning. This is a hands-on role for an engineer who wants their work to directly contribute to Australia\'s clean energy transition.',
  '$85,000 – $110,000 per year',
  'Engineering Manager',
  'Design solar PV systems for residential, commercial, and small-utility clients.;Perform site assessments, shading analysis, and energy yield modelling.;Prepare single-line diagrams, electrical drawings, and Clean Energy Council compliance documentation.;Liaise with distribution network service providers (DNSPs) for grid-connection approvals.;Supervise installation crews and conduct final commissioning & safety testing.;Mentor junior engineers and apprentice electricians.',
  'Bachelor\'s degree in Electrical Engineering or related discipline.;Clean Energy Council (CEC) accreditation for grid-connect solar PV design and install.;Minimum 3 years of experience in solar PV system design.;Proficiency with PVsyst, AutoCAD Electrical, and HelioScope (or similar).;Current Australian driver\'s licence and willingness to travel to regional sites.',
  'Experience with battery storage and hybrid system design.;Knowledge of AS/NZS 5033 and AS/NZS 4777 standards.;Project management certification (e.g. PRINCE2, AGILE).;Previous experience with utility-scale (> 100 kW) commercial installations.'
),
(
  'SC001',
  'Solar Panel Engineer',
  'Design and install high-efficiency solar panel installations for commercial clients. Optimize energy capture and integrate storage solutions.',
  '$90,000 - $115,000 per year',
  'Technical Director',
  'Design commercial solar panel layouts;Perform shading and structural analyses;Supervise onsite engineering activities;Collaborate with electrical contractors to ensure safety standards.',
  'Degree in Engineering;Clean Energy Council accreditation;2+ years of commercial solar design experience;Willingness to travel to project sites.',
  'Experience with large-scale battery systems;Project management background (Agile/Scrum);Knowledge of industrial grid-connection rules.'
),
(
  'SC002',
  'Renewable Energy Analyst',
  'Analyze solar power generation data and consult on energy efficiency projects for residential and commercial customers.',
  '$80,000 - $100,000 per year',
  'Business Development Manager',
  'Analyze energy generation profiles;Prepare detailed cost-benefit and payback analyses;Consult clients on state/federal rebates and incentives;Write technical energy reports.',
  'Data analysis skills;Strong knowledge of electricity markets;Excellent reporting capability;Proficient in Excel and database reporting.',
  'Experience with grid-modeling software;Python or SQL proficiency;Prior consulting or client-facing role experience.'
)
ON DUPLICATE KEY UPDATE 
  title = VALUES(title),
  description = VALUES(description),
  salary = VALUES(salary),
  reporting_to = VALUES(reporting_to),
  responsibilities = VALUES(responsibilities),
  essential_requirements = VALUES(essential_requirements),
  preferable_requirements = VALUES(preferable_requirements);

-- --------------------------------------------------------
-- Seed Data for `users` (admin/admin hashed)
-- --------------------------------------------------------
INSERT INTO users (username, password) VALUES
('admin', '$2y$10$iSUbhQqMBUpZRpJriF90G.ong29S1DZGkdgBovTP/1U8G2lg/ZmIy')
ON DUPLICATE KEY UPDATE password = VALUES(password);

-- --------------------------------------------------------
-- Seed Data for `about`
-- --------------------------------------------------------
INSERT INTO about (member_id, full_name, student_id, role, part1_contribution, part2_contribution, quote_original, quote_translation, quote_language) VALUES
(
  1,
  'Duong Danh Dat Nguyen',
  '105928000',
  'Home Page & Infrastructure',
  'Built index.html with the services table (rowspan/colspan), company stats bar, hero section with CSS background image, Acknowledgement of Country, and the global SolarCore CSS foundation. Set up the GitHub repository.',
  'Infrastructure, database seeding, dynamic header/navigation/footer inclusions, index page conversion, and user login/logout mechanisms.',
  'được đây',
  'Nice / All good',
  'Vietnamese'
),
(
  2,
  'James Magiatzis',
  '105901030',
  'Jobs Page & Project Management',
  'Developed jobs.html with two detailed renewable-energy job profiles (Full-Stack Web Developer WD001 and Solar Systems Engineer SE002), ordered/unordered lists, the floated "Why Work at SolarCore?" aside element, and the Jira workflow board configuration.',
  'Jobs flow implementation, jobs database setup, full text and reference search logic, and responsibilities rendering.',
  'Γειά σου τι κάνεις',
  'Hello, how are you?',
  'Greek'
),
(
  3,
  'Marksamuel Someth',
  '105920006',
  'Job Application Page (Forms & Validation)',
  'Built apply.html with the Expression of Interest form, eight skills-checkbox set (PHP, MySQL, HTML5/CSS3, JavaScript, Solar PV, Electrical Engineering, Project Management, Customer Service), Flexbox layout, fieldset + legend grouping, and HTML5 patterns for job reference, postcode, and phone number validation.',
  'Apply form template conversion, process_eoi form validation and sanitization engine, cross-validation of Australian states and postcodes, database insertion logic.',
  'សួស្តី',
  'Hello',
  'Khmer'
),
(
  4,
  'Jose Leonardo Vergara',
  '103641855',
  'About Us Page & Quality Assurance',
  'Built about.html with the team intro card, nested lists, definition list of member contributions, group photo figure with figcaption, fun facts table, and performed final W3C HTML/CSS validation, accessibility review, and ZIP packaging for submission.',
  'About page database integration, administrator dashboard (manage.php UI and logic), session protections, query sorting and filtering implementations.',
  'La calidad nunca es un accidente',
  'Quality is never an accident.',
  'Spanish'
)
ON DUPLICATE KEY UPDATE
  full_name = VALUES(full_name),
  student_id = VALUES(student_id),
  role = VALUES(role),
  part1_contribution = VALUES(part1_contribution),
  part2_contribution = VALUES(part2_contribution),
  quote_original = VALUES(quote_original),
  quote_translation = VALUES(quote_translation),
  quote_language = VALUES(quote_language);
