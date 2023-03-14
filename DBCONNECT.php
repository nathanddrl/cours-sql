<?php
function dbConnect()
{
    $dbType = 'mysql';
    $dbHost = 'localhost';
    $dbName = 'courssql';
    $dbUser = 'root';
    $dbPass = '';
    try {
        $db = new PDO($dbType.':host='.$dbHost.';dbname='.$dbName, $dbUser, $dbPass);
        //attributes PDO (pour afficher les erreurs et les exceptions et gérer l'encodage des caractères)
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->exec('SET NAMES utf8'); 
        return $db;
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}
?>
