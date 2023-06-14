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
        <h1>Villaviciosa Turismo</h1>
        <nav>
            <a href="../index.html" title="Página de inicio" tabindex="1" accesskey="I">Inicio</a>
            <a href="../gastronomia.html" title="Gastronomía" tabindex="2" accesskey="G">Gastronomía</a>
            <a href="../rutas.html" title="Rutas turísticas" tabindex="3" accesskey="R"> Rutas</a>
            <a href="../meteorologia.html" title="Información meteorológica" tabindex="4" accesskey="M">Meteorología</a>
            <a href="../juego.html" title="Juego" tabindex="5" accesskey="J">Juego</a>
            <a href="reservas.php" class="active" title="Reserva de rutas" tabindex="6" accesskey="V">Reservas</a>
            <?php
            $login->showLogout();
            ?>
        </nav>
    </header>
    <main>
        <?php
        class Reservas
        {
            private $db;
            public function __construct($db)
            {
                $this->db = $db;
                if (!isset($_SESSION['user_id'])) {
                    $this->showNoLoginMessage();
                } else {
                    if ($_SERVER['REQUEST_METHOD'] == "GET") {
                        if (isset($_GET['id_recurso'])) {
                            $this->showBookingView($_GET['id_recurso']);
                        } else {
                            $this->showResources();
                        }
                    }
                    if ($_SERVER['REQUEST_METHOD'] == "POST") {
                        $this->bookResource($_POST['id_recurso'], $_POST['date'], $_POST['hour'], $_POST['num_personas']);
                    }

                }
            }

            public function showNoLoginMessage()
            {
                echo "<h2> ¡Necesitas <a href='login.php'>iniciar sesión</a> para consultar esta página!</h2>";
            }

            public function showResources()
            {
                echo "<h2>Recursos turísticos</h2>";
                $resources = $this->db->findAll("Recurso");
                foreach ($resources as $r) {
                    echo "<article>";
                    echo "<h3>" . $r['nombre'] . "</h3>";
                    echo "<dl>";
                    echo "<dt>Aforo:</dt>";
                    echo "<dd> " . $r['max_ocupacion'] . "</dd>";
                    echo "<dt>Precio:</dt>";
                    echo "<dd>" . $r['precio'] . "€</dd>";
                    echo "</dl>";
                    echo "<p>" . $r['descripcion'] . "</p>";
                    echo "<form action='#' method='GET'>";
                    echo "<button name='id_recurso' type='submit' value='" . $r['id'] . "'>Reservar</button>";
                    echo "</form>";
                    echo "</article>";
                }
            }

            public function showBookingView($id)
            {
                $r = $this->db->findResourceById($id);
                $hours = $this->db->findAvailabilityByResourceId($id);

                $open = explode(":", $hours['hora_apertura']);
                $open = $open[0] . ":" . $open[1];

                $close = explode(":", $hours['hora_cierre']);
                $close = $close[0] . ":" . $close[1];

                echo "<h2> Reserva de " . $r['nombre'] . "</h2>";
                echo "<section>";
                echo "<h3>Descripción</h3>";

                echo "<p>" . $r['descripcion'] . "</p>";
                echo "<dl>";
                echo "<dt>Aforo:</dt>";
                echo "<dd> " . $r['max_ocupacion'] . "</dd>";
                echo "<dt>Precio:</dt>";
                echo "<dd>" . $r['precio'] . "€</dd>";
                echo "</dl>";

                echo "<p> Disponible de " . $open . " a " . $close . "</p>";
                echo "<form action='#' method='POST'>";

                echo "<label for='hour'> Seleccione la hora de reserva: </label>";
                echo "<input id='hour' name='hour' type='time' min='" . $open . "'max='" . $close . "'required/>";

                echo "<label for='date'> Seleccione el día de la reserva: </label>";
                echo "<input id='date' name='date' type='date' min='" . date("Y-m-d") . "'required/>";

                echo "<label for='num_personas'> Seleccione el número de personas: </label>";
                echo "<input id='num_personas' type='number' name='num_personas' value=0 min='1' max='" . $r['max_ocupacion'] . "'/>";
                echo "<button name='id_recurso' type='submit' value='" . $id . "'>Reservar</button>";
                echo "</form>";
                echo "</section>";

            }

            public function bookResource($id, $date, $time, $num_personas)
            {
                $r = $this->db->findResourceById($id);
                $hours = $this->db->findAvailabilityByResourceId($id);
                // Convertir al formato de mysql
                $time = $time . ":00";
                $date = $date . " " . $time;

                if (strtotime($date) < strtotime('now'))
                    return;
                if (strtotime($time) < strtotime($hours['hora_apertura']))
                    return;
                if (strtotime($time) > strtotime($hours['hora_cierre']))
                    return;

                $freeSpots = $r['max_ocupacion'] - $this->db->findOccupiedSpotsByResourceIdAndDate($id, $date);

                if ($num_personas > $freeSpots) {
                    $this->showBookingView($id);
                    echo "<p> Lo sentimos, " . $r['nombre'] . " tiene aforo completo para esa fecha</p>";
                    return;
                }

                $r = $this->db->book($_SESSION['user_id'], $id, $num_personas, $date);
                $this->showBookingSummary();

            }

            public function showBookingSummary()
            {
                $user = $this->db->findById("Usuario", $_SESSION['user_id']);
                $bookings = $this->db->findBookingsByUserId($_SESSION['user_id']);
                $totalPrice = 0;
                echo "<h2> Presupuesto de reservas de " . $user['nombre'] . " " . $user['apellidos'] . "</h2>";
                echo "
                <table>
                    <tr>
                        <th scope='col' id='recurso'>Recurso</th>
                        <th scope='col' id='numPersonas'>Número de personas</th>
                        <th scope='col' id='fecha'>Fecha y hora</th>
                        <th scope='col' id='precio'>Precio</th>
                    </tr>
                ";
                foreach ($bookings as $b) {
                    $r = $this->db->findResourceById($b['id_recurso']);
                    $price = $b['num_personas'] * $r['precio'];
                    $totalPrice += $price;
                    echo "
                    <tr>
                        <td headers='recurso'>" . $r['nombre'] . "</td>
                        <td headers='numPersonas'>" . $b['num_personas'] . "</td>
                        <td headers='fecha'>" . $b['fecha'] . "</td>
                        <td headers='precio'>" . $price . "</td>
                    </tr>
                    ";
                }
                echo "</table>";
                echo "
                <dl>
                    <dt>Precio total:</dt>
                    <dd> " . $totalPrice . "€</dd> 
                </dl>
                ";
            }
        }
        $db = new Database();
        $reservas = new Reservas($db);
        ?>
    </main>

    <footer>
        <p>Jairo García Castro - Software y Estándares para la Web 2022</p>
    </footer>
</body>

</html>