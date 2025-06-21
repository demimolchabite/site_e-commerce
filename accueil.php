<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php?msg=" . urlencode("Veuillez vous connecter pour accéder à l'accueil."));
    exit();
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Accueil - E-Commerce</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .card-title {
      font-size: 1rem;
      height: 3em;
      overflow: hidden;
    }
    .card-img-top {
      height: 200px;
      object-fit: cover;
      cursor: pointer;
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
      padding-top: 80px;
    }
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.5);
      z-index: 9999;
    }
    .modal-content {
      background: white;
      width: 90%;
      max-width: 600px;
      margin: 50px auto;
      padding: 20px;
      border-radius: 10px;
      position: relative;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 fixed-top">
  <a class="navbar-brand" href="#">MonShop</a>
  <div class="ms-auto">
    <a href="panier.php" class="btn btn-outline-light">🛒 Panier (<span id="cart-count">0</span>)</a>
  </div>
</nav>

<div class="sidebar">
  <a href="accueil.php">🏠 accueil</a>
  <a href="profil.php">👤 Profil</a>
  <a href="panier.php">🛒 Panier</a>
  <a href="logout.php">🚪 Déconnexion</a>
</div>

<div class="main-content">
  <div class="container text-center">
    <h1 class="display-4">Bienvenue sur MonShop</h1>
    <p class="lead">Découvrez nos produits et faites vos achats en ligne facilement.</p>
  </div>

  <div class="container my-4">
    <h2 class="mb-4 text-center">Nos Produits</h2>
    <div id="produits" class="row g-4"></div>
  </div>
</div>

<!-- MODALE produit -->
<div id="product-modal" class="modal">
  <div class="modal-content">
    <button onclick="fermerModal()" style="position:absolute; top:10px; right:15px;" class="btn btn-sm btn-danger">✖</button>
    <div id="modal-content-body">
      <h3 id="modal-title"></h3>
      <img id="modal-image" src="" alt="" class="img-fluid my-3">
      <p id="modal-description"></p>
      <p><strong>Prix :</strong> <span id="modal-price"></span>€</p>

      <label for="modal-qty">Quantité :</label>
      <input type="number" id="modal-qty" class="form-control mb-3" min="1" value="1" />

      <button class="btn btn-primary" id="modal-ajouter">Ajouter au panier</button>
    </div>
  </div>
</div>

<script>
  const username = <?= json_encode($username) ?>;
  const keyPanier = "panier_user_" + username;
  const produitsDiv = document.getElementById("produits");

  let produitsCache = {}; // cache local pour les détails produit

  fetch("https://dummyjson.com/products?limit=12")
    .then(res => res.json())
    .then(data => {
      if (!data.products || data.products.length === 0) {
        produitsDiv.innerHTML = '<p class="text-center">Aucun produit disponible pour le moment.</p>';
        return;
      }

      data.products.forEach(prod => {
        produitsCache[prod.id] = prod;

        const col = document.createElement("div");
        col.className = "col-12 col-sm-6 col-md-4 col-lg-3";
        col.innerHTML = `
          <div class="card h-100">
            <img src="${prod.thumbnail}" class="card-img-top" alt="${prod.title}" onclick="afficherDetailsProduit(${prod.id})">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">${prod.title}</h5>
              <p class="card-text">${prod.price}€</p>
              <button class="btn btn-primary mt-auto" onclick="afficherDetailsProduit(${prod.id})">Voir plus</button>
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

  function afficherDetailsProduit(id) {
    const prod = produitsCache[id];
    if (!prod) return;

    document.getElementById("modal-title").textContent = prod.title;
    document.getElementById("modal-description").textContent = prod.description || "Pas de description.";
    document.getElementById("modal-price").textContent = prod.price;
    document.getElementById("modal-image").src = prod.thumbnail;
    document.getElementById("modal-qty").value = 1;

    document.getElementById("modal-ajouter").onclick = () => {
      const qty = parseInt(document.getElementById("modal-qty").value);
      if (isNaN(qty) || qty < 1) {
        alert("Veuillez entrer une quantité valide.");
        return;
      }
      ajouterAuPanier(prod.id, qty);
      fermerModal();
    };

    document.getElementById("product-modal").style.display = "block";
  }

  function fermerModal() {
    document.getElementById("product-modal").style.display = "none";
  }

  window.onclick = function(event) {
    const modal = document.getElementById("product-modal");
    if (event.target === modal) {
      fermerModal();
    }
  };

  function ajouterAuPanier(id, quantite = 1) {
    let panier = JSON.parse(localStorage.getItem(keyPanier)) || [];

    quantite = parseInt(quantite);
    if (isNaN(quantite) || quantite < 1) quantite = 1;

    const index = panier.findIndex(p => p.id === id);
    if (index > -1) {
      panier[index].quantite = (parseInt(panier[index].quantite) || 0) + quantite;
    } else {
      panier.push({ id, quantite });
    }

    localStorage.setItem(keyPanier, JSON.stringify(panier));
    majCompteurPanier();
  }

  function majCompteurPanier() {
    const panier = JSON.parse(localStorage.getItem(keyPanier)) || [];
    const total = panier.reduce((acc, item) => acc + (parseInt(item.quantite) || 0), 0);
    document.getElementById("cart-count").textContent = total;
  }

  majCompteurPanier();
</script>

</body>
</html>
