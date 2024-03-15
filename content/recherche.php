<?php
require_once "../config.php";

function contenuSimilaire($auteur, $titre, $tag)
{

    $tagQuery = $bdd->prepare("SELECT * FROM article WHERE tag LIKE '%$tag%'");
    $resultTag = $tagQuery->execute(array());
    $auteurQuery = $bdd->prepare("SELECT * FROM article WHERE autor LIKE '%$auteur%'");
    $resultAuteur = $auteurQuery->execute(array());
    $titreQuery = $bdd->prepare("SELECT * FROM article WHERE titre LIKE '%$titre%'");
    $resultTitre = $titreQuery->execute(array());

    $contenuSimilaire = array();

    while ($rowTag = $resultTag->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($contenuSimilaire[$rowTag['id']])) {
            $contenuSimilaire[$rowTag['id']] = 0;
        }
        $contenuSimilaire[$rowTag['id']] += 1;
    }
    while ($rowAuteur = $resultAuteur->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($contenuSimilaire[$rowAuteur['id']])) {
            $contenuSimilaire[$rowAuteur['id']] = 0;
        }
        $contenuSimilaire[$rowAuteur['id']] += 1;
    }
    while ($rowTitre = $resultTitre->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($contenuSimilaire[$rowTitre['id']])) {
            $contenuSimilaire[$rowTitre['id']] = 0;
        }
        $contenuSimilaire[$rowTitre['id']] += 1;
    }
    arsort($contenuSimilaire);
    $topID = array_keys(array_slice($contenuSimilaire, 0, 5));

    return $topID;
}