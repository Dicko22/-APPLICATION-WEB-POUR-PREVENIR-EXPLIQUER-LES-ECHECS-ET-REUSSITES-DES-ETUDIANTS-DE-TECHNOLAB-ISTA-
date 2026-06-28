<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechnoLAB-ISTA | Suivi Académique Intelligent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<section class="hero-section">

    <!-- ── Barre de navigation ────────────────────────────── -->
    <nav class="hero-navbar">
        <a href="index.php" class="hero-nav-logo">
            <i class="fa-solid fa-graduation-cap" style="color:#818cf8; font-size:24px;"></i>
            TechnoLAB-ISTA
        </a>
        <ul class="hero-nav-links">
            <li><a href="#accueil">Accueil</a></li>
            <li><a href="#fonctionnalites">Fonctionnalités</a></li>
            <li><a href="#contact">Contact</a></li>
            <li>
                <a href="login.php" style="
                    background:#4361ee; color:white;
                    padding:8px 20px; border-radius:8px;
                    font-weight:600;">
                    Se connecter →
                </a>
            </li>
        </ul>
    </nav>

    <!-- ── Section héro ───────────────────────────────────── -->
    <div class="hero-content" id="accueil">
        <span class="hero-badge">
            <i class="fa-solid fa-star" style="font-size:10px;"></i>
            Plateforme académique intelligente
        </span>
        <h1 class="hero-title">
            Suivi académique <span>intelligent</span><br>
            pour la réussite des étudiants<br>
            de TechnoLAB-ISTA
        </h1>
        <p class="hero-desc">
            Notre plateforme centralise les données académiques (notes, absences) 
            et détecte automatiquement les étudiants en difficulté avant qu'il 
            ne soit trop tard. Administrez, analysez et agissez en temps réel.
        </p>
        <div class="hero-buttons">
            <a href="login.php" class="btn-hero-primary">
                <i class="fa-solid fa-right-to-bracket"></i> Se connecter
            </a>
            <a href="#fonctionnalites" class="btn-hero-outline">
                <i class="fa-solid fa-circle-play"></i> Découvrir la plateforme
            </a>
        </div>
    </div>

    <!-- ── Bande de fonctionnalités ───────────────────────── -->
    <div class="features-strip" id="fonctionnalites">
        <div class="feature-card">
            <i class="fa-solid fa-magnifying-glass-chart"></i>
            <h6>Détection Précoce</h6>
            <p>Identifiez automatiquement les étudiants à risque avant la fin du semestre.</p>
        </div>
        <div class="feature-card">
            <i class="fa-solid fa-bell"></i>
            <h6>Alertes Intelligentes</h6>
            <p>Système de zones Verte / Orange / Rouge basé sur notes et absences.</p>
        </div>
        <div class="feature-card">
            <i class="fa-solid fa-chart-line"></i>
            <h6>Tableau de Bord</h6>
            <p>Visualisez en un coup d'œil la santé académique de tout l'établissement.</p>
        </div>
        <div class="feature-card">
            <i class="fa-solid fa-users"></i>
            <h6>Multi-rôles</h6>
            <p>Accès adapté pour l'administration, les enseignants et les étudiants.</p>
        </div>
    </div>

</section>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
