/*SCRIPT DE CREATION DE LA DB BUDGETSQUIRREL */

DROP DATABASE IF EXISTS budgetsquirrel;

CREATE DATABASE budgetsquirrel CHARACTER SET 'utf8';

USE budgetsquirrel;

CREATE TABLE utilisateur(
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        niss VARCHAR(11) NOT NULL,
        date_naissance DATE NOT NULL,
        photo VARCHAR(100) NOT NULL DEFAULT 'froggy.png',
        CONSTRAINT pk_utilisateur PRIMARY KEY(niss) ,
        CONSTRAINT chk_niss CHECK (LENGTHB(niss) = 11),
        CONSTRAINT chk_photo CHECK (photo IN ('froggy.png','gollum.jpg','politecat.jpg', 'raccoon.jpg')),
        CONSTRAINT UNIQUE (niss, date_naissance) -- clé composée à utiliser dans la table transaction financiere
        );
-- Vérification de l'écriture côté bdd : OK!
        
CREATE TABLE carte(
        nom_carte VARCHAR(255) NOT NULL,
        numero_carte VARCHAR(17) NOT NULL,
        type_carte VARCHAR(100) NOT NULL,
    	niss_util VARCHAR(11), 
        is_deleted INT DEFAULT 0,
       	CONSTRAINT fk_niss_util_carte FOREIGN KEY(niss_util) REFERENCES utilisateur(niss), 
-- ajouter un DROP CASCADE à chaque FK niss pour gérer le cas où l'utilisateur admin veut supprimer un utilisateur de la DB?
    	CONSTRAINT pk_carte PRIMARY KEY(numero_carte),
        CONSTRAINT chk_numero_carte_low CHECK (LENGTHB(numero_carte) >= 16),
        CONSTRAINT chk_numero_carte_high CHECK (LENGTHB(numero_carte) <= 17), 
-- CONTRAINTE <= 17 potentiellement inutile. quand on test avec un numéro de carte de 18 chiffres, MySQL retourne "Data too long" grace au VARCHAR(17)
        CONSTRAINT uc_carte UNIQUE (numero_carte, niss_util),
        CONSTRAINT chk_type_carte CHECK (type_carte IN('Visa', 'Visa Prepaid', 'Maestro', 'Mastercard'))
        );
        
-- Vérif de l'écriture côté bdd : OK!

CREATE TABLE budget_mensuel(
        budget_id INT NOT NULL AUTO_INCREMENT,
        mois INT NOT NULL,
        annee INT NOT NULL,
        bilan FLOAT, 
        niss_util VARCHAR(11) NOT NULL,
        date_naissance_util DATE NOT NULL,
        CONSTRAINT fk_niss_util_budget FOREIGN KEY (niss_util) REFERENCES utilisateur(niss),
        CONSTRAINT pk_budget_mensuel PRIMARY KEY(budget_id),
        CONSTRAINT uc_budget_mensuel UNIQUE (mois, annee, niss_util),
        CONSTRAINT fk_date_util_bm FOREIGN KEY (niss_util, date_naissance_util) REFERENCES utilisateur (niss, date_naissance)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
        CONSTRAINT CHECK (annee >= YEAR(date_naissance_util))
        );
        
        -- ! Pour l'instant, bilan de budget_mensuel = null
        -- ? ajouter un trigger ou une fk après avoir créé la vue qui donne le bilan?
        -- ALTER TABLE budgetsquirrel.budget_mensuel
	-- ADD CONSTRAINT fk_bilan_from_view FOREIGN KEY (bilan) REFERENCES stat_depenses_revenus_mois(bilan_total_mois)
        
CREATE TABLE categorie_tf(
        nom_tf VARCHAR(100),
        description_tf TEXT,
        CONSTRAINT pk_categorie_tf PRIMARY KEY(nom_tf)
        );

        -- Faut-il des contraintes uniques pour la table transaction_financiere ?
        -- à voir lundi mais a priori je ne crois pas

