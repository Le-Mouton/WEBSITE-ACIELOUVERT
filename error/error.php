<?php
function catchError($file, $path, $error, $pseudo='', $ip='', $fromID = '', $toID = '')
{
    $time = date("Y-m-d H:i:s");
    if(empty($pseudo)){
        error_log($time . " :: " . $file . "::" . $error . ' de ' . ' ' . $ip . "\n", 3, "{$path}error/log/error.txt");
    }
    else{
        if(empty($toID)){
            if(empty($fromID)){
                error_log($time . " :: "  . $file . "::" . $error . ' par ' . $pseudo . ' ' . $ip . "\n", 3, "{$path}error/log/error.txt");
            }else{
                error_log($time . " :: "  . $file . "::" . $error . ' par ' . $pseudo . ' ' . $ip . ' :: ID du contenu : ' . $fromID . "\n", 3, "{$path}error/log/error.txt");
            }
        } else {
            error_log($time . " :: "  . $file . "::" . $error . ' par ' . $pseudo . ' ' . $ip . " :: ID de l'élément " . $fromID . " ajouté à " . $toID . "\n", 3, "{$path}error/log/error.txt");
        }
    }
}
//TODO: file source de la log
function updateLog($path, $update, $pseudo, $ip='', $fromID = '', $toID = ''){
    $time = date("Y-m-d H:i:s");
    if(empty($toID)){
        if(empty($fromID)){
            error_log($time . " :: " . $update . ' par ' . $pseudo . ' ' . $ip . "\n", 3, "{$path}error/log/log.txt");
        }else{
            error_log($time . " :: " . $update . ' par ' . $pseudo . ' ' . $ip . ' :: ID du contenu : ' . $fromID . "\n", 3, "{$path}error/log/log.txt");
        }
    } else {
        error_log($time . " :: " . $update . ' par ' . $pseudo . ' ' . $ip . " :: ID de l'élément " . $fromID . " ajouté à " . $toID . "\n", 3, "{$path}error/log/log.txt");
    }
}
//TODO: fonction notification news sur le site et par mail si accepté
