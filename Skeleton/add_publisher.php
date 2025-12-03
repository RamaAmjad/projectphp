<?php
include 'header.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $n = $_POST['name'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $contact = $_POST['contact_info'];

    $sql = "INSERT INTO publisher(name, city, country, contact_info)
            VALUES('$n', '$city', '$country', '$contact')";

    mysqli_query($conn, $sql);

    header("Location: dashboard.php?table=publisher");
}
?>

<style>
body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; }
h2 { color: #333; }
form { background: #fff; padding: 20px; border-radius: 8px; width: 350px; }
input { display: block; width: 100%; margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
button { padding: 10px 15px; border: none; border-radius: 4px; background: #4CAF50; color: white; cursor: pointer; }
button:hover { background: #45a049; }
</style>

<h2>Add Publisher</h2>

<form method="post">
    Name:<input name="name" required>
    City:<input name="city">
    Country:<input name="country">
    Contact Info:<input name="contact_info">
    <button>Add</button>
</form>
