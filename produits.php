<?php
$pdo = new PDO("mysql:host=localhost;dbname=bd_devoir", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->query("SELECT id, nom, prix FROM produit");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>