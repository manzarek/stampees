<?php

/**
 * Classe Contrôleur des requêtes de l'interface Catalogue.
 * 
 */

class Catalogue extends Routeur {

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
   * Afficher le portail d'enchères pour voir l'ensemble du catalogue d'enchères.
   */  
  public function voirCatalogue() {
    $encheresDb = $this->oRequetesSQL->getEncheres();
    $header = "Parcourir les enchères.";
    $this->afficherSelectionEncheres($encheresDb, $header);
  }

  /**
   * Afficher les résultats de la recherche du catalogue seulement.
   */
  public function rechercher() {
    $keywords = $_GET['keywords'];
    $encheresTrouvees = $this->oRequetesSQL->rechercher($keywords);
    $header = "Recherche de '$keywords'";
    $this->afficherSelectionEncheres($encheresTrouvees, $header);
  }

  /**
   * Afficher une sélection d'enchère
   * @param array $selectionEncheres, tableau des enchères à afficher
   * @param string $header, en-tête de la page à afficher
   */
  private function afficherSelectionEncheres($selectionEncheres, $header) {

    $encheres = [];

    // Pour les favoris de l'utilisateur, si connecté
    if ($this->oUtilConn) {
      $utilisateur_id = $this->oUtilConn->utilisateur_id;
    }

    // Extraction de toutes les propriétés de l'enchère nécessaires au filtres
    // et à l'affichage
    foreach ($selectionEncheres as $enchere) {
        $image = $this->oRequetesSQL->getImages($enchere['timbre_id']);
        $enchere['image'] = $image['nom_fichier'];
        $oUtilitaire = new Utilitaire;
        $enchere['mise_minimum'] = $oUtilitaire->getMiseMinimumFormattee($enchere['enchere_id']);
        $enchere['temps_restant'] = $oUtilitaire->determinerTempsRestant($enchere['date_fin']);
        $enchere['nombre_mises'] = $this->oRequetesSQL->getNombreMises($enchere['enchere_id']);

        if ($this->oUtilConn) {
          $enchere['favori'] = $this->oRequetesSQL->isEnchereFavorite($enchere['enchere_id'], $utilisateur_id);
        }

        $encheres[] = $enchere;
    }

    // Filtrage des enchères
    $oFiltre = new Filtre([
      'temps' => ($_GET['temps'] ?? null),
      'pays' => ($_GET['pays'] ?? null),
      'condition' => ($_GET['condition'] ?? null),
      'centrage' => ($_GET['centrage'] ?? null),
      'couleur' => ($_GET['couleur'] ?? null),
      'certification' => ($_GET['certification'] ?? 2),
      'annee_debut' => ($_GET['anneeDebut'] ?? null),
      'annee_fin' => ($_GET['anneeFin'] ?? null),
      'coup_coeur' => ($_GET['coupCoeur'] ?? 2),
    ]);

    $encheres = $oFiltre->filtrer($encheres);

    // Tri des enchères
    $oTri = new Tri([
      'tri' => ($_GET['tri'] ?? null)
    ]);
    $encheres = $oTri->trier($encheres);

    // Pour l'affichage sur la page
    $liens = $this->formerURL($oFiltre, $oTri);
    $nombre_encheres = count($encheres);

    // Pour populer les filtres
    $pays = $this->oRequetesSQL->getPays();
    $conditions = $this->oRequetesSQL->getConditions();
    $centrages = $this->oRequetesSQL->getCentrages();
    $couleurs = $this->oRequetesSQL->getCouleurs();
    $certification = $this->oRequetesSQL->getCertification();
    $coups_coeurs = $this->oRequetesSQL->getCoupsCoeur();

    $recherche = [];
    $recherche['keywords'] = $_GET['keywords'] ?? null;

    new Vue("/Catalogue/vCatalogue",
            array(
              'titre'  => "Enchères Stampee's",
              'oUtilConn' => $this->oUtilConn,
              'encheres' => $encheres,
              'header' => $header,
              'lien' => $liens,
              'filtre' => $oFiltre,
              'tri'    => $oTri,
              'recherche' => $recherche,
              'pays'  => $pays,
              'conditions'  => $conditions,
              'centrages'  => $centrages,
              'couleurs'  => $couleurs,
              'certification' => $certification,
              'coup_coeur'  => $coups_coeurs,
              'nombre_encheres' => $nombre_encheres
            ),
            "/Frontend/gabarit-frontend");
  }


  /**
   * Forme l'url de certains liens de la page en fonction des filtres déjà activés.
   * @param object $filtre, le filtre appliqué sur les enchères
   */
  private function formerURL($filtre, $tri) {

    $url_page = "";
    $url = "";
    $url_temps = "";
    $url_tri = "";
    $url_queries = [];
    $lien = [];
    $premierSeparateur = "";

    // Recherche ou catalogue
    if (isset($_GET['keywords'])) {
      $keywords = $_GET['keywords'];
      $url_page = "recherche?keywords=$keywords";
      $premierSeparateur = '&';
    } else {
       $url_page = "catalogue";
       $premierSeparateur = '?';
    }

    // Si filtre de pays
    if ($filtre->pays) {
      $url_queries[] = "pays=" . $filtre->pays;
    }

    // Si filtre de condition
    if ($filtre->condition) {
      $url_queries[] = "condition=" . $filtre->condition;
    }

    // Si filtre de centrage
    if ($filtre->centrage) {
      $url_queries[] = "centrage=" . $filtre->centrage;
    }

    // Si filtre de couleur
    if ($filtre->couleur) {
      $url_queries[] = "couleur=" . $filtre->couleur;
    }

    // Si filtre de certification
    if ($filtre->certification != 2) {
      $url_queries[] = "certification=" . $filtre->certification;
    }

    // Si année de début
    if ($filtre->annee_debut) {
      $url_queries[] = "anneeDebut=" . $filtre->annee_debut;
    }

    // Si année de fin
    if ($filtre->annee_fin) {
      $url_queries[] = "anneeFin=" . $filtre->annee_fin;
    }
  
    // Si filtre coup de coeur
    if ($filtre->coup_coeur != 2) {
      $url_queries[] = "coupCoeur=" . $filtre->coup_coeur;
    }


    if (count($url_queries) != 0) {
      $url = $url_page . $premierSeparateur . implode('&', $url_queries) . '&';
    } else {
      $url = $url_page . $premierSeparateur;
    }
  
    // Tri
    if ($filtre->temps) {
      $url_tri = $url . "temps=" . $filtre->temps . '&';
    } else {
      $url_tri = $url;
    }

    $lien['prix_croissant'] = $url_tri . "tri=prix_asc";
    $lien['prix_decroissant'] = $url_tri . "tri=prix_desc";
    $lien['fin_enchere'] = $url_tri . "tri=date_fin";

    // Enchères courantes ou passées
    if ($tri->tri) {
      $url_temps = $url . "tri=" . $tri->tri . '&';
    } else {
      $url_temps = $url;
    }

    $lien['temps_tout'] = $url_temps;
    $lien['temps_courant'] = $url_temps . "temps=courant";
    $lien['temps_passe'] = $url_temps . "temps=passe";

    return $lien;
  }
}