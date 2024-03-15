<!DOCTYPE html>
<html lang="fr">
<?php
$path = "../";
require_once "../canva/header.php";
echo echoheader($path);
if(isset($_SESSION['user'])){
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();
}
?>
<main>
    <section class="partner-container">
        <div class="article-list">
            <div class="title">
                <h1 id="partner-color">Nos Interviews:</h1>
            </div>
            <?php
            $category="interview";
            if(isset($data)){
                if($data['permissions'] == 2){
                    $statement = $bdd->prepare("SELECT * FROM article WHERE category LIKE :category ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC");
                    $statement->execute(array('category'=>$category));
                } elseif($data['pseudo']){
                    $statement = $bdd->prepare("SELECT * FROM article WHERE category LIKE :category AND (date_publication <= NOW() OR autor = :autor) ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC");
                    $statement->execute(array('autor'=>$data['pseudo'], 'category'=>$category));
                }
            } else {
                $statement = $bdd->prepare("SELECT * FROM article WHERE category LIKE :category AND date_publication <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC");
                $statement->execute(array('category'=>$category));
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
    </section>
</main>
<?php
$path = "../";
require_once "../canva/footer.php";
echo echofooter($path);
?>

</html>