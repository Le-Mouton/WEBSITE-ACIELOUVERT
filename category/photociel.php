
<?php
$path = "../";
require_once "../canva/header.php";
echo echoheader($path);
?>
<main>
    <section class="photoduciel-container">
        <div class="title">
            <h1>LA PHOTO <br><span id="photo-color">DU CIEL</span></h1>
        </div>
        <a href="../media/spotting.php" class="btn-photociel" id="spo-aero">
            <div class="title"><h2>SPOTTING</h2></div>
        </a>
        <a href="../media/astrophoto.php" class="btn-photociel" id="spo-spatial">
            <div class="title"><h2>ASTROPHOTO</h2></div>
        </a>
    </section>
</main>
<?php
$path = "../";
require_once "../canva/footer.php";
echo echofooter($path);
?>
