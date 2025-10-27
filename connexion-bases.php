<?php
 // Début connexion à la base de données
        $serveur = "localhost";
        $name = "root";
        $password = "";

        try {
            $connecter = new PDO("mysql:host=$serveur;dbname=gestion_des_etudiants;charset=utf8", $name, $password);
            $connecter->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }

?>