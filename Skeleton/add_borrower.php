<?php
include 'header.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $type_id    = $_POST['type_id'];
    $contact_info = $_POST['contact_info'];

    mysqli_query($conn, "INSERT INTO borrower(first_name,last_name,type_id,contact_info)
                         VALUES('$first_name','$last_name','$type_id','$contact_info')");
    header("Location: dashboard.php?table=borrower");
    exit;
}
?>

<style>
body { font-family: Arial, sans-serif; background: #eef2f3; padding: 20px; }
.header h1 { color: #333; }
.container { background: #fff; padding: 20px; border-radius: 8px; width: 350px; }
input { display: block; width: 100%; margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
textarea { display: block; width: 100%; margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
button { padding: 10px 15px; border: none; border-radius: 4px; background: #3F51B5; color: white; cursor: pointer; }
button:hover { background: #3949ab; }
.logout { display: inline-block; margin-top: 20px; color: #f44336; text-decoration: none; }
.logout:hover { text-decoration: underline; }
</style>

<div class="header">
    <h1>Library Dashboard</h1>
</div>

<div class="container">
    <h2>Add Borrower</h2>
    <form method="post">
        First Name: <input name="first_name"><br>
        Last Name: <input name="last_name"><br>
        Type ID: <input name="type_id"><br>
        Contact Info: <input name="contact_info"><br>
        <button>Add</button>
    </form>
</div>
<a class="logout" href="logout.php">Logout</a>
