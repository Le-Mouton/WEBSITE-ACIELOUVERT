<?php
global $bdd;
session_start();
require_once '../config.php';
require_once '../error/error.php';

if(isset($_SESSION['user'])){
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();

} else {
    catchError('edit.php','../', 'Erreur de connexion', ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=connexion');
    exit();
}
if($data['permissions'] == 2) {
    $article = $bdd->prepare('SELECT id, nom FROM article');
    $article->execute();

    $media = $bdd->prepare('SELECT id, nom FROM media');
    $media->execute();

    $video = $bdd->prepare('SELECT id, titre FROM video');
    $video->execute();
} elseif ($data['permissions'] == 1){
    $article = $bdd->prepare('SELECT id, nom FROM article WHERE autor = ?');
    $article->execute(array($data['pseudo']));

    $media = $bdd->prepare('SELECT id, nom FROM media WHERE autor = ?');
    $media->execute(array($data['pseudo']));

    $video = $bdd->prepare('SELECT id, titre FROM video WHERE autor = ?');
    $video->execute(array($data['pseudo']));
}
if (isset($_GET['type'])) {
    if ($_GET['type'] == 'article') {
        if (isset($_POST['article'])) {
            $nom = htmlspecialchars($_POST['nom']);
            $newautor = htmlspecialchars($_POST['autor']);
            $date = $_POST['date'];
            $newcategory = htmlspecialchars($_POST['category']);
            $newtype = htmlspecialchars($_POST['type']);
            $id = $_POST['article'];

            $images = $_FILES['fileElem'];

            if (!empty($nom)) {
                $upnom = $bdd->prepare('UPDATE article SET nom = ? WHERE id = ?');
                $upnom->execute(array($nom, $id));
            }
            if (!empty($newautor)) {
                $upautor = $bdd->prepare('UPDATE article SET autor = ? WHERE id = ?');
                $upautor->execute(array($newautor, $id));
            }
            if (!empty($date)) {
                $update = $bdd->prepare('UPDATE article SET date_publication = ? WHERE id = ?');
                $update->execute(array($date, $id));
            }
            if (!empty($newcategory)) {
                $upcategory = $bdd->prepare('UPDATE article SET category = ? WHERE id = ?');
                $upcategory->execute(array($newcategory, $id));
            }
            if (!empty($newtype)) {
                $uptype = $bdd->prepare('UPDATE article SET type = ? WHERE id = ?');
                $uptype->execute(array($newtype, $id));
            }
            if (isset($images)) {
                $ftpServer = 'ftp.cluster029.hosting.ovh.net';
                $ftpUsername = 'acoshon';
                $ftpPassword = 'Acielouvert1';
                $ftpConnection = ftp_connect($ftpServer);
                $ftplogin = ftp_login($ftpConnection, $ftpUsername, $ftpPassword);
                ftp_pasv($ftpConnection, true);

                $fileCount = count($_FILES['fileElem']['name']);

                $art_filename = $bdd->prepare('SELECT * FROM article WHERE id = ?');
                $art_filename->execute(array($id));

                $file_name = $art_filename->fetch(PDO::FETCH_ASSOC);

                for ($i = 0; $i < $fileCount; $i++) {
                    $fileName = $_FILES['fileElem']['name'][$i];
                    $fileType = $_FILES['fileElem']['type'][$i];
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                    if (isset($_FILES['fileElem']['type'][$i]) && strpos($fileType, 'image/') === 0 && in_array(strtolower($fileExtension), ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'], true)) {

                        $remoteDirectory = 'www/public/articles/content/' . $file_name['filename'] . '_fichiers';
                        $remoteFile = $remoteDirectory . '/' . $_FILES['fileElem']['name'][$i];
                        $localFile = $_FILES['fileElem']['tmp_name'][$i];

                        ftp_put($ftpConnection, $remoteFile, $localFile, FTP_BINARY);
                    }

                }
                ftp_close($ftpConnection);
                updateLog('../',"Modification d'un article", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $id);
            }
        } else {
            catchError('edit.php','../', "Données incorrectes", $data['pseudo'], $_SERVER['REMOTE_ADDR']);
            header('Location: ../index.php?update=data');
            die();
        }
    } elseif ($_GET['type'] == 'media') {
        if (isset($_POST['media'])) {
            $nom = htmlspecialchars($_POST['nom']);
            $newautor = htmlspecialchars($_POST['autor']);
            $date = $_POST['date'];
            $newcategory = htmlspecialchars($_POST['category']);
            $id = $_POST['media'];

            $images = $_FILES['fileElem'];

            if (!empty($nom)) {
                $upnom = $bdd->prepare('UPDATE media SET nom = ? WHERE id = ?');
                $upnom->execute(array($nom, $id));
            }
            if (!empty($newautor)) {
                $upautor = $bdd->prepare('UPDATE media SET autor = ? WHERE id = ?');
                $upautor->execute(array($newautor, $id));
            }
            if (!empty($date)) {
                $update = $bdd->prepare('UPDATE media SET date_publication = ? WHERE id = ?');
                $update->execute(array($date, $id));
            }
            if (!empty($newcategory)) {
                $upcategory = $bdd->prepare('UPDATE media SET category = ? WHERE id = ?');
                $upcategory->execute(array($newcategory, $id));
            }
            updateLog('../',"Modification d'un media", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $id);
        } else {
            catchError('edit.php','../', "Données incorrectes", $data['pseudo'], $_SERVER['REMOTE_ADDR']);
            header('Location: ../index.php?update=data');
            die();
        }
    } elseif ($_GET['type'] == 'video') {
        if (isset($_POST['video'])) {
            $id = $_POST['video'];
            $titre = htmlspecialchars($_POST['titre']);
            $newautor = htmlspecialchars($_POST['autor']);
            $link = htmlspecialchars($_POST['link']);
            $articleID = htmlspecialchars($_POST['articleId']);
            $description = htmlspecialchars($_POST['description']);
            $date = htmlspecialchars($_POST['date']);

            if (!empty($titre)) {
                $upnom = $bdd->prepare('UPDATE video SET titre = ? WHERE id = ?');
                $upnom->execute(array($titre, $id));
            }
            if (!empty($newautor)) {
                $upautor = $bdd->prepare('UPDATE video SET autor = ? WHERE id = ?');
                $upautor->execute(array($newautor, $id));
            }
            if (!empty($date)) {
                $update = $bdd->prepare('UPDATE video SET date = ? WHERE id = ?');
                $update->execute(array($date, $id));
            }
            if (!empty($link)) {
                $upcategory = $bdd->prepare('UPDATE video SET link = ? WHERE id = ?');
                $upcategory->execute(array($link, $id));
            }
            if (!empty($description)) {
                $upcategory = $bdd->prepare('UPDATE video SET description = ? WHERE id = ?');
                $upcategory->execute(array($description, $id));
            }
            if (isset($articleID)) {
                $upcategory = $bdd->prepare('UPDATE video SET article = ? WHERE id = ?');
                $upcategory->execute(array($articleID, $id));
            }
            updateLog('../',"Modification d'une vidéo", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $id);
        } else {
            catchError('edit.php','../', "Données incorrectes", $data['pseudo'], $_SERVER['REMOTE_ADDR']);
            header('Location: ../index.php?update=data');
            die();
        }
    } else {
        catchError('edit.php','../', "Type de contenu inconnu", $data['pseudo'], $_SERVER['REMOTE_ADDR']);
        header('Location: ../index.php?update=type');
        die();
    }
}
if($data['permissions'] >= '1'){
    $path="../";
    require_once "../canva/header.php";

    echo echoheader($path);
    ?>
    <main>
        <div class="form-group" id="select-form-group">
            <select class="content-select" name="content" id="content-select">
                <option value="">--Choisissez un type de contenu à ajouter--</option>
                <option value="article">Article</option>
                <option value="media">Media</option>
                <option value="video">Vidéo</option>
            </select>
        </div>
        <div class="drop-area" id="article" style="display: none;">
            <form id="drop" action="edit.php?type=article" method="post" enctype="multipart/form-data">
                <p id="aero-color">Choisissez un article à modifier:</p>
                <div class="form-group">
                    <select name="article" id="media-select">
                        <option value="">--Choisissez un article à modifier--</option>
                        <?php while ($row = $article->fetch(PDO::FETCH_ASSOC)){
                            ?> <option value="<?= $row['id']?>"><?= $row['nom']?></option>
                        <?php }?>
                    </select>
                </div>
                <p id="aero-color">Glissez-déposez des fichiers ici</p>
                <div class="form-group">
                    <input type="file" name="fileElem[]" id="fileElem" multiple accept="*/*" onchange="handleFiles(this.files, 'fileList1')">
                    <output id="fileList1"></output>
                </div>
                <p id="aero-color">Glissez-déposez votre image de présentation</p>
                <div class="form-group">
                    <input type="file" name="frontpicture" id="frontpicture" multiple accept="*/*" onchange="handleFiles(this.files, 'fileList2')">
                    <output id="fileList2"></output>
                </div>
                <p id="aero-color">Choisissez la catégorie de votre article:</p>
                <div class="form-group">
                    <select name="category" id="category-select">
                        <option value="">--Choisissez une catégorie--</option>
                        <option value="aeronautique">Aéronautique</option>
                        <option value="atmosvert">Atmos'vert</option>
                        <option value="interview">Interview</option>
                        <option value="partenariat">Partenariat</option>
                        <option value="spatial">Spatial</option>
                    </select>
                </div>
                <p id="aero-color">Choisissez une sous catégorie:</p>
                <div class="form-group">
                    <select name="type" id="category-select">
                        <option value="">--Choisissez une sous catégorie--</option>
                        <option value="technologie">Technologie</option>
                        <option value="creation">Création</option>
                        <option value="histoire">Histoire</option>
                        <option value="actualite">Actualité</option>
                        <option value="quiz">Quiz</option>
                    </select>
                </div>
                <p id="aero-color">Choisissez la date à laquelle vous voulez que l'article paraisse :</p>
                <div class="form-group">
                    <input type="date" id="date" name="date"/>
                </div>
                <p id="aero-color">Inscrivez le nom de votre article:</p>
                <div class="form-group">
                    <input type="text" id="input-article" name="nom" class="form-control" placeholder="Nom de l'article..." autocomplete="off">
                </div>
                <p id="aero-color">Inscrivez le nom de l'auteur uniquement si l'article ne vous appartient pas:</p>
                <div class="form-group">
                    <input type="text" id="input-article" name="autor" class="form-control" placeholder="Nom de l'auteur si ce n'est pas vous..." autocomplete="off">
                </div>
                <button type="submit" id="aero-color">Modifier</button>
            </form>
        </div>
        <div class="drop-area" id="media" style="display: none">
            <form id="drop" action="edit.php?type=media" method="post" enctype="multipart/form-data">
                <p id="spatial-color">Choisissez une image à modifier:</p>
                <div class="form-group">
                    <select name="media" id="media-select" id="select-form-group">
                        <option value="">--Choisissez un media à modifier--</option>
                        <?php while ($row = $media->fetch(PDO::FETCH_ASSOC)){
                            ?> <option value="<?= $row['id']?>"><?= $row['nom']?></option>
                        <?php }?>
                    </select>
                </div>
                <p id="spatial-color">Choisissez la catégorie de votre image:</p>
                <div class="form-group">
                    <select name="category" id="category-select">
                        <option value="">Catégorie</option>
                        <option value="spotting">SPOTTING</option>
                        <option value="astrophoto">ASTROPHOTO</option>
                    </select>
                </div>
                <p id="spatial-color">Description de votre image:</p>
                <div class="form-group">
                    <textarea name="description" id="description" rows="4" cols="50" placeholder="Soyez inspiré..."></textarea>
                </div>
                <p id="spatial-color">Choisissez la date à laquelle vous voulez que l'image paraisse :</p>
                <div class="form-group">
                    <input type="date" id="date" name="date"/>
                </div>
                <p id="spatial-color">Inscrivez le nom de votre image:</p>
                <div class="form-group">
                    <input type="text" id="input-article" name="nom" class="form-control" placeholder="Nom de l'article..." autocomplete="off">
                </div>
                <p id="spatial-color">Inscrivez le nom de l'auteur uniquement si l'image ne vous appartient pas:</p>
                <div class="form-group">
                    <input type="text" id="input-article" name="autor" class="form-control" placeholder="Nom de l'auteur si ce n'est pas vous..." autocomplete="off">
                </div>
                <button type="submit" id="spatial-color">Publier</button>
            </form>
        </div>
        <div class="drop-area" id="video-cnt" style="display: none">
            <form id="drop" action="edit.php?type=video" method="post" enctype="multipart/form-data">
                <p id="multi-color">Choisissez une vidéo à modifier:</p>
                <div class="form-group">
                    <select name="video" id="media-select">
                        <option value="">--Choisissez une video à modifier--</option>
                        <?php while ($row = $video->fetch(PDO::FETCH_ASSOC)){
                            ?> <option value="<?= $row['id']?>"><?= $row['titre']?></option>
                        <?php }?>
                    </select>
                </div>
                <p id="multi-color">Glissez le lien de votre vidéo ici:</p>
                <div class="form-group">
                    <input type="text" id="input-article" name="link" class="form-control" placeholder="Lien de la vidéo..." autocomplete="off">
                </div>
                <p id="multi-color">Inscrivez l'ID de l'article associé à la vidéo s'il y en a un:</p>
                <div class="form-group">
                    <input type="number" id="input-article" name="articleId" class="form-control" placeholder="ID de l'article associé..." autocomplete="off">
                </div>
                <p id="multi-color">Inscrivez le titre de la vidéo:</p>
                <div class="form-group">
                    <input type="text" id="input-article" name="titre" class="form-control" placeholder="Titre de la vidéo..." autocomplete="off">
                </div>
                <p id="multi-color">Description de votre vidéo:</p>
                <div class="form-group">
                    <textarea name="description" id="description" rows="4" cols="50" placeholder="Soyez inspiré..."></textarea>
                </div>
                <p id="multi-color">Choisissez la date à laquelle vous voulez que la vidéo paraisse :</p>
                <div class="form-group">
                    <input type="date" id="date" name="date"/>
                </div>
                <p id="multi-color">Inscrivez le nom de l'auteur uniquement si la vidéo ne vous appartient pas:</p>
                <div class="form-group">
                    <input type="text" id="input-article" name="autor" class="form-control" placeholder="Nom de l'auteur si ce n'est pas vous..." autocomplete="off">
                </div>
                <button type="submit" id="multi-color">Publier</button>
            </form>
        </div>
        <script>
            function handleFiles(files, id) {
                var fileList = document.getElementById(id);
                fileList.innerHTML = '';

                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var listItem = document.createElement('li');

                    if (file.type === 'text/html') {
                        listItem.textContent = 'Nom: ' + file.name + ' (HTML)';
                        // Affiche la prévisualisation ou effectue d'autres opérations nécessaires
                    } else if (file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/svg+xml') {
                        listItem.textContent = 'Nom: ' + file.name + ' (Image)';
                        // Affiche la prévisualisation ou effectue d'autres opérations nécessaires
                    } else {
                        listItem.textContent = 'Nom: ' + file.name + ' (Non pris en charge)';
                    }

                    fileList.appendChild(listItem);
                }
            }
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var contentSelect = document.getElementById('content-select');
                var articleDiv = document.getElementById('article');
                var mediaDiv = document.getElementById('media');
                var videoDiv = document.getElementById('video-cnt');

                contentSelect.addEventListener('change', function () {
                    videoDiv.style.display = 'none';
                    articleDiv.style.display = 'none';
                    mediaDiv.style.display = 'none';

                    // Afficher la section correspondante à la valeur sélectionnée
                    var selectedValue = contentSelect.value;
                    if (selectedValue === 'article') {
                        articleDiv.style.display = 'block';
                    } else if (selectedValue === 'media') {
                        mediaDiv.style.display = 'block';
                    } else if (selectedValue === 'video') {
                        videoDiv.style.display = 'block';
                    }
                });
            });
        </script>
    </main>
    <?php
    $path = "../";
    require_once "../canva/footer.php";
    echo echofooter($path);

} else {
    catchError('edit.php', '../', "Accès refusé", $data['pseudo'], $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=access');
    exit();
}
?>
