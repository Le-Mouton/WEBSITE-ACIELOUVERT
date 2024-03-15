<!DOCTYPE html>
<html lang="fr">
<?php
$path = "../";
require_once "../canva/header.php";
echo echoheader($path);
?>
<main>
    <div class="legal-box">
        <h2>Droit de reproduction</h2>
        <p>Toutes nos images sont sous <span class="bold"> licence Creative Commons</span> et obéissent donc aux conditions spécifiques de leur licence.</p>
        <div class="legal-box-img"><img src="../img/cc.webp" alt=""><p>Ce pictogramme signifie que nos images sont sous licence Creative Commons, que les reproductions d’œuvres sous cette licence devront citer l’auteur, avoir un but non commercial et être partagées en conservant la même licence.</p></div>
        <p class="bold">Pour plus d’informations, suivez ce lien : <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/">https://creativecommons.org/licenses/by-nc-sa/4.0/</a></p>
    </div>
</main>
<?php
$path = "../";
require_once "../canva/footer.php";
echo echofooter($path);
?>

</html>