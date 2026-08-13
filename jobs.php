<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SwinTech Careers - Job Descriptions</title>
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>

  
  <?php include("header.inc"); ?>

  <main>
    <div class="hero">
      <h1>We Are Hiring!</h1>
      <p>Exciting opportunities await at SwinTech — join our innovative IT team.</p>
    </div>

    
    <section class="jobs-container">
      <?php
      require_once("settings.php"); 

      $conn = @mysqli_connect($host, $user, $pwd, $sql_db);

      if (!$conn) {
          echo "<p>Database connection failure.</p>";
      } else {
          $query = "SELECT * FROM jobs ORDER BY title ASC LIMIT 2";
          $result = mysqli_query($conn, $query);

          if ($result && mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                  echo '<div class="job">';
                  echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
                  echo "<p><strong>Reference ID:</strong> " . htmlspecialchars($row['job_ref']) . "</p>";
                  echo "<p><strong>Salary Range:</strong> " . htmlspecialchars($row['salary_range']) . "</p>";
                  echo "<p><strong>Reports To:</strong> " . htmlspecialchars($row['reports_to']) . "</p>";

                  echo "<h3>Job Description</h3>";
                  echo "<p>" . htmlspecialchars($row['job_description']) . "</p>";

                  echo "<h3>Key Responsibilities</h3><ul>";
                  foreach (explode(';', $row['responsibilities']) as $item) {
                      echo "<li>" . htmlspecialchars(trim($item)) . "</li>";
                  }
                  echo "</ul>";

                  echo "<h3>Qualifications</h3><h4>Essential</h4><ol>";
                  foreach (explode(';', $row['qual_essential']) as $item) {
                      echo "<li>" . htmlspecialchars(trim($item)) . "</li>";
                  }
                  echo "</ol><h4>Preferable</h4><ol>";
                  foreach (explode(';', $row['qual_preferable']) as $item) {
                      echo "<li>" . htmlspecialchars(trim($item)) . "</li>";
                  }
                  echo "</ol>";

                  echo '<a href="application.php?job_ref=' . urlencode($row['job_ref']) . '" class="apply-btn">Apply Now</a>';
                  echo '</div>';
              }
          } else {
              echo "<p>No job listings found.</p>";
          }

          mysqli_free_result($result);
          mysqli_close($conn);
      }
      ?>
    </section>

   
    <aside class="info-box">
      <h3>Why Work at SwinTech?</h3>
      <ul>
        <li>Collaborative work environment</li>
        <li>Career growth opportunities</li>
        <li>Health and wellness benefits</li>
        <li>Work with cutting-edge technology</li>
      </ul>
    </aside>
  </main>

  
  <?php include("footer.inc"); ?>
</body>
</html>
