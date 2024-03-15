<?php
// Démarrage de la session
session_start();
// Include de la base de données
require_once '../config.php';


// Si la session n'existe pas
if(!isset($_SESSION['user']))
{
    header('Location:../index.php');
    die();
}


// Si les variables existent
if(isset($_POST['user'], $_POST['perm'])){
    $perm = $_POST['perm'];
    $userid = $_POST['user'];

    echo $perm . $userid;

    $update = $bdd->prepare('UPDATE users SET permissions = :permission WHERE id = :id');
    $update->execute(array(
        "permission" => $perm,
        "id" => $userid
    ));
    header('Location: profile.php');
    die();
}
else{
    header('Location: profile.php');
    die();
}



