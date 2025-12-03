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

<style>
body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
h2 { color: #333; }
form { background: #fff; padding: 20px; border-radius: 8px; width: 350px; }
input { display: block; width: 100%; margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
button { padding: 10px 15px; border: none; border-radius: 4px; background: #9C27B0; color: white; cursor: pointer; }
button:hover { background: #7b1fa2; }
</style>

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
