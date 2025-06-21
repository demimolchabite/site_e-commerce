<?php
// Configuration du cookie de session (7h de durée de vie)
$cookieLifetime = 60 * 60 * 7;

session_set_cookie_params([
    'lifetime' => $cookieLifetime,
    'path' => '/',
    'secure' => false, // mettre true en HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Démarrer la session (ne doit être appelé qu'une seule fois)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
