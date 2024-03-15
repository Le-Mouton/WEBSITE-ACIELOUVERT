<?php

global $bdd;
session_start();
require_once '../../config.php'; // ajout connexion bdd
require_once '../../error/error.php';

if(isset($_GET['id'])){
        $ID = $_GET['id'];
    if (isset($_SESSION['user'])) {
        // On récupère les données de l'utilisateur
        $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
        $req->execute(array($_SESSION['user']));
        $data = $req->fetch();

        $checkView = $bdd->prepare('SELECT * FROM vue WHERE article_id = (?) AND user_id = (?) AND type = 1');
        $checkView->execute(array($ID, $data['id']));


        if($checkView->rowCount() == 0){
            $addview = $bdd->prepare('INSERT INTO vue(article_id, user_id, type) VALUES (:article_id, :user_id, :type)');
            $addview->execute(array('article_id' =>$ID, 'user_id'=>$data['id'], 'type'=>1));
        }
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        $checkView = $bdd->prepare('SELECT * FROM vue WHERE article_id = (?) AND ip = (?) AND type = 1');
        $checkView->execute(array($ID, $ip));

        if ($checkView->rowCount() == 0) {
            $addview = $bdd->prepare('INSERT INTO vue(article_id, ip, type) VALUES (:article_id, :ip, :type)');
            $addview->execute(array('article_id' => $ID, 'ip' => $ip, 'type' => 1));
        }
    }
    $query = 'SELECT * FROM article WHERE id LIKE :ID';
    $statement = $bdd->prepare($query);
    $statement->bindParam(':ID', $ID, PDO::PARAM_STR);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);

    $now = new DateTime();
    $dateTime = new DateTime($result['date_publication']);

    if($dateTime >= $now){
        if(!isset($data)){
            catchError('article.php', '../../', 'Données incorrectes', ip: $_SERVER['REMOTE_ADDR']);
            header('Location: ../../index.php?update=data');
            exit();
        } else {
            if ($data['permissions'] <= 1 && $data['pseudo'] != $result['autor']) {
                catchError('article.php', '../../', 'Données incorrectes', ip: $_SERVER['REMOTE_ADDR']);
                header('Location: ../../index.php?update=data');
                exit();
            }
        }
    }
    $path = '../../';
    require_once '../../canva/header.php';
    echo echoheader($path);
    ?>
        <main>
    <section class="main-container">
            <div class="article-page">
            <div class="article-container">
                <?php
                    $reformDate = $dateTime->format('d F Y');

                    echo '<p>' . $result['autor'] . '<br>' . $reformDate . '</p>';

                    $like = $bdd->prepare('SELECT * FROM likes WHERE article_id = (?) AND type = 1');
                    $like->execute(array($ID));
                    ?><div class="tag-list"><?php
                    $tagid = $result['tag'];
                    if($tagid!=''){
                        $alltagid = explode(',', $tagid);
                        $parametres = str_repeat('?,', count($alltagid) - 1) . '?';
                        $queryTag = $bdd->prepare('SELECT * FROM tag WHERE id IN (' . $parametres . ')');
                        $queryTag->execute($alltagid);
                        $alltag = $queryTag->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($alltag as $tag) :
                            echo '<p class="tag-elem" id="tag">' . $tag['nom'] . '</p>';
                        endforeach;
                    }
                    ?></div>
                <div class="row">
                    <div class="more-button">
                        <ul><a href=""><i class="fas fa-bars"></i></a>
                            <li class="more-button-list"  id="more-button-list"><a onclick="copyToClipboard()">Partager</a>
                                <script>
                                    function copyToClipboard() {
                                        var tempInput = document.createElement("input");
                                        tempInput.value = window.location.href;
                                        document.body.appendChild(tempInput);
                                        tempInput.select();
                                        document.execCommand("copy");
                                        document.body.removeChild(tempInput);
                                        alert("Lien copié dans le presse-papiers !");
                                    }</script>
                                <?php
                                if (isset($_SESSION['user'])) {
                                    if($data['permissions'] == 2){
                                        if($result['selected'] == 1){
                                            ?>
                                            <a href="../selected.php?id=<?= $ID?>&category=<?= $result['category']?>&remove=true">Retirer le coup de cœur</a>
                                            <?php }
                                        else{?>
                                        <a href="../selected.php?id=<?= $ID?>&category=<?= $result['category']?>&remove=false">Coup de cœur</a>
                                        <?php }?>
                                        <a href="../../content/tag.php">Gérer les tags</a>
                                        <a href="../../content/delete.php?id=<?= $result['id'] ?>&type=article">Supprimer l'article</a>
                                        <a href="../../content/edit.php">Modifier l'article</a>
                                    <?php }}?>
                            </li></ul></div>
                    <a href="#section-commentaire"><i class="far fa-comment-alt"></i></a>
                    <?php if(isset($data['id'])){
                        $checklike = $bdd->prepare('SELECT * FROM likes WHERE user_id = ? AND article_id = ? AND type = 1');
                        $checklike->execute(array($data['id'], $ID));
                        if($checklike->rowCount() == 1){
                            ?>
                            <style>
                                .article-container .row .like i{
                                    color: red;
                                }
                            </style> <?php }?>
                    <a class="like" href="../../like.php?id_art=<?= $ID ?>&id_user=<?= $data['id'] ?>&type=1"><i class="fas fa-heart"></i></a>
                    <?php } else {?> <a href=""><i class="fas fa-heart"></i></a><?php }?>
                </div>
                <?php
                    if(file_exists($ID . '.htm')) {
                        $pageContent = file_get_contents($ID . '.htm');
                        $encoding = mb_detect_encoding($pageContent, 'ASCII, UTF-8, ISO-8859-1, Windows-1252');
                        if (strtolower($encoding) != 'utf-8') {
                            $pageContent = $contenuUtf8 = mb_convert_encoding($pageContent, 'UTF-8', $encoding);
                        }

                        echo $pageContent;
                    } else{
                        $dir = opendir($result['filename']);
                        ?>
                        <div class="infographie-container">
                        <?php
                        while ($fichier = readdir($dir)) {
                            if($fichier != '.' && $fichier != '..'){
                                $filename = $result['filename'];
                                $filePath = $filename . "/" . $fichier;

                                ?>

                                <img src='<?= $filePath?>' alt='<?= $fichier ?>'/><br>
                                <?php
                            }
                        }

                        closedir($dir);
                    }
                ?></div>
            </div>
                <aside id="aside">
                    <div class="article-category">
                        <h3>Catégories:</h3>
                        <div class="color-bar" id="aero-color"></div>
                        <a href="../../category/aeronautique.php" class="nav-category"><p>Aéronautique</p></a>
                        <a href="../../category/spatial.php" class="nav-category"><p>Spatial</p></a>
                        <a href="../../category/multimedia.php" class="nav-category"><p>Multimédia</p></a>
                        <a href="../../category/atmosvert.php" class="nav-category"><p>Atmos'Vert</p></a>
                        <a href="../../category/projet.php" class="nav-category"><p>Projets</p></a>
                        <div class="color-bar" id="aero-color"></div>
                    </div>
                    <div class="search-bar">
                        <h3>Rechercher:</h3>
                        <div class="color-bar" id="aero-color"></div>
                        <input type="text" id="search-input" placeholder="Rechercher...">
                        <div id="searchResults" class="search-results"></div>
                    </div>
                    <div class="container-article-récent">
                        <h2>Autres articles:</h2>
                        <?php
                        // Sélection des titres et des premières images des 6 articles les plus récents
                        $query = "SELECT id, nom, date_publication, autor, filename FROM article ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 4";
                        $statement = $bdd->query($query);

                        // Affichage des résultats
                        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                            $id = $row['id'];
                            $titre = $row['nom'];
                            $autor = $row['autor'];
                            $date = $row['date_publication'];
                            $fileName = $row['filename'];

                            $uploadDirectory = '../../articles/content/' . $fileName . '_fichiers';
                            $fileName = 'frontpicture';

                            $dateTime = new DateTime($date);

                            $reformDate = $dateTime->format('d/m/Y');


                            $parts = explode('/', $uploadDirectory); // Diviser le chemin en parties
                            $encodedParts = array_map('rawurlencode', $parts); // Encoder chaque partie
                            $uploadDirectoryEncoded = implode('/', $encodedParts); // Reconstruire le chemin

                            $patern = $uploadDirectory . '/' . $fileName . '.*';
                            $filePath = glob($patern);

                            $patern = $uploadDirectory . '/' .'frontpicture' . '.*';
                            $filePath = glob($patern);
                            if($filePath) {
                                $pathextension = pathinfo($filePath[0], PATHINFO_EXTENSION);
                                $file = $uploadDirectoryEncoded . '/frontpicture.' . $pathextension;
                            }else{
                                $file='';
                            }
                            $like = $bdd->prepare('SELECT * FROM likes WHERE article_id = (?) AND type = 1');
                            $like->execute(array($id));
                            $vue = $bdd->prepare('SELECT * FROM vue WHERE article_id = (?) AND type = 1');
                            $vue->execute(array($id));
                            $com = $bdd->prepare('SELECT * FROM commentaire WHERE article_id = (?) AND type = 1');
                            $com->execute(array($id));
                        if (isset($_SESSION['user'])){
                            $checklike = $bdd->prepare('SELECT * FROM likes WHERE user_id = ? AND article_id = ? AND type = 1');
                            $checklike->execute(array($data['id'], $id));
                        if($checklike->rowCount() == 1){
                            ?>
                            <style>
                                #coeur<?= $id?>{
                                    color: red;
                                    font-weight: 900;
                                }
                                #coeur<?= $id?> span{
                                    color: white;
                                }
                            </style> <?php }}?>
                            <a href="article.php" class="bart" id="<?php echo $id?>">
                                <article class="art" id="<?php echo 'id' . $id ?>">
                                    <div class="bright">
                                        <p><?php echo $autor?><br><?php echo $reformDate?></p>
                                        <h3><?php echo $titre?></h3>
                                        <div class="color-bar"></div>
                                        <div class="row">
                                            <i class="fas fa-eye"><?= "   " . $vue->rowCount() ?></i>
                                            <i id="coeur<?= $id?>" class="fas fa-heart"><span><?= "   " . $like->rowCount() ?></span></i>
                                            <i class="far fa-comment-alt"><?= "   " . $com->rowCount() ?></i>
                                        </div>
                                    </div>
                                </article>
                            </a>
                            <style>
                                #id<?php echo $id?>{
                                    color: white;
                                    background-image: url(<?php
                        if(!empty($file)){
                            echo $file;
                        }else{
                            $uploadDirectory = '../../articles/content/' . $titre . '_infographie';
                            $parts = explode('/', $uploadDirectory); // Diviser le chemin en parties
                            $encodedParts = array_map('rawurlencode', $parts); // Encoder chaque partie
                            $uploadDirectoryEncoded = implode('/', $encodedParts); // Reconstruire le chemin

                            $patern = $uploadDirectory . '/' .'frontpicture' . '.*';
                            $filePath = glob($patern);
                            $pathextension = pathinfo($filePath[0], PATHINFO_EXTENSION);
                            echo $uploadDirectoryEncoded . '/frontpicture.' . $pathextension;
                            }
                        ?>);
                                    background-repeat: no-repeat;
                                    background-size: cover;
                                    background-position: center;
                                }
                            </style>
                            <script>
                                document.querySelectorAll('.bart').forEach(function(link) {
                                    link.addEventListener('click', function(event) {
                                        event.preventDefault();

                                        var linkId = this.id;

                                        window.location.href = 'article.php?id=' + linkId;
                                    });
                                });
                            </script>
                            <?php

                        }
                        ?>
                    </div>
                </aside id="aside">
                <div class="close-aside" id="close-aside"><button class="btn-aside" onclick="toggleAside()"><i class="fas fa-angle-right"></i></button></div>
                <div class="open-aside" id="open-aside"><button class="btn-aside" onclick="toggleAside()"><i class="fas fa-angle-left"></i></button></div>
                <script>
                    function toggleAside() {
                        var aside = document.getElementById('aside');
                        var close = document.getElementById('close-aside');
                        var open = document.getElementById('open-aside');
                        var btn = document.getElementById('more-button-list');

                        if (aside.style.display === 'none' || aside.style.display === '') {
                            aside.style.display = 'flex';
                            close.style.display = 'block'
                            open.style.display = 'none'
                            btn.style.right = '20%';
                        } else {
                            aside.style.display = 'none';
                            close.style.display = 'none';
                            open.style.display = 'block';
                            btn.style.right = 'auto';
                        }
                    }
                </script>
            </div>
    </section>
            <section class="commentaires" id="section-commentaire">
                <h3>Commentaires: </h3>
                <div class="comment">
                <?php
                $comment = $bdd->prepare('SELECT * FROM commentaire WHERE article_id = (?) AND type = 1');
                $comment->execute(array($ID));

                while ($row = $comment->fetch(PDO::FETCH_ASSOC)) {
                    $dateTime = new DateTime($row['date']);
                    $reformDate = $dateTime->format('d/m/Y');

                    ?>
                    <div class="comment-box">
                        <p><?= $row['autor'] ?> <br>
                        <?= $reformDate?></p>
                        <div class="row">
                        <p id="comment"><?= $row['commentaire']?></p>
                        <?php
                        if (isset($_SESSION['user'])) {
                            if ($row['autor'] == $data['pseudo'] or $data['permissions'] == 2) {
                                echo "<a href=\"../../comment.php?id=" . $row['id'] . "&ida=" . $row['article_id'] . "&type=1" . "\" id=\"btnSupprimer\"><i class=\"fas fa-times\"></i></a>";
                            }
                        }
                        ?>
                    </div></div>
                    <?php
                }
                if($comment->rowCount() == 0){
                    echo "Il n'y a aucun commentaire pour le moment n'hésitez pas à en mettre un!";
                }
                ?>
                </div>
                <form action="../../comment.php?id=<?= $ID?>&type=1" method="post">
                    <p>Écrivez vôtre commentaire ici :</p>
                    <div class="form-comment">
                        <textarea name="comment" id="multilineInput" rows="4" cols="50" placeholder="Soyez inspiré..."></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit">Envoyer le commentaire</button>
                    </div>
                </form>
            </section>


<?php
$path = '../../';
require_once '../../canva/footer.php';
echo echofooter($path);
} else {
    catchError('article.php', '../../', 'Données incorrectes' . $_GET['id'], ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../../index.php?update=data');
    exit();
}
