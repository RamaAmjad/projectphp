<?php
include 'header.php';
require 'db.php';

$id = $_GET['id'];

// جلب بيانات الكتاب
$sql = "SELECT * FROM book WHERE book_id = $id";
$result = mysqli_query($conn, $sql);
$bk = mysqli_fetch_assoc($result);

// جلب جميع المؤلفين والناشرين
$authors_result = mysqli_query($conn, "SELECT author_id, first_name FROM author");
$publishers_result = mysqli_query($conn, "SELECT publisher_id, name FROM publisher");

// تخزين المؤلفين والناشرين في مصفوفات
$authors = [];
while($row = mysqli_fetch_assoc($authors_result)) {
    $authors[] = $row;
}

$publishers = [];
while($row = mysqli_fetch_assoc($publishers_result)) {
    $publishers[] = $row;
}

// جلب المؤلف المرتبط بالكتاب
$author_assoc = mysqli_query($conn, "SELECT author_id FROM bookauthor WHERE book_id = $id");
$bk_author = mysqli_fetch_assoc($author_assoc)['author_id'] ?? null;

// عند الإرسال
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $t = $_POST['title'];
    $c = $_POST['category'];
    $ty = $_POST['book_type'];
    $p = $_POST['original_price'];
    $pub = $_POST['publisher_id'];
    $author_selected = $_POST['author'];
    $available = $_POST['available'];

    // تحديث بيانات الكتاب
    $sql = "
        UPDATE book 
        SET title='$t', category='$c', book_type='$ty', original_price='$p', publisher_id='$pub', available='$available'
        WHERE book_id=$id
    ";
    mysqli_query($conn, $sql);

    // تحديث المؤلف
    mysqli_query($conn, "DELETE FROM bookauthor WHERE book_id=$id");
    mysqli_query($conn, "INSERT INTO bookauthor(book_id, author_id) VALUES($id, $author_selected)");

    header("Location: dashboard.php?table=book");
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

input, select {
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

input:focus, select:focus {
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

        <h2>Edit Book</h2>

        <label>Title:</label>
        <input name="title" required value="<?= htmlspecialchars($bk['title']); ?>">

        <label>Category:</label>
        <input name="category" required value="<?= htmlspecialchars($bk['category']); ?>">

        <label>Type:</label>
        <input name="book_type" required value="<?= htmlspecialchars($bk['book_type']); ?>">

        <label>Price:</label>
        <input name="original_price" required value="<?= htmlspecialchars($bk['original_price']); ?>">

        <label>Publisher:</label>
        <select name="publisher_id" required>
            <option value="">Select Publisher</option>
            <?php foreach($publishers as $p) { ?>
                <option value="<?= $p['publisher_id']; ?>" <?= $p['publisher_id']==$bk['publisher_id']?'selected':''; ?>>
                    <?= htmlspecialchars($p['name']); ?>
                </option>
            <?php } ?>
        </select>

        <label>Author:</label>
        <select name="author" required>
            <option value="">Select Author</option>
            <?php foreach($authors as $a) { ?>
                <option value="<?= $a['author_id']; ?>" <?= $a['author_id']==$bk_author?'selected':''; ?>>
                    <?= htmlspecialchars($a['first_name']); ?>
                </option>
            <?php } ?>
        </select>

        <label>Available:</label>
        <select name="available" required>
            <option value="">Select Availability</option>
            <option value="1" <?= $bk['available']==1?'selected':''; ?>>Available</option>
            <option value="0" <?= $bk['available']==0?'selected':''; ?>>Not Available</option>
        </select>

        <button>Save</button>
    </form>
</div>
