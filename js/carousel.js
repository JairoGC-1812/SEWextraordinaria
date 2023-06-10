class Carousel {
    constructor(imgDict) {
        if (imgDict === undefined) {
            this.imgDict =
            {
                "abastos_1.jpg": "Interior del mercado de abastos de Villaviciosa",
                "molin_pena.jpg": "Molín La Peña, junto al Ríu Profundu",
                "teatro_riera.jpg": "Fachada del Teatro Riera de Villaviciosa",
                "villaviciosa_aerea.jpg": "Vista aérea de Villaviciosa",
                "exaltacion_manzana_1.jpg": "Escultura de la Exaltación a la Manzana, de Eduardo Úrculo",
                "molin_perniles1.jpg": "Molín de Perniles, junto al Ríu Profundu",
                "ateneo_1.jpg": "Fachada del Ateneo Obrero de Villaviciosa"
            }
        }
        else {
            this.imgDict = imgDict;
        }
        this.index = 0;
    }

    prev() {
        this.index--;
        if (this.index == -1)
            this.index = Object.keys(this.imgDict).length - 1;
        this.setImage();
    }

    next() {
        this.index++;
        if (this.index == Object.keys(this.imgDict).length)
            this.index = 0;
        this.setImage();
    }
    setImage() {
        var img = document.querySelector("button + img");
        img.src = "multimedia/" + Object.keys(this.imgDict)[this.index];
        img.alt = Object.values(this.imgDict)[this.index];
    }
}
var carousel = new Carousel();
