<?php
function echofooter($path){
    $footer = "
    <footer>
        <div class=\"row\"><img src=\"{$path}img/logo.png\" alt=\"logo\">
        <p>Un site réalisé par des étudiants en formation d'ingénieur dans le domaine aéronautique et spatial à l'IPSA dans le cadre du Grand Projet AERO 1 et 2.</p>
        </div>
        <div class=\"row\">
            <a href=\"{$path}category/mentionslegales.php\">Mentions légales</a>
            <a href=\"{$path}category/droit.php\">Droit de production</a>
            <a href=\"{$path}category/cookies.php\">Utilisation de cookies</a>
            <a href=\"{$path}category/confidentialite.php\">Confidentialités</a>
        </div>
        
        <div class=\"row\">Copyright © 2023 IPSA Toulouse - Tous droits réservés</div>
        
        </footer>
        </body>";
    return $footer;
}


?>
