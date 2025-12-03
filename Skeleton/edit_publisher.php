<?php
include 'header.php';
require 'db.php';

$id = $_GET['id'];

$sql = "SELECT * FROM publisher WHERE publisher_id = $id";
$result = mysqli_query($conn, $sql);
$pub = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $n = $_POST['name'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $contact = $_POST['contact_info'];

    $sql = "
        UPDATE publisher
        SET 
            name = '$n',
            city = '$city',
            country = '$country',
            contact_info = '$contact'
        WHERE publisher_id = $id
    ";

    mysqli_query($conn, $sql);

    header("Location: dashboard.php?table=publisher");
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

<h2>Edit Publisher</h2>

<form method="post">
    Name:<input name="name" value="<?php echo $pub['name']; ?>" required><br>
    City:<input name="city" value="<?php echo $pub['city']; ?>"><br>
    Country:<input name="country" value="<?php echo $pub['country']; ?>"><br>
    Contact Info:<input name="contact_info" value="<?php echo $pub['contact_info']; ?>"><br>
    <button>Save</button>
</form>
</div>