CREATE TABLE transaction_financiere(
        num_tf INT NOT NULL AUTO_INCREMENT,
        date_tf DATE,
        montant FLOAT,
   	    niss_util VARCHAR(11) NOT NULL,
        date_naissance_util DATE NOT NULL, -- pb au niveau de la db: obligé d'entrer la date de naissance pour enregistrer une transaction
        -- Errreur au niveau de la db : Requête : INSERT INTO `transaction_financiere`(`date_tf`, `montant`, `niss_util`, `date_naissance_util`, `cat_tf`) VALUES ('2020-12-12' ,'-30', '19650922666', '1965-09-22', 'accessoires')
        -- renvoie: #1364 - Field 'date_naissance_util' doesn't have a default value lorsque l'utilisateur n'a pas encore de transaction ajoutée ou si le budget mensuel n'existe pas encore
        budget_id INT,
        cat_tf VARCHAR(100),
        CONSTRAINT fk_niss_util_tf FOREIGN KEY (niss_util) REFERENCES utilisateur(niss),
        CONSTRAINT fk_budget_id FOREIGN KEY (budget_id) REFERENCES budget_mensuel(budget_id),
        CONSTRAINT fk_cat_tf FOREIGN KEY (cat_tf) REFERENCES categorie_tf(nom_tf),
        CONSTRAINT pk_transaction_financiere PRIMARY KEY (num_tf),
        CONSTRAINT fk_date_util_tf FOREIGN KEY (niss_util, date_naissance_util) REFERENCES utilisateur (niss, date_naissance) 
                ON DELETE CASCADE
                ON UPDATE CASCADE,
        CONSTRAINT CHECK (date_tf > date_naissance_util)
            -- CONSTRAINT chk_date_tf CHECK(date_tf > utilisateur.date_naissance) -- PB : champ date_naissance inconnu dans CHECK
            -- ajouter contrainte check date = mois et annee du budget_id
            -- le niss_util de transaction_financiere correspond au niss_util du budget_mensuel > check ou trigger?
            -- SOLUTION Proposée: https://stackoverflow.com/questions/3880698/can-a-check-constraint-relate-to-another-table
            --   Make a compound key of the utilisateur table's key combined with the date.naissance columns, 
            --   then use this compound key for your foreign key reference in your transaction_financiere table. 
            --   This will give you the ability to write the necessary row-level CHECK constraints in the transaction_financiere table e.g.
            -- in table utilisateur under
            -- date_naissance DATE NOT NULL,
            -- add
            -- UNIQUE (niss, date_naissance)
            -- then in table transaction_financiere add
            -- after niss_util a new column
            -- date_naissance_util DATE,
            -- FOREIGN KEY (niss_util, date_naissance_util) REFERENCES utilisateur (niss, date_naissance)
            -- ON DELETE CASCADE
            -- ON UPDATE CASCADE,
            -- date_naissance_util NOT NULL,
            -- CHECK (date_tf > tf_date_naissance)
            -- PB CONSEQUENTE côté client: une fois la contrainte date_tf ajouté: l'utilisateur peut toujours enregistré une transaction avec une date 
            -- anterieure à sa date de naissance MAIS
            -- la transaction n'est pas enregistré dans la db (constraint failure, c'est correct)
            -- le budget pour l'année inferieur à sa naissance est crée
            -- l'utilisateur ne sait pas que sa transaction n'a pas pu être enregistré et a un message de confirmation de création dans l'app
        );


CREATE TABLE tf_cash(
        num_tf INT,
        CONSTRAINT pk_tf_cash PRIMARY KEY (num_tf),
        CONSTRAINT fk_num_tf_cash FOREIGN KEY (num_tf) REFERENCES transaction_financiere(num_tf) ON DELETE CASCADE
        );

CREATE TABLE tf_virement(
        num_tf INT,
        communication TEXT,
        destbenef VARCHAR(255) NOT NULL,
        CONSTRAINT pk_tf_virement PRIMARY KEY (num_tf),
        CONSTRAINT fk_num_tf_virement FOREIGN KEY (num_tf) REFERENCES transaction_financiere(num_tf) ON DELETE CASCADE
        );

CREATE TABLE tf_carte(
        num_tf INT,
        numero_carte VARCHAR(255),
        CONSTRAINT pk_tf_carte PRIMARY KEY (num_tf),
        CONSTRAINT fk_num_tf_carte FOREIGN KEY (num_tf) REFERENCES transaction_financiere(num_tf) ON DELETE CASCADE,
        CONSTRAINT fk_numero_carte FOREIGN KEY (numero_carte) REFERENCES carte(numero_carte)
        -- le niss_util.numero carte utilisé appartient toujours à l'utilisateur qui crée la transaction > Trigger ou check?
        );
    
