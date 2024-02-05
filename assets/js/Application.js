import Decompte from "./Decompte.js";
import Fetch from "./Fetch.js";
import Filtre from "./Filtre.js";
import Recherche from "./Recherche.js";
import Zoom from "./Zoom.js";

export default class Application{

    #eMessageErreurConnexion;
    #eModaleConnexion;
    #eUtilisateur;
    #eConnecter;
    #eDeconnecter;
    #eCreerEnchere;
    #eVoirFavoris;
    #ePlacerMise;
    #eAjouterFavori;
    #eRetirerFavori;
    #eAjouterCoupCoeur;
    #eRetirerCoupCoeur;
    #formConnexion;
    #formMise;
    #btnConnexion;
    #miseConfirmation;
    #oZoom;
    #oDecompte;
    #eRecherche;
    #oRecherche;
    #eFiltre;
    #oFiltre;
    
    /**
     * Constructeur de la classe Application
     */
    constructor() {
        this.#eMessageErreurConnexion = document.getElementById('messageErreurConnexion');
        this.#eModaleConnexion        = document.getElementById('modaleConnexion');
        this.#eUtilisateur            = document.getElementById('utilisateur');
        this.#eConnecter              = document.getElementById('connecter'); 
        this.#eDeconnecter            = document.getElementById('deconnecter'); 
        this.#eCreerEnchere           = document.getElementById('creerEnchere');
        this.#eVoirFavoris            = document.getElementById('voirFavoris');
        this.#formConnexion           = document.getElementById('formConnexion');
        this.#btnConnexion            = document.getElementById('boutonConnexion');
        this.#ePlacerMise             = document.getElementById('placerMise');
        this.#eAjouterFavori          = document.getElementById('ajouterFavori');
        this.#eRetirerFavori          = document.getElementById('retirerFavori');
        this.#eAjouterCoupCoeur       = document.getElementById('ajouterCoupCoeur');
        this.#eRetirerCoupCoeur       = document.getElementById('retirerCoupCoeur');
        this.#eRecherche              = document.getElementById('champ-recherche');
        this.#eFiltre                 = document.getElementById('filtres');       

        this.#eConnecter.addEventListener('click', this.afficherFenetreConnection.bind(this));
        this.#btnConnexion.addEventListener('click', this.traiterFormulaireConnection.bind(this));
        this.#eDeconnecter.addEventListener('click', this.deconnecter.bind(this));

        // Si on est sur la fiche d'enchère
        if (this.#ePlacerMise) {
            this.#ePlacerMise.addEventListener('click', this.verifierMise.bind(this));
        }

        if(this.#eAjouterFavori) {
            this.#eAjouterFavori.addEventListener('click', this.ajouterFavori.bind(this));
        }

        if(this.#eRetirerFavori) {
            this.#eRetirerFavori.addEventListener('click', this.retirerFavori.bind(this));
        }

        if(this.#eAjouterCoupCoeur) {
            this.#eAjouterCoupCoeur.addEventListener('click', this.ajouterCoupCoeur.bind(this));
        }

        if(this.#eRetirerCoupCoeur) {
            this.#eRetirerCoupCoeur.addEventListener('click', this.retirerCoupCoeur.bind(this));
        }

        if (document.querySelector("#imagePrincipale")) {
            this.#oZoom = new Zoom("imagePrincipale", "imageZoom");
        }

        if (document.querySelector("#tempsRestant")) {
            this.#oDecompte = new Decompte();
        }

        if(this.#eRecherche) {
            this.#oRecherche = new Recherche(this.#eRecherche);
        }

        if(this.#eFiltre) {
            this.#oFiltre = new Filtre();
        }

    }


    /**
     * Affichage de la fenêtre modale
     */
    afficherFenetreConnection() {
        this.#eMessageErreurConnexion.innerHTML = "&nbsp;";
        document.getElementById('boutonFermer').addEventListener('click', this.fermerModaleConnexion.bind(this));
        document.getElementById('modaleConnexion').showModal();
        console.log("afficher");
    }

    /**
     * Fonction de rappel du bouton fermer de la modale de connexion. Ferme la fenêtre modale.
     * @param {Event} event
     */
    fermerModaleConnexion(event) {
        event.preventDefault();
        document.getElementById('modaleConnexion').close();
    }

