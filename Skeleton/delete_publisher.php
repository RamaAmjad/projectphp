<?php
require 'db.php';
session_start();

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM publisher WHERE publisher_id=$id");

    header("Location: dashboard.php");
?>
