class Carousel {
    constructor(imgDict) {
        if (imgDict === undefined) {
            this.imgDict =
            {
                "aves_mlilwane.jpg": "Hipopótamo rugiendo en la reserva de Mlilwane con aves levantando el vuelo detrás.",
                "elefante_hlane.png": "Elefante paseando en el parque nacional de Hlane", 
                "jirafas_nisela.jpg": "Jirafas por la sabana de Nisela.",
                "lobamba.jpg": "Vista aérea de la ciudad de Lobamba",
                "rinoceronte_mkhaya.jpg": "Rinoceronte en la reserva de Mkhaya",
                "zebras_mlilwane.jpg": "Zebras en la reserva natural de Mlilwane",
                "casas_mlilwane.jpg": "Cabañas de adobe tradicionales suazi",
                "bulembu.jpg": "Vista aérea del pueblo minero de bulembu"
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
        let img = document.querySelector("button + img");
        img.src = "multimedia/" + Object.keys(this.imgDict)[this.index];
        img.alt = Object.values(this.imgDict)[this.index];
    }
}
let carousel = new Carousel();
