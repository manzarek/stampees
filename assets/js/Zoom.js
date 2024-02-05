
export default class Zoom {

    /**
     * Représente le constructeur de la classe
     */
    constructor(imgID, resultID){

        this.img = document.getElementById(imgID);
        this.result = document.getElementById(resultID);

        /* Create lens: */
        this.lens = document.createElement("DIV");
        this.lens.setAttribute("class", "img-zoom-lens");

        /* Insert lens: */
        this.img.parentElement.insertBefore(this.lens, this.img);

        this.calculerRatio();

        /* Set background properties for the result DIV */
        this.result.style.backgroundImage = "url('" + this.img.src + "')";
        //this.result.style.backgroundSize = (this.img.width * this.cx) + "px " + (this.img.height * this.cy) + "px";

        this.originalLensWidth = this.lens.getBoundingClientRect().width;
        this.originalLensHeight = this.lens.getBoundingClientRect().height;

        /* Execute a function when someone moves the cursor over the image, or the lens: */
        this.lens.addEventListener("mousemove", this.moveLens.bind(this));
        this.img.addEventListener("mousemove", this.moveLens.bind(this));
        this.result.addEventListener("mousemove", () => {console.log("result");});

        /* And also for touch screens: */
        // this.lens.addEventListener("touchmove", this.moveLens.bind(this));
        // this.img.addEventListener("touchmove", this.moveLens.bind(this));

        // Pour le zoom de la souris
        this.scale = 1;
        this.lens.addEventListener('wheel', this.zoom.bind(this));
    }


    calculerRatio() {
        /* Calculate the ratio between result DIV and lens: */
        this.cx = this.result.offsetWidth / this.lens.getBoundingClientRect().width;
        this.cy = this.result.offsetHeight / this.lens.getBoundingClientRect().height;

        this.result.style.backgroundSize = (this.img.width * this.cx) + "px " + (this.img.height * this.cy) + "px";
    }

    moveLens(e) {

        let pos, x, y;

        /* Prevent any other actions that may occur when moving over the image */
        e.preventDefault();

        /* Get the cursor's x and y positions: */
        pos = this.getCursorPos(e);
        this.pos = pos;

        /* Calculate the position of the lens: */
       // x = pos.x - (this.lens.offsetWidth / 2);
        //y = pos.y - (this.lens.offsetHeight / 2);
        const lensWidth = this.lens.getBoundingClientRect().width;
        const lensHeight = this.lens.getBoundingClientRect().height;
        x = pos.x - (lensWidth / 2);
        y = pos.y - (lensHeight / 2);

        /* Prevent the lens from being positioned outside the image: */
        //if (x > this.img.width - this.lens.offsetWidth) {x = this.img.width - this.lens.offsetWidth;}
        if (x > this.img.width - lensWidth) {x = this.img.width - lensWidth;}
        if (x < 0) {x = 0;}
        //if (y > this.img.height - this.lens.offsetHeight) {y = this.img.height - this.lens.offsetHeight;}
        if (y > this.img.height - lensHeight) {y = this.img.height - lensHeight;}
        if (y < 0) {y = 0;}

        /* Set the position of the lens: */
        this.lens.style.left = x + "px";
        this.lens.style.top = y + "px";

        /* Display what the lens "sees": */
        this.result.style.backgroundPosition = "-" + (x * this.cx) + "px -" + (y * this.cy) + "px";
    }
  
    getCursorPos(e) {

        let a, x = 0, y = 0;

        e = e || window.event;

        /* Get the x and y positions of the image: */
        a = this.img.getBoundingClientRect();

        /* Calculate the cursor's x and y coordinates, relative to the image: */
        x = e.pageX - a.left;
        y = e.pageY - a.top;

        /* Consider any page scrolling: */
        x = x - window.pageXOffset;
        y = y - window.pageYOffset;

        return {x : x, y : y};
    }

    zoom(event) {
        event.preventDefault();

        this.scale += event.deltaY * 0.001;

        // Restrict scale
        this.scale = Math.min(Math.max(0.5, this.scale), 3.5);

        console.log(this.scale);

        //this.lens.style.transform = `scale(${this.scale})`;
        console.log(this.originalLensWidth);
        console.log(this.originalLensHeight);
        this.lens.style.width = this.originalLensWidth * this.scale + "px";
        this.lens.style.height = this.originalLensHeight * this.scale + "px";
        this.calculerRatio();
        this.rescale();
    }

    rescale() {
        let pos, x, y;

        /* Get the cursor's x and y positions: */
        pos = this.pos;

        /* Calculate the position of the lens: */
       // x = pos.x - (this.lens.offsetWidth / 2);
        //y = pos.y - (this.lens.offsetHeight / 2);
        const lensWidth = this.lens.getBoundingClientRect().width;
        const lensHeight = this.lens.getBoundingClientRect().height;
        x = pos.x - (lensWidth / 2);
        y = pos.y - (lensHeight / 2);

        /* Prevent the lens from being positioned outside the image: */
        //if (x > this.img.width - this.lens.offsetWidth) {x = this.img.width - this.lens.offsetWidth;}
        if (x > this.img.width - lensWidth) {x = this.img.width - lensWidth;}
        if (x < 0) {x = 0;}
        //if (y > this.img.height - this.lens.offsetHeight) {y = this.img.height - this.lens.offsetHeight;}
        if (y > this.img.height - lensHeight) {y = this.img.height - lensHeight;}
        if (y < 0) {y = 0;}

        /* Set the position of the lens: */
        this.lens.style.left = x + "px";
        this.lens.style.top = y + "px";

        /* Display what the lens "sees": */
        this.result.style.backgroundPosition = "-" + (x * this.cx) + "px -" + (y * this.cy) + "px";
    }

}