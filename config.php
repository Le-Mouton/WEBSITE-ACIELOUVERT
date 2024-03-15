<?php

try
{
    $bdd = new PDO("mysql:host=ADRESS;dbname=NAME;charset=utf8", "USERNAME", "PASSWORD");

}
catch(PDOException $e)
{
    catchError('config.php', '', 'Connexion interrompue: ' . $e . ' :: ' , ip: $_SERVER['REMOTE_ADDR']);
    die();
}

function ServerConnection(){
    $ftpServer = 'SERVER ADRESS';
    $ftpUsername = 'USERNAME';
    $ftpPassword = 'PASSWORD';
    $ftpConnection = ftp_connect($ftpServer);
    $ftplogin = ftp_login($ftpConnection, $ftpUsername, $ftpPassword);
    ftp_pasv($ftpConnection, true);

    return $ftpConnection;
}