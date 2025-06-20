<?php include 'config.php'; ?>

<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=bd_devoir", "root", "");
$username = $_SESSION['username'];

// Récupération des infos de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE Username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Profil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
  <h1 class="mb-4">👤 Mon Profil</h1>

  <div class="card">
    <div class="card-body">
      <h5 class="card-title"><?= htmlspecialchars($user['Username']) ?></h5>
      <p class="card-text"><strong>ID utilisateur :</strong> <?= htmlspecialchars($user['ID']) ?></p>
      <a href="panier.php" class="btn btn-primary">Voir mon panier</a>
      <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
    </div>
  </div>
</div>

</body>
</html>
