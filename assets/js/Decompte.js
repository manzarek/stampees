export default class Decompte {


    /**
     * Représente le constructeur de la classe
     */
    constructor(){
        // Set the date we're counting down to
        const dateFin = document.querySelector("#dateFin").innerHTML;
        this.countDownDate = new Date(dateFin).getTime();

        // Update the count down every 1 second
        this.intervalle = setInterval(this.afficherDecompte.bind(this), 1000);
    }

    afficherDecompte() {

        // Get today's date and time
        const now = new Date().getTime();

        // Find the distance between now and the count down date
        const distance = this.countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        let days = Math.floor(distance / (1000 * 60 * 60 * 24));
        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);

        days = days > 0 ? days + "j " : " ";
        hours = hours > 0 ? hours + "h " : " ";
        minutes = minutes > 0 ? minutes + "m " : " ";
        seconds = seconds + "s ";

        // Display the result in the element with id="demo"
        // document.getElementById("tempsRestant").innerHTML = "Temps restant: " + days + "j " + hours + "h "
        // + minutes + "m " + seconds + "s ";
        document.getElementById("tempsRestant").innerHTML = "Temps restant: " + days + hours + minutes + seconds;

        // If the count down is finished, write some text
        if (distance < 0) {
            clearInterval(this.intervalle);
            document.getElementById("tempsRestant").innerHTML = "Temps restant: Enchère Terminée";
        }

    }
}