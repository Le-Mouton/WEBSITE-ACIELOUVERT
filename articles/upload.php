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
    catchError('article/upload.php', '../', 'Erreur de connexion', ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=connexion');
    exit();
}

if($data['permissions'] >= '1') {

    $pseudo = $data['pseudo'];
    $error = 0;
    if (!empty($_FILES['fileElem']) && !empty($_FILES['frontpicture'])) {
        // The file upload process was successful
        $nom = htmlspecialchars($_POST['nom']);
        $category = htmlspecialchars($_POST['category']);
        $date_publication = htmlspecialchars($_POST['date']);

        if (!isset($_POST['autor']) && $_POST['autor'] != '') {
            $pseudo = $_POST['autor'];
        }

        $fileCount = count($_FILES['fileElem']['name']);

        $ftpConnection = ServerConnection();

        // Boucle à travers chaque fichier
        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = $_FILES['fileElem']['name'][$i];
            $fileType = $_FILES['fileElem']['type'][$i];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

            if (isset($_FILES['fileElem']['type'][$i]) && $fileType === 'text/html' && in_array(strtolower($fileExtension), ['htm', 'html'], true)) {

                $ip = $_SERVER['REMOTE_ADDR'];

                $verif = $bdd->prepare('SELECT * FROM article WHERE filename LIKE :filename');
                $verif->execute(array('filename' => '%' . pathinfo($fileName, PATHINFO_FILENAME) . '%'));

                $results = $verif->fetchAll(PDO::FETCH_ASSOC);

                if (empty($results)) {

                    if ($date_publication != '') {
                        $insert = $bdd->prepare('INSERT INTO article(nom, ip, autor, filename, category, date_publication) VALUES(:nom, :ip, :autor, :filename, :category, :date_publication)');
                        $insert->execute(array(
                            'nom' => $nom,
                            'filename' => pathinfo($fileName, PATHINFO_FILENAME),
                            'ip' => $ip,
                            'autor' => $pseudo,
                            'category' => $category,
                            'date_publication' => $date_publication
                        ));
                    } else {
                        $insert = $bdd->prepare('INSERT INTO article(nom, ip, autor, filename, category) VALUES(:nom, :ip, :autor, :filename, :category)');
                        $insert->execute(array(
                            'nom' => $nom,
                            'filename' => pathinfo($fileName, PATHINFO_FILENAME),
                            'ip' => $ip,
                            'autor' => $pseudo,
                            'category' => $category
                        ));
                    }
                    $lastInsertId = $bdd->lastInsertId();
                    $id = $lastInsertId;

                    $doss = pathinfo($fileName, PATHINFO_FILENAME);

                    $nameidarticle = $lastInsertId . '.htm'; // Utiliser un indice unique pour chaque fichier
                    $remoteFile = 'www/public/articles/content/' . $nameidarticle;
                    $localFile = $_FILES['fileElem']['tmp_name'][$i];
                    $remoteDirectory = 'www/public/articles/content/' . $doss . '_fichiers';

                    ftp_mkdir($ftpConnection, $remoteDirectory);

                    ftp_put($ftpConnection, $remoteFile, $localFile, FTP_BINARY);

                    $localFile = $_FILES['frontpicture']['tmp_name'];
                    $fileType = $_FILES['frontpicture']['type'];
                    $fileExtension = pathinfo($_FILES['frontpicture']['name'], PATHINFO_EXTENSION); // Obtenir l'extension du fichier

                    $newFileName = 'frontpicture.' . $fileExtension; // Utiliser une extension appropriée

                    $remoteFile = 'www/public/articles/content/' . $doss . '_fichiers/' . $_FILES['frontpicture']['name'];
                    $newRemoteFile = 'www/public/articles/content/' . $doss . '_fichiers/' . $newFileName;

                    ftp_put($ftpConnection, $remoteFile, $localFile, FTP_BINARY);
                    ftp_rename($ftpConnection ,$remoteFile, $newRemoteFile);

                    for ($i = 0; $i < $fileCount; $i++) {
                        $fileName = $_FILES['fileElem']['name'][$i];
                        $fileType = $_FILES['fileElem']['type'][$i];
                        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                        if (isset($_FILES['fileElem']['type'][$i]) && strpos($fileType, 'image/') === 0 && in_array(strtolower($fileExtension), ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'], true)) {            // C'est un fichier HTML, effectuez les opérations nécessaires

                            $remoteFile = $remoteDirectory . '/' . $_FILES['fileElem']['name'][$i];
                            $localFile = $_FILES['fileElem']['tmp_name'][$i];

                            ftp_put($ftpConnection, $remoteFile, $localFile, FTP_BINARY);

                        }

                    }
                } else {
                    catchError('article/upload.php', '../', 'Contenu déjà existant', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
                    header('Location: ../index.php?update=already_exist');
                    exit();
                }

            } else {
                $fileName = $nom . "_infographie";
                $ip = $_SERVER['REMOTE_ADDR'];

                $verif = $bdd->prepare('SELECT * FROM article WHERE nom LIKE :nom');
                $verif->execute(array('nom' => '%' . pathinfo($fileName, PATHINFO_FILENAME) . '%'));

                $results = $verif->fetchAll(PDO::FETCH_ASSOC);

                if (empty($results)) {

                    if ($date_publication != '') {
                        $insert = $bdd->prepare('INSERT INTO article(nom, ip, autor, filename, category, date_publication) VALUES(:nom, :ip, :autor, :filename, :category, :date_publication)');
                        $insert->execute(array(
                            'nom' => $nom,
                            'filename' => pathinfo($fileName, PATHINFO_FILENAME),
                            'ip' => $ip,
                            'autor' => $pseudo,
                            'category' => $category,
                            'date_publication' => $date_publication
                        ));
                    } else {
                        $insert = $bdd->prepare('INSERT INTO article(nom, ip, autor, filename, category) VALUES(:nom, :ip, :autor, :filename, :category)');
                        $insert->execute(array(
                            'nom' => $nom,
                            'filename' => pathinfo($fileName, PATHINFO_FILENAME),
                            'ip' => $ip,
                            'autor' => $pseudo,
                            'category' => $category
                        ));
                    }
                    $lastInsertId = $bdd->lastInsertId();
                    $id = $lastInsertId;

                    $doss = pathinfo($fileName, PATHINFO_FILENAME);

                    $remoteDirectory = 'www/public/articles/content/' . $doss;

                    ftp_mkdir($ftpConnection, $remoteDirectory);

                    $localFile = $_FILES['frontpicture']['tmp_name'];
                    $fileType = $_FILES['frontpicture']['type'];
                    $fileExtension = pathinfo($_FILES['frontpicture']['name'], PATHINFO_EXTENSION); // Obtenir l'extension du fichier

                    $newFileName = 'frontpicture.' . $fileExtension; // Utiliser une extension appropriée

                    $remoteFile = 'www/public/articles/content/' . $doss . '/' .  $_FILES['frontpicture']['name'];
                    $newRemoteFile = 'www/public/articles/content/' . $doss . '/' .  $newFileName;

                    ftp_put($ftpConnection, $remoteFile, $localFile, FTP_BINARY);
                    ftp_rename($ftpConnection ,$remoteFile, $newRemoteFile);

                    for ($i = 0; $i < $fileCount; $i++) {
                        $fileName = $_FILES['fileElem']['name'][$i];
                        $fileType = $_FILES['fileElem']['type'][$i];
                        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                        if (isset($_FILES['fileElem']['type'][$i]) && strpos($fileType, 'image/') === 0 && in_array(strtolower($fileExtension), ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'], true)) {            // C'est un fichier HTML, effectuez les opérations nécessaires

                            $remoteFile = $remoteDirectory . '/' . $_FILES['fileElem']['name'][$i];
                            $localFile = $_FILES['fileElem']['tmp_name'][$i];

                            ftp_put($ftpConnection, $remoteFile, $localFile, FTP_BINARY);

                        }

                    }
                } else {
                    catchError('article/upload.php', '../', 'Contenu déjà existant', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
                    header('Location: ../index.php?update=already_exist');
                    exit();
                }

            }
        }
        updateLog('../',"Nouvel article", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $id);
        ftp_close($ftpConnection);
        header('Location: ../index.php');
        exit();
    } else {
        catchError('article/upload.php', '../', 'Aucun fichier trouvé', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
        header('Location: ../index.php?update=nothing');
        exit();
    }
} else {
    catchError('article/upload.php', '../', 'Accès refusé', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=access');
    exit();
}
