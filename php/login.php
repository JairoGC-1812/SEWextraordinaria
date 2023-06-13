<?php
    class Login{
        private $db;
        private $redirect;
        public function __construct(Database $db){
            $this->db = $db;
            $this->redirect;
        }

        public function showLogin(){
            echo "
            <article>
                <h3> Inicio de sesión </h3>
                <form method='post' action='#'>
                    <label for='user'> Usuario: </label>
                    <input type='text' name='user' id='user'/>
                    <label for='passwd'> Contraseña:</label>
                    <input type='password' name='passwd' id='passwd'/>
                    <input type='submit' value='Iniciar sesión'/>
                </form>
            <article>
            ";
        }

        public function login(){
            $userId = $this->db->login($_POST['user'], $_POST['passwd']);
            if(isset($userId)){
                $_SESSION['user_id'] = $userId;
            }
        }
    }
    $login = new Login($db);

    if(!isset($_SESSION['user_id'])){ //Si el usuario no está logeado, imprimimos el formulario de login
        $login->showLogin();
    }
    if(isset($_POST['user']) && isset($_POST['passwd'])){
        $login->login();
    } 
?>