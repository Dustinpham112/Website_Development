<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Swintech Job Application</title>
  <link rel="stylesheet" href="styles/styles.css" />
</head>

<body class="apply-page">

<?php
  include("header.inc"); 
?>

  <!-- MAIN FORM -->
  <main class="apply-form-container">
    <h2>Job Application Form</h2>
    <form action="process_eoi.php" method="post" novalidate="novalidate">
      <label for="jobRef">Job Reference Number:</label>
      <select id="jobRef" name="jobRef" required>
        <option value="">Select a job</option>
        <option value="NT101">NT101 - Network Administrator</option>
        <option value="SA202">SA202 - Systems Administrator</option>
      </select>

      <label for="firstName">First Name:</label>
      <input type="text" id="firstName" name="firstName" maxlength="20" pattern="[A-Za-z]+" required />

      <label for="lastName">Last Name:</label>
      <input type="text" id="lastName" name="lastName" maxlength="20" pattern="[A-Za-z]+" required />

      <label for="dob">Date of Birth:</label>
      <input type="text" id="dob" name="dob" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" required />

      <fieldset>
        <legend>Gender</legend>
        <label><input type="radio" name="gender" value="Male" required /> Male</label>
        <label><input type="radio" name="gender" value="Female" /> Female</label>
        <label><input type="radio" name="gender" value="Other" /> Other</label>
      </fieldset>

      <label for="street">Street Address:</label>
      <input type="text" id="street" name="street" maxlength="40" required />

      <label for="suburb">Suburb/Town:</label>
      <input type="text" id="suburb" name="suburb" maxlength="40" required />

      <label for="state">State:</label>
      <select id="state" name="state" required>
        <option value="">Select State</option>
        <option value="VIC">VIC</option>
        <option value="NSW">NSW</option>
        <option value="QLD">QLD</option>
        <option value="NT">NT</option>
        <option value="WA">WA</option>
        <option value="SA">SA</option>
        <option value="TAS">TAS</option>
        <option value="ACT">ACT</option>
      </select>

      <label for="postcode">Postcode:</label>
      <input type="text" id="postcode" name="postcode" pattern="\d{4}" maxlength="4" required />

      <label for="email">Email Address:</label>
      <input type="email" id="email" name="email" pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$" required />

      <label for="phone">Phone Number:</label>
      <input type="tel" id="phone" name="phone" pattern="[\d\s]{8,12}" required />

      <fieldset>
        <legend>Required Technical Skills</legend>
        <label><input type="checkbox" name="skills[]" value="HTML" /> HTML</label> 
        <label><input type="checkbox" name="skills[]" value="CSS" /> CSS</label> 
        <label><input type="checkbox" name="skills[]" value="JavaScript" /> JavaScript</label> 
        <label><input type="checkbox" name="skills[]" value="Python" /> Python</label>
      </fieldset>
      <label>
        <input type="checkbox" id="other_skills_checkbox" name="other_skills_checkbox" value="true" /> Do you have other skills?
      </label>

      <label for="otherSkills">Other Skills:</label>
      <textarea id="otherSkills" name="otherSkills" rows="4" cols="50"></textarea>

      <button type="submit" class="apply-submit-btn">Apply</button>
    </form>
  </main>

  <!-- FOOTER -->
<?php
  include("footer.inc");
?>
</body>

</html>
