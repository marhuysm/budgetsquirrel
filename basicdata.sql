/*SCRIPT D'AJOUT DE DONNÉES DANS LA DB BUDGETSQUIRREL

À EXECUTER APRÈS initdb.sql */

USE budgetsquirrel;

/*Ajout de quelques utilisateurs de base */

INSERT INTO utilisateur (nom, prenom, niss, date_naissance, photo) VALUES ('Mustermann', 'Erika', '19940307121', '1994-03-07', 'politecat.jpg');

INSERT INTO utilisateur (nom, prenom, niss, date_naissance, photo) VALUES ('Tartempion', 'Jean-Jacques', '19831206323', '1983-12-06', 'raccoon.jpg');

INSERT INTO utilisateur (nom, prenom, niss, date_naissance, photo) VALUES ('Baggins', 'Bilbo', '19650922666', '1965-09-22', 'gollum.jpg');

/* Ajout de cartes à chaque utilisateur */

INSERT INTO carte (nom_carte, numero_carte, type_carte, niss_util) VALUES ("Visa Gandalf", "34566666666611111", "Visa", "19650922666");

INSERT INTO carte (nom_carte, numero_carte, type_carte, niss_util) VALUES ("Mastercard Comté", "2342111111111111", "Mastercard", "19650922666");

INSERT INTO carte (nom_carte, numero_carte, type_carte, niss_util) VALUES ("Maestro Moria", "1891455555555555", "Mastercard", "19650922666");

INSERT INTO carte (nom_carte, numero_carte, type_carte, niss_util) VALUES ("Prepaid Bpost", "1895678930000576", "Visa Prepaid", "19940307121");

INSERT INTO carte (nom_carte, numero_carte, type_carte, niss_util) VALUES ("Maestro BNP", "10004566667654320", "Maestro", "19940307121");

INSERT INTO carte (nom_carte, numero_carte, type_carte, niss_util) VALUES ("Visa BNP", "1000409797979797", "Visa", "19940307121");

INSERT INTO carte (nom_carte, numero_carte, type_carte, niss_util) VALUES ("Visa CBC", "2340000000065432", "Visa", "19831206323");

INSERT INTO carte (nom_carte, numero_carte, type_carte, niss_util) VALUES ("Maestro CBC", "19000054678888325", "Maestro", "19831206323");



/*Ajout de transactions. il faudrait peut-être trouver comment faire un check / automatiser côté serveur la création de budget? 
Pareil pour l'automatisation pour les types de transaction, voir si c'est possible*/


/*Ajout des catégories disponibles */

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('accessoires', 'Objets variés : décoration, sacs, bijoux,...');

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('vêtements', 'Tout ce qui est porté : veste, pull, chaussures...');

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('courses alimentaires', 'Nourriture : ingrédients, fruits, épices, pâtes...');

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('frais médicaux', 'Opérations médicales, visites chez le généraliste, chez le dentiste, frais de pharmacie...');

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('transports', "Transports en commun, essence, abonnement de train...");

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('culture', "Livres, ticket de cinéma, entrée de musée...");

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('restauration', "Tout ce qui est mangé à l'exterieur : sandwich, café, restaurant,... ");

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('voyage', "Billet d'avion, agence de voyage...");

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('salaire', "Salaire régulier reçu ou versé");

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('facture', "Facture d'électricité, internet...");

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('revenu indépendant', "Revenu indépendant de quelque nature qu'il soit");

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('pension', "Pension reçue ou versée");

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('argent de poche', "Argent de poche reçu ou donné");

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('remboursement', "Argent reçu ou donné dans le contexte d'un remboursement");

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('cadeau', "Argent reçu ou donné dans le contexte d'un cadeau");

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('chèque-repas', "Chèques-repas reçus ou dépensés");

INSERT INTO categorie_tf (nom_tf, description_tf) VALUES ('autre', "Toute autre nature de transaction");
