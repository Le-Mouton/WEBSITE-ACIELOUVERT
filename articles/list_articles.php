<?php
global $bdd;
session_start();
require_once '../config.php';
require_once '../error/error.php';

if(isset($_SESSION['user'])){
    // On récupere les données de l'utilisateur
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$path = "../";
require_once "../canva/header.php";
echo echoheader($path);
?>
<main>
    <div class="blur-banner-header">
        <div class="blur">
            <div class="title">
                <h1>TOUS LES ARTICLES</h1>
            </div>
        </div>
    </div>
    <section class="full-article">
        <?php
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $articlesPerPage = 9;
        $offset = ($page - 1) * $articlesPerPage;

        if(isset($data)){
            if($data['permissions'] == 2){
                $statement = $bdd->prepare("SELECT * FROM article ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT $articlesPerPage OFFSET $offset");
                $statement->execute(array());
            } elseif($data['pseudo']){
                $statement = $bdd->prepare("SELECT * FROM article WHERE (date_publication <= NOW() OR autor = :autor) ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT $articlesPerPage OFFSET $offset");
                $statement->execute(array('autor'=>$data['pseudo']));
            }
        } else {
            $statement = $bdd->prepare("SELECT * FROM article WHERE date_publication <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT $articlesPerPage OFFSET $offset");
            $statement->execute(array());
        }

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
            <a href="content/article.php" class="bart" id="<?php echo $id?>">
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

                        window.location.href = 'content/article.php?id=' + linkId;
                    });
                });
            </script>
            <?php

        }
        ?>
    </section>

    <?php
    // Pagination
    if(isset($data)){
        if($data['permissions'] == 2){
            $statement = $bdd->prepare("SELECT * FROM article ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW()))");
            $statement->execute(array());
        } elseif($data['pseudo']){
            $statement = $bdd->prepare("SELECT * FROM article WHERE (date_publication <= NOW() OR autor = :autor) ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW()))");
            $statement->execute(array('autor'=>$data['pseudo']));
        }
    } else {
        $statement = $bdd->prepare("SELECT * FROM article WHERE date_publication <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW()))");
        $statement->execute(array());
    }

    $nbr_page = ceil($statement->rowCount() / $articlesPerPage);
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

</html>


