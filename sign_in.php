<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=bd_devoir", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$username || !$password) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Vérifie si le nom d'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Ce nom d'utilisateur est déjà pris.";
        } else {
            // Insère le nouvel utilisateur
            $stmt = $pdo->prepare("INSERT INTO utilisateur (username, motdepasse) VALUES (?, ?)");
            $stmt->execute([$username, $password]);

            // Démarre une session automatiquement
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $pdo->lastInsertId();

            header("Location: panier.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Créer un compte</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm p-4">
        <h3 class="text-center mb-4">📝 Créer un compte</h3>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label">Nom d'utilisateur</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-success w-100">Créer mon compte</button>
        </form>

        <div class="text-center mt-3">
          <span>Déjà un compte ?</span>
          <a href="login.php">Se connecter</a>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
