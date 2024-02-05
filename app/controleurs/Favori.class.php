<?php

/**
 * Classe Contrôleur des requêtes de l'interface Favori
 * 
 */

class Favori extends Routeur {

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
   * Ajoute un favori pour un utilisateur et une enchère spécifiés dans la requête POST.
   * Valide que l'utilisateur est connecté pour effectuer cette requête. Retourne un 
   * message de confirmation ou d'erreur en format JSON.
   */
  public function ajouterFavori() {
    if ($this->oUtilConn) { 
      if (count($_POST) !== 0) {
        $enchere_id = $_POST['enchere_id'];

        $retour = $this->oRequetesSQL->ajouterFavori($enchere_id, $this->oUtilConn->utilisateur_id);

        if ($retour) {
          $this->messageRetourAction = "Votre favori a bien été enregistré.";
          $msgRetour = ['statut' => 'OK'];
        }
        else {
          $this->messageRetourAction = "Erreur lors de l'enregistrement du favori. Merci de nous contacter.";
          $msgRetour = ['statut' =>  $this->messageRetourAction];
        }

        echo json_encode(($msgRetour));

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
   * Retire un favori pour un utilisateur et une enchère spécifiés dans la requête POST.
   * Valide que l'utilisateur est connecté pour effectuer cette requête. Retourne un 
   * message de confirmation ou d'erreur en format JSON.
   */
  public function retirerFavori() {
    if ($this->oUtilConn) { 
      if (count($_POST) !== 0) {
          $requete = $_POST;
          $enchere_id = $requete['enchere_id'];

          $retour = $this->oRequetesSQL->supprimerFavori($enchere_id, $this->oUtilConn->utilisateur_id);

          if ($retour) {
            $this->messageRetourAction = "Votre favori a bien été supprimé.";
            $msgRetour = ['statut' => 'OK'];
          }
          else {
            $this->messageRetourAction = "Erreur lors de la suppression du favori. Merci de nous contacter.";
            $msgRetour = ['statut' =>  $this->messageRetourAction];
          }
          echo json_encode(($msgRetour));
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
   * Affiche les favoris pour un utilisateur connecté.
   */
  public function afficherFavoris() {

    if ($this->oUtilConn) { 
      $utilisateur_id = $this->oUtilConn->utilisateur_id;

      $encheresFavorites = $this->oRequetesSQL->getEncheresFavorites($utilisateur_id);

      $encheres = [];

      foreach ($encheresFavorites as $enchere) {
          $image = $this->oRequetesSQL->getImages($enchere['timbre_id']);
          $enchere['image'] = $image['nom_fichier'];
          $oUtilitaire = new Utilitaire;
          $enchere['mise_minimum'] = $oUtilitaire->getMiseMinimumFormattee($enchere['enchere_id']);
          $enchere['temps_restant'] = $oUtilitaire->determinerTempsRestant($enchere['date_fin']);
          $enchere['nombre_mises'] = $this->oRequetesSQL->getNombreMises($enchere['enchere_id']);
          $enchere['favori'] = true;
          $encheres[] = $enchere;
      }

      new Vue("/Favori/vFavoris",
              array(
                'titre'  => "Favoris des Enchères Stampee's",
                'oUtilConn' => $this->oUtilConn,
                'encheres' => $encheres
              ),
              "/Frontend/gabarit-frontend");
    }
    else {
      header("Location: ./");
    }
  }
}
