<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once 'config/database.php';

// ── Ajouter une filière ──────────────────────────────────────
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $nom    = trim(mysqli_real_escape_string($conn, $_POST['nom_filiere']));
    $niveau = trim(mysqli_real_escape_string($conn, $_POST['niveau']));
    if (!empty($nom) && !empty($niveau)) {
        $sql = "INSERT INTO Filieres (nom_filiere, niveau) VALUES ('$nom', '$niveau')";
        if (mysqli_query($conn, $sql)) {
            $message = ['type' => 'success', 'text' => 'Filière ajoutée avec succès !'];
        } else {
            $message = ['type' => 'error', 'text' => 'Erreur lors de l\'ajout.'];
        }
    } else {
        $message = ['type' => 'error', 'text' => 'Veuillez remplir tous les champs.'];
    }
}

// ── Supprimer une filière ────────────────────────────────────
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    mysqli_query($conn, "DELETE FROM Filieres WHERE id_filiere = $id");
    header('Location: classes.php');
    exit;
}

// ── Filières avec statistiques ───────────────────────────────
$sql = "SELECT
            f.id_filiere,
            f.nom_filiere,
            f.niveau,
            COUNT(DISTINCT e.matricule)   AS nb_etudiants,
            ROUND(AVG(d.moyenne_generale), 2) AS moy_generale,
            SUM(CASE WHEN d.statut_zone = 'Zone rouge'   THEN 1 ELSE 0 END) AS nb_rouge,
            SUM(CASE WHEN d.statut_zone = 'Zone orange'  THEN 1 ELSE 0 END) AS nb_orange,
            SUM(CASE WHEN d.statut_zone = 'Zone verte'   THEN 1 ELSE 0 END) AS nb_verte
        FROM Filieres f
        LEFT JOIN Etudiants e ON e.id_filiere = f.id_filiere
        LEFT JOIN Diagnostics d ON d.matricule_etudiant = e.matricule
        GROUP BY f.id_filiere
        ORDER BY f.niveau, f.nom_filiere";
$result = mysqli_query($conn, $sql);
$filieres = [];
while ($row = mysqli_fetch_assoc($result)) {
    $filieres[] = $row;
}

$pageTitle = "Classes & Filières";
include 'includes/header.php';
?>

<div id="wrapper">
<?php include 'includes/navbar.php'; ?>

