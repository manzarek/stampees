<?php

/**
 * Classe contrôleur des requêtes de l'interface Enchere
 * 
 */

class ControleurEnchere extends Routeur {

  private $classRetour = "fait";
  private $messageRetourAction = "";
  private $oUtilConn;

  const IMAGE_DIR_PATH = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR .
   ".." . DIRECTORY_SEPARATOR .  "img" . DIRECTORY_SEPARATOR;

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
   * Créer une enchère.
   */  
  public function insererEnchere() {

    if (isset($_SESSION['oUtilConn'])) 
    {
      $enchereTimbre  = [];
      $erreursTimbre = [];
      $erreursEnchere = [];
      $erreursImage = [];
      $oTimbre = [];
      $oEnchere = [];
      $pays = $this->oRequetesSQL->getListePays();
      $conditions = $this->oRequetesSQL->getListeConditions();
      $couleurs = $this->oRequetesSQL->getListeCouleurs();
      $centrages = $this->oRequetesSQL->getListeCentrages();

      if (count($_POST) !== 0) {

        // Retour de saisie du formulaire

        // Contient les infos à la fois sur l'enchère et le timbre
        $enchereTimbre = $_POST;

        // Cas de la certification (checkbox)
        $enchereTimbre['certification'] = array_key_exists('certification', $enchereTimbre) ? 1 : 0;
        
        $oTimbre = new Timbre([
          'titre'           => $enchereTimbre['titre'],
          'date_creation'   => $enchereTimbre['date_creation'],
          'tirage'          => $enchereTimbre['tirage'],
          'dimensions'      => $enchereTimbre['dimensions'],
          'certification'   => $enchereTimbre['certification'],
          'pays_id'         => $enchereTimbre['pays_id'],
          'condition_id'    => $enchereTimbre['condition_id'],
          'centrage_id'     => $enchereTimbre['centrage_id'],
          'couleur_id'      => $enchereTimbre['couleur_id']
        ]); // création d'un objet Timbre pour contrôler la saisie

        $erreursTimbre = $oTimbre->erreurs;

        $oEnchere = new Enchere([
          'prix_plancher'   => $enchereTimbre['prix_plancher']
        ]); // création d'un objet Enchere pour contrôler la saisie

        $erreursEnchere = $oEnchere->erreurs;

        // Vérification de l'image
        $image_file = $_FILES["image"];

        if (!$image_file['name']) {
          $erreursImage['image'] = "Aucune image sélectionnée";
        }

        if (count($erreursTimbre) === 0 && count($erreursEnchere) === 0 && count($erreursImage) === 0) { // aucune erreur de saisie -> requête SQL d'ajout

          // Création des dates
          $date_debut = new DateTime();
          $date_fin = clone $date_debut;
          $date_fin->modify('+2 weeks');
          
          $date_creation = new DateTime();
          if ($oTimbre->date_creation) {
            $date_creation->setDate($oTimbre->date_creation,1,1); // 1er janvier de l'année spécifiée
            $date_creation->setTime(0,0,0);
          }
          else {
            $date_creation->setDate(0,1,1); // 1er janvier de l'an 0
            $date_creation->setTime(0,0,0);
          }

          $enchere_id = $this->oRequetesSQL->ajouterEnchere([
            'utilisateur_id'    => $this->oUtilConn->utilisateur_id,
            'date_debut' => $date_debut->format("Y-m-d H:i:s"),
            'date_fin' => $date_fin->format("Y-m-d H:i:s"),
            'prix_plancher' => $oEnchere->prix_plancher,
          ]);

          if ($enchere_id <= 0 ) {
            throw new Exception("Erreur: enchère non insérée.");
          }

          $timbre_id = $this->oRequetesSQL->ajouterTimbre([
            'titre'    => $oTimbre->titre,
            'date_creation' => $date_creation->format("Y-m-d H:i:s"),
            'tirage' => $oTimbre->tirage,
            'dimensions' => $oTimbre->dimensions,
            'certification' => $oTimbre->certification,
            'pays_id' => $oTimbre->pays_id,
            'condition_id' => $oTimbre->condition_id,
            'centrage_id' => $oTimbre->centrage_id,
            'enchere_id' => $enchere_id,
            'utilisateur_id' => $this->oUtilConn->utilisateur_id,
            'couleur_id' => $oTimbre->couleur_id
          ]);
          
          if ($timbre_id < 0 ) {
            throw new Exception("Erreur: timbre non insérée.");
          }

          $image_file = $_FILES["image"];

          // Sortir si le fichier d'image est vide
          if (filesize($image_file["tmp_name"]) <= 0) {
            throw new Exception("Erreur: le fichier image est vide.");
          }

          // Sortir si le fichier d'image n'est pas valide
          $image_type = exif_imagetype($image_file["tmp_name"]);
          if (!$image_type) {
            throw new Exception("Erreur: le fichier téléversé n'est pas une image.");
          }

          $image_extension = image_type_to_extension($image_type, true);
          $image_name = bin2hex(random_bytes(16)) . $image_extension;
          $image_to_path = self::IMAGE_DIR_PATH . $image_name;

          move_uploaded_file($image_file["tmp_name"], $image_to_path);

          $image_id = $this->oRequetesSQL->ajouterImage([
            'nom_fichier' => $image_name,
            'timbre_id' => $timbre_id
          ]);

          if ( $image_id > 0) { // test de la clé de l'utilisateur ajouté
            $this->messageRetourAction = "L'enchère numéro $enchere_id a bien été créée.";
          } else {
            $this->classRetour = "erreur";
            $this->messageRetourAction = "Une erreur est survenue. Ajout de l'enchère non effectué. Merci de nous contacter si le problème persiste.";

            new Vue("/Frontend/vConfirmation",
                array(
                  'titre'  => "Créer une enchère",
                  'header' => 'Problème technique',
                  'message' => $this->messageRetourAction,
                  'oUtilConn' => $this->oUtilConn
                ),
                "/Frontend/gabarit-frontend");
            exit;
            }

          $newURL = "voirEnchere?enchere_id=$enchere_id";

          new Vue("/ControleurEnchere/vConfirmationEnchere",
                array(
                  'titre'  => "Créer une enchère",
                  'header' => 'Félicitations!',
                  'message' => $this->messageRetourAction,
                  'oUtilConn' => $this->oUtilConn,
                  'lienFiche' =>  $newURL
                ),
                "/Frontend/gabarit-frontend");
          exit;

        } 
      } 


      $pays = $this->oRequetesSQL->getListePays();

      new Vue("/ControleurEnchere/vAjouterEnchere",
        array(
          'titre'  => "Créer une enchère",
          'oUtilConn' => $this->oUtilConn,
          'pays'      => $pays,
          'conditions' => $conditions,
          'couleurs'  => $couleurs,
          'centrages' => $centrages,
          'timbre'    => $oTimbre,
          'enchere'    => $oEnchere,
          'erreursTimbre' => $erreursTimbre,
          'erreursEnchere' => $erreursEnchere,
          'erreursImage' => $erreursImage
        ),
        "/Frontend/gabarit-frontend");

    } else {
      throw new Exception(self::ERROR_FORBIDDEN);
    }

  }

