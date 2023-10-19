class StaticMap{
    constructor(){
        this.latitud = "-26.543989";
        this.longitud = "31.532736";
        
    }

    getMapaEstaticoGoogle(){
        let apiKey = "&key=AIzaSyBXjk9mx9DWgzplUVXKroSbRdrewyo0uho";
        //URL: obligatoriamente https
        let url = "https://maps.googleapis.com/maps/api/staticmap?";
        //Parámetros
        // centro del mapa (obligatorio si no hay marcadores)
        let centro = "center=" + this.latitud + "," + this.longitud;
        //zoom (obligatorio si no hay marcadores)
        //zoom: 1 (el mundo), 5 (continentes), 10 (ciudad), 15 (calles), 20 (edificios)
        let zoom ="&zoom=8";
        //Tamaño del mapa en pixeles (obligatorio)
        let size= "&size=800x600";
        //Escala (opcional)
        //Formato (opcional): PNG,JPEG,GIF
        //Tipo de mapa (opcional)
        //Idioma (opcional)
        //region (opcional)
        //marcadores (opcional)
        let marcador = "&markers=color:red%7Clabel:%7C" + this.latitud + "," + this.longitud;
        //rutas. path (opcional)
        //visible (optional)
        //style (opcional)
        let sensor = "&sensor=false";
        this.imagenMapa = url + centro + zoom + size + marcador + sensor + apiKey;
        $("main>section:nth-of-type(2) > h2").after("<img src='"+ this.imagenMapa+ "' alt='mapa estático google' />");
    }
}
let map = new StaticMap();
window.addEventListener("load", map.getMapaEstaticoGoogle());