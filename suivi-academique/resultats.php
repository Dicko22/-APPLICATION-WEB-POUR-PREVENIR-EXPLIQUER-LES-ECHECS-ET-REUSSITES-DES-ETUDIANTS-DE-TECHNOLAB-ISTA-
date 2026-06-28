<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once 'config/database.php';

// ─── Filtres ────────────────────────────────────────────────
$filiere_id = isset($_GET['filiere']) ? (int)$_GET['filiere'] : 0;
$recherche  = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';

// ─── Liste des filières (pour le filtre) ────────────────────
$result_filieres = mysqli_query($conn, "SELECT * FROM Filieres ORDER BY nom_filiere");

// ─── Requête principale : étudiants + moyennes ──────────────
$where = [];
if ($filiere_id > 0) {
    $where[] = "e.id_filiere = $filiere_id";
}
if (!empty($recherche)) {
    $r = mysqli_real_escape_string($conn, $recherche);
    $where[] = "(e.nom LIKE '%$r%' OR e.prenom LIKE '%$r%' OR e.matricule LIKE '%$r%')";
}
$clause_where = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "
    SELECT
        e.matricule,
        e.nom,
        e.prenom,
        f.nom_filiere,
        d.moyenne_generale,
        d.statut_zone
    FROM Etudiants e
    LEFT JOIN Filieres f ON f.id_filiere = e.id_filiere
    LEFT JOIN Diagnostics d ON d.matricule_etudiant = e.matricule
    $clause_where
    ORDER BY d.moyenne_generale DESC";

$result = mysqli_query($conn, $sql);
$total_resultats = $result ? mysqli_num_rows($result) : 0;

// ─── Fonction : badge de statut ─────────────────────────────
function badgeNote($moy) {
    if ($moy === null) return '<span style="color:#94a3b8;">—</span>';
    if ($moy >= 16) return '<span class="badge-excellent">EXCELLENT</span>';
    if ($moy >= 14) return '<span class="badge-bien">BIEN</span>';
    if ($moy >= 12) return '<span class="badge-assez-bien">ASSEZ-BIEN</span>';
    if ($moy >= 10) return '<span class="badge-passable">PASSABLE</span>';
    if ($moy >= 8)  return '<span class="badge-insuffisant">INSUFFISANT</span>';
    return '<span class="badge-mal">MAL</span>';
}

function tauxReussite($moy) {
    if ($moy === null) return '—';
    return round(min(100, ($moy / 20) * 100)) . '%';
}

$pageTitle = "Grades détaillés";
include 'includes/header.php';
?>

<div id="wrapper">
<?php include 'includes/navbar.php'; ?>

<main id="main-content">

    <!-- ── Top Bar ─────────────────────────────────────────── -->
    <div class="top-bar">
        <div class="top-bar-title">
            <h4>Grades détaillés</h4>
            <p>Notes & Évaluations → Grades détaillés</p>
        </div>
        <div class="top-bar-actions">
            <!-- Export CSV -->
            <a href="export_csv.php?filiere=<?= $filiere_id ?>&recherche=<?= urlencode($recherche) ?>"
               style="
                background:#4361ee; color:white;
                padding:9px 18px; border-radius:8px;
                font-size:13px; font-weight:600; text-decoration:none;
                display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-file-export"></i> Exporter
            </a>
        </div>
    </div>

    <!-- ── Tableau avec filtres ────────────────────────────── -->
    <div class="table-wrapper">

        <!-- Filtres -->
        <form method="GET" action="resultats.php">
            <div class="table-filters">
                <!-- Filière -->
                <select name="filiere">
                    <option value="0">Toutes les filières</option>
                    <?php
                    while ($f = mysqli_fetch_assoc($result_filieres)) {
                        $sel = ($f['id_filiere'] == $filiere_id) ? 'selected' : '';
                        echo "<option value='{$f['id_filiere']}' $sel>{$f['nom_filiere']}</option>";
                    }
                    ?>
                </select>

                <!-- Recherche -->
                <input
                    type="text"
                    name="recherche"
                    placeholder="Rechercher un étudiant..."
                    value="<?= htmlspecialchars($recherche) ?>"
                    style="min-width:220px;">

                <button type="submit" style="
                    background:#4361ee; color:white;
                    border:none; border-radius:8px;
                    padding:7px 16px; font-size:13px;
                    font-weight:600; cursor:pointer;">
                    <i class="fa-solid fa-magnifying-glass"></i> Filtrer
                </button>

                <?php if ($filiere_id || $recherche): ?>
                    <a href="resultats.php" style="font-size:13px; color:#64748b; text-decoration:none;">
                        <i class="fa-solid fa-xmark"></i> Effacer
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Tableau -->
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Matricule</th>
                    <th>Filière</th>
                    <th>Moy. générale</th>
                    <th>Taux de réussite</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_resultats > 0): ?>
                    <?php $i = 1; while ($etudiant = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td style="color:#94a3b8;"><?= $i++ ?></td>
                            <td><?= htmlspecialchars($etudiant['prenom']) ?></td>
                            <td style="font-weight:600;"><?= htmlspecialchars($etudiant['nom']) ?></td>
                            <td style="font-family:monospace; font-size:13px; color:#64748b;">
                                <?= htmlspecialchars($etudiant['matricule']) ?>
                            </td>
                            <td style="font-size:13px;"><?= htmlspecialchars($etudiant['nom_filiere'] ?? '—') ?></td>
                            <td>
                                <?php if ($etudiant['moyenne_generale'] !== null): ?>
                                    <strong><?= $etudiant['moyenne_generale'] ?></strong>
                                    <span style="color:#94a3b8; font-size:12px;">/20</span>
                                <?php else: ?>
                                    <span style="color:#94a3b8;">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?= tauxReussite($etudiant['moyenne_generale']) ?></td>
                            <td><?= badgeNote($etudiant['moyenne_generale']) ?></td>
                            <td>
                                <a href="profil.php?matricule=<?= urlencode($etudiant['matricule']) ?>"
                                   style="font-size:13px; color:#4361ee; text-decoration:none;"
                                   title="Voir le profil">
                                    <i class="fa-solid fa-eye"></i> Profil
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding:32px; color:#94a3b8;">
                            <i class="fa-solid fa-users-slash" style="font-size:24px; margin-bottom:8px; display:block;"></i>
                            Aucun étudiant trouvé.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination (info) -->
        <div class="pagination-wrapper">
            <span>Affichage de <?= min(1, $total_resultats) ?> à <?= $total_resultats ?> étudiant(s)</span>
            <div style="display:flex; gap:4px;">
                <span style="
                    background:#4361ee; color:white;
                    width:28px; height:28px; border-radius:6px;
                    display:flex; align-items:center; justify-content:center;
                    font-size:13px; font-weight:700;">1</span>
            </div>
        </div>
    </div>

</main>
</div>

<?php $pageScripts = ''; include 'includes/footer.php'; ?>
