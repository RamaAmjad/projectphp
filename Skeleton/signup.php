<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u = $_POST['username'];
    $e = $_POST['email'];
    $p = $_POST['password'];
    $r = $_POST['role'];

    // Secure password hash
    $hashed = password_hash($p, PASSWORD_DEFAULT);

    // Prepared SQL to prevent SQL Injection
    $sql = "INSERT INTO users(username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $u, $e, $hashed, $r);
    mysqli_stmt_execute($stmt);

    // Redirect after successful signup
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Signup</title>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background-color: #e8f0fe; /* soft comfortable blue */
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        background: white;
        padding: 40px;
        width: 340px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: center;
    }

    .container h2 {
        margin-bottom: 20px;
        font-size: 26px;
        color: #0D47A1;
    }

    input, select {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 15px;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: #1565C0;
        color: white;
        border: none;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0D47A1;
    }

    .link {
        margin-top: 15px;
        font-size: 14px;
    }

    .link a {
        color: #1565C0;
        text-decoration: none;
    }

    .link a:hover {
        text-decoration: underline;
    }
</style>
</head>

<body>

<div class="container">
    <h2>Signup</h2>

    <form method="post">
        <input name="username" placeholder="Username" required>
        <input name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <select name="role" required>
            <option value="student">Student</option>
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
        </select>

        <button>Signup</button>

        <div class="link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </form>
</div>

</body>
</html>
