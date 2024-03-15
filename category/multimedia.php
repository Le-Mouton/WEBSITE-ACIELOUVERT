<!DOCTYPE html>
<html lang="fr">
<?php
$path = "../";
require_once "../canva/header.php";
echo echoheader($path);
?>
<main>
<figure class="background">
    <div class="title"><h1>MULTIMEDIA</h1></div>
    <div class="box-part">
        <a href="../video/content/video.php" class="box-part-button" id="video">
            <p>Vidéos</p>
        </a>
        <a href="https://app.emaze.com/@ALOFCLFIR/expo-virtuelle" class="box-part-button" id="expo">
            <p>Exposition Virtuelle</p>
        </a>
        <a href="photociel.php" class="box-part-button" id="photociel">
            <p>Photo du ciel</p>
        </a>
        <a href="interviews.php" class="box-part-button" id="interview">
            <p>Interviews</p>
        </a>
    </div>
</figure>
</main>
<?php
$path = "../";
require_once "../canva/footer.php";
echo echofooter($path);
?>

</html>