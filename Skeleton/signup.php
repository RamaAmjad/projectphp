<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u = $_POST['username'];
    $e = $_POST['email'];
    $p = $_POST['password'];
    $r = $_POST['role'];

   // $hashed = password_hash($p, PASSWORD_DEFAULT);


    $sql = "INSERT INTO users(username,email,password,role) VALUES('$u','$e','$p','$r')";

    mysqli_query($conn, $sql);


}
?>
<h2>Signup</h2>

<form method="post">
    Username: <input name="username"><br>
    Email: <input name="email"><br>
    Password: <input type="password" name="password"><br>
    Role:<select name="role">
        <option>student</option>
        <option>staff</option>
        <option>admin</option>
    </select><br>
    <button>Signup</button>
</form>
<a href="login.php">Login</a>