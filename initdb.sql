/*SCRIPT DE CREATION DE LA DB BUDGETSQUIRREL */

DROP DATABASE IF EXISTS budgetsquirrel;

CREATE DATABASE budgetsquirrel CHARACTER SET 'utf8';

USE budgetsquirrel;

CREATE TABLE utilisateur(
        nom VARCHAR(100),
        prenom VARCHAR(100),
        niss VARCHAR(11),
        date_naissance DATE,
        photo VARCHAR(100),
        CONSTRAINT pk_utilisateur PRIMARY KEY(niss)
        );
        
CREATE TABLE carte(
        nom_carte VARCHAR(255),
        numero_carte VARCHAR(17),
        type_carte VARCHAR(100),
    	niss_util VARCHAR(11),
        is_deleted INT DEFAULT 0,
       	CONSTRAINT fk_niss_util_carte FOREIGN KEY (niss_util) REFERENCES utilisateur(niss),
    	CONSTRAINT pk_carte PRIMARY KEY(numero_carte),
        CONSTRAINT uc_carte UNIQUE (numero_carte, niss_util)
        );

CREATE TABLE budget_mensuel(
     budget_id INT NOT NULL AUTO_INCREMENT,
     mois INT,
     annee INT,
     statut VARCHAR(100) DEFAULT("en_cours"), -- Supprimer statut?
     bilan FLOAT,
     reste INT NULL, -- Supprimer reste?
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
     CONSTRAINT pk_transaction_financiere PRIMARY KEY (num_tf)
        );


CREATE TABLE tf_cash(
    num_tf INT,
    CONSTRAINT fk_num_tf_cash FOREIGN KEY (num_tf) REFERENCES transaction_financiere(num_tf)
 	   );

CREATE TABLE tf_virement(
    num_tf INT,
    communication TEXT,
    destbenef VARCHAR(255),
    CONSTRAINT fk_num_tf_virement FOREIGN KEY (num_tf) REFERENCES transaction_financiere(num_tf)
    );

CREATE TABLE tf_carte(
    num_tf INT,
    numero_carte VARCHAR(255),
    CONSTRAINT fk_num_tf_carte FOREIGN KEY (num_tf) REFERENCES transaction_financiere(num_tf),
    CONSTRAINT fk_numero_carte FOREIGN KEY (numero_carte) REFERENCES carte(numero_carte)
    );

-- CREATE OR REPLACE VIEW historique_v AS
--     SELECT tf.montant, tf.date_tf, tf.cat_tf, 
--         Case
--             when tf.num_tf = 1 then 'cash'
--             when tf.num_tf = 2 then 'carte'
--             when tf.num_tf = 3 then 'virement'
--         end as type_transaction, 
--         c.nom_carte, tfv.destbenef, tfv.communication
--     FROM transaction_financiere tf
--     LEFT JOIN tf_carte tfc ON tfc.num_tf = tf.num_tf 
--     LEFT JOIN tf_virement tfv ON tfv.num_tf = tf.num_tf 
--     INNER JOIN carte c ON c.numero_carte = tfc.numero_carte -- a remplacer par numero_carte lors du prochain update du code
-- ;



 --------------------------------------------------

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

 ENGINE=InnoDB
