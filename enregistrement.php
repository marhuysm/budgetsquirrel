<?php
    session_start();
?>

<!DOCTYPE html>
<html>

    <script src= 
    "https://code.jquery.com/jquery-1.12.4.min.js"> 
     $(document).ready(function() { 
                $('input[type="radio"]').click(function() { 
                    var inputValue = $(this).attr("value"); 
                    var targetBox = $("." + inputValue); 
                    $(".selectt").not(targetBox).hide(); 
                    $(targetBox).show(); 
                }); 
            }); 
        </script> 

<head>
    <meta charset="utf-8">
    <title>Enregistrement</title>
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

        //$getCartes = $bdd->prepare("SELECT * FROM budgetsquirrel.carte WHERE niss_util = $niss");
        //si suppression logique, alors 
        $getCartes = $bdd->prepare("SELECT * FROM budgetsquirrel.carte WHERE niss_util = $niss AND is_deleted = 0");
        $getCartes->execute();
        $cartes = $getCartes->fetchAll();
        

        $getCategories = $bdd->prepare("SELECT * FROM budgetsquirrel.categorie_tf");
        $getCategories->execute();
        $categories = $getCategories->fetchAll();

        if(isset($_POST['ajout_transaction'])){
           
            $montant = htmlspecialchars($_POST['montant_transaction']);
            $date_tf = htmlspecialchars($_POST['date_transaction']);
            $cat_tf = htmlspecialchars($_POST['categorie_transaction']);

            //ajout d'une verification de l'existence d'un budget en cours pour la date donnée
            //ajoud d'une verification de l'existence d'un reste du bilan précedent
            //Pour le travail sur la partie budget il faut envisager une contrainte relative au clôture du budget, envisageant que
            //un utilisateur peut clôturer son budget pendant le mois en cours, MAIS
            //si l'utilisateur ne clôture pas son budget, le système le fera le premier jour du mois suivant au minuit
            $check = $bdd->prepare("SELECT count(1) as total FROM budgetsquirrel.budget_mensuel WHERE statut = 'EnCours')");  //Verif si il n'existe pas déjà un budget
            $check->execute();
            $budget = $check-> fetch();

            $check = $bdd->prepare("SELECT MAX(budget_id), reste FROM budgetsquirrel.budget_mensuel WHERE statut = 'Clos' GROUP BY reste)"); //Verif si il n'y a pas un reste du bilan précedent
            $check->execute();
            $resteBilanPrecedent = $check-> fetch();   // <- il faut corriger le query puis qu'il retourne une seule valeur avec fetch, vu que le select porte sur deux colonnes

            if ($budget['total'] == 0){
                $query = $bdd->prepare("INSERT INTO budgetsquirrel.budget_mensuel (mois, annee, statut, bilan, reste, niss_util) 
                VALUES (?,?,?,?,?,?)");
                $query->execute(array(MONTH($date_tf), YEAR($date_tf), 'EnCours', $resteBilanPrecedent + $montant, 0, $niss));
            }
            
            //recuperer le budget_id du budget_mensuel en cours
            $check = $bdd->prepare("SELECT budget_id FROM budgetsquirrel.budget_mensuel WHERE statut = 'EnCours')");  //Verif si il n'existe pas déjà un budget
            $check->execute();
            $budgetId = $check-> fetch();

            // if (isset($_POST['montant_transaction']) && isset($_POST['date_transaction']) && isset($_POST['categorie_transaction'])){
             if ($montant != null&& $montant != 0 && isset($_POST['date_transaction']) && isset($_POST['categorie_transaction'])){
                try {
                    /* Il faut créer et initialiser le budget_id s'il n'existe pas encore, 
                    et lier la transaction au bon budget_id correspondant au mois et à l'année enregistrée */
                    $query = $bdd->prepare("INSERT INTO budgetsquirrel.transaction_financiere (montant, date_tf, niss_util, budget_id, cat_tf) 
                    VALUES (?,?,?,?,?)");
                    $query->execute(array($montant, $date_tf, $niss, $budgetId, $cat_tf));
    
                    echo ("transaction enregistrée");
                }
                catch(Error $e){
                   echo $e->getMessage();
                }
            }
            else{
                echo("Vous n'avez pas entré toutes les valeurs pour la transaction");
            }         
           
        }
  ?>

    <header>

        <nav class=menu>
            <div class="logo-container">
                <img src="img/logo.png">
                <a href="homepage.php">Budget Squirrel</a>
            </div>
        
            <ul class = "pages">
                <li><a href="historique.html">Historique</a></li>
                <li><a href="enregistrement.php">Enregistrement</a></li>
                <li><a href="stat.html">Statistiques</a></li>
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

    <!-- Selection hide / parties grisées en fonction du bouton de type de transaction -->
    <div class="container">
        <h1>Enregistrer une nouvelle transaction</h1>
        <h2>Complétez les informations suivantes :</h2>
        <form method = "POST">
            <div class="row">
                <div class="four columns">
                    <p>
                        <label for="montant_transaction">
                            <span>Montant</span>
                        </label>
                        <input type="number" id="montant_transaction" name="montant_transaction"> <br> 
                        <span class="footsize_text">Vous pouvez entrer un montant négatif (précédé de "-"), qui sera donc considéré comme une dépense, ou positif, et ce sera alors un revenu .</span>

                    </p>

                </div>

                <div class="four columns">

                    <p>
                        <label for="date_transaction">
                            <span>Date</span>
                        </label>
                        <input type="date" id="date_transaction" name="date_transaction">
                    </p>

                </div>

                <div class="four columns">
                    <p>
                        <label for="categorie_transaction">
                            <span>Catégorie de transaction</span>
                        </label>
                        <select name="categorie_transaction">
                             <span>Carte utilisée</span>
                            <?php foreach ($categories as $categorie): ?>
                                <option value ="<?php echo $categorie['nom_tf']?>"><?php echo $categorie['nom_tf']?></option>
                             <?php endforeach; ?>
                        </select> <br> 
                        <span class="footsize_text"><?php echo $categorie['description_tf']?></span> 
                        
                        <!-- Voir comment adapter la description à chaque sélection : javascript?-->

                    </p>
                </div>

            </div>

            <h3>Sélectionnez le type de transaction effectuée :</h3>

            <div class="transaction-selection">
                
                <input type="radio" id="cash" name="typetransact" value="cash">
                <label for="cash">Payement en liquide</label>
    
                <input type="radio" id="virement" name="typetransact" value="virement">
                <label for="virement">Virement banquaire</label>
    
                <input type="radio" id="carte" name="typetransact" value="carte">
                <label for="carte">Payement par carte</label> <br>
        
            </div>
            
            <fieldset>
                <legend>Payement par carte</legend>
                <p>
                <select name="carte_select">
                    <span>Carte utilisée</span>
                        <?php foreach ($cartes as $carte): ?>
                            <option value ="<?php echo $carte['nom_carte']?>"><?php echo $carte['nom_carte']?></option>
                    <?php endforeach; ?>
                
                </select>
                </p>
            </fieldset>

            <fieldset>
                <legend>Virement banquaire</legend>
                <p>
                    <label for="benefdest">
                        <span>Bénéficiaire</span>
                    </label> <br>
                    <input type="text" id="benefdest" name="benefdest" placeholder="Nom du bénéficiaire">
                    </input>
                </p>
                <p>
                    <label for="benefdest">
                        <span>Communication</span>
                    </label> <br>
                    <textarea id="benefdest" name="benefdest" rows="4" cols="50"
                        placeholder="Rentrez éventuellement la communication utilisée"></textarea>
                </p>
            </fieldset>
            <br>

            <button type="submit" class="mybutton full_button" name="ajout_transaction">Ajouter cette transaction</button>
        </form>
    </div>

    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC
            ULB)
        </p>
    </footer>
</body>

</html>