<?php

/**
 * Classe Contrôleur des requêtes de l'application admin
 */

class Admin extends Routeur {

  const IMAGE_DIR_PATH = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR .
   ".." . DIRECTORY_SEPARATOR .  "img" . DIRECTORY_SEPARATOR;

  private $entite;
  private $action;
  private $utilisateur_id;
  private $enchere_id;

  private $oUtilConn;

  private $methodes = [
    'utilisateur' => [
      'l' => 'listerUtilisateurs',
      'a' => 'ajouterUtilisateur',
      'm' => 'modifierUtilisateur',
      's' => 'supprimerUtilisateur',
      'd' => 'deconnecter'
    ],
    'enchere' => [
      'l' => 'listerEncheres',
      'a' => 'ajouterEnchere',
      'm' => 'modifierEnchere',
      's' => 'supprimerEnchere'      
    ]
  ];

  private $classRetour = "fait";
  private $messageRetourAction = "";

  /**
   * Constructeur qui initialise le contexte du contrôleur  
   */  
  public function __construct() {
    $this->entite    = $_GET['entite']    ?? 'enchere';
    $this->action    = $_GET['action']    ?? 'l';
    $this->utilisateur_id = $_GET['utilisateur_id'] ?? null;
    $this->enchere_id  = $_GET['enchere_id']  ?? null;
    $this->oRequetesSQL = new RequetesSQL;
  }

  /**
   * Gérer l'interface d'administration 
   */  
  public function gererAdmin() {
    if (isset($_SESSION['oUtilConn'])) {
      $this->oUtilConn = $_SESSION['oUtilConn'];

      // Vérification du statut d'administrateur
      $statut = $this->oRequetesSQL->getUtilisateurStatut($this->oUtilConn->utilisateur_id);
      if ($statut['titre'] != 'administrateur') {
        unset ($_SESSION['oUtilConn']);
        throw new Exception(self::ERROR_FORBIDDEN);
      }

      if (isset($this->methodes[$this->entite])) {
        if (isset($this->methodes[$this->entite][$this->action])) {
          $methode = $this->methodes[$this->entite][$this->action];
          $this->$methode();
        } else {
          throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
        }
      } else {
        throw new Exception("L'entité $this->entite n'existe pas.");
      }
    } else {
        $this->connecter();
    }
  }

    /**
   * Connecter un utilisateur
   */
  public function connecter() {
    $messageErreurConnexion = ""; 
    if (count($_POST) !== 0) {
      $utilisateur = $this->oRequetesSQL->connecter($_POST);
      if ($utilisateur !== false) {
        $_SESSION['oUtilConn'] = new Utilisateur($utilisateur);
        $this->gererAdmin();
        exit;         
      } else {
        $messageErreurConnexion = "Courriel ou mot de passe incorrect.";
      }
    }
    
    new Vue('/Membre/modaleConnexion',
            array(
              'titre'                  => 'Connexion',
              'actionUri'              => 'admin',
              'messageErreurConnexion' => $messageErreurConnexion
            ),
            'Admin/gabarit-admin-min');
  }

  /**
   * Déconnecter un utilisateur
   */
  public function deconnecter() {
    unset ($_SESSION['oUtilConn']);
    $this->connecter();
  }

  /**
   * Gérer des enchères
   */
  public function listerEncheres() {

    $encheres = $this->oRequetesSQL->getEncheres();

    new Vue('/Admin/vAdminEncheres',
            array(
              'oUtilConn'           => $this->oUtilConn,
              'titre'               => 'Gestion des enchères',
              'encheres'            => $encheres,
              'classRetour'         => $this->classRetour, 
              'messageRetourAction' => $this->messageRetourAction
            ),
            '/Admin/gabarit-admin');
  }

  /**
   * Gérer des utilisateurs.
   */
  public function listerUtilisateurs() {
    new Vue('/Admin/vAdminDevEnCours',
            array(
              'oUtilConn' => $this->oUtilConn,
              'titre'     => "Gestion des utilisateurs",
            ),
            '/Admin/gabarit-admin');
  }

  /**
   * Ajouter une enchère. En développement.
   */
  public function ajouterEnchere() {
    new Vue('/Admin/vAdminDevEnCours',
        array(
          'oUtilConn' => $this->oUtilConn,
          'titre'     => "Ajouter une enchère",
        ),
        '/Admin/gabarit-admin');
  }

  /**
   * Ajouter une enchère. En développement.
   */
  public function modifierEnchere() {
    new Vue('/Admin/vAdminDevEnCours',
        array(
          'oUtilConn' => $this->oUtilConn,
          'titre'     => "Modifier une enchère",
        ),
        '/Admin/gabarit-admin');
  }

  /**
   * Supprimer une enchère.
   */
  public function supprimerEnchere() {

    $timbre_id = $this->oRequetesSQL->getTimbreId($this->enchere_id);
    $image_nom_fichier = self::IMAGE_DIR_PATH . $this->oRequetesSQL->getImage($timbre_id)['nom_fichier'];

    if ($this->oRequetesSQL->supprimerEnchere($this->enchere_id, $timbre_id)) {
      $this->messageRetourAction = "Suppression de l'enchère numéro $this->enchere_id effectuée dans la DB. ";

      // On supprime aussi l'image
      if (unlink($image_nom_fichier)) {
        $this->messageRetourAction .= " Suppression de l'image réussie.";
      }
      else {
        $this->messageRetourAction .= " Suppression de l'image non effectuée.";
      }


    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Suppression de l'enchère numéro $this->enchere_id non effectuée.";
    }
    $this->listerEncheres();
  }
}