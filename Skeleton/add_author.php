<?php
include 'header.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $country    = $_POST['country'];
    $bio        = $_POST['bio'];

    mysqli_query($conn, "INSERT INTO author(first_name,last_name,country,bio)
                         VALUES('$first_name','$last_name','$country','$bio')");
    header("Location: dashboard.php?table=author");
    exit;
}
?>

<style>
body { font-family: Arial, sans-serif; background: #f3f3f3; padding: 20px; }
.header h1 { color: #333; }
.container { background: #fff; padding: 20px; border-radius: 8px; width: 400px; }
input, textarea { display: block; width: 100%; margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
button { padding: 10px 15px; border: none; border-radius: 4px; background: #00BCD4; color: white; cursor: pointer; }
button:hover { background: #0097a7; }
.logout { display: inline-block; margin-top: 20px; color: #f44336; text-decoration: none; }
.logout:hover { text-decoration: underline; }
</style>

<div class="header">
    <h1>Library Dashboard</h1>
</div>

<div class="container">
    <h2>Add Author</h2>
    <form method="post">
        First Name: <input name="first_name"><br>
        Last Name: <input name="last_name"><br>
        Country: <input name="country"><br>
        Bio: <textarea name="bio" rows="4"></textarea><br>
        <button>Add</button>
    </form>
</div>
<a class="logout" href="logout.php">Logout</a>
