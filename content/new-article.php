<?php
global $bdd;
session_start();
    require_once '../config.php'; // ajout connexion bdd
    if(isset($_SESSION['user'])){
    // On récupere les données de l'utilisateur
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();

    }
?>
<?php
if($data['permissions'] >= '1'){
    ?>

    <html lang="fr">
    <?php
    $path="../";
    require_once "../canva/header.php";

    echo echoheader($path);
    ?>
    <main>
        <div class="form-group">
            <select name="content" id="content-select">
                <option value="">--Choisissez un type de contenu à ajouter--</option>
                <option value="article">Article</option>
                <option value="media">Media</option>
            </select>
        </div>
        <div class="drop-area" id="article" style="display: none;">
            <form id="drop" action="../articles/upload.php" method="post" enctype="multipart/form-data">
                <p>Glissez-déposez des fichiers ici</p>
                <div class="form-group">
                    <input type="file" name="fileElem[]" id="fileElem" multiple accept="*/*" onchange="handleFiles(this.files)">
                    <p>Glissez-déposez votre image de présentation</p>
                    <input type="file" name="frontpicture" id="frontpicture" multiple accept="*/*" onchange="handleFiles(this.files)">
                </div>
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
                <div class="form-group">
                    <input type="date" id="date" name="date"/>
                </div>
                <div class="form-group">
                    <input type="text" id="input-article" name="nom" class="form-control" placeholder="Nom de l'article..." required="required" autocomplete="off">
                </div>
                <div class="form-group">
                    <input type="text" id="input-article" name="autor" class="form-control" placeholder="Nom de l'auteur si ce n'est pas vous..." autocomplete="off">
                </div>
                <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Publier</button>
                </div>
            </form>
            <output id="fileList"></output>
        </div>
        <div class="drop-area" id="media" style="display: none">
            <form id="drop" action="../media/upload.php" method="post" enctype="multipart/form-data">
                <p>Glissez-déposez des fichiers ici</p>
                <div class="form-group">
                    <input type="file" name="fileElem" id="fileElem" accept="*/*" onchange="handleFiles(this.files)">
                </div>
                <div class="form-group">
                    <select name="category" id="category-select">
                        <option value="">--Choisissez une catégorie--</option>
                        <option value="spotting">SPOTTING</option>
                        <option value="astrophoto">ASTROPHOTO</option>
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="description" id="description" rows="4" cols="50" placeholder="Soyez inspiré..."></textarea>
                </div>
                <div class="form-group">
                    <input type="date" id="date" name="date"/>
                </div>
                <div class="form-group">
                    <input type="text" id="input-article" name="nom" class="form-control" placeholder="Nom de l'article..." required="required" autocomplete="off">
                </div>
                <div class="form-group">
                    <input type="text" id="input-article" name="autor" class="form-control" placeholder="Nom de l'auteur si ce n'est pas vous..." autocomplete="off">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Publier</button>
                </div>
            </form>
            <output id="fileList"></output>
        </div>

        <script>
            function handleFiles(files) {
                var fileList = document.getElementById('fileList');
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

                contentSelect.addEventListener('change', function () {
                    // Masquer toutes les sections
                    articleDiv.style.display = 'none';
                    mediaDiv.style.display = 'none';

                    // Afficher la section correspondante à la valeur sélectionnée
                    var selectedValue = contentSelect.value;
                    if (selectedValue === 'article') {
                        articleDiv.style.display = 'block';
                    } else if (selectedValue === 'media') {
                        mediaDiv.style.display = 'block';
                    }
                });
            });
        </script>
    </main>
    <?php
    $path = "../";
    require_once "../canva/footer.php";
    echo echofooter($path);
    ?>
    </html>
<?php
} else {
    header('Location: ../index.php');
    exit();
}
?>