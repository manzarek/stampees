<?php

/**
 * Classe Contrôleur des requêtes de l'interface frontend.
 */

class Frontend extends Routeur {

  private $classRetour = "fait";
  private $messageRetourAction = "";
  private $oUtilConn;

  const NOMBRE_TUILE_ACCUEIL = 6;

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
   * Afficher la page d'accueil.
   */  
  public function voirAccueil() {

    $encheresCoupCoeur = $this->oRequetesSQL->getEncheresCoupCoeur(self::NOMBRE_TUILE_ACCUEIL);

    $encheres = [];

    foreach ($encheresCoupCoeur as $enchere) {
        $image = $this->oRequetesSQL->getImages($enchere['timbre_id']);
        $enchere['image'] = $image['nom_fichier'];
        $oUtilitaire = new Utilitaire;
        $enchere['mise_minimum'] = $oUtilitaire->getMiseMinimumFormattee($enchere['enchere_id']);
        $enchere['temps_restant'] = $oUtilitaire->determinerTempsRestant($enchere['date_fin']);
        $enchere['nombre_mises'] = $this->oRequetesSQL->getNombreMises($enchere['enchere_id']);
        $encheres[] = $enchere;
    }

    new Vue("/Frontend/vAccueil",
            array(
              'titre'     => "Enchères Stampee's",
              'oUtilConn' => $this->oUtilConn,
              'encheres'  => $encheres
            ),
            "/Frontend/gabarit-frontend");
  }


  /**
   * Afficher la page d'actualités.
   */  
  public function voirActualites() {

    new Vue("/Frontend/vDeveloppement",
            array(
              'titre'  => "Enchères Stampee's",
              'oUtilConn' => $this->oUtilConn
            ),
            "/Frontend/gabarit-frontend");
  }
}