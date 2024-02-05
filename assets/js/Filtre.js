
/**
 * Classe pour gérer les filtres de la page catalogue.
 */
export default class Filtre {

    #ePays;
    #eCondition;
    #eCentrage;
    #eCouleur;
    #eCertification;
    #eAnneeDebut;
    #anneeDebut;
    #eAnneeFin;
    #anneeFin;
    #eCoupCoeur;

    /**
     * Représente le constructeur de la classe
     */
    constructor(){
        if (document.querySelector("#listePays")) {
            this.#ePays = document.querySelector("#listePays");
            this.#ePays.addEventListener('change', this.filtrer.bind(this));

            this.#eCondition = document.querySelector("#listeConditions");
            this.#eCondition.addEventListener('change', this.filtrer.bind(this));

            this.#eCentrage = document.querySelector("#listeCentrages");
            this.#eCentrage.addEventListener('change', this.filtrer.bind(this));

            this.#eCouleur = document.querySelector("#listeCouleurs");
            this.#eCouleur.addEventListener('change', this.filtrer.bind(this));

            this.#eCertification = document.querySelector("#listeCertifications");
            this.#eCertification.addEventListener('change', this.filtrer.bind(this));

            this.#eAnneeDebut = document.querySelector("#anneeDebut");
            this.#eAnneeDebut.addEventListener('keypress', this.filtrerAnneeDebut.bind(this));

            this.#eAnneeFin = document.querySelector("#anneeFin");
            this.#eAnneeFin.addEventListener('keypress', this.filtrerAnneeFin.bind(this));

            this.#anneeDebut = "";
            this.#anneeFin = "";

            this.#eCoupCoeur = document.querySelector("#listeCoupCoeur");
            this.#eCoupCoeur.addEventListener('change', this.filtrer.bind(this));

            document.querySelector("#sort-btn").addEventListener('click', this.montrerSousmenu.bind(this));
            window.addEventListener('click', this.fermerSousmenu.bind(this));
        }
    }


    /**
     * Forme l'URL et envoi la requête suite à un clic sur un élément de filtre.
     */
    filtrer() {
        window.location.href = this.formerURL();
    }

    /**
     * Forme l'URL et envoi la requête suite à un clic sur le filtre d'année de début.
     * @param {Event} evt 
     */
    filtrerAnneeDebut(evt) {

        if (evt.key == 'Enter') {
            const anneeDebut = this.#eAnneeDebut.value;

            if (anneeDebut == "") {
                this.filtrer();
            }
            else {
                if (this.isAnneeValide(anneeDebut)) {
                    this.filtrer();
                }
            }
        }
    }

    /**
     * Forme l'URL et envoi la requête suite à un clic sur le filtre d'année de fin.
     * @param {Event} evt 
     */
    filtrerAnneeFin(evt) {

        if (evt.key == 'Enter') {
            const anneeFin = this.#eAnneeFin.value;

            if (anneeFin == "") {
                this.filtrer();
            }
            else {
                if (this.isAnneeValide(anneeFin)) {
                    this.filtrer();
                }
            }
        }
    }

    /**
     * Valide l'année dans le filtre d'année de création du timbre.
     * @param {string} annee 
     * @returns 
     */
    isAnneeValide(annee) {

        // Validation de l'année
        const regexAnnee = /^\d{4}/;

        if (annee.match(regexAnnee)) {
            const anneeActuelle = new Date().getFullYear();
            if (annee <= anneeActuelle) {
                return true;
            }
        }
        return false;
    }

    /**
     * Forme l'URL suite à un clic sur un élément de tri.
     * @returns string
     */
    formerURL() {
        let url_page = "";
        let url_queries = [];
        let url = "";
        let premierSeparateur = "";

        // Catalogue ou recherche
        const keywords = document.querySelector("#valeurRecherche").value;
        if (keywords) {
            url_page += "recherche?" + "keywords=" + keywords;
            premierSeparateur = "&";
        } else {
            url_page += "catalogue";
            premierSeparateur = "?";
        }

        // Enchères courantes ou passées
        const temps = document.querySelector("#valeurTemps").value;
        if (temps != "") {
            url_queries.push(`temps=${temps}`);
        }


        // Enchères courantes ou passées
        const tri = document.querySelector("#valeurTri").value;
        if (tri != "") {
            url_queries.push(`tri=${tri}`);
        }

        // Pays
        const pays_id = this.#ePays.value;
        if (pays_id != 0) {
            url_queries.push(`pays=${pays_id}`);
        }

        // Condition
        const condition_id = this.#eCondition.value;
        if (condition_id != 0) {
            url_queries.push(`condition=${condition_id}`);
        }

        // Centrage
        const centrage_id = this.#eCentrage.value;
        if (centrage_id != 0) {
            url_queries.push(`centrage=${centrage_id}`);
        }

        // Couleur
        const couleur_id = this.#eCouleur.value;
        if (couleur_id != 0) {
            url_queries.push(`couleur=${couleur_id}`);
        }

        // Certification
        const certification = this.#eCertification.value;
        if (certification != 2) {
            url_queries.push(`certification=${certification}`);
        }

        // Année début
        const anneeDebut = this.#eAnneeDebut.value;
        if (anneeDebut != "") {
            if (this.isAnneeValide(anneeDebut)) {
                url_queries.push(`anneeDebut=${anneeDebut}`);
            }
        }

        // Année fin
        const anneeFin = this.#eAnneeFin.value;
        if (anneeFin != "") {
            if (this.isAnneeValide(anneeFin)) {
                url_queries.push(`anneeFin=${anneeFin}`);
            }
        }
    
        // Coup de coeur
        const coupCoeur = this.#eCoupCoeur.value;
        if (coupCoeur != 2) {
            url_queries.push(`coupCoeur=${coupCoeur}`);
        }

        if (url_queries.length != 0) {
            url = url_page + premierSeparateur + url_queries.join('&');
        }
        else {
            url = url_page;
        }
        return url;
    }

    /**
     * Montre le sous-menu de tri.
     */
    montrerSousmenu() {
        document.getElementById("myDropdown").classList.toggle("afficher-sousmenu");
    }

    /**
     * Ferme le sous-menu de tri sur un clic dans la fenêtre.
     * @param {Event} evt 
     */
    fermerSousmenu(evt) {

        if (! (evt.target.matches('.sort-btn') || evt.target.matches('.sort-span'))) {
            let dropdowns = document.getElementsByClassName("dropdown-content");
            let i;
            for (i = 0; i < dropdowns.length; i++) {
                let openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('afficher-sousmenu')) {
                    openDropdown.classList.remove('afficher-sousmenu');
                }
            }
        }

    }
}

