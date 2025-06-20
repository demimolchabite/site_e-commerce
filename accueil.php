<?php include 'config.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Accueil - E-Commerce</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-title {
      font-size: 1rem;
      height: 3em;
      overflow: hidden;
    }
    .card-img-top {
      height: 200px;
      object-fit: cover;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 200px;
      background-color: #f8f9fa;
      padding-top: 60px;
      border-right: 1px solid #dee2e6;
    }

    .sidebar a {
      display: block;
      padding: 12px 20px;
      color: #333;
      text-decoration: none;
    }

    .sidebar a:hover {
      background-color: #e2e6ea;
    }

    .main-content {
      margin-left: 200px;
      padding: 20px;
      padding-top: 80px; /* espace pour navbar fixe */
    }
  </style>
</head>
<body>

<!-- Navbar horizontale en haut -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 fixed-top">
  <a class="navbar-brand" href="#">MonShop</a>
  <div class="ms-auto">
    <a href="panier.php" class="btn btn-outline-light">🛒 Panier (<span id="cart-count">0</span>)</a>
  </div>
</nav>

<!-- Sidebar verticale à gauche -->
<div class="sidebar">
  <a href="accueil.php">🏠 accueil</a>
  <a href="profil.php">👤 Profil</a>
  <a href="panier.php">🛒 Panier</a>
  <a href="logout.php">🚪 Déconnexion</a>
</div>

<!-- Contenu principal -->
<div class="main-content">
  <div class="container text-center">
    <h1 class="display-4">Bienvenue sur MonShop</h1>
    <p class="lead">Découvrez nos produits et faites vos achats en ligne facilement.</p>
  </div>

  <div class="container my-4">
    <h2 class="mb-4 text-center">Nos Produits</h2>
    <div id="produits" class="row g-4">
      <!-- Produits chargés dynamiquement -->
    </div>
  </div>
</div>

<script>
  const produitsDiv = document.getElementById("produits");

  fetch("https://dummyjson.com/products?limit=12")
    .then(res => res.json())
    .then(data => {
      if (data.products.length === 0) {
        produitsDiv.innerHTML = '<p class="text-center">Aucun produit disponible pour le moment.</p>';
        return;
      }

      data.products.forEach(prod => {
        const col = document.createElement("div");
        col.className = "col-12 col-sm-6 col-md-4 col-lg-3";

        col.innerHTML = `
          <div class="card h-100">
            <img src="${prod.thumbnail}" class="card-img-top" alt="${prod.title}">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">${prod.title}</h5>
              <p class="card-text">${prod.price}€</p>
              <button class="btn btn-primary mt-auto" onclick="ajouterAuPanier(${prod.id})">Ajouter au panier</button>
            </div>
          </div>
        `;

        produitsDiv.appendChild(col);
      });
    })
    .catch(error => {
      produitsDiv.innerHTML = '<p class="text-danger">Erreur lors du chargement des produits.</p>';
      console.error("Erreur API :", error);
    });

  function ajouterAuPanier(id) {
    let panier = JSON.parse(localStorage.getItem("panier")) || [];
    if (!panier.includes(id)) {
      panier.push(id);
      localStorage.setItem("panier", JSON.stringify(panier));
    }
    majCompteurPanier();
  }

  function majCompteurPanier() {
    const panier = JSON.parse(localStorage.getItem("panier")) || [];
    document.getElementById("cart-count").textContent = panier.length;
  }

  majCompteurPanier();
</script>

</body>
</html>
