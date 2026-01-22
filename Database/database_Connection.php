<?php
// 1. I-set ang iyong server details
$serverName = "DESKTOP-LSU5CF3\SQLEXPRESS"; 
$database   = "ISCP";

try {
    /**
     * PAGBUO NG CONNECTION STRING
     */
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database;TrustServerCertificate=true", "", "");
    
    // I-set ang error mode para makita natin kung may mali sa backend
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // TANGGAL ANG ECHO DITO PARA WALANG LUMALABAS SA SCREEN

} catch (PDOException $e) {
    // Optional: Iwanan itong die() para huminto ang system kapag walang database
    die("Database Connection Error: " . $e->getMessage());
}
?>