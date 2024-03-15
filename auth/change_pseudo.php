<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['user']))
{
    catchError('change_pseudo.php', '../', 'Erreur de connexion', ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=connexion');
    exit();
}
if(!empty($_POST['pseudo'])){
    $new_pseudo = htmlspecialchars($_POST['pseudo']);
        $update = $bdd->prepare('UPDATE users SET pseudo = :pseudo WHERE token = :token');
        $update->execute(array(
            "pseudo" => $new_pseudo,
            "token" => $_SESSION['user']
        ));
        header('Location: profile.php?err=success_password');
        die();
}
else{
    header('Location: profile.php');
    die();
}



