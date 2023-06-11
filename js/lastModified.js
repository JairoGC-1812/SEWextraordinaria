class LastModification {
    setLastModificationDate() {
        $("footer > p").after("<p> Última modificación: " + document.lastModified + "</p>");
    }
}

let lastMod = new LastModification();
window.addEventListener("load", lastMod.setLastModificationDate());