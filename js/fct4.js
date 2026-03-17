'use strict';

// Quand la page est complètement chargée
window.onload = async () => {
    // Récupère les paramètres d’URL (ex : ?mmsi=123456789)
    const urlParams = new URLSearchParams(window.location.search);
    const mmsi = urlParams.get('mmsi'); // extrait le MMSI si présent

    // Envoie une requête à fct4.php, avec ou sans paramètre mmsi
    const response = await fetch(`php/fct4.php${mmsi ? '?mmsi=' + mmsi : ''}`);

    // Attend la réponse JSON
    const data = await response.json();

    // Si une erreur est renvoyée depuis le PHP, affiche-la
    if (data.error) {
        alert('Erreur: ' + data.error);
        return;
    }

    // Sinon, trace les clusters sur la carte
    plotClusters(data);
};

function plotClusters(data) {
    // Couleurs pour les clusters 0 à 4 (indexés de 0 à 4)
    const colors = ['#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#FF00FF'];
    const traces = [];

    // Pour chaque cluster de 1 à 5
    for (let cluster = 1; cluster <= 5; cluster++) {
        // Filtrer les données pour ne garder que celles de ce cluster
        const clusterData = data.filter(d => d.cluster === cluster);

        // Définir une trace Plotly pour ce cluster
        const trace = {
            type: 'scattergeo', // graphique géographique
            mode: 'markers',    // affichage de points
            name: `Cluster ${cluster}`, // nom de la série
            lon: clusterData.map(d => parseFloat(d.LON)), // longitudes
            lat: clusterData.map(d => parseFloat(d.LAT)), // latitudes
            text: clusterData.map(d => `${d.VesselName} (${d.MMSI})`), // info bulle
            marker: {
                color: colors[cluster], // couleur selon cluster
                size: 8,
                line: { width: 1, color: 'black' } // bordure noire
            }
        };

        // Ajouter la trace à la liste globale
        traces.push(trace);
    }

    // Définir le layout de la carte Plotly
    const layout = {
        title: "Prédiction des Clusters - Carte Marine",
        autosize: true,
        margin: { l: 0, r: 0, b: 0, t: 40 }, // marges
        geo: {
            scope: 'north america', // zone géographique
            resolution: 50, // qualité
            showland: true,
            landcolor: '#E5ECF6',
            showocean: true,
            oceancolor: '#C8DFFA',
            showlakes: true,
            lakecolor: '#C8DFFA',
            showcountries: true,
            countrycolor: '#AAAAAA',
            projection: {
                type: 'mercator' // projection Mercator
            },
            center: {
                lon: -89.5,  // centré sur
                lat: 26.5
            },
            lonaxis: { range: [-97, -82] },
            lataxis: { range: [22, 31] },
        }
    };

    // Dessine la carte avec les données et le layout
    Plotly.newPlot('map', traces, layout, { responsive: true });
}
