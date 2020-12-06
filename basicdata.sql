/*SCRIPT D'AJOUT DE DONNÉES DANS LA DB BUDGETSQUIRREL

À EXECUTER APRÈS initdb.sql */

USE budgetsquirrel;

/*Ajout de quelques utilisateurs de base */

INSERT INTO utilisateur (nom, prenom, niss, date_naissance, photo) VALUES ('Mustermann', 'Erika', '19940307121', '1994-03-07', 'politecat.jpg');

INSERT INTO utilisateur (nom, prenom, niss, date_naissance, photo) VALUES ('Tartempion', 'Jean-Jacques', '19831206323', '1983-12-06', 'raccoon.jpg');

INSERT INTO utilisateur (nom, prenom, niss, date_naissance, photo) VALUES ('Skywalker', 'Anakin', '19960225111', '1996-02-25', 'froggy.jpg');

INSERT INTO utilisateur (nom, prenom, niss, date_naissance, photo) VALUES ('Baggins', 'Bilbo', '19650922666', '1965-09-22', 'froggy.jpg');
