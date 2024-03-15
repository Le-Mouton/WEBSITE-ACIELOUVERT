<?php
global $bdd;
require_once '../config.php';
require_once '../error/error.php';


session_start();
if(isset($_SESSION['user'])){
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();
} else {
    catchError('media/upload.php', '../', 'Erreur de connexion', ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=connexion');
    exit();
}

if($data['permissions'] >= '1'){

    $pseudo = $data['pseudo'];
    $error = 0;

        if (!empty($_FILES['fileElem'])) {
            $nom = htmlspecialchars($_POST['nom']);
            $category = htmlspecialchars($_POST['category']);
            $date_publication = htmlspecialchars($_POST['date']);
            $media = $_FILES['fileElem'];
            $description = $_POST['description'];
            $filename = $_FILES['fileElem']['name'];

            if (!isset($_POST['autor']) && $_POST['autor'] != '') {
                $pseudo = $_POST['autor'];
            }

            $ftpConnection = ServerConnection();

            $verif = $bdd->prepare('SELECT * FROM media WHERE filename = ?');
            $verif->execute(array($_FILES['fileElem']['name']));

            $extension = pathinfo($_FILES['fileElem']['name'], PATHINFO_EXTENSION);

            if($verif->rowCount() == 0){
                if ($date_publication != ''){
                    $newmedia = $bdd->prepare('INSERT INTO media(nom ,date_publication, autor, description, filename, category, type) VALUES (:nom, :date_publication, :autor, :description, :filename, :category, :type)');
                    $newmedia->execute(array('nom' => $nom, 'date_publication' => $date_publication, 'autor' => $pseudo, 'description' => $description, 'filename' => $filename, 'category' => $category, 'type'=>$extension));
                } else {
                    $newmedia = $bdd->prepare('INSERT INTO media(nom , autor, description, filename, category, type) VALUES (:nom, :autor, :description, :filename, :category, :type)');
                    $newmedia->execute(array('nom' => $nom, 'autor' => $pseudo, 'description' => $description, 'filename' => $filename, 'category' => $category, 'type'=>$extension));
                }

                $directory = 'www/public/media/content';

                $localFile = $_FILES['fileElem']['tmp_name'];
                $newFileName = $directory . '/media' . $bdd->lastInsertId() . '.' . pathinfo($_FILES['fileElem']['name'], PATHINFO_EXTENSION);

                ftp_put($ftpConnection, $newFileName, $localFile);
                updateLog('../',"Nouveau media", $data['pseudo'], $_SERVER['REMOTE_ADDR'] , fromID: $bdd->lastInsertId() );
            } else {
                catchError('media/upload.php','../', 'Contenu déjà existant', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
                header('Location: ../index.php?update=already_exist');
                exit();
            }
            ftp_close($ftpConnection);
            header('Location: ../index.php');
            exit();
        } else {
            catchError('media/upload.php','../', 'Aucun fichier trouvé', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
            header('Location: ../index.php?upgrade=nothing');
            exit();
        }
} else {
    catchError('media/upload.php','../', 'Accès refusé', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=access');
    exit();
}
