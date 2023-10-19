class News {
    constructor(query) {
        this.q = query;
        this.lang = "es";
    }

    showNews() {
        let apiKey = "56cca78ee678da353dbdbd0c6182986f";
        let url = "https://gnews.io/api/v4/search?q=";
        url += this.q;
        url += "&lang=" + this.lang;
        url += "&max=10"
        url += "&apikey=" + apiKey;
        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: function (data) {
                let articles = data.articles;

                for (let i = 0; i < articles.length; i++) {
                    let art = "<section>";
                    art += "<h3><a href=\"";
                    art += articles[i]['url'];
                    art += "\">";
                    art += articles[i]['title'];
                    art += "</a></h3>";
                    art += "<p>";
                    art += articles[i]['description'];
                    art += "</p>";
                    art += "</section>";
                    $("main>section:nth-of-type(3)>h2").after(art);
                }
            }
        });
    }
}

let news = new News("eswatini");
news.showNews();