<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inscription</title>
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
        
        if(isset($_POST['inscription'])) {

            $nom = htmlspecialchars($_POST['nom']);
            $prenom = htmlspecialchars($_POST['prenom']);
            $date_naissance = htmlspecialchars($_POST['date_naissance']);
            $niss = htmlspecialchars($_POST['niss']);
            $photo = htmlspecialchars($_POST['photo']);

            // If toutes les données obligatoires sont remplies : 
            if ($nom != null&& $prenom != null&& $date_naissance != null&& $niss != null&& !empty($photo)){

                $check = $bdd->prepare("SELECT count(1) as total FROM utilisateur WHERE niss = $niss");  //Verif si il n'existe pas déjà un utilisateur avec ce niss
                $check->execute();
                $donnees = $check-> fetch();

                if ($donnees['total'] == 0){
                    $query = $bdd->prepare("INSERT INTO budgetsquirrel.utilisateur (nom, prenom, niss, date_naissance, photo) 
                    VALUES (?,?,?,?,?)");
                    $query->execute(array($nom, $prenom, $niss, $date_naissance, $photo));
                }
                else if ($donnees['total'] == 1){
                    ;
                }
            }
            else{

                echo "Veuillez remplir tous les champs et sélectionner une photo de profil.";
            }
            

        }


        $getUtilisateurs = $bdd->prepare("SELECT * FROM budgetsquirrel.utilisateur");
        $getUtilisateurs->execute();
        $utilisateurs = $getUtilisateurs->fetchAll();
		?>

    <header>
        
    <div class="logo-container">
                <img src="img/logo.png">
                <a href="index.html">Budget Squirrel</a>
            </div>

    </header>

    <div class="container">
        <h1>Créez votre profil</h1>
        <form method="POST">

            <section>
                <h2>Complétez les informations suivantes :</h2>
                <div class="row">
                    <div class="six columns">
                        <p>
                            <label for="prenom">
                               <span>Prénom</span>
                            </label>
                            <input type="text" id="prenom" name="prenom"><br>
                        </p>
                    </div>

                    <div class="six columns">
                        <p>
                            <label for="nom">
                                
                                <span>Nom</span>
                            </label>
                            <input type="text" id="nom" name="nom"><br>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="six columns">
                        <p>
                            <label for="date_naissance">
                                <span>Date de naissance</span>
                            </label>
                            <input type="date" id="date_naissance" name="date_naissance"><br>
                        </p>
                    </div>
                    
                    <div class="six columns">
                        <p>
                            <label for="niss">
                                <span>NISS</span>
                            </label>
                            <input type="text" id="niss" name="niss" , placeholder="00 00 00 000 00" minlength="11" maxlength="11" pattern="[0-9]{2}[.\- ]{0,1}[0-9]{2}[.\- ]{0,1}[0-9]{2}[.\- ]{0,1}[0-9]{3}[.\- ]{0,1}[0-9]{2}"><br>
                            <span class="footsize_text">Rappel : un NISS est composé de exactement 11 chiffres (vous n'avez pas besoin d'introduire d'espace ou d'autres caractères)</span>
                        </p>
                    </div>
                </div>

            </section>

            <section>
                <fieldset class="pic-selector">
                    <legend>Choisissez votre photo de profil :</legend>
                    <input type="radio" id="politecat.jpg" name="photo" value="politecat.jpg">
                    <label for="politecat.jpg" class="drinkpic-cc politecat"></label>

                    <input type="radio" id="froggy.png" name="photo" value="froggy.png">
                    <label for="froggy.png" class="drinkpic-cc froggy"></label>

                    <input type="radio" id="raccoon.jpg" name="photo" value="raccoon.jpg">
                    <label for="raccoon.jpg" class="drinkpic-cc raccoon"></label>

                    <input type="radio" id="gollum.jpg" name="photo" value="gollum.jpg">
                    <label for="gollum.jpg" class="drinkpic-cc gollum"></label>
                </fieldset>
            </section> <br>

            <button type="submit" class="mybutton full_button" name="inscription">Inscription</button>
        </form>



        <div> 
            <?php
            if(isset($_POST['inscription'])){

                if ($nom != null&& $prenom != null&& $date_naissance != null&& $niss != null&& !empty($photo)){
                    if ($donnees['total'] == 0){
                        echo("Votre inscription a bien été enregistrée !");
                    echo("<a href='connexion.php'>Connectez-vous</a>");
                    $_POST['inscription'] = null; // Evite la réécriture des données à chaque refresh
                    }
                    else if($donnees['total'] == 1){
                    echo("Votre inscription n'a pas été enregistrée, car un utilisateur utilise déjà ce NISS. Veuillez réésayer en entrant un autre NISS, ou ");
                    echo("<a href='connexion.php'>connectez-vous</a>");
                    $_POST['inscription'] = null; // Evite la réécriture des données à chaque refresh
                  } 

                }

               
            }
            ?>
        
        </div>

    </div>
    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC
            ULB)
        </p>
    </footer>
</body>

</html>