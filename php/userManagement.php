<?php
require_once('database.php');
class UserManagement
{
    private $db;
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function respond($pageRequested)
    {
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            if (isset($_GET['logout']) && $_GET['logout']) { //Si nos llega un logout == true desconectamos al usuario de la sesión
                unset($_SESSION['user_id']);
            }
            if (!isset($_SESSION['user_id'])) { //Si el usuario no está logeado, imprimimos el formulario de registro o de login según lo que se solicite
                if ($pageRequested == "login")
                    $this->showLogin();
                if ($pageRequested == "signup")
                    $this->showSignup();
            } else { // Si lo está, le informamos y le ofrecemos volver a reservas
                $this->loginSuccess();
            }
        } else if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (isset($_POST['user']) && isset($_POST['name']) && isset($_POST['surname']) && isset($_POST['passwd'])) {
                $this->signup(); //Si nos llegan los datos de registro, registramos e iniciamos sesión
            } else if (isset($_POST['user']) && isset($_POST['passwd'])) {
                $this->login(); //Si solo nos llegan los datos de inicio sesión, sólo hacemos login
            }
        }

    }

    public function showLogin()
    {
        echo "
                <h2> Inicio de sesión </h2>
                <form method='post' action='#'>
                    <p>
                        <label for='user'> Usuario: </label>
                        <input type='text' placeholder='Usuario' name='user' id='user' required/>
                    </p>
                    <p>
                        <label for='passwd'> Contraseña:</label>
                        <input type='password' placeholder='Contraseña' name='passwd' id='passwd' required/>
                    </p>
                    <p>
                        <input type='submit' value='Iniciar sesión'/>
                    </p>
                    <p>¿Aún no tienes cuenta? <a href='signup.php'>¡Regístrate!</a></p>
                </form>
            ";
    }

    public function showSignup()
    {
        echo "
        <h2> Registro de usuario </h2>
        <form method='post' action='#'>
            <p>
                <label for='user'> Usuario: </label>
                <input type='text' placeholder='Usuario' name='user' id='user'/>
            </p>
            <p>
                <label for='name'> Nombre: </label>
                <input type='text' placeholder='Nombre' name='name' id='name'/>
            </p>
            <p>
                <label for='surname'> Apellidos: </label>
                <input type='text' placeholder='Apellidos' name='surname' id='surname'/>
            </p>
            <p>
                <label for='passwd'> Contraseña:</label>
                <input type='password' placeholder='Contraseña' name='passwd' id='passwd'/>
            </p>
            <p>
                <input type='submit' value='Registrarse'/>
            </p>
            <p> ¿Ya tienes cuenta? <a href='login.php'>¡Inicia sesión!</a><p>
        </form>
    ";
    }

    public function showLogout()
    {
        if (isset($_SESSION['user_id'])) {
            echo '<a href="login.php?logout=true" title="Cerrar sesión" tabindex="6" accesskey="C">Cerrar sesión</a>';
        }
    }

    public function loginSuccess()
    {
        echo "<h2> ¡Inicio de sesión exitoso! Ya puede volver a <a href='reservas.php'>la sección de reservas</a> </h2>";
    }
    public function loginFail()
    {
        echo "<p> El usuario o la contraseña son incorrectos, por favor vuelva a intentarlo.</p>";
        $this->showLogin();
    }

    public function login()
    {
        $userId = $this->db->login($_POST['user'], $_POST['passwd']);

        if (isset($userId)) {
            $_SESSION['user_id'] = $userId;
            $this->loginSuccess();
        } else {
            $this->loginFail();
        }
    }

    public function signup()
    {
        $this->db->signup($_POST['user'], $_POST['name'], $_POST['surname'], $_POST['passwd']);
        $this->login();
    }

    public function logout()
    {
        session_destroy();
    }
}
?>