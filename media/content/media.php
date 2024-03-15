<?php
global $bdd;
session_start();
require_once '../../config.php'; // ajout connexion bdd
require_once '../../error/error.php';

if(isset($_GET['id'])){
    $ID = $_GET['id'];
    $media = $bdd->prepare('SELECT * FROM media WHERE id = ?');
    $media->execute(array($ID));

    $result = $media->fetch(PDO::FETCH_ASSOC);
    $dateTime = new DateTime($result['date_publication']);
    $now = new DateTime();
    if (isset($_SESSION['user'])) {
        // On récupère les données de l'utilisateur
        $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
        $req->execute(array($_SESSION['user']));
        $data = $req->fetch();

        $checkView = $bdd->prepare('SELECT * FROM vue WHERE article_id = (?) AND user_id = (?) AND type = 2');
        $checkView->execute(array($ID, $data['id']));

        if($checkView->rowCount() == 0){
            $addview = $bdd->prepare('INSERT INTO vue(article_id, user_id, type) VALUES (:article_id, :user_id, :type)');
            $addview->execute(array('article_id' =>$ID, 'user_id'=>$data['id'], 'type'=>2));
        }
    }   else {
        $ip = $_SERVER['REMOTE_ADDR'];
        $checkView = $bdd->prepare('SELECT * FROM vue WHERE article_id = (?) AND ip = (?) AND type = 2');
        $checkView->execute(array($ID, $ip));

        if($checkView->rowCount() == 0){
            $addview = $bdd->prepare('INSERT INTO vue(article_id, ip, type) VALUES (:article_id, :ip, :type)');
            $addview->execute(array('article_id' =>$ID, 'ip'=>$ip, 'type'=>1));
        }
    }
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
    ?>
    <?php
    $path = '../../';
    require_once '../../canva/header.php';
    echo echoheader($path);
    ?>
    <main>
        <div class="article-page">
            <div class="article-container">
                <?php

                $reformDate = $dateTime->format('d F Y');

                echo '<p>' . $result['autor'] . '<br>' . $reformDate . '</p>';

                $like = $bdd->prepare('SELECT * FROM likes WHERE article_id = (?) AND type = 2');
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
                            <li class="more-button-list" id="more-button-list"><a href="">Partager</a>
                                <?php
                                if (isset($_SESSION['user'])) {
                                    if($data['permissions'] == 2){ ?>
                                        <a href="../../content/tag.php">Gérer les tags</a>
                                        <a href="../../content/delete.php?id=<?=$result['id']?>&type=media">Supprimer le media</a>
                                        <a href="../../content/edit.php">Modifier l'article</a>
                                    <?php }}?>
                            </li></ul></div>
                    <a href="#section-commentaire"><i class="far fa-comment-alt"></i></a>
                    <?php if(isset($data['id'])){
                        $checklike = $bdd->prepare('SELECT * FROM likes WHERE user_id = ? AND article_id = ? AND type = 2');
                        $checklike->execute(array($data['id'], $ID));
                        if($checklike->rowCount() == 1){
                            ?>
                            <style>
                                .article-container .row .like i{
                                    color: red;
                                }
                            </style> <?php }?>
                        <a class="like" href="../../like.php?id_art=<?= $ID ?>&id_user=<?= $data['id'] ?>&type=2"><i class="fas fa-heart"></i></a>
                    <?php } else {?> <a href=""><i class="fas fa-heart"></i></a><?php }?>
                    <a href="../<?= $result['category']?>.php"><i class="fas fa-sign-out-alt"></i></a>
                </div>
                <?php
                    echo '<h1>'. $result['nom']. '</h1>';
                    echo '<img src="media' . $result['id'] . '.'. $result['type'] . '">';
                    echo '<p id="description">'. $result['description']. '</p>';
                ?>
            </div>
        </div>
        <section class="commentaires" id="section-commentaire">
            <h3>Commentaires:</h3>
            <div class="comment">
                <?php
                $comment = $bdd->prepare('SELECT * FROM commentaire WHERE article_id = (?) AND type = 2');
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
                                    echo "<a href=\"../../  comment.php?id= " . $row['id'] . "&ida=" . $row['article_id'] . "\" \"btn-supprimer\" id=\"btnSupprimer\"><i class=\"fas fa-times\"></i></a>";
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
            <form action="../../comment.php?id=<?= $ID?>&type=2" method="post">
                <p>Écrivez vôtre commentaire ici :</p>
                <div class="form-comment">
                    <textarea name="comment" id="multilineInput" rows="4" cols="50" placeholder="Soyez inspiré..."></textarea>
                </div>
                <div class="form-group">
                    <button type="submit">Envoyer le commentaire</button>
                </div>
            </form>
        </section>
    </main>
    <?php
} else {
    catchError('media.php', '../../', 'Données incorrectes', ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=data');
    exit();
}
$path = '../../';
require_once '../../canva/footer.php';
echo echofooter($path);
