<?php
    session_start();
    include('../includes/config.php');

    if (!isset($_SESSION['user_id'])) {
        header("HTTP/1.1 401 Unauthorized");
        exit();
    }

    if (isset($_GET['groupe'])) {
        $groupe_sanguin = $_GET['groupe'];
        
        $check_stock = $bdd->prepare("SELECT COUNT(*) as quantite FROM poche WHERE GroupeSanguin = ? AND DatePeremption > CURDATE()");
        $check_stock->execute([$groupe_sanguin]);
        $stock = $check_stock->fetch();
        
        header('Content-Type: application/json');
        echo json_encode($stock);
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Paramètre groupe manquant']);
    }
?>