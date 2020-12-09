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
        CONSTRAINT pk_utilisateur PRIMARY KEY(niss),
        CONSTRAINT chk_niss CHECK (DATALENGTH(niss) = 11),
        CONSTRAINT chk_photo CHECK (photo IN ('froggy.png','gollum.jpg','politecat.jpg', 'raccoon.jpg'))
        );
        
CREATE TABLE carte(
        nom_carte VARCHAR(255) NOT NULL,
        numero_carte VARCHAR(17) NOT NULL,
        type_carte VARCHAR(100) NOT NULL,
    	niss_util VARCHAR(11), 
        is_deleted INT DEFAULT 0,
       	CONSTRAINT fk_niss_util_carte FOREIGN KEY (niss_util) REFERENCES utilisateur(niss),
    	CONSTRAINT pk_carte PRIMARY KEY(numero_carte),
        CONSTRAINT chk_numero_carte CHECK (DATALENGTH(numero_carte) >= 16),
        CONSTRAINT uc_carte UNIQUE (numero_carte, niss_util),
        CONSTRAINT chk_type_carte CHECK (type_carte IN('Visa', 'Visa Prepaid', 'Maestro', 'Mastercard'))
        );

CREATE TABLE budget_mensuel(
     budget_id INT NOT NULL AUTO_INCREMENT,
     mois INT NOT NULL,
     annee INT NOT NULL,
     bilan FLOAT, -- comment faire pour que le bilan soit calculé automatiquement du côté de la DB?
     niss_util VARCHAR(11),
     CONSTRAINT fk_niss_util_budget FOREIGN KEY (niss_util) REFERENCES utilisateur(niss),
     CONSTRAINT pk_budget_mensuel PRIMARY KEY(budget_id),
     CONSTRAINT uc_budget_mensuel UNIQUE (mois, annee, niss_util)
        );
        
CREATE TABLE categorie_tf(
     nom_tf VARCHAR(100),
     description_tf TEXT,
     CONSTRAINT pk_categorie_tf PRIMARY KEY(nom_tf)
    );

        -- Faut-il des contraintes uniques pour la table transaction_financiere ?

CREATE TABLE transaction_financiere(
     num_tf INT NOT NULL AUTO_INCREMENT,
     date_tf DATE,
     montant FLOAT,
   	 niss_util VARCHAR(11),
     budget_id INT,
     cat_tf VARCHAR(100),
     CONSTRAINT fk_niss_util_tf FOREIGN KEY (niss_util) REFERENCES utilisateur(niss),
     CONSTRAINT fk_budget_id FOREIGN KEY (budget_id) REFERENCES budget_mensuel(budget_id),
     CONSTRAINT fk_cat_tf FOREIGN KEY (cat_tf) REFERENCES categorie_tf(nom_tf),
     CONSTRAINT pk_transaction_financiere PRIMARY KEY (num_tf),
     CONSTRAINT chk_date_tf CHECK(date_tf > utilisateur.date_naissance) -- ok?
          -- ajouter contrainte check date = mois et annee du budget_id
        );


CREATE TABLE tf_cash(
    num_tf INT,
    CONSTRAINT fk_num_tf_cash FOREIGN KEY (num_tf) REFERENCES transaction_financiere(num_tf) ON DELETE CASCADE
    );

CREATE TABLE tf_virement(
    num_tf INT,
    communication TEXT,
    destbenef VARCHAR(255) NOT NULL,
    CONSTRAINT fk_num_tf_virement FOREIGN KEY (num_tf) REFERENCES transaction_financiere(num_tf) ON DELETE CASCADE
    );

CREATE TABLE tf_carte(
    num_tf INT,
    numero_carte VARCHAR(255),
    CONSTRAINT fk_num_tf_carte FOREIGN KEY (num_tf) REFERENCES transaction_financiere(num_tf) ON DELETE CASCADE,
    CONSTRAINT fk_numero_carte FOREIGN KEY (numero_carte) REFERENCES carte(numero_carte)
    )
    
    ENGINE=InnoDB;
    
    CREATE VIEW historique_v
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

    CREATE VIEW stat_depenses_revenus_mois
AS
SELECT * FROM
    (SELECT budget_id, MONTH(date_tf) as mois, YEAR(date_tf) as annee, SUM(montant) 
            as 'bilan_depenses_mois',COUNT(num_tf) as 'nb_depenses' 
        FROM `historique_v`  
        WHERE montant < 0 GROUP BY budget_id) depenses
        NATURAL JOIN
    (SELECT budget_id, MONTH(date_tf), YEAR(date_tf), SUM(montant) 
            as 'bilan_revenus_mois' , COUNT(num_tf) as 'nb_revenus' 
        FROM `historique_v` 
        WHERE montant > 0 GROUP BY budget_id) revenus

        ;

-- Pour la répartition par catégorie et la somme des dépenses et revenus par catégorie : 

	CREATE VIEW stat_cat
    AS
SELECT * FROM
(SELECT CAT.description_tf, CAT.nom_tf 
	FROM categorie_tf CAT) c
LEFT JOIN
   (SELECT HIST.cat_tf, 
    COUNT(HIST.cat_tf) as 'nb_utilisations', 
    SUM(CASE WHEN HIST.montant < 0 THEN montant ELSE 0 END) as 'bilan_depenses_cat', 
    SUM(CASE WHEN HIST.montant > 0 THEN montant ELSE 0 END) as 'bilan_revenus_cat'
        FROM historique_v HIST GROUP BY HIST.cat_tf) h
ON c.nom_tf = h.cat_tf
;

-- Enfin, répartition des dépenses et entrées par type de payement : 


	CREATE VIEW stat_types
    AS
SELECT HIST.typetf, COUNT(HIST.typetf) as 'nb_utilisations', 
SUM(CASE WHEN HIST.montant < 0 THEN montant ELSE 0 END) as 'total_depenses_type', 
SUM(CASE WHEN HIST.montant > 0 THEN montant ELSE 0 END) as 'total_revenus_type'
FROM historique_v HIST
GROUP BY HIST.typetf
