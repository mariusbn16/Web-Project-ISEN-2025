<?php

    require_once('database.php');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $db = dbConnect();
    if(!$db) {
        header('HTTP/1.1 503 Service Unavailable');
        exit;
    }
    // REcuperation de request
    $request = $_GET['request'] ?? null;

    // si request = get-data
    if($request == 'get-data'){
        $mmsi = $_GET['mmsi'] ?? null;

        // appel de la fonction qui recupère les prédicitons
        if($mmsi!=null){$results = dbPredictType($db, $mmsi);} 

        if($results==false){
            error_log('Erreur récupération des données.');
            exit();
        }
        // renvoi les données e JSON
        header('Content-Type: application/json;charset=utf8');
                    header('Cache-control: no-store, no-cache, must-revalidate');
                    header('Pragma: no-cache');
                    header('HTTP/1.1 200 OK');        
                    echo json_encode($results);
    }

    // si requet == predict-type
    if($request == 'predict-type'){
        $Length = $_GET['Length']; $Width = $_GET['Width']; $Draft = $_GET['Draft']; $SOG = $_GET['SOG']; $COG = $_GET['COG']; 
        $Etat = $_GET['Etat'];

        // récupération des données de la requete
        $data_argument = $_GET['Length'] . ' ' . $_GET['Width'] . ' ' . $_GET['Draft'] . ' ' . $_GET['SOG'] . ' ' . $_GET['COG'] . ' ' . $_GET['Etat'];

        // préparation de la comandes et des variables pour l'execution des scipt python
        $command = "python3 ../script/script.py " . $data_argument;
        $output;
        $return_var;

        // execution script python
        $res = exec($command, $output, $return_var);

        // envoi du resultat en json
        header('Content-Type: application/json;charset=utf8');
                    header('Cache-control: no-store, no-cache, must-revalidate');
                    header('Pragma: no-cache');
                    header('HTTP/1.1 200 OK');        
                    echo json_encode($output); 
    }

?>