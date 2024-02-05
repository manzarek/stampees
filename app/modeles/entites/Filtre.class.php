<?php

/**
 * Classe de Filtre
 *
 */
class Filtre
{
    private $temps;
    private $pays;
    private $condition;
    private $centrage;
    private $couleur;
    private $certification;
    private $annee_debut;
    private $annee_fin;
    private $coup_coeur;


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
    public function getTemps()      { return $this->temps; }
    public function getPays()       { return $this->pays; }
    public function getCondition()       { return $this->condition; }
    public function getCentrage()       { return $this->centrage; }
    public function getCouleur()       { return $this->couleur; }
    public function getCertification()       { return $this->certification; }
    public function getAnnee_debut()       { return $this->annee_debut; }
    public function getAnnee_fin()       { return $this->annee_fin; }
    public function getCoup_coeur()       { return $this->coup_coeur; } 

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
     * Mutateur de la propriété temps
     * @param int $temps
     * @return $this
     */    
    public function setTemps($temps) {
        $this->temps = $temps;
        return $this;
    }

    /**
     * Mutateur de la propriété pays
     * @param int $pays
     * @return $this
     */    
    public function setPays($pays) {
        $this->pays = $pays;
        return $this;
    }

    /**
     * Mutateur de la propriété condition
     * @param int $condition
     * @return $this
     */    
    public function setCondition($condition) {
        $this->condition = $condition;
        return $this;
    }

    /**
     * Mutateur de la propriété centrage
     * @param int $centrage
     * @return $this
     */    
    public function setCentrage($centrage) {
        $this->centrage = $centrage;
        return $this;
    }

    /**
     * Mutateur de la propriété couleur
     * @param int $couleur
     * @return $this
     */    
    public function setCouleur($couleur) {
        $this->couleur = $couleur;
        return $this;
    }

    /**
     * Mutateur de la propriété certification
     * @param int $certification
     * @return $this
     */    
    public function setCertification($certification) {
        $this->certification = $certification;
        return $this;
    }

    /**
     * Mutateur de la propriété annee_debut
     * @param int $annee_debut
     * @return $this
     */    
    public function setAnnee_debut($annee_debut) {
        $this->annee_debut = $annee_debut;
        return $this;
    }

    /**
     * Mutateur de la propriété annee_fin
     * @param int $annee_debut
     * @return $this
     */    
    public function setAnnee_fin($annee_fin) {
        $this->annee_fin = $annee_fin;
        return $this;
    }

    /**
     * Mutateur de la propriété coup_coeur
     * @param int $coup_coeur
     * @return $this
     */    
    public function setCoup_coeur($coup_coeur) {
        $this->coup_coeur = $coup_coeur;
        return $this;
    }

    /**
     * Filtre les enchères à partir des différents critères donnés au constructeur.
     * @param array $encheres
     * @return array le tableau d'enchères filtrées
     */
    public function filtrer($encheres) {

        $resultats = [];

        // Filtrage des enchères courantes/passées
        if ($this->temps) {
            $resultats = array_filter($encheres, function($enchere) {

                if ($this->temps == 'courant') {
                    return ($enchere['temps_restant'] != "Terminé") ? true : false;
                } else if ($this->temps == 'passe') {
                    return ($enchere['temps_restant'] == "Terminé") ? true : false;
                } else {
                    return true;
                }

            });
        } else {
            $resultats = $encheres;
        }

        // Filtrage des pays
        if ($this->pays) {
            $resultats = array_filter($resultats, function($enchere) {
                return ($this->pays == $enchere['pays_id']) ? true : false;
            });
        }

        // Filtrage des conditions
        if ($this->condition) {
            $resultats = array_filter($resultats, function($enchere) {
                return ($this->condition == $enchere['condition_id']) ? true : false;
            });
        } 

        // Filtrage des centrages
        if ($this->centrage) {
            $resultats = array_filter($resultats, function($enchere) {
                return ($this->centrage == $enchere['centrage_id']) ? true : false;
            });
        } 

        // Filtrage des couleurs
        if ($this->couleur) {
            $resultats = array_filter($resultats, function($enchere) {
                return ($this->couleur == $enchere['couleur_id']) ? true : false;
            });
        } 

        // Filtrage de la certification
        // Ici la valeur 2 indique non spécifié, donc non filtré
        if ($this->certification != 2) {
            $resultats = array_filter($resultats, function($enchere) {
                return ($this->certification == $enchere['certification']) ? true : false;
            });
        }

        // Filtrage année début
        if ($this->annee_debut) {
            $resultats = array_filter($resultats, function($enchere) {
                $dateCreation = new DateTime($enchere['date_creation']);
                $dateCreation = $dateCreation->format("Y");
                if ($dateCreation == "0000") return false;
                return ($dateCreation >= $this->annee_debut) ? true : false;
            });
        }

        // Filtrage année début
        if ($this->annee_fin) {
            $resultats = array_filter($resultats, function($enchere) {
                $dateCreation = new DateTime($enchere['date_creation']);
                $dateCreation = $dateCreation->format("Y");
                if ($dateCreation == "0000") return false;
                return ($dateCreation <= $this->annee_fin) ? true : false;
            });
        }

        // Filtrage des coups de coeur
        // Ici la valeur 2 indique non spécifié, donc non filtré
        if ($this->coup_coeur != 2) {
            $resultats = array_filter($resultats, function($enchere) {
                return ($this->coup_coeur == $enchere['coup_coeur']) ? true : false;
            });
        }

        return $resultats;
    }
}
