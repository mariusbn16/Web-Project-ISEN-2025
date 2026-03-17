<?php 

    require_once('database.php');


    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // test lorsqu'il y a des erreurs
    error_log("GET: " . var_export($_GET, true));
    error_log("POST: " . var_export($_POST, true));
    error_log("REQUEST: " . var_export($_REQUEST, true));

    // connection bdd
    $db = dbConnect();
    if(!$db) {
        header('HTTP/1.1 503 Service Unavailable');
        exit;
    }


    $request = $_GET['request'] ?? null;

    // si request = add-data
    if ($request == 'add-data') {

        // recuperation des données de la requete
        $fields = ['cog', 'date', 'draft', 'heading', 'lat', 'length', 'lon', 'mmsi', 'sog', 'etat', 'time', 'vessel_name', 'width'];
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $_POST[$field] ?? null;
        }

        // appel fonction qui ajoute les données à la bdd
        dbAddData($db, $data);
        }
    else{
        error_log("echec add-data");
        error_log("Échec add-data : valeur request = " . var_export($request, true));
    }

    // si request = etat => pour recuperer les valeurs etat
    if($request == 'etat'){
        $data = dbGetEtat($db);
        header('Content-Type: application/json;charset=utf8');
                header('Cache-control: no-store, no-cache, must-revalidate');
                header('Pragma: no-cache');
                header('HTTP/1.1 200 OK');        
                echo json_encode($data);
    }
?>