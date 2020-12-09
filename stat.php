<?php
    session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8"/>
    <title>Statistiques</title>
    <link rel="preconnect" href="https://fonts.gstatic.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500&display=swap"
        rel="stylesheet"/>
    <link rel="stylesheet" href="css/skeleton.css"/>
    <link rel="stylesheet" href="css/normalize.css"/>
    <link rel="stylesheet" href="css/app.css"/>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
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

        
        // les requêtes suivantes doivent ss doute devenir des vues
        // et il faut trouver un meilleur moyen de calculer le bilan par mois

        $getStatMois = $bdd->prepare("SELECT * FROM stat_depenses_revenus_mois");
        $getStatMois->execute();
        $fetchedStatMois = $getStatMois->fetchAll();

        $getStatCategories = $bdd->prepare("SELECT * FROM stat_cat");
        $getStatCategories->execute();
        $fetchedStatCat = $getStatCategories->fetchAll();

        $getStatTypes = $bdd->prepare("SELECT * FROM stat_types ");
        $getStatTypes->execute();
        $fetchedStatTypes = $getStatTypes->fetchAll();
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

            <table class="u-full-width">
                <thead>
                    <tr>
                        <th>Mois</th>
                        <th>Année</th>
                        <th>Nombre de dépenses</th>
                        <th>Total des dépenses</th>
                        <th>Nombre de revenus</th>
                        <th>Total des revenus</th>
                        <th>Bilan</th>  
                        <th></th>
                    </tr>
                    </thead>

                <tbody>
                    <?php

                     foreach ($fetchedStatMois as $stat_mois) {
                        echo "<tr>";
                        echo "<td>" . $stat_mois["mois"] ."</td>";
                        echo "<td>" . $stat_mois["annee"] . "</td>";
                        echo "<td>" . $stat_mois["nb_depenses"] . "</td>";
                        echo "<td>" . $stat_mois["bilan_depenses_mois"]  ." €". "</td>";
                        echo "<td>" . $stat_mois["nb_revenus"] . "</td>";
                        echo "<td>" . $stat_mois["bilan_revenus_mois"] ." €"."</td>";
                        echo "</tr>";

                        }

                    ?>
                </tbody>

            </table>

             <!-- ajout des graphes en javascript -->
            <canvas id="graph1"> 
        
            </canvas>
        </div>

        <div>
            <h2>Répartition totale par catégorie</h2>

            <table class="u-full-width">
                <thead>
                    <tr>
                        <th>Catégorie</th>
                        <th>Description</th>
                        <th>Nombre d'utilisations</th>
                        <th>Bilan de dépenses</th>
                        <th>Bilan de revenus</th>
                        <th></th>
                    </tr>
                    </thead>

                <tbody>
                    <?php

                     foreach ($fetchedStatCat as $stat_cat) {
                        echo "<tr>";
                        echo "<td>" . $stat_cat["nom_tf"] ."</td>";
                        echo "<td>" . $stat_cat["description_tf"] . "</td>";
                        echo "<td>" . $stat_cat["nb_utilisations"] . "</td>";
                        echo "<td>" . $stat_cat["bilan_depenses_cat"] . " €"."</td>";
                        echo "<td>" . $stat_cat["bilan_revenus_cat"] ." €"."</td>";
                        echo "</tr>";

                        }

                    ?>
                </tbody>

            </table>
        </div>

        <div>
            <h2>Répartition totale par type de transaction</h2>

            <table class="u-full-width">
                <thead>
                    <tr>
                        <th>Type de transaction</th>
                        <th>Nombre d'utilisations</th>
                        <th>Bilan de dépenses</th>
                        <th>Bilan de revenus</th>
                        <th></th>
                    </tr>
                    </thead>

                <tbody>
                    <?php

                     foreach ($fetchedStatTypes as $stat_type) {
                        echo "<tr>";
                        echo "<td>" . $stat_type["typetf"] ."</td>";
                        echo "<td>" . $stat_type["nb_utilisations"] . "</td>";
                        echo "<td>" . $stat_type["total_depenses_type"] . " €"."</td>";
                        echo "<td>" . $stat_type["total_revenus_type"] ." €"."</td>";
                        echo "</tr>";

                        }
                        ?>
                        </tbody>
        
                    </table>
        </div>

    </div>
    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC
            ULB)
        </p>
    </footer>
</body>

</html>

<!-- ajout des graphes en javascript -->
<script type="text/javascript">
    var ctx = document.getElementById('graph1').getContext('2d');

    var bilan = <?php echo json_encode($bilan); ?>;
    
    var mois = <?php
                    foreach ($fetchedStatMois as $stat_mois) {
                        echo json_encode(array_values($stat_mois["mois"]), JSON_FORCE_OBJECT);
                    }
                    ?>

    var dep = <?php
                    foreach ($fetchedStatMois as $stat_mois) {
                        echo json_encode(array_values($stat_mois["bilan_depenses_mois"]), JSON_FORCE_OBJECT);
                    }
                    ?>

    var rev = <?php
                    foreach ($fetchedStatMois as $stat_mois) {
                        echo json_encode(array_values($stat_mois["bilan_revenus_mois"]), JSON_FORCE_OBJECT);
                    }
                    ?>
    
    var data = {
        labels: [mois],
        datasets: [
            {
                label: 'depenses',
                backgroundColor: 'rgb(255,99,132)',
                borderColor: 'rgb(255,99,132)',
                data: [dep]
            },
            {
                label: 'revenus',
                backgroundColor: 'rgb(155,99,132)',
                borderColor: 'rgb(155,99,132)',
                data: [rev]
            }
        ]
    } 

    var options = {
        responsive: true
    }
    
    var config = {
        type: 'bar',
        data: data,
        options: options
    }

    var graph1 = new Chart(ctx, config);
</script>
