<?php include 'config.php'; ?>

<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=bd_devoir", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$error = '';
$success = '';

if (isset($_SESSION['user'])) {
    header("Location: accueil.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Vérifications basiques
    if (strlen($username) < 3) {
        $error = "Le nom d'utilisateur doit faire au moins 3 caractères.";
    } elseif ($password !== $password_confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit faire au moins 6 caractères.";
    } else {
        // Vérifier si username existe déjà
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Ce nom d'utilisateur est déjà pris.";
        } else {
            // Insérer l'utilisateur (pour l'instant mot de passe en clair)
            $stmt = $pdo->prepare("INSERT INTO utilisateur (username, motdepasse) VALUES (?, ?)");
            $stmt->execute([$username, $password]);

            $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f1f1f1;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .register-box {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }
  </style>
</head>
<body>

<div class="register-box">
  <h3 class="text-center mb-4">📝 Inscription</h3>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php elseif ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="post" autocomplete="off">
    <div class="mb-3">
      <label for="username" class="form-label">Nom d'utilisateur</label>
      <input type="text" name="username" id="username" class="form-control" required minlength="3">
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Mot de passe</label>
      <input type="password" name="password" id="password" class="form-control" required minlength="6">
    </div>

    <div class="mb-3">
      <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
      <input type="password" name="password_confirm" id="password_confirm" class="form-control" required minlength="6">
    </div>

    <button type="submit" class="btn btn-success w-100">S'inscrire</button>
  </form>

  <div class="text-center mt-3">
    <p>Déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
  </div>
</div>

</body>
</html>
