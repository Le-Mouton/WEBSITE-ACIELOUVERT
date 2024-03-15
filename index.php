<?php
global $bdd;
session_start();
require_once 'config.php';
require_once 'error/error.php';


if (isset($_SESSION['user'])) {
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();

}

$path = "";

require_once "canva/header.php";

echo echoheader($path);

$update = $_GET['update'] ?? '' ;
switch ($update){
    case '':
        break;
    case 'mail':
        echo "<script>alert('Un Email de vérification vous a été envoyé !');</script>";
        break;
    case 'error':
        echo "<script>alert('Le lien de vérification est invalide ou expiré.');</script>";
        break;
    case 'newpassword':
        echo "<script>alert('Votre mot de passe a été mis à jour avec succès.');</script>";
        break;
    case 'data':
        echo "<script>alert('Les données entré sont incorrectes.');</script>";
        break;
    case 'already_exist':
        echo "<script>alert('Ce contenu existe déjà.');</script>";
        break;
    case 'access':
        echo "<script>alert(\"Vous n'avez pas la permission d'accéder à cette page.\");</script>";
        break;
    case 'connexion':
        echo "<script>alert('Problème de connexion à votre compte.');</script>";
        break;
    case 'nothing':
        echo "<script>alert('Aucun fichier trouvé.');</script>";
        break;
    case 'type':
        echo "<script>alert('Type de contenu inconnu.');</script>";
        break;
    case 'tcc':
        echo "<script>alert('Il y a déjà plus de 4 articles coup de coeur.');</script>";
        break;
}

?>
<main>    
    <figure class="slide">
        <div class="slideshow-container">
            <div class="mySlides fade">
                <a href="category/photociel.php"><img src="img/1.png" alt="img1"/></a>
            </div>
            <div class="mySlides fade">
                <a href="articles/list_articles.php"><img src="img/2.png" alt="img2"/></a>
            </div>
            <div class="mySlides fade">
                <a href="category/partenariats.php"><img src="img/3.png" alt="img3"/></a>
            </div>
            <div class="mySlides fade">
                <a href="category/interviews.php"><img src="img/4.png" alt="img3"/></a>
            </div>
        </div>
    </figure>
    <section class="article-recent">
        <div class="row"><h2>ARTICLES RÉCENTS:</h2></div>
        <section class="article">
        <?php
        if(isset($data)){
            if($data['permissions'] == 2){
                $statement = $bdd->prepare("SELECT * FROM article ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 6");
                $statement->execute(array());
            } elseif($data['pseudo']){
                $statement = $bdd->prepare("SELECT * FROM article WHERE (date_publication <= NOW() OR autor = :autor) ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 6");
                $statement->execute(array('autor'=>$data['pseudo']));
            }
        } else {
            $statement = $bdd->prepare("SELECT * FROM article WHERE date_publication <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 6");
            $statement->execute(array());
        }

            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $titre = $row['nom'];
                $autor = $row['autor'];
                $date = $row['date_publication'];
                $fileName = $row['filename'];

                $uploadDirectory = 'articles/content/' . $fileName . '_fichiers';
                $fileName = 'frontpicture';
                
                $dateTime = new DateTime($date);

                $reformDate = $dateTime->format('d/m/Y');


                $parts = explode('/', $uploadDirectory); // Diviser le chemin en parties
                $encodedParts = array_map('rawurlencode', $parts); // Encoder chaque partie
                $uploadDirectoryEncoded = implode('/', $encodedParts); // Reconstruire le chemin

                $patern = $uploadDirectoryEncoded . '/' . $fileName . '.*';
                $filePath = glob($patern);

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
                <a href="articles/content/article.php" class="bart" id="<?php echo $id?>">
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
                        if(isset($filePath[0]))
                            echo $filePath[0];
                        else{
                            $uploadDirectory = 'articles/content/' . $titre . '_infographie';
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

                            window.location.href = 'articles/content/article.php?id=' + linkId;
                        });
                    });
                </script>
                <?php

            }
        ?>
        </section>
        <a href="articles/list_articles.php" class="voir-plus-btn">Découvrir plus d'articles</a>
    </section>
    <aside class="autre">
        <div class="filtre">
        <section class="categorie">

            <div class="box">
                <div class="titre"><h3>LE SAVIEZ<br><span id="projet-color">VOUS?</span></h3></div>
                <a href=""><img src="img/lesaviezvous.webp" alt=""></a>
            </div>
        </section>
        <section class="categorie">

            <div class="box">
                <div class="titre"><h3>ASTRO<br><span id="spatial-color">PHOTO</span></h3></div>
                <?php
                $statement = $bdd->prepare("SELECT * FROM media WHERE category = 'astrophoto' AND date_publication <= NOW() LIMIT 1");
                $statement->execute();

                $result = $statement->fetch(PDO::FETCH_ASSOC);
                echo '<a href="media/astrophoto.php"><img src="media/content/media' . $result['id'] . '.' . $result['type'] . '" alt=""></a>';
                ?>
        </section>
        <section class="categorie">
            <div class="box">
                <div class="titre"><h3>SPOTTING<br><span id="aero-color">AÉRIEN</span></h3></div>
                <?php
                $statement = $bdd->prepare("SELECT * FROM media WHERE category = 'spotting' AND date_publication <= NOW() LIMIT 1");
                $statement->execute();

                $result = $statement->fetch(PDO::FETCH_ASSOC);
                echo '<a href="media/spotting.php"><img src="media/content/media' . $result['id'] . '.' . $result['type'] . '" alt=""></a>';
                ?>
            </div>
        </section>
        </div>
    </aside>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="script.js"></script>
    <script>showSlides()</script>
</main>
<?php
$path = "";
require_once "canva/footer.php";
echo echofooter($path);
?>

</html>