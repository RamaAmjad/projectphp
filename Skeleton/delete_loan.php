<?php
require 'db.php';
session_start();

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM loan WHERE loan_id=$id");

header("Location: dashboard.php?table=loan&msg=Deleted");
?>