    /**
     * Traitement du formulaire dans la fenêtre modale
     * @param {Event} event
     */
    traiterFormulaireConnection(event) {
        event.preventDefault();
        let fd = new FormData(this.#formConnexion);
        const oFetch = new Fetch();
        oFetch.connecter(fd, this.confirmerConnection.bind(this));
    }

    /**
     * Fonction de rappel de la connection. Affiche un message d'erreur s'il y a un problème
     * de connection ou une mauvaise combinaison courriel/mot de passe.
     * @param {Object} utilisateur 
     * @param {string} msgErreur 
     */
    confirmerConnection(utilisateur, msgErreur) {
        if (msgErreur) {
            this.#eMessageErreurConnexion.innerHTML = msgErreur;
        }
        else {
            if (!utilisateur) {
                this.#eMessageErreurConnexion.innerHTML = "Courriel ou mot de passe incorrect.";
            } else {
                this.#eUtilisateur.innerHTML = utilisateur.prenom + " " + utilisateur.nom;
                this.#eConnecter  .classList.add('hidden-menu');
                this.#eDeconnecter.classList.remove('hidden-menu');
                this.#eCreerEnchere.classList.remove('hidden-menu');
                this.#eVoirFavoris.classList.remove('hidden-menu');
                this.#eModaleConnexion.close();
                sessionStorage.setItem("login", true);
                
                this.reafficherPage();
            }
        }
    }

    /**
     * Déconnecter l'usager.
     */
    deconnecter() {
        let oFetch = new Fetch();
        oFetch.deconnecter(this.adapterInterfaceDeconnection.bind(this));
    }

    /**
     * Adapte l'interface usager suite à une déconnection.
     */
    adapterInterfaceDeconnection(codeRetour) {
        if (codeRetour) {
            this.#eUtilisateur.innerHTML = "";
            this.#eDeconnecter.classList.add('hidden-menu');
            this.#eConnecter  .classList.remove('hidden-menu');
            this.#eCreerEnchere.classList.add('hidden-menu');
            this.#eVoirFavoris.classList.add('hidden-menu');
            sessionStorage.setItem("login", false);
            this.reafficherPage();
        }
        else {
            this.afficherModaleErreur("Problème technique lors de la déconnection. Merci de nous contacter.");
        }
    }

    /**
     * Confirmation de mise
     * @param {Event} evt
     */
    verifierMise(evt) {
        evt.preventDefault();

        if (sessionStorage.getItem("login") == "true" ) {

            // Validation pour le montant minimum de la mise
            const miseMinimum = document.querySelector("#miseMinimum").value;
            const mise = document.querySelector("#montantMise").value;

            const regexMise = /^[0-9]+(\.[0-9][0-9])?$/;
            if (mise.match(regexMise)) {
                if (parseFloat(mise) < parseFloat(miseMinimum)) {
                    const msgErreur = "La mise doit être supérieure ou égale à la mise minimum.";
                    this.afficherModaleErreur(msgErreur);
                    console.log("icitte");
                }
                else {
                    this.confirmerMiseUsager(mise);
                }
            }
            else {
                const msgErreur = "Format de mise incorrect.";
                this.afficherModaleErreur(msgErreur);
            }

        } else {
            this.afficherFenetreConnection();
        }
    }

    /**
     * Affiche une fenêtre modale d'erreur.
     * @param {string} message 
     */
    afficherModaleErreur(message) {
        document.getElementById('msgErreur').innerHTML = message;
        document.querySelector("#modaleErreur .fermerModale").addEventListener('click', this.fermerModale.bind(this));

        const modaleErreur =  document.getElementById('modaleErreur');
        modaleErreur.showModal();
    }

    /**
     * Fonction de rappel pour fermer la fenêtre modale d'erreur.
     */
    fermerModale() {
        document.getElementById('modaleErreur').close();
    }

    /**
     * Affiche une fenêtre de confirmation pour placer une mise.
     * @param {string} mise 
     */
    confirmerMiseUsager(mise) {

        document.getElementById('mise').innerHTML = "Montant: " + Number(mise).toFixed(2) + "$";
        this.#miseConfirmation = document.getElementById('miseConfirmation');

        document.getElementById('btnConfirmerMise').addEventListener('click', this.placerMise.bind(this));
        document.getElementById('btnAnnulerMise').addEventListener('click', () => {
            this.#miseConfirmation.close();
        });

        this.#miseConfirmation.showModal();
    }

    /**
     * Envoi la requête pour placer une mise.
     */
    placerMise(){
        const formulaireMise = document.getElementById('formMise');
        const fd = new FormData(formulaireMise);
        const oFetch = new Fetch();
        oFetch.placerMise(fd, this.confirmerMiseTransaction.bind(this));
    }

    /**
     * Affiche une fenêtre pour confirmer que la mise a bien été placée, sinon
     * une fenêtre avec le message d'erreur.
     * @param {string} msgErreur 
     * @param {Object} reponse 
     */
    confirmerMiseTransaction(msgErreur, reponse = null) {

        this.#miseConfirmation.close();
        if (msgErreur) {
            this.afficherModaleErreur(msgErreur);
        } else {
            if (!reponse) {
                this.afficherModaleErreur("Problème technique sur le serveur: Aucune réponse.");
            }
            else {
                if (reponse['statut'] != "OK") {
                    this.afficherModaleErreur("Problème technique sur le serveur.");
                }
                else {
                    const titre ="Confirmation de mise";
                    const msg = "Votre mise a bien été enregistée";
                    this.afficherConfirmation(this.reafficherPage.bind(this), msg, titre);
                }
            }
        }
    }

    /**
     * Affiche un message de confirmation pour l'usager.
     * @param {function} cb 
     * @param {string} message 
     * @param {string} titre 
     */
    afficherConfirmation(cb, message, titre = "Confirmation") {
        const modaleConfirmation = document.querySelector("#modaleConfirmation");
        document.querySelector("#titreConfirmation").innerHTML = titre;
        document.querySelector("#msgConfirmation").innerHTML = message;
        
        modaleConfirmation.showModal();
        document.querySelector("#modaleConfirmation .fermerModale").addEventListener('click', () => {
            modaleConfirmation.close();
            cb();
        });


    }

    /**
     * Réaffiche la page courante.
     */
    reafficherPage() {
        window.location.reload();
    }

    /**
     * Vérifie la réponse renvoyée par le serveur suite à une requête.
     * @param {string} msgErreur 
     * @param {Object} reponse 
     */
    verifierReponse(msgErreur, reponse = null) {
        if (msgErreur) {
            this.afficherModaleErreur(msgErreur);
        } else {
            if (!reponse) {
                this.afficherModaleErreur("Problème technique sur le serveur: Aucune réponse.");
            }
            else {
                if (reponse['statut'] != "OK") {
                    this.afficherModaleErreur(reponse['statut']);
                }
                else {
                    this.reafficherPage();
                }
            }
        }
    }

    /**
     * Ajoute un favori.
     * @param {Event} evt 
     */
    ajouterFavori(evt) {
        evt.preventDefault();

        if (sessionStorage.getItem("login") == "true" ) { 
            const formulaireFavori = document.getElementById('formFavori');
            const fd = new FormData(formulaireFavori);
            const oFetch = new Fetch();
            oFetch.ajouterFavori(fd, this.verifierReponse.bind(this));
        }
        else {
            this.afficherFenetreConnection();
        }
    }

    /**
     * Retire un favori.
     * @param {Event} evt 
     */
    retirerFavori(evt) {
        evt.preventDefault();

        if (sessionStorage.getItem("login") == "true" ) { 
            const formulaireFavori = document.getElementById('formFavori');
            const fd = new FormData(formulaireFavori);
            const oFetch = new Fetch();
            oFetch.retirerFavori(fd, this.verifierReponse.bind(this));
        }
        else {
            this.afficherFenetreConnection();
        }

    }

    /**
     * Ajout un coup-de-coeur à une enchère.
     * @param {Event} evt 
     */
    ajouterCoupCoeur(evt) {
        evt.preventDefault();

        if (sessionStorage.getItem("login") == "true" ) { 
            const formCoupCoeur = document.getElementById('formCoupCoeur');
            const fd = new FormData(formCoupCoeur);
            const oFetch = new Fetch();
            oFetch.ajouterCoupCoeur(fd, this.verifierReponse.bind(this));
        }
        else {
            this.afficherFenetreConnection();
        }
    }

    /**
     * Retire un coup-de-coeur à une enchère.
     * @param {Event} evt 
     */
    retirerCoupCoeur(evt) {
        evt.preventDefault();

        if (sessionStorage.getItem("login") == "true" ) { 
            const formCoupCoeur = document.getElementById('formCoupCoeur');
            const fd = new FormData(formCoupCoeur);
            const oFetch = new Fetch();
            oFetch.retirerCoupCoeur(fd, this.verifierReponse.bind(this));
        }
        else {
            this.afficherFenetreConnection();
        }
    }
}