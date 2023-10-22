class Quiz {
    constructor(quiz) {
        if (quiz === undefined) {
            this.quiz = [
                {
                    question: "¿Cuál es uno de los principales cultivos de Esuatini?",
                    options: [
                        "Avellana", "Maíz", "Cáñamo", "Olivo", "Tomate"
                    ],
                    answer: 1
                },
                {
                    question: "¿Con cuál de estos países comparte frontera Esuatini?",
                    options: [
                        "Lesoto", "Botsuana", "Namibia", "Mozambique", "Zimbabue"
                    ],
                    answer: 3
                },
                {
                    question: "¿Qué potencia colonial europea gobernó en Esuatini?",
                    options: [
                        "Imperio Británico", "Alemania", "Francia", "España", "Portugal"
                    ],
                    answer: 0
                },
                {
                    question: "¿Qué mar u océano baña las costas de Esuatini?",
                    options: [
                        "Pacífico", "Atlántico", "Índico", "Mediterráneo", "Esuatini no tiene costa"
                    ],
                    answer: 4
                },
                {
                    question: "¿Cuál es la capital legislativa de Esuatini?",
                    options: [
                        "Mbabane", "Lobamba", "Manzini", "Matata", "Mgazini"
                    ],
                    answer: 1
                },
                {
                    question: "¿En qué año se independizó Esuatini?",
                    options: [
                        "2003", "1918", "1903", "1894", "1968"
                    ],
                    answer: 4
                },
                {
                    question: "¿Con qué otro país comparte esuatini el embalse de Pongolapoort?",
                    options: [
                        "Mozambique", "Lesoto", "Sudáfrica", "Botsuana", "Namibia"
                    ],
                    answer: 2
                },
                {
                    question: "¿Cuál es la religión mayoritaria en Esuatini?",
                    options: [
                        "Catolicismo", "Cristianismo protestante", "Budismo", "Islam sunita", "Judaísmo"
                    ],
                    answer: 1
                },
                {
                    question: "¿Cuál es el gentilicio en castellano de Esuatini?",
                    options: [
                        "Esuatinés", "Suatín", "Esua", "Suazi", "Esuatinero"
                    ],
                    answer: 3
                },
                {
                    question: "¿Qué sistema de gobierno tiene Esuatini?",
                    options: [
                        "Monarquía absoluta", "República federal", "Confederación de estados", "Estado federal", "República soviética"
                    ],
                    answer: 0
                }
            ];
        } else {
            this.quiz = quiz;
        }
        this.correct = 0;
        this.index = 0;
    }

    start() {
        this.parseQuestion();
    }

    parseQuestion() {
        let question = "<article>";
        question += "<h3>" + this.quiz[this.index].question + "</h3>";
        question += "<fieldset>";
        question += "<legend>Selecciona una única respuesta</legend>";
        for (let i = 0; i < this.quiz[this.index].options.length; i++) {
            question += "<p><input name=\"answer\" id=\"" + i + "\" type=\"radio\" value=\"";
            question += this.quiz[this.index].options[i];
            question += "\"/>";

            question += "<label for=\"" + i + "\">";
            question += this.quiz[this.index].options[i];
            question += "</label></p>";
        }
        question += "</fieldset>";

        question += "<input type=\"button\" value=\"Siguiente\" onclick=\"quiz.next();\"/>";
        question += "</article>";

        $("section>:not(h2)").remove();
        $("h2").after(question);
    }

    next() {
        if (!this.checkSelectedOption())
            return;
        if (this.index < this.quiz.length - 1) {
            this.index++;
            this.parseQuestion();
        }
        else{
            this.finish();
        }
        
    }

    checkSelectedOption() {
        let checked = $("input[type=radio]:checked");

        if (checked.length == 0) {
            if($("h3 + p:only-of-type").length == 0)
                $("h3").after("<p>¡Tienes que seleccionar una respuesta!</p>");
            return false;
        }

        if (checked.val() === this.quiz[this.index].options[this.quiz[this.index].answer]) {
            this.correct++;
        }
        return true;
    }

    finish() {
        let txt = "<article>";
        txt += "<h3>¡Test finalizado!</h3>";
        txt += "<p> Tu puntuación es: " + this.correct + "/" + this.quiz.length + "</p>";
        txt += "<p>" + this.endingMessage() + "</p>";
        txt += "<input type=\"button\" value=\"Volver\" onclick=\"quiz.reset();\"/>";
        txt += "</article>";

        
        $("section>:not(h2)").remove();
        $("h2").after(txt);
    }

    endingMessage() {
        let totalQuestions = this.quiz.length;

        if (this.correct / totalQuestions <= 0.3)
            return "¡¿Pero tú leíste algo?! Revisa y vuelve en un ratín anda...";

        if (this.correct / totalQuestions < 0.5)
            return "¡Andas cerca! Revisa un poco más y seguro que apruebas";

        if (this.correct / totalQuestions <= 0.7)
            return "Más o menos te manejas, pero todavía se puede mejorar";

        if (this.correct / totalQuestions <= 0.9)
            return "¡Así se hace! Poca gente conoce mejor que tu La Villa";

        if (this.correct / totalQuestions <= 1)
            return "¡Qué profesional! Por tus venas corre pura sangre maliaya";

    }
    reset() {
        let introductionTxt = "<p>";
        introductionTxt += "En este pequeño juego de de preguntas tipo test podrás verificar si tus conocimientos de la Villa son";
        introductionTxt += "tan adecuados como este maravilloso concejo merece. Si descubres que no es el caso, ¡no te preocupes!,";
        introductionTxt += "encontrarás las respuestas a todas las preguntas en este mismo sitio web. ¡Suerte!";
        introductionTxt += "</p>";
        introductionTxt += "<input type=\"button\" value=\"¡Comenzar!\" onclick=\"quiz.start();\"/>";
        introductionTxt += "</section>";


        $("section>:not(h2)").remove();
        $("section").append(introductionTxt);
        this.correct = 0;
        this.index = 0;
    }
}

let quiz = new Quiz();
