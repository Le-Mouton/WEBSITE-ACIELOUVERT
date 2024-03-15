<?php
session_start();
require_once '../config.php';
require_once '../error/error.php';

if(isset($_SESSION['user'])){
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();

} else {
    catchError('delete.php','../', 'Erreur de connexion', ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=connexion');
    exit();
}
if($data['permissions'] == '2'){
    if ($_GET['type'] == 'article') {
        if (isset($_GET['id'])) {
            $articleId = $_GET['id'];

            $query = $bdd->prepare('SELECT * FROM article WHERE id = ?');
            $query->execute(array($articleId));
            $article = $query->fetch(PDO::FETCH_ASSOC);

            $ftpServer = 'ftp.cluster029.hosting.ovh.net';
            $ftpUsername = 'acoshon';
            $ftpPassword = 'Acielouvert1';
            $ftpConnection = ftp_connect($ftpServer);
            $ftplogin = ftp_login($ftpConnection, $ftpUsername, $ftpPassword);
            ftp_pasv($ftpConnection, true);

            $fichierhtm = 'www/public/articles/content/' . $article['id'] . '.htm'; // Adapter le chemin
            $dossier = 'www/public/articles/content/' . $article['filename'] . '_fichiers'; // Adapter le chemin

            $parts = explode('/', $dossier); // Diviser le chemin en parties
            $encodedParts = array_map('rawurlencode', $parts); // Encoder chaque partie
            $dossier = implode('/', $encodedParts); // Reconstruire le chemin

            $parts = explode('/', $fichierhtm); // Diviser le chemin en parties
            $encodedParts = array_map('rawurlencode', $parts); // Encoder chaque partie
            $fichierhtm = implode('/', $encodedParts); // Reconstruire le chemin

            ftp_delete($ftpConnection, $fichierhtm);

            $files = ftp_rawlist($ftpConnection, $dossier);

            if ($files !== false) {
                foreach ($files as $file) {
                    $parts = preg_split("/\s+/", $file);
                    $filename = end($parts);

                    ftp_delete($ftpConnection, $dossier . '/' . $filename);
                }
                ftp_rmdir($ftpConnection, $dossier);
            } else {
                ftp_rmdir($ftpConnection, $dossier);
            }
            ftp_close($ftpConnection);

            $deleteQuery = $bdd->prepare('DELETE FROM article WHERE id = ?');
            $deleteQuery->execute(array($articleId));

            $delLike = $bdd->prepare('DELETE FROM likes WHERE article_id = ?');
            $delLike->execute(array($articleId));

            $delView = $bdd->prepare('DELETE FROM vue WHERE article_id = ?');
            $delView->execute(array($articleId));

            header('Location: ../index.php');
            updateLog('../',"Suppression d'un article", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $articleId);
        } else {
            catchError('delete.php','../', 'Données incorrectes', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
            header('Location: ../index.php?update=data');
        }
        exit();
    } elseif ($_GET['type'] == 'media') {
        if (isset($_GET['id'])) {
            $mediaID = $_GET['id'];

            $query = $bdd->prepare('SELECT * FROM media WHERE id = ?');
            $query->execute(array($mediaID));
            $article = $query->fetch(PDO::FETCH_ASSOC);

            $ftpServer = 'ftp.cluster029.hosting.ovh.net';
            $ftpUsername = 'acoshon';
            $ftpPassword = 'Acielouvert1';
            $ftpConnection = ftp_connect($ftpServer);
            $ftplogin = ftp_login($ftpConnection, $ftpUsername, $ftpPassword);
            ftp_pasv($ftpConnection, true);

            $dossier = 'www/public/media/content';
            $listeFichiers = ftp_nlist($ftpConnection, $dossier);

            $parts = explode('/', $dossier); // Diviser le chemin en parties
            $encodedParts = array_map('rawurlencode', $parts); // Encoder chaque partie
            $dossier = implode('/', $encodedParts); // Reconstruire le chemin

            $nomFichierRecherche = 'media' . $mediaID;
            $cheminFichierRecherche = $dossier . '/' . $nomFichierRecherche;

            if (in_array($cheminFichierRecherche, $listeFichiers)) {
                ftp_delete($ftpConnection, $cheminFichierRecherche);
            }

            ftp_close($ftpConnection);

            $deleteQuery = $bdd->prepare('DELETE FROM media WHERE id = ?');
            $deleteQuery->execute(array($mediaID));

            $delLike = $bdd->prepare('DELETE FROM likes WHERE article_id = ?');
            $delLike->execute(array($mediaID));

            $delView = $bdd->prepare('DELETE FROM vue WHERE article_id = ?');
            $delView->execute(array($mediaID));

            header('Location: ../index.php');
            updateLog('../',"Suppression d'un media", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $mediaID);
        } else {
            catchError('delete.php','../', 'Données incorrectes', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
            header('Location: ../index.php?update=data');
        }
        exit();
    } elseif ($_GET['type'] == 'video'){
            if (isset($_GET['id'])) {
                $videoID = $_GET['id'];

                $delVideo = $bdd->prepare('DELETE FROM video WHERE id = ?');
                $delVideo->execute(array($videoID));
                header('Location: ../index.php');
                updateLog('../',"Suppression d'une vidéo", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $videoID);
            } else {
                catchError('delete.php','../', 'Données incorrectes', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
                header('Location: ../index.php?update=data');
            }
        exit();
    } else {
        catchError('delete.php','../', 'Type de contenu inconnu', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
        header('Location: ../index.php?update=type');
        exit();
    }
} else {
    catchError('delete.php', '../', 'Accès refusé', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=access');
    exit();
}