-- ENGINE=InnoDB;

        -- Trigger de création de budget s'il n'existe pas déjà pour le mois, année, utilisateur donné

        -- Idée à mettre en place : lors de la création d'une nouvelle transaction financière coté db, on entre le montant, la date, le niss et la catégorie
        -- Ensuite, le trigger vérifie si un budget_mensuel existe déjà pour cette combinaison de NISS, mois et année
        -- Si oui, l'id du budget mensuel correspondant est extrait et ajouté à la tf créée
        -- Si non, d'abord, le budget mensuel correspondant au triplet niss-mois-année est créée
        -- Puis, son id est extrait, et enfin, ajouté à la transaction créée
        -- Comment mettre en place tout ça ?
        -- Pour l'instant, ça fonctionne côté appli : les vérifications sont faites manuellement en php

            -- CREATE TRIGGER trg_before_ajout_tf BEFORE INSERT 
            -- ON transaction_financiere FOR EACH ROW
            --     BEGIN
            --      IF (SELECT COUNT(*) FROM budget_mensuel 
            --             WHERE mois = MONTH(NEW.date_tf)  
            --             AND annee = YEAR(NEW.date_tf)
            --             AND niss_utils = (NEW.niss_util)) = 0 
            --             THEN
            --         INSERT INTO budget_mensuel(mois, annee, niss) 
            --         values(MONTH(NEW.date_tf), YEAR(NEW.date_tf), NEW.niss_util);
            --     END IF;

            --     UPDATE transaction_financiere
            --     SET budget_id = NEW.budget_id
            -- END
            -- ;

            -- CREATE TRIGGER trg_before_ajouttf BEFORE INSERT ON transaction_financiere FOR EACH ROW
            -- BEGIN
            -- SET @COUNT=(SELECT COUNT(*) FROM budget_mensuel 
            --                WHERE (mois = MONTH(NEW.date_tf)  
            --                AND annee = YEAR(NEW.date_tf)
            --                AND niss_util = (NEW.niss_util)) );
            -- IF @COUNT = 0 THEN
            --     INSERT INTO budget_mensuel(mois, annee, niss) 
            --            VALUES (MONTH(NEW.date_tf), YEAR(NEW.date_tf), NEW.niss_util)
            -- END IF;
            -- END;
        -- SOLUTION TROUVÉE: problème de syntaxe expliqué ici https://dev.mysql.com/doc/refman/5.7/en/trigger-syntax.html :
        -- By using the BEGIN ... END construct, you can define a trigger that executes multiple statements. Within the BEGIN block, 
        -- you also can use other syntax that is permitted within stored routines such as conditionals and loops. However, just as for 
        -- stored routines, if you use the mysql program to define a trigger that executes multiple statements, it is necessary to 
        -- redefine the mysql statement delimiter so that you can use the ; statement delimiter within the trigger definition. 

DELIMITER //
CREATE TRIGGER trg_before_ajout_tf BEFORE INSERT 
ON transaction_financiere 
FOR EACH ROW
BEGIN
DECLARE rowcount INT;

SELECT COUNT(*) INTO rowcount FROM budget_mensuel 
WHERE mois = MONTH(NEW.date_tf) AND annee = YEAR(NEW.date_tf) AND niss_util = NEW.niss_util;

IF rowcount = 0 THEN
    INSERT INTO budget_mensuel(mois, annee, niss_util) 
    VALUES (MONTH(NEW.date_tf), YEAR(NEW.date_tf), NEW.niss_util);
END IF;
END;//
DELIMITER ;

        -- Potentiellement, si possible : trigger d'écriture de transaction_financiere dans la bonne table? Comment gérer ça du côté de la db?
        -- ça me semble pas possible, seul le trigger d'ajout automatique à un budget_mensuel est possible
        -- à confirmer lundi 

CREATE OR REPLACE VIEW historique_v
AS
SELECT tf.num_tf, tf.date_tf, tf.montant, tf.niss_util, tf.budget_id, tf.cat_tf, tfct.numero_carte, tfv.destbenef, tfv.communication, c.nom_carte,
    CASE 
        WHEN tf.num_tf = tfv.num_tf THEN "virement"
        WHEN tf.num_tf = tfct.num_tf THEN "carte"
        WHEN tf.num_tf = tfcs.num_tf THEN "cash"
        ELSE " "
    END as typetf
