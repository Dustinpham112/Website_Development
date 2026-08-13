CREATE DATABASE IF NOT EXISTS website_development;
USE website_development;
CREATE TABLE IF NOT EXISTS jobs (
    job_id INT AUTO_INCREMENT PRIMARY KEY,
    job_ref VARCHAR(10) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    salary_range VARCHAR(50),
    reports_to VARCHAR(100),
    job_description TEXT,
    responsibilities TEXT, 
    qual_essential TEXT,   
    qual_preferable TEXT  
    );

INSERT INTO jobs 
    (job_ref, title, salary_range, reports_to, job_description, responsibilities, qual_essential, qual_preferable)
VALUES
(
    'NT101', 
    'Network Administrator', 
    '$60,000 - $75,000 per year', 
    'IT Manager', 
    'The Network Administrator is responsible for...', 
    'Monitor and maintain;Install and troubleshoot;Ensure network security;Support end-users;Document network', 
    'Bachelor\'s degree;2+ years of experience;Good knowledge of Windows and Linux;Understanding of network protocols;Experience with cloud services;Certifications like CCNA',
    'Relevant degrees;Advanced certifications' 
),
(
    'SA202', 
    'Systems Administrator', 
    '$65,000 - $80,000 per year', 
    'IT Manager', 
    'The Systems Administrator will manage servers...', 
    'Install and configure;Maintain system performance;Perform backups;Apply patches;Provide technical support',
    'Bachelor\'s degree;2+ years of experience;Good knowledge of Windows and Linux;Understanding of virtualization;Experience with cloud services;Certifications like MCSA',
    'Knowledge of scripting;Familiarity with DevOps' 
);