  /**
   * Affiche une fiche d'enchère dont l'id est spécifié dans le requête GET.
   */
  public function voirEnchere() {

    $this->messageRetourAction = "";
    $enchere_id = $_GET['enchere_id'] ?? null;

    if (!$enchere_id) {
      throw new Exception(self::ERROR_BAD_REQUEST);
    }

    if ($this->oRequetesSQL->isEnchereExiste($enchere_id)) {
      $this->afficherFicheEnchere($enchere_id);
    }
    else {
      throw new Exception(self::ERROR_NOT_FOUND);
    }

  }

  /**
   * Afficher une fiche d'enchère donnée.
   * @param int $enchere_id
   */
  private function afficherFicheEnchere($enchere_id) {

    if (!is_null($enchere_id)) {

      $timbre = $this->oRequetesSQL->getTimbre($enchere_id);
      $enchere = $this->oRequetesSQL->getEnchere($enchere_id);
      $image = $this->oRequetesSQL->getImages($timbre['timbre_id']);
      $mises = $this->oRequetesSQL->getMises($enchere_id);

      // Calculs nécessaires pour ces champs
      $oUtilitaire = new Utilitaire;
      $enchere['mise_minimum'] = $oUtilitaire->getMiseMinimumFormattee($enchere_id);

      $date_heure_fin = new DateTime($enchere['date_fin']);
      $enchere['date_fin'] = $date_heure_fin->format(DateTime::ATOM);

      $statutUtilisateur = [];

      // Vérification pour utilisateur
      if ($this->oUtilConn) {
        $utilisateur_id = $this->oUtilConn->utilisateur_id;

        // Pour coup de coeur
        if ($this->oRequetesSQL->getUtilisateurStatut($utilisateur_id)['titre'] == 'administrateur') {
          $statutUtilisateur['admin'] = true;
        }

        $enchere['favori'] = $this->oRequetesSQL->isEnchereFavorite($enchere_id, $utilisateur_id);
      }
   
      $dateCreation = new DateTime($timbre['date_creation']);
      $timbre['annee_creation'] = $dateCreation->format("Y");

      $timbre['certification'] = $timbre['certification'] == 1 ? "Oui" : "Non";

  
      new Vue("/ControleurEnchere/vFicheEnchere",
        array(
          'titre'  => "Voir une enchère",
          'oUtilConn' => $this->oUtilConn,
          'timbre' => $timbre,
          'enchere' => $enchere,
          'image' => $image,
          'mises' => $mises,
          'statutUtilisateur' => $statutUtilisateur,
          'messageRetourAction' => $this->messageRetourAction
        ),
        "/Frontend/gabarit-frontend");
    }
    else {
      throw new Exception("Numéro d'enchère vide.");
    }
  }