FROM budgetsquirrel.transaction_financiere tf
LEFT JOIN budgetsquirrel.tf_virement tfv
ON tf.num_tf = tfv.num_tf
LEFT JOIN budgetsquirrel.tf_carte tfct
ON tf.num_tf = tfct.num_tf
LEFT JOIN budgetsquirrel.tf_cash tfcs
ON tf.num_tf = tfcs.num_tf
LEFT JOIN budgetsquirrel.carte c
ON tfct.numero_carte = c.numero_carte
;

 -- Pour l'écran de statistiques : 

 -- PB de transactions par mois : il faut au moins avoir une dépense et un revenu / mois, sinon le mois ne s'affiche pas
 -- ? solution?
 -- Solution proposée: ajout d'un query pour le bilan total par mois, 
 -- (ça nous permet d'avoir tous les resultats, peut importe si c'est positif ou negatif)
 -- ensuite jointure a gauche avec les depenses pour pouvoir afficher Null si jamais il n'y a pas de depense
 -- (ça nous permet de filtrer que les depenses, et donc le total des depenses)
 -- ensuite jointure a gauche avec les revenus pour pouvoir afficher Null si jamais il n'y a pas de revenu
 -- (ça nous permet de filtrer que les revenus, et donc le total des revenus) 
 -- COALESCE utilisé pour remplacer le NULL avec 0
 -- alias as (i.e. as nb_depenses) utilisé pour faciliter la syntaxe php dans stat.php

CREATE OR REPLACE VIEW stat_depenses_revenus_mois
AS
SELECT  budget_id, mois, annee, bilan_total_mois, nb_total, niss_util, 
        COALESCE(bilan_depenses_mois, 0) as bilan_depenses_mois, COALESCE(nb_depenses, 0) as nb_depenses, 
        COALESCE(bilan_revenus_mois, 0) as bilan_revenus_mois, COALESCE(nb_revenus, 0) as nb_revenus
FROM
    (SELECT budget_id, MONTH(date_tf) as mois, YEAR(date_tf) as annee, SUM(montant) as bilan_total_mois, COUNT(num_tf) as nb_total, niss_util 
     FROM historique_v
     GROUP BY budget_id) total
NATURAL LEFT JOIN
    (SELECT budget_id, MONTH(date_tf) as mois, YEAR(date_tf) as annee, SUM(montant) as bilan_depenses_mois , COUNT(num_tf) as nb_depenses, niss_util
     FROM historique_v
     WHERE montant < 0 
     GROUP BY budget_id) depenses
NATURAL LEFT JOIN
    (SELECT budget_id, MONTH(date_tf) as mois, YEAR(date_tf) as annee, SUM(montant) as bilan_revenus_mois, COUNT(num_tf) as nb_revenus, niss_util
     FROM historique_v
     WHERE montant > 0 
     GROUP BY budget_id) revenus
;


-- Pour la répartition par catégorie et la somme des dépenses et revenus par catégorie : 

CREATE OR REPLACE VIEW stat_cat
AS
SELECT  description_tf, nom_tf, niss_util, 
        COALESCE(nb_utilisations, 0) as nb_utilisations, 
        COALESCE(bilan_depenses_cat, 0) as bilan_depenses_cat, 
        COALESCE(bilan_revenus_cat, 0) as bilan_revenus_cat
FROM
    (SELECT CAT.description_tf, CAT.nom_tf
	 FROM categorie_tf CAT) c
     LEFT JOIN
        (SELECT HIST.cat_tf, HIST.niss_util, 
                COUNT(HIST.cat_tf) as nb_utilisations, 
                SUM(CASE WHEN HIST.montant < 0 THEN montant ELSE 0 END) as bilan_depenses_cat, 
                SUM(CASE WHEN HIST.montant > 0 THEN montant ELSE 0 END) as bilan_revenus_cat
        FROM historique_v HIST 
        GROUP BY HIST.cat_tf) h
     ON c.nom_tf = h.cat_tf
;

-- Enfin, répartition des dépenses et entrées par type de payement : 

CREATE OR REPLACE VIEW stat_types
AS
SELECT  HIST.typetf, COUNT(HIST.typetf) as nb_utilisations, HIST.niss_util,
        SUM(CASE WHEN HIST.montant < 0 THEN montant ELSE 0 END) as total_depenses_type, 
        SUM(CASE WHEN HIST.montant > 0 THEN montant ELSE 0 END) as total_revenus_type
