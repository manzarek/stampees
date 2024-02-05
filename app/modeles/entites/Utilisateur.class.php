<?php

/**
 * Classe de l'entité Utilisateur
 *
 */
class Utilisateur
{
  private $utilisateur_id;
  private $nom;
  private $prenom;
  private $courriel;
  private $nom_usager;
  private $mot_de_passe;
  private $adresse;
  private $ville;
  private $province;
  private $code_postal;
  private $pays_id;

  private $erreurs = array();

  /**
   * Constructeur de la classe
   * @param array $proprietes, tableau associatif des propriétés 
   *
   */ 
  public function __construct($proprietes = []) {
    $t = array_keys($proprietes);
    foreach ($t as $nom_propriete) {
      $this->__set($nom_propriete, $proprietes[$nom_propriete]);
    } 
  }

  /**
   * Accesseur magique d'une propriété de l'objet
   * @param string $prop, nom de la propriété
   * @return property value
   */     
  public function __get($prop) {
    return $this->$prop;
  }

  // Getters explicites nécessaires au moteur de templates TWIG
  public function getUtilisateur_id()       { return $this->utilisateur_id; }
  public function getNom()      { return $this->nom; }
  public function getPrenom()   { return $this->prenom; }
  public function getNom_usager()   { return $this->nom_usager; }  
  public function getCourriel() { return $this->courriel; }
  public function getMot_de_passe()      { return $this->mot_de_passe; }
  public function getAdresse()      { return $this->adresse; } 
  public function getVille()      { return $this->ville; }
  public function getProvince()      { return $this->province; } 
  public function getCode_postal()      { return $this->code_postal; } 
  public function getPays_id()      { return $this->pays_id; }
  public function getErreurs()      { return $this->erreurs; }
  
  /**
   * Mutateur magique qui exécute le mutateur de la propriété en paramètre 
   * @param string $prop, nom de la propriété
   * @param $val, contenu de la propriété à mettre à jour    
   */   
  public function __set($prop, $val) {
    $setProperty = 'set'.ucfirst($prop);
    $this->$setProperty($val);
  }

  /**
   * Mutateur de la propriété utilisateur_id 
   * @param int $utilisateur_id
   * @return $this
   */    
  public function setUtilisateur_id($utilisateur_id) {
    unset($this->erreurs['utilisateur_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $utilisateur_id)) {
      $this->erreurs['utilisateur_id'] = "Numéro d'utilisateur incorrect.";
    }
    $this->utilisateur_id = $utilisateur_id;
    return $this;
  }    

  /**
   * Mutateur de la propriété nom 
   * @param string $nom
   * @return $this
   */    
  public function setNom($nom) {
    unset($this->erreurs['nom']);
    $nom = trim($nom);
    $regExp = '/^(?:\b\w{2,}\b\s*)+$/';
    if (!preg_match($regExp, $nom)) {
      $this->erreurs['nom'] = 'Au moins 2 caractères alphabétiques pour chaque mot';
    }
    $this->nom = $nom;
    return $this;
  }

  /**
   * Mutateur de la propriété prenom 
   * @param string $prenom
   * @return $this
   */    
  public function setPrenom($prenom) {
    unset($this->erreurs['prenom']);
    $prenom = trim($prenom);
    $regExp = '/^(?:\b\w{2,}\b\s*)+$/';
    if (!preg_match($regExp, $prenom)) {
      $this->erreurs['prenom'] = 'Au moins 2 caractères alphabétiques pour chaque mot';
    }
    $this->prenom = $prenom;
    return $this;
  }

  /**
   * Mutateur de la propriété nom_usager
   * @param string $nom_usager
   * @return $this
   */    
  public function setNom_usager($nom_usager) {
    unset($this->erreurs['nom_usager']);
    $nom_usager = trim($nom_usager);
    $regExp = '/^[A-Za-z][A-Za-z0-9_]{7,29}$/';
    if (!preg_match($regExp, $nom_usager)) {
      $this->erreurs['nom_usager'] = 'Doit commencer avec une lettre. Tous les caractères 
        doivent être des lettres, des chiffres ou des caractères de soulignement (_).';
    }
    $this->nom_usager = $nom_usager;
    return $this;
  }


  /**
   * Mutateur de la propriété adresse
   * @param string $adresse
   * @return $this
   */    
  public function setAdresse($adresse) {
    unset($this->erreurs['adresse']);
    $adresse = trim($adresse);
    $regExp = '/^(?!\s*$).+/';
    if (!preg_match($regExp, $adresse)) {
      $this->erreurs['adresse'] = 'Champ vide';
    }
    $this->adresse = $adresse;
    return $this;
  }

  /**
   * Mutateur de la propriété ville
   * @param string $ville
   * @return $this
   */    
  public function setVille($ville) {
    unset($this->erreurs['ville']);
    $ville = trim($ville);
    $regExp = '/^(?!\s*$).+/';
    if (!preg_match($regExp, $ville)) {
      $this->erreurs['ville'] = 'Champ vide';
    }
    $this->ville = $ville;
    return $this;
  }

  /**
   * Mutateur de la propriété province
   * @param string $province
   * @return $this
   */    
  public function setProvince($province) {
    unset($this->erreurs['province']);
    $province = trim($province);
    $regExp = '/^(?!\s*$).+/';
    if (!preg_match($regExp, $province)) {
      $this->erreurs['province'] = 'Champ vide';
    }
    $this->province = $province;
    return $this;
  }

  /**
   * Mutateur de la propriété code_postal
   * @param string $code_postal
   * @return $this
   */    
  public function setCode_postal($code_postal) {
    unset($this->erreurs['code_postal']);
    $code_postal = trim($code_postal);
    $regExp = '/^(?!\s*$).+/';
    if (!preg_match($regExp, $code_postal)) {
      $this->erreurs['code_postal'] = 'Champ vide';
    }
    $this->code_postal = $code_postal;
    return $this;
  }

  /**
   * Mutateur de la propriété pays_id
   * @param string $pays_id
   * @return $this
   */    
  public function setPays_id($pays_id) {
    unset($this->erreurs['pays_id']);
    $pays_id = trim($pays_id);
    $regExp = '/\d+/ ';
    if (!preg_match($regExp, $pays_id)) {
      $this->erreurs['pays_id'] = 'Champ vide';
    }
    $this->pays_id = $pays_id;
    return $this;
  }

  /**
   * Mutateur de la propriété courriel
   * @param string $courriel
   * @return $this
   */    
  public function setCourriel($courriel) {
    unset($this->erreurs['courriel']);
    $courriel = trim($courriel);
    $regExp = '/\w+([.-]?\w+)*@\w+([.-]?\w+)*\.\w{2,}/';
    if (!preg_match($regExp, $courriel)) {
      $this->erreurs['courriel'] = 'Format incorrect';
    }
    $this->courriel = $courriel;
    return $this;
  }

  /**
   * Mutateur de la propriété mot_de_passe
   * @param string $mot_de_passe
   * @return $this
   */    
  public function setMot_de_passe($mot_de_passe) {
    unset($this->erreurs['mot_de_passe']);
    $mot_de_passe = trim($mot_de_passe);
    $regExp = '/(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{10,}/';
    if (!preg_match($regExp, $mot_de_passe)) {
      $this->erreurs['mot_de_passe'] = 'Au moins 10 caractères, une majuscule, une minuscule et un chiffre';
    }
    $this->mot_de_passe = $mot_de_passe;
    return $this;
  }

}