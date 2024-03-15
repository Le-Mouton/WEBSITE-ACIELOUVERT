<?php   
session_start();
require_once '../config.php';

if (isset($_POST['email']) && isset($_POST['new_password']) && isset($_POST['retype_password']) && isset($_POST['new_password']) == isset($_POST['retype_password'])) {
    $userEmail = $_POST['email'];
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(50));
    $expirationDate = date('Y-m-d H:i:s', strtotime('+1 day'));

    $query = $bdd->prepare("INSERT INTO password_resets (email, password_hash, token, expiration_date) VALUES (:email, :password_hash, :token, :expiration_date)");
    $query->execute([
        ':email' => $userEmail,
        ':password_hash' => $newPassword,
        ':token' => $token,
        ':expiration_date' => $expirationDate
    ]);

    $to = $_POST["email"];
    $subject = "Réinitialisation de votre mot de passe";
    $message = "Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant : https://acosh.ovh/auth/reinitialisation.php?token=$token";
    $headers = "From: no-reply@acielouvert.fr";

    mail($to, $subject, $message, $headers);
    header("Location: ../index.php?update=mail");

}



