class XMLParser {
    constructor() {
        let url = "xml/rutas.xml";

        $.ajax({
            dataType: "xml",
            url: url,
            method: 'GET',
            success: (data) => this.parseHTML(data)
        });

    }

    parseHTML(xml) {
        this.xml = xml;
        let rutas = $("ruta", this.xml);

        for (let i = 0; i < rutas.length; i++) {
            let txt = "<article>";
            txt += "<h3>" + $(rutas[i]).attr("nombre") + "</h3>";
            txt += "<section><h4>Detalles</h4>";
            txt += "<dl>";
            txt += "<dt> Tipos </dt>";
            let tipos = $("tipos>tipo", rutas[i]);

            for (let t = 0; t < tipos.length; t++)
                txt += "<dd>" + $(tipos[t]).text(); + "</dd>";
            txt += "</dl>";
            txt += "<dl>"
            txt += "<dt>Características</dt>"
            txt += "<dd> Dificultad:" + $(rutas[i]).attr("dificultad") + "</dd>";
            txt += "<dd> Transporte:" + $(rutas[i]).attr("transporte") + "</dd>";
            txt += "<dd> Duración: " + $("tiempo", rutas[i]).attr("duracion") + "</dd>";
            txt += "<dd>" + $("personas-adecuadas", rutas[i]).text() + "</dd>";
            txt += "<dd> Recomendación: " + $("recomendacion", rutas[i]).text() + "</dd>";
            txt += "</dl>";
            txt += "<dl>";
            txt += "<dt>Referencias</dt>";
            let ref = $("referencias>referencia", rutas[i]);
            for (let r = 0; r < ref.length; r++) {
                txt += "<dd><a href=\"" + $(ref[r]).text() + "\">" +
                    $(ref[r]).text().split("/")[2] + "</a></dd>";
            }
            txt += "</dl>";
            txt += "</section>";

            txt += "<p>" + $("ruta>descripcion", rutas[i]).text() + "</p>";

            txt += "<section><h4>Hitos</h4>";
            let hitos = $("hitos>hito", rutas[i]);
            for (let h = 0; h < hitos.length; h++) {
                txt += "<section>";
                txt += "<h5>" + $(hitos[h]).attr("nombre") + "</h5>";
                txt += "<p>" + $("descripcion", hitos[h]).text() + "</p>";
                let images = $("imagenes>imagen", hitos[h]);
                for (let im = 0; im < images.length; im++) {
                    txt += "<img src=\"" + $(images[im]).text() + "\"";
                    txt += "alt=\"" + $(images[im]).text() + "\"/>";
                }

                let videos = $("videos>video", hitos[h]);
                for (let vi = 0; vi < videos.length; vi++) {
                    txt += "<video controls>";
                    txt += "<source src=\"" + $(videos[vi]).text() + "\"";
                    txt += "type=\"video/mp4\"/>";
                    txt += "</video>";
                }
                txt += "</section>";
            }
            txt += "</section>";

            txt += "<section>";
            txt += "<h4> Altimetría <h4>";
            txt += "<a href=\"" + $("altimetria", rutas[i]).text() + "\">Altimetría de " + $(rutas[i]).attr("nombre") + "</a>";
            txt += this.createSVG(rutas[i]);
            txt += "</section>";

            txt += "<section>";
            txt += "<h4> Planimetría </h4>";
            txt += "<aside title=\"ruta" + (i + 1) + "\">";
            txt += "</aside>";
            txt += "</section>";

            this.createKML(rutas[i]);

            txt += "</article>";
            $("main").append(txt);
        }

        this.initMap();
    }

