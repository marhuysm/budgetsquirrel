<?php
    session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Statistiques</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/skeleton.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/app.css">
</head>

<body>

<?php 

        $bdd = new PDO('mysql:host=localhost;dbname=budgetsquirrel', 'root');

        $niss = $_SESSION['niss'];

        $getConnexion = $bdd->prepare("SELECT * FROM budgetsquirrel.utilisateur WHERE niss = $niss");
        $getConnexion-> execute();
        $connexion = $getConnexion->fetch();
        
        // pour récupérer le total des dépenses, il faut faire une requête qui récupère toutes les transactions appartenant 
        // à l'utilisateur et étant < 0, et qui en fait la somme :

        $getDepenses = $bdd->prepare("SELECT SUM(montant) as total_depenses FROM budgetsquirrel.transaction_financiere WHERE niss_util = $niss AND montant < 0");
        $getDepenses->execute();
        $fetchedDepenses = $getDepenses->fetch();
        $total_depenses = $fetchedDepenses["total_depenses"];

        //de même pour les revenus, sauf que > 0 : 

        $getRevenus = $bdd->prepare("SELECT SUM(montant) as total_revenus FROM budgetsquirrel.transaction_financiere WHERE niss_util = $niss AND montant > 0");
        $getRevenus->execute();
        $fetchedRevenus = $getRevenus->fetch();
        $total_revenus = $fetchedRevenus["total_revenus"];

        // enfin , le bilan fait le mm calcul, mais sans condition : 

        $getBilan = $bdd->prepare("SELECT SUM(montant) as bilan FROM budgetsquirrel.transaction_financiere WHERE niss_util = $niss");
        $getBilan->execute();
        $fetchedBilan = $getBilan->fetch();
        $bilan = $fetchedBilan["bilan"];
  ?>

    <header>

    <nav class=menu>
            <div class="logo-container">
                <img src="img/logo.png">
                <a href="homepage.php">Budget Squirrel</a>
            </div>

            <ul class="pages">
                <li><a href="historique.php">Historique</a></li>
                <li><a href="enregistrement.php">Enregistrement</a></li>
                <li><a href="stat.php">Statistiques</a></li>
            </ul>

            <div class="profile-container">
                <img  class = "photo-profil" src="img/<?php echo $connexion['photo']?>">
                <ul>
                    <li><a href="profil.php"><?php ?>Profil de <?php echo $connexion['prenom']; echo " "; echo $connexion['nom'] ?></a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
              </ul>
            </div>
        </nav>

    </header>

    <div class="container">

        <h1>Statistiques</h1>

        <div class="row">
            <div class="four columns">
                <p>Total des dépenses : <?php echo $total_depenses; echo " €"; ?></p>
            </div>

            <div class="four columns">
                <p>Total des revenus : <?php echo $total_revenus; echo " €"; ?></p>
            </div>

            <div class="four columns">
                <p>Bilan : <?php echo $bilan; echo " €"; ?></p>
            </div>

        </div>

        <div>
            <h2>Répartition des transactions par mois</h2>
        </div>

        <div>
            <h2>Répartition totale par catégorie</h2>
        </div>

        <div>
            <h2>Répartition totale par type de transaction</h2>
        </div>

    </div>
    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC
            ULB)
        </p>
    </footer>
</body>

</html>