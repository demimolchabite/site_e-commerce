<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php?msg=" . urlencode("Veuillez vous connecter pour accéder au panier."));
    exit();
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Mon Panier</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .card-img-top {
      height: 100px;
      object-fit: cover;
    }
    .table th, .table td {
      vertical-align: middle;
    }
    /* Styles modale */
    #modal-supprimer {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.5);
      z-index: 1050;
    }
    #modal-supprimer .modal-content {
      background: white;
      max-width: 400px;
      margin: 100px auto;
      padding: 20px;
      border-radius: 8px;
      position: relative;
    }
  </style>
</head>
<body class="bg-light">

<div class="container mt-5">
  <h3>🛒 Mon Panier</h3>
  <div id="panier-container">
    <p>Chargement du panier...</p>
  </div>
  <a href="accueil.php" class="btn btn-outline-primary mt-3">← Retour à l'accueil</a>
</div>

<!-- Modale suppression -->
<div id="modal-supprimer">
  <div class="modal-content">
    <h5>Supprimer un produit</h5>
    <p id="modal-produit-nom"></p>
    <label for="quantite-supprimer">Quantité à supprimer :</label>
    <input type="number" id="quantite-supprimer" class="form-control mb-3" min="1" />
    <div class="d-flex justify-content-end gap-2">
      <button class="btn btn-secondary" onclick="fermerModaleSupprimer()">Annuler</button>
      <button class="btn btn-danger" onclick="confirmerSuppression()">Supprimer</button>
    </div>
  </div>
</div>

<script>
  const username = <?= json_encode($username) ?>;
  const keyPanier = "panier_user_" + username;

  let panier = JSON.parse(localStorage.getItem(keyPanier)) || [];
  const panierContainer = document.getElementById("panier-container");

  let produits = [];
  let produitEnCoursSuppression = null;

  if (panier.length === 0) {
    panierContainer.innerHTML = "<p>Votre panier est vide.</p>";
  } else {
    fetch("https://dummyjson.com/products?limit=100")
      .then(res => res.json())
      .then(data => {
        produits = data.products;
        afficherPanier();
      })
      .catch(err => {
        panierContainer.innerHTML = '<p class="text-danger">Erreur lors du chargement des produits du panier.</p>';
        console.error(err);
      });
  }

  function afficherPanier() {
    let totalGlobal = 0;

    let html = `
      <table class="table table-bordered table-striped align-middle mt-4">
        <thead class="table-dark">
          <tr>
            <th>Image</th>
            <th>Produit</th>
            <th>Prix unitaire</th>
            <th>Quantité</th>
            <th>Total</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
    `;

    panier.forEach(item => {
      const prod = produits.find(p => p.id === item.id);
      if (!prod) return;

      const total = prod.price * item.quantite;
      totalGlobal += total;

      html += `
        <tr>
          <td><img src="${prod.thumbnail}" class="card-img-top" alt="${prod.title}"></td>
          <td>${prod.title}</td>
          <td>${prod.price.toFixed(2)} €</td>
          <td>${item.quantite}</td>
          <td>${total.toFixed(2)} €</td>
          <td>
            <button class="btn btn-sm btn-danger" onclick="ouvrirModaleSupprimer(${item.id})">🗑️ Supprimer</button>
          </td>
        </tr>
      `;
    });

    html += `
        </tbody>
        <tfoot>
          <tr class="table-secondary">
            <th colspan="4" class="text-end">Total général :</th>
            <th colspan="2"><strong>${totalGlobal.toFixed(2)} €</strong></th>
          </tr>
        </tfoot>
      </table>
    `;

    panierContainer.innerHTML = html;
  }

  // Ouvrir la modale suppression avec la quantité max par défaut
  function ouvrirModaleSupprimer(id) {
    produitEnCoursSuppression = panier.find(item => item.id === id);
    if (!produitEnCoursSuppression) return;

    const prod = produits.find(p => p.id === id);
    if (!prod) return;

    document.getElementById('modal-produit-nom').textContent = `Produit : ${prod.title}`;
    const inputQty = document.getElementById('quantite-supprimer');
    inputQty.value = produitEnCoursSuppression.quantite;
    inputQty.max = produitEnCoursSuppression.quantite;
    inputQty.min = 1;

    document.getElementById('modal-supprimer').style.display = 'block';
  }

  function fermerModaleSupprimer() {
    document.getElementById('modal-supprimer').style.display = 'none';
    produitEnCoursSuppression = null;
  }

  function confirmerSuppression() {
    if (!produitEnCoursSuppression) return;

    const inputQty = document.getElementById('quantite-supprimer');
    let qtyASupprimer = parseInt(inputQty.value);

    if (isNaN(qtyASupprimer) || qtyASupprimer < 1) {
      alert("Veuillez saisir une quantité valide à supprimer.");
      return;
    }

    if (qtyASupprimer > produitEnCoursSuppression.quantite) {
      alert(`La quantité à supprimer ne peut pas dépasser ${produitEnCoursSuppression.quantite}.`);
      return;
    }

    // Mise à jour du panier
    if (qtyASupprimer === produitEnCoursSuppression.quantite) {
      // suppression complète du produit
      panier = panier.filter(item => item.id !== produitEnCoursSuppression.id);
    } else {
      // réduction de la quantité
      produitEnCoursSuppression.quantite -= qtyASupprimer;
    }

    localStorage.setItem(keyPanier, JSON.stringify(panier));
    fermerModaleSupprimer();
    afficherPanier();
  }

  // Fermer modale si clic en dehors
  window.onclick = function(event) {
    const modal = document.getElementById('modal-supprimer');
    if (event.target === modal) {
      fermerModaleSupprimer();
    }
  }
</script>

</body>
</html>
