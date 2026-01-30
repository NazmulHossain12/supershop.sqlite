<?php
$dbPath = getenv('APPDATA') . '\beekeeper-studio\app.db';
if (!file_exists($dbPath)) {
    echo "Beekeeper database not found at: $dbPath\n";
    exit(1);
}

try {
    $db = new PDO("sqlite:$dbPath");
    $stmt = $db->query("SELECT * FROM used_connection");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "--- Used Connection ---\n";
        print_r($row);
        echo "-----------------------\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
