<?php
function echoheader($path){

    global $bdd;

    session_start();

    require_once "{$path}config.php";
    if(isset($_SESSION['user'])){
        $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
        $req->execute(array($_SESSION['user']));
        $data = $req->fetch();
    }
    $header= "
<!DOCTYPE html>
<html lang=\"fr\">
        <head>
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
            <meta http-equiv=\"Content-Type\" content=\"text/html; charset=us-ascii\">
            <meta name=\"language\" content=\"French\">
            <link rel=\"stylesheet\" href=\"{$path}styles.css\">
            <link rel=\"icon\" href=\"{$path}img/icon.ico\" />
            <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css\">
            <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
            <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
            <link href=\"https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;500;700&display=swap\" rel=\"stylesheet\">
            <title>ACielOuvert</title>
        </head>
        
        <body>
        <header>
            <nav>
                <div class=\"row\" id=\"row1\">
                    <ul id='more'>
                        <div class='more-menu'>
                                <div class='barre'></div>
                                <div class='barre'></div>
                                <div class='barre'></div>
                        </div>
                        <div class='box-btn-row1'>
                            <ul id='btn-row'>
                                <a href=\"{$path}category/partenariats.php\">Partenariats</a>
                            </ul>
                            <ul id='btn-row'>
                                <a href=\"{$path}category/interviews.php\">Interviews</a>
                            </ul>
                            <ul id='btn-row'>
                                <a href=\"{$path}category/photociel.php\">La Photo du ciel</a>
                            </ul>
                            <ul id='btn-row'>
                                <a href=\"{$path}articles/list_articles.php\">Tous les articles</a>
                            </ul>
                            <ul class=\"search-container\"  id='btn-row'>
                                <input type=\"text\" id=\"search-header\" oninput=\"getSearchResults(this.value)\" placeholder=\"Rechercher...\">
                                <div id=\"searchResults\" class=\"search-results\"></div>
                            </ul> 
                        </div>
                    </ul>
                    <ul id='icon'>
                        <a href='https://twitter.com/acielouvertipsa?lang=fr'><i class=\"fab fa-twitter\"></i></a>
                    </ul>
                    <ul id='icon'>
                        <a href='https://www.instagram.com/a_ciel_ouvert_ipsa/?hl=fr'><i class=\"fab fa-instagram\"></i></a>
                    </ul>
                    <ul id='icon'>
                        <a href='https://www.youtube.com/channel/UCNQc9adb66MzajfP75lKfXg'><i class=\"fab fa-youtube\"></i></a>
                    </ul>
                    <ul id='icon'>
                        <a href='https://www.tiktok.com/@a_ciel_ouvert'><i class=\"fab fa-tiktok\"></i></a>
                    </ul>
                    <ul id='btn-row1'>
                        <a href=\"{$path}category/partenariats.php\">Partenariats</a>
                    </ul>
                    <ul id='btn-row1'>
                        <a href=\"{$path}category/interviews.php\">Interviews</a>
                    </ul>
                    <ul id='btn-row1'>
                        <a href=\"{$path}category/photociel.php\">La Photo du ciel</a>
                    </ul>
                    <ul id='btn-row1'>
                        <a href=\"{$path}articles/list_articles.php\">Tous les articles</a>
                    </ul>
                    <ul class=\"\"  id='btn-row1'>
                        <div class=\"search-header\">
                          <input type=\"input\" class=\"search-header-bar\" placeholder=\"Rechercher\" name=\"search-header\" id='search-header' required />
                          <label for=\"search-header\" class=\"search-header-bar-label\">Rechercher...</label>
                        </div>
                    </ul>";
                if (!isset($_SESSION['user'])) {
                    $header .= "<ul class='dropdown-content' id=\"account\">
                        <a href=\"{$path}auth/login.php\">Connexion/Inscription</a>
                        </ul>";
                } else {
                    $header .= "<ul class='dropdown-content'>
                        <a href=\"\" id=\"account\"><p>" . $data['pseudo'] . "</p></a>
                        <li class=\"dropdown\">
                            <a href=\"{$path}auth/deconnexion.php\">Déconnexion</a>
                            <a href=\"{$path}auth/profile.php\">Profil</a>
                        ";

                    if ($data['permissions'] == '1') {
                        $header .= "
                            <a href=\"{$path}content/new-content.php\">Ajouter du contenu</a>
                            <a href=\"{$path}content/edit.php\">Modifier du contenu</a>
                            </li>";
                    } elseif ($data['permissions'] == '2'){
                        $header .= "
                            <a href=\"{$path}content/new-content.php\">Ajouter du contenu</a>
                            <a href=\"{$path}error/log-page.php\">Logs</a>
                            <a href=\"{$path}content/tag.php\">Modifier des tags</a>
                            <a href=\"{$path}content/edit.php\">Modifier du contenu</a>
                            </li>";
                    } else{
                        $header .="</li>";
                    }
                    $header .= "</ul>";
                }

                $header .= "
                </div> 
                <a id='middle-logo' href='{$path}index.php' class='logo'><img src='{$path}img/gif.gif' id='gif'><img id='img-logo' src=\"{$path}img/logo.png\" alt=\"logo\"></a>             
                <div class=\"row\" id=\"row2\">
                    <ul id=\"logo\">
                        <a href='{$path}index.php' class='logo'><img src='{$path}img/gif.gif' id='gif'><img src=\"{$path}img/logo.png\" alt=\"logo\"></a>          
                    </ul>
                    <ul id=\"jaune\">
                        <a href=\"{$path}category/aeronautique.php\">Aéronautique</a>
                        <li>
                        <div class='color-bar' id='aero-color'></div>
                        <a id='jaune' href='{$path}category/aeronautique.php?type=actualite'>Actualité</a>
                        <a id='jaune' href='{$path}category/aeronautique.php?type=technologie'>Technologie</a>
                        <a id='jaune' href='{$path}category/aeronautique.php?type=histoire'>Histoire</a>
                        <a id='jaune' href='{$path}category/aeronautique.php?type=creation'>Création</a>
                        <a id='jaune' href='{$path}category/aeronautique.php?type=quiz'>Quiz</a></li>
                    </ul>
                    <ul id=\"rouge\">
                        <a href=\"{$path}category/spatial.php\">Spatial</a>
                        <li>
                        <div class='color-bar' id='spatial-color'></div>
                        <a id='rouge' href='{$path}category/spatial.php?type='>Actualité</a>
                        <a id='rouge' href='{$path}category/spatial.php?type=technologie'>Technologie</a>
                        <a id='rouge' href='{$path}category/spatial.php?type=histoire'>Histoire</a>
                        <a id='rouge' href='{$path}category/spatial.php?type=creation'>Création</a>
                        <a id='rouge' href='{$path}category/spatial.php?type=quiz'>Quiz</a></li>
                    </ul>
                    <ul id=\"violet\">
                        <a href=\"{$path}category/multimedia.php\">Multimédia</a>
                        <li>
                        <div class='color-bar' id='multi-color'></div>
                        <a id='violet' href='{$path}category/interviews.php'>Interviews</a>
                        <a id='violet' href='{$path}category/photociel.php'>La Photo du ciel</a>
                        <a id='violet' href='https://app.emaze.com/@ALOFCLFIR/expo-virtuelle'>Expo virtuelle</a>
                        <a id='violet' href='{$path}video/content/video.php'>Vidéos</a>
                    </ul>
                    <ul id=\"vert\">
                        <a href=\"{$path}category/atmosvert.php\">AtmosVert</a>
                    </ul>     
                    <ul id=\"bleu\">
                        <a href=\"{$path}category/projet.php\">Projets</a>
                    </ul>   
                </div>
            </nav>
            </header>";
    return $header;
    }

?>
