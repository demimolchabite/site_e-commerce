<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php?msg=" . urlencode("Veuillez vous connecter pour accéder au profil."));
    exit();
}

// Affichage des données session
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'] ?? 'Non défini';
$cart_id = $_SESSION['cart_id'] ?? 'Aucun panier actif';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Profil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card p-4 shadow-sm">
    <h3 class="mb-3">👤 Mon Profil</h3>
    <p><strong>Nom d'utilisateur :</strong> <?= htmlspecialchars($username) ?></p>
    <p><strong>ID utilisateur :</strong> <?= htmlspecialchars($user_id) ?></p>
    <p><strong>ID panier courant :</strong> <?= htmlspecialchars($cart_id) ?></p>

    <div class="mt-3">
      <a href="panier.php" class="btn btn-outline-primary">Voir mon panier</a>
      <a href="logout.php" class="btn btn-outline-danger">Se déconnecter</a>
    </div>
  </div>
</div>

</body>
</html>
