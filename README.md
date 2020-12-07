# budgetsquirrel
A PHP project for our M2 database course

## Description des fichiers et de l'état du projet : 

- Dossier css : complet , skeleton + adaptations css perso
- Dossier img : images de l'appli (logo, photos de profil...)
- Dossier Rapport LaTex : self explanatory, pour l'instant, rapport vide, il y a seulement la structure
- basicdata.sql : fichier d'écriture des premières données : pour l'instant, il y a seulement qq utilisateurs
- connexion.html : simple version html de l'écran de connexion
- connexion.php : version php de l'écran de connexion:  le select va chercher, via php, les différents utilisateurs présents dans la bdd. Fonctionnel. Ouvre une session avec le niss utilisateur, session est appelée à chaque page de l'appli, et ça permet de récupérer slmt les infos liées à l'utilisateur.Le POST du formulaire redirige directement vers homepage.php et est récupéré par SESSION.
- enregistrement.html : simple version html de l'écran d'enregistrement de transaction
- historique.html : simple version html de l'écran d'historique
- homepage.html : simple version html de l'écran principal/ tuto de l'app
- homepage.php : wip, version php de l'écran principal. Premier écran après la connexion de connexion.php. Redirige vers les autres pages de l'app et permet d'ouvrir la session et définir le niss de l'utilisateur ( grace au POST de connexion.php).
- index.html : simple version html de la landing page. normalement, pas besoin de php ici. Permet juste d'avoir une landing page, accessible également via localhost/budgetsquirrel/ (sans aucun nom de fichier)
- initdb.sql : fichier de création de la base de donnée et des tables. Assez basique pour l'instant, à modifier et optimiser (en ajoutant suppression de la bdd au début ou en utilisant IF NOT EXISTS? Besoin également de rajouter toutes les contraintes et de voir s'il ne faut pas des infos supplémentaires)
- inscription.php : WIP fonctionel de la page d'inscription d'un nouvel utilisateur. Il est donc possible d'enregistrer un nouvel utilisateur dans la table utilisateur. Si le NISS est déjà utilisé, un message d'erreur est prévu (avec possibilité de se rediriger vers connexion.php). Il y a aussi un message de succès lorsque les données sont enregistrées, ainsi qu'un lien vers connexion.php.
- logout.php : écran de déconnexion. Permet d'effacer les données de session de l'utilisateur, qui est ensuite redirigé vers index.html via un bouton
- profil.html : simple version html de l'écran de présentation / modif du profil
- profil.php : wip de l'écran de présentation et de modif du profil. Il est pour l'instant possible de consulter ses infos et d'ajouter une carte à son utilisateur connecté (wip sur la suppression et le changement de photo)
- stat.html : simple version html de l'écran de statistiques
- utils.js : fichier javascript. Pour l'instant seulement utilisé pour faire le lien entre les pages (onclick), mais sera aussi utile pour afficher / cacher des éléments du formulaire d'ajout de transaction

### Note Marie : 

#### vdd 4/12 : 
Hello ! Pour l'instant, je n'ai pas pu encore avancer sur la partie php, j'ai passé la journée à étudier les scripts sql! Mais je m'occupe d'au moins la page d'inscription pour samedi 5 décembre. Mais n'hésite pas à explorer et modifier des trucs en attendant. J'ai aussi mis des commentaires sur les données à enregistrer dans le drive (Trad EA-relationnel)

#### dim 6/12 : 

Je bosse sur la partie php et j'ai créé une bdd basique (cf initdb.sql), à voir si c'est ok pour les données enregistrées, et il manque encore bcp de contraintes. L'écran d'inscription.php est plus ou moins fini, la gestion de connexion.php permet d'ouvrir une session de l'app aussi. J'essaie de peaufiner l'affichage des infos utilisateurs dans homepage.php et profil.php (+modifs possibles à faire dans profil.php : ajout de carte, suprr de carte, et changement de photo de profil)

#### lu 7/12 : 

page de profil : suppression de carte (suppression logique) fonctionne parfaitement. Ajouté une colonne gérant la suprr logique dans la table carte. Ajouté également un tableau montrant les cartes dispos/non supprimées

page d'enregistrement: WIP budget_id. à 11:18 : l'id de budget est soit créé, soit récupéré, en fonction de s'il existe ou non, cf "enregistrement.php"
WIP à 12:00 : lorsque l'utilisateur entre toutes les données requises, on cherche si un budget_id correspond déjà au mois et à l'année sélectionnée. Si oui, on extrait le budget id, sinon, on crée le budget mensuel correspondant, et ensuite, on extrait le budget id. Le budget id récupéré, on peut créer la transaction de l'utilisateur, liée à son niss, et au budget id correspondant


## Pour faire fonctionner le projet : 
1. Démarrer xammp
2. Tout mettre dans un dossier budgetsquirrel dans xammp/htdocs
3. Ouvrir localhost/phpmyadmin sur son navigateur
4. Run initdb.sql dans phpmyadmin pour créer la bdd budgetsquirrel
5. Lorsque le script sql d'ajout de données sera créé : run le script d'ajout de données
5. Pour run l'application : toujours dans le navigateur (best on chrome) aller dans localhost/budgetsquirrel/ + la page .php ou html choisie (par ex localhost/budgetsquirrel/inscription.php)
