<?php
echo "<pre>";
echo "pdo_mysql.default_socket: ".ini_get('pdo_mysql.default_socket')."\n";
echo "mysqli.default_socket  : ".ini_get('mysqli.default_socket')."\n\n";

$paths = [
    ini_get('pdo_mysql.default_socket'),
    ini_get('mysqli.default_socket'),
    '/tmp/mysql.sock',
    '/var/lib/mysql/mysql.sock',
    '/var/run/mysqld/mysqld.sock'
];

$paths = array_unique(array_filter($paths));
foreach ($paths as $p) {
    echo (file_exists($p) ? "[FOUND] " : "[MISS]  ").$p."\n";
}
echo "</pre>";
