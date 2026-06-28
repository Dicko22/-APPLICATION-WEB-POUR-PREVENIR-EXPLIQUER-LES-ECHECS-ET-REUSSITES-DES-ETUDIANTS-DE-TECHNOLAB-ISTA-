<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once 'config/database.php';

// Récupérer toutes les alertes (Zone rouge + orange)
$sql = "SELECT e.matricule, e.nom, e.prenom, f.nom_filiere,
               d.statut_zone, d.moyenne_generale, d.total_absences, d.date_analyse
        FROM Diagnostics d
        JOIN Etudiants e ON e.matricule = d.matricule_etudiant
        LEFT JOIN Filieres f ON f.id_filiere = e.id_filiere
        WHERE d.statut_zone IN ('Zone rouge', 'Zone orange')
        ORDER BY d.statut_zone ASC, d.moyenne_generale ASC";
$result = mysqli_query($conn, $sql);

$pageTitle = "Alertes";
include 'includes/header.php';
?>

<div id="wrapper">
<?php include 'includes/navbar.php'; ?>

<main id="main-content">

    <div class="top-bar">
        <div class="top-bar-title">
            <h4><i class="fa-solid fa-bell" style="color:#ef4444; margin-right:8px;"></i>Alertes actives</h4>
            <p>Étudiants en situation de décrochage scolaire</p>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Étudiant</th>
                    <th>Matricule</th>
                    <th>Filière</th>
                    <th>Moyenne</th>
                    <th>Absences</th>
                    <th>Zone</th>
                    <th>Date analyse</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <?php
                        $zone  = $row['statut_zone'];
                        $badge = ($zone === 'Zone rouge') ? 'badge-mal' : 'badge-insuffisant';
                        ?>
                        <tr>
                            <td style="font-weight:600;">
                                <?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?>
                            </td>
                            <td style="font-family:monospace; font-size:13px; color:#64748b;">
                                <?= htmlspecialchars($row['matricule']) ?>
                            </td>
                            <td><?= htmlspecialchars($row['nom_filiere'] ?? '—') ?></td>
                            <td><strong><?= $row['moyenne_generale'] ?></strong>/20</td>
                            <td><?= $row['total_absences'] ?> h</td>
                            <td><span class="<?= $badge ?>"><?= htmlspecialchars($zone) ?></span></td>
                            <td style="font-size:13px; color:#64748b;">
                                <?= date('d/m/Y', strtotime($row['date_analyse'])) ?>
                            </td>
                            <td>
                                <a href="profil.php?matricule=<?= urlencode($row['matricule']) ?>"
                                   style="font-size:13px; color:#4361ee; text-decoration:none;">
                                    <i class="fa-solid fa-eye"></i> Voir profil
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:40px; color:#94a3b8;">
                            <i class="fa-solid fa-check-circle" style="font-size:32px; color:#22c55e; display:block; margin-bottom:10px;"></i>
                            Aucune alerte active. Tous les étudiants sont en zone verte !
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>
</div>

<?php $pageScripts = ''; include 'includes/footer.php'; ?>
