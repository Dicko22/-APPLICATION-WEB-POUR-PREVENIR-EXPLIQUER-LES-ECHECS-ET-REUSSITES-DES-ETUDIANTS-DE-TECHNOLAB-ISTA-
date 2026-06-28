<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once 'config/database.php';

// ─── Matricule passé en paramètre ───────────────────────────
$matricule = isset($_GET['matricule'])
    ? mysqli_real_escape_string($conn, $_GET['matricule'])
    : '';

if (empty($matricule)) {
    header('Location: resultats.php');
    exit;
}

// ─── Données de l'étudiant ───────────────────────────────────
$sql_etudiant = "
    SELECT e.*, f.nom_filiere, d.moyenne_generale, d.total_absences, d.statut_zone
    FROM Etudiants e
    LEFT JOIN Filieres f ON f.id_filiere = e.id_filiere
    LEFT JOIN Diagnostics d ON d.matricule_etudiant = e.matricule
    WHERE e.matricule = '$matricule'
    LIMIT 1";
$result_etudiant = mysqli_query($conn, $sql_etudiant);

if (!$result_etudiant || mysqli_num_rows($result_etudiant) === 0) {
    echo "<p>Étudiant introuvable.</p>";
    exit;
}
$etudiant = mysqli_fetch_assoc($result_etudiant);

// ─── Notes détaillées ───────────────────────────────────────
$sql_notes = "
    SELECT
        m.nom_matiere,
        m.coefficient,
        n.note_devoir,
        n.note_examen,
        n.semestre,
        ROUND((n.note_devoir + n.note_examen) / 2, 2) AS note_finale,
        ROUND(((n.note_devoir + n.note_examen) / 2) * m.coefficient, 2) AS note_ponderee
    FROM Notes n
    JOIN Matieres m ON m.id_matiere = n.id_matiere
    WHERE n.matricule_etudiant = '$matricule'
    ORDER BY n.semestre, m.nom_matiere";
$result_notes = mysqli_query($conn, $sql_notes);

// ─── Calcul des totaux ───────────────────────────────────────
$total_coef     = 0;
$total_ponderee = 0;
$all_notes      = [];

while ($note = mysqli_fetch_assoc($result_notes)) {
    $all_notes[]     = $note;
    $total_coef     += $note['coefficient'];
    $total_ponderee += $note['note_ponderee'];
}
$moy_calculee = ($total_coef > 0) ? round($total_ponderee / $total_coef, 2) : 0;

// ─── Taux de réussite ────────────────────────────────────────
$taux = round(min(100, ($moy_calculee / 20) * 100));

// ─── Initiales pour l'avatar ─────────────────────────────────
$initiales = strtoupper(
    substr($etudiant['prenom'], 0, 1) . substr($etudiant['nom'], 0, 1)
);

// ─── Appréciation ────────────────────────────────────────────
function appreciation($note) {
    if ($note >= 16) return ['EXCELLENT',  'badge-excellent'];
    if ($note >= 14) return ['BIEN',       'badge-bien'];
    if ($note >= 12) return ['ASSEZ-BIEN', 'badge-assez-bien'];
    if ($note >= 10) return ['PASSABLE',   'badge-passable'];
    if ($note >= 8)  return ['INSUFFISANT','badge-insuffisant'];
    return ['MAL', 'badge-mal'];
}

$pageTitle = htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']);
include 'includes/header.php';
?>

<div id="wrapper">
<?php include 'includes/navbar.php'; ?>

