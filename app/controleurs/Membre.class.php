<?php

/**
 * Classe Contrôleur des requêtes de l'interface Membre.
 */

class Membre extends Routeur {

    private $classRetour = "fait";
    private $messageRetourAction = "";
    private $oUtilConn;


    /**
     * Constructeur qui initialise l'utilisateur s'il est connecté
     * et la propriété oRequetesSQL déclarée dans la classe Routeur
     * 
     */
    public function __construct() {
        $this->oUtilConn = $_SESSION['oUtilConn'] ?? null;
        $this->oRequetesSQL = new RequetesSQL;
    }

    /**
     * Connecter un utilisateur
     */
    public function connecter() {
        $utilisateur = $this->oRequetesSQL->connecter($_POST);
        if ($utilisateur !== false) {
            $_SESSION['oUtilConn'] = new Utilisateur($utilisateur);
        }
        echo json_encode($utilisateur);
    }

    /**
     * Déconnecter un utilisateur
     */
    public function deconnecter() {
        unset ($_SESSION['oUtilConn']);
        echo json_encode(true);
    }

    /**
     * Afficher et gérer le formulaire d'inscription
     * 
     */  
    public function inscrire() {

        $utilisateur  = [];
        $erreurs = [];
        $pays = $this->oRequetesSQL->getListePays();

        if (count($_POST) !== 0) {

            // Retour de saisie du formulaire
            $utilisateur = $_POST;
            $oUtilisateur = new Utilisateur($utilisateur); // création d'un objet Utilisateur pour contrôler la saisie
            $erreurs = $oUtilisateur->erreurs;

            // Vérification de la duplication de nom usager
            if (!array_key_exists('nom_usager', $erreurs)) {
                $champs = ['nom_usager' => $oUtilisateur->nom_usager];
                if ($this->oRequetesSQL->isNomUsagerExiste($champs)) {
                $erreurs['nom_usager'] = "Le nom d'usager existe déjà";
                }
            }

            // Vérification de la duplication de courriel
            if (!array_key_exists('courriel', $erreurs)) {
                $champs = ['courriel' => $oUtilisateur->courriel];
                if ($this->oRequetesSQL->isCourrielExiste($champs)) {
                $erreurs['courriel'] = "Le courriel existe déjà";
                }
            }

            if (count($erreurs) === 0) { // aucune erreur de saisie -> requête SQL d'ajout

                $statut = $this->oRequetesSQL->getUtilisateurStatutId([
                    'utilisateur_profil' => 'client'
                ]);

                $utilisateur_id = $this->oRequetesSQL->ajouterUtilisateur([
                    'nom'    => $oUtilisateur->nom,
                    'prenom' => $oUtilisateur->prenom,
                    'courriel' => $oUtilisateur->courriel,
                    'nom_usager' => $oUtilisateur->nom_usager,          
                    'mot_de_passe' => $oUtilisateur->mot_de_passe,
                    'adresse' => $oUtilisateur->adresse,
                    'ville' => $oUtilisateur->ville,
                    'province' => $oUtilisateur->province,
                    'code_postal' => $oUtilisateur->code_postal,
                    'pays_id' => $oUtilisateur->pays_id,
                    'statut_id' => $statut['statut_id']          
                ]);
                if ( $utilisateur_id > 0) { // test de la clé de l'utilisateur ajouté
                    $this->messageRetourAction = "Ajout de l'utilisateur numéro $utilisateur_id effectué.";

                // Pour ouvrir immédiatement la session. Désactivé pour le moment.
                // $_SESSION['oUtilConn'] = new Utilisateur([
                //     'utilisateur_id' => $utilisateur_id,
                //     'nom' => $oUtilisateur->nom,
                //     'prenom' => $oUtilisateur->prenom,
                //     'courriel' => $oUtilisateur->courriel,
                //     'nom_usager' => $oUtilisateur->nom_usager
                // ]);
                // $this->oUtilConn = $_SESSION['oUtilConn'];

                } else {
                    $this->classRetour = "erreur";
                    $this->messageRetourAction = "Erreur d'insertion dans BD: Ajout de l'utilisateur non effectué.";
                }

                header("Location:" . "espaceMembre");

                exit;
            }
        }

        new Vue("/Membre/vInscription",
                array(
                'titre'       => "S'inscrire à Stampee's",
                'utilisateur' => $utilisateur,
                'oUtilConn' => $this->oUtilConn,
                'pays'        => $pays,
                'erreurs'     => $erreurs
                ),
                "/Frontend/gabarit-frontend");
    }

    /**
     * Affiche les messages de confirmation d'inscription à l'utilisateur.
     */
    public function afficherMessage() {

        if ($this->oUtilConn) {
            new Vue("/Frontend/vConfirmation",
                array(
                'titre'  => "S'inscrire à Stampee's",
                'oUtilConn' => $this->oUtilConn,
                'message' => "Encore bravo! Vous êtes maintenant connecté. Vous pouvez maintenant naviguer à travers le site."
                ),
                "/Frontend/gabarit-frontend");
        } else {
            new Vue("/Frontend/vConfirmation",
                array(
                'titre'  => "S'inscrire à Stampee's",
                'oUtilConn' => $this->oUtilConn,
                'message' => "Félicitations! Vous êtes maintenant membre de Stampee's. Vous pouvez maintenant vous connecter."
                ),
                "/Frontend/gabarit-frontend");
        }
    }

}
