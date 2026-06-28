<?php
// ─── Connexion à la base de données MariaDB ───────────────
$conn = mysqli_connect('localhost', 'root', '', 'prev_echec_technolab');

if (!$conn) {
    die('Erreur de connexion : ' . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8');
?>
