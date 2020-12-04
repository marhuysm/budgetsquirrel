# budgetsquirrel
A PHP project for our M2 database course

## Description des fichiers et de l'état du projet : 

- Dossier css : complet , skeleton + adaptations css perso
- Dossier img : images de l'appli (logo, photos de profil...)
- connexion.html : simple version html de l'écran de connexion
- enregistrement.html : simple version html de l'écran d'enregistrement de transaction
- historique.html : simple version html de l'écran d'historique
- homepage.html : simple version html de l'écran principal/ tuto de l'app
- index.html : simple version html de la landing page
- initdb.sql : fichier de création de la base de donnée et des tables
- inscription.php : WIP pas encore fonctionnel de la page d'inscription d'un nouvel utilisateur
- profil.html : simple version html de l'écran de présentation / modif du profil
- stat.html : simple version html de l'écran de statistiques
- utils.js : fichier javascript. Pour l'instant seulement utilisé pour faire le lien entre les pages (onclick), mais sera aussi utile pour afficher / cacher des éléments du formulaire d'ajout de transaction

Pour faire fonctionner le projet : 
1. Démarrer xammp
2. Tout mettre dans un dossier budgetsquirrel dans xammp/htdocs
3. Ouvrir localhost/phpmyadmin sur son navigateur
4. Run initdb.sql dans phpmyadmin
5. Lorsque le script sql d'ajout de données sera créé : run le script d'ajout de données
5. Pour run l'application : toujours dans le navigateur (best on chrome) aller dans localhost/budgetsquirrel/ + la page .php ou html choisie (par ex localhost/budgetsquirrel/inscription.php)
