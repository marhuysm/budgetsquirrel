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

        // une fois que l'utilisateur a sélectionné le mois qu'il veut consulter, on récupère les données du get
        // ainsi que le mois et l'année , afin de pouvoir afficher le bon budget mensuel par la suite.

        
        if(isset($_GET['selection_mois'])){
            try{
                $table_month = htmlspecialchars($_GET['table_month']);
                $parsed_date = date_parse_from_format('Y-m', $table_month);
                $mois_choisi = $parsed_date["month"]; // mois en int
                $annee_choisie = $parsed_date["year"]; // année en int
                $budget_id = 0;

        
                // là, on a juste le mois et l'année choisies. Maintenant, il faut lier cette info à la table budget.
                // On selectionne donc le budget correspondant à la date : 
                $getBudget = $bdd->prepare("SELECT budget_id as budget_id FROM budget_mensuel WHERE mois = $mois_choisi AND annee = $annee_choisie");
                $getBudget->execute();
                $fetchedBudget = $getBudget->fetch();
                $budget_id = $fetchedBudget["budget_id"];
        
                // Maintenant qu'on a accès au budget_id correspondnant au mois choisi, on attribue une nouvelle valeur à budget_id : 

                if(empty($budget_id)){
                    
                    echo "Vous n'avez pas encore créé de transaction pour ce mois";
                    // dans le cas ou on a pas de budget, budget_id == 0 : 
                    // PB ici : comment éviter le message d'erreur lié ? (lié au fait que $budget_id = $fetchedBudget["budget_id"] renvoie
                    // Trying to access array offset on value of type bool in /opt/lampp/htdocs/budgetsquirrel/historique.php)

                }
                else{

                    echo($budget_id);

                    // sélection de toutes les transactions pour le mois, pour ensuite les afficher dans un tableau

                    $getTransactions = $bdd->prepare("SELECT * FROM budgetsquirrel.transaction_financiere WHERE niss_util = $niss AND budget_id = $budget_id");
                    $getTransactions->execute();
                    $transactions = $getTransactions->fetchAll();

                    // total à afficher : besoin d'écrire la somme des transactions pour le mois dans la colonne bilan de budget mensuel
                    
                    //calcul de la somme : 

                    $GetTotalMois = $bdd->prepare("SELECT SUM(montant) as total FROM budgetsquirrel.transaction_financiere WHERE niss_util = $niss AND budget_id = $budget_id");
                    $GetTotalMois->execute();
                    $fetchedTotal = $GetTotalMois->fetch();
                    $total_mois = $fetchedTotal["total"];

                    //réécriture de bilan dans la table budget_mensuel : 

                    $WriteTotal = $bdd->prepare("UPDATE budgetsquirrel.budget_mensuel SET bilan = $total_mois WHERE budget_id = $budget_id");
                    $WriteTotal->execute();

                    // Est-ce mieux de directement utiliser $total_mois dans le total, ou d'appeler la valeur de la colonne bilan du 
                    //budget mensuel pour afficher le total de chaque mois? Pour l'instant, j'utilise total_mois
                }
        
            }
            // catch inutile?
            catch(Exception $e){
                echo "in catch";
                echo 'Message: ' .$e->getMessage();
            }
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

            // si l'utilisateur a sélectionné un mois ET qu'un budget_id valide a été défini
            if (isset($_GET['selection_mois']) && (isset($budget_id))){
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
            <?php 
                if (isset($_GET['selection_mois']) && isset($budget_id)){
                    echo("<div class='centered_message'>");
                    echo("<p>Total: " . $total_mois ."€</p>");

                    echo("<button class='mybutton full_button' onclick=''>Cloturer ce mois</button>");

                }
                else{
                    ;
                }
                ?>

        </div>

        

    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC
            ULB)
        </p>
    </footer>
</body>

</html>