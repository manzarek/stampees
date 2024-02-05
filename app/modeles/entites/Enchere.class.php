<?php

/**
 * Classe de l'entité Enchère
 *
 */
class Enchere
{

    private $enchere_id;
    private $utilisateur_id;
    private $date_debut;
    private $date_fin;
    private $prix_plancher;    

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
    public function getEnchere_id()      { return $this->enchere_id; }
    public function getUtilisateur_id()       { return $this->utilisateur_id; }
    public function getDate_debut()       { return $this->date_debut; }
    public function getDate_fin()       { return $this->date_fin; }
    public function getPrix_plancher()       { return $this->prix_plancher; }
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
        unset($this->erreurs['date_fin']);
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