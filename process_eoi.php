<?php
session_start(); 
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: apply.php");
    exit;
}

require_once("settings.php"); 

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$errors = []; 

$job_ref = sanitize_input($_POST["jobRef"]);
$first_name = sanitize_input($_POST["firstName"]);
$last_name = sanitize_input($_POST["lastName"]);
$dob = sanitize_input($_POST["dob"]);
$gender = isset($_POST["gender"]) ? sanitize_input($_POST["gender"]) : "";
$street = sanitize_input($_POST["street"]);
$suburb = sanitize_input($_POST["suburb"]);
$state = sanitize_input($_POST["state"]);
$postcode = sanitize_input($_POST["postcode"]);
$email = sanitize_input($_POST["email"]);
$phone = sanitize_input($_POST["phone"]);
$skills = isset($_POST["skills"]) ? $_POST["skills"] : []; 
$other_skills_checkbox = isset($_POST["other_skills_checkbox"]);
$other_skills = sanitize_input($_POST["otherSkills"]);

if (empty($job_ref)) $errors[] = "Job Reference Number is required.";
if (empty($first_name)) $errors[] = "First Name is required.";
if (empty($last_name)) $errors[] = "Last Name is required.";
if (empty($dob)) $errors[] = "Date of Birth is required.";
if (empty($gender)) $errors[] = "Gender is required.";
if (empty($street)) $errors[] = "Street Address is required.";
if (empty($suburb)) $errors[] = "Suburb/Town is required.";
if (empty($state)) $errors[] = "State is required.";
if (empty($postcode)) $errors[] = "Postcode is required.";
if (empty($email)) $errors[] = "Email is required.";
if (empty($phone)) $errors[] = "Phone Number is required.";
if (empty($skills)) $errors[] = "You need to choose a least skill.";

if (!empty($first_name) && !preg_match("/^[A-Za-z]{1,20}$/", $first_name)) $errors[] = "First Name must contain a maximum of 20 alphabetic characters.";
if (!empty($last_name) && !preg_match("/^[A-Za-z]{1,20}$/", $last_name)) $errors[] = "Last Name must contain a maximum of 20 alphabetic characters.";
if (!empty($street) && strlen($street) > 40) $errors[] = "Street Address cannot exceed 40 characters.";
if (!empty($suburb) && strlen($suburb) > 40) $errors[] = "Suburb/Town cannot exceed 40 characters.";
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email is not in correct format..";
if (!empty($phone) && !preg_match("/^[\d\s]{8,12}$/", $phone)) $errors[] = "Phone Number must be 8-12 digits (can contain spaces).";
if (!empty($dob) && !preg_match("~^\d{2}/\d{2}/\d{4}$~", $dob)) $errors[] = "Date of Birth must be in dd/mm/yyyy format.";

if (!empty($postcode) && !preg_match("/^\d{4}$/", $postcode)) {
    $errors[] = "Postcode must have exactly 4 digits.";
} elseif (!empty($state) && !empty($postcode)) {
    $first_digit = $postcode[0];
    $state_valid = false;
    switch ($state) {
        case "VIC": $state_valid = in_array($first_digit, ['3', '8']); break;
        case "NSW": $state_valid = in_array($first_digit, ['1', '2']); break;
        case "QLD": $state_valid = in_array($first_digit, ['4', '9']); break;
        case "NT":
        case "ACT": $state_valid = ($first_digit == '0'); break;
        case "WA":  $state_valid = ($first_digit == '6'); break;
        case "SA":  $state_valid = ($first_digit == '5'); break;
        case "TAS": $state_valid = ($first_digit == '7'); break;
    }
    if (!$state_valid) {
        $errors[] = "Postcode ($postcode) does not match State ($state).";
    }
}

if ($other_skills_checkbox && empty($other_skills)) {
    $errors[] = "You selected 'Other Skills' but did not provide any further description..";
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST; 
    header("Location: apply.php");
    exit;
} else {
    $conn = @mysqli_connect($host, $user, $pwd, $sql_db);

    if (!$conn) {
        echo "<p>Unable to connect to database. Error: " . mysqli_connect_error() . "</p>";
        exit;
    }

    $sql_create_table = "CREATE TABLE IF NOT EXISTS eoi (
        EOInumber INT AUTO_INCREMENT PRIMARY KEY,
        job_ref VARCHAR(10) NOT NULL,
        first_name VARCHAR(20) NOT NULL,
        last_name VARCHAR(20) NOT NULL,
        dob DATE NOT NULL,
        gender VARCHAR(10),
        street_address VARCHAR(40) NOT NULL,
        suburb_town VARCHAR(40) NOT NULL,
        state VARCHAR(3) NOT NULL,
        postcode CHAR(4) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(12) NOT NULL,
        skill_html BOOLEAN DEFAULT FALSE,
        skill_css BOOLEAN DEFAULT FALSE,
        skill_js BOOLEAN DEFAULT FALSE,
        skill_python BOOLEAN DEFAULT FALSE,
        other_skills TEXT,
        status VARCHAR(7) DEFAULT 'New' 
    )";
    
    mysqli_query($conn, $sql_create_table); 

    $dob_parts = explode('/', $dob);
    $dob_sql = $dob_parts[2] . '-' . $dob_parts[1] . '-' . $dob_parts[0];

    $skill_html = in_array("HTML", $skills) ? 1 : 0;
    $skill_css = in_array("CSS", $skills) ? 1 : 0;
    $skill_js = in_array("JavaScript", $skills) ? 1 : 0;
    $skill_python = in_array("Python", $skills) ? 1 : 0;

    $sql_insert = "INSERT INTO eoi (job_ref, first_name, last_name, dob, gender, street_address, suburb_town, state, postcode, email, phone, skill_html, skill_css, skill_js, skill_python, other_skills) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt, "sssssssssssiiiis", 
        $job_ref, 
        $first_name, 
        $last_name, 
        $dob_sql, 
        $gender, 
        $street, 
        $suburb, 
        $state, 
        $postcode, 
        $email, 
        $phone, 
        $skill_html, 
        $skill_css, 
        $skill_js, 
        $skill_python, 
        $other_skills
    );

    if (mysqli_stmt_execute($stmt)) {
        $eoi_number = mysqli_insert_id($conn);

        echo "<html><head><title>Application Successful</title></head><body>";
        echo "<h1>Thank you for applying.!</h1>";
        echo "<p>Your application has been submitted successfully.</p>";
        echo "<p>Your EOI number is: <strong>$eoi_number</strong></p>";
        echo "<a href='index.php'>Back to home page</a>";
        echo "</body></html>";
        
        unset($_SESSION['form_errors']);
        unset($_SESSION['form_data']);

    } else {
        echo "<p>An error occurred while saving the application.: " . mysqli_error($conn) . "</p>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>


