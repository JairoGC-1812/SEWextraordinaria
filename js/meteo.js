class Meteo {

    constructor() {
        this.apikey = "caed756cd5eeb819fe6f21c852f860d1";
        this.tipo = "&mode=json";
        this.unidades = "&units=metric";
        this.idioma = "&lang=es";
        this.baseUrl = "https://api.openweathermap.org/data/2.5/weather?q=";
        this.ciudad = "villaviciosa";
    }

    loadJson() {

        let url = this.baseUrl;
        url += this.ciudad + this.tipo + this.unidades +
            this.idioma + "&APPID=" + this.apikey;

        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: function (datos) {
                var content = "<section><h3>Meteorología</h3><img src=\"http://openweathermap.org/img/w/" +
                    datos.weather[0].icon + ".png\" alt=\"Icono del tiempo\">" +
                    "<ul>" +
                    "<li>Tiempo: " + datos.weather[0].description + "</li>" +
                    "<li>Temperatura: " + datos.main.temp + "ºC</li>" +
                    "<li>Temperatura mínima: " + datos.main.temp_min + "ºC</li>" +
                    "<li>Temperatura máxima: " + datos.main.temp_max + "ºC</li>" +
                    "</ul></section>"
                $("main>section:nth-of-type(3)").append(content);
                console.log(datos.coord.lon);
                console.log(datos.coord.lat);
            },
            error: function () {
                $("main>section:nth-of-type(3)>section:last-of-type").after("<p>¡Tenemos problemas! No podemos obtener datos de meteorología de <a href='http://openweathermap.org'>OpenWeatherMap</a></p>");
            }
        });
    }
}

var meteo = new Meteo();
window.addEventListener('load', meteo.loadJson());