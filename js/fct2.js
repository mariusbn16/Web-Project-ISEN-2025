'use strict'

getEtat();

// recuperation du formulaire
const form = document.getElementById("vessel-data"); 

form.addEventListener("submit", getFormElem);
async function getEtat(){
    // requete AJAX pour recuperer les valeurs 'etat'
    const response = await fetch('php/fct2.php?request=etat');

    if (response.ok){
        const data = await response.json();
        displayEtat(data);
    }
}

// Affichage des etats dans le menu deroulant
function displayEtat(response) {
    const select = document.getElementById("etat");

    response.forEach(element => {
        var option = document.createElement("option");
        option.text = element['nom'];  
        option.value = element['etat']; 
        select.add(option);
    });
}

// Recuperation des données du formulaire et injection dans la BDD
async function getFormElem(event){

    event.preventDefault();

    const inputs = form.querySelectorAll("input, select");

    const data = {};

    inputs.forEach(input => {
    if (input.id) {
        data[input.id] = input.value;
    }
    });
    // Requete pour inserer les données dans la BDD
    const response = await fetch("php/fct2.php?request=add-data", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "cog=" + data['cog'] + "&date=" + data['date'] + "&draft=" + data['draft'] + "&heading=" + data['heading'] + 
        "&lat=" + data['lat'] + "&length=" + data['length'] + "&lon=" + data['lon'] + "&mmsi=" + data['mmsi'] + 
        "&sog=" + data['sog'] + "&etat=" + data['etat'] + "&time=" + data['time'] + "&vessel_name=" + data['vessel_name'] +
        "&width=" + data['width'],
    });

    if(response.ok) {console.log(response)}
    else{console.log("not ok")} 
}
