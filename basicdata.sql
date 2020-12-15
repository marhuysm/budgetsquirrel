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

/*Ajout de diverses transactions, et des infos de type de transaction, pour chaque utilisateur*/

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-12-12','-55','19650922666','restauration');

INSERT INTO tf_cash(num_tf) VALUES (LAST_INSERT_ID());

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-12-03','132','19650922666','remboursement');

INSERT INTO tf_cash(num_tf) VALUES (LAST_INSERT_ID());

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-11-17','-156','19650922666','voyage');

INSERT INTO tf_cash(num_tf) VALUES (LAST_INSERT_ID());

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-12-02','300','19650922666','salaire');

INSERT INTO tf_virement(num_tf, communication, destbenef) VALUES (LAST_INSERT_ID(), 'salaire décembre', 'Thorin');

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-11-02','600','19650922666','salaire');

INSERT INTO tf_virement(num_tf, communication, destbenef) VALUES (LAST_INSERT_ID(), 'salaire novembre', 'Thorin');

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-10-02','900','19650922666','salaire');

INSERT INTO tf_virement(num_tf, communication, destbenef) VALUES (LAST_INSERT_ID(), 'salaire octobre', 'Thorin');

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-10-25','-30','19650922666','culture');

INSERT INTO tf_carte(num_tf, numero_carte) VALUES (LAST_INSERT_ID(), '2342111111111111');

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-12-15','-178','19650922666','vêtements');

INSERT INTO tf_carte(num_tf, numero_carte) VALUES (LAST_INSERT_ID(), '34566666666611111');


INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-12-02','-167','19831206323','restauration');

INSERT INTO tf_cash(num_tf) VALUES (LAST_INSERT_ID());

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-12-10','600','19831206323','remboursement');

INSERT INTO tf_cash(num_tf) VALUES (LAST_INSERT_ID());

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-11-07','-156','19831206323','frais médicaux');

INSERT INTO tf_cash(num_tf) VALUES (LAST_INSERT_ID());

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-12-02','1658','19831206323','salaire');

INSERT INTO tf_virement(num_tf, communication, destbenef) VALUES (LAST_INSERT_ID(), 'salaire décembre', 'Commune Laeken');

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-11-02','1658','19831206323','salaire');

INSERT INTO tf_virement(num_tf, communication, destbenef) VALUES (LAST_INSERT_ID(), 'salaire novembre', 'Commune Laeken');

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-10-02','1658','19831206323','salaire');

INSERT INTO tf_virement(num_tf, communication, destbenef) VALUES (LAST_INSERT_ID(), 'salaire octobre', 'Commune Laeken');

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-10-15','-75','19831206323','culture');

INSERT INTO tf_carte(num_tf, numero_carte) VALUES (LAST_INSERT_ID(), '2340000000065432');

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-12-15','-18','19831206323','transports');

INSERT INTO tf_carte(num_tf, numero_carte) VALUES (LAST_INSERT_ID(), '19000054678888325');


INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-12-05','-197','19940307121','remboursement');

INSERT INTO tf_cash(num_tf) VALUES (LAST_INSERT_ID());

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-12-10','630','19940307121','cadeau');

INSERT INTO tf_cash(num_tf) VALUES (LAST_INSERT_ID());

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-11-07','-15','19940307121','transports');

INSERT INTO tf_cash(num_tf) VALUES (LAST_INSERT_ID());

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-12-02','2400','19940307121','salaire');

INSERT INTO tf_virement(num_tf, communication, destbenef) VALUES (LAST_INSERT_ID(), 'salaire décembre', 'Cour des Comptes');

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-11-02','2400','19940307121','salaire');

INSERT INTO tf_virement(num_tf, communication, destbenef) VALUES (LAST_INSERT_ID(), 'salaire novembre', 'Cour des Comptes');

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-10-02','2400','19940307121','salaire');

INSERT INTO tf_virement(num_tf, communication, destbenef) VALUES (LAST_INSERT_ID(), 'salaire octobre', 'Cour des Comptes');

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-10-15','-375','19940307121','facture');

INSERT INTO tf_carte(num_tf, numero_carte) VALUES (LAST_INSERT_ID(), '1895678930000576');

INSERT INTO transaction_financiere(date_tf, montant, niss_util, cat_tf) VALUES ('2020-12-15','-89','19940307121','restauration');

INSERT INTO tf_carte(num_tf, numero_carte) VALUES (LAST_INSERT_ID(), '1000409797979797');

