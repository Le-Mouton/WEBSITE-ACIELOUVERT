<?php
global $bdd;
session_start();
require_once '../config.php'; // ajout connexion bdd
if(isset($_SESSION['user'])){
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();

    $userList = $bdd->prepare('SELECT pseudo FROM users');
    $userList->execute();
    $userListData = $userList->fetch();

} else {
    catchError('new-content.php','../', 'Erreur de connexion', ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=connexion');
    exit();
}

if($data['permissions'] >= '1'){

    $path="../";
    require_once "../canva/header.php";

    echo echoheader($path);
    ?>
    <main>
        <div class="form-group" id="select-form-group">
            <select class="content-select" name="content" id="content-select">
                <option value="">Ajouter du contenu</option>
                <option value="article">Article</option>
                <option value="media">Media</option>
                <option value="video">Video</option>
                <option value="infographie">Infographie</option>
            </select>
            <span id="alert-mobile">Attention il est conseillé de publier du contenu sur ordinateur!</span>
        </div>
        <div class="drop-area" id="article" style="display: none;">
            <form id="drop" action="../articles/upload.php" method="post" enctype="multipart/form-data">
                <p id="aero-color">Glissez-déposez des fichiers ici</p>
                <div class="form-group">
                    <input type="file" name="fileElem[]" id="fileElem" multiple accept="*/*" onchange="handleFiles(this.files, 'files')">
                    <output id="fileListfiles"></output>
                </div>
                <p id="aero-color">Glissez-déposez votre image de présentation</p>
                <div class="form-group">
                    <input type="file" name="frontpicture" id="frontpicture" multiple accept="*/*" onchange="handleFiles(this.files, 'frontpicture')">
                    <output id="fileListfrontpicture"></output>
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
                    <input type="text" id="input-article" name="nom" class="form-control" placeholder="Nom de l'article..." required="required" autocomplete="off">
                </div>
                <?php
                if($data['permissions'] == 2){
                ?>
                <p id="aero-color">Inscrivez le nom de l'auteur uniquement si l'article ne vous appartient pas:</p>
                <div class="form-group">
                    <input type="text" id="input-article" name="autor" class="form-control" placeholder="Nom de l'auteur si ce n'est pas vous..." autocomplete="off">
                </div>
                <?php
                }
                ?>
                <button type="submit" id="aero-color">Publier</button>
            </form>
        </div>
        <div class="drop-area" id="media" style="display: none">
            <form id="drop" action="../media/upload.php" method="post" enctype="multipart/form-data">
                <p id="spatial-color">Glissez votre image à publier:</p>
                <div class="form-group">
                    <input type="file" name="fileElem" id="fileElem" accept="*/*" onchange="handleFiles(this.files)">
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
                    <input type="text" id="input-article" name="nom" class="form-control" placeholder="Nom de l'article..." required="required" autocomplete="off">
                </div>
                <?php
                if($data['permissions'] == 2){
                    ?>
                    <p id="spatial-color">Inscrivez le nom de l'auteur uniquement si l'article ne vous appartient pas:</p>
                    <div class="form-group">
                        <input type="text" id="input-article" name="autor" class="form-control" placeholder="Nom de l'auteur si ce n'est pas vous..." autocomplete="off">
                    </div>
                    <?php
                }
                ?>
                <button type="submit" id="spatial-color">Publier</button>
            </form>
        </div>
        <div class="drop-area" id="video-cnt" style="display: none">
            <form id="drop" action="../video/upload.php" method="post" enctype="multipart/form-data">
                <p id="multi-color">Glissez le lien de votre vidéo ici:</p>
                <div class="form-group">
                    <input type="text" id="input-article" name="link" class="form-control" placeholder="Lien de la vidéo..." required="required" autocomplete="off">
                </div>
                <p id="multi-color">Inscrivez l'ID de l'article associé à la vidéo s'il y en a un:</p>
                <div class="form-group">
                    <input type="number" id="input-article" name="articleId" class="form-control" placeholder="ID de l'article associé..." autocomplete="off">
                </div>
                <p id="multi-color">Inscrivez le titre de la vidéo:</p>
                <div class="form-group">
                    <input type="text" id="input-article" name="titre" class="form-control" placeholder="Titre de la vidéo..." required="required" autocomplete="off">
                </div>
                <p id="multi-color">Description de votre vidéo:</p>
                <div class="form-group">
                    <textarea name="description" id="description" rows="4" cols="50" placeholder="Soyez inspiré..."></textarea>
                </div>
                <p id="multi-color">Choisissez la date à laquelle vous voulez que la vidéo paraisse :</p>
                <div class="form-group">
                    <input type="date" id="date" name="date"/>
                </div>
                <?php
                if($data['permissions'] == 2){
                    ?>
                    <p id="multi-color">Inscrivez le nom de l'auteur uniquement si l'article ne vous appartient pas:</p>
                    <div class="form-group">
                        <input type="text" id="input-article" name="autor" class="form-control" placeholder="Nom de l'auteur si ce n'est pas vous..." autocomplete="off">
                    </div>
                    <?php
                }
                ?>
                <button type="submit" id="multi-color">Publier</button>
            </form>
        </div>
        <div class="drop-area" id="infographie-cnt" style="display: none">
            <form id="drop" action="../articles/upload.php" method="post" enctype="multipart/form-data">
                <p id="aero-color">Glissez-déposez des fichiers ici</p>
                <div class="form-group">
                    <input type="file" name="fileElem[]" id="fileElem" multiple accept="*/*" onchange="handleFiles(this.files, 'files')">
                    <output id="fileListfiles"></output>
                </div>
                <p id="aero-color">Glissez-déposez votre image de présentation</p>
                <div class="form-group">
                    <input type="file" name="frontpicture" id="frontpicture" multiple accept="*/*" onchange="handleFiles(this.files, 'frontpicture')">
                    <output id="fileListfrontpicture"></output>
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
                    <input type="text" id="input-article" name="nom" class="form-control" placeholder="Nom de l'article..." required="required" autocomplete="off">
                </div>
                <?php
                if($data['permissions'] == 2){
                    ?>
                    <p id="aero-color">Inscrivez le nom de l'auteur uniquement si l'article ne vous appartient pas:</p>
                    <div class="form-group">
                        <input type="text" id="input-article" name="autor" class="form-control" placeholder="Nom de l'auteur si ce n'est pas vous..." autocomplete="off">
                    </div>
                    <?php
                }
                ?>
                <button type="submit" id="aero-color">Publier</button>
            </form>
        </div>
        <script>
            function handleFiles(files, id) {
                var fileList = document.getElementById('fileList'+id);
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
                var infographieDiv = document.getElementById('infographie-cnt');

                contentSelect.addEventListener('change', function () {
                    articleDiv.style.display = 'none';
                    mediaDiv.style.display = 'none';
                    videoDiv.style.display = 'none';
                    infographieDiv.style.display = 'none';

                    var selectedValue = contentSelect.value;
                    if (selectedValue === 'article') {
                        articleDiv.style.display = 'block';
                    } else if (selectedValue === 'media') {
                        mediaDiv.style.display = 'block';
                    } else if(selectedValue === 'video'){
                        videoDiv.style.display = 'block';
                    } else if(selectedValue === 'infographie') {
                        infographieDiv.style.display = 'block';
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
    catchError('new-content.php','../', 'Accès refusé', $data['pseudo'], $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=access');
    exit();
}
?>