<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$mode = $_GET['mode'] ?? '';
$r = $_GET['r'] ?? '';
$selectedTable = $_GET['table'] ?? $_SESSION['last_table'] ?? '';
if ($selectedTable) $_SESSION['last_table'] = $selectedTable;

$search = $_GET['search'] ?? '';

// Helper functions
function fetchAll($conn, $sql) {
    $res = mysqli_query($conn, $sql);
    if(!$res) return [];
    $rows = [];
    while($row = mysqli_fetch_assoc($res)) $rows[] = $row;
    return $rows;
}

function esc($conn, $v){ return mysqli_real_escape_string($conn,(string)$v); }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Dashboard</h2>

    <h3>Tables</h3>
    <ul>
        <?php foreach (['book','author','publisher','borrower','loan','sale'] as $table): ?>
            <li><a href="dashboard.php?table=<?php echo $table; ?>"><?php echo ucfirst($table); ?></a></li>
        <?php endforeach; ?>
    </ul>

    <h3>Reports</h3>
    <ul>
        <li><a href="dashboard.php?mode=reports">Reports Dashboard</a></li>
    </ul>

    <h3>About Us</h3>
    <ul>
        <li><a href="dashboard.php?mode=about">About Our System</a></li>
    </ul>

    <a class="logout-btn" href="logout.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
<h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

<?php if($mode=='about'): ?>
<div class="about-box">
    <h2>About Our Library System</h2>
    <p>This Library Management Dashboard allows admins to manage books, authors, borrowers, loans, and sales within a simple interface.</p>
</div>
<?php endif; ?>

<?php if($mode=='reports'): ?>
<div class="reports-box">
<h2>Reports Dashboard</h2>
<div class="report-links">
    <a href="dashboard.php?mode=reports&r=total_value">Total Value of Books</a>
    <a href="dashboard.php?mode=reports&r=books_by_author">Books by Author</a>
    <a href="dashboard.php?mode=reports&r=borrower_books">Borrower Loan/Sale</a>
    <a href="dashboard.php?mode=reports&r=current_loans">Current Loans</a>
    <a href="dashboard.php?mode=reports&r=books_country">Books by Country</a>
    <a href="dashboard.php?mode=reports&r=never_borrowed">Never Borrowed/Bought</a>
    <a href="dashboard.php?mode=reports&r=multiple_authors">Books with Multiple Authors</a>
    <a href="dashboard.php?mode=reports&r=sold_books">Sold Books</a>
    <a href="dashboard.php?mode=reports&r=available_books">Available Books</a>
    <a href="dashboard.php?mode=reports&r=borrower_history">Loan History</a>
</div>

