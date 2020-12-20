/*SCRIPT DE CREATION DE LA DB BUDGETSQUIRREL */

DROP DATABASE IF EXISTS budgetsquirrel;

CREATE DATABASE budgetsquirrel CHARACTER SET 'utf8';

USE budgetsquirrel;

-- Création des tables

CREATE TABLE utilisateur(
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        niss VARCHAR(11) NOT NULL,
        date_naissance DATE NOT NULL,
        photo VARCHAR(100) NOT NULL DEFAULT 'froggy.png',
        CONSTRAINT pk_utilisateur PRIMARY KEY(niss) ,
        CONSTRAINT chk_niss CHECK (LENGTHB(niss) = 11),
        CONSTRAINT chk_photo CHECK (photo IN ('froggy.png','gollum.jpg','politecat.jpg', 'raccoon.jpg')),
        CONSTRAINT uk_utilisateur UNIQUE (niss) 
        );
        
CREATE TABLE carte(
        nom_carte VARCHAR(255) NOT NULL,
        numero_carte VARCHAR(17) NOT NULL,
        type_carte VARCHAR(100) NOT NULL,
    	niss_util VARCHAR(11), 
        is_deleted INT DEFAULT 0,
        CONSTRAINT fk_niss_util_carte FOREIGN KEY(niss_util) REFERENCES utilisateur(niss) 
                ON DELETE CASCADE
                ON UPDATE CASCADE, 
    	CONSTRAINT pk_carte PRIMARY KEY(numero_carte),
        CONSTRAINT chk_numero_carte_low CHECK (LENGTHB(numero_carte) >= 16),
        CONSTRAINT chk_numero_carte_high CHECK (LENGTHB(numero_carte) <= 17), 
-- CONTRAINTE <= 17 potentiellement inutile. quand on test avec un numéro de carte de 18 chiffres, MySQL retourne "Data too long" grace au VARCHAR(17)
        CONSTRAINT uc_carte UNIQUE (numero_carte, niss_util),
        CONSTRAINT chk_type_carte CHECK (type_carte IN('Visa', 'Visa Prepaid', 'Maestro', 'Mastercard'))
        );
        
CREATE TABLE budget_mensuel(
        budget_id INT NOT NULL AUTO_INCREMENT,
        mois INT NOT NULL,
        annee INT NOT NULL,
        bilan FLOAT, 
        niss_util VARCHAR(11) NOT NULL,
        CONSTRAINT pk_budget_mensuel PRIMARY KEY(budget_id),
        CONSTRAINT uc_budget_mensuel UNIQUE (mois, annee, niss_util),
        CONSTRAINT fk_niss_util_bm FOREIGN KEY (niss_util) REFERENCES utilisateur (niss)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        );
        
CREATE TABLE categorie_tf(
        nom_tf VARCHAR(100),
        description_tf TEXT,
        CONSTRAINT pk_categorie_tf PRIMARY KEY(nom_tf)
        );

CREATE TABLE transaction_financiere(
        num_tf INT NOT NULL AUTO_INCREMENT,
        date_tf DATE,
        montant FLOAT,
   	niss_util VARCHAR(11) NOT NULL,
        budget_id INT,
        cat_tf VARCHAR(100),
        CONSTRAINT fk_budget_id FOREIGN KEY (budget_id) REFERENCES budget_mensuel(budget_id),
        CONSTRAINT fk_cat_tf FOREIGN KEY (cat_tf) REFERENCES categorie_tf(nom_tf),
        CONSTRAINT pk_transaction_financiere PRIMARY KEY (num_tf),
        CONSTRAINT fk_niss_util_tf FOREIGN KEY (niss_util) REFERENCES utilisateur (niss) 
                ON DELETE CASCADE
                ON UPDATE CASCADE
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

        );


DELIMITER //
DROP TRIGGER IF EXISTS trg_before_ajout_tf//

CREATE TRIGGER trg_before_ajout_tf BEFORE INSERT 
ON transaction_financiere 
FOR EACH ROW

BEGIN
DECLARE rowcount INT;
DECLARE bi INT;
DECLARE naissance DATE;
-- Verifier la différence entre la date_naissance et la date de transaction

SELECT date_naissance INTO naissance FROM utilisateur WHERE niss = NEW.niss_util;
IF NEW.date_tf < naissance THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = "Impossible d'entrer une date plus petite que la date de naissance de l'utilisateur";
        -- message appelé quand, par exemple, la requête suivante est entrée
        -- INSERT INTO `transaction_financiere`(`date_tf`, `montant`, `niss_util`, `cat_tf`) VALUES ('1920-12-02','666','19650922666','salaire')
END IF;

SELECT COUNT(*) INTO rowcount FROM budget_mensuel 
WHERE mois = MONTH(NEW.date_tf) AND annee = YEAR(NEW.date_tf) AND niss_util = NEW.niss_util;

IF rowcount = 0 THEN
    INSERT INTO budget_mensuel(mois, annee, bilan, niss_util) 
    VALUES (MONTH(NEW.date_tf), YEAR(NEW.date_tf), NEW.montant, NEW.niss_util);
END IF;
        SELECT budget_id INTO bi FROM budget_mensuel WHERE mois = MONTH(NEW.date_tf) AND annee = YEAR(NEW.date_tf) AND niss_util = NEW.niss_util;
SET NEW.budget_id = bi;
END;//

