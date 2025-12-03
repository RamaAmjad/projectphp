<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// accept both parameter names for compatibility
$report = $_GET['r'] ?? $_GET['report'] ?? '';
$data = [];
$title = "Reports";

// helper: fetchAll
function fetchAll($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if (!$result) return [];
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// helper: detect borrower name field (returns field name or null)
function getBorrowerNameField($conn) {
    $res = mysqli_query($conn, "SHOW COLUMNS FROM borrower");
    $candidates = ['name','borrower_name','full_name','first_name','fname'];
    $found = null;
    while ($col = mysqli_fetch_assoc($res)) {
        $field = $col['Field'];
        if (in_array($field, $candidates)) return $field;
        // fallback: any field containing 'name'
        if (stripos($field,'name') !== false) $found = $field;
    }
    return $found; // can be null
}

// sanitize GET helper
function esc($conn, $v) { return mysqli_real_escape_string($conn, (string)$v); }

switch ($report) {

    case 'total_value':
        $title = "Total Value of All Books";
        $sql = "SELECT SUM(original_price) AS total_value FROM book";
        $data = fetchAll($conn, $sql);
        break;

    case 'available_books':
        $title = "Books Currently Available";
        $sql = "SELECT * FROM book WHERE available = 1";
        $data = fetchAll($conn, $sql);
        break;

    case 'borrower_books':
        // show a select form if no borrower_id provided
        $title = "Books Borrowed/Bought by Borrower";
        $borrower_id = $_GET['borrower_id'] ?? '';
        if (!$borrower_id) {
            // fetch borrower list (with detected name field)
            $nameField = getBorrowerNameField($conn) ?? 'borrower_id';
            $borrowers = fetchAll($conn, "SELECT borrower_id, `". $nameField ."` FROM borrower ORDER BY borrower_id ASC");
            // we will render the form later in HTML body
            $data = ['__form_borrowers' => $borrowers, '__name_field' => $nameField];
        } else {
            $bid = intval($borrower_id);
            $title .= " (#$bid)";
            $sql = "
                SELECT b.title,
                       l.loan_date, l.return_date,
                       s.sale_date, s.sale_price
                FROM book b
                LEFT JOIN loan l ON b.book_id = l.book_id AND l.borrower_id = $bid
                LEFT JOIN sale s ON b.book_id = s.book_id AND s.borrower_id = $bid
                WHERE l.borrower_id = $bid OR s.borrower_id = $bid
            ";
            $data = fetchAll($conn, $sql);
        }
        break;

    case 'books_country':
        $title = "Books Published in Selected Country";
        $country = $_GET['country'] ?? '';
        if ($country !== '') {
            $sql = "SELECT * FROM book WHERE country = '".esc($conn,$country)."'";
            $data = fetchAll($conn, $sql);
        } else {
            $data = ['__need_country' => true];
        }
        break;

    case 'never_borrowed':
        $title = "Borrowers Who Never Borrowed or Bought";
        $sql = "
            SELECT br.*
            FROM borrower br
            LEFT JOIN loan l ON br.borrower_id = l.borrower_id
            LEFT JOIN sale s ON br.borrower_id = s.borrower_id
            WHERE l.loan_id IS NULL AND s.sale_id IS NULL
        ";
        $data = fetchAll($conn, $sql);
        break;

    case 'multiple_authors':
        $title = "Books With More Than One Author";
        $sql = "
            SELECT b.book_id, b.title, COUNT(ba.author_id) AS authors_count
            FROM book b
            JOIN book_author ba ON b.book_id = ba.book_id
            GROUP BY b.book_id
            HAVING COUNT(ba.author_id) > 1
        ";
        $data = fetchAll($conn, $sql);
        break;

    case 'sold_books':
        $title = "Books That Were Sold & Their Prices";
        $sql = "
            SELECT b.title, s.sale_price, s.sale_date
            FROM sale s
            JOIN book b ON b.book_id = s.book_id
        ";
        $data = fetchAll($conn, $sql);
        break;

    case 'current_loans':
        $title = "Current Loans and Due Dates";
        $sql = "
            SELECT b.title, l.loan_date, l.return_date, br.borrower_id
            FROM loan l
            JOIN book b ON b.book_id = l.book_id
            LEFT JOIN borrower br ON br.borrower_id = l.borrower_id
            WHERE l.return_date IS NULL
        ";
        $data = fetchAll($conn, $sql);
        break;

    case 'borrower_history':
        $title = "Loan History for Selected Borrower";
        $bid = $_GET['id'] ?? '';
        if ($bid == '') {
            $data = ['__need_borrower_id' => true];
        } else {
            $sql = "
                SELECT b.title, l.loan_date, l.return_date
                FROM loan l
                JOIN book b ON b.book_id = l.book_id
                WHERE l.borrower_id = '".esc($conn,$bid)."'
            ";
            $data = fetchAll($conn, $sql);
        }
        break;

    case 'borrowed_range':
        $title = "Books Borrowed Within a Date Range";
        $from = $_GET['from'] ?? '';
        $to   = $_GET['to'] ?? '';
        if ($from && $to) {
            $sql = "
                SELECT b.title, l.loan_date, l.return_date, br.borrower_id
                FROM loan l
                JOIN book b ON b.book_id = l.book_id
                LEFT JOIN borrower br ON br.borrower_id = l.borrower_id
                WHERE l.loan_date BETWEEN '".esc($conn,$from)."' AND '".esc($conn,$to)."'
            ";
            $data = fetchAll($conn, $sql);
        } else {
            $data = ['__need_date_range' => true];
        }
        break;

    default:
        $title = "All Reports";
        $data = [];
        break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($title); ?></title>
</head>
<body>
<h1><?php echo htmlspecialchars($title); ?></h1>

<div class="report-links">
    <a href="reports.php?r=total_value">Total Value</a>
    <a href="reports.php?r=available_books">Available Books</a>
    <a href="reports.php?r=borrower_books">Borrower Loan/Sale</a>

    <a href="reports.php?r=books_country">Books by Country</a>
    <a href="reports.php?r=never_borrowed">Never Borrowed/Bought</a>
    <a href="reports.php?r=multiple_authors">Multiple Authors</a>
    <a href="reports.php?r=sold_books">Sold Books</a>
    <a href="reports.php?r=current_loans">Current Loans</a>
    <a href="reports.php?r=borrower_history">Borrower History</a>
    <a href="reports.php?r=borrowed_range">Borrowed by Date</a>
</div>

<?php
// Render optional forms if data contains markers
if (isset($data['__form_borrowers'])) {
    // borrower selection form
    $bs = $data['__form_borrowers'];
    $nameField = $data['__name_field'] ?? 'borrower_id';
    echo '<div class="form-inline"><form method="get">
            <input type="hidden" name="r" value="borrower_books">
            <label>Select borrower: </label>
            <select name="borrower_id">';
    foreach ($bs as $br) {
        $id = htmlspecialchars($br['borrower_id']);
        $label = htmlspecialchars($br[$nameField] ?? $id);
        echo "<option value=\"{$id}\">{$label}</option>";
    }
    echo '</select><button type="submit">View</button></form></div>';
    // stop here (form displayed). When user submits we'll show results.
}

// Books by country form
if (!empty($data) && isset($data['__need_country'])) {
    echo '<div class="form-inline"><form method="get">
            <input type="hidden" name="r" value="books_country">
            <label>Country: </label>
            <input name="country" placeholder="e.g. Jordan" required>
            <button type="submit">Filter</button>
          </form></div>';
    $data = []; // nothing to show until submitted
}

// Borrower history needs borrower id
if (!empty($data) && isset($data['__need_borrower_id'])) {
    // show simple form to enter id
    echo '<div class="form-inline"><form method="get">
            <input type="hidden" name="r" value="borrower_history">
            <label>Borrower ID: </label>
            <input name="id" required>
            <button type="submit">View</button>
          </form></div>';
    $data = [];
}

// Borrowed range needs dates
if (!empty($data) && isset($data['__need_date_range'])) {
    echo '<div class="form-inline"><form method="get">
            <input type="hidden" name="r" value="borrowed_range">
            <label>From: </label><input type="date" name="from" required>
            <label>To: </label><input type="date" name="to" required>
            <button type="submit">Filter</button>
          </form></div>';
    $data = [];
}

// If after handling forms we still have a special marker like empty, clear it
if ($data === [] && $report === '') {
    echo '<p class="no-data">Select a report or click one of the buttons above.</p>';
}

// If $data is regular rows (not special marker), print table:
if (!empty($data) && !isset($data['__form_borrowers']) && !isset($data['__need_country']) && !isset($data['__need_borrower_id']) && !isset($data['__need_date_range'])) {
    echo '<table><tr>';
    // header from first row
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
} else {
    // if a report was selected but data empty (no results), show message
    if ($report !== '' && empty($data)) {
        // If we already showed a form above, don't repeat message
        if (!isset($data['__form_borrowers']) && !isset($data['__need_country']) && !isset($data['__need_borrower_id']) && !isset($data['__need_date_range'])) {
            echo '<p class="no-data">No data found for this report.</p>';
        }
    }
}
?>

<a class="back" href="dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>