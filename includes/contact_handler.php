<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../contact.php');
    exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../contact.php');
    exit();
}

$subject = 'Nouveau message de contact - Banque de Sang';
$body = "<h3>Nouveau message</h3>"
      . "<p><strong>Nom:</strong> " . htmlspecialchars($name) . "</p>"
      . "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>"
      . "<p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";

@mail('contact@banquedesang.fr', $subject, $body, "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\nFrom: no-reply@banquedesang.fr\r\n");

header('Location: ../contact.php?sent=1');
exit();
?>