FROM historique_v HIST
GROUP BY HIST.typetf, HIST.niss_util -- HIST.niss_util ajouté pour que ça fonctionne pour tous les utilisateurs
;

-- Restrictions du côté de la db : 
-- Nous définissons deux utilisateurs principaux : 
-- un utilisateur "app", qui est utilisé à travers l'app, et un utilisateur administrateur, qui a accès à tout au niveau de la DB


DROP USER IF EXISTS 'utilisateur_app'@'localhost';

DROP USER IF EXISTS 'utilisateur_admin_db'@'localhost';

-- création de l'utilisateur côté app
CREATE USER 'utilisateur_app'@'localhost' IDENTIFIED BY 'user';

-- création de l'utilisateur côté serveur
CREATE USER 'utilisateur_admin_db'@'localhost' IDENTIFIED BY 'admin';

-- l'utilisateur admin côté serveur peut tout faire (à modifier?)
GRANT ALL PRIVILEGES ON budgetsquirrel. * TO 'utilisateur_admin_db'@'localhost';

-- l'utilisateur app peut s'inscrire : il peut donc créer une nouvelle ligne dans la table utilisateur
-- il peut également modifier certaines de ses infos > update

GRANT INSERT ON budgetsquirrel.utilisateur TO 'utilisateur_app'@'localhost';
GRANT UPDATE ON budgetsquirrel.utilisateur TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut aussi visualiser la liste des utilisateurs sur son écran de connexion, et a besoin du select 
-- sur cette table aussi pour récupérer les informations qui le concernent :

GRANT SELECT ON budgetsquirrel.utilisateur TO 'utilisateur_app'@'localhost';

-- l'utilisateur app peut aussi ajouter ses cartes : il peut donc créer des lignes dans la table carte

GRANT INSERT ON budgetsquirrel.utilisateur TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut consulter la table des transactions financières créées

GRANT SELECT ON budgetsquirrel.transaction_financiere TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut consulter la vue qui rassemble différentes informations sur ses transactions par mois

GRANT SELECT ON budgetsquirrel.stat_depenses_revenus_mois TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut consulter la vue qui rassemble différentes informations sur ses transactions par catégorie

GRANT SELECT ON budgetsquirrel.stat_cat TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut consulter la vue qui rassemble différentes informations sur ses transactions par type

GRANT SELECT ON budgetsquirrel.stat_types TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut consulter la table des cartes

GRANT SELECT ON budgetsquirrel.carte TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut insérer de nouvelles cartes dans la table des cartes

GRANT INSERT ON budgetsquirrel.carte TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut modifier ("supprimer") ses cartes dans la table des cartes

GRANT UPDATE ON budgetsquirrel.carte TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut consulter la table des catégories de transaction

GRANT SELECT ON budgetsquirrel.categorie_tf TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut consulter la table des budgets mensuels

GRANT SELECT ON budgetsquirrel.budget_mensuel TO 'utilisateur_app'@'localhost';

-- Normalement, l'utilisateur ne devra pas INSERT dans la table budget_mensuel, vu que ça va ê automatisé par un trigger
-- Mais en attendant : 
GRANT INSERT ON budgetsquirrel.budget_mensuel TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut créer de nouvelles transactions financières : 

GRANT INSERT ON budgetsquirrel.transaction_financiere TO 'utilisateur_app'@'localhost';

-- ... Et les visualiser

GRANT SELECT ON budgetsquirrel.transaction_financiere TO 'utilisateur_app'@'localhost';

-- ... Et les supprimer

GRANT DELETE ON budgetsquirrel.transaction_financiere TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut spécifier la nature de sa TF, et pour cela, il doit pouvoir INSERT dans les 3 tableaux concernés : 

GRANT INSERT ON budgetsquirrel.tf_cash TO 'utilisateur_app'@'localhost';
GRANT INSERT ON budgetsquirrel.tf_carte TO 'utilisateur_app'@'localhost';
GRANT INSERT ON budgetsquirrel.tf_virement TO 'utilisateur_app'@'localhost';

-- L'utilisateur peut consulter la vue d'historique : 

GRANT SELECT ON budgetsquirrel.historique_v TO 'utilisateur_app'@'localhost';

-- Autres privilèges ?
