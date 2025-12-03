<?php
include 'header.php';
require 'db.php';

// جلب المؤلفين
$authors = mysqli_query($conn, "SELECT author_id, first_name FROM author");

// جلب الناشرين
$publishers = mysqli_query($conn, "SELECT publisher_id, name FROM publisher");

// عند الإرسال
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $t = $_POST['title'];
    $c = $_POST['category'];
    $ty = $_POST['book_type'];
    $p = $_POST['original_price'];
    $pub = $_POST['publisher_id'];
    $author_selected = $_POST['author'];
    $available = $_POST['available']; // جديد

    // إدخال الكتاب
    $sql = "INSERT INTO book(title, category, book_type, original_price, publisher_id, available)
            VALUES('$t', '$c', '$ty', '$p', '$pub', '$available')";
    
    mysqli_query($conn, $sql);
    $book_id = mysqli_insert_id($conn);
    mysqli_query($conn, "INSERT INTO bookauthor(book_id, author_id) VALUES($book_id, $author_selected)");

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
    height: calc(100vh - 70px); /* خصم ارتفاع الهيدر */
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
    text-align: center; /* النص داخل الحقول في الوسط */
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

        <h2>Add Book</h2>

        <label>Title:</label>
        <input name="title" required placeholder="Enter book title">

        <label>Category:</label>
        <input name="category" required placeholder="Enter category">

        <label>Type:</label>
        <input name="book_type" required placeholder="Enter book type">

        <label>Price:</label>
        <input name="original_price" required placeholder="Enter price">

        <label>Publisher:</label>
        <select name="publisher_id" required>
            <option value="">Select Publisher</option>
            <?php while ($row = mysqli_fetch_assoc($publishers)) { ?>
                <option value="<?= $row['publisher_id']; ?>">
                    <?= htmlspecialchars($row['name']); ?>
                </option>
            <?php } ?>
        </select>

        <label>Author:</label>
        <select name="author" required>
            <option value="">Select Author</option>
            <?php while ($row = mysqli_fetch_assoc($authors)) { ?>
                <option value="<?= $row['author_id']; ?>">
                    <?= htmlspecialchars($row['first_name']); ?>
                </option>
            <?php } ?>
        </select>

        <label>Available:</label>
        <select name="available" required>
            <option value="">Select Availability</option>
            <option value="1">Available</option>
            <option value="0">Not Available</option>
        </select>

        <button>Add</button>
    </form>
</div>
