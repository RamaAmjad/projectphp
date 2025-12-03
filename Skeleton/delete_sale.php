<?php
require 'db.php';
session_start();

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM sale WHERE sale_id=$id");

header("Location: dashboard.php?table=sale&msg=Deleted");
?>
