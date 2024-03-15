<?php
require_once 'config.php';

if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $query = $bdd->prepare("SELECT * FROM articles WHERE nom LIKE '%$search%' LIMIT 5") ;
    $query->execute();
    echo '<ul>';
        while ($result = $query->fetchAll(PDO::FETCH_ASSOC)) {

            echo "<li onclick='fill(" . $result['nom'] . ")'>
                <a>
                     ". $result['nom'] ."
            </li></a> ";
        }
    echo "</ul>";
}