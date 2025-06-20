<?php
session_start();

// Redirection si l'utilisateur est déjà connecté via cookie
if (isset($_COOKIE['username'])) {
    header("Location: voir_user.php");
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=bd_devoir", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$error = '';

// Récupérer un message d'erreur envoyé via l'URL (ex: ?msg=xxx)
if (isset($_GET['msg'])) {
    $error = htmlspecialchars($_GET['msg']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Vérifier les identifiants dans la base
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE username = ? AND motdepasse = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Authentification réussie : créer un cookie valable 7 heures
            setcookie("username", $user['username'], time() + 7 * 3600, "/");
            header("Location: accueil.php");
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
        <div class="alert alert-danger"><?= $error ?></div>
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
