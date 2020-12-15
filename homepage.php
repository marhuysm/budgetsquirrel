<?php
    session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Accueil</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/skeleton.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/app.css">
</head>

<body>

<?php 
        if (isset($_POST['niss'])) {
            $_SESSION['niss'] = htmlspecialchars($_POST['niss']);
        }

        $bdd = new PDO('mysql:host=localhost;dbname=budgetsquirrel', 'utilisateur_app', 'user');
        $niss = $_SESSION['niss'];

        $getConnexion = $bdd->prepare("SELECT * FROM budgetsquirrel.utilisateur WHERE niss = $niss");
        $getConnexion-> execute();
        $connexion = $getConnexion->fetch();
  ?>


    <header>

        <nav class=menu>
            <div class="logo-container">
                <img src="img/logo.png">
                <a href="homepage.php">Budget Squirrel</a>
            </div>
        
            <ul class = "pages">
                <li><a href="historique.php">Historique</a></li>
                <li><a href="enregistrement.php">Enregistrement</a></li>
                <li><a href="stat.php">Statistiques</a></li>
            </ul>
            <div class="profile-container">
                <img  class = "photo-profil" src="img/<?php echo $connexion['photo']?>">
                <ul>
                    <li><a href="profil.php"><?php ?>Profil de <?php echo $connexion['prenom']; echo " "; echo $connexion['nom'] ?></a></li>
                    <li><a  href="logout.php">Déconnexion</a></li>
              </ul>
            </div>
        
        </nav>

    </header>


<div class="container">
    <h1>Bienvenue sur ton écran d'accueil, <?php echo $connexion['prenom']; echo " "; echo $connexion['nom'] ?> !</h1>

    <div class = "section">
        <span class="homepage_content">
            <p><b> Enregistrement des transactions </b></p>
            <p style="text-align:justify"> Budget Squirrel est une application souple qui permet l'enregistrement des transactions financières. Pour ce faire, un utilisateur doit être reconnu par le système.
        Dans l'onglet Enregistrement, vous pouvez ajouter les détails d'une transactions (son montant, sa date, sa catégorie, ainsi que le type de paiement utilisé). Pour enregistrer vos dépenses, ajoutez un montant négatif (précédé de "-"). Pour enregistrer vos revenus, ajoutez un montant positif. Vous n'avez pas besoin d'entrer de monnaie, notez que l'application utilise l'euro par défaut (€). </p>
        </span>
        <span class="homepage_image">
            <img class="homepage" src="img/tuto_enregistrement.png" alt="écran enregistrement"> 
        </span>
    </div>

    <div class = "section">
        <span class = "homepage_image">
            <img class="homepage" src="img/creation_profil.png" alt="écran création profil">   
        </span>
        <span class="homepage_content">
            <p><b> Comment créer son profil ? </b></p>
            <p style="text-align:justify"> Le profil de Budget Squirrel enregistre les données propres aux utilisateurs permettant l'identification ulterieure, ainsi que la gestion des cartes de paiement employées par l'utilisateur. </p>
            <p style="text-align:justify"> Si vous ouvrez Budget Squirrel pour la toute première fois, l'application vous offre la possibilité de vous inscrire, et propose la collecte d'une série des données nécessaires pour la création et gestion ulterieure de votre profil (i.e. nom, prénom, date de naissance, etc). Si vous êtes déjà inscrit et que vous souhaitez changer les informations de votre profil, vous pouvez y accéder en cliquant sur votre profil (en haut, à droite de la page). Une fois enregistré, il n'est plus desormais possible de modifier votre nom, numéro NISS, ou date de naissance. Vous pouvez enrichir votre profil en ajoutant des types des cartes employés (dans la section "Ajouter une nouvelle carte"), et vous pouvez également supprimer les cartes devenues obsolètes (dans la section "Supprimer une carte"). </p>
        </span>
    </div>
    
    <div class = "section">
        <span class="homepage_content">
            <p><b> Consulter l'historique </b></p>
            <p style="text-align:justify"> L'historique de l'application suit chronologiquement toute depense ou revenu enregistré. Pour consulter la liste, l'utilisateur est invité à choisir un mois, et à confirmer son choix avec le bouton "Voir le budget du mois sélectionné".
            La liste des transactions affichée contient pour chaque transaction son type, sa catégorie, son montant et sa date, ainsi que des informations sur le type de paiement employé. </p>
        </span>
        <span class = "homepage_image">
            <img class="homepage" src="img/tuto_historique.png" alt="écran historique">   
        </span>
    </div>

    <div class = "section">
        <span class = "homepage_image">
            <img class="homepage" src="img/tuto_statistiques.png" alt="écran statistiques">   
        </span>
        <span class="homepage_content">
            <p><b> Statistiques </b></p>
            <p style="text-align:justify"> Budget Squirrel propose trois types des statistiques à ses utilisateurs: la répartition des transactions par mois, la répartition totale par catégorie, ainsi que la répartition totale par type de transaction. L'utilisateur est invité à consulter cette page des rapports pour garder une meilleure vue sur ses entrées et sorties financières.</p>
        </span> 
    </div>
</div>

    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC
            ULB)
        </p>
    </footer>
</body>

</html>
