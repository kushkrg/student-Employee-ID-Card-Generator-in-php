<?php
/* Database configuration - change if your MySQL setup is different */
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "id_card";

mysqli_report(MYSQLI_REPORT_OFF); // handle errors manually (never leak server details)
$conn = mysqli_connect($servername, $username, $password, $database);

if ($conn === false) {
    die('Database connection failed. Please check config.php and import database/id_card.sql');
}
mysqli_set_charset($conn, 'utf8mb4');
