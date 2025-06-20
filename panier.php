<?php
session_start();

// Vérification du cookie "username"
if (!isset($_COOKIE['username'])) {
    header("Location: login.php?msg=" . urlencode("Vous devez être connecté pour accéder au panier."));
    exit();
}

// Connexion à la BDD
$pdo = new PDO("mysql:host=localhost;dbname=bd_devoir", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$username = $_COOKIE['username'];

// Récupérer utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.php?msg=" . urlencode("Utilisateur inconnu, veuillez vous reconnecter."));
    exit();
}

// Récupérer panier utilisateur
$stmtPanier = $pdo->prepare("SELECT * FROM panier WHERE user_id = ?");
$stmtPanier->execute([$user['ID']]);
$itemsPanier = $stmtPanier->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Mon Panier</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2>Bienvenue <?= htmlspecialchars($user['username']) ?></h2>

  <h3>Votre panier :</h3>

  <?php if (empty($itemsPanier)): ?>
    <p>Votre panier est vide.</p>
  <?php else: ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID Produit</th>
          <th>Quantité</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($itemsPanier as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['product_id']) ?></td>
            <td><?= htmlspecialchars($item['quantite']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <a href="logout.php" class="btn btn-danger">Déconnexion</a>
</div>
</body>
</html>
