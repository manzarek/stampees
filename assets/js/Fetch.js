/**
 * Classe qui regroupe toutes les connections Fetch faites au serveur.
 */

export default class Fetch {


    /**
     * Représente le constructeur de la classe
     */
    constructor(){

    }

    /**
     * Pour faire la connection du membre.
     * @param {Object} formData 
     * @param {function} cb 
     */
    connecter(formData, cb) {
        let utilisateur;
        let msgErreur = "";

        fetch('connecter', {method: 'post', body: formData})
            .then (reponse => reponse.json())
            .then (utilisateur => {
                cb(utilisateur, msgErreur);
            })
            .catch(() => {
                msgErreur = "Problème technique sur le serveur.";
                cb(utilisateur, msgErreur);
            });
    }

    /**
     * Pour faire la déconnection du membre.
     * @param {function} cb 
     */
    deconnecter(cb) {
        fetch('deconnecter')
            .then (reponse => reponse.json())
            .then (codeRetour => {
                cb(codeRetour);
            });
    }

    /**
     * Pour placer une mise.
     * @param {Object} formData 
     * @param {function} cb 
     */
    placerMise(formData, cb) {
        let msgErreur;
        fetch('placerMise', {method: 'post', body: formData})
            .then (reponse => reponse.json())
            .then (reponse => {
                cb(msgErreur, reponse);
            })
        .catch(() => {
            msgErreur = "Problème technique sur le serveur.";
            cb(msgErreur, reponse);
        });
    }

    /**
     * Pour ajouter un favori.
     * @param {Object} formData 
     * @param {function} cb 
     */
    ajouterFavori(formData, cb) {
        let msgErreur;
        fetch('ajouterFavori', {method: 'post', body: formData})
            .then (reponse => reponse.json())
            .then (reponse => {
                cb(msgErreur, reponse);
            })
        .catch(() => {
            msgErreur = "Problème technique sur le serveur.";
            cb(msgErreur, reponse);
        });
    }

    /**
     * Pour retirer un favori.
     * @param {Object} formData 
     * @param {function} cb 
     */
    retirerFavori(formData, cb) {
        let msgErreur;
        fetch('retirerFavori', {method: 'post', body: formData})
            .then (reponse => reponse.json())
            .then (reponse => {
                cb(msgErreur, reponse);
            })
        .catch(() => {
            msgErreur = "Problème technique sur le serveur.";
            cb(msgErreur, reponse);
        });
    }

    /**
     * Pour ajouter un coup de coeur.
     * @param {Object} formData 
     * @param {function} cb 
     */
    ajouterCoupCoeur(formData, cb) {
        let msgErreur;
        fetch('ajouterCoupCoeur', {method: 'post', body: formData})
            .then (reponse => reponse.json())
            .then (reponse => {
                cb(msgErreur, reponse);
            })
        .catch(() => {
            msgErreur = "Problème technique sur le serveur.";
            cb(msgErreur, reponse);
        });
    }

    /**
     * Pour retirer un coup de coeur.
     * @param {Object} formData 
     * @param {function} cb 
     */
    retirerCoupCoeur(formData, cb) {
        let msgErreur;
        fetch('retirerCoupCoeur', {method: 'post', body: formData})
            .then (reponse => reponse.json())
            .then (reponse => {
                cb(msgErreur, reponse);
            })
        .catch(() => {
            msgErreur = "Problème technique sur le serveur.";
            cb(msgErreur, reponse);
        });
    }
}