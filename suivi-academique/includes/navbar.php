<?php
// Détermine la page active pour le menu
$pageCourante = basename($_SERVER['PHP_SELF']);
function actif($pages) {
    global $pageCourante;
    return in_array($pageCourante, (array)$pages) ? 'active' : '';
}
?>

<!-- ═══════════════════════════════════════════════════════════
     BARRE DE NAVIGATION LATÉRALE (SIDEBAR)
═══════════════════════════════════════════════════════════ -->
<nav id="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <i class="fa-solid fa-graduation-cap"></i>
        <div>
            <span class="logo-title">TechnoLAB-ISTA</span>
            <span class="logo-sub">Suivi académique</span>
        </div>
    </div>

    <hr class="sidebar-hr">

    <!-- Menu principal -->
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?= actif('dashboard.php') ?>">
                <i class="fa-solid fa-house"></i> Accueil
            </a>
        </li>
        <li>
            <a href="dashboard.php" class="<?= actif('dashboard.php') ?>">
                <i class="fa-solid fa-chart-line"></i> Tableau de bord
            </a>
        </li>
        <li>
            <a href="etudiants.php" class="<?= actif('etudiants.php') ?>">
                <i class="fa-solid fa-user-graduate"></i> Étudiants
            </a>
        </li>
        <li>
            <a href="professeurs.php" class="<?= actif('professeurs.php') ?>">
                <i class="fa-solid fa-chalkboard-user"></i> Professeurs
            </a>
        </li>
        <li>
            <a href="classes.php" class="<?= actif('classes.php') ?>">
                <i class="fa-solid fa-building-columns"></i> Classes & Filières
            </a>
        </li>
        <li>
            <a href="absences.php" class="<?= actif('absences.php') ?>">
                <i class="fa-solid fa-calendar-xmark"></i> Absences
            </a>
        </li>
        <li>
            <a href="alertes.php" class="<?= actif('alertes.php') ?>">
                <i class="fa-solid fa-bell"></i> Alertes
                <?php
                // Afficher le nombre d'alertes actives (si BD connectée)
                if (isset($conn)) {
                    $r = mysqli_query($conn, "SELECT COUNT(*) AS nb FROM Diagnostics WHERE statut_zone = 'Zone rouge'");
                    $nb = mysqli_fetch_assoc($r)['nb'] ?? 0;
                    if ($nb > 0) echo "<span class='badge-alert'>$nb</span>";
                }
                ?>
            </a>
        </li>
        <li>
            <a href="statistiques.php" class="<?= actif('statistiques.php') ?>">
                <i class="fa-solid fa-chart-pie"></i> Statistiques
            </a>
        </li>
        <li>
            <a href="resultats.php" class="<?= actif('resultats.php') ?>">
                <i class="fa-solid fa-clipboard-list"></i> Notes & Évaluations
            </a>
        </li>
        <li>
            <a href="parametres.php" class="<?= actif('parametres.php') ?>">
                <i class="fa-solid fa-gear"></i> Paramètres
            </a>
        </li>
    </ul>

    <!-- Profil utilisateur (bas de sidebar) -->
    <div class="sidebar-user">
        <div class="user-avatar">
            <?= strtoupper(substr($_SESSION['user_nom'] ?? 'A', 0, 1)) ?>
        </div>
        <div class="user-info">
            <span class="user-nom"><?= htmlspecialchars($_SESSION['user_nom'] ?? 'Admin') ?></span>
            <span class="user-role"><?= htmlspecialchars(ucfirst($_SESSION['user_role'] ?? 'Administrateur')) ?></span>
        </div>
        <a href="logout.php" class="btn-logout" title="Déconnexion">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</nav>
<!-- FIN SIDEBAR -->
