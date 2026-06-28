<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once 'config/database.php';

$sql = "SELECT e.*, f.nom_filiere, d.moyenne_generale, d.statut_zone
        FROM Etudiants e
        LEFT JOIN Filieres f ON f.id_filiere = e.id_filiere
        LEFT JOIN Diagnostics d ON d.matricule_etudiant = e.matricule
        ORDER BY e.nom ASC";
$result = mysqli_query($conn, $sql);

$pageTitle = "Étudiants";
include 'includes/header.php';
?>

<div id="wrapper">
<?php include 'includes/navbar.php'; ?>

<main id="main-content">

    <div class="top-bar">
        <div class="top-bar-title">
            <h4><i class="fa-solid fa-user-graduate" style="color:#4361ee; margin-right:8px;"></i>Liste des étudiants</h4>
            <p>Gestion et suivi de tous les étudiants</p>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Matricule</th>
                    <th>Email</th>
                    <th>Filière</th>
                    <th>Moyenne</th>
                    <th>Zone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php $i=1; while ($row = mysqli_fetch_assoc($result)): ?>
                <?php
                $zone  = $row['statut_zone'] ?? '';
                $badge = $zone === 'Zone rouge' ? 'badge-mal'
                       : ($zone === 'Zone orange' ? 'badge-insuffisant' : 'badge-excellent');
                ?>
                <tr>
                    <td style="color:#94a3b8;"><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['prenom']) ?></td>
                    <td style="font-weight:600;"><?= htmlspecialchars($row['nom']) ?></td>
                    <td style="font-family:monospace; font-size:13px; color:#64748b;"><?= htmlspecialchars($row['matricule']) ?></td>
                    <td style="font-size:13px;"><?= htmlspecialchars($row['email'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['nom_filiere'] ?? '—') ?></td>
                    <td><?= $row['moyenne_generale'] ? $row['moyenne_generale'].'/20' : '—' ?></td>
                    <td><?php if($zone) echo "<span class='$badge'>$zone</span>"; else echo '—'; ?></td>
                    <td>
                        <a href="profil.php?matricule=<?= urlencode($row['matricule']) ?>"
                           style="font-size:13px; color:#4361ee; text-decoration:none;">
                            <i class="fa-solid fa-eye"></i> Profil
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</main>
</div>

<?php $pageScripts = ''; include 'includes/footer.php'; ?>
