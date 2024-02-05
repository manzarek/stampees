<?php

/**
 * Classe Utilitaire pour traiter des requêtes SQL provenant de d'autres
 * contrôleurs. Cette classe ne devrait pas apparaître dans le table de routage.
 */
class Utilitaire extends Routeur {

    /**
     * Constructeur qui initialise la propriété oRequetesSQL déclarée dans la 
     * classe Routeur.
     * 
     */
    public function __construct() {
        $this->oRequetesSQL = new RequetesSQL;
    }

    /**
     * Retourne la mise minimum à effectuer pour une enchère donnée.
     * La mise est formattée et prête pour affichage.
     * @param int $enchere_id
     * @return string
     */
    public function getMiseMinimumFormattee($enchere_id) {
        return number_format((float)$this->determinerMiseMinimum($enchere_id), 2, '.', '');
    }

    /**
     * Détermine la mise minimum à effectuer pour une enchère donnée.
     * L'incrément sur la nouvelle mise varie selon le montant.
     * @param int $enchere_id
     * @return float
     */
    public function determinerMiseMinimum($enchere_id) {

        $enchere = $this->oRequetesSQL->getEnchere($enchere_id);
        $mises = $this->oRequetesSQL->getMises($enchere_id);

        $prixPlancher = $enchere['prix_plancher'];
        
        if (!$mises) {
            $derniereMise = $prixPlancher;
        }
        else {
            $derniereMise = $mises[0]['mise'];
        }

        if ($derniereMise < 20) {
            $increment = 0.5;
        }
        else if ($derniereMise < 100) {
            $increment = 1.0;
        }
        else if ($derniereMise < 300) {
            $increment = 2.50;
        }
        else if ($derniereMise < 500) {
            $increment = 5.00;
        }
        else {
            $increment = 10.00;
        }

        $miseMinimum = $derniereMise + $increment;
        return $miseMinimum;
    }

    /**
     * Détermine la différence de temps entre une date spécifiée en
     * paramètre et la date actuelle. Si la différence est négative,
     * la chaine "Terminé" est retournée. Sinon, la chaine est formattée
     * et prête pour affichage.
     * @param string $date_fin
     * @return string
     */
    public function determinerTempsRestant($date_fin) {
        $date_heure_actuelle = new DateTime();
        $date_heure_fin = new DateTime($date_fin);

        $intervalle = $date_heure_actuelle->diff($date_heure_fin);
        $resultat = "";

        if ($intervalle->invert == 1) {
            $resultat = "Terminé";
        }
        else {
            $resultat = $intervalle->d . "j " . $intervalle->h . "h " . $intervalle->i . "m";
        }
        return $resultat;
    }
}
