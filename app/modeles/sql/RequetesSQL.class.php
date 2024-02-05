<?php

/**
 * Classe des requêtes SQL
 *
 */
class RequetesSQL extends RequetesPDO {

  /* GESTION DES UTILISATEURS 
     ======================== */

  /**
   * Connecter un utilisateur
   * @param array $champs, tableau avec les champs utilisateur_courriel et utilisateur_mdp  
   * @return array|false ligne de la table, false sinon 
   */ 
  public function connecter($champs) {

    $this->sql = "
      SELECT utilisateur_id, nom, prenom, courriel, nom_usager
      FROM utilisateur
      WHERE courriel = :courriel AND mot_de_passe = SHA2(:mot_de_passe, 512) ";

    return $this->getLignes($champs, RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Ajouter un utilisateur
   * @param array $champs tableau des champs de l'utilisateur
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */ 
  public function ajouterUtilisateur($champs) {
    $this->sql = '
      INSERT INTO utilisateur SET nom = :nom, prenom = :prenom,
      nom_usager = :nom_usager, courriel = :courriel,
      adresse = :adresse, ville = :ville,
      province = :province,  code_postal = :code_postal,
      pays_id = :pays_id, statut_id = :statut_id, mot_de_passe = SHA2(:mot_de_passe, 512),
      date_enregistrement = NOW()';

    return $this->CUDLigne($champs); 
  }

/**
 * Retourne l'id d'un statut d'utilisateur donné
 * @param array $champs tableau des champs de l'utilisateur
 * @return array|false tableau associatif ayant pour valeur l'id du statut, s'il existe. false sinon.
 */
  public function getUtilisateurStatutId($champs){

    $this->sql = "
      SELECT statut_id FROM statut_utilisateur
      WHERE titre = :utilisateur_profil";

      return $this->getLignes($champs, RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
 * Retourne le nom du statut pour un utilisateur donné
 * @param array $champs tableau des champs de l'utilisateur
 * @return array|false tableau associatif ayant pour valeur le nom du statut, s'il existe. false sinon.
 */
  public function getUtilisateurStatut($utilisateur_id){

    $this->sql = "
      SELECT titre 
      FROM utilisateur 
      INNER JOIN statut_utilisateur ON utilisateur.statut_id = statut_utilisateur.statut_id
      WHERE utilisateur_id = :utilisateur_id";

      return $this->getLignes(['utilisateur_id' => $utilisateur_id], RequetesPDO::UNE_SEULE_LIGNE);
  }


  /* GESTION DES ENCHÈRES
     ======================== */

  /**
  * Retourne les ids des pays et leur nom
  * @return array
  */
  public function getListePays(){
    $champs = [];
    $this->sql = "
      SELECT pays_id, nom 
      FROM pays
      ORDER BY nom ASC";

      return $this->getLignes($champs);
  }

  /**
  * Retourne les ids des conditions (timbre) et leur nom.
  * @return array
  */
  public function getListeConditions(){
    $champs = [];
    $this->sql = "
      SELECT condition_id, nom
      FROM `condition`
      ORDER BY condition_id ASC";

      return $this->getLignes($champs);
  }

 /**
  * Retourne les ids des couleurs (timbre) et leur nom.
  * @return array
  */
  public function getListeCouleurs(){
    $champs = [];
    $this->sql = "
      SELECT couleur_id, nom
      FROM couleur
      ORDER BY couleur_id ASC";

      return $this->getLignes($champs);
  }

 /**
  * Retourne les ids des centrages (timbre) et leur nom.
  * @return array
  */
  public function getListeCentrages(){
    $champs = [];
    $this->sql = "
      SELECT centrage_id, nom
      FROM centrage
      ORDER BY centrage_id ASC";

      return $this->getLignes($champs);
  }

 /**
  * Retourne vrai si le nom d'usager donné en paramètre existe déjà
  * dans la table utilisateur, sinon faux.
  * @param array $champs tableau associatif contenant le nom d'usager
  * @return boolean
  */
  public function isNomUsagerExiste($champs){
    $this->sql = "
      SELECT *
      FROM utilisateur
      WHERE nom_usager = :nom_usager";

      $resultats = $this->getLignes($champs, self::UNE_SEULE_LIGNE);

      return $resultats ? true : false;
  }

 /**
  * Retourne vrai si le courriel donné en paramètre existe déjà
  * dans la table utilisateur, sinon faux.
  * @param array $champs tableau associatif contenant le courriel
  * @return boolean
  */
  public function isCourrielExiste($champs){
    $this->sql = "
      SELECT *
      FROM utilisateur
      WHERE courriel = :courriel";

      $resultats = $this->getLignes($champs, self::UNE_SEULE_LIGNE);

      return $resultats ? true : false;
  }

  /**
   * Ajouter une enchère
   * @param array $champs tableau des champs de l'enchère
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */ 
  public function ajouterEnchere($champs) {
    $this->sql = "
      INSERT INTO enchere SET date_debut = :date_debut, date_fin = :date_fin,
      utilisateur_id = :utilisateur_id, prix_plancher = :prix_plancher";

    return $this->CUDLigne($champs); 
  }

  /**
   * Ajouter un timbre
   * @param array $champs tableau des champs du timbre
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */ 
  public function ajouterTimbre($champs) {
    $this->sql = "
      INSERT INTO timbre SET titre = :titre, date_creation = :date_creation,
      tirage = :tirage, dimensions = :dimensions, certification = :certification,
      pays_id = :pays_id, condition_id = :condition_id, centrage_id = :centrage_id,
      enchere_id = :enchere_id, utilisateur_id = :utilisateur_id, couleur_id = :couleur_id";

    return $this->CUDLigne($champs); 
  }

  /**
   * Ajouter une image
   * @param array $champs tableau des champs de l'image
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */ 
  public function ajouterImage($champs) {
    $this->sql = "
      INSERT INTO image SET nom_fichier = :nom_fichier, timbre_id = :timbre_id";

    return $this->CUDLigne($champs); 
  }

  /**
   * Récupération d'un timbre associé à une enchère. Seule la première rangée trouvée est retournée.
   * @param int $enchere_id
   * @return array
   */
  public function getTimbre($enchere_id) {
    $this->sql = "
      SELECT timbre_id, titre, date_creation, tirage, dimensions, certification, pays.nom AS pays,
        centrage.nom AS centrage, couleur.nom AS couleur, `condition`.nom AS `condition`
      FROM timbre
      INNER JOIN pays ON timbre.pays_id = pays.pays_id
      INNER JOIN centrage ON timbre.centrage_id = centrage.centrage_id
      INNER JOIN couleur ON timbre.couleur_id = couleur.couleur_id
      INNER JOIN `condition` on timbre.condition_id = `condition`.condition_id
      WHERE enchere_id = :enchere_id";
      
    return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
  }


  /**
   * Récupération d'une enchère à partir de son id.
   * @param int $enchere_id
   * @return array
   */
  public function getEnchere($enchere_id) {
    $this->sql = "
      SELECT enchere_id, date_fin, prix_plancher, coup_coeur, nom_usager, enchere.utilisateur_id
      FROM enchere
      INNER JOIN utilisateur ON enchere.utilisateur_id = utilisateur.utilisateur_id
      WHERE enchere_id = :enchere_id";
      
    return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  
  /**
   * Récupération d'images partir de l'id du timbre. Pour le moment, seule la première
   * image trouvée est retournée.
   * @param int $timbre_id
   * @return array
   */
  public function getImages($timbre_id) {
    $this->sql = "
      SELECT *
      FROM image
      WHERE timbre_id = :timbre_id";
      
    return $this->getLignes(['timbre_id' => $timbre_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Récupération des mises partir de l'id de l'enchère.
   * @param int $enchere_i
   * @return array
   */
  public function getMises($enchere_id) {
    $this->sql = "
      SELECT mise, date, nom_usager
      FROM historique_mise
      INNER JOIN utilisateur ON historique_mise.utilisateur_id = utilisateur.utilisateur_id
      WHERE enchere_id = :enchere_id
      ORDER by mise DESC";
      
    return $this->getLignes(['enchere_id' => $enchere_id]);
  }

  /**
   * Ajouter une mise pour un utilisateur et une enchère donnés.
   * @param array $champs, tableau contenant la mise, l'id de l'utilisateur et l'id de l'enchère
   */
  public function placerMise($champs) {
    $this->sql = "
      INSERT INTO historique_mise SET mise = :mise, date = NOW(), utilisateur_id = :utilisateur_id,
        enchere_id = :enchere_id";

    return $this->CUDLigne($champs); 
  }

  /**
   * Vérifier si une enchère existe.
   * @param int $enchere_id
   * @return bool, vrai si l'enchère existe
   */
  public function isEnchereExiste($enchere_id) {
    $this->sql = "
      SELECT enchere_id
      FROM enchere
      WHERE enchere_id = :enchere_id";

    $resultat = $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);

    return $resultat ? true : false;
  }

  /**
   * Obtenir toutes les enchères.
   * @return array
   */
  public function getEncheres() {
    $this->sql = "
      SELECT enchere.enchere_id, date_fin, coup_coeur, timbre_id, titre, certification, timbre.pays_id, condition_id, centrage_id, couleur_id, date_creation, nom_usager
      FROM enchere
      INNER JOIN timbre ON enchere.enchere_id = timbre.enchere_id
      INNER JOIN utilisateur ON enchere.utilisateur_id = utilisateur.utilisateur_id";

    return $this->getLignes([]);
  }

  /**
   * Obtenir le nombre de mises pour une enchère donnée.
   * @param int $enchere_id
   * @return int
   */
  public function getNombreMises($enchere_id) {
    $this->sql = "
      SELECT COUNT(*) AS resultat
      FROM historique_mise
      WHERE enchere_id = :enchere_id";

    return $this->getLignes(['enchere_id' => $enchere_id], self::UNE_SEULE_LIGNE)['resultat'];
  }

  /**
   * Ajouter un favori pour un utilisateur et une enchère donnés.
   * @param int $enchere_id
   * @param int $utilisateur_id
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function ajouterFavori($enchere_id, $utilisateur_id) {

    $champs = ['enchere_id' => $enchere_id, 'utilisateur_id' => $utilisateur_id];

    $this->sql = "
      INSERT INTO favori SET enchere_id = :enchere_id, utilisateur_id = :utilisateur_id";

    return $this->CUDLigne($champs); 
  }

  /**
   * Supprimer un favori pour un utilisateur et une enchère donnés.
   * @param int $enchere_id
   * @param int $utilisateur_id
   * @return boolean true si suppression effectuée, false sinon
   */
  public function supprimerFavori($enchere_id, $utilisateur_id) {

    $champs = ['enchere_id' => $enchere_id, 'utilisateur_id' => $utilisateur_id];

    $this->sql = "
      DELETE FROM favori
      WHERE enchere_id = :enchere_id AND utilisateur_id = :utilisateur_id";

    return $this->CUDLigne($champs); 
  }

  /**
   * Obtenir les enchères favorites pour un utilisateur donné.
   * @param int $utilisateur_id
   * @return array
   */
  public function getEncheresFavorites($utilisateur_id) {
    $this->sql = "
      SELECT enchere.enchere_id, date_fin, coup_coeur, timbre_id, titre
      FROM enchere
      INNER JOIN favori ON enchere.enchere_id = favori.enchere_id
      INNER JOIN timbre ON enchere.enchere_id = timbre.enchere_id
      WHERE favori.utilisateur_id = :utilisateur_id";

    return $this->getLignes(['utilisateur_id' => $utilisateur_id]);
  }

  /**
   * Vérifier si une enchère est favorite.
   * @param int $enchere_id
   * @param int $utilisateur_id
   * @return bool, vrai si l'enchère est dans les favori de l'utilisateur
   */
  public function isEnchereFavorite($enchere_id, $utilisateur_id) {

    $champs = ['enchere_id' => $enchere_id , 'utilisateur_id' => $utilisateur_id];
    $this->sql = "
      SELECT enchere_id
      FROM favori
      WHERE utilisateur_id = :utilisateur_id AND enchere_id = :enchere_id";

    $resultats = $this->getLignes($champs, self::UNE_SEULE_LIGNE);
    return $resultats ? true : false;
  }

  /**
   * Mettre une enchère en tant que coup de coeur.
   * @param int $enchere_id
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function setEnchereCoupCoeur($enchere_id) {
    $this->sql = '
      UPDATE enchere SET coup_coeur = 1
      WHERE enchere_id = :enchere_id';

    return $this->CUDLigne(['enchere_id' => $enchere_id]);
  }

  /**
   * Retirer le coup de coeur d'une enchère.
   * @param int $enchere_id
   * @return bool true si suppression effectuée, false sinon
   */
  public function unsetEnchereCoupCoeur($enchere_id) {
    $this->sql = '
      UPDATE enchere SET coup_coeur = 0
      WHERE enchere_id = :enchere_id';

    return $this->CUDLigne(['enchere_id' => $enchere_id]);
  }


  /**
   * Rechercher une chaine de caractères dans le titre de l'enchère ou le champs tirage.
   * @param string $keywords
   * @return array, les enchères où la chaine a été trouvée
   */
  public function rechercher($keywords) {
    $this->sql = "
      SELECT enchere.enchere_id, date_fin, coup_coeur, timbre_id, titre, certification, pays_id, condition_id, centrage_id, couleur_id, date_creation
      FROM enchere
      INNER JOIN timbre ON enchere.enchere_id = timbre.enchere_id
      WHERE titre LIKE :keywords1 OR tirage LIKE :keywords2";

    $searchKeywords = '%'. $keywords .'%';
    return $this->getLignes(['keywords1' => $searchKeywords, 'keywords2' => $searchKeywords]);
  }

  /**
   * Retourne un tableau des pays distincts pour tous les timbres, ainsi que le nombre de timbre
   * par pays.
   * @return array
   */
  public function getPays() {
    $this->sql = "
      SELECT timbre.pays_id, nom, COUNT(*) AS nombre_timbres
      FROM timbre
      INNER JOIN pays ON timbre.pays_id = pays.pays_id
      GROUP BY pays.pays_id";

    return $this->getLignes([]);
  }

  /**
   * Retourne un tableau des conditions distinctes pour tous les timbres, ainsi que le nombre de timbre
   * par condition.
   * @return array
   */
  public function getConditions() {
    $this->sql = "
      SELECT timbre.condition_id, nom, COUNT(*) AS nombre_timbres
      FROM timbre
      INNER JOIN `condition` ON timbre.condition_id = `condition`.condition_id
      GROUP BY `condition`.condition_id";

    return $this->getLignes([]);
  }

  /**
   * Retourne un tableau des centrages distincts pour tous les timbres, ainsi que le nombre de timbre
   * par centrage.
   * @return array
   */
  public function getCentrages() {
    $this->sql = "
      SELECT timbre.centrage_id, nom, COUNT(*) AS nombre_timbres
      FROM timbre
      INNER JOIN `centrage` ON timbre.centrage_id = `centrage`.centrage_id
      GROUP BY `centrage`.centrage_id";

    return $this->getLignes([]);
  }

  /**
   * Retourne un tableau des couleurs distinctes pour tous les timbres, ainsi que le nombre de timbre
   * par couleur
   * @return array
   */
  public function getCouleurs() {
    $this->sql = "
      SELECT timbre.couleur_id, nom, COUNT(*) AS nombre_timbres
      FROM timbre
      INNER JOIN `couleur` ON timbre.couleur_id = `couleur`.couleur_id
      GROUP BY `couleur`.couleur_id";

    return $this->getLignes([]);
  }

  /**
   * Retourne un tableau du nombre de timbres certifiés et non certifiés.
   * @return array, tableau associatif
   */
  public function getCertification() {
    $resultat = [];

    $this->sql = "
      SELECT COUNT(*) AS nombre_timbres_certifie
      FROM timbre
      WHERE certification = 1";
    $resultat['nombre_timbres_certifie'] = $this->getLignes([], self::UNE_SEULE_LIGNE)['nombre_timbres_certifie'];

    $this->sql = "
      SELECT COUNT(*) AS nombre_timbres_non_certifie
      FROM timbre
      WHERE certification = 0";
    $resultat['nombre_timbres_non_certifie'] = $this->getLignes([], self::UNE_SEULE_LIGNE)['nombre_timbres_non_certifie'];

    return $resultat;
  }

  /**
   * Obtenir les enchères créées par un utilisateur donné.
   * @param int $utilisateur_id
   * @return array
   */
  public function getEncheresUtilisateur($utilisateur_id) {
    $this->sql = "
      SELECT enchere.enchere_id, date_fin, coup_coeur, timbre_id, titre
      FROM enchere
      INNER JOIN timbre ON enchere.enchere_id = timbre.enchere_id
      WHERE enchere.utilisateur_id = :utilisateur_id";

    return $this->getLignes(['utilisateur_id' => $utilisateur_id]);
  }

  /**
   * Retourne un tableau du nombre de timbres coup de coeur ou non.
   * @return array, tableau associatif
   */
  public function getCoupsCoeur() {
    $resultat = [];

    $this->sql = "
      SELECT COUNT(*) AS nombre_coup_coeur
      FROM enchere
      WHERE coup_coeur = 1";
    $resultat['nombre_coup_coeur'] = $this->getLignes([], self::UNE_SEULE_LIGNE)['nombre_coup_coeur'];

    $this->sql = "
      SELECT COUNT(*) AS nombre_non_coup_coeur
      FROM enchere
      WHERE coup_coeur = 0";
    $resultat['nombre_non_coup_coeur'] = $this->getLignes([], self::UNE_SEULE_LIGNE)['nombre_non_coup_coeur'];

    return $resultat;
  }

  /**
   * Obtenir un nombre limité d'enchères coup de coeur.
   * @param int $nombre_encheres
   * @return array
   */
  public function getEncheresCoupCoeur($nombre_encheres) {
    $this->sql = "
      SELECT enchere.enchere_id, date_fin, coup_coeur, timbre_id, titre
      FROM enchere
      INNER JOIN timbre ON enchere.enchere_id = timbre.enchere_id
      WHERE coup_coeur = 1
      LIMIT :nombre_encheres";

    return $this->getLignes(['nombre_encheres' => $nombre_encheres]);
  }

  /**
   * Supprimer une enchère, le timbre associé et l'image associé au timbre.
   * @param int $enchere_id
   * @param int $timbre_id
   * @return boolean true si suppression effectuée, false sinon
   */ 
  public function supprimerEnchere($enchere_id, $timbre_id) {
    
    $champs = [];

    $this->sql_multiple[] = 'DELETE FROM image WHERE timbre_id = :timbre_id';
    $champs[] =  ['timbre_id' => $timbre_id];

    $this->sql_multiple[] = 'DELETE FROM timbre WHERE enchere_id = :enchere_id';
    $champs[] =  ['enchere_id' => $enchere_id];

    $this->sql_multiple[] = 'DELETE FROM historique_mise WHERE enchere_id = :enchere_id';
    $champs[] =  ['enchere_id' => $enchere_id];

    $this->sql_multiple[] = 'DELETE FROM favori WHERE enchere_id = :enchere_id';
    $champs[] =  ['enchere_id' => $enchere_id];

    $this->sql_multiple[] = 'DELETE FROM commentaire WHERE enchere_id = :enchere_id';
    $champs[] =  ['enchere_id' => $enchere_id];

    $this->sql_multiple[] = 'DELETE FROM enchere WHERE enchere_id = :enchere_id';
    $champs[] =  ['enchere_id' => $enchere_id];
  
    return $this->TransactionCUDLigne($champs); 
  }

  /**
   * Retourne l'id du timbre associé à une enchère.
   * @param int $enchere_id
   * @return int
   */ 
  public function getTimbreId($enchere_id) {
    $this->sql = "
      SELECT timbre_id
      FROM timbre
      WHERE enchere_id = :enchere_id";
    return $this->getLignes(['enchere_id' => $enchere_id], self::UNE_SEULE_LIGNE)['timbre_id']; 
  }

  /**
   * Retourne le nom de fichier d'une image à partir de l'id d'un timbre.
   * @param $timbre_id
   */
  public function getImage($timbre_id) {
      $this->sql = "
        SELECT nom_fichier
        FROM image
        WHERE timbre_id = :timbre_id";

    return $this->getLignes(['timbre_id' => $timbre_id], self::UNE_SEULE_LIGNE);
  }
}
