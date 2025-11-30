<?php
include 'header.php';
require 'db.php';

$id = $_GET['id'] ?? 0;
$res = mysqli_query($conn, "SELECT * FROM borrower WHERE borrower_id=$id");
$borrower = mysqli_fetch_assoc($res);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $type_id    = $_POST['type_id'];
    $contact_info = $_POST['contact_info'];

    mysqli_query($conn, "UPDATE borrower SET first_name='$first_name', last_name='$last_name',
                     type_id='$type_id', contact_info='$contact_info' WHERE borrower_id=$id");
    header("Location: dashboard.php?table=borrower");
    exit;
}
?>

<div class="header">
    <h1>Library Dashboard</h1>
</div>

<div class="container">
    <h2>Edit Borrower</h2>
    <form method="post">
        First Name: <input name="first_name" value="<?php echo htmlspecialchars($borrower['first_name']); ?>"><br>
        Last Name: <input name="last_name" value="<?php echo htmlspecialchars($borrower['last_name']); ?>"><br>
        Type ID: <input name="type_id" value="<?php echo htmlspecialchars($borrower['type_id']); ?>"><br>
        Contact Info: <input name="contact_info" value="<?php echo htmlspecialchars($borrower['contact_info']); ?>"><br>
        <button>Save</button>
    </form>
</div>
<a class="logout" href="logout.php">Logout</a>
