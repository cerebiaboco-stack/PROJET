<?php
session_start();
require_once __DIR__ . '/config.php';

function bl_log($message) {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    $line = '[' . date('Y-m-d H:i:s') . "] " . $message . "\n";
    @file_put_contents($logDir . '/app.log', $line, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
    header('Location: ../index.php');
    exit();
}

$action = $_POST['action'];

if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        header('Location: ../login.php?error=empty');
        exit();
    }

    $stmt = $conn->prepare("SELECT IdUsers, NomUsers, MotDePasse, Role FROM users WHERE Email = ?");
    if (!$stmt) {
        bl_log('LOGIN PREP ERROR: ' . $conn->error);
        header('Location: ../login.php?error=db_error');
        exit();
    }
    $stmt->bind_param('s', $email);
    if (!$stmt->execute()) {
        bl_log('LOGIN EXEC ERROR: ' . $conn->error);
    }
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['MotDePasse'])) {
            $_SESSION['user_id'] = $user['IdUsers'];
            $_SESSION['user_name'] = $user['NomUsers'];
            $_SESSION['user_role'] = $user['Role'];
            header('Location: ../dashboard.php');
            exit();
        }
    }

    header('Location: ../login.php?error=invalid');
    exit();
}

if ($action === 'register') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $institution = trim($_POST['institution'] ?? '');
    $institution_name = trim($_POST['institution_name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($firstname === '' || $lastname === '' || $email === '' || $institution === '' || $role === '' || $password === '' || $confirm_password === '') {
        header('Location: ../register.php?error=empty');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../register.php?error=invalid_email');
        exit();
    }

    if ($password !== $confirm_password) {
        header('Location: ../register.php?error=password_mismatch');
        exit();
    }

    if (strlen($password) < 6) {
        header('Location: ../register.php?error=password_length');
        exit();
    }

    $stmt = $conn->prepare('SELECT IdUsers FROM users WHERE Email = ?');
    if (!$stmt) {
        bl_log('REGISTER CHECK PREP ERROR: ' . $conn->error);
        header('Location: ../register.php?error=db_error');
        exit();
    }
    $stmt->bind_param('s', $email);
    if (!$stmt->execute()) {
        bl_log('REGISTER CHECK EXEC ERROR: ' . $conn->error);
    }
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        header('Location: ../register.php?error=email_exists');
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $fullname = $firstname . ' ' . $lastname;

    $system_role = 'user';
    if ($role === 'medecin') {
        $system_role = 'doctor';
    } elseif ($role === 'administratif') {
        $system_role = 'admin';
    }

    $stmt = $conn->prepare('INSERT INTO users (NomUsers, Email, MotDePasse, Role, DateCreation) VALUES (?, ?, ?, ?, CURDATE())');
    if (!$stmt) {
        bl_log('REGISTER INSERT PREP ERROR: ' . $conn->error);
        header('Location: ../register.php?error=db_error');
        exit();
    }
    $stmt->bind_param('ssss', $fullname, $email, $hashed_password, $system_role);
    if ($stmt->execute()) {
        if ($role === 'medecin') {
            $user_id = $stmt->insert_id;
            $default_hospital_id = 1;
            $specialite = 'Médecin généraliste';
            $contact = $phone;
            $stmt2 = $conn->prepare('INSERT INTO medecin (IdHopital, IdUsers, Nom, Specialite, Contact) VALUES (?, ?, ?, ?, ?)');
            if ($stmt2) {
                $stmt2->bind_param('iisss', $default_hospital_id, $user_id, $fullname, $specialite, $contact);
                if (!$stmt2->execute()) {
                    bl_log('REGISTER INSERT MEDECIN ERROR: ' . $conn->error);
                }
            } else {
                bl_log('REGISTER MEDECIN PREP ERROR: ' . $conn->error);
            }
        }

        header('Location: ../login.php?success=registered');
        exit();
    }

    bl_log('REGISTER INSERT USER ERROR: ' . $conn->error);
    header('Location: ../register.php?error=db_error');
    exit();
}

header('Location: ../index.php');
exit();
?>