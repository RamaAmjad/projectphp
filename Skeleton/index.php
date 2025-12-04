<?php
session_start();
require 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $u = trim($_POST['username']);
    $p = trim($_POST['password']);

    // Validate inputs
    if (empty($u) || empty($p)) {
        $error = "Please enter both username and password.";
    } else {

        // Secure prepared statement
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $u);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($p, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit;
            }
        }

        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Library Login</title>

    <style>
        body {
            background-color: #e8f0fe; /* soft blue */
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: white;
            width: 350px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #aaa;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #4c8bf5;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 17px;
            cursor: pointer;
        }

        button:hover {
            background: #3c75d1;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        a {
            display: block;
            margin-top: 12px;
            text-decoration: none;
            color: #4c8bf5;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>

</head>
<body>

<div class="login-container">
    <h2>Login</h2>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="post">
        <input name="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <button>Login</button>
    </form>

    <a href="signup.php">Create a New Account</a>
</div>

</body>
</html>
