<?php
require 'db_connect.php'; 

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password_input = $_POST['password'];
    $check = $conn->prepare("SELECT id FROM managers WHERE username=?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "<p style='color:red;'>Username already exists!</p>";
    } else {
        $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO managers (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);
        
        if ($stmt->execute()) {
            $message = "<p style='color:green;'>Registration successful! <a href='login.php'>Login now</a></p>";
        } else {
            $message = "<p style='color:red;'>Registration failed! Please try again.</p>";
        }
        $stmt->close();
    }
    $check->close();
    $conn->close();
}
?>

<form method="POST" action="register.php">
    <h2>Manager Register</h2>
    
    <?php if (!empty($message)) echo $message; ?>

    <div>
        Username: <input type="text" name="username" required><br>
    </div>
    <div>
        Password: <input type="password" name="password" required><br>
    </div>
    <button type="submit">Register</button>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</form>
