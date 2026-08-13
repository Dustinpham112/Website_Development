<?php
session_start();
require 'db_connect.php'; 
$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM managers WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $remaining = strtotime($user['locked_until']) - time();
            $login_error = "⏳ Account locked. Try again in $remaining seconds.";
        
        } elseif (password_verify($password, $user['password'])) {            
            session_regenerate_id(true); 

            $_SESSION['loggedin'] = true; 
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id']; 

            $stmt_reset = $conn->prepare("UPDATE managers SET failed_attempts=0, locked_until=NULL WHERE username=?");
            $stmt_reset->bind_param("s", $username);
            $stmt_reset->execute();
            $stmt_reset->close();

            header("Location: manage.php");
            exit;

        } else {
            $new_attempts = $user['failed_attempts'] + 1;
            
            $stmt_fail = $conn->prepare("UPDATE managers SET failed_attempts = ? WHERE username = ?");
            $stmt_fail->bind_param("is", $new_attempts, $username); 
            $stmt_fail->execute();
            $stmt_fail->close();
            
            if ($new_attempts >= 3) {
                $lockTime = date("Y-m-d H:i:s", strtotime("+5 minutes"));
                
                $stmt_lock = $conn->prepare("UPDATE managers SET locked_until = ? WHERE username = ?");
                $stmt_lock->bind_param("ss", $lockTime, $username); 
                $stmt_lock->execute();
                $stmt_lock->close();
                
                $login_error = "🚫 Too many attempts. Account locked for 5 minutes.";
            } else {
                $login_error = "❌ Incorrect password.";
            }
        }
    } else {
        $login_error = "❌ User not found.";
    }
    $stmt->close();
    $conn->close();
}
?>

<form method="POST" action="login.php">
    <h2>Manager Login</h2>
    
    <?php if (!empty($login_error)): ?>
        <p style="color: red; background-color: #ffebee; padding: 10px; border-radius: 4px;">
            <?php echo $login_error; ?>
        </p>
    <?php endif; ?>

    <div>
        Username: <input type="text" name="username" required><br>
    </div>
    <div>
        Password: <input type="password" name="password" required><br>
    </div>
    <button type="submit">Login</button>
</form>
