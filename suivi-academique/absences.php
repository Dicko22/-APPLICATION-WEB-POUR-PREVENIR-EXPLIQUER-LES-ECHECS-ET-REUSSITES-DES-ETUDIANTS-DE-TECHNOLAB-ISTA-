<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once 'config/database.php';
$pageTitle = "Absences";
include 'includes/header.php';
?>

<div id="wrapper">
<?php include 'includes/navbar.php'; ?>

<main id="main-content">

    <div class="top-bar">
        <div class="top-bar-title">
            <h4><i class="fa-solid fa-calendar-xmark" style="color:#4361ee; margin-right:8px;"></i>Absences</h4>
            <p>Suivi et pointage des présences</p>
        </div>
    </div>

    <!-- Contenu à développer -->
    <div style="
        background: white;
        border-radius: 12px;
        border: 2px dashed #cbd5e1;
        padding: 60px 40px;
        text-align: center;
        margin-top: 16px;">

        <i class="fa-solid fa-calendar-xmark" style="font-size:48px; color:#cbd5e1; margin-bottom:20px; display:block;"></i>

        <h5 style="font-size:18px; font-weight:700; color:#1e293b; margin-bottom:8px;">
            Absences
        </h5>
        <p style="color:#64748b; font-size:14px; max-width:400px; margin:0 auto 24px;">
            Cette section est en cours de développement.
            Elle sera disponible dans la prochaine version de l'application.
        </p>

        <a href="dashboard.php" style="
            background:#4361ee; color:white;
            padding:10px 24px; border-radius:8px;
            text-decoration:none; font-size:14px; font-weight:600;">
            <i class="fa-solid fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>

</main>
</div>

<?php $pageScripts = ''; include 'includes/footer.php'; ?>