<div class="report-output">
<?php
switch($r) {

    case 'total_value':
        $rows = fetchAll($conn,"SELECT SUM(original_price) AS total FROM book");
        echo "<h3>Total Book Value: ".($rows[0]['total']??0)." USD</h3>";
        break;

    case 'books_by_author':
        $author_id = $_GET['author_id'] ?? '';
        if(!$author_id){
            $authors = fetchAll($conn,"SELECT author_id, first_name FROM author ORDER BY first_name");
            echo '<form method="get">
                    <input type="hidden" name="mode" value="reports">
                    <input type="hidden" name="r" value="books_by_author">
                    <label>Select Author:</label>
                    <select name="author_id">';
            foreach($authors as $a){
                echo '<option value="'.$a['author_id'].'">'.htmlspecialchars($a['first_name']).'</option>';
            }
            echo '</select><button type="submit">View Books</button></form>';
        } else {
            $author_id = intval($author_id);
            $books = fetchAll($conn,"SELECT b.title, b.category, b.original_price
                                     FROM book b
                                     JOIN bookauthor ba ON b.book_id = ba.book_id
                                     WHERE ba.author_id = $author_id");
            if($books){
                echo "<table><tr><th>Title</th><th>Category</th><th>Price</th></tr>";
                foreach($books as $b){
                    echo "<tr><td>{$b['title']}</td><td>{$b['category']}</td><td>{$b['original_price']}</td></tr>";
                }
                echo "</table>";
            } else echo "<p>No books found for this author.</p>";
        }
        break;

    case 'borrower_books':
        $borrower_id = $_GET['borrower_id'] ?? '';
        if(!$borrower_id){
            $borrowers = fetchAll($conn,"SELECT borrower_id, first_name, last_name FROM borrower ORDER BY first_name");
            echo '<form method="get">
                    <input type="hidden" name="mode" value="reports">
                    <input type="hidden" name="r" value="borrower_books">
                    <label>Select Borrower:</label>
                    <select name="borrower_id">';
            foreach($borrowers as $b){
                $label = htmlspecialchars($b['first_name'].' '.$b['last_name']);
                echo '<option value="'.$b['borrower_id'].'">'.$label.'</option>';
            }
            echo '</select><button type="submit">View Books</button></form>';
        } else {
            $bid = intval($borrower_id);
            $books = fetchAll($conn,"SELECT b.title, l.loan_date, l.return_date, s.sale_date, s.sale_price
                                     FROM book b
                                     LEFT JOIN loan l ON b.book_id = l.book_id AND l.borrower_id = $bid
                                     LEFT JOIN sale s ON b.book_id = s.book_id AND s.borrower_id = $bid
                                     WHERE l.borrower_id=$bid OR s.borrower_id=$bid");
            if($books){
                echo "<table><tr><th>Book</th><th>Loan Date</th><th>Return Date</th><th>Sale Date</th><th>Sale Price</th></tr>";
                foreach($books as $b){
                    echo "<tr>
                            <td>{$b['title']}</td>
                            <td>{$b['loan_date']}</td>
                            <td>{$b['return_date']}</td>
                            <td>{$b['sale_date']}</td>
                            <td>{$b['sale_price']}</td>
                          </tr>";
                }
                echo "</table>";
            } else echo "<p>No books borrowed or bought by this borrower.</p>";
        }
        break;

    case 'current_loans':
        $rows = fetchAll($conn,"SELECT b.title, l.loan_date, l.return_date, br.first_name AS borrower
                                 FROM loan l
                                 JOIN book b ON b.book_id = l.book_id
                                 LEFT JOIN borrower br ON br.borrower_id = l.borrower_id
                                 WHERE l.return_date IS NULL");
        if($rows){
            echo "<table><tr><th>Book</th><th>Loan Date</th><th>Due Date</th><th>Borrower</th></tr>";
            foreach($rows as $r) echo "<tr><td>{$r['title']}</td><td>{$r['loan_date']}</td><td>{$r['return_date']}</td><td>{$r['borrower']}</td></tr>";
            echo "</table>";
        } else echo "<p>No current loans.</p>";
        break;

    case 'books_country':
        $country = $_GET['country'] ?? '';
        if(!$country){
            $countries = fetchAll($conn,"SELECT DISTINCT country FROM publisher ORDER BY country");
            echo '<form method="get">
                    <input type="hidden" name="mode" value="reports">
                    <input type="hidden" name="r" value="books_country">
                    <label>Select Country:</label>
                    <select name="country">';
            foreach($countries as $c){
                echo '<option value="'.htmlspecialchars($c['country']).'">'.htmlspecialchars($c['country']).'</option>';
            }
            echo '</select><button type="submit">View Books</button></form>';
        } else {
            $country = esc($conn,$country);
            $rows = fetchAll($conn,"SELECT b.title, p.name AS publisher
                                     FROM book b
                                     JOIN publisher p ON b.publisher_id = p.publisher_id
                                     WHERE p.country='$country'");
            if($rows){
                echo "<table><tr><th>Book</th><th>Publisher</th></tr>";
                foreach($rows as $b) echo "<tr><td>{$b['title']}</td><td>{$b['publisher']}</td></tr>";
                echo "</table>";
            } else echo "<p>No books found from selected country.</p>";
        }
        break;

    case 'never_borrowed':
        $rows = fetchAll($conn,"SELECT br.* FROM borrower br
                                LEFT JOIN loan l ON br.borrower_id=l.borrower_id
                                LEFT JOIN sale s ON br.borrower_id=s.borrower_id
                                WHERE l.loan_id IS NULL AND s.sale_id IS NULL");
        if($rows){
            echo "<table><tr>";
            foreach(array_keys($rows[0]) as $col) echo "<th>$col</th>";
            echo "</tr>";
            foreach($rows as $b){
                echo "<tr>";
                foreach($b as $v) echo "<td>$v</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else echo "<p>All borrowers have borrowed or bought books.</p>";
        break;

    case 'multiple_authors':
        $rows = fetchAll($conn,"SELECT b.title, COUNT(ba.author_id) AS authors_count
                                FROM book b
                                JOIN bookauthor ba ON b.book_id = ba.book_id
                                GROUP BY b.book_id
                                HAVING COUNT(ba.author_id) > 1");
        if($rows){
            echo "<table><tr><th>Book</th><th>Number of Authors</th></tr>";
            foreach($rows as $r) echo "<tr><td>{$r['title']}</td><td>{$r['authors_count']}</td></tr>";
            echo "</table>";
        } else echo "<p>No books with multiple authors.</p>";
        break;

    case 'sold_books':
        $rows = fetchAll($conn,"SELECT b.title, s.sale_price, s.sale_date
                                FROM sale s
                                JOIN book b ON b.book_id = s.book_id");
        if($rows){
            echo "<table><tr><th>Book</th><th>Price</th><th>Sale Date</th></tr>";
            foreach($rows as $r) echo "<tr><td>{$r['title']}</td><td>{$r['sale_price']}</td><td>{$r['sale_date']}</td></tr>";
            echo "</table>";
        } else echo "<p>No sold books.</p>";
        break;

    case 'available_books':
        $rows = fetchAll($conn,"SELECT title, category FROM book WHERE available=1");
        if($rows){
            echo "<table><tr><th>Title</th><th>Category</th></tr>";
            foreach($rows as $r) echo "<tr><td>{$r['title']}</td><td>{$r['category']}</td></tr>";
            echo "</table>";
        } else echo "<p>No books available.</p>";
        break;

    case 'borrower_history':
        $borrower_id = $_GET['borrower_id'] ?? '';
        if(!$borrower_id){
            // قائمة منسدلة بأسماء المستعيرين
            $borrowers = fetchAll($conn,"SELECT borrower_id, first_name, last_name FROM borrower ORDER BY first_name");
            echo '<form method="get">
                    <input type="hidden" name="mode" value="reports">
                    <input type="hidden" name="r" value="borrower_history">
                    <label>Select Borrower:</label>
                    <select name="borrower_id">';
            foreach($borrowers as $b){
                $label = htmlspecialchars($b['first_name'].' '.$b['last_name']);
                echo '<option value="'.$b['borrower_id'].'">'.$label.'</option>';
            }
            echo '</select><button type="submit">View Loan History</button></form>';
        } else {
            $bid = intval($borrower_id);
            $rows = fetchAll($conn,"SELECT b.title, l.loan_date, l.return_date
                                    FROM loan l 
                                    JOIN book b ON b.book_id=l.book_id
                                    WHERE l.borrower_id=$bid");
            if($rows){
                echo "<table><tr><th>Book</th><th>Loan Date</th><th>Return Date</th></tr>";
                foreach($rows as $r) echo "<tr><td>{$r['title']}</td><td>{$r['loan_date']}</td><td>{$r['return_date']}</td></tr>";
                echo "</table>";
            } else echo "<p>No loans for this borrower.</p>";
        }
        break;

    default:
        echo "<p>Select a report above.</p>";
        break;
}
?>
</div>
</div>
<?php endif; ?>

<!-- SHOW TABLE VIEWER IF NOT IN MODE -->
<?php if (!$mode): ?>
<div class="table-view-section">
<form method="get">
    <input type="hidden" name="table" value="<?php echo htmlspecialchars($selectedTable); ?>">
    <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>
</div>
<?php endif; ?>

<!-- SHOW TABLE RESULTS -->
<?php
if($selectedTable && !$mode){
    $sql="SELECT * FROM $selectedTable";
    if($search){
        $cols=[];
        $colResult = mysqli_query($conn,"SHOW COLUMNS FROM `$selectedTable`");
        while($col=mysqli_fetch_assoc($colResult)){
            $field=$col['Field'];
            $type=$col['Type'];
            if(preg_match('/int|decimal|float|double/',$type)){
                $cols[]="CAST(`$field` AS CHAR) LIKE '%$search%'";
            } else $cols[]="`$field` LIKE '%$search%'";
        }
        $sql.=" WHERE ".implode(" OR ",$cols);
    }

    $result = mysqli_query($conn,$sql);
    if($result && mysqli_num_rows($result)){
        if($role=='admin') echo '<a class="add-btn" href="add_'.$selectedTable.'.php">Add New '.ucfirst($selectedTable).'</a>';
        echo '<table><tr>';
        while($field=mysqli_fetch_field($result)) echo '<th>'.htmlspecialchars($field->name).'</th>';
        if($role=='admin') echo '<th>Actions</th>';
        echo '</tr>';
        while($row=mysqli_fetch_assoc($result)){
            echo '<tr>';
            foreach($row as $v) echo '<td>'.htmlspecialchars($v).'</td>';
            if($role=='admin'){
                $pk=array_keys($row)[0];
                echo '<td><a href="edit_'.$selectedTable.'.php?id='.$row[$pk].'">Edit</a> | 
                          <a href="delete_'.$selectedTable.'.php?id='.$row[$pk].'" onclick="return confirm(\'Delete?\')">Delete</a></td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    } else echo "<p>No records found for <strong>$selectedTable</strong></p>";
}
?>
</div> <!-- main-content -->
</div> <!-- container -->
</body>
</html>