    createSVG(ruta) {
        let totalDistance = 0;
        let maxHeight;
        let hitos = $("hitos>hito", ruta);
        let lines = [];

        let lastDistance = 0;

        let lastHeight = parseInt($("ruta>coordenadas", ruta).attr("altitud"));
        maxHeight = lastHeight;

        for (let i = 0; i < hitos.length; i++) {
            totalDistance += parseInt($(hitos[i]).attr("distancia-desde-anterior"));

            let height = parseInt($("hito>coordenadas", hitos[i]).attr("altitud"));
            maxHeight = height > maxHeight ? height : maxHeight;
            let line = "<line x1=\"" + lastDistance +
                "\" y1=\"" + lastHeight +
                "\" x2=\"" + totalDistance +
                "\" y2=\"" + height +
                "\" style=\"stroke:#f00; stroke-width: 3; stroke-linecap: round;\"/>";

            lastDistance = totalDistance;
            lastHeight = height;

            lines.push(line);
        }

        let svg = "<svg width=\"" + totalDistance +
            "\" height=\"" + (maxHeight + 10) +
            "\" viewbox=\"0 0 " + totalDistance + " " + maxHeight +
            "\" transform=\"matrix(1,0,0,-1,0,0)\">";

        for (let i = 0; i < lines.length; i++) {
            svg += lines[i];
        }

        svg += "</svg>";
        console.log("Nombre de la ruta: " + $(ruta).attr("nombre"));
        console.log(svg);
        return svg;
    }

    createKML(ruta) {
        let kml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        kml += "<kml xmlns=\"http://www.opengis.net/kml/2.2\">";
        kml += "<Document>";
        kml += "<Placemark>";
        kml += "<name>" + $(ruta).attr("nombre") + "</name>";
        kml += "<LineString>";
        kml += "<extrude>1</extrude>";
        kml += "<tesellate>1</tesellate>";
        kml += "<coordinates>";

        let coord = $("ruta>coordenadas", ruta);
        kml += $(coord).attr("longitud") + ",";
        kml += $(coord).attr("latitud") + ",";
        kml += $(coord).attr("altitud") + " ";

        let hitos = $("hitos>hito", ruta);

        for (let i = 0; i < hitos.length; i++) {
            coord = $("hito>coordenadas", hitos[i]);
            kml += $(coord).attr("longitud") + ",";
            kml += $(coord).attr("latitud") + ",";
            kml += $(coord).attr("altitud") + " ";

        }
        kml += "</coordinates>";
        kml += "<altitudeMode>clampToGround</altitudeMode>";
        kml += "</LineString>";
        kml += "<Style>";
        kml += "<LineStyle>";
        kml += "<color> #ff0000ff </color>";
        kml += "<width>5</width>";
        kml += "</LineStyle>";
        kml += "</Style>";
        kml += "</Placemark>";
        kml += "</Document>";
        kml += "</kml>";
        
        console.log("Nombre de la ruta: " + $(ruta).attr("nombre"));
        console.log(kml);
    }
    
    initMap() {
        var script = document.createElement("script");
        script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBXjk9mx9DWgzplUVXKroSbRdrewyo0uho&callback=initMap"
        window.initMap = function () {
            let maps = [];
            for (let i = 1; i < $("aside").length + 1; i++) {

                let src = "js/ruta" + i + ".kml";
                $.ajax({
                    dataType: "xml",
                    url: src,
                    method: 'GET',
                    success: function (data) {
                        let kml = $("Document>Placemark", data);
                        let coordArray = $("LineString>coordinates", kml).text().split(" ");
                        coordArray = coordArray.filter(x => x !== "");
                        let path = [];
                        for (let j = 0; j < coordArray.length; j++) {
                            let lat = parseFloat(coordArray[j].split(",")[1]);
                            let lng = parseFloat(coordArray[j].split(",")[0]);
                            path[j] = {lat: lat, lng: lng};
                        }                     
                        maps[i - 1] = new google.maps.Map($("aside[title=\"ruta" + i + "\"]").get(0), {
                            center: path[parseInt(path.length / 2)],
                            zoom: 9,
                            mapTypeId: 'hybrid'
                        });
                        let route = new google.maps.Polyline({
                            path: path,
                            geodesic: true,
                            strokeColor: $("Style>LineStyle>color", kml).text(),
                            strokeWeight: $("Style>LineStyle>width", kml).text()
                        });
                        route.setMap(maps[i-1]);
                    }

                });
            };
        }

        document.head.appendChild(script);
    }


}

let parser = new XMLParser();