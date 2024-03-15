<?php

//TODO: log editable depuis la log-page et tri

require_once "../config.php";
session_start();
$path = "../";

require_once "../canva/header.php";
echo echoheader($path);

?>
<main>
    <div class="log-view">
        <p class="titre">LOG CONTENU:</p>
        <div class="scroll">
        <?php
        $filePath = "log/log.txt";

        if (file_exists($filePath)) {
            $lines = file($filePath);

            $lines = array_reverse($lines);

            foreach ($lines as $line) {
                echo "<span>" . htmlspecialchars($line) . "</span><br>";
            }
        }
        ?>
        </div>
    </div>
    <div class="log-view">
        <p class="titre">LOG ERREUR:</p>
        <div class="scroll">
            <?php
            $filePath = "log/error.txt";

            if (file_exists($filePath)) {
                $lines = file($filePath);

                $lines = array_reverse($lines);

                foreach ($lines as $line) {
                    echo "<span>" . htmlspecialchars($line) . "</span><br>";
                }
            }
            ?>
        </div>
    </div>
</main>
<?php
require_once "../canva/footer.php";
echo echofooter($path);
?>