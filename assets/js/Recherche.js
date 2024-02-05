
export default class Recherche {

    #eRecherche;

    /**
     * Constructeur de la classe Recherche
     */
    constructor(eRecherche) {
        this.#eRecherche = eRecherche;
        console.log(this.#eRecherche);
        this.#eRecherche.addEventListener('keypress', this.entrerRechercheClavier.bind(this));
        document.querySelector("#loupe").addEventListener('click', this.entrerRechercheBouton.bind(this));
    }

    entrerRechercheClavier(evt) {
        if (evt.key == "Enter") {
            evt.preventDefault();
            this.rechercher();
        }
    }

    entrerRechercheBouton(evt) {
        this.rechercher();
    }

    rechercher() {
        let keywords = this.#eRecherche.value;
        if (keywords != "") {
            const urlRecherche = "recherche?keywords=" + keywords;
            const urlEncode = encodeURI(urlRecherche);

            window.location.href = urlEncode;
        }
    }
}