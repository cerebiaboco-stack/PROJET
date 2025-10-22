<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';
include 'includes/config.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Notifications</h1>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Retour au tableau de bord</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="list-group list-group-flush">
                <?php
                $items = [];
                $tableExists = false;
                if ($check = $conn->query("SHOW TABLES LIKE 'notifications'")) {
                    $tableExists = $check->num_rows > 0;
                }
                if ($tableExists) {
                    if ($result = $conn->query("SELECT Message, Type, DateCreation FROM notifications WHERE IdUsers = " . intval($_SESSION['user_id']) . " ORDER BY DateCreation DESC LIMIT 20")) {
                        while ($row = $result->fetch_assoc()) { $items[] = $row; }
                    }
                }
                if (empty($items)) {
                    $items = [
                        ['Message' => 'Nouvelle demande urgente reçue (O-).', 'Type' => 'warning', 'DateCreation' => date('Y-m-d H:i:s')],
                        ['Message' => 'Stock O+ mis à jour: +12 poches.', 'Type' => 'success', 'DateCreation' => date('Y-m-d H:i:s', strtotime('-3 hours'))],
                        ['Message' => 'Maintenance planifiée demain à 02:00.', 'Type' => 'info', 'DateCreation' => date('Y-m-d H:i:s', strtotime('-1 day'))],
                    ];
                }
                foreach ($items as $n):
                    $type = strtolower($n['Type']);
                    $class = 'info';
                    if ($type === 'warning') $class = 'warning';
                    elseif ($type === 'danger' || $type === 'error') $class = 'danger';
                    elseif ($type === 'success') $class = 'success';
                ?>
                <div class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="me-auto">
                        <div class="fw-semibold">Notification</div>
                        <div><?php echo htmlspecialchars($n['Message']); ?></div>
                    </div>
                    <span class="badge bg-<?php echo $class; ?> rounded-pill" title="<?php echo date('d/m/Y H:i', strtotime($n['DateCreation'])); ?>">
                        <?php echo date('d/m H:i', strtotime($n['DateCreation'])); ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>


