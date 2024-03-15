<?php
session_start();
require_once '../config.php';
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Vérifier si le token existe et n'est pas expiré
    $query = $bdd->prepare("SELECT * FROM password_resets WHERE token = :token AND expiration_date > NOW()");
    $query->execute([':token' => $token]);
    $result = $query->fetch();

    if ($result) {
        // Token valide, mettre à jour le mot de passe de l'utilisateur
        $updateQuery = $bdd->prepare("UPDATE users SET password = :password WHERE email = :email");
        $updateQuery->execute([
            ':password' => $result['password_hash'],
            ':email' => $result['email']
        ]);

        // Supprimer le token de la base de données après utilisation
        $deleteQuery = $bdd->prepare("DELETE FROM password_resets WHERE token = :token");
        $deleteQuery->execute([':token' => $token]);

        header('Location: ../index.php?update=newpassword');
    } else {
        header('Location: ../index.php?update=error');
    }
}