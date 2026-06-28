<?php
session_start();

// ─── Vérification de la session ────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

// ─── Requêtes SQL pour les KPI ────────────────────────────
// Total étudiants
$r = mysqli_query($conn, "SELECT COUNT(*) AS nb FROM Etudiants");
$total_etudiants = mysqli_fetch_assoc($r)['nb'] ?? 0;

// Alertes (Zone rouge)
$r = mysqli_query($conn, "SELECT COUNT(*) AS nb FROM Diagnostics WHERE statut_zone = 'Zone rouge'");
$total_alertes = mysqli_fetch_assoc($r)['nb'] ?? 0;

// Difficulté (Zone orange)
$r = mysqli_query($conn, "SELECT COUNT(*) AS nb FROM Diagnostics WHERE statut_zone = 'Zone orange'");
$total_difficulte = mysqli_fetch_assoc($r)['nb'] ?? 0;

// Réussite (Zone verte)
$r = mysqli_query($conn, "SELECT COUNT(*) AS nb FROM Diagnostics WHERE statut_zone = 'Zone verte'");
$total_reussite = mysqli_fetch_assoc($r)['nb'] ?? 0;

// Taux de réussite
$taux = ($total_etudiants > 0)
    ? round(($total_reussite / $total_etudiants) * 100, 1)
    : 0;

// ─── Alertes récentes (5 dernières) ─────────────────────────
$sql_alertes = "
    SELECT e.nom, e.prenom, e.matricule, d.statut_zone, d.moyenne_generale, d.date_analyse
    FROM Diagnostics d
    JOIN Etudiants e ON e.matricule = d.matricule_etudiant
    ORDER BY d.date_analyse DESC
    LIMIT 5";
$result_alertes = mysqli_query($conn, $sql_alertes);

// ─── Données pour le graphique en courbe ─────────────────────
// Moyenne générale par semestre
$sql_courbe = "SELECT semestre, AVG((note_devoir + note_examen) / 2) AS moy
               FROM Notes GROUP BY semestre ORDER BY semestre";
$result_courbe = mysqli_query($conn, $sql_courbe);
$labels_courbe = [];
$data_courbe   = [];
while ($row = mysqli_fetch_assoc($result_courbe)) {
    $labels_courbe[] = 'Sem ' . $row['semestre'];
    $data_courbe[]   = round($row['moy'], 2);
}
// Données par défaut si vide
if (empty($data_courbe)) {
    $labels_courbe = ['Sem 1','Sem 2','Sem 3','Sem 4','Sem 5','Sem 6'];
    $data_courbe   = [8.75, 10.23, 14.77, 14.86, 15.19, 15.60];
}

$pageTitle = "Tableau de bord";
include 'includes/header.php';
?>

<div id="wrapper">
<?php include 'includes/navbar.php'; ?>

