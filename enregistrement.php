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

        $getCartes = $bdd->prepare("SELECT * FROM budgetsquirrel.carte WHERE niss_util = $niss");
        $getCartes->execute();
        $cartes = $getCartes->fetchAll();
        

        $getCategories = $bdd->prepare("SELECT * FROM budgetsquirrel.categorie_tf");
        $getCategories->execute();
        $categories = $getCategories->fetchAll();
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
        <form action="valider_transaction.php">
            <div class="row">
                <div class="four columns">
                    <p>
                        <label for="montanttransaction">
                            <span>Montant</span>
                        </label>
                        <input type="number" id="montant" name="montant"> <br> 
                        <span class="footsize_text">Vous pouvez entrer un montant négatif (précédé de "-"), qui sera donc considéré comme une dépense, ou positif, et ce sera alors un revenu .</span>

                    </p>

                </div>

                <div class="four columns">

                    <p>
                        <label for="datetransaction">
                            <span>Date</span>
                        </label>
                        <input type="date" id="datetransaction" name="datetransaction">
                    </p>

                </div>

                <div class="four columns">
                    <p>
                        <label for="catégorietransact">
                            <span>Catégorie de transaction</span>
                        </label>
                        <select name="carte_select">
                             <span>Carte utilisée</span>
                            <?php foreach ($categories as $categorie): ?>
                                <option value ="<?php echo $categorie['nom_tf']?>"><?php echo $categorie['nom_tf']?></option>
                             <?php endforeach; ?>
                        </select> <br> 
                        <span class="footsize_text"><?php echo $categorie['description_tf']?></span> <!-- Voir comment adapter la description à chaque sélection : javascript?-->

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

            <input type="submit" class="mybutton full_button" value="Ajouter cette transaction">
        </form>
    </div>

    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC
            ULB)
        </p>
    </footer>
</body>

</html>