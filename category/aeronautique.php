<?php

$path = "../";
require_once "../canva/header.php";
require_once"../config.php";
echo echoheader($path);

if(isset($_SESSION['user'])){
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();
}

?>
<main>
<div class="banner-head" id="aero">
    <div class="banner-box">
        <h1 id="aero-color">AÉRONAUTIQUE</h1>
        <p>Coups de cœur de la rédaction:</p>
        <div class="article-box">
            <?php

            $category="aeronautique";

            if(isset($data)){
                if($data['permissions'] == 2){
                    $statement = $bdd->prepare("SELECT * FROM article WHERE category LIKE :category AND selected = 1 ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 4");
                    $statement->execute(array('category'=>$category));
                } elseif($data['pseudo']){
                    $statement = $bdd->prepare("SELECT * FROM article WHERE category LIKE :category AND selected = 1 AND (date_publication <= NOW() OR autor = :autor) ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 4");
                    $statement->execute(array('autor'=>$data['pseudo'], 'category'=>$category));
                }
            } else {
                $statement = $bdd->prepare("SELECT * FROM article WHERE category LIKE :category AND selected = 1 AND date_publication <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 4");
                $statement->execute(array('category'=>$category));
            }

            $statement->execute();

            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $articlesPerPage = 9;
            $offset = ($page - 1) * $articlesPerPage;

            // Affichage des résultats
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $titre = $row['nom'];
                $autor = $row['autor'];
                $date = $row['date_publication'];
                $fileName = $row['filename'];

                $uploadDirectory = '../articles/content/' . $fileName . '_fichiers';
                $fileName = 'frontpicture';

                $dateTime = new DateTime($date);

                // Formatez la date selon le format souhaité
                $reformDate = $dateTime->format('d/m/Y');

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'JPEG', 'webp'];

                foreach ($allowedExtensions as $extension) {
                    $filePath = $uploadDirectory . '/' . $fileName . '.' . $extension;


                    if (file_exists($filePath)) {

                        $filePath;
                        break;
                    }
                }

                ?>
                <a href="../articles/content/article.php" class="bart" id="<?php echo $id?>">
                    <article class="art" id="<?php echo 'id' . $id ?>">
                        <div class="color-barre" id="aero-color"></div>
                        <div class="bright">
                            <p><?php echo $autor?><br><?php echo $reformDate?></p>
                            <h3><?php echo $titre?></h3>
                        </div>
                    </article>
                </a>
                <style>
                    #id<?php echo $id?>{
                        color: white;
                        background-image: url(<?php
                    $parts = explode('/', $filePath); // Diviser le chemin en parties
                    $encodedParts = array_map('rawurlencode', $parts); // Encoder chaque partie
                    $filePathEncoded = implode('/', $encodedParts); // Reconstruire le chemin

                    if(file_exists($filePathEncoded))
                        echo $filePathEncoded;
                    else{
                        $uploadDirectory = '../articles/content/' . $titre . '_infographie';
                        $parts = explode('/', $uploadDirectory); // Diviser le chemin en parties
                        $encodedParts = array_map('rawurlencode', $parts); // Encoder chaque partie
                        $uploadDirectoryEncoded = implode('/', $encodedParts); // Reconstruire le chemin

                        $patern = $uploadDirectory . '/' .'frontpicture' . '.*';
                        $filePath = glob($patern);
                        $extension = pathinfo($filePath[0], PATHINFO_EXTENSION);
                        echo $uploadDirectoryEncoded . '/frontpicture.' . $extension;
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

                            window.location.href = '../articles/content/article.php?id=' + linkId;
                        });
                    });
                </script>
                <?php

            }
            ?>
        </div>
    </div>
