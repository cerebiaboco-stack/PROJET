<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
include 'includes/header.php';
include 'includes/config.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Hôpitaux</h1>
        <a href="dashboard.php?section=hopitaux" class="btn btn-outline-secondary btn-sm">Retour au tableau de bord</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Ville</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rows = [];
                        if ($check = $conn->query("SHOW TABLES LIKE 'hopital'")) {
                            if ($check->num_rows > 0) {
                                if ($res = $conn->query("SELECT Nom, Ville, Contact FROM hopital ORDER BY Nom")) {
                                    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
                                }
                            }
                        }
                        if (empty($rows)) {
                            $rows = [
                                ['Nom' => 'Centre National Hospitalier Universitaire (CNHU)', 'Ville' => 'Cotonou', 'Contact' => '+229 01 02 03 04'],
                                ['Nom' => 'Centre National Hospitalier de Pneumo-Phtisiologie', 'Ville' => 'Cotonou', 'Contact' => '+229 05 06 07 08'],
                                ['Nom' => 'Hôpital d\'Instructions des Armées du Camp Guézo', 'Ville' => 'Cotonou', 'Contact' => '+229 09 10 11 12'],
                                ['Nom' => 'Hôpital de la Mère et de l\'Enfant Lagune', 'Ville' => 'Cotonou', 'Contact' => '+229 13 14 15 16'],
                                ['Nom' => 'Hôpital Bethesda', 'Ville' => 'Cotonou', 'Contact' => '+229 17 18 19 20'],
                                ['Nom' => 'Clinique Coopérative de Santé de Sikecodji', 'Ville' => 'Cotonou', 'Contact' => '+229 21 22 23 24'],
                                ['Nom' => 'Centre d\'Imagerie Médicale du Littoral', 'Ville' => 'Cotonou', 'Contact' => '+229 25 26 27 28'],
                                ['Nom' => 'Clinique les Grâces', 'Ville' => 'Cotonou', 'Contact' => '+229 29 30 31 32'],
                                ['Nom' => 'Clinique de la Vue', 'Ville' => 'Cotonou', 'Contact' => '+229 33 34 35 36'],
                                ['Nom' => 'Clinique La Lumière (chirurgie des yeux)', 'Ville' => 'Cotonou', 'Contact' => '+229 37 38 39 40'],
                                ['Nom' => 'Clinique St Vincent de Paul', 'Ville' => 'Cotonou', 'Contact' => '+229 41 42 43 44'],
                                ['Nom' => 'Clinique Centrale', 'Ville' => 'Cotonou', 'Contact' => '+229 45 46 47 48'],
                                ['Nom' => 'Clinique Point E', 'Ville' => 'Cotonou', 'Contact' => '+229 49 50 51 52'],
                                ['Nom' => 'Clinique Val de Grâce de Cotonou', 'Ville' => 'Cotonou', 'Contact' => '+229 53 54 55 56'],
                                ['Nom' => 'Polyclinique Atinkanmey', 'Ville' => 'Cotonou', 'Contact' => '+229 57 58 59 60'],
                                ['Nom' => 'Clinique Sainte Gerarda', 'Ville' => 'Cotonou', 'Contact' => '+229 61 62 63 64'],
                                ['Nom' => 'Centre Hospitalier International de Calavi (CHIC)', 'Ville' => 'Abomey-Calavi', 'Contact' => '+229 01 21 400 111'],
                                ['Nom' => 'Clinique Centrale de Calavi', 'Ville' => 'Abomey-Calavi', 'Contact' => '+229 01 99 47 68 27'],
                                ['Nom' => 'Clinique Val-de-Grace', 'Ville' => 'Abomey-Calavi', 'Contact' => '+229 60 80 76 32'],
                                ['Nom' => 'Clinique Bon Secours', 'Ville' => 'Abomey-Calavi', 'Contact' => '+229 66 20 35 21'],
                                ['Nom' => 'Clinique Pédiatrique d\'Abomey-Calavi', 'Ville' => 'Abomey-Calavi', 'Contact' => '+229 95 86 39 45'],
                                ['Nom' => 'Polyclinique Coopérative de Calavi', 'Ville' => 'Abomey-Calavi', 'Contact' => '+229 67 68 69 70'],
                                ['Nom' => 'Hôpital de zone d\'ALLADA', 'Ville' => 'Allada', 'Contact' => '+229 71 72 73 74'],
                                ['Nom' => 'St Luc', 'Ville' => 'Cotonou', 'Contact' => '+229 75 76 77 78'],
                                ['Nom' => 'Bon samaritin', 'Ville' => 'Cotonou', 'Contact' => '+229 79 80 81 82']
                            ];
                        }
                        foreach ($rows as $h): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($h['Nom']); ?></td>
                            <td><?php echo htmlspecialchars($h['Ville']); ?></td>
                            <td><?php echo htmlspecialchars($h['Contact']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>


