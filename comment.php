<?php

require_once 'config.php';
require_once 'error/error.php';
session_start();

if (isset($_SESSION['user'])) {
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();

}

if(!empty($data)){
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();

    if(isset($_POST['comment'], $_GET['id'], $_GET['type'])){
        $comment = htmlspecialchars($_POST['comment']);
        $autor = (string) $data['pseudo'];
        $user_id = (int) $data['id'];
        $article_id =  $_GET['id'];
        $type = $_GET['type'];

        $check = $bdd->prepare('SELECT * FROM commentaire WHERE commentaire =(?) AND type = (?)');
        $check->execute(array($comment, $type));

        if ($check->rowCount()==0){
            if(!empty($comment) && !empty($autor) && !empty($user_id) && !empty($article_id)){
                $addcomment = $bdd->prepare('INSERT INTO commentaire(article_id, user_id, autor, commentaire, type) VALUES (:article_id, :user_id, :autor, :commentaire, :type)');
                $addcomment->execute(array('article_id'=>$article_id, 'user_id'=>$user_id, 'autor'=>$autor, 'commentaire'=>$comment, 'type'=>$type));
                updateLog('',"Nouveau commentaire", $autor, $_SERVER['REMOTE_ADDR'], fromID: $article_id);
                header('Location:' . $_SERVER['HTTP_REFERER']);
            } else {
                catchError('comment.php', '', 'Données incorrectes',$data['pseudo'], $_SERVER['REMOTE_ADDR']);
                header('Location: ../index.php?update=data');
                exit();
            }
        } else {
            catchError('comment.php', '', 'Contenu déjà existant',$data['pseudo'], $_SERVER['REMOTE_ADDR']);
            header('Location: ../index.php?update=already_exist');
            exit();
        }
    } elseif (isset($_GET['id'], $_GET['ida'])){
        $id_article = $_GET['ida'];
        $id = $_GET['id'];
        $type = $_GET['type'];

        $delcom = $bdd->prepare('DELETE FROM commentaire WHERE id = ? AND article_id = ? AND type = ?');
        $delcom->execute(array($id, $id_article, $type));
        updateLog('',"Suppression de commentaire", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $id_article);
        header('Location:' . $_SERVER['HTTP_REFERER']);
    } else {
        catchError('comment.php', '', 'Données incorrectes',$data['pseudo'], $_SERVER['REMOTE_ADDR']);
        header('Location: ../index.php?update=data');
        exit();
    }
} else {
    if(isset($_POST['comment'], $_GET['id'], $_GET['type'])){
        $comment = htmlspecialchars($_POST['comment']);
        $autor = 'Anonyme';
        $user_id = 1;
        $article_id =  $_GET['id'];
        $type =  $_GET['type'];

        $check = $bdd->prepare('SELECT * FROM commentaire WHERE commentaire = ? AND type = ?');
        $check->execute(array($comment, $type));

        if ($check->rowCount()==0){
            if(!empty($comment) && !empty($autor) && !empty($user_id) && !empty($article_id)){
                $addcomment = $bdd->prepare('INSERT INTO commentaire(article_id, user_id, autor, commentaire, type) VALUES (:article_id, :user_id, :autor, :commentaire, :type)');
                $addcomment->execute(array('article_id'=>$article_id, 'user_id'=>$user_id, 'autor'=>$autor, 'commentaire'=>$comment, 'type'=>$type));
                updateLog('',"Nouveau commentaire", $autor, $_SERVER['REMOTE_ADDR'], fromID: $article_id);
                header('Location:' . $_SERVER['HTTP_REFERER']);
            } else {
                catchError('comment.php', '', 'Données incorrectes', ip: $_SERVER['REMOTE_ADDR']);
                header('Location: ../index.php?update=data');
                exit();
            }
        } else {
            catchError('comment.php', '', 'Contenu déjà existant', ip: $_SERVER['REMOTE_ADDR']);
            header('Location: ../index.php?update=already_exist');
            exit();
        }
    } else {
        catchError('comment.php', '', 'Données incorrectes', ip: $_SERVER['REMOTE_ADDR']);
        header('Location: ../index.php?update=data');
        exit();
    }
}