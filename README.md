# budgetsquirrel
A PHP project for our M2 database course

## Description des fichiers et de l'état du projet : 

- Dossier css : complet , skeleton + adaptations css perso
- Dossier img : images de l'appli (logo, photos de profil...)
- connexion.html : simple version html de l'écran de connexion
- connexion.php : version php de l'écran de connexion:  le select va chercher, via php, les différents utilisateurs présents dans la bdd
- enregistrement.html : simple version html de l'écran d'enregistrement de transaction
- historique.html : simple version html de l'écran d'historique
- homepage.html : simple version html de l'écran principal/ tuto de l'app
- index.html : simple version html de la landing page
- initdb.sql : fichier de création de la base de donnée et des tables. Assez basique pour l'instant, à modifier et optimiser (en ajoutant suppression de la bdd au début ou en utilisant IF NOT EXISTS, à voir)
- inscription.php : WIP pas encore fonctionnel de la page d'inscription d'un nouvel utilisateur
- profil.html : simple version html de l'écran de présentation / modif du profil
- stat.html : simple version html de l'écran de statistiques
- utils.js : fichier javascript. Pour l'instant seulement utilisé pour faire le lien entre les pages (onclick), mais sera aussi utile pour afficher / cacher des éléments du formulaire d'ajout de transaction

### Note Marie : 

Hello ! Pour l'instant, je n'ai pas pu encore avancer sur la partie php, j'ai passé la journée à étudier les scripts sql! Mais je m'occupe d'au moins la page d'inscription pour samedi 5 décembre. Mais n'hésite pas à explorer et modifier des trucs en attendant. J'ai aussi mis des commentaires sur les données à enregistrer dans le drive (Trad EA-relationnel)


## Pour faire fonctionner le projet : 
1. Démarrer xammp
2. Tout mettre dans un dossier budgetsquirrel dans xammp/htdocs
3. Ouvrir localhost/phpmyadmin sur son navigateur
4. Run initdb.sql dans phpmyadmin pour créer la bdd budgetsquirrel
5. Lorsque le script sql d'ajout de données sera créé : run le script d'ajout de données
5. Pour run l'application : toujours dans le navigateur (best on chrome) aller dans localhost/budgetsquirrel/ + la page .php ou html choisie (par ex localhost/budgetsquirrel/inscription.php)
