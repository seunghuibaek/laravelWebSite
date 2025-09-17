<?php
try {
    // 소켓 우선 시도
    $pdo = new PDO('mysql:unix_socket=/tmp/mysql.sock;dbname=bluewar111;charset=utf8mb4', 'bluewar111', 'b4842310!!');
    echo "OK via socket\n";
} catch (Throwable $e) {
    try {
        // TCP(예비)
        $pdo = new PDO('mysql:host=localhost;port=3306;dbname=bluewar111;charset=utf8mb4', 'bluewar111', 'b4842310!!');
        echo "OK via TCP\n";
    } catch (Throwable $e2) {
        echo $e->getMessage() . "\n----\n" . $e2->getMessage();
    }
}
