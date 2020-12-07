<?php
    session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Historique</title>
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

        // l'objectif de la page : 
        // Afficher un formulaire de sélection de mois, puis, une fois envoyé, montrer le tableau des transactions financières pour le mois
        // et donner la possibilité à l'utilisateur de visualiser son total pour ce mois ainsi que de clore le mois sélectionné.
        
        // A essayer si on a le temps : get le dernier budget en cours et l'afficher "par défaut"

        // une fois que l'utilisateur a sélectionné le mois qu'il veut consulté, on récupère les données du get
        // ainsi que le mois et l'année , afin de pouvoir afficher le bon budget mensuel par la suite.

        if(isset($_GET['selection_mois'])){
            $table_month = htmlspecialchars($_GET['table_month']);
            $parsed_date = date_parse_from_format('Y-m', $table_month);
            $mois_choisi = $parsed_date["month"]; // mois en int
            $annee_choisie = $parsed_date["year"]; // année en int

            echo($table_month . " <br>" . $mois_choisi . "<br>" . "$annee_choisie");

            $getTransactions = $bdd->prepare("SELECT * FROM budgetsquirrel.transaction_financiere WHERE niss_util = $niss");
            $getTransactions->execute();
            $transactions = $getTransactions->fetchAll();
        }



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
                <li><a href="stat.html">Statistiques</a></li>
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

        <h1>Historique</h1>

        <div class="centered_message">
            <h3>Choisissez le mois que vous voulez consulter</h3>
            <form method = "GET">
                <p>

                    <label for="table_month"></label>
                    <input id="table_month" type="month" name="table_month">
                </p>
                <p>

                <button type="submit" class="mybutton full_button" name="selection_mois">Voir le budget du mois sélectionné</button>
                </p>

            </form>
        </div>


        <div class="centered_message">
            <?php
                if (isset($_GET['selection_mois'])){
                    echo("<h2>". "Budget du " . $mois_choisi ."-". $annee_choisie ."<h2>");   
                }
            ?>
        </div>

        <table class="u-full-width">
    <tr>
                   <th>Montant</th>
                    <th>Date</th>
                    <th>Catégorie</th>
                    <th>type de transaction</th>
                    <th>Carte utilisée</th>
                    <th>Destinataire/Bénéficiaire</th>
                    <th>Communication</th>
    </tr>

    <?php

    if (isset($_GET['selection_mois'])){
        foreach ($transactions as $transaction) {
                echo "<tr>";
                echo "<td>" . $transaction["montant"] ."€" ."</td>";
                echo "<td>" . $transaction["date_tf"] . "</td>";
                echo "<td>" . $transaction["cat_tf"] . "</td>";
                echo "</tr>";
        }
    }

    ?>

</table>

        <hr>

        <div class="centered_message">
            <p>Total : 4567€</p>
            <button class="mybutton full_button" onclick="">Cloturer ce mois</button>
        </div>
    </div>

    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC
            ULB)
        </p>
    </footer>
</body>

</html>