<main id="main-content">

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-title">
            <h4><i class="fa-solid fa-building-columns" style="color:#4361ee; margin-right:8px;"></i>Classes & Filières</h4>
            <p><?= count($filieres) ?> filière(s) enregistrée(s)</p>
        </div>
        <div class="top-bar-actions">
            <button onclick="document.getElementById('modal-ajout').style.display='flex'"
                style="background:#4361ee; color:white; border:none; border-radius:8px;
                       padding:9px 18px; font-size:13px; font-weight:600; cursor:pointer;">
                <i class="fa-solid fa-plus"></i> Ajouter une filière
            </button>
        </div>
    </div>

    <!-- Message retour -->
    <?php if (!empty($message)): ?>
        <div style="
            background:<?= $message['type']==='success' ? '#dcfce7' : '#fee2e2' ?>;
            border:1px solid <?= $message['type']==='success' ? '#86efac' : '#fca5a5' ?>;
            color:<?= $message['type']==='success' ? '#15803d' : '#b91c1c' ?>;
            border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:14px;">
            <i class="fa-solid <?= $message['type']==='success' ? 'fa-check-circle' : 'fa-circle-exclamation' ?>"></i>
            <?= htmlspecialchars($message['text']) ?>
        </div>
    <?php endif; ?>

    <!-- Cartes filières -->
    <div class="row g-3">
        <?php foreach ($filieres as $filiere): ?>
            <div class="col-lg-4 col-md-6">
                <div style="
                    background:white; border-radius:12px;
                    border:1px solid #e2e8f0;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06);
                    overflow:hidden;">

                    <!-- En-tête de la carte -->
                    <div style="background:#1F3864; padding:16px 20px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <div>
                                <h6 style="color:white; font-weight:700; font-size:15px; margin:0 0 4px;">
                                    <?= htmlspecialchars($filiere['nom_filiere']) ?>
                                </h6>
                                <span style="
                                    background:rgba(255,255,255,0.2);
                                    color:white; font-size:11px; font-weight:600;
                                    padding:2px 10px; border-radius:10px;">
                                    <?= htmlspecialchars($filiere['niveau']) ?>
                                </span>
                            </div>
                            <div style="text-align:right;">
                                <div style="color:white; font-size:26px; font-weight:800; line-height:1;">
                                    <?= $filiere['nb_etudiants'] ?>
                                </div>
                                <div style="color:rgba(255,255,255,0.7); font-size:11px;">étudiants</div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div style="padding:16px 20px;">

                        <!-- Moyenne générale -->
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <span style="font-size:13px; color:#64748b;">Moyenne générale</span>
                            <strong style="font-size:15px; color:#1e293b;">
                                <?= $filiere['moy_generale'] ? $filiere['moy_generale'].'/20' : '—' ?>
                            </strong>
                        </div>

                        <!-- Répartition zones -->
                        <div style="font-size:12px; color:#64748b; margin-bottom:8px;">Répartition par zone</div>
                        <div style="display:flex; gap:8px; margin-bottom:16px;">
                            <div style="flex:1; text-align:center; background:#dcfce7; border-radius:6px; padding:6px 4px;">
                                <div style="font-weight:800; color:#15803d; font-size:16px;">
                                    <?= $filiere['nb_verte'] ?? 0 ?>
                                </div>
                                <div style="font-size:10px; color:#15803d;">Verte</div>
                            </div>
                            <div style="flex:1; text-align:center; background:#fef9c3; border-radius:6px; padding:6px 4px;">
                                <div style="font-weight:800; color:#a16207; font-size:16px;">
                                    <?= $filiere['nb_orange'] ?? 0 ?>
                                </div>
                                <div style="font-size:10px; color:#a16207;">Orange</div>
                            </div>
                            <div style="flex:1; text-align:center; background:#fee2e2; border-radius:6px; padding:6px 4px;">
                                <div style="font-weight:800; color:#b91c1c; font-size:16px;">
                                    <?= $filiere['nb_rouge'] ?? 0 ?>
                                </div>
                                <div style="font-size:10px; color:#b91c1c;">Rouge</div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="display:flex; gap:8px;">
                            <a href="resultats.php?filiere=<?= $filiere['id_filiere'] ?>"
                               style="flex:1; text-align:center; background:#4361ee; color:white;
                                      padding:8px; border-radius:8px; font-size:13px;
                                      font-weight:600; text-decoration:none;">
                                <i class="fa-solid fa-list"></i> Voir les grades
                            </a>
                            <a href="classes.php?supprimer=<?= $filiere['id_filiere'] ?>"
                               style="background:#fee2e2; color:#ef4444; padding:8px 12px;
                                      border-radius:8px; font-size:13px; text-decoration:none;"
                               onclick="return confirm('Supprimer cette filière ? Les étudiants associés seront aussi supprimés.')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($filieres)): ?>
            <div class="col-12">
                <div style="background:white; border-radius:12px; border:2px dashed #cbd5e1;
                            padding:60px; text-align:center;">
                    <i class="fa-solid fa-building-columns" style="font-size:48px; color:#cbd5e1; display:block; margin-bottom:16px;"></i>
                    <p style="color:#64748b; margin:0;">Aucune filière enregistrée.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

</main>
</div>

<!-- ── Modal : Ajouter une filière ────────────────────────── -->
<div id="modal-ajout" style="
    display:none; position:fixed; inset:0;
    background:rgba(0,0,0,0.5); z-index:9999;
    align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:32px; width:420px; max-width:90vw;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h5 style="font-weight:700; margin:0;">Ajouter une filière</h5>
            <button onclick="document.getElementById('modal-ajout').style.display='none'"
                style="background:none; border:none; font-size:20px; cursor:pointer; color:#64748b;">✕</button>
        </div>
        <form method="POST" action="classes.php">
            <input type="hidden" name="action" value="ajouter">
            <div style="margin-bottom:14px;">
                <label class="form-label">Nom de la filière</label>
                <input type="text" name="nom_filiere" class="form-control"
                       placeholder="Ex : Informatique de Gestion" required>
            </div>
            <div style="margin-bottom:20px;">
                <label class="form-label">Niveau</label>
                <select name="niveau" class="form-control">
                    <option value="L1">L1</option>
                    <option value="L2" selected>L2</option>
                    <option value="L3">L3</option>
                    <option value="M1">M1</option>
                    <option value="M2">M2</option>
                </select>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button"
                    onclick="document.getElementById('modal-ajout').style.display='none'"
                    style="background:#f1f5f9; color:#64748b; border:none; border-radius:8px;
                           padding:10px 20px; cursor:pointer; font-size:14px;">
                    Annuler
                </button>
                <button type="submit"
                    style="background:#4361ee; color:white; border:none; border-radius:8px;
                           padding:10px 20px; cursor:pointer; font-size:14px; font-weight:600;">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

<?php $pageScripts = ''; include 'includes/footer.php'; ?>
