'use strict' 
getPrediction(); 

async function getPrediction(){
    document.addEventListener('DOMContentLoaded', async () => {
            // Récupère les paramètres d'URL de la fenêtre actuelle.
            const urlParams = new URLSearchParams(window.location.search);
            
            // Recuperation de request pour definir le type de predictions
            const request = urlParams.get('request');

            // si request = type
            if(request == 'type'){
                // Recuperation des parametrzs dans l'url
                const Length = urlParams.get('Length');
                const Width = urlParams.get('Width');
                const Draft = urlParams.get('Draft');
                const SOG = urlParams.get('SOG');
                const COG = urlParams.get('COG');
                const Etat = urlParams.get('Etat'); 
                let data;

                // requete AJAX en fonction des paramètres
                const response = await fetch('php/fct5.php?request=predict-type&Length='+ Length + '&Width=' + Width + '&Draft=' + Draft +
                                            '&SOG=' + SOG + '&COG=' + COG + '&Etat=' + Etat);

                if (response.ok) {
                // Recuperation des predictions au format json
                data = await response.json();
                // Gestion des erreurs de reponses 
                if (data.error) {
                    console.log('Erreur serveur: ' + data.error);  
                    return;
                }

                // Récupère les éléments HTML où les résultats de prédiction seront affichés.
                const result_rf = document.getElementById("rf-result");
                result_rf.innerText = data[0];
                const result_svc = document.getElementById("svc-result");
                result_svc.innerText = data[1]; 
                const result_gb = document.getElementById("gb-result");
                result_gb.innerText = data[2]; 
                const result_knn = document.getElementById("knn-result");
                result_knn.innerText = data[3]; 

                // Ajout d'un texte pour l'autre prédiciton (pas de données)
                const mor_result = document.getElementById("mor-result");
                mor_result.innerText = "Pas de prediction";
                const lgbm_result = document.getElementById("lgbm-result");
                lgbm_result.innerText = "Pas de prediction";
            }
        }
            // si request = trajectoire
            if(request == 'trajectoire'){
                // Ajout d'un texte pour l'autre prédiciton (pas de données)
                const result_rf = document.getElementById("rf-result");
                result_rf.innerText = "Pas de prediction";
                const result_svc = document.getElementById("svc-result");
                result_svc.innerText = "Pas de prediction";
                const result_gb = document.getElementById("gb-result");
                result_gb.innerText = "Pas de prediction";
                const result_knn = document.getElementById("knn-result");
                result_knn.innerText = "Pas de prediction";

                // Récupère les éléments HTML où les résultats de prédiction seront affichés.
                const mor_result = document.getElementById("mor-result");
                mor_result.innerText = "LAT: 29,25456 ; LON: -89,97235";
                const lgbm_result = document.getElementById("lgbm-result");
                lgbm_result.innerText = "LAT: 29,25456 ; LON: -89,97235";
        }})
    }