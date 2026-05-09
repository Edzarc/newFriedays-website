<?php
require_once 'config/db.php'; // your PDO connection

try {
    // SQL query to get the port
    $sql = "SHOW VARIABLES LIKE 'port'";
    $stmt = $pdo->query($sql);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo "MySQL is running on port: " . $row["Value"];
    } else {
        echo "Could not retrieve port information.";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>