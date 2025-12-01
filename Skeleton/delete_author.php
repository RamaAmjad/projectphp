<?php
require 'db.php';
session_start();

$id = $_GET['id'] ?? 0;
mysqli_query($conn, "DELETE FROM author WHERE author_id=$id");
header("Location: dashboard.php?table=author");
exit;
