<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=bd_devoir", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Vérifier utilisateur
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE username = ? AND motdepasse = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Stocker username et id dans la session
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['ID'];

            // Rediriger vers panier.php ou profil.php
            header("Location: panier.php");
            exit();
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Connexion</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <h3 class="mb-4 text-center">Se connecter</h3>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="">
          <div class="mb-3">
            <label>Nom d'utilisateur</label>
            <input type="text" name="username" class="form-control" required />
          </div>
          <div class="mb-3">
            <label>Mot de passe</label>
            <input type="password" name="password" class="form-control" required />
          </div>
          <button class="btn btn-primary w-100" type="submit">Connexion</button>
        </form>

        <div class="text-center mt-3">
          <a href="register.php">Créer un compte</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
