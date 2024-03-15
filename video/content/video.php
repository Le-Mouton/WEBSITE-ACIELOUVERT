<?php
$path = "../../";
require_once "../../canva/header.php";
require_once "../../error/error.php";
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
            <div class="title" style="margin-left: 5%">
                <h1 id="partner-color">Nos vidéos:</h1>
            </div>

            <div class="video-list">
                <?php
                $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                $articlesPerPage = 9;
                $offset = ($page - 1) * $articlesPerPage;
                if(isset($data)){
                    if($data['permissions'] == 2){
                        $recherche = $bdd->prepare("SELECT * FROM video ORDER BY ABS(TIMESTAMPDIFF(SECOND, date, NOW())) ASC LIMIT $articlesPerPage OFFSET $offset");
                        $recherche->execute(array());
                    } elseif($data['pseudo']){
                        $recherche = $bdd->prepare("SELECT * FROM video WHERE (date <= NOW() OR autor = :currentUserId) ORDER BY ABS(TIMESTAMPDIFF(SECOND, date, NOW())) ASC LIMIT 9 OFFSET $offset");
                        $recherche->execute(array('currentUserId'=>$data['pseudo']));
                    }
                } else {
                    $recherche = $bdd->prepare("SELECT * FROM video WHERE date <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date, NOW())) ASC LIMIT 9 OFFSET $offset");
                    $recherche->execute(array());
                }
                while ($result = $recherche->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <div class="video-frame">
                            <iframe src="https://www.youtube.com/embed/<?= $result['link'] ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            <div class="titre"><p><?= $result['titre'] ?></p></div>
                            <p><?= $result['description']?></p>
                            <div class="video-frame-more">
                                <?php
                                if(!empty($result['article'])){
                                ?>
                                <a href="../../articles/content/article.php?id=<?= $result['article']?>">Voir l'article associé</a>
                                <?php }?>
                                <p><?= $result['autor']?></p>
                            </div>
                        </div>
                    <?php
                }
                ?>
            </div>
            <?php
            if(isset($data)){
                if($data['permissions'] == 2){
                    $statement = $bdd->prepare("SELECT * FROM video ORDER BY ABS(TIMESTAMPDIFF(SECOND, date, NOW()))");
                    $statement->execute(array());
                } elseif($data['pseudo']){
                    $statement = $bdd->prepare("SELECT * FROM video WHERE (date <= NOW() OR creator_id = :currentUserId) ORDER BY ABS(TIMESTAMPDIFF(SECOND, date, NOW()))");
                    $statement->execute(array('currentUserId'=>$data['pseudo']));
                }
            } else {
                $statement = $bdd->prepare("SELECT * FROM video WHERE date <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date, NOW()))");
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
    </section>
</main>
<?php
$path = "../../";
require_once "../../canva/footer.php";
echo echofooter($path);
?>