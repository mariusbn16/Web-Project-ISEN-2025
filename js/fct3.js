'use strict';

const bt = document.getElementById("prediction-type");
bt.addEventListener("click", predictType);

// recuperation de MMMSI du bateau selectionné
const bt2 = document.getElementById("prediction-trajectoire");
bt2.addEventListener("click", () => {
    const radio = document.querySelectorAll('input[name="selected-vessel"]');
    let MMSI = null;

    radio.forEach(radio => {
        if (radio.checked) {
            MMSI = radio.value;
        }
    });
    // redicrection vers la nouvelle page avec la requete associé dans l'url
    if(MMSI!=null){window.location.href = "fct5.html?request=trajectoire";}
    else{alert("Veuillez choisir un bateau");}
})

async function predictType(event){

    const radio = document.querySelectorAll('input[name="selected-vessel"]');
    let MMSI = null;

    radio.forEach(radio => {
        if (radio.checked) {
            MMSI = radio.value;
        }
    });

    if (MMSI != null){
        let data;
        console.log("cdt ok"); 

        // requete pour recuperer les données associés au MMSI
        const response = await fetch('php/fct5.php?request=get-data&mmsi=' + MMSI);
        if (response.ok) {
        data = await response.json();
        if (data.error) {
            console.log('Erreur serveur: ' + data.error);
            displayErrors(data.error);
            return;
        }
        console.log(data);

        // préparation des données à envoyé dans l'url
        const param_url = "?request=type&Length=" + data[0]['Length'] + "&Width=" + data[0]['Width'] + "&Draft=" + data[0]['Draft'] + 
                            "&SOG=" + data[0]['SOG'] + "&COG=" + data[0]['COG'] + "&Etat=" + data[0]['Etat'];
        
        // redicrection vers la nouvelle page avec les valeurs associés dans l'url
        window.location.href = "fct5.html" + param_url;
    }
    }
    else {alert("Veuillez choisir un bateau");}
}


// Fonction pour récupérer les données des navires
async function getVessels() {

    const response = await fetch('php/fct3.php');
    let data;
    if (response.ok) {
        data = await response.json();
        if (data.error) {
            console.log('Erreur serveur: ' + data.error);
            displayErrors(data.error);
            return;
        } 
        displayVessels(data);
        createMap_Mapbox(data);
    } else {
        console.log('HTTP error: ' + response.status);
        displayErrors(response.status);
    }

}

// Fonction pour remplir le tableau avec les données des navires
function displayVessels(data) {
    let table = document.getElementById('ships-table');
    // Ajout d'une ligne pour chaque navire
    for (let row of data) {
        let tr = document.createElement('tr');
        tr.innerHTML =
            "<td>" + row.MMSI + "</td>" +
            "<td>" + row.BaseDateTime + "</td>" +
            "<td>" + row.LAT + "</td>" +
            "<td>" + row.LON + "</td>" +
            "<td>" + row.SOG + "</td>" +
            "<td>" + row.COG + "</td>" +
            "<td>" + row.Heading + "</td>" +
            "<td>" + row.VesselName + "</td>" +
            "<td>" + row.Etat + "</td>" +
            "<td>" + row.Length + "</td>" +
            "<td>" + row.Width + "</td>" +
            "<td>" + row.Draft + "</td>"+
            "<td class=\"checkbox-cell\"><input type=\"radio\" name=\"selected-vessel\" value=\"" + row.MMSI + "\"></td>";
        table.appendChild(tr);
    }
}


// Fonction pour créer la carte avec Mapbox
function createMap_Mapbox(data) {
    mapboxgl.accessToken = ''; 

    // Initialiser la carte
    const map = new mapboxgl.Map({
        container: 'map', // ID du div
        style: 'mapbox://styles/mapbox/streets-v12', // Style de la carte (rues)
        center: [-85, 25], // Centré sur le Golfe du Mexique
        zoom: 4
    });

    // Attendre que la carte soit chargée
    map.on('load', () => {
        // Ajouter des marqueurs pour chaque bateau
        for (let row of data) {
            if (row.LAT != null && row.LON != null) {
                // Créer un élément HTML pour le marqueur
                const el = document.createElement('div');
                el.className = 'marker'; // Style défini dans visualisation.html

                // Ajouter un marqueur avec popup
                new mapboxgl.Marker(el)
                    .setLngLat([parseFloat(row.LON), parseFloat(row.LAT)])
                    .setPopup(
                        new mapboxgl.Popup({ offset: 25 })
                            .setHTML(`<h3>${row.VesselName}</h3><p>MMSI: ${row.MMSI}</p>`)
                    )
                    .addTo(map);
            }
        }
    });
}

// Fonction pour afficher les erreurs
function displayErrors(code) {
    // Tableau met en correspondance valeur numérique avec un texte
    let messages = {
        400: 'Requête incorrecte',
        401: 'Authentifiez-vous',
        403: 'Accès refusé',
        404: 'Page non trouvée',
        500: 'Erreur interne du serveur',
        503: 'Service indisponible'
    };
    // Afficher l'erreur dans une pop-up
    alert(messages[code]);
}

getVessels();

// ecoute si appui sur le bouton et recuperation du MMSI avant de changer de page
document.getElementById('prediction-cluster').addEventListener('click', () => {
    const radios = document.querySelectorAll('input[type=radio]:checked');
    if (radios.length === 0) {
        alert('Veuillez sélectionner un bateau pour prédire le cluster.');
        return;
    }

    const radio = document.querySelectorAll('input[name="selected-vessel"]');
    let MMSI = null;

    radio.forEach(radio => {
        if (radio.checked) {
            MMSI = radio.value;
        }
    });
    window.location.href = `fct4.html?mmsi=${MMSI}`;
});