  /**
   * Place une mise pour un utilisateur spécifié dans la requête POST. Détermine la mise
   * minimum à partir de la DB et valide la mise. Retourne un message de confirmation
   * ou d'erreur en format JSON.
   */
  public function placerMise() {
    if ($this->oUtilConn) {

      if (count($_POST) !== 0) {
        $requeteMise = $_POST;
        $enchere_id = $requeteMise['enchere_id'];
        $mise = $requeteMise['montant'];

        $oUtilitaire = new Utilitaire;
        $mise_minimum = $oUtilitaire->determinerMiseMinimum($enchere_id);

        if ($mise >= $mise_minimum ) {
          $retour = $this->oRequetesSQL->placerMise([
            'enchere_id' => $enchere_id,
            'mise' => $mise,
            'utilisateur_id' => $this->oUtilConn->utilisateur_id
          ]);

          if ($retour) {
            $this->messageRetourAction = "Votre mise a bien été enregistrée.";
            $msgRetour = ['statut' => 'OK'];
          }
          else {
            $this->messageRetourAction = "Erreur lors de l'enregistrement de la mise. Merci de nous contacter.";
            $msgRetour = ['statut' =>  $this->messageRetourAction];
          }
          echo json_encode(($msgRetour));

        }
      }
      else {
        throw new Exception(self::ERROR_BAD_REQUEST);
      }

    } 
    else {
      throw new Exception(self::ERROR_FORBIDDEN);
    }
  }

  /**
   * Ajoute un statut coup-de-coeur pour une enchère spécifiée dans la requête POST.
   * Valide si un usager administrateur a produit cette requête. Retourne un message de confirmation
   * ou d'erreur en format JSON.
   */
  public function ajouterCoupCoeur() {
    if ($this->oUtilConn) {

      // Vérification du statut d'administrateur
      $statut = $this->oRequetesSQL->getUtilisateurStatut($this->oUtilConn->utilisateur_id);
      if ($statut['titre'] != 'administrateur') {
        throw new Exception(self::ERROR_FORBIDDEN);
      }

      $enchere_id = $_POST['enchere_id'];
      $retour = $this->oRequetesSQL->setEnchereCoupCoeur($enchere_id);

      if ($retour) {
        $this->messageRetourAction = "Le coup de coeur a été ajouté.";
        $msgRetour = ['statut' => 'OK'];
      }
      else {
        $this->messageRetourAction = "Erreur lors de l'enregistrement du statut.";
        $msgRetour = ['statut' =>  $this->messageRetourAction];
      }

      echo json_encode(($msgRetour));

    }
    else {
      throw new Exception(self::ERROR_FORBIDDEN);
    }

  }