</div>
<section class="main-container">
    <div class="article-list">
        <div class="title">
            <h3 id="aero-color">
                <?php $type = $_GET["type"] ?? '';

                switch ($type){
                    case '':
                        echo'Nos articles:';
                        break;
                    case 'actualite':
                        echo "Nos articles d'actualié:";
                        break;
                    case 'creation':
                        echo 'Nos articles au sujet de création:';
                        break;
                    case 'technologie';
                        echo 'Nos articles de technologie:';
                        break;
                    case 'histoire';
                        echo "Nos articles d'hisoire:";
                        break;
                    case 'quiz';
                        echo "Nos quizs:";
                        break;
                }?></h3>

        </div>

        <?php
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $articlesPerPage = 4;
        $offset = ($page - 1) * $articlesPerPage;

        $category="aeronautique";

        if(!empty($type)){
            if(isset($data)){
                if($data['permissions'] == 2){
                    $statement = $bdd->prepare("SELECT * FROM article WHERE category LIKE :category AND type LIKE :type ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT $articlesPerPage OFFSET $offset");
                    $statement->execute(array('category'=>$category, 'type'=>$type));
                } elseif($data['pseudo']){
                    $statement = $bdd->prepare("SELECT * FROM article WHERE category LIKE :category AND type LIKE :type AND (date_publication <= NOW() OR autor = :autor) ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT $articlesPerPage OFFSET $offset");
                    $statement->execute(array('autor'=>$data['pseudo'], 'category'=>$category, 'type'=>$type));
                }
            } else {
                $statement = $bdd->prepare("SELECT * FROM article WHERE category LIKE :category AND type LIKE :type AND date_publication <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT $articlesPerPage OFFSET $offset");
                $statement->execute(array('category'=>$category, 'type'=>$type));
            }
        } else {
            if(isset($data)){
                if($data['permissions'] == 2){
                    $statement = $bdd->prepare("SELECT * FROM article WHERE category LIKE :category ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT $articlesPerPage OFFSET $offset");
                    $statement->execute(array('category'=>$category));
                } elseif($data['pseudo']){
                    $statement = $bdd->prepare("SELECT * FROM article WHERE category LIKE :category AND (date_publication <= NOW() OR autor = :autor) ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT $articlesPerPage OFFSET $offset");
                    $statement->execute(array('autor'=>$data['pseudo'], 'category'=>$category));
                }
            } else {
                $statement = $bdd->prepare("SELECT * FROM article WHERE category LIKE :category AND date_publication <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT $articlesPerPage OFFSET $offset");
                $statement->execute(array('category'=>$category));
            }
        }
        // Affichage des résultats
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $titre = $row['nom'];
            $autor = $row['autor'];
            $date = $row['date_publication'];
            $fileName = $row['filename'];

            $uploadDirectory = '../articles/content/' . $fileName . '_fichiers';
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
            <a href="../articles/content/article.php" class="bart" id="<?php echo $id?>">
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
                            $uploadDirectory = '../articles/content/' . $titre . '_infographie';
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

                        window.location.href = '../articles/content/article.php?id=' + linkId;
                    });
                });
            </script>
            <?php

        }
        ?>
    </div>
    <aside id="aside">
        <div class="article-category">
            <h3>Catégories:</h3>
            <div class="color-bar" id="aero-color"></div>
            <a href="aeronautique.php?type=actualite" class="nav-category"><p>Actualité</p></a>
            <a href="aeronautique.php?type=technologie" class="nav-category"><p>Technologie</p></a>
            <a href="aeronautique.php?type=histoire" class="nav-category"><p>Histoire</p></a>
            <a href="aeronautique.php?type=creation" class="nav-category"><p>Créations</p></a>
            <a href="aeronautique.php?type=quiz" class="nav-category"><p>Quiz</p></a>
            <div class="color-bar" id="aero-color"></div>
        </div>
        <div class="search-bar">
            <h3>Rechercher:</h3>
            <div class="color-bar" id="aero-color"></div>

            <input type="text" id="search-input" placeholder="Rechercher...">
            <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
            <script src="../script.js"></script>

            <div id="searchResults"></div>
        </div>
    </aside>
    <div class="close-aside" id="close-aside"><button class="btn-aside" onclick="toggleAside()"><i class="fas fa-angle-right"></i></button></div>
    <div class="open-aside" id="open-aside"><button class="btn-aside" onclick="toggleAside()"><i class="fas fa-angle-left"></i></button></div>
    <script>
        function toggleAside() {
            var aside = document.getElementById('aside');
            var close = document.getElementById('close-aside');
            var open = document.getElementById('open-aside');

            if (aside.style.display === 'none' || aside.style.display === '') {
                aside.style.display = 'flex';
                close.style.display = 'block'
                open.style.display = 'none'
            } else {
                aside.style.display = 'none';
                close.style.display = 'none';
                open.style.display = 'block';
            }
        }
    </script>
</section>
    <?php
    $nbrpage = $bdd->prepare("SELECT * FROM article WHERE category = ? AND date_publication <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW()))");
    $nbrpage->execute(array($category));

    $nbr_page = ceil($nbrpage->rowCount() / $articlesPerPage);
    ?>
    <div class="page">
        <?php
        $nextPage = $page + 1;
        $prevPage = $page - 1;

        if ($prevPage > 0) {
            echo '<a href="?page=' . $prevPage . '" class="other-page">Page précédente</a>';
        }
        ?>   <p class="page-nbr"><?php echo $page; ?></p> <?php
        if ($nextPage <= $nbr_page) {
            echo '<a href="?page=' . $nextPage . '" class="other-page">Page suivante</a>';
        }
        ?>
    </div>
</main>
<?php
$path = "../";
require_once "../canva/footer.php";
echo echofooter($path);
?>