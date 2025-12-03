<?php
include 'header.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $b = $_POST['borrower_id'];
    $bk = $_POST['book_id'];
    $ld = $_POST['loan_date'];
    $rd = $_POST['return_date'];

    $sql = "INSERT INTO loan(borrower_id, book_id, loan_date, return_date)
            VALUES('$b', '$bk', '$ld', '$rd')";

    mysqli_query($conn, $sql);

    header("Location: dashboard.php?table=loan");
}
?>

<style>
body { font-family: Arial, sans-serif; background: #f0f4f7; padding: 20px; }
h2 { color: #333; }
form { background: #fff; padding: 20px; border-radius: 8px; width: 320px; }
input { display: block; width: 100%; margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
button { padding: 10px 15px; border: none; border-radius: 4px; background: #FF9800; color: white; cursor: pointer; }
button:hover { background: #e68a00; }
</style>

<h2>Add Loan</h2>

<form method="post">
    Borrower ID:<input name="borrower_id">
    Book ID:<input name="book_id">
    Loan Date:<input type="date" name="loan_date">
    Dut Date : <input type = "date" name= "dut date">
    Return Date:<input type="date" name="return_date">
    <button>Add</button>
</form>
