<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
include 'includes/header.php';
include 'includes/config.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Demandes</h1>
        <a href="dashboard.php?section=demandes" class="btn btn-outline-secondary btn-sm">Retour au tableau de bord</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hôpital</th>
                            <th>Groupe</th>
                            <th>Quantité</th>
                            <th>Date</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $columns = [];
                        if ($desc = $conn->query('DESCRIBE demande')) {
                            while ($c = $desc->fetch_assoc()) { $columns[] = $c['Field']; }
                        }
                        $joinCol = null;
                        if (in_array('IdHopital', $columns, true)) { $joinCol = 'IdHopital'; }
                        elseif (in_array('HopitalId', $columns, true)) { $joinCol = 'HopitalId'; }

                        $result = null;
                        if ($joinCol) {
                            $sql = "SELECT d.*, h.Nom AS hopital_nom FROM demande d JOIN hopital h ON d.$joinCol = h.IdHopital ORDER BY d.DateDemande DESC";
                            $result = $conn->query($sql);
                        } else {
                            $sql = "SELECT d.*, NULL AS hopital_nom FROM demande d ORDER BY d.DateDemande DESC";
                            $result = $conn->query($sql);
                        }

                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                                $status_class = '';
                                if ($row['Statut'] == 'En attente') $status_class = 'bg-warning';
                                elseif ($row['Statut'] == 'Approuvé') $status_class = 'bg-success';
                                elseif ($row['Statut'] == 'Rejeté') $status_class = 'bg-danger';
                                else $status_class = 'bg-info';
                        ?>
                        <tr>
                            <td><?php echo isset($row['hopital_nom']) && $row['hopital_nom'] ? $row['hopital_nom'] : 'N/A'; ?></td>
                            <td><span class="badge bg-primary"><?php echo $row['GroupeSanguin']; ?></span></td>
                            <td><?php echo $row['Quantite']; ?> poches</td>
                            <td><?php echo date('d/m/Y', strtotime($row['DateDemande'])); ?></td>
                            <td><span class="badge <?php echo $status_class; ?>"><?php echo $row['Statut']; ?></span></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <?php
                        $demo = [
                            ['hopital' => 'St Luc', 'groupe' => 'O+', 'qte' => 4, 'date' => date('Y-m-d'), 'statut' => 'En attente'],
                            ['hopital' => 'CNHU', 'groupe' => 'A-', 'qte' => 2, 'date' => date('Y-m-d', strtotime('-1 day')), 'statut' => 'Approuvé'],
                            ['hopital' => "Hôpital de zone d'ALLADA", 'groupe' => 'B+', 'qte' => 6, 'date' => date('Y-m-d', strtotime('-2 days')), 'statut' => 'En attente'],
                            ['hopital' => 'Bon samaritin', 'groupe' => 'AB+', 'qte' => 1, 'date' => date('Y-m-d', strtotime('-3 days')), 'statut' => 'Rejeté'],
                            ['hopital' => 'Clinique coopérative de Calavi', 'groupe' => 'O-', 'qte' => 3, 'date' => date('Y-m-d', strtotime('-4 days')), 'statut' => 'Approuvé']
                        ];
                        foreach ($demo as $row):
                            $status_class = '';
                            if ($row['statut'] == 'En attente') $status_class = 'bg-warning';
                            elseif ($row['statut'] == 'Approuvé') $status_class = 'bg-success';
                            elseif ($row['statut'] == 'Rejeté') $status_class = 'bg-danger';
                            else $status_class = 'bg-info';
                        ?>
                        <tr>
                            <td><?php echo $row['hopital']; ?></td>
                            <td><span class="badge bg-primary"><?php echo $row['groupe']; ?></span></td>
                            <td><?php echo $row['qte']; ?> poches</td>
                            <td><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                            <td><span class="badge <?php echo $status_class; ?>"><?php echo $row['statut']; ?></span></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>






