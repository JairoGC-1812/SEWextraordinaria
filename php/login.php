<!DOCTYPE HTML>

<html lang="es">

<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <meta name="author" content="Jairo García Castro - UO271449" />
    <meta name="description" content="Pantalla de inicio de sesión" />
    <meta name="keywords" content="Esuatini, inicio, sesión, reservas" />

    <title>Turismo Esuatini - Login</title>
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/nav.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>

<body>
    <?php
    require_once("userManagement.php");

    $db = new Database();
    session_start();
    $login = new UserManagement($db);
    ?>
    <header>
        <h1>Turismo Esuatini</h1>
        <nav>
            <a href="../index.html" title="Página de inicio" tabindex="1" accesskey="I">Inicio</a>
            <a href="../gastronomia.html" title="Gastronomía" tabindex="2" accesskey="G">Gastronomía</a>
            <a href="../rutas.html" title="Rutas turísticas" tabindex="3" accesskey="R"> Rutas</a>
            <a href="../meteorologia.html" title="Información meteorológica" tabindex="4" accesskey="M">Meteorología</a>
            <a href="../juego.html" title="Juego" tabindex="5" accesskey="J">Juego</a>
            <a href="reservas.php" title="Reserva de rutas" tabindex="6" accesskey="V">Reservas</a>
            <?php
            $login->showLogout();
            ?>
        </nav>
    </header>
    <main>
        <?php
        $login->respond("login");
        ?>
    </main>

    <footer>
        <p>Jairo García Castro - Software y Estándares para la Web 2023</p>
    </footer>
</body>

</html>