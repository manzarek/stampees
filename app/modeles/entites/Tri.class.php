<?php

/**
 * Classe de Tri pour la page Catalogue.
 *
 */
class Tri
{
    private $tri;

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
    public function getTri()      { return $this->tri; }

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
     * Mutateur de la propriété tri
     * @param int $tri
     * @return $this
     */    
    public function setTri($tri) {
        $this->tri = $tri;
        return $this;
    }

    /**
     * Tri un tableau de donnée selon le type de tri spécifié dans le constructeur.
     * @param $encheres
     */
    public function trier($encheres) {
        switch($this->tri) {
            case 'prix_asc':
                usort($encheres, array('Tri', 'trierPrixCroissant'));
                break;
            case 'prix_desc':
                usort($encheres, array('Tri','trierPrixDecroissant'));
                break;
            case 'date_fin':
                usort($encheres, array('Tri','trierDate'));
                break;
        }
        return $encheres;
    }

    /**
     * Fonction de tri par prix croissant
     */
    public function trierPrixCroissant($a, $b) {
        $champ = 'mise_minimum';
        if ($a[$champ] == $b[$champ]) {
            return 0;
        }
        return ($a[$champ] < $b[$champ]) ? -1 : 1;
    }

    /**
     * Fonction de tri par prix décroissant
     */
    public function trierPrixDecroissant($a, $b) {
        $champ = 'mise_minimum';
        if ($a[$champ] == $b[$champ]) {
            return 0;
        }
        return ($a[$champ] > $b[$champ]) ? -1 : 1;
    }

    /**
     * Fonction de tri par date
     */
    public function trierDate($a, $b) {
        $t1 = strtotime($a["date_fin"]);
        $t2 = strtotime($b["date_fin"]);
    
        return ($t1 - $t2);
    }

}