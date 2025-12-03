<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// helper functions
function fetchAll($conn, $sql) {
    $res = mysqli_query($conn, $sql);
    $rows = [];
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = $r;
        }
    }
    return $rows;
}

function esc($conn, $v) { return mysqli_real_escape_string($conn, (string)$v); }

// get report type
$report = $_GET['r'] ?? '';
$title = '';
$data = [];

switch ($report) {

    case 'total_value':
        $title = 'Total Value of All Books';
        $data = fetchAll($conn, "SELECT SUM(original_price) AS total_value FROM book");
        break;

    case 'books_by_author':
        $title = 'Books Written by a Selected Author';
        $author_id = $_GET['author_id'] ?? '';
        if ($author_id) {
            $author_id = intval($author_id);
            $data = fetchAll($conn, "
                SELECT b.title, b.category, b.original_price
                FROM book b
                JOIN book_author ba ON b.book_id = ba.book_id
                WHERE ba.author_id = $author_id
            ");
        } else {
            $data = ['__need_author' => true];
        }
        break;

    case 'borrower_books':
        $title = 'Books Borrowed or Bought by a Specific Borrower';
        $borrower_id = $_GET['borrower_id'] ?? '';
        if ($borrower_id) {
            $bid = intval($borrower_id);
            $data = fetchAll($conn, "
                SELECT b.title, l.loan_date, l.return_date, s.sale_date, s.sale_price
                FROM book b
                LEFT JOIN loan l ON b.book_id = l.book_id AND l.borrower_id = $bid
                LEFT JOIN sale s ON b.book_id = s.book_id AND s.borrower_id = $bid
                WHERE l.borrower_id = $bid OR s.borrower_id = $bid
            ");
        } else {
            $borrowers = fetchAll($conn, "SELECT borrower_id, CONCAT(first_name,' ',last_name) AS full_name FROM borrower");
            $data = ['__need_borrower' => $borrowers];
        }
        break;

    case 'current_loans':
        $title = 'Current Loans and Due Dates';
        $data = fetchAll($conn, "
            SELECT b.title, l.loan_date, l.return_date, CONCAT(br.first_name,' ',br.last_name) AS borrower_name
            FROM loan l
            JOIN book b ON l.book_id = b.book_id
            JOIN borrower br ON l.borrower_id = br.borrower_id
            WHERE l.return_date IS NULL
        ");
        break;

    case 'books_country':
        $title = 'Books Published in a Selected Country';
        $country = $_GET['country'] ?? '';
        if ($country) {
            $data = fetchAll($conn, "SELECT * FROM book WHERE country='".esc($conn,$country)."'");
        } else {
            $data = ['__need_country' => true];
        }
        break;

    case 'never_borrowed':
        $title = 'Borrowers Who Never Borrowed or Bought a Book';
        $data = fetchAll($conn, "
            SELECT br.*
            FROM borrower br
            LEFT JOIN loan l ON br.borrower_id = l.borrower_id
            LEFT JOIN sale s ON br.borrower_id = s.borrower_id
            WHERE l.loan_id IS NULL AND s.sale_id IS NULL
        ");
        break;

    case 'multiple_authors':
        $title = 'Books With More Than One Author';
        $data = fetchAll($conn, "
            SELECT b.title, COUNT(ba.author_id) AS authors_count
            FROM book b
            JOIN book_author ba ON b.book_id = ba.book_id
            GROUP BY b.book_id
            HAVING authors_count > 1
        ");
        break;

    case 'sold_books':
        $title = 'Books That Were Sold and Their Sale Prices';
        $data = fetchAll($conn, "
            SELECT b.title, s.sale_price, s.sale_date
            FROM sale s
            JOIN book b ON s.book_id = b.book_id
        ");
        break;

    case 'available_books':
        $title = 'Books Currently Available for Borrowing';
        $data = fetchAll($conn, "SELECT * FROM book WHERE available = 1");
        break;

    case 'borrower_history':
        $title = 'Loan History for a Selected Borrower';
        $bid = $_GET['borrower_id'] ?? '';
        if ($bid) {
            $bid = intval($bid);
            $data = fetchAll($conn, "
                SELECT b.title, l.loan_date, l.return_date
                FROM loan l
                JOIN book b ON l.book_id = b.book_id
                WHERE l.borrower_id = $bid
            ");
        } else {
            $borrowers = fetchAll($conn, "SELECT borrower_id, CONCAT(first_name,' ',last_name) AS full_name FROM borrower");
            $data = ['__need_borrower_history' => $borrowers];
        }
        break;

    default:
        $title = 'Reports';
        break;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($title); ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1><?php echo htmlspecialchars($title); ?></h1>

<div class="report-links">
    <a href="reports.php?r=total_value">Total Value</a>
    <a href="reports.php?r=books_by_author">Books by Author</a>
    <a href="reports.php?r=borrower_books">Borrower Books</a>
    <a href="reports.php?r=current_loans">Current Loans</a>
    <a href="reports.php?r=books_country">Books by Country</a>
    <a href="reports.php?r=never_borrowed">Never Borrowed</a>
    <a href="reports.php?r=multiple_authors">Multiple Authors</a>
    <a href="reports.php?r=sold_books">Sold Books</a>
    <a href="reports.php?r=available_books">Available Books</a>
    <a href="reports.php?r=borrower_history">Borrower History</a>
</div>

<?php
// forms for selection
if (isset($data['__need_author'])) {
    echo '<form method="get"><input type="hidden" name="r" value="books_by_author"><label>Select Author: </label><select name="author_id">';
    foreach ($data['__need_author'] as $a) {
        echo '<option value="'.$a['author_id'].'">'.htmlspecialchars($a['full_name']).'</option>';
    }
    echo '</select><button type="submit">View</button></form>';
}

if (isset($data['__need_borrower'])) {
    echo '<form method="get"><input type="hidden" name="r" value="borrower_books"><label>Select Borrower: </label><select name="borrower_id">';
    foreach ($data['__need_borrower'] as $b) {
        echo '<option value="'.$b['borrower_id'].'">'.htmlspecialchars($b['full_name']).'</option>';
    }
    echo '</select><button type="submit">View</button></form>';
}

if (isset($data['__need_borrower_history'])) {
    echo '<form method="get"><input type="hidden" name="r" value="borrower_history"><label>Select Borrower: </label><select name="borrower_id">';
    foreach ($data['__need_borrower_history'] as $b) {
        echo '<option value="'.$b['borrower_id'].'">'.htmlspecialchars($b['full_name']).'</option>';
    }
    echo '</select><button type="submit">View</button></form>';
}

if (isset($data['__need_country'])) {
    echo '<form method="get"><input type="hidden" name="r" value="books_country"><label>Country: </label><input name="country" required><button type="submit">Filter</button></form>';
}

// show table if data exists
if (!empty($data) && !is_array($data[0] ?? null) && !isset($data['__need_author']) && !isset($data['__need_borrower']) && !isset($data['__need_borrower_history']) && !isset($data['__need_country'])) {
    echo '<table border="1"><tr>'; 
    foreach (array_keys($data[0]) as $col) {
        echo '<th>'.htmlspecialchars($col).'</th>';
    }
    echo '</tr>';
    foreach ($data as $row) {
        echo '<tr>';
        foreach ($row as $val) echo '<td>'.htmlspecialchars((string)$val).'</td>';
        echo '</tr>';
    }
    echo '</table>';
}

?>

<a href="dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>
