CREATE TABLE IF NOT EXISTS jobs (
    job_id INT AUTO_INCREMENT PRIMARY KEY,
    job_reference VARCHAR(10) NOT NULL UNIQUE,
    job_title VARCHAR(100) NOT NULL,
    job_location VARCHAR(100) NOT NULL,
    employment_type VARCHAR(50) NOT NULL,
    salary_range VARCHAR(50),
    reports_to VARCHAR(100),
    job_description TEXT NOT NULL,
    responsibilities TEXT NOT NULL,
    essential_requirements TEXT NOT NULL,
    preferable_requirements TEXT,
    posted_date DATE DEFAULT CURRENT_DATE
);

INSERT INTO jobs (
    job_reference,
    job_title,
    job_location,
    employment_type,
    salary_range,
    reports_to,
    job_description,
    responsibilities,
    essential_requirements,
    preferable_requirements
) VALUES

(
    'SOL01',
    'Solar Installation Technician',
    'Melbourne, VIC',
    'Full-Time',
    '$70,000 - $85,000',
    'Operations Manager',
    'Install and maintain residential and commercial solar panel systems across Victoria.',
    'Install solar panels; inspect electrical systems; perform maintenance and troubleshooting; follow WHS standards.',
    'Certificate III in Electrotechnology; White Card; strong problem-solving skills; valid driver licence.',
    'Previous solar industry experience; Working at Heights certification.'
),

(
    'SOL02',
    'Renewable Energy Project Coordinator',
    'Melbourne, VIC',
    'Full-Time',
    '$80,000 - $95,000',
    'Project Director',
    'Coordinate renewable energy projects from planning through deployment.',
    'Manage schedules; communicate with clients and contractors; prepare progress reports; monitor budgets.',
    'Experience in project coordination; excellent communication skills; proficiency with Microsoft Office.',
    'Knowledge of renewable energy systems; PMP certification.'
),

(
    'SOL03',
    'Customer Support Consultant',
    'Hybrid - Melbourne, VIC',
    'Part-Time',
    '$55,000 - $65,000',
    'Customer Service Manager',
    'Provide customer assistance regarding solar energy products and services.',
    'Answer customer enquiries; resolve complaints; maintain CRM records; support the sales team.',
    'Strong communication skills; customer service experience; basic computer literacy.',
    'Experience using CRM software; renewable energy knowledge.'
),

(
    'SOL04',
    'Solar Energy Sales Representative',
    'Melbourne, VIC',
    'Full-Time',
    '$75,000 + Commission',
    'Sales Manager',
    'Promote SolarCore Energy solutions to residential and commercial clients.',
    'Generate leads; conduct consultations; prepare sales proposals; achieve monthly sales targets.',
    'Sales experience; strong negotiation skills; valid Australian driver licence.',
    'Background in renewable energy sales; experience with B2B sales.'
),

(
    'SOL05',
    'Junior Web Systems Developer',
    'Melbourne, VIC',
    'Internship',
    '$30/hour',
    'IT Team Lead',
    'Assist in developing and maintaining SolarCore Energy internal web applications.',
    'Support PHP development; maintain databases; test web features; troubleshoot issues.',
    'Basic knowledge of PHP, MySQL, HTML, CSS, and JavaScript.',
    'Experience with GitHub and responsive web design.'
);
