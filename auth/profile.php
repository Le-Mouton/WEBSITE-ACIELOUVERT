<?php
global $bdd;
session_start();

require_once "../config.php";
require_once "../error/error.php";

if(isset($_SESSION['user'])){
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();

    $searchTerm = $data['pseudo'];

    if (isset($data)) {
        if ($data['permissions'] == 2) {
            $statementa = $bdd->prepare("SELECT * FROM article WHERE autor = ? ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 6");
            $statementa->execute(array($searchTerm));
        } elseif ($data['pseudo']) {
            $statementa = $bdd->prepare("SELECT * FROM article WHERE autor = ? AND (date_publication <= NOW() OR autor = ?) ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 6");
            $statementa->execute(array($searchTerm, $data['pseudo']));
        }
    } else {
        $statementa = $bdd->prepare("SELECT * FROM article WHERE autor = ? AND date_publication <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 6");
        $statementa->execute(array($searchTerm));
    }
    if (isset($data)) {
        if ($data['permissions'] == 2) {
            $statementm = $bdd->prepare("SELECT * FROM media WHERE autor = ? ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 6");
            $statementm->execute(array($searchTerm));
        } elseif ($data['pseudo']) {
            $statementm = $bdd->prepare("SELECT * FROM media WHERE autor = ? AND (date_publication <= NOW() OR autor = ?) ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 6");
            $statementm->execute(array($searchTerm, $data['pseudo']));
        }
    } else {
        $statementm = $bdd->prepare("SELECT * FROM media WHERE autor = ? AND date_publication <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC LIMIT 6");
        $statementm->execute(array($searchTerm));
    }

    $resultsArticle = $statementa->fetchAll(PDO::FETCH_ASSOC);
    $resultsMedia = $statementm->fetchAll(PDO::FETCH_ASSOC);
} else {
    catchError('profile.php', '../', 'Erreur de connexion', ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=connexion');
    exit();
}
if(isset($_SESSION['user'])){
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <?php
    $path = "../";
    require_once "../canva/header.php";
    echo echoheader($path);
    ?>
    <main>
        <div class="profile">
            <p>Pseudo: <?php echo $data['pseudo'] ?></p>
            <p>Adresse mail: <?php echo $data['email'] ?></p>
            <p>Création du compte: <?php echo $data['date_inscription'] ?></p>
            <p>ID: <?php echo $data['id'] ?></p>
            <?php
            switch ($data['permissions']){
                case 0:
                    ?> <p>Permission: Membre</p><?php
                    break;
                case 1:
                    ?> <p>Permission: Auteur</p> <?php
                    break;
                case 2:
                    ?> <p>Permission: Administrateur</p> <?php
                    break;
            }
            ?>
            <?php
            if ($data['permissions'] == 2){
                ?>
                <form class="profil-form" action="change_perm.php" method="post">
                    <p>Modifier la permission d'un utilisateur:</p>
                <select name="user" id="user">
                    <option value="">-- Utilisateur --</option>
                    <?php
                    $userquery = $bdd->prepare('SELECT id, pseudo FROM users');
                    $userquery->execute();
                    $allusers = $userquery->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($allusers as $user) {
                        ?>
                        <option value="<?= $user['id'] ?>"><?=$user['pseudo']?></option>
                        <?php
                    }
                    ?>
                </select>
                    <select name="perm" id="perm">
                        <option value="">-- Permission --</option>
                        <option value="0">Membre</option>
                        <option value="1">Auteur</option>
                        <option value="2">Administrateur</option>
                    </select>
                    <button type="submit">Modifier</button>
                </form>
                <?php
            }
            ?>
        </div>
        <section class="article">
            <?php
            if (!empty($resultsArticle)) {
                foreach ($resultsArticle as $article) {
                    $id = $article['id'];
                    $titre = $article['nom'];
                    $autor = $article['autor'];
                    $date = $article['date_publication'];
                    $fileName = $article['filename'];

                    $uploadDirectory = '../articles/content/' . $fileName . '_fichiers';
                    $fileName = 'frontpicture';

                    $dateTime = new DateTime($date);

                    // Formatez la date selon le format souhaité
                    $reformDate = $dateTime->format('d/m/Y');

                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'JPEG', 'webp'];

                    $filePath = '';

                    foreach ($allowedExtensions as $extension) {
                        $filePath = $uploadDirectory . '/' . $fileName . '.' . $extension;

                        if (file_exists($filePath)) {
                            break;
                        }
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
                        background-image: url(<?php echo $filePath?>);
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
            }
            ?>
        </section>
        <section class="media">
            <?php
            if (!empty($resultsMedia)) {
                foreach ($resultsMedia as $media) {
                                   ?>
                <a class="profile-media" href="../media/content/media.php?id=<?= $media['id']?>"><img src="../media/content/media<?= $media['id']?>.<?= $media['type']?>" alt="<?= $media['nom'] ?>"></a>
                    <?php
                }
            }
            ?>
        </section>
    </main>

    <?php
    $path = "../";
    require_once "../canva/footer.php";
    echo echofooter($path);
    ?>

    </html>

    <?php
}
?>