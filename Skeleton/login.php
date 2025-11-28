<?php
session_start();
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 1. Read form input
    $u = $_POST['username'];
    $p = $_POST['password'];

    // 2. Run query to find user
    $sql = "SELECT * FROM users WHERE username = '$u'";
    $result = mysqli_query($conn, $sql);

    // 3. Check if user exists (should be 1 record)
    if (mysqli_num_rows($result) == 1) {

        // 4. Fetch the user row
        $user = mysqli_fetch_assoc($result);
        
        // 5. Verify password
        if (password_verify($p, $user['password'])) {

            // 6. Store session data
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // 7. Redirect to books page
            header("Location: books.php");
            exit;
        }
    }
}

?>
<h2>Login</h2>

<form method="post">
    Username:<input name="username"><br>
    Password:<input type="password" name="password"><br>
    <button>Login</button>
</form>
<a href="signup.php">Signup</a>