<main id="main-content">

    <!-- ── Top Bar ─────────────────────────────────────────── -->
    <div class="top-bar">
        <div class="top-bar-title">
            <h4>Bonjour, <?= htmlspecialchars($_SESSION['user_nom'] ?? 'Admin') ?> 👋</h4>
            <p>Voici un aperçu des performances académiques</p>
        </div>
        <div class="top-bar-actions">
            <div class="btn-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Rechercher une classe / filière...">
            </div>
            <a href="alertes.php" class="btn-notif">
                <i class="fa-solid fa-bell"></i>
                <?php if ($total_alertes > 0): ?>
                    <span class="dot"></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- ── Cartes KPI ──────────────────────────────────────── -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="kpi-icon danger"><i class="fa-solid fa-bell"></i></div>
                <div>
                    <div class="kpi-label">Alertes</div>
                    <div class="kpi-value"><?= $total_alertes ?></div>
                    <div class="kpi-sub">Étudiants en zone rouge</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="kpi-icon warning"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div>
                    <div class="kpi-label">Difficulté</div>
                    <div class="kpi-value"><?= $total_difficulte ?></div>
                    <div class="kpi-sub">Étudiants en zone orange</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="kpi-icon success"><i class="fa-solid fa-trophy"></i></div>
                <div>
                    <div class="kpi-label">Réussite</div>
                    <div class="kpi-value"><?= $total_reussite ?></div>
                    <div class="kpi-sub"><?= $taux ?>% du total</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="kpi-icon info"><i class="fa-solid fa-users"></i></div>
                <div>
                    <div class="kpi-label">Total étudiants</div>
                    <div class="kpi-value"><?= $total_etudiants ?></div>
                    <div class="kpi-sub">Inscrits cette année</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Alertes récentes + Répartition ──────────────────── -->
    <div class="row g-3 mb-4">

        <!-- Alertes récentes -->
        <div class="col-lg-7">
            <div class="card-section h-100">
                <div class="card-section-header">
                    <h6><i class="fa-solid fa-bell" style="color:#ef4444;"></i> Alertes récentes</h6>
                    <a href="alertes.php" style="font-size:13px; color:#4361ee; text-decoration:none;">
                        Voir toutes les alertes →
                    </a>
                </div>
                <div class="card-section-body">
                    <?php if (mysqli_num_rows($result_alertes) > 0): ?>
                        <?php while ($al = mysqli_fetch_assoc($result_alertes)): ?>
                            <?php
                            $zone  = $al['statut_zone'];
                            $classe_dot = ($zone === 'Zone rouge') ? 'rouge'
                                        : (($zone === 'Zone orange') ? 'orange' : 'vert');
                            ?>
                            <div class="alert-item">
                                <div class="alert-dot <?= $classe_dot ?>"></div>
                                <div>
                                    <div class="alert-nom">
                                        <?= htmlspecialchars($al['prenom'] . ' ' . $al['nom']) ?>
                                    </div>
                                    <div class="alert-info">
                                        Matricule : <?= htmlspecialchars($al['matricule']) ?> &bull;
                                        Moy : <?= $al['moyenne_generale'] ?>/20 &bull;
                                        <span style="color:<?= $zone==='Zone rouge'?'#ef4444':($zone==='Zone orange'?'#f59e0b':'#22c55e') ?>; font-weight:600;">
                                            <?= htmlspecialchars($zone) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="alert-time">
                                    <?= date('d/m/Y', strtotime($al['date_analyse'])) ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color:#94a3b8; font-size:14px; text-align:center; padding:20px 0;">
                            <i class="fa-solid fa-check-circle" style="color:#22c55e;"></i>
                            Aucune alerte active pour le moment.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Graphique répartition (Donut) -->
        <div class="col-lg-5">
            <div class="card-section h-100">
                <div class="card-section-header">
                    <h6><i class="fa-solid fa-chart-pie" style="color:#4361ee;"></i> Répartition des étudiants</h6>
                </div>
                <div class="card-section-body" style="display:flex; flex-direction:column; align-items:center;">
                    <canvas id="chartRepartition" width="220" height="220"></canvas>
                    <div style="margin-top:16px; width:100%;">
                        <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px;">
                            <span><span style="display:inline-block;width:10px;height:10px;background:#22c55e;border-radius:2px;margin-right:6px;"></span>Réussite (&gt;12)</span>
                            <strong><?= $total_reussite ?> (<?= $taux ?>%)</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px;">
                            <span><span style="display:inline-block;width:10px;height:10px;background:#f59e0b;border-radius:2px;margin-right:6px;"></span>Moyen (8–12)</span>
                            <strong><?= $total_difficulte ?></strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:13px;">
                            <span><span style="display:inline-block;width:10px;height:10px;background:#ef4444;border-radius:2px;margin-right:6px;"></span>Risque (&lt;8)</span>
                            <strong><?= $total_alertes ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Graphique évolution des moyennes ─────────────────── -->
    <div class="card-section">
        <div class="card-section-header">
            <h6><i class="fa-solid fa-chart-line" style="color:#4361ee;"></i> Évolution de la moyenne générale</h6>
        </div>
        <div class="card-section-body">
            <canvas id="chartEvolution" height="90"></canvas>
        </div>
    </div>

</main>
</div>

<?php
// Données pour les graphiques (JSON pour JavaScript)
$json_labels  = json_encode($labels_courbe);
$json_data    = json_encode($data_courbe);
$pageScripts = "
<script>
// ── Graphique en courbe : évolution des moyennes ───────────
const ctxLine = document.getElementById('chartEvolution').getContext('2d');
new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: $json_labels,
        datasets: [{
            label: 'Moyenne générale',
            data: $json_data,
            borderColor: '#4361ee',
            backgroundColor: 'rgba(67,97,238,0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#4361ee',
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: false, min: 0, max: 20,
                grid: { color: '#f1f5f9' },
                ticks: { font: { size: 12 } }
            },
            x: { grid: { display: false }, ticks: { font: { size: 12 } } }
        }
    }
});

// ── Graphique donut : répartition ─────────────────────────
const ctxDonut = document.getElementById('chartRepartition').getContext('2d');
new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
        labels: ['Réussite', 'Moyen', 'Risque'],
        datasets: [{
            data: [<?= $total_reussite ?>, <?= $total_difficulte ?>, <?= $total_alertes ?>],
            backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        cutout: '68%',
        plugins: { legend: { display: false } }
    }
});
</script>";

include 'includes/footer.php';
?>
