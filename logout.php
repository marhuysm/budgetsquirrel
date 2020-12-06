
<?php

if (!isset($_SESSION))
  {
    session_start();
  }   
   ?>

<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <title>Budget Squirrel</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/skeleton.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/app.css">
</head>



<body>

<?php 
        $_SESSION = array();
        session_destroy();
  ?>

    <div class="landing_header">
        <div class="glass_box">
            <img class="biglogo" src="img/logo.png">
            <h1>Budget Squirrel</h1>
        </div>
    </div>

    <div class="container landing_container">
        <div class="centered_message">
            <div>
                <h2> Merci d'avoir utilisé Budgetsquirrel !</h2> <br>
                <h3> Vous vous êtes bien déconnecté de votre profil. </h3> <br>
            </div>
            <div >
                <button onclick="goTo('index.html')" class="mybutton full_button">Retourner à la page d'accueil</button>
                
            </div>
            
    
        </div>
    </div>


    <footer>
        <p>Ce projet a été développé dans le cadre du cours de conception et gestion de banques de données (MA2 STIC ULB)
        </p>
    </footer>
</body>

<script type="text/javascript" src="utils.js"></script>

</html>