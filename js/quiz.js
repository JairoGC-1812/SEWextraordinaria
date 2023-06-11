class Quiz {
    constructor(quiz) {
        if (quiz === undefined) {
            this.quiz = [
                {
                    question: "¿Cuál es el tipo de bebida más característico de Villaviciosa?",
                    options: [
                        "Alcoholes", "Infusiones", "Gaseosas", "Calientes", "Isotónicas"
                    ],
                    answer: 0
                },
                {
                    question: "¿En qué año desembarcó Carlos V en Tazones?",
                    options: [
                        "1879", "1563", "1486", "1517", "1497"
                    ],
                    answer: 3
                },
                {
                    question: "¿Cuál es el más famoso de los Molinos del Ríu Profundu?",
                    options: [
                        "Molín de Perniles", "Molín La Peña", "Molín El Pitu", "Molín de Villaverde", "Molín de Griselda"
                    ],
                    answer: 1
                },
                {
                    question: "Además de Villaviciosa, ¿qué actual concejo pertenecía al territorio histórico de Maliaio?",
                    options: [
                        "Colunga", "Sariego", "Cabranes", "Valdés", "Xixón"
                    ],
                    answer: 2
                },
                {
                    question: "Completa la canción: \"Ta sabrosona y cantarina, ta pistonuda y [...]\"",
                    options: [
                        "sabe perbién", "bébese bien", "muncho alcohol tien", "siéntame bien", "tú tamién"
                    ],
                    answer: 1
                },
                {
                    question: "Además de la manzana, ¿cuál es la fruta más característica de Villaviciosa?",
                    options: [
                        "Plátano", "Piña", "Melocotón", "Higo", "Arándano"
                    ],
                    answer: 4
                },
                {
                    question: "¿A qué concejo pertenece el Picu Fariu?",
                    options: [
                        "Xixón", "Villaviciosa", "Sariego", "Siero", "Cabranes"
                    ],
                    answer: 2
                },
                {
                    question: "¿Qué tipo de animal es un bugre?",
                    options: [
                        "Pez", "Crustáceo", "Mamífero", "Molusco", "Ave"
                    ],
                    answer: 1
                },
                {
                    question: "¿Qué postre es típico consumir en Pascua?",
                    options: [
                        "Tartaletas", "Pastel de tiñosu", "Carbayones", "Picatostes", "Empanada de cabello de ángel"
                    ],
                    answer: 3
                },
                {
                    question: "¿Quien esculpió la Exaltación de la Manzana?",
                    options: [
                        "Eduardo Úrculo", "José Cardín Fernández", "Pilar Fernández Carballedo", "Covadonga Romero Rodríguez", "Francisco Fresno"
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
