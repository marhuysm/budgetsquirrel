<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Connexion</title>
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

        $getUtilisateurs = $bdd->prepare("SELECT * FROM budgetsquirrel.utilisateur");
        $getUtilisateurs->execute();
        $utilisateurs = $getUtilisateurs->fetchAll();
        
        if(isset($_POST["connexion"])){

            echo 'Le niss de l utilisateur sélectionné est ' . $niss = htmlspecialchars($_POST['niss']) . '.';

           }

           ?>

    <div class="landing_header">
        <div class="glass_box">
            <img class="biglogo" src="img/logo.png">
            <h1>Budget Squirrel</h1>
        </div>
    </div>
    <div class="container landing_container">
        <div class="centered_message">

            <form method="POST">

                <h2>Sélectionnez votre profil</h2>

                <p>
                    <select name="niss">
                        <?php foreach ($utilisateurs as $utilisateur): ?>
                            <option value ="<?php echo $utilisateur['niss']?>"><?php echo $utilisateur['nom']; echo " "; echo $utilisateur['prenom']?></option>
                    <?php endforeach; ?>
                        </select>
                </p>

                <p>
                <button type="submit" class="mybutton full_button" name="connexion">Connexion</button>
                </p>

              </form>
        </div>
    </div>
    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC ULB)
        </p>
    </footer>
</body>

</html>