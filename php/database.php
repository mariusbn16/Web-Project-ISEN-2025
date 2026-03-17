<?php

    require_once('constants.php'); // Inclut le fichier de constantes pour les informations de connexion à la base de données.

    // Connexion à la base de donnée
    function dbConnect(){
        try {
            // Tente de créer une nouvelle instance PDO pour se connecter à la base de données.
            $db = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASSWORD);
            // Configure PDO pour lancer des exceptions en cas d'erreur SQL.
            $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $exception){
            error_log('Connection error: '.$exception->getMessage());
            return false; // Retourne false pour indiquer un échec.
        }
        return $db; // Retourne l'objet PDO connecté.
    }

    // Recuperation des etat dans la table etat
    function dbGetEtat($db){
        try {
            // Exécute une requête pour sélectionner toutes les entrées de la table 'etat'.
            $statement = $db->query('SELECT etat, nom FROM etat');
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $exception) {
            error_log('Request error: '.$exception->getMessage());
            return false; // Retourne false pour indiquer un échec.
        }
        return $result; // Retourne les résultats.
    }

    // Ajoute les données du formulaire dans la base de donnée
    function dbAddData($db, $data){ 
        try {
            // Vérifie la présence des champs obligatoires 'mmsi' et 'vessel_name'.
            foreach (['mmsi', 'vessel_name'] as $key) {
                if (empty($data[$key])) {
                    throw new Exception("Champ obligatoire manquant : $key");
                }
            }

            // Requête préparée pour insérer ou ignorer (si MMSI existe déjà) un bateau.
            $request_bateau = "INSERT IGNORE INTO bateau (MMSI, VesselName, Length, Width, Draft)
                                VALUES (:mmsi, :vessel_name, :length, :width, :draft)";
                    
            $statement_bateau = $db->prepare($request_bateau);

            // Lie les paramètres pour la table 'bateau'.
            $statement_bateau->bindParam(':mmsi', $data['mmsi'], PDO::PARAM_STR);
            $statement_bateau->bindParam(':vessel_name', $data['vessel_name'], PDO::PARAM_STR);
            $statement_bateau->bindParam(':length', $data['length'], PDO::PARAM_STR);
            $statement_bateau->bindParam(':width', $data['width'], PDO::PARAM_STR);
            $statement_bateau->bindParam(':draft', $data['draft'], PDO::PARAM_STR);

            $statement_bateau->execute(); // Exécute l'insertion du bateau.

            // Requête préparée pour insérer une position.
            $request_position = "INSERT INTO position (BaseDateTime, LAT, LON, SOG, COG, Heading, Etat, MMSI) 
                                    VALUES (:basedatetime, :lat, :lon, :sog, :cog, :heading, :etat, :mmsi)";

            $statement_position = $db->prepare($request_position);

            // Concatène la date et l'heure pour le champ 'basedatetime'.
            $basedatetime = $data['date']. ' '. $data['time'];
            // Lie les paramètres pour la table 'position'.
            $statement_position->bindParam(':basedatetime', $basedatetime, PDO::PARAM_STR);
            $statement_position->bindParam(':lat', $data['lat'], PDO::PARAM_STR);
            $statement_position->bindParam(':lon', $data['lon'], PDO::PARAM_STR);
            $statement_position->bindParam(':sog', $data['sog'], PDO::PARAM_STR);
            $statement_position->bindParam(':cog', $data['cog'], PDO::PARAM_STR);
            $statement_position->bindParam(':heading', $data['heading'], PDO::PARAM_STR);
            $statement_position->bindParam(':etat', $data['etat'], PDO::PARAM_INT);
            $statement_position->bindParam(':mmsi', $data['mmsi'], PDO::PARAM_STR);

            $statement_position->execute(); // Exécute l'insertion de la position.

        } catch (PDOException $exception) {
            error_log('Erreur PDO : ' . $exception->getMessage());
            error_log('Données : ' . print_r($data, true));
            return false; // Retourne false en cas d'erreur.
        }
        return true; // Retourne true si tout s'est bien passé.
    }

     //Récupère toutes les informations sur les bateaux et leurs dernières positions.  
    function dbGetVessels($db) {
        try {
            // Requête SQL pour joindre les tables 'bateau' et 'position' et récupérer les données.
            $sql = "SELECT n.MMSI, n.VesselName, n.Length, n.Width, n.Draft,
                        p.BaseDateTime, p.LAT, p.LON, p.SOG, p.COG, p.Heading, p.Etat
                    FROM bateau n 
                    INNER JOIN position p ON n.MMSI = p.MMSI
                    ORDER BY p.BaseDateTime DESC";
            
            $statement = $db->query($sql); // Exécute la requête.
            $result = $statement->fetchAll(PDO::FETCH_ASSOC); // Récupère tous les résultats.
            
            return $result; // Retourne les résultats.
        } catch (PDOException $exception) {
            error_log('Request error: ' . $exception->getMessage());
            return false;
        }
    }
    
    
     //Récupère les dernières données d'un bateau spécifique pour la prédiction de son type.
    function dbPredictType($db, $mmsi){
        try {
            // Requête SQL pour obtenir les dernières données de position et les caractéristiques du bateau.
            $sql = "SELECT b.Length, b.Width, b.Draft, p.SOG, p.COG, p.Etat
                    FROM bateau b
                    INNER JOIN position p on b.MMSI = p.MMSI
                    WHERE b.MMSI = :mmsi
                    ORDER BY p.id DESC 
                    LIMIT 1";

            $statement = $db->prepare($sql); // Prépare la requête.
            $statement->bindParam(':mmsi', $mmsi, PDO::PARAM_STR); // Lie le paramètre MMSI.
            $statement->execute(); // Exécute la requête.
            $result = $statement->fetchAll(PDO::FETCH_ASSOC); // Récupère les résultats.

            return $result; // Retourne les résultats.
        }
        catch (PDOException $exception) {
            error_log('Request error: ' . $exception->getMessage());
            return false;
        }
    }
?>