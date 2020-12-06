<?php
    session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Profil</title>
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

        if(isset($_POST['ajout_carte'])) {

            $nom_carte = htmlspecialchars($_POST['nom_carte']);
            $numero_carte = htmlspecialchars($_POST['numero_carte']);
            $type_carte = htmlspecialchars($_POST['type_carte']);
            $niss_util = $_SESSION['niss'];
            
            $query = $bdd->prepare("INSERT INTO budgetsquirrel.carte (nom_carte, numero_carte, type_carte, niss_util) 
            VALUES (?,?,?,?)");
            $query->execute(array($nom_carte, $numero_carte, $type_carte, $niss_util));
        }

        $getCartes = $bdd->prepare("SELECT * FROM budgetsquirrel.carte WHERE niss_util = $niss");
        $getCartes->execute();
        $cartes = $getCartes->fetchAll();

        if (isset($_POST['suppr_carte'])) {
            $nom_carte = htmlspecialchars($_POST['carte_suppr']);

            $query = $bdd->prepare("DELETE FROM budgetsquirrel.carte WHERE niss_util = $niss AND nom_carte = $nom_carte");
            $query->execute();

        }
        
  ?>
    <header>

        <nav class=menu>
            <div class="logo-container">
                <img src="img/logo.png">
                <a href="homepage.php">Budget Squirrel</a>
            </div>

            <ul class="pages">
                <li><a href="historique.html">Historique</a></li>
                <li><a href="enregistrement.html">Enregistrement</a></li>
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
        <div class="row">
            <h1>Mes informations</h1>

            <div class="four columns">
                <p><?php echo $connexion['nom']; echo ", "; echo $connexion['prenom'] ?></p>
            </div>

            <div class="four columns">
                <p><?php echo $connexion['niss'] ?></p>
            </div>

            <div two="four columns">
                <p><?php echo $connexion['date_naissance'] ?></p>
            </div>

        </div>

        <div>
            <div>
                <h3>Ajouter une nouvelle carte :</h3>
                <form method="POST">
                    <div class="row">
                        <div class="four columns">
                            <p>
                                <label for="nom_carte">
                                    <span>Nom de carte</span>
                                </label>
                                <input type="text" id="nom_carte" name="nom_carte">
                            </p>
                        </div>

                        <div class="four columns">
                            <p>
                                <label for="numero_carte">
                                    <span>N° de carte</span>
                                </label>
                                <input type="text" id="numero_carte" name="numero_carte">
                            </p>
                        </div>

                        <div class="four columns">
                            <p>
                                <label for="type_carte">
                                    <span>Type de carte</span>
                                </label>
                                <select name="type_carte">
                                    <option value="Visa">Visa</option>
                                    <option value="Mastercard">Mastercard</option>
                                    <option value="Visa Prepaid">Visa Prepaid</option>
                                    <option value="Maestro">Maestro</option>
                                </select>
                            </p>
        
                        </div>

                    </div>

                    <button type="submit" class="mybutton full_button" name="ajout_carte">Ajouter</button>
                </form>
                <div> 
            <?php
            if(isset($_POST['ajout_carte'])) {
                echo("Votre carte ".$nom_carte." a bien été enregistrée !");
                $_POST['ajout_carte'] = null; // Evite la réécriture des données à chaque refresh

            } 
            ?>
         <br>
        </div>

            </div>

            <div>
                <h3>Suprimer une carte :</h3>
                <form method = "POST">
                <select name="carte_suppr">
                        <?php foreach ($cartes as $carte): ?>
                            <option value ="<?php echo $carte['nom_carte']?>"><?php echo $carte['nom_carte']?></option>
                    <?php endforeach; ?>
                        </select>
                </p>
                <button type="submit" class="mybutton full_button" name="suppr_carte">Supprimer</button>
                </form>

                <?php
            if(isset($_POST['suppr_carte'])) {
                echo("Votre carte ".$nom_carte." a bien été supprimée !");
                $_POST['suppr_carte'] = null; // Evite la réécriture des données à chaque refresh

            } 
            ?>
            </div>

        </div>

        <div>
            <h3>Changer de photo de profil :</h3>

            <section>
                <form>
                    <fieldset class="pic-selector">
                        <legend>Sélectionnez votre nouvelle photo de profil :</legend>
                        <input type="radio" id="politecat.jpg" name="photoprofil" value="politecat.jpg">
                        <label for="politecat.jpg" class="drinkpic-cc politecat"></label>
    
                        <input type="radio" id="froggy.png" name="photoprofil" value="froggy.png">
                        <label for="froggy.png" class="drinkpic-cc froggy"></label>
    
                        <input type="radio" id="raccoon.jpg" name="photoprofil" value="raccoon.jpg">
                        <label for="raccoon.jpg" class="drinkpic-cc raccoon"></label>
                    </fieldset> <br>

                    <input type="submit" class="mybutton full_button" value="Changer">

                </form>

            </section> <br>
        </div>

    </div>
    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC
            ULB)
        </p>
    </footer>
</body>

</html>