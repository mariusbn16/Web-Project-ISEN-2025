<?php
require_once('database.php');

// Connexion à la base
$db = dbConnect();
if ($db === false) {
    echo json_encode(['error' => 'Erreur de connexion à la base de données']);
    exit();
}

// Vérifie si un MMSI est passé en paramètre
$mmsi = isset($_GET['mmsi']) ? $_GET['mmsi'] : null;

// Préparer la requête SQL (un seul bateau ou tous)
if ($mmsi) {
    $sql = "SELECT n.MMSI, n.VesselName, n.Length, n.Width, n.Draft, p.id,
                   p.BaseDateTime, p.LAT, p.LON, p.SOG, p.COG, p.Heading, p.Etat
            FROM bateau n
            INNER JOIN position p ON n.MMSI = p.MMSI
            WHERE n.MMSI = :mmsi
            ORDER BY p.BaseDateTime DESC
            LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':mmsi', $mmsi, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql = "SELECT n.MMSI, n.VesselName, n.Length, n.Width, n.Draft, p.id,
                   p.BaseDateTime, p.LAT, p.LON, p.SOG, p.COG, p.Heading, p.Etat
            FROM bateau n
            INNER JOIN position p ON n.MMSI = p.MMSI
            ORDER BY p.BaseDateTime DESC
            LIMIT 200";
    $stmt = $db->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Si aucun résultat
if (!$rows) {
    echo json_encode([]);
    exit();
}

// Appliquer le script Python à chaque navire pour prédire son cluster
foreach ($rows as &$row) {
    $cmd = "python3 ../script/clusters.py"
        . " --sog {$row['SOG']} --cog {$row['COG']}"
        . " --latitude {$row['LAT']} --longitude {$row['LON']}"
        . " --heading {$row['Heading']} --length {$row['Length']}"
        . " --width {$row['Width']} --draft {$row['Draft']}"
        . " --status {$row['Etat']} -t '{$row['BaseDateTime']}'";

    exec($cmd, $output, $returnCode);

    if ($returnCode !== 0 || empty($output)) {
        error_log("Erreur Python [$returnCode] sur $cmd");
        $row['cluster'] = -1; // Indiquer erreur
        continue;
    }

    $cluster = intval($output[0]);

    // Si cluster est hors plage, le rejeter
    if ($cluster < 1 || $cluster > 5) {
        $row['cluster'] = -1;
    } else {
        $row['cluster'] = $cluster;
    }
}

// Réponse JSON
echo json_encode($rows);
?>
