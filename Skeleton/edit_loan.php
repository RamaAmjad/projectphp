<?php
include 'header.php';
require 'db.php';

$id = $_GET['id'];

$sql = "SELECT * FROM loan WHERE loan_id = $id";
$result = mysqli_query($conn, $sql);
$ln = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $b = $_POST['borrower_id'];
    $bk = $_POST['book_id'];
    $ld = $_POST['loan_date'];
    $rd = $_POST['return_date'];

    $sql = "
        UPDATE loan
        SET 
            borrower_id = '$b',
            book_id = '$bk',
            loan_date = '$ld',
            return_date = '$rd'
        WHERE loan_id = $id
    ";

    mysqli_query($conn, $sql);

    header("Location: dashboard.php?table=loan");
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

<h2>Edit Loan</h2>

<form method="post">
    Borrower ID:<input name="borrower_id" value="<?php echo $ln['borrower_id']; ?>"><br>
    Book ID:<input name="book_id" value="<?php echo $ln['book_id']; ?>"><br>
    Loan Date:<input type="date" name="loan_date" value="<?php echo $ln['loan_date']; ?>"><br>
    Return Date:<input type="date" name="return_date" value="<?php echo $ln['return_date']; ?>"><br>
    <button>Save</button>
</form>

</div>
