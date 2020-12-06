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

        $getTransactions = $bdd->prepare("SELECT * FROM budgetsquirrel.transaction_financiere WHERE niss_util = $niss");
        $getTransactions->execute();
        $transactions = $getTransactions->fetchAll();

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
            <form>
                <p>

                    <label for="table-month"></label>
                    <input id="table-month" type="month" name="table-month">
                </p>
                <p>

                    <input type="submit" class="mybutton full_button" value="Go">
                </p>

            </form>
        </div>

        <div class="centered_message">
            <h2>Mois Année</h2>
        </div>

        <table class="u-full-width">
            <thead>
                <tr>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Catégorie</th>
                    <th>type de transaction</th>
                    <th>Carte utilisée</th>
                    <th>Destinataire/Bénéficiaire</th>
                    <th>Communication</th>

                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>+56€</td>
                    <td>23/11/2018</td>
                    <td>testestest</td>
                    <td>testtest</td>
                    <td>testestestes</td>
                    <td>testest</td>
                    <td>testestestest</td>

                </tr>
            </tbody>


        </table>

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

    foreach ($transactions as $transaction) {
        echo "<tr>";
        echo "<td>" . $transaction["montant"] ."€" ."</td>";
        echo "<td>" . $transaction["date_tf"] . "</td>";
        echo "<td>" . $transaction["cat_tf"] . "</td>";
        echo "</tr>";
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