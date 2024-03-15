<?php
session_start();

require_once "../config.php";
require_once '../error/error.php';

session_start();
if(isset($_SESSION['user'])){
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();

} else {
    catchError('selected.php', '../', 'Erreur de connexion', ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=connexion');
    exit();
}
if($data['permissions'] >= '2') {
    if(isset($_GET['id'], $_GET['category'], $_GET['remove'])) {
        $articleid = $_GET['id'];
        $categoryArticle = $_GET['category'];
        $remove = $_GET['remove'];

        if ($remove == 'false') {
            $request = $bdd->prepare('SELECT * FROM article WHERE selected = 1');
            $request->execute();
            $results = $request->fetchAll(PDO::FETCH_ASSOC);
            $already = 0;
            foreach ($results as $result) {
                if ($result['category'] == $categoryArticle) {
                    $already += 1;
                }
            }

            if ($already < 4) {

                $articleExists = false;
                foreach ($results as $result) {
                    if ($result['id'] == $articleid) {
                        $articleExists = true;
                        break;
                    }
                }

                if (!$articleExists) {
                    $addArticle = $bdd->prepare('UPDATE article SET selected = 1 WHERE id = :id');
                    $addArticle->execute(array(':id' => $articleid));
                } else {
                    header('Location: ../../category/' . $categoryArticle . '.php');
                }
                header('Location: ../../category/' . $categoryArticle . '.php');
            } else {
                catchError('selected.php', '../', "Trop d'article coup de coeur", $data['pseudo'], $_SERVER['REMOTE_ADDR']);
                header('Location: ../index.php?update=tcc');
                exit();
            }
        } elseif ($remove == 'true') {
            $removeSelected = $bdd->prepare('UPDATE article SET selected = 0 WHERE id = :id');
            $removeSelected->execute(array('id' => $articleid));
            header('Location: ../../category/' . $categoryArticle . '.php');
        } else {
            catchError('selected.php', '../', "Données incorrectes", $data['pseudo'], $_SERVER['REMOTE_ADDR']);
            header('Location: ../index.php?update=data');
            exit();
        }
    } else {
        catchError('selected.php', '../', "Données incorrectes", $data['pseudo'], $_SERVER['REMOTE_ADDR']);
        header('Location: ../index.php?update=data');
        exit();
    }
} else {
    catchError('selected.php', '../', 'Accès refusé', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=access');
    exit();
}