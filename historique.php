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

        $bdd = new PDO('mysql:host=localhost;dbname=budgetsquirrel', 'utilisateur_app', 'user');

        $niss = $_SESSION['niss'];

        $getConnexion = $bdd->prepare("SELECT * FROM budgetsquirrel.utilisateur WHERE niss = $niss");
        $getConnexion-> execute();
        $connexion = $getConnexion->fetch();

        // l'objectif de la page : 

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
                $getBudget = $bdd->prepare("SELECT budget_id as budget_id FROM budget_mensuel WHERE mois = $mois_choisi AND annee = $annee_choisie AND niss_util = $niss");
                $getBudget->execute();
                $fetchedBudget = $getBudget->fetch();
                $budget_id = $fetchedBudget["budget_id"] ?? ''; //utilisation de '??' (null coalescing operator) pour remplacer un budget qui est Null par un budget vide -> ''
                
                // si l'utilisateur demande à supprimer une transaction :

                if (isset($_POST['suppr_tf'])){

                    $num_tf_suppr = $_POST['suppr_tf'];

                    $query=$bdd->prepare("DELETE FROM budgetsquirrel.transaction_financiere WHERE num_tf = $num_tf_suppr AND niss_util = $niss");
                    $query->execute();

                }

                // Maintenant qu'on a accès au budget_id correspondant au mois choisi, on attribue une nouvelle valeur à budget_id : 

                if(!empty($budget_id)){

                    //Requête de l'historique et du bilan correspondant au budget de l'utilisateur

                    $GetTotalMois = $bdd->prepare("SELECT bilan_total_mois as total FROM budgetsquirrel.stat_depenses_revenus_mois WHERE budget_id = $budget_id");
                    $GetTotalMois->execute();
                    $fetchedTotal = $GetTotalMois->fetch();
                    $total_mois = $fetchedTotal["total"] ?? 0; //pour traiter le cas d'utilisation ou l'utilisateur supprime l'unique transaction (revenu ou depense) d'un budget. ça evite l'erreur générée par un bilan qui est NULL
                 
                    $getHistorique = $bdd->prepare("SELECT * FROM budgetsquirrel.historique_v WHERE niss_util = $niss AND budget_id = $budget_id");
                    $getHistorique->execute();
                    $historique = $getHistorique->fetchAll();

                }
            
        
            }

            catch(Exception $e){
                echo 'Caught exception: ',  $e->getMessage(), "\n";
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

        <form method = "POST">
            <table class="table-hist u-full-width">
                <thead>
                    <tr>
                        <th>Montant</th>
                        <th>Date</th>
                        <th>Catégorie</th>
                        <th>type de transaction</th>
                        <th>Carte utilisée</th>
                        <th>Destinataire/Bénéficiaire</th>
                        <th>Communication</th>
                        <th></th>
                    </tr>
                    </thead>

                <tbody>
                    <?php

                    // si l'utilisateur a sélectionné un mois ET qu'un budget_id valide a été défini
                    //split de "if" pour garder la selection de mois, mais ajouter un check sur le budget vide
                    //ajout d'une ligne sur les huit colonnes de la table pour afficher le message d'erreur'
                    if (isset($_GET['selection_mois'])) {
                        if (!empty($budget_id)) {
                            foreach ($historique as $hist_tf) {
                                echo "<tr>";
                                echo "<td>" . $hist_tf["montant"] ."€" ."</td>";
                                echo "<td>" . $hist_tf["date_tf"] . "</td>";
                                echo "<td>" . $hist_tf["cat_tf"] . "</td>";
                                echo "<td>" . $hist_tf["typetf"] . "</td>";
                                echo "<td>" . $hist_tf["nom_carte"] . "</td>";
                                echo "<td>" . $hist_tf["destbenef"] . "</td>";
                                echo "<td>" . $hist_tf["communication"] . "</td>";
                                echo "<td><button type='submit' class='suppr_button' name='suppr_tf' value=". $hist_tf["num_tf"]."></button></td>";
                                echo "</tr>";

                            }
                        }
                        else {
                            echo "<tr>";
                            echo "<td colspan=8>" . "Vous n'avez pas encore créé de transaction pour ce mois" ."</td>";
                            echo "</tr>";
                        }
                    }

                    ?>
                </tbody>

            </table>
        </form>    

        <div class="centered_message">
        
           <?php 
                if (isset($_GET['selection_mois']) && !empty($budget_id)){
                    echo("<div class='centered_message'>");
                    echo("<p><b>Total: " . $total_mois ."€</b></p>");

                }
                else{
                    ;
                }
                ?>

        </div>

    </div>
    </div>

        

    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC
            ULB)
        </p>
    </footer>
</body>

</html>
