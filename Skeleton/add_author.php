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
