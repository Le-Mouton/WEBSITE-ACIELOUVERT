<?php
$path = "../";
require_once "../canva/header.php";
echo echoheader($path);
if(isset($_SESSION['user'])){
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();
}

$category = 'spotting';
if(isset($data)){
    if($data['permissions'] == 2){
        $statement = $bdd->prepare("SELECT * FROM media WHERE category LIKE :category ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC");
        $statement->execute(array('category'=>$category));
    } elseif($data['pseudo']){
        $statement = $bdd->prepare("SELECT * FROM media WHERE category LIKE :category AND (date_publication <= NOW() OR autor = :autor) ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC");
        $statement->execute(array('autor'=>$data['pseudo'], 'category'=>$category));
    }
} else {
    $statement = $bdd->prepare("SELECT * FROM media WHERE category LIKE :category AND date_publication <= NOW() ORDER BY ABS(TIMESTAMPDIFF(SECOND, date_publication, NOW())) ASC");
    $statement->execute(array('category'=>$category));
}

?>
<main>
    <section class="media-container">
      <h1>SPOTTING</h1>
        <div class="all-media" id="all-media">
            <?php

            foreach ($statement as $media){
                ?>
                <a class="btn-img-media" href="content/media.php?id=<?= $media['id']?>"><img src="content/media<?= $media['id']?>.<?= $media['type']?>" alt="<?= $media['nom'] ?>"></a>
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
