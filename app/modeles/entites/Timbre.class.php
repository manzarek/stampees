<?php

/**
 * Classe de l'entité Timbre
 *
 */
class Timbre
{

    private $timbre_id;
    private $titre;
    private $date_creation;
    private $tirage;
    private $dimensions;
    private $certification;
    private $pays_id;
    private $condition_id;
    private $centrage_id;
    private $enchere_id;
    private $utilisateur_id;
    private $couleur_id;

    private $erreurs = array();

    const ANNEE_CREATION_MINIMUM = 1840;

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
    public function getTimbre_id()       { return $this->timbre_id; }
    public function getTitre()      { return $this->titre; }
    public function getDate_creation()      { return $this->date_creation; }
    public function getTirage()      { return $this->tirage; }
    public function getDimensions()      { return $this->dimensions; }
    public function getCertification()      { return $this->certification; }
    public function getPays_id()      { return $this->pays_id; }
    public function getCondition_id()      { return $this->condition_id; }
    public function getCentrage_id()      { return $this->centrage_id; }
    public function getEnchere_id()      { return $this->enchere_id; }
    public function getUtilisateur_id()       { return $this->utilisateur_id; }
    public function getCouleur_id()       { return $this->couleur_id; }
    public function getErreurs()              { return $this->erreurs; }

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
     * Mutateur de la propriété timbre_id 
     * @param int $timbre_id
     * @return $this
     */    
    public function setTimbre_id($timbre_id) {
        unset($this->erreurs['timbre_id']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $timbre_id)) {
            $this->erreurs['timbre_id'] = "Numéro de timbre incorrect.";
        }
        $this->timbre_id = $timbre_id;
        return $this;
    }

    /**
     * Mutateur de la propriété titre
     * @param int $titre
     * @return $this
     */    
    public function setTitre($titre) {
        unset($this->erreurs['titre']);
        $regExp = '/^(?!\s*$).+/';
        if (!preg_match($regExp, $titre)) {
            $this->erreurs['titre'] = "Champ vide";
        }
        $this->titre = $titre;
        return $this;
    }

    /**
     * Mutateur de la propriété date_creation
     * @param int $date_creation
     * @return $this
     */    
    public function setDate_creation($date_creation) {
        unset($this->erreurs['date_creation']);
        $regExp = '/^\d*$/';
        if (!preg_match($regExp, $date_creation)) {
            $this->erreurs['date_creation'] = "Année invalide";
        }
        else {
            if (!empty($date_creation)) {
                if ($date_creation < self::ANNEE_CREATION_MINIMUM || $date_creation > date("Y")) {
                    $this->erreurs['date_creation'] = "Année de création invalide";
                }
            }
        }
        $this->date_creation = $date_creation;
        return $this;
    }

    /**
     * Mutateur de la propriété tirage
     * @param int $tirage
     * @return $this
     */    
    public function setTirage($tirage) {
        unset($this->erreurs['tirage']);
        $this->tirage = $tirage;
        return $this;
    }

    /**
     * Mutateur de la propriété dimensions
     * @param int $dimensions
     * @return $this
     */    
    public function setDimensions($dimensions) {
        unset($this->erreurs['dimensions']);
        $this->dimensions = $dimensions;
        return $this;
    }

    /**
     * Mutateur de la propriété certification
     * @param int $certification
     * @return $this
     */    
    public function setCertification($certification) {
        unset($this->erreurs['certification']);
        $this->certification = $certification;
        return $this;
    }

    /**
     * Mutateur de la propriété pays_id 
     * @param int $pays_id
     * @return $this
     */    
    public function setPays_id($pays_id) {
        unset($this->erreurs['pays_id']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $pays_id)) {
            $this->erreurs['pays_id'] = "Numéro d'utilisateur incorrect.";
        }
        $this->pays_id = $pays_id;
        return $this;
    }

    /**
     * Mutateur de la propriété condition_id
     * @param int $condition_id
     * @return $this
     */    
    public function setCondition_id($condition_id) {
        unset($this->erreurs['condition_id']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $condition_id)) {
            $this->erreurs['condition_id'] = "Numéro de condition incorrect.";
        }
        $this->condition_id = $condition_id;
        return $this;
    }

    /**
     * Mutateur de la propriété centrage_id
     * @param int $centrage_id
     * @return $this
     */    
    public function setCentrage_id($centrage_id) {
        unset($this->erreurs['centrage_id']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $centrage_id)) {
            $this->erreurs['centrage_id'] = "Numéro de centrage incorrect.";
        }
        $this->centrage_id = $centrage_id;
        return $this;
    }

    /**
     * Mutateur de la propriété enchere_id
     * @param int $enchere_id
     * @return $this
     */    
    public function setEnchere_id($enchere_id) {
        unset($this->erreurs['enchere_id']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $enchere_id)) {
            $this->erreurs['enchere_id'] = "Numéro d'enchère incorrect.";
        }
        $this->enchere_id = $enchere_id;
        return $this;
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
     * Mutateur de la propriété couleur_id 
     * @param int $couleur_id 
     * @return $this
     */    
    public function setCouleur_id($couleur_id) {
        unset($this->erreurs['couleur_id']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $couleur_id)) {
            $this->erreurs['couleur_id'] = "Numéro de couleur incorrect.";
        }
        $this->couleur_id = $couleur_id;
        return $this;
    }

    /**
     * Mutateur de la propriété date_debut
     * @param int $date_debut
     * @return $this
     */    
    public function setDate_debut($date_debut) {
        unset($this->erreurs['date_debut']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $date_debut)) {
            $this->erreurs['date_debut'] = "Champ invalide";
        }
        $this->date_debut = $date_debut;
        return $this;
    }

    /**
     * Mutateur de la propriété date_fin
     * @param int $date_fin
     * @return $this
     */    
    public function setDate_fin($date_fin) {
        unset($this->erreurs['date_debut']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $date_fin)) {
            $this->erreurs['date_fin'] = "Champ invalide";
        }
        $this->date_fin = $date_fin;
        return $this;
    }

    /**
     * Mutateur de la propriété prix_plancher
     * @param int $prix_plancher
     * @return $this
     */    
    public function setPrix_plancher($prix_plancher) {
        unset($this->erreurs['prix_plancher']);
        $regExp = '/^[0-9]+(\.[0-9][0-9])?$/';
        if (!preg_match($regExp, $prix_plancher)) {
            $this->erreurs['prix_plancher'] = "Format de prix incorrect.";
        }
        $this->prix_plancher = $prix_plancher;
        return $this;
    }
}