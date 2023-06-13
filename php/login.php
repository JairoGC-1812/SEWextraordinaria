<!DOCTYPE HTML>

<html lang="es">

<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <meta name="author" content="Jairo García Castro - UO271449" />
    <meta name="description" content="Sección de reserva de recursos Turísticos de Villaviciosa" />
    <meta name="keywords" content="Villaviciosa, turismo, restaurantes, rutas, recursos, eventos" />

    <title>Villaviciosa Turismo - Meteorología</title>
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/nav.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
</head>

<body>
    <header>
        <h1>Villaviciosa Turismo</h1>
        <nav>
            <a href="index.html" title="Página de inicio" tabindex="1" accesskey="I">Inicio</a>
            <a href="gastronomia.html" title="Gastronomía" tabindex="2" accesskey="G">Gastronomía</a>
            <a href="rutas.html" title="Rutas turísticas" tabindex="3" accesskey="R"> Rutas</a>
            <a href="meteorologia.html" title="Información meteorológica" tabindex="4" accesskey="M">Meteorología</a>
            <a href="juego.html" title="Juego" tabindex="5" accesskey="J">Juego</a>
            <a href="php/reservas.php" class="active" title="Reserva de rutas" tabindex="6" accesskey="V">Reservas</a>
        </nav>
    </header>
    <main>
        <?php
        require_once('database.php');
        class Login
        {
            private $db;
            private $redirect;
            public function __construct(Database $db)
            {
                $this->db = $db;
                $this->redirect;
                if ($_SERVER['REQUEST_METHOD'] == "GET") {
                    if (!isset($_SESSION['user_id'])) { //Si el usuario no está logeado, imprimimos el formulario de login
                        $this->showLogin();
                    }else{                              // Si lo está, le informamos y le ofrecemos volver a reservas
                        $this->loginSuccess();
                    }
                } else if($_SERVER['REQUEST_METHOD'] == "POST"){
                    $this->login();
                }
            }

            public function showLogin()
            {
                echo "
                <h2> Inicio de sesión </h2>
                <form method='post' action='#'>
                    <label for='user'> Usuario: </label>
                    <input type='text' name='user' id='user'/>
                    <label for='passwd'> Contraseña:</label>
                    <input type='password' name='passwd' id='passwd'/>
                    <input type='submit' value='Iniciar sesión'/>
                </form>
            ";
            }

            public function loginSuccess(){
                echo "<h2> ¡Inicio de sesión exitoso! Ya puede volver a <a href='reservas.php'>la seccion de reservas</a> </h2>";
            }
            public function loginFail(){
                echo "<p> El usuario o la contraseña son incorrectos, por favor vuelva a intentarlo.</p>";
                $this->showLogin();
            }

            public function login()
            {
                $userId = $this->db->login($_POST['user'], $_POST['passwd']);

                if (isset($userId)) {
                    $_SESSION['user_id'] = $userId;
                    $this->loginSuccess();
                } else{
                    $this->loginFail();
                }
            }
        }

        require_once("database.php");
        $db = new Database();
        session_start();
        $login = new Login($db);
        ?>
    </main>

    <footer>
        <p>Jairo García Castro - Software y Estándares para la Web 2022</p>
    </footer>
</body>

</html>