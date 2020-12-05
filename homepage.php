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
        $bdd = new PDO('mysql:host=localhost;dbname=budgetsquirrel', 'root');

        session_start();

        $_SESSION['niss'] = htmlspecialchars($_POST['niss']);

        $niss = htmlspecialchars($_POST['niss']);
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
                <li><a href="historique.html">Historique</a></li>
                <li><a href="enregistrement.html">Enregistrement</a></li>
                <li><a href="stat.html">Statistiques</a></li>
            </ul>

            <ul>
                <li><a href="profil.php"><?php ?>Profil</a></li>
                <li><a href="">Déconnexion</a></li>
            </ul>
        </nav>

    </header>


<div class="container">
    <h1>Bienvenue sur ton écran d'accueil, <?php echo $connexion['prenom']; echo " "; echo $connexion['nom'] ?> !</h1>

    <div>
        <p>Tuto enregistrement</p>
    </div>

    <div>
        <p>Tuto profil</p>
    </div>

    <div>
        <p>Tuto historique</p>
    </div>

    <div>
        <p>Tuto statistiques</p>
    </div>
</div>
    

    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC
            ULB)
        </p>
    </footer>
</body>

</html>