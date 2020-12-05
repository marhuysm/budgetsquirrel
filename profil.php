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

            <ul>
                <li><a href="profil.html">Profil</a></li>
                <li><a href="">Déconnexion</a></li>
            </ul>
        </nav>

    </header>
    <div class="container">
        <div class="row">
            <h1>Mes informations</h1>

            <div class="four columns">
                <p><?php echo $connexion['nom']; echo ","; echo $connexion['prenom'] ?></p>
            </div>

            <div class="four columns">
                <p>NISS</p>
            </div>

            <div two="four columns">
                <p>jj/mm/aaaa</p>
            </div>

        </div>

        <div>
            <div>
                <h3>Ajouter une nouvelle carte :</h3>
                <form>
                    <div class="row">
                        <div class="four columns">
                            <p>
                                <label for="nomcarte">
                                    <span>Nom de carte</span>
                                </label>
                                <input type="text" id="nomcarte" name="nomcarte">
                            </p>
                        </div>

                        <div class="four columns">
                            <p>
                                <label for="numerocarte">
                                    <span>N° de carte</span>
                                </label>
                                <input type="text" id="numero" name="numero">
                            </p>
                        </div>

                        <div class="four columns">
                            <p>
                                <label for="typecarte">
                                    <span>Type de carte</span>
                                </label>
                                <input list="typecarte" name="typec">
                                <datalist id="typecarte">
                                    <option value="Visa">
                                    <option value="Mastercard">
                                    <option value="Visa Prepaid">
                                    <option value="Maestro">
                                </datalist>
                            </p>
        
                        </div>

                    </div>






                    <input type="submit" class="mybutton full_button" value="Ajouter">
                </form>
            </div>

            <div>
                <h3>Suprimer une carte :</h3>
                <form>
                    <p>
                        <label for="cartedispo">
                            <span>Carte sélectionnée</span>
                        </label>
                        <input list="cartesdispo" name="cartesuppr">
                    </p>
                    <datalist id="cartesdispo">
                        <option value="Visa Carrefour">
                        <option value="Prepaid Bpost">
                        <option value="Débit BNP">
                    </datalist>
                    <input type="submit" class="mybutton full_button" value="Supprimer">
                </form>
            </div>

        </div>

        <div>
            <h3>Changer de photo de profil :</h3>

            <section>
                <form>
                    <fieldset class="pic-selector">
                        <legend>Sélectionnez votre nouvelle photo de profil :</legend>
                        <input type="radio" id="politecat" name="photoprofil" value="politecat">
                        <label for="politecat" class="drinkpic-cc politecat"></label>
    
                        <input type="radio" id="froggy" name="photoprofil" value="froggy">
                        <label for="froggy" class="drinkpic-cc froggy"></label>
    
                        <input type="radio" id="racoon" name="photoprofil" value="racoon">
                        <label for="racoon" class="drinkpic-cc racoon"></label>
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