<?php
require 'db.php';
session_start();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Check if publisher is linked to any book
    $linkedBooks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM book WHERE publisher_id=$id"))['cnt'];
    
    if($linkedBooks > 0){
        echo "<script>
                alert('❌ Cannot delete this publisher because it is linked to books.');
                window.history.back();
              </script>";
        exit;
    }

    // Safe to delete
    mysqli_query($conn, "DELETE FROM publisher WHERE publisher_id=$id");
    echo "<script>
            alert('✅ Publisher deleted successfully.');
            window.location.href='dashboard.php?table=publisher';
          </script>";
    exit;
}

echo "<script>
        alert('❌ Invalid ID.');
        window.history.back();
      </script>";
exit;
?>
