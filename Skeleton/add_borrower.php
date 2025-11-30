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
