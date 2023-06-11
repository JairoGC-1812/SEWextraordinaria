class MeteoForecast {
    constructor() {
        this.baseUrl = "https://api.open-meteo.com/v1/forecast";
        this.lat = "43.48";
        this.lon = "-5.44"
        this.hourlyData = "cloudcover";
        this.dailyData = "temperature_2m_max,temperature_2m_min,precipitation_probability_max"
        this.timezone = "Europe/Berlin"
    }

    load() {
        let url = this.baseUrl;
        url += "?latitude=" + this.lat;
        url += "&longitude=" + this.lon;
        url += "&hourly=" + this.hourlyData;
        url += "&daily=" + this.dailyData;
        url += "&timezone=" + this.timezone;

        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: function (data) {

                for (let i = 0; i < data.daily.time.length; i++) {

                    var content =
                        "<section><h3>" +
                        data.daily.time[i] +
                        "</h3><dl>" +
                        // La cobertura me la da por hora y yo quiero la media del día
                        "<dt>Cobertura de nubes:</dt>" +
                        "<dd>" +
                        (data.hourly.cloudcover.slice(i, i + 24).reduce((acc, x) => acc + x, 0) / 24).toFixed(2) + data.hourly_units.cloudcover +
                        "</dd>" +
                        "<dt>Temperatura mínima:</dt>" +
                        "<dd>" + data.daily.temperature_2m_min[i] + data.daily_units.temperature_2m_min + "</dd>" +
                        "<dt>Temperatura máxima:</dt>" +
                        "<dd>" + data.daily.temperature_2m_max[i] + data.daily_units.temperature_2m_max + "</dd>" +
                        "<dt>Probabilidad de precipitaciones:</dt>" +
                        "<dd>" + data.daily.precipitation_probability_max[i] + data.daily_units.precipitation_probability_max + "</dd>" +
                        "</dl></section>"
                    $("main>section:only-of-type").append(content);

                }
            }
        })
    }
}

let meteoForecast = new MeteoForecast();
window.addEventListener("load", meteoForecast.load());