DELIMITER ;


-- Mettre à jour le bilan de budget_mensuel à chaque nouvel insert de transaction

DELIMITER //
DROP TRIGGER IF EXISTS trg_after_ajout_tf//

CREATE TRIGGER trg_after_ajout_tf AFTER INSERT 
ON transaction_financiere 
FOR EACH ROW

BEGIN

DECLARE bilan_calcul FLOAT;

SELECT bilan_total_mois INTO bilan_calcul FROM stat_depenses_revenus_mois WHERE budget_id = NEW.budget_id AND niss_util = NEW.niss_util;
UPDATE budget_mensuel SET bilan = bilan_calcul WHERE budget_id = NEW.budget_id AND niss_util = NEW.niss_util;
END;//

DELIMITER ;

-- Pareil après suppression 

DELIMITER //
DROP TRIGGER IF EXISTS trg_after_suppr_tf//

CREATE TRIGGER trg_after_suppr_tf AFTER DELETE
ON transaction_financiere 
FOR EACH ROW

BEGIN

DECLARE bilan_calcul FLOAT;

SELECT bilan_total_mois INTO bilan_calcul FROM stat_depenses_revenus_mois WHERE budget_id = OLD.budget_id AND niss_util = OLD.niss_util;
UPDATE budget_mensuel SET bilan = bilan_calcul WHERE budget_id = OLD.budget_id AND niss_util = OLD.niss_util;
END;//

DELIMITER ;

-- le niss_util.numero carte utilisé appartient toujours à l'utilisateur qui crée la transaction > Trigger 

DELIMITER //
DROP TRIGGER IF EXISTS trg_before_ajout_tf_carte_niss//

CREATE TRIGGER trg_before_ajout_tf_carte_niss BEFORE INSERT
ON tf_carte 
FOR EACH ROW

BEGIN

DECLARE niss_tf VARCHAR(255);
DECLARE niss_carte VARCHAR(255);

SELECT niss_util INTO niss_tf FROM transaction_financiere WHERE num_tf = NEW.num_tf;
SELECT niss_util INTO niss_carte FROM carte WHERE numero_carte = NEW.numero_carte;

IF niss_tf != niss_carte THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = "Impossible d'utiliser une carte que l'utilisateur lié à la transaction ne possède pas";
END IF;
END;//

DELIMITER ;

-- une tf ne peut être utilisée qu'une seule fois soit dans la table tf_carte, tf_cash ou tf_virement
-- Pour ça, il faut créer 3 triggers (, toujours le même, 1 pour chaque table)

DELIMITER //
DROP TRIGGER IF EXISTS trg_before_ajout_tf_carte//

CREATE TRIGGER trg_before_ajout_tf_carte BEFORE INSERT -- verifier l'intégrité de la rel. (t-e) héritage
ON tf_carte 
FOR EACH ROW

BEGIN

DECLARE nb_tf_cash INT;
DECLARE nb_tf_vir INT;

SELECT COUNT(*) INTO nb_tf_cash FROM tf_cash WHERE num_tf = NEW.num_tf;
SELECT COUNT(*) INTO nb_tf_vir FROM tf_virement WHERE num_tf = NEW.num_tf; 

IF (nb_tf_cash != 0) OR (nb_tf_vir != 0) THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = "Impossible d'entrer une même transaction dans plusieurs tables définissant le type de virement.";
END IF;
END;//

DELIMITER ;

DELIMITER //
DROP TRIGGER IF EXISTS trg_before_ajout_tf_cash//

CREATE TRIGGER trg_before_ajout_tf_cash BEFORE INSERT -- verifier l'intégrité de la rel. (t-e) héritage
ON tf_cash 
FOR EACH ROW

BEGIN

DECLARE nb_tf_carte INT;
DECLARE nb_tf_vir INT;

SELECT COUNT(*) INTO nb_tf_carte FROM tf_carte WHERE num_tf = NEW.num_tf;
SELECT COUNT(*) INTO nb_tf_vir FROM tf_virement WHERE num_tf = NEW.num_tf; 

IF (nb_tf_carte != 0) OR (nb_tf_vir != 0) THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = "Impossible d'entrer une même transaction dans plusieurs tables définissant le type de virement.";
END IF;
END;//

DELIMITER ;

DELIMITER ;

DELIMITER //
DROP TRIGGER IF EXISTS trg_before_ajout_tf_virement//

CREATE TRIGGER trg_before_ajout_tf_virement BEFORE INSERT -- verifier l'intégrité de la rel. (t-e) héritage
ON tf_virement
FOR EACH ROW

BEGIN

DECLARE nb_tf_carte INT;
DECLARE nb_tf_cash INT;

SELECT COUNT(*) INTO nb_tf_carte FROM tf_carte WHERE num_tf = NEW.num_tf;
SELECT COUNT(*) INTO nb_tf_cash FROM tf_cash WHERE num_tf = NEW.num_tf; 

IF (nb_tf_carte != 0) OR (nb_tf_cash != 0) THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = "Impossible d'entrer une même transaction dans plusieurs tables définissant le type de virement.";
END IF;
END;//

DELIMITER ;

-- Création des différentes vues

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
ORDER BY tf.date_tf
;

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
ORDER BY annee ASC, mois ASC
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
        GROUP BY HIST.cat_tf, HIST.niss_util) h
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

