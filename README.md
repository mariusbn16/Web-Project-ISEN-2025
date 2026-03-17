# Web-Project-ISEN-2025

IMPORTANT : Deux personnes ont travaillés sur ce projet. Je ne suis pas l'auteur de tout le code.

Réalisé par : Martinez Morganne et Bougouin Marius (Trinôme 6) 
==========

Projet Web 3ème année ISEN de Brest

Ce projet contient une interface web permettant de charger des données, d'appliquer des modèles de machine learning, et de visualiser les résultats.

PRÉREQUIS
---------
1. Serveur local (ex: XAMPP, WAMP, ou MAMP) avec :
   - PHP 7.x ou 8.x
   - MySQL
2. Navigateur web moderne (Chrome, Firefox, etc.)
3. Python 3.8+ avec les bibliothèques suivantes :
   - scikit-learn
   - pandas
   - joblib
   - numpy

STRUCTURE DU PROJET
-------------------

/index.html
    Page d'accueil du site.

/fct2.html à /fct5.html
    Interfaces des différentes fonctionnalités proposées :
    - fct2 : Chargement et prétraitement de données
    - fct3 : Visualisation 
    - fct4 : Application des modèles d'ia
    - fct5 : résultats

/css/style2.css
    Feuille de style pour les pages HTML.

/js/fct2.js à /js/fct5.js
    Scripts JavaScript associés aux pages HTML respectives.

/php/constants.php
    Fichier de configuration => à adapter selon le serveur (identifiants et mdp).

/php/database.php
    Connexion et requêtes vers la base de données MySQL.

/php/fct2.php à /php/fct5.php
    Traitement serveur pour chaque fonctionnalité.

/bdd.sql
    Script SQL pour créer la base de données nécessaire.

/script/
    Dossier contenant les éléments Python du projet :
    - script.py : Script principal pour exécuter les modèles
    - clusters.py : Code de clustering ou d'analyse de groupes
    - *.pkl : Fichiers de modèles sauvegardés :
        - model_knn.pkl : Modèle KNN
        - model_svm.pkl : Modèle SVM
        - model_rf.pkl : Modèle Random Forest
        - model_gb.pkl : Modèle Gradient Boosting
        - scaler.pkl : Standardisation des données
        - encoder.pkl : Encodage des variables catégorielles

/img/
    - isen.png : Logo ou image liée à l’interface
    - image.png : Illustration supplémentaire



