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

        $bdd = new PDO('mysql:host=localhost;dbname=budgetsquirrel', 'utilisateur_app', 'user');

        $niss = $_SESSION['niss'];

        if(isset($_POST['ajout_carte'])) {

            $nom_carte = htmlspecialchars($_POST['nom_carte']);
            $numero_carte = htmlspecialchars($_POST['numero_carte']);
            $type_carte = htmlspecialchars($_POST['type_carte']);
            $niss_util = $_SESSION['niss'];

            //Verif si il n'existe pas déjà une carte avec ce num et cet utilisateur
            
            $check = $bdd->prepare("SELECT count(1) as total FROM carte WHERE numero_carte = $numero_carte AND niss_util = $niss");  
            $check->execute();
            $donnees = $check-> fetch();
            $fetchedDonnees = $donnees['total'];
            
            if ($fetchedDonnees == 0){
                $query = $bdd->prepare("INSERT INTO budgetsquirrel.carte (nom_carte, numero_carte, type_carte, niss_util) 
                VALUES (?,?,?,?)");
                $query->execute(array($nom_carte, $numero_carte, $type_carte, $niss_util));
            }
            else if ($fetchedDonnees == 1){
                ;
            }
           
        }

        //$getCartes = $bdd->prepare("SELECT * FROM budgetsquirrel.carte WHERE niss_util = $niss");
        //Si suppression logique (voir lignes 61-65)

        if (isset($_POST['suppr_carte'])) {

            $numero_carte = htmlspecialchars($_POST['carte_suppr']); 
            
            //PB : foreign key empêche la suppression : comment gérer la supr de carte?
            //Solution proposée: supression logique en lieu d'une supression phisique, cad:
            //Ajouter dans la table "carte" une colonne is_deleted avec des valeurs possibles 0 ou 1
            //Inclure dans les queries qui font appel au table carte la condition WHERE is_deleted = 0  
            //Dans le cas present ici au lieu de "DELETE FROM budgetsquirrel.carte.... on peut mettre
            
            $is_deleted = true;

            $query = $bdd->prepare("UPDATE budgetsquirrel.carte SET is_deleted = :is_deleted WHERE niss_util = $niss AND numero_carte = :numero_carte"); 
            $query->bindParam(':numero_carte' , $numero_carte, PDO::PARAM_STR);
            $query->bindParam(':is_deleted' , $is_deleted, PDO::PARAM_INT);
            $query->execute();

            // PB : SUPPRESSION NE FONCTIONNE TOUJOURS PAS ?! (je sais pas du tout pourquoi, ça passe jusqu'à la boucle ici et le query fonctionne en sql direct)
            // solution : bind le numero de carte
        }

        if (isset($_POST['changer_photo'])) {

            $photo = htmlspecialchars($_POST['photo']);

            $query = $bdd->prepare("UPDATE budgetsquirrel.utilisateur SET photo = :photo WHERE niss = $niss"); 
            $query->bindParam(':photo', $photo, PDO::PARAM_STR);
            $query->execute();

        }

        $getConnexion = $bdd->prepare("SELECT * FROM budgetsquirrel.utilisateur WHERE niss = $niss");
        $getConnexion-> execute();
        $connexion = $getConnexion->fetch();

        $getCartes = $bdd->prepare("SELECT * FROM budgetsquirrel.carte WHERE niss_util = $niss AND is_deleted = 0");
        $getCartes->execute();
        $cartes = $getCartes->fetchAll();

        $getCartesInactives = $bdd->prepare("SELECT * FROM budgetsquirrel.carte WHERE niss_util = $niss AND is_deleted = 1");
        $getCartesInactives->execute();
        $cartesInactives = $getCartesInactives->fetchAll();
        
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
        <div class="row">
            <h1>Mes informations</h1>

            <div class="four columns">
                <p><?php echo $connexion['nom']; echo ", "; echo $connexion['prenom'] ?></p>
            </div>

            <div class="four columns">
                <p> <b>NISS : </b><?php echo $connexion['niss'] ?></p>
            </div>

            <div two="four columns">
                <p> <b>Date de naissance : </b> <?php echo $connexion['date_naissance'] ?></p>
            </div>

        </div>

        <h3>Mes cartes actuelles : </h3>

        <table class="u-full-width">
            <tr>
                        <th>Nom de la carte</th>
                        <th>Numéro de la carte</th>
                        <th>Type de carte</th>
            </tr>

            <?php

            foreach ($cartes as $carte) {
                echo "<tr>";
                echo "<td>" . $carte["nom_carte"] ."</td>";
                echo "<td>" . $carte["numero_carte"] . "</td>";
                echo "<td>" . $carte["type_carte"] . "</td>";
                echo "</tr>";
            }

            ?>

        </table>

        <h3>Mes anciennes cartes : </h3>

        <table class="u-full-width">
            <tr>
                        <th>Nom de la carte</th>
                        <th>Numéro de la carte</th>
                        <th>Type de carte</th>
            </tr>

            <?php

            foreach ($cartesInactives as $carte) {
                echo "<tr>";
                echo "<td>" . $carte["nom_carte"] ."</td>";
                echo "<td>" . $carte["numero_carte"] . "</td>";
                echo "<td>" . $carte["type_carte"] . "</td>";
                echo "</tr>";
            }

            ?>

        </table>

        <div> 

        </div>

        <div>
            <div>
                <h3>Ajouter une nouvelle carte :</h3>
                <form method= "POST">
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
                                <input type="text"  minlength="16" maxlength="17" pattern="[0-9.]+" id="numero_carte" name="numero_carte">
                                <br>
                                <span class="footsize_text">Votre numéro de carte doit être composé de 16 ou 17 chiffres</span>
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

                if ($fetchedDonnees == 0){
                    echo("Votre carte ".$nom_carte." a bien été enregistrée !");
                }
                else if ($fetchedDonnees == 1){
                    echo("Vous avez déjà enregistré une carte avec ce numéro.");
                }

                $_POST['ajout_carte'] = null; // Evite la réécriture des données à chaque refresh
            } 
            ?>
         <br>
         <br>
        </div>

            </div>

            <div>
                <h3>Suprimer une carte :</h3>
                <form method = "POST">
                <select name="carte_suppr">
                        <?php foreach ($cartes as $carte): ?>
                            <option value ="<?php echo $carte['numero_carte']?>"><?php echo $carte['nom_carte']?></option>
                    <?php endforeach; ?>
                        </select>
                        <br>
                        <br>
                        <p>
                        <button type="submit" class="mybutton full_button" name="suppr_carte">Supprimer</button>
                        </p>
                
                </form>

                <?php
            if(isset($_POST['suppr_carte'])) {

                echo("Votre carte a bien été supprimée !");
                $_POST['suppr_carte'] = null; // Evite la réécriture des données à chaque refresh

            } 
            ?>
            <br>
            <br>
            </div>

        </div>

        <div>
            <h3>Changer de photo de profil :</h3>

            <section>
                <form method = "POST">
                    <fieldset class="pic-selector">
                        <legend>Sélectionnez votre nouvelle photo de profil :</legend>
                        <input type="radio" id="politecat.jpg" name="photo" value="politecat.jpg">
                        <label for="politecat.jpg" class="drinkpic-cc politecat"></label>
    
                        <input type="radio" id="froggy.png" name="photo" value="froggy.png">
                        <label for="froggy.png" class="drinkpic-cc froggy"></label>
    
                        <input type="radio" id="raccoon.jpg" name="photo" value="raccoon.jpg">
                        <label for="raccoon.jpg" class="drinkpic-cc raccoon"></label>

                        <input type="radio" id="gollum.jpg" name="photo" value="gollum.jpg">
                        <label for="gollum.jpg" class="drinkpic-cc gollum"></label>

                    </fieldset> 

                    <button type="submit" class="mybutton full_button" name="changer_photo">Changer</button>

                </form>

                <?php
                        if (isset($_POST['changer_photo'])) {

                            echo("Photo de profil changée");
                        }
                ?>

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