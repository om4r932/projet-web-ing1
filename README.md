# Pokédex Web Application

Cette application web est un Pokédex interactif développé dans le cadre de la SAE (Situation d'Apprentissage et d'Évaluation) sur les bases de données et le développement web.

![Aperçu du Pokédex](./demo.png)

## Description

Le projet consiste en une application dynamique permettant de rechercher, visualiser des informations détaillées (statistiques, types, formes, familles d'évolution) sur différents Pokémon. L'application exploite une base de données relationnelle pour stocker et récupérer ces informations.

## Fonctionnalités

- **Recherche intuitive** : Recherche de Pokémon par nom avec autocomplétion.
- **Détails complets** : Affichage des caractéristiques (nom en plusieurs langues, taille, poids, type).
- **Statistiques** : Visualisation des statistiques de base avec des barres de progression.
- **Sprites multiples** : Affichage dynamique des différentes formes du Pokémon (Normal, Chromatique, Méga, Gigamax).
- **Arbre d'évolution** : Navigation dans la famille d'évolution du Pokémon (pré-évolution et évolutions).

## Technologies utilisées

- **Frontend** : HTML5, CSS3, JavaScript.
- **Backend** : PHP 8.x.
- **Base de données** : MySQL / MariaDB via PDO (PHP Data Objects).
- **Versionnage** : Git.

## Données

Les données de l'application proviennent de l'API [TyraDex](https://tyradex.app/), une API open-source fournissant des informations détaillées sur les Pokémon. Elles sont importées et normalisées via le script `data_insertion.py`.

## Architecture

- `index.php` : Point d'entrée et logique d'affichage.
- `db.php` : Connexion à la base de données et fonctions d'accès aux données.
- `schema.sql` : Script de création de la structure SQL.
- `data_insertion.py` : Script d'importation des données.
- `script.js` / `style.css` : Scripts et styles.

## Installation

1. Clonez ce dépôt.
2. Installez les dépendances Python nécessaires pour le script d'import :
    ```bash
    pip install -r requirements.txt
    ```
3. Importez le script `schema.sql` dans MySQL.
4. Configurez votre connexion dans `db.php` et `.env`.
5. Lancez le script d'importation :
    ```bash
    python data_insertion.py
    ```
6. Lancez via un serveur local (XAMPP/WAMP/MAMP).

## Auteurs

- Étudiants : Omar ID EL MOUMEN
- Encadrants : Nathalie Pernelle, Manel Zarrouk, Samir Youcef.
