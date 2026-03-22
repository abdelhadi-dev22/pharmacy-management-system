<?php
require_once 'includes/db.php';
requireLogin();
?>
<!DOCTYPE html>
<?php require_once "includes/i18n.php"; ?>
<html lang="<?php echo htmlspecialchars($_SESSION["lang"] ?? "fr"); ?>" dir="<?php echo htmlspecialchars(__("dir")); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(__("app_title")); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body>
    <!-- Background Shapes -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1><i class="fas fa-tachometer-alt"></i> <?php echo htmlspecialchars(__("dashboard")); ?></h1>
            <div class="welcome-message">
                Bonjour, <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?></strong> !
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card stat-total">
                <div class="stat-icon">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars(__("total_medications")); ?></h3>
                    <?php
                    $db = getDB();
                    $stmt = $db->query("SELECT COUNT(*) as total FROM medicaments WHERE quantite > 0");
                    $result = $stmt->fetch();
                    ?>
                    <p class="stat-number"><?php echo $result['total']; ?></p>
                </div>
            </div>
            
            <div class="stat-card stat-warning">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars(__("low_stock")); ?></h3>
                    <?php
                    $stmt = $db->query("SELECT COUNT(*) as total FROM medicaments WHERE quantite <= seuil_minimum");
                    $result = $stmt->fetch();
                    ?>
                    <p class="stat-number"><?php echo $result['total']; ?></p>
                </div>
            </div>
            
            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars(__("expire_soon")); ?></h3>
                    <?php
                    $stmt = $db->query("SELECT COUNT(*) as total FROM medicaments WHERE date_peremption <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND date_peremption >= CURDATE()");
                    $result = $stmt->fetch();
                    ?>
                    <p class="stat-number"><?php echo $result['total']; ?></p>
                </div>
            </div>
            
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars(__('sales')); ?> (Aujourd'hui)</h3>
                    <?php
                    $stmt = $db->query("SELECT COUNT(*) as total FROM ventes WHERE DATE(created_at) = CURDATE() AND statut = 'completed'");
                    $result = $stmt->fetch();
                    ?>
                    <p class="stat-number"><?php echo $result['total']; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Contenu principal -->
        <div class="content-grid">
            <!-- <?php echo htmlspecialchars(__("stock_alerts")); ?> -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-bell"></i> <?php echo htmlspecialchars(__("stock_alerts")); ?></h3>
                </div>
                <div class="card-body">
                    <?php
                    // Médicaments en rupture de stock
                    $stmt = $db->query("SELECT nom, quantite FROM medicaments WHERE quantite = 0 ORDER BY nom LIMIT 5");
                    $ruptures = $stmt->fetchAll();
                    
                    // Médicaments périmés
                    $stmt = $db->query("SELECT nom, date_peremption FROM medicaments WHERE date_peremption < CURDATE() AND quantite > 0 ORDER BY date_peremption LIMIT 5");
                    $perimes = $stmt->fetchAll();
                    ?>
                    
                    <?php if (!empty($ruptures)): ?>
                        <div class="alert-section">
                            <h4><i class="fas fa-times-circle text-danger"></i> <?php echo htmlspecialchars(__("out_of_stock")); ?></h4>
                            <ul class="alert-list">
                                <?php foreach ($ruptures as $med): ?>
                                    <li><?php echo htmlspecialchars($med['nom']); ?> - Quantité: <?php echo $med['quantite']; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($perimes)): ?>
                        <div class="alert-section">
                            <h4><i class="fas fa-calendar-times text-warning"></i> <?php echo htmlspecialchars(__("expired")); ?></h4>
                            <ul class="alert-list">
                                <?php foreach ($perimes as $med): ?>
                                    <li><?php echo htmlspecialchars($med['nom']); ?> - Expiré le <?php echo date('d/m/Y', strtotime($med['date_peremption'])); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($ruptures) && empty($perimes)): ?>
                        <p class="text-success"><i class="fas fa-check-circle"></i> Aucune alerte urgente.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- <?php echo htmlspecialchars(__("recent_sales")); ?> -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> <?php echo htmlspecialchars(__("recent_sales")); ?></h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>N° Facture</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $db->query("
                                    SELECT v.numero_facture, c.nom as client, v.total_final, v.created_at 
                                    FROM ventes v 
                                    LEFT JOIN clients c ON v.client_id = c.id 
                                    WHERE v.statut = 'completed' 
                                    ORDER BY v.created_at DESC 
                                    LIMIT 10
                                ");
                                $ventes = $stmt->fetchAll();
                                
                                foreach ($ventes as $vente):
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($vente['numero_facture']); ?></td>
                                    <td><?php echo htmlspecialchars($vente['client'] ?: 'Non renseigné'); ?></td>
                                    <td><?php echo number_format($vente['total_final'], 2, ',', ' '); ?> <?php echo htmlspecialchars(__("currency")); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($vente['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Graphiques -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-line"></i> Statistiques des ventes (7 derniers jours)</h3>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/scripts.js"></script>
    <script>
        $(document).ready(function() {
            $('.data-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                }
            });
            
            // Graphique des ventes
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Ventes (€)',
                        data: [],
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
            
            // Charger les données du graphique
            $.ajax({
                url: 'api/stats.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    salesChart.data.labels = data.labels;
                    salesChart.data.datasets[0].data = data.data;
                    salesChart.update();
                }
            });
        });
    </script>
</body>
</html>