  /**
   * Retire un statut coup-de-coeur pour une enchère spécifiée dans la requête POST.
   * Valide si un usager administrateur a produit cette requête. Retourne un message de confirmation
   * ou d'erreur en format JSON.
   */
  public function retirerCoupCoeur() {

    if ($this->oUtilConn) {

      // Vérification du statut d'administrateur
      $statut = $this->oRequetesSQL->getUtilisateurStatut($this->oUtilConn->utilisateur_id);
      if ($statut['titre'] != 'administrateur') {
        throw new Exception(self::ERROR_FORBIDDEN);
      }

      $enchere_id = $_POST['enchere_id'];
      $retour = $this->oRequetesSQL->unsetEnchereCoupCoeur($enchere_id);

      if ($retour) {
        $this->messageRetourAction = "Le coup de coeur a été retiré.";
        $msgRetour = ['statut' => 'OK'];
      }
      else {
        $this->messageRetourAction = "Erreur lors de l'enregistrement du statut.";
        $msgRetour = ['statut' =>  $this->messageRetourAction];
      }

      echo json_encode(($msgRetour));

    }
    else {
      throw new Exception(self::ERROR_FORBIDDEN);
    }

  }

  /**
   * Voir les enchères créées par l'utilisateur connecté.
   */
  public function voirEncheresUtilisateur() {
    if ($this->oUtilConn) { 
      $utilisateur_id = $this->oUtilConn->utilisateur_id;

      $encheresUtilisateur = $this->oRequetesSQL->getEncheresUtilisateur($utilisateur_id);

      $encheres = [];

      foreach ($encheresUtilisateur as $enchere) {
          $image = $this->oRequetesSQL->getImages($enchere['timbre_id']);
          $enchere['image'] = $image['nom_fichier'];
          $oUtilitaire = new Utilitaire;
          $enchere['mise_minimum'] = $oUtilitaire->getMiseMinimumFormattee($enchere['enchere_id']);
          $enchere['temps_restant'] = $oUtilitaire->determinerTempsRestant($enchere['date_fin']);
          $enchere['nombre_mises'] = $this->oRequetesSQL->getNombreMises($enchere['enchere_id']);
          $encheres[] = $enchere;
      }

      new Vue("/ControleurEnchere/vMesEncheres",
              array(
                'titre'  => "Mes Enchères Stampee's",
                'oUtilConn' => $this->oUtilConn,
                'encheres' => $encheres
              ),
              "/Frontend/gabarit-frontend");
    }
    else {
      header("Location: ./");
    }
  }

  /**
   * Pour supprimer une enchère, par l'utilisateur.
   */
  public function supprimerEnchere() {
    if ($this->oUtilConn) { 

      $enchere_id = $_GET['enchere_id'] ?? null;
      if (!$enchere_id) {
        throw new Exception("Requête invalide");
      }

      $enchere = $this->oRequetesSQL->getEnchere($enchere_id);

      if (!$enchere) {
        throw new Exception(self::ERROR_BAD_REQUEST);
      }

      // Contrôle de l'identité
      if ($this->oUtilConn->utilisateur_id != $enchere['utilisateur_id']) {
        throw new Exception(self::ERROR_FORBIDDEN);
      }

      $timbre_id = $this->oRequetesSQL->getTimbreId($enchere_id);
      $image_nom_fichier = self::IMAGE_DIR_PATH . $this->oRequetesSQL->getImage($timbre_id)['nom_fichier'];

      if ($this->oRequetesSQL->supprimerEnchere($enchere_id, $timbre_id)) {
        $this->messageRetourAction = "Suppression de l'enchère numéro $enchere_id effectuée dans la DB. ";

        // On supprime aussi l'image
        if (unlink($image_nom_fichier)) {
          $this->messageRetourAction .= " Suppression de l'image réussie.";
        }
        else {
          $this->messageRetourAction .= " Suppression de l'image non effectuée.";
        }
      } else {
        $this->messageRetourAction = "Suppression de l'enchère numéro $enchere_id non effectuée.";
      }
      new Vue("/ControleurEnchere/vConfirmationSuppression",
        array(
          'titre'  => "Suppression d'enchère",
          'header' => "Suppression d'enchère",
          'message' => $this->messageRetourAction,
          'oUtilConn' => $this->oUtilConn
        ),
      "/Frontend/gabarit-frontend");

    } else {
      throw new Exception(self::ERROR_FORBIDDEN);
    }
  }
}
