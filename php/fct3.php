<?php
    // Fichier PHP qui fournit les données des navires en JSON
    // Il utilisera dbGetVessels() pour récupérer les données et les renverra en JSON 

    require_once('database.php');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $db = dbConnect();
    if(!$db) {
        header('HTTP/1.1 503 Service Unavailable');
        exit;
    }

    // Récupérer les données des navires
    $results = dbGetVessels($db);
    if($results==false){
        error_log('Erreur récupération des données.');
        exit();
    }

    // Renvoyer les données en JSON
    header('Content-Type: application/json;charset=utf8');
                header('Cache-control: no-store, no-cache, must-revalidate');
                header('Pragma: no-cache');
                header('HTTP/1.1 200 OK');        
                echo json_encode($results);

?>
