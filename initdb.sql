/*SCRIPT DE CREATION DE LA DB BUDGETSQUIRREL */

CREATE DATABASE budgetsquirrel CHARACTER SET 'utf8';

USE budgetsquirrel;

CREATE TABLE utilisateur(
        nom VARCHAR(100),
        prenom VARCHAR(100),
        niss VARCHAR(11),
        date_naissance DATE,
        photo VARCHAR(100),
        nombre_transactions INT DEFAULT 0,
        CONSTRAINT pk_utilisateur PRIMARY KEY(niss)
        );
        
-- Foreign key creation avec constraint avant ou sans?

CREATE TABLE carte(
        nom_carte VARCHAR(255),
        numero_carte VARCHAR(17),
        type_carte VARCHAR(100),
    	niss_util VARCHAR(11),
        is_deleted INT DEFAULT 0,
       	CONSTRAINT fk_niss_util_carte FOREIGN KEY (niss_util) REFERENCES utilisateur(niss),
    	CONSTRAINT pk_carte PRIMARY KEY(nom_carte)
        );

CREATE TABLE budget_mensuel(
    budget_id INT NOT NULL AUTO_INCREMENT,
     mois INT,
     annee INT,
     statut VARCHAR(100),
     bilan INT,
     reste INT NULL,
     niss_util VARCHAR(11),
     CONSTRAINT fk_niss_util_budget FOREIGN KEY (niss_util) REFERENCES utilisateur(niss),
     CONSTRAINT pk_budget_mensuel PRIMARY KEY(budget_id)
        );
        
CREATE TABLE categorie_tf(
    nom_tf VARCHAR(100),
     description_tf TEXT,
     CONSTRAINT pk_categorie_tf PRIMARY KEY(nom_tf)
    );

CREATE TABLE transaction_financiere(
     num_tf INT NOT NULL AUTO_INCREMENT,
     date_tf DATE,
     montant INT,
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
    nom_carte VARCHAR(255),
     CONSTRAINT fk_num_tf_carte FOREIGN KEY (num_tf) REFERENCES transaction_financiere(num_tf),
     CONSTRAINT fk_nom_carte FOREIGN KEY (nom_carte) REFERENCES carte(nom_carte)
        )
    
 ENGINE=InnoDB
