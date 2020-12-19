<?php
    session_start();
?>

<!DOCTYPE html>
<html>

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

        $bdd = new PDO('mysql:host=localhost;dbname=budgetsquirrel', 'utilisateur_app', 'user');

        $niss = $_SESSION['niss'];

        $getConnexion = $bdd->prepare("SELECT * FROM budgetsquirrel.utilisateur WHERE niss = $niss");
        $getConnexion-> execute();
        $connexion = $getConnexion->fetch();

        $getCartes = $bdd->prepare("SELECT * FROM budgetsquirrel.carte WHERE niss_util = $niss AND is_deleted = 0");
        $getCartes->execute();
        $cartes = $getCartes->fetchAll();
        
        $getCategories = $bdd->prepare("SELECT * FROM budgetsquirrel.categorie_tf");
        $getCategories->execute();
        $categories = $getCategories->fetchAll();

        if(isset($_POST['ajout_transaction'])){
           
            $montant = htmlspecialchars($_POST['montant_transaction']);
            $date_tf = htmlspecialchars($_POST['date_transaction']);
            $parsed_date = date_parse_from_format('Y-m', $date_tf);
            $mois_tf = $parsed_date["month"]; // mois en int
            $annee_tf = $parsed_date["year"]; // année en int
            $cat_tf = htmlspecialchars($_POST['categorie_transaction']);
            $type_tf = htmlspecialchars($_POST['type_tf']);

            // If toutes les données obligatoires sont remplies : 
             if ($montant != null&& $montant != 0&& $date_tf != null&& $date_tf != null&& !empty($type_tf)){

                // vérifier que toutes les infos sont remplies, en fonction du champs (cash, virement ou carte)

                // Gestion de types de payement : cash, virement ou carte?
                // objectif : en plus de l'enregistrement dans la table transaction_financière, il faut enregistrer chaque num_tf
                // à soit la table tf_cash , soit la table tf_carte, soit la table tf_virement
                // l'info de la catégorie est donnée par type_tf, qui a déjà été récupérée au dessus dans la variable $type_tf.
                // ici, on a bien vérifié que type_tf n'est pas vide (pour pouvoir enregistrer la requête), et de toute façon, 
                // on a déjà défini une valeur par défaut (cash) dans le form,
                // il ne reste plus qu'à enregistrer l'id de la transaction qui vient d'être récupéré dans la bonne table, ainsi que les
                // éventuelles informations supplémentaires

                // si cash : pas d'infos en plus. 

                if ($type_tf == "cash"){
                    
                    try {

                        $query = $bdd->prepare("INSERT INTO budgetsquirrel.transaction_financiere (montant, date_tf, niss_util, cat_tf) 
                        VALUES (?,?,?,?)");
                        $query->execute(array($montant, $date_tf, $niss, $cat_tf));
                        // echo "\nPDO::errorCode(): ", $query->errorCode();// <-- pour retrouver le code erreur
                        if ($query->errorCode() == 45000) {  // 45000 est le numéro qui correspond au contrainte sur la date_naissance dans la création des transactions financieres
                            echo "\n Erreur ", $query->errorCode(), ": La date de création de la transaction que vous essayez d'enregistrer ne peut pas être inférieure à votre date de naissance. </br>";
                        } else {
                            
                            // récup. l'id de la transaction qui vient d'être créée : 
        
                            $query = $bdd->prepare("SELECT LAST_INSERT_ID() as num_tf");
                            $query->execute();
                            $num_tf =  $query->fetch();
                            $num_tf = $num_tf["num_tf"];
        
                            echo ("Transaction enregistrée sous le numéro ". $num_tf . "<br>");

                            // Peu importe le type de transaction créée, récup du budget_id de la transaction créée quand toutes les infos sont là: 

                            $query = $bdd->prepare("SELECT budget_id as budget_id FROM transaction_financiere WHERE num_tf = $num_tf");
                            $query->execute();
                            $budget_id =  $query->fetch();
                            $budget_id = $budget_id["budget_id"];
                            
                            echo("Id du budget pour cette transaction : " . $budget_id. "<br>");
        
                            // enregistrement de la transaction dans la table cash

                            $query = $bdd->prepare("INSERT INTO budgetsquirrel.tf_cash (num_tf)
                            VALUES (?)");
                            $query->execute(array($num_tf));

                            echo ("Merci d'avoir enregistré ce payement en liquide !");

                        }

                    }
                    catch(PDOExecption $e){
                       echo $e->getMessage();
                    }
                } // si virement : d'abord vérifier si l'utilisateur a entré un destinataire/béneficiaire
                else if ($type_tf == "virement"){

                    $destbenef = htmlspecialchars($_POST['destbenef']);
                    $communication = htmlspecialchars($_POST['communication']);

                    if ($destbenef != null){

                        $destbenef = htmlspecialchars($_POST['destbenef']);
                        $communication = htmlspecialchars($_POST['communication']);

                        try {

                            $query = $bdd->prepare("INSERT INTO budgetsquirrel.transaction_financiere (montant, date_tf, niss_util, cat_tf) 
                            VALUES (?,?,?,?)");
                            $query->execute(array($montant, $date_tf, $niss, $cat_tf));
                            if ($query->errorCode() == 45000) {  // 45000 est le numéro qui correspond au contrainte sur la date_naissance dans la création des transactions financieres
                                echo "\n Erreur ", $query->errorCode(), ": La date de création de la transaction que vous essayez d'enregistrer ne peut pas être inférieure à votre date de naissance. </br>";
                            } else {
                                
                                // récup. l'id de la transaction qui vient d'être créée : 

                                $query = $bdd->prepare("SELECT LAST_INSERT_ID() as num_tf");
                                $query->execute();
                                $num_tf =  $query->fetch();
                                $num_tf = $num_tf["num_tf"];
            
            
                                echo ("Transaction enregistrée sous le numéro ". $num_tf . "<br>");

                                // Peu importe le type de transaction créée, récup du budget_id de la transaction créée quand toutes les infos sont là: 

                                $query = $bdd->prepare("SELECT budget_id as budget_id FROM transaction_financiere WHERE num_tf = $num_tf");
                                $query->execute();
                                $budget_id =  $query->fetch();
                                $budget_id = $budget_id["budget_id"];
                                
                                echo("Id du budget pour cette transaction : " . $budget_id . "<br>");

                                $query = $bdd->prepare("INSERT INTO budgetsquirrel.tf_virement (num_tf, communication, destbenef)
                                VALUES (?,?,?)");
                                $query->execute(array($num_tf, $communication, $destbenef));

                                echo ("Merci d'avoir enregistré ce virement !");
                            }
        
                        }
                        catch(PDOExecption $e){
                           echo $e->getMessage();
                        }
                    }
                    else {
                        echo "N'oubliez pas d'ajouter au moins un destinataire.";
                    }

                } //pareil pour carte
                else if($type_tf == "carte"){
                    if (isset($_POST['carte_select'])){

                        $numero_carte = htmlspecialchars($_POST['carte_select']);

                        try {

                            $query = $bdd->prepare("INSERT INTO budgetsquirrel.transaction_financiere (montant, date_tf, niss_util, cat_tf) 
                            VALUES (?,?,?,?)");
                            $query->execute(array($montant, $date_tf, $niss, $cat_tf));
                            if ($query->errorCode() == 22007) {  // 22007 est le numéro qui correspond au contrainte sur la date_naissance dans la création des transactions financieres
                                echo "\n Erreur ", $query->errorCode(), ": La date de création de la transaction que vous essayez d'enregistrer ne peut pas être inférieure à votre date de naissance. </br>";
                            } else {
                                // récup. l'id de la transaction qui vient d'être créée : 
            
                                $query = $bdd->prepare("SELECT LAST_INSERT_ID() as num_tf");
                                $query->execute();
                                $num_tf =  $query->fetch();
                                $num_tf = $num_tf["num_tf"];
            
            
                                echo ("Transaction enregistrée sous le numéro ". $num_tf . "<br>");

                                // Peu importe le type de transaction créée, récup du budget_id de la transaction créée quand toutes les infos sont là: 

                                $query = $bdd->prepare("SELECT budget_id as budget_id FROM transaction_financiere WHERE num_tf = $num_tf");
                                $query->execute();
                                $budget_id =  $query->fetch();
                                $budget_id = $budget_id["budget_id"];
                                
                                echo("Id du budget pour cette transaction : " . $budget_id . "<br>");

                                echo("Merci d'avoir enregistré cette transaction par carte !");

                                $query = $bdd->prepare("INSERT INTO budgetsquirrel.tf_carte (num_tf, numero_carte )
                                VALUES (?, ?)");
                                $query->execute(array($num_tf, $numero_carte));
                            }
        
                        }
                        catch(PDOExecption $e){
                           echo $e->getMessage();
                        }
                    }
                    else{
                        echo "N'oubliez pas de sélectionner votre carte.";
                    }                        
                }                
    
            }
            else{
                echo("Vous n'avez pas entré toutes les valeurs pour la transaction.");
                echo("Veillez à entrer le montant, la date, la catégorie et le type de transaction effectuée.");

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
                <li><a href="historique.php">Historique</a></li>
                <li><a href="enregistrement.php">Enregistrement</a></li>
                <li><a href="stat.php">Statistiques</a></li>
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
                        <input type="number" step="0.01" id="montant_transaction" name="montant_transaction"> <br> 
                        <span class="footsize_text">Vous pouvez entrer un montant négatif (précédé de "-"), qui sera donc considéré comme une dépense, ou positif, et ce sera alors un revenu .</span>
                        <span class="footsize_text">Vous n'avez pas besoin d'entrer de monnaie (€).</span>

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
                        
                    </p>
                </div>

            </div>

            <h3>Sélectionnez le type de transaction effectuée :</h3>

            <div class="transaction-selection">
                <!-- rajout d'une fonction javascript togglePayment() pour gérer l'affichage des sections correspondant au type de paiement. -->
                <script type="text/javascript">
                    function togglePayment() {
                        var section_carte = document.getElementById('section_carte');
                        var section_virement = document.getElementById('section_virement');

                        if(document.getElementById('cash').checked) {
                            section_carte.style.display = 'none';
                            section_virement.style.display = 'none';
                        }
                        else if(document.getElementById('virement').checked) {
                            section_carte.style.display = 'none';
                            section_virement.style.display = 'block';
                        }
                        else if(document.getElementById('carte').checked) {
                            section_carte.style.display = 'block';
                            section_virement.style.display = 'none';
                        }
                    }
                </script>

                <input type="radio" id="cash" name="type_tf" value="cash" onClick="togglePayment()" checked="checked">
                <label for="cash">Payement en liquide</label>
    
                <input type="radio" id="virement" name="type_tf" value="virement" onClick="togglePayment()">
                <label for="virement">Virement bancaire</label>
    
                <input type="radio" id="carte" name="type_tf" value="carte" onClick="togglePayment()">
                <label for="carte">Payement par carte</label> <br>
        
            </div>
            
            <fieldset id="section_carte" style="display: none;">
                <legend>Payement par carte</legend>
                <p>
                <select name="carte_select">
                    <span>Carte utilisée</span>
                        <option value="" disabled selected>Choisissez votre carte</option> <!-- permet d'éviter d'avoir une carte sélectionnée par défaut dans le form -->
                        <?php foreach ($cartes as $carte): ?>
                            <option value ="<?php echo $carte['numero_carte']?>"><?php echo $carte['nom_carte']?></option>
                    <?php endforeach; ?>
                
                </select>
                </p>
            </fieldset>

            <fieldset id="section_virement" style="display: none;">
                <legend>Virement bancaire</legend>
                <p>
                    <label for="destbenef">
                        <span>Bénéficiaire / Destinataire</span>
                    </label> <br>
                    <input type="text" id="destbenef" name="destbenef" placeholder="Nom du bénéficiaire">
                    </input>
                </p>
                <p>
                    <label for="communication">
                        <span>Communication</span>
                    </label> <br>
                    <textarea id="communication" name="communication" rows="4" cols="50"
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