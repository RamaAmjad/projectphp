<?php
include 'header.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $country    = $_POST['country'];
    $bio        = $_POST['bio'];

    mysqli_query($conn, "INSERT INTO author(first_name,last_name,country,bio)
                         VALUES('$first_name','$last_name','$country','$bio')");
    header("Location: dashboard.php?table=author");
    exit;
}
?>

<style>
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f2f5; 
    margin: 0;
}

/* ---------- Form Container ---------- */
.form-container {
    display: flex; 
    justify-content: center; 
    align-items: center; 
    height: calc(100vh - 70px);
}

/* ---------- Form ---------- */
form { 
    background: #ffffff; 
    padding: 35px 30px; 
    border-radius: 12px; 
    width: 420px; 
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

form:hover {
    transform: translateY(-3px);
}

h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #222;
    font-weight: 600;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #555;
    font-size: 14px;
}

input, textarea {
    display: block; 
    width: 95%; 
    margin-bottom: 20px; 
    padding: 12px;
    border-radius: 6px; 
    border: 1px solid #ccc;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    text-align: center;
    background-color: #fff;
}

input:focus, textarea:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 5px rgba(74,144,226,0.3);
}

button { 
    width: 100%;
    padding: 12px; 
    border: none; 
    border-radius: 6px; 
    background: #4a90e2; 
    color: white; 
    font-size: 16px;
    font-weight: 500;
    cursor: pointer; 
    transition: background 0.2s ease, transform 0.2s ease;
}

button:hover { 
    background: #357ABD; 
    transform: translateY(-2px);
}
</style>

<div class="form-container">
    <form method="post">

        <h2>Add Author</h2>

        <label>First Name:</label>
        <input name="first_name" required placeholder="Enter first name">

        <label>Last Name:</label>
        <input name="last_name" required placeholder="Enter last name">

        <label>Country:</label>
        <input name="country" required placeholder="Enter country">

        <label>Bio:</label>
        <textarea name="bio" rows="4" required placeholder="Enter biography"></textarea>

        <button>Add</button>
    </form>
</div>
