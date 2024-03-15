<?php

require_once 'config.php';
require_once 'error/error.php';


if(!empty($_GET['id_user']) && !empty($_GET['id_art']) && !empty($_GET['type'])){
    $getArt = $_GET['id_art'];
    $getUser = $_GET['id_user'];
    $type = $_GET['type'];
    $check = $bdd->prepare('SELECT * FROM likes WHERE user_id = ? AND article_id = ? AND type = ?');
    $check->execute(array($getUser, $getArt, $type));

    if ($check->rowCount() == 0){
        $insert = $bdd->prepare('INSERT INTO likes(user_id, article_id, type) VALUES (:user_id, :article_id, :type)');
        $insert->execute(array('user_id'=> $getUser, 'article_id'=>$getArt, 'type'=>$type));
    } else {
        $del = $bdd->prepare('DELETE FROM likes WHERE user_id = :user_id AND article_id = :article_id AND type = :type');
        $del->execute(array('user_id'=> $getUser, 'article_id'=>$getArt, 'type'=>$type));
    }
    header('Location:' . $_SERVER['HTTP_REFERER']);
} else {
    catchError('like.php', '', "Données incorrectes", ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=data');
    exit();
}
