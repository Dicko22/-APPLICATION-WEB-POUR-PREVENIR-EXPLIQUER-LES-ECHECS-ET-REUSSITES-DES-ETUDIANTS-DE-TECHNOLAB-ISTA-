<?php
session_start();

// ─── Si déjà connecté → rediriger vers dashboard ───────────
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'config/database.php';

$erreur = '';

// ─── Traitement du formulaire ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp   = trim($_POST['mot_de_passe'] ?? '');

    if (empty($email) || empty($mdp)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        // Vérification dans la table users
        $email_securise = mysqli_real_escape_string($conn, $email);
        $sql    = "SELECT * FROM users WHERE email = '$email_securise'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Vérification du mot de passe (comparaison simple)
            if ($mdp === $user['mot_de_passe']) {
                // ── Connexion réussie ──
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_nom']  = $user['nom'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email']= $user['email'];

                header('Location: dashboard.php');
                exit;
            } else {
                $erreur = 'Mot de passe incorrect.';
            }
        } else {
            $erreur = 'Aucun compte trouvé avec cet email.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | TechnoLAB-ISTA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="login-wrapper">

    <!-- ── Côté gauche (fond sombre + texte de bienvenue) ── -->
    <div class="login-left">
        <a href="index.php" style="
            display:flex; align-items:center; gap:10px;
            color:white; text-decoration:none; font-size:18px;
            font-weight:700; margin-bottom:48px;">
            <i class="fa-solid fa-graduation-cap" style="color:#818cf8; font-size:26px;"></i>
            TechnoLAB-ISTA
        </a>
        <h2>Bienvenue !</h2>
        <p>
            Connectez-vous à votre compte pour accéder à votre 
            espace personnel et suivre les performances académiques.
        </p>

        <!-- Infos de test -->
        <div style="
            margin-top:40px;
            background:rgba(255,255,255,0.08);
            border:1px solid rgba(255,255,255,0.15);
            border-radius:10px; padding:16px;
            max-width:360px;">
            <p style="color:#818cf8; font-size:12px; font-weight:700; margin-bottom:8px;">
                <i class="fa-solid fa-key"></i> Comptes de démonstration :
            </p>
            <p style="color:rgba(255,255,255,0.7); font-size:13px; margin:0; line-height:1.8;">
                Admin : admin@technolab.ml / admin123<br>
                Prof  : sadio@technolab.ml / prof123<br>
                Étudiant : mamadou@technolab.ml / etudiant123
            </p>
        </div>
    </div>

    <!-- ── Côté droit (formulaire) ─────────────────────────── -->
    <div class="login-right">
        <div class="login-form-box">
            <h3>Identifiez-vous</h3>
            <p class="sub">Entrez vos identifiants pour accéder à la plateforme</p>

            <!-- Message d'erreur -->
            <?php if (!empty($erreur)) : ?>
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($erreur) ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <form method="POST" action="login.php">

                <div style="margin-bottom:16px;">
                    <label class="form-label" for="email">Adresse e-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="Entrez votre adresse e-mail"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required>
                </div>

                <div style="margin-bottom:8px;">
                    <label class="form-label" for="mot_de_passe">Mot de passe</label>
                    <input
                        type="password"
                        id="mot_de_passe"
                        name="mot_de_passe"
                        class="form-control"
                        placeholder="Entrez votre mot de passe"
                        required>
                </div>

                <div style="text-align:right; margin-bottom:20px;">
                    <a href="#" style="font-size:13px; color:#4361ee; text-decoration:none;">
                        Vous avez oublié votre mot de passe ?
                    </a>
                </div>

                <button type="submit" class="btn-login">
                    Se connecter &rarr;
                </button>
            </form>

            <div class="link-register">
                Vous n'avez pas de compte ?
                <a href="#">Inscrivez-vous</a>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
