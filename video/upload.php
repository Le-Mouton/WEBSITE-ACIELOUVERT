<?php
global $bdd;
require_once '../config.php';
require_once '../error/error.php';

session_start();

if (isset($_SESSION['user'])) {
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();

    $_SESSION['user-info'] = $data;
} else {
    catchError('video/upload.php', '../', 'Erreur de connexion', ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=connexion');
    exit();
}


if($data['permissions'] >= '1') {

    $pseudo = $data['pseudo'];
    $error = 0;
    if (!empty($_POST['link'])  && !empty($_POST['description']) && !empty($_POST['titre'])) {
        $link = htmlspecialchars($_POST['link']);
        $date_publication = htmlspecialchars($_POST['date']);
        $description = htmlspecialchars($_POST['description']);
        $titre = htmlspecialchars($_POST['titre']);
        $articleID = htmlspecialchars($_POST['articleId']);

        if (!isset($_POST['autor']) && $_POST['autor'] != '') {
            $pseudo = $_POST['autor'];
        }

        $path = parse_url($link, PHP_URL_PATH);
        $videoId = ltrim($path, '/');
            if(isset($date_publication)){
                $newVideo = $bdd->prepare('INSERT INTO video(link, autor, description, date, article, ip, titre) VALUES (:link, :autor, :description, :date, :article, :ip, :titre)');
                $newVideo->execute(array('link'=>$videoId,
                                        'autor'=>$pseudo,
                                        'description'=>$description,
                                        'date'=>$date_publication,
                                        'article'=>$articleID,
                                        'ip'=>$_SERVER['REMOTE_ADDR'],
                                        'titre'=>$titre));
                updateLog('../',"Nouvelle vidéo", $data['pseudo'] ,$_SERVER['REMOTE_ADDR'], fromID: $bdd->lastInsertId() );

            } else{
                $newVideo = $bdd->prepare('INSERT INTO video(link, autor, description, article, ip, titre) VALUES (:link, :autor, :description, :article, :ip, :titre)');
                $newVideo->execute(array('link'=>$videoId,
                                        'autor'=>$pseudo,
                                        'description'=>$description,
                                        'article'=>$articleID,
                                        'ip'=>$_SERVER['REMOTE_ADDR'],
                                        'titre'=>$titre));
                updateLog('../',"Nouvelle vidéo", $data['pseudo'], $_SERVER['REMOTE_ADDR'] , fromID: $bdd->lastInsertId() );
            }
    } else {
        catchError('video/upload.php','../', 'Données incorrectes', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
        header('Location: ../index.php?update=data');
        exit();
    }
} else {
    catchError('video/upload.php','../', 'Accès refusé', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=access');
    exit();
}