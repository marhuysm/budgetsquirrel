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
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
</head>

<body>

<?php 

        $bdd = new PDO('mysql:host=localhost;dbname=budgetsquirrel', 'utilisateur_app', 'user');

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

        $getStatMois = $bdd->prepare("SELECT * FROM stat_depenses_revenus_mois WHERE niss_util = $niss");
        $getStatMois->execute();
        $fetchedStatMois = $getStatMois->fetchAll();

        $getStatCategories = $bdd->prepare("SELECT * FROM stat_cat WHERE niss_util = $niss");
        $getStatCategories->execute();
        $fetchedStatCat = $getStatCategories->fetchAll();

        $getStatTypes = $bdd->prepare("SELECT * FROM stat_types WHERE niss_util = $niss");
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
                        <th>No. dépenses</th>
                        <th>Total dépenses</th>
                        <th>No. revenus</th>
                        <th>Total revenus</th>
                        <th>No. transactions</th>  
                        <th>Total transactions</th>
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
                        echo "<td>" . $stat_mois["nb_total"] . "</td>";
                        echo "<td>" . $stat_mois["bilan_total_mois"] ." €"."</td>";
                        echo "</tr>";

                        }

                    ?>
                </tbody>

            </table>

             <!-- ajout des graphes en javascript -->
             <div class = "section">
                <canvas id="graph_stat_mois"></canvas>
            </div>
            <!-- PB comment convertir le result set obtenu pour les requetes SQL en javascript - le retour maintenant n'est pas un array mais un string concaténé? --> 
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

             <!-- ajout des graphes en javascript -->
             <div class = "section">
                <span class="canvas_stat">
                    <canvas id="graph_stat_cat_depenses"></canvas>
                </span>
                <span class="canvas_stat">
                    <canvas id="graph_stat_cat_revenus"></canvas>
                </span>
            </div>
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

            <!-- ajout des graphes en javascript -->
            <!-- <canvas id="repartition_type_transaction"></canvas> -->
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
<!-- graph statistiques mois -->
<script type="text/javascript">
   
    var ctx_stat_mois = document.getElementById('graph_stat_mois').getContext('2d');

    var bilan = <?php echo json_encode($bilan); ?>;

    var mois = [<?php
                    $statMois = array();
                    foreach ($fetchedStatMois as $stat_mois) {
                        array_push($statMois, "'" . $stat_mois["mois"] . "/" . $stat_mois["annee"] . "'");
                    }
                    echo implode(", ", $statMois);
                    ?>];
    console.log("mois : " + mois);

    var dep = [<?php
                    $depMois = array();
                    foreach ($fetchedStatMois as $stat_mois) {
                        array_push($depMois, $stat_mois["bilan_depenses_mois"]);
                    }
                    echo implode(", ", $depMois);
                    ?>];
    console.log("dep : " + dep);

    var rev = [<?php
                    $revMois = array();
                    foreach ($fetchedStatMois as $stat_mois) {
                        array_push($revMois, $stat_mois["bilan_revenus_mois"]);
                    }
                    echo implode(", ", $revMois);
                    ?>];
    console.log("rev : " + rev);
    
    var data_stat_mois = {
            labels: mois,
            datasets: [
                {
                    label: 'depenses',
                    backgroundColor: 'rgb(255,99,132)',
                    borderColor: 'rgb(255,99,132)',
                    data: dep
                },
                {
                    label: 'revenus',
                    backgroundColor: 'rgb(155,99,132)',
                    borderColor: 'rgb(155,99,132)',
                    data: rev
                }
            ]
    } 

    var options = {
        responsive: true
    }
    
    var config_stat_mois = {
        type: 'bar',
        data: data_stat_mois,
        options: options
    }

var graph_stat_mois = new Chart(ctx_stat_mois, config_stat_mois);


// ajout des graphes en javascript
// graph statistiques mois
var ctx_stat_cat_depenses = document.getElementById('graph_stat_cat_depenses').getContext('2d');
var ctx_stat_cat_revenus = document.getElementById('graph_stat_cat_revenus').getContext('2d');
var categorie = [<?php
                    $statCat = array();
                    foreach ($fetchedStatCat as $stat_cat) {
                        array_push($statCat, "'" . $stat_cat["nom_tf"] . "'");
                    }
                    echo implode(", ", $statCat);
                   ?>];

var colors = [];
for (i = 0; i < categorie.length; i++) {
    r = Math.floor(Math.random() * 200);
    g = Math.floor(Math.random() * 200);
    b = Math.floor(Math.random() * 200);
    c = 'rgb(' + r + ', ' + g + ', ' + b + ')';
    colors.push(c);
};

console.log("categorie : " + categorie);

var dep_cat = [<?php
                $depCat = array();
                foreach ($fetchedStatCat as $stat_cat) {
                    array_push($depCat, $stat_cat["bilan_depenses_cat"]);
                }
                echo implode(", ", $depCat);
                ?>];
console.log("dep_cat : " + dep_cat);

var rev_cat = [<?php
                $revCat = array();
                foreach ($fetchedStatCat as $stat_cat) {
                    array_push($revCat, $stat_cat["bilan_revenus_cat"]);
                }
                echo implode(", ", $revCat);
                ?>];
console.log("rev_cat : " + rev_cat);

var data_stat_cat_depenses = {
    labels: categorie,
    datasets: [
        {
            label: 'depenses',
            backgroundColor: colors,
            data: dep_cat
        }
    ]
} 

var data_stat_cat_revenus = {
    labels: categorie,
    datasets: [
        {
            label: 'revenus',
            backgroundColor: colors,
            data: rev_cat
        }
    ]
}

var options_dep = {
    responsive: true,
    legend: {
        display: false
    },
    title: {
            display: true,
            text: 'Dépenses par catégorie'
        }
}

var options_rev = {
    responsive: true,
    legend: {
        display: false
    },
    title: {
            display: true,
            text: 'Revenus par catégorie'
        }
}

var config_stat_cat_depenses = {
    type: 'pie',
    data: data_stat_cat_depenses,
    options: options_dep
}

var config_stat_cat_revenus = {
    type: 'pie',
    data: data_stat_cat_revenus,
    options: options_rev
}

var graph_stat_cat_depenses = new Chart(ctx_stat_cat_depenses, config_stat_cat_depenses);
var graph_stat_cat_revenus = new Chart(ctx_stat_cat_revenus, config_stat_cat_revenus);

// ajout des graphes en javascript
// graph repartition_type_transaction
// var ctx_stat_type = document.getElementById('repartition_type_transaction').getContext('2d');
// var typeTransaction = [<?php
//                     $statType = array();
//                     foreach ($fetchedStatTypes as $stat_type) {
//                         array_push($statType, "'" . $stat_type["typetf"] . "'");
//                     }
//                     echo implode(", ", $statType);
//                     ?>];
// console.log("type de Transaction : " + typeTransaction);

// var nbUtilisations = [<?php
//                 $statType = array();
//                     foreach ($fetchedStatTypes as $stat_type) {
//                     array_push($statType, $stat_type["nb_utilisations"]);
//                 }
//                 echo implode(", ", $statType);
//                 ?>];
// console.log("nombre utilisations : " + nbUtilisations);

// var totalRevType = [<?php
//                 $revType = array();
//                 foreach ($fetchedStatTypes as $stat_type) {
//                     array_push($revType, $stat_type["total_revenus_type"]);
//                 }
//                 echo implode(", ", $revType);
//                 ?>];
// console.log("Total revenus type : " + totalRevType);

// var totalDepType = [<?php
//                 $depType = array();
//                 foreach ($fetchedStatTypes as $stat_type) {
//                     array_push($depType, $stat_type["total_depenses_type"]);
//                 }
//                 echo implode(", ", $depType);
//                 ?>];
// console.log("Total depenses type : " + totalDepType);

// var data_stat_type = {
//     labels: typeTransaction,
//     datasets: [
//         {
//             label: 'bilan',
//             backgroundColor: 'rgb(255,99,132)',
//             borderColor: 'rgb(255,99,132)',
//             data: [{
//                 x: typeTransaction,
//                 y: nbUtilisations,
//                 r: totalDepType
//             }]
//         // }, 
//         // {
//         //     label: 'revenus',
//         //     backgroundColor: 'rgb(155,99,132)',
//         //     borderColor: 'rgb(155,99,132)',
//         //     data: [{
//         //         x: typeTransaction,
//         //         y: totalRevType,
//         //         r: nbUtilisations
//         //     }]
//         }
//     ]
// }

// var options = {
//     responsive: true
// }

// var config_stat_type = {
//     type: 'bubble',
//     data: data_stat_type,
//     options: options
// }

// var graph_stat_type = new Chart(ctx_stat_type, config_stat_type);

</script>
