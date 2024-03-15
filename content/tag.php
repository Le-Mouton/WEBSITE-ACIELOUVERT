<?php
require_once '../config.php';
require_once '../error/error.php';

session_start();
require_once '../config.php'; // ajout connexion bdd
if(isset($_SESSION['user'])){
    $req = $bdd->prepare('SELECT * FROM users WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();

} else {
    catchError('tag.php','../', 'Erreur de connexion', ip: $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=connexion');
    exit();
}

if($data['permissions'] == '2'){
if(isset($_GET['type'])) {
    if ($_GET['type'] = 'article') {
        if (isset($_POST['selectedArticleDelete'], $_POST['tagSelectionDelete'])) {
            $selectedArticleIDDelete = htmlspecialchars($_POST['selectedArticleDelete']);
            $tagSelectionDelete = htmlspecialchars($_POST['tagSelectionDelete']);

            $deleteTagId = "," . intval($tagSelectionDelete);
            $deleteTagQuery = $bdd->prepare('UPDATE article SET tag = REPLACE(tag, :deleteTagId, "") WHERE id = :selectedArticleIDDelete');
            $deleteTagQuery->execute(array('deleteTagId' => $deleteTagId, 'selectedArticleIDDelete' => $selectedArticleIDDelete));
        } elseif (isset($_POST['selecttags'], $_POST['selectedArticle'])) {

            $tag = htmlspecialchars($_POST['selecttags']);

            $selectedArticleID = htmlspecialchars($_POST['selectedArticle']);

            $searchArticle = $bdd->prepare('SELECT * FROM article WHERE id = :id');
            $searchArticle->execute(array('id' => $selectedArticleID));
            $resultArticle = $searchArticle->fetch(PDO::FETCH_ASSOC);

            $searchTag = $bdd->prepare('SELECT * FROM tag WHERE nom = :tag');
            $searchTag->execute(array('tag' => $tag));
            $resultTag = $searchTag->fetch(PDO::FETCH_ASSOC);

            if ($resultTag != '') {
                // Le tag existe déjà, vérifions s'il est déjà associé à l'article
                $tagId = $resultTag['id'];
                if ($resultArticle['tag'] != '') {
                    $existingTags = explode(',', $resultArticle['tag']);
                } else {
                    $existingTags = [];
                }

                if (!in_array($tagId, $existingTags)) {
                    $newTagString = $resultArticle['tag'] . ',' . $tagId;
                    $addtag = $bdd->prepare('UPDATE article SET tag = :tag WHERE id = :id');
                    $addtag->execute(array('tag' => $newTagString, 'id' => $selectedArticleID));
                    updateLog('../', "Ajout d'un tag", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $tagId, toID: $selectedArticleID);
                }
            } else {
                    $tag = htmlspecialchars($_POST['tagsA']);
                    // Le tag n'existe pas, créons-le et l'associons à l'article
                    $createtag = $bdd->prepare('INSERT INTO tag(nom) VALUES (:nom)');
                    $createtag->execute(array('nom' => $tag));
                    updateLog('../',"Création d'un tag", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $bdd->lastInsertId());

                    $int = $bdd->lastInsertId();
                    $newTagString = ($resultArticle['tag'] !== '') ? $resultArticle['tag'] . ',' . $int : $int;

                    $addtag = $bdd->prepare('UPDATE article SET tag = :tag WHERE id = :id');
                    $addtag->execute(array('tag' => $newTagString, 'id' => $selectedArticleID));
                    updateLog('../',"Ajout d'un tag", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $int, toID: $selectedArticleID);
                }

            } else {
                catchError('tag.php','../', "Données incorrectes", $data['pseudo'], $_SERVER['REMOTE_ADDR']);
                header('Location: ../index.php?update=data');
                exit();
            }
    } elseif ($_GET['type'] = 'media') {
            if (isset($_POST['selectedMediaDelete'], $_POST['tagSelectionDelete'])) {
                $selectedMediaIDDelete = htmlspecialchars($_POST['selectedMediaDelete']);
                $tagSelectionDelete = htmlspecialchars($_POST['tagSelectionDelete']);

                $deleteTagId = "," . intval($tagSelectionDelete);
                $deleteTagQuery = $bdd->prepare('UPDATE media SET tag = REPLACE(tag, :deleteTagId, "") WHERE id = :selectedMediaIDDelete');
                $deleteTagQuery->execute(array('deleteTagId' => $deleteTagId, 'selectedMediaIDDelete' => $selectedMediaIDDelete));
            } elseif (isset($_POST['selecttags'], $_POST['selectedMedia'])) {
                $selectedMediaID = htmlspecialchars($_POST['selectedMedia']);
                $tag = htmlspecialchars($_POST['selecttags']);

                $searchMedia = $bdd->prepare('SELECT * FROM media WHERE id = :id');
                $searchMedia->execute(array('id' => $selectedMediaID));
                $resultMedia = $searchMedia->fetch(PDO::FETCH_ASSOC);

                $searchTag = $bdd->prepare('SELECT * FROM tag WHERE nom = :tag');
                $searchTag->execute(array('tag' => $tag));
                $resultTag = $searchTag->fetch(PDO::FETCH_ASSOC);

                if ($resultTag != '' ) {
                    $tagId = $resultTag['id'];
                    if ($resultMedia['tag'] != '') {
                        $existingTags = explode(',', $resultMedia['tag']);
                    } else {
                        $existingTags = [];
                    }
                    if (!in_array($tagId, $existingTags)) {
                        $newTagString = $resultMedia['tag'] . ',' . $tagId;
                        $addtag = $bdd->prepare('UPDATE media SET tag = :tag WHERE id = :id');
                        $addtag->execute(array('tag' => $newTagString, 'id' => $selectedMediaID));
                        updateLog('../',"Ajout d'un tag", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $tagId, toID: $selectedMediaID);
                    }
                } else {
                    $tag = htmlspecialchars($_POST['tagsM']);
                    // Le tag n'existe pas, créons-le et l'associons à l'article
                    $createtag = $bdd->prepare('INSERT INTO tag(nom) VALUES (:nom)');
                    $createtag->execute(array('nom' => $tag));
                    updateLog('../',"Création d'un tag", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $bdd->lastInsertId());

                    $int = $bdd->lastInsertId();
                    $newTagString = ($resultMedia['tag'] !== '') ? $resultMedia['tag'] . ',' . $int : $int;

                    $addtag = $bdd->prepare('UPDATE media SET tag = :tag WHERE id = :id');
                    $addtag->execute(array('tag' => $newTagString, 'id' => $selectedMediaID));
                    updateLog('../',"Ajout d'un tag", $data['pseudo'], $_SERVER['REMOTE_ADDR'], fromID: $int, toID: $selectedMediaID);
                }
            } else {
                catchError('tag.php','../', "Données incorrectes", $data['pseudo'], $_SERVER['REMOTE_ADDR']);
                header('Location: ../index.php?update=data');
                exit();
            }
        } else {
            catchError('tag.php','../', "Type de contenu inconnu", $data['pseudo'], $_SERVER['REMOTE_ADDR']);
            header('Location: ../index.php?update=type');
            exit();
    }
}
// Récupération des articles existants
$articlesQuery = $bdd->query('SELECT nom, id FROM article');
$existingArticles = $articlesQuery->fetchAll(PDO::FETCH_ASSOC);

$mediaQuery = $bdd->query('SELECT nom, id FROM media');
$existingMedia = $mediaQuery->fetchAll(PDO::FETCH_ASSOC);

$tagsQuery = $bdd->query('SELECT id, nom FROM tag');
$allTags = $tagsQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
$path = "../";
require_once "../canva/header.php";
echo echoheader($path);
?>
<main>
    <div class="form-group" id="select-form-group">
        <select class="content-select" name="content" id="content-select">
            <option value="">Modifier des tags</option>
            <option value="article">Article</option>
            <option value="media">Media</option>
        </select>
        <span id="alert-mobile">Attention il est conseillé de faire les modifications sur ordinateur!</span>
    </div>
    <div class="drop-area" id="article" style="display: none">
        <form action="tag.php?type=article" method="post" id="tagForm">
            <p id="aero-color">Ajouter des tags :</p>
            <div class="form-group">
                <select id="selectedArticle" name="selectedArticle">
                    <option value="">Séléctionner un article</option>
                    <?php foreach ($existingArticles as $article) : ?>
                        <option value="<?php echo htmlspecialchars($article['id']); ?>"><?php echo htmlspecialchars($article['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <select id="tagSelection1" name="selecttags">
                    <option value="">Séléctionner un tag</option>
                    <option value="createNew">Créer un nouveau tag</option>
                    <?php foreach ($allTags as $tag) : ?>
                        <option value="<?php echo htmlspecialchars($tag['nom']); ?>"><?php echo htmlspecialchars($tag['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <script>
                document.getElementById('tagSelection1').addEventListener('change', function () {
                    var newTagInput = document.getElementById('newTagInput1');
                    newTagInput.style.display = (this.value === 'createNew') ? 'block' : 'none';
                });
            </script>
            <div class="form-group" id="newTagInput1" style="display: none;">
                <label for="tagsA">Ou créez un nouveau tag :</label>
                <input type="text" id="tagsA" name="tagsA">
            </div>
            <button id="aero-color" type="submit">Ajouter les tags</button>
        </form>

        <form action="tag.php?type=media" method="post" id="deleteTagForm">
            <p id="aero-color">Supprimer des tags :</p>
            <div class="form-group">
                <select id="selectedArticleDelete" name="selectedArticleDelete">
                    <option value="">Sélectionner un article</option>
                    <?php foreach ($existingArticles as $article) : ?>
                        <option value="<?php echo htmlspecialchars($article['id']); ?>"><?php echo htmlspecialchars($article['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <select id="tagSelectionDelete" name="tagSelectionDelete">
                    <option value="">Sélectionner un tag</option>
                    <?php foreach ($allTags as $tag) : ?>
                        <option value="<?php echo htmlspecialchars($tag['id']); ?>"><?php echo htmlspecialchars($tag['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button id="aero-color" name="deleteTag">Supprimer le tag</button>
        </form>
    </div>

    <div class="drop-area" id="media" style="display: none">
        <form action="tag.php?type=media" method="post" id="tagForm">
            <p id="spatial-color">Ajouter des tags :</p>
            <div class="form-group">
                <select id="selectedMedia" name="selectedMedia">
                    <option value="">Séléctionner un media</option>
                    <?php foreach ($existingMedia as $media) : ?>
                        <option value="<?php echo htmlspecialchars($media['id']); ?>"><?php echo htmlspecialchars($media['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <select id="tagSelection2" name="selecttags">
                    <option value="">Séléctionner un tag</option>
                    <option value="createNew">Créer un nouveau tag</option>
                    <?php foreach ($allTags as $tag) : ?>
                        <option value="<?php echo htmlspecialchars($tag['nom']); ?>"><?php echo htmlspecialchars($tag['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <script>
                document.getElementById('tagSelection2').addEventListener('change', function () {
                    var newTagInput = document.getElementById('newTagInput2');
                    newTagInput.style.display = (this.value === 'createNew') ? 'block' : 'none';
                });
            </script>
            <div class="form-group" id="newTagInput2" style="display: none;">
                <label for="tagsM">Ou créez un nouveau tag :</label>
                <input type="text" id="tagsM" name="tagsM">
            </div>
            <button id="spatial-color" type="submit">Ajouter les tags</button>
        </form>
        <form action="tag.php?type=media" method="post" id="deleteTagForm">
            <p id="spatial-color">Supprimer des tags :</p>
            <div class="form-group">
                <select id="selectedMediaDelete" name="selectedMediaDelete">
                    <option value="">Sélectionner un media</option>
                    <?php foreach ($existingMedia as $media) : ?>
                        <option value="<?php echo htmlspecialchars($media['id']); ?>"><?php echo htmlspecialchars($media["nom"]); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <select id="tagSelectionDelete" name="tagSelectionDelete">
                    <option value="">Sélectionner un tag</option>
                    <?php foreach ($allTags as $tag) : ?>
                        <option value="<?php echo htmlspecialchars($tag['id']); ?>"><?php echo htmlspecialchars($tag['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button id="spatial-color" name="deleteTag">Supprimer le tag</button>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var contentSelect = document.getElementById('content-select');
            var articleDiv = document.getElementById('article');
            var mediaDiv = document.getElementById('media');

            contentSelect.addEventListener('change', function () {
                articleDiv.style.display = 'none';
                mediaDiv.style.display = 'none';

                var selectedValue = contentSelect.value;
                if (selectedValue === 'article') {
                    articleDiv.style.display = 'block';
                } else if (selectedValue === 'media') {
                    mediaDiv.style.display = 'block';
                }
            });
        });
    </script>
</main>
<?php
$path = "../";
require_once "../canva/footer.php";
echo echofooter($path);
} else {
    catchError('tag.php', '../', "Accès refusé", $data['pseudo'], $_SERVER['REMOTE_ADDR']);
    header('Location: ../index.php?update=access');
    exit();
}