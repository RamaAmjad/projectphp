<?php
require 'db.php';
$id = $_GET['id'];

$sql = "DELETE FROM book WHERE book_id = $id";

if (!mysqli_query($conn, $sql)) {
    $error = mysqli_error($conn);
    header("Location: dashboard.php?table=book&error=" . urlencode("Cannot delete: book has sales or loans."));
    exit;
}

header("Location: dashboard.php?table=book&msg=Deleted");
exit;