<main id="main-content">

    <!-- ── Top Bar ─────────────────────────────────────────── -->
    <div class="top-bar">
        <div class="top-bar-title">
            <h4>Profil détaillé</h4>
            <p>
                <a href="resultats.php" style="color:#4361ee; text-decoration:none;">Grades détaillés</a>
                &rsaquo; <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?>
            </p>
        </div>
        <div class="top-bar-actions">
            <a href="resultats.php" style="
                background:white; border:1px solid #e2e8f0;
                color:#64748b; padding:9px 16px;
                border-radius:8px; font-size:13px; text-decoration:none;
                display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- ── Carte profil étudiant ─────────────────────────────── -->
    <div class="profil-card mb-4">

        <!-- Avatar + Infos -->
        <div class="profil-avatar"><?= $initiales ?></div>
        <div class="profil-info">
            <h5><?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></h5>
            <p>
                Matricule : <strong><?= htmlspecialchars($etudiant['matricule']) ?></strong>
                &bull; <?= htmlspecialchars($etudiant['nom_filiere'] ?? 'N/A') ?>
            </p>
            <p style="margin-top:6px;">
                <?php
                $zone = $etudiant['statut_zone'] ?? '';
                if ($zone === 'Zone rouge') {
                    echo '<span class="badge-mal">Zone Rouge</span>';
                } elseif ($zone === 'Zone orange') {
                    echo '<span class="badge-insuffisant">Zone Orange</span>';
                } elseif ($zone === 'Zone verte') {
                    echo '<span class="badge-excellent">Zone Verte</span>';
                } else {
                    echo '<span style="color:#94a3b8; font-size:13px;">Diagnostic non disponible</span>';
                }
                ?>
            </p>
        </div>

        <!-- Statistiques du profil -->
        <div class="profil-stats">
            <div class="profil-stat-item">
                <div class="stat-val"><?= $moy_calculee ?><span style="font-size:14px; font-weight:400; color:#64748b;">/20</span></div>
                <div class="stat-label">Moyenne générale</div>
            </div>
            <div class="profil-stat-item">
                <div class="stat-val" style="color:<?= $taux >= 50 ? '#22c55e' : '#ef4444' ?>;"><?= $taux ?>%</div>
                <div class="stat-label">Taux de réussite</div>
            </div>
            <div class="profil-stat-item">
                <div class="stat-val"><?= $etudiant['total_absences'] ?? 0 ?></div>
                <div class="stat-label">Heures d'absence</div>
            </div>
            <div class="profil-stat-item">
                <div class="stat-val"><?= $total_coef ?></div>
                <div class="stat-label">Total crédits</div>
            </div>
        </div>
    </div>

    <!-- ── Tableau des notes ─────────────────────────────────── -->
    <div class="table-wrapper">
        <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0;">
            <h6 style="font-weight:700; font-size:15px; margin:0;">
                <i class="fa-solid fa-clipboard-list" style="color:#4361ee;"></i>
                Relevé de notes détaillé
            </h6>
        </div>

        <table class="table table-notes mb-0">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Coefficient</th>
                    <th>Note devoir</th>
                    <th>Note examen</th>
                    <th>Moyenne /20</th>
                    <th>Moy. pondérée</th>
                    <th>Appréciation</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($all_notes)): ?>
                    <?php foreach ($all_notes as $note): ?>
                        <?php [$appre, $badge] = appreciation($note['note_finale']); ?>
                        <tr>
                            <td style="font-weight:600;"><?= htmlspecialchars($note['nom_matiere']) ?></td>
                            <td><?= $note['coefficient'] ?></td>
                            <td><?= $note['note_devoir'] ?>/20</td>
                            <td><?= $note['note_examen'] ?>/20</td>
                            <td><strong><?= $note['note_finale'] ?></strong></td>
                            <td><?= $note['note_ponderee'] ?></td>
                            <td><span class="<?= $badge ?>"><?= $appre ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding:32px; color:#94a3b8;">
                            Aucune note enregistrée pour cet étudiant.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <!-- Totaux -->
            <tfoot>
                <tr>
                    <td><strong>TOTAL</strong></td>
                    <td><strong><?= $total_coef ?></strong></td>
                    <td colspan="2"></td>
                    <td><strong><?= $moy_calculee ?>/20</strong></td>
                    <td><strong><?= round($total_ponderee, 2) ?></strong></td>
                    <td>
                        <?php [$appre, $badge] = appreciation($moy_calculee); ?>
                        <span class="<?= $badge ?>"><?= $appre ?></span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</main>
</div>

<?php $pageScripts = ''; include 'includes/footer.php'; ?>
