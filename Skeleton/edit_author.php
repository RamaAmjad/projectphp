<?php
include 'header.php';
require 'db.php';

$id = $_GET['id'] ?? 0;
$res = mysqli_query($conn, "SELECT * FROM author WHERE author_id=$id");
$author = mysqli_fetch_assoc($res);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $country    = $_POST['country'];
    $bio        = $_POST['bio'];

    mysqli_query($conn, "UPDATE author SET first_name='$first_name', last_name='$last_name',
                     country='$country', bio='$bio' WHERE author_id=$id");
    header("Location: dashboard.php?table=author");
    exit;
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
    <h2>Edit Author</h2>
    <form method="post">
        First Name: <input name="first_name" value="<?php echo htmlspecialchars($author['first_name']); ?>"><br>
        Last Name: <input name="last_name" value="<?php echo htmlspecialchars($author['last_name']); ?>"><br>
        Country: <input name="country" value="<?php echo htmlspecialchars($author['country']); ?>"><br>
        Bio: <textarea name="bio" rows="4"><?php echo htmlspecialchars($author['bio']); ?></textarea><br>
        <button>Save</button>
    </form>
</div>
