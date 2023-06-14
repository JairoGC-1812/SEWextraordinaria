<?php
class Database
{
    private $servername;
    private $username;
    private $password;
    private $databaseName;

    public function __construct()
    {
        $this->servername = "localhost";
        $this->username = "DBUSER2023";
        $this->password = "DBPSWD2023";
        $this->databaseName = "recursos";
        $this->createDB();
        $this->populate();
    }
    private function openConnection()
    {
        $db = new mysqli($this->servername, $this->username, $this->password, $this->databaseName);
        if ($db->connect_error) {
            exit("<h2> Error de conexión a la base de datos: " . $db->connect_error . "</h2>");
        }
        return $db;

    }
    private function createDB()
    {
        $db = new mysqli($this->servername, $this->username, $this->password);
        if ($db->connect_error) {
            exit("<h2> Error de conexión a la base de datos: " . $db->connect_error . "</h2>");
        }

        $create = "CREATE DATABASE IF NOT EXISTS " . $this->databaseName . " COLLATE utf8_spanish_ci";
        if (!($db->query($create) === TRUE)) {
            exit("<h2> Error de creación de la base de datos: " . $db->connect_error . "</h2>");
        }
        $this->createTables();
    }

    private function createTables()
    {
        $db = $this->openConnection();
        $userTable = "CREATE TABLE IF NOT EXISTS Usuario (
            id INT NOT NULL AUTO_INCREMENT,
            usuario VARCHAR(255) NOT NULL,
            nombre VARCHAR(255) NOT NULL,
            apellidos VARCHAR(255) NOT NULL,
            pswd VARCHAR(255) NOT NULL,
            PRIMARY KEY(id),
            UNIQUE(usuario)
            )";
        $resourceTable = "CREATE TABLE IF NOT EXISTS Recurso(
            id INT NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(255) NOT NULL,
            descripcion LONGTEXT NOT NULL,
            max_ocupacion INT NOT NULL,
            precio DECIMAL(50,2) NOT NULL,
            PRIMARY KEY(id),
            CHECK (max_ocupacion > 0),
            CHECK (precio >= 0)
            )";
        $availabilityTable = "CREATE TABLE IF NOT EXISTS DisponibilidadHoraria(
            id INT NOT NULL AUTO_INCREMENT,
            id_recurso INT UNIQUE REFERENCES Recurso(id),
            hora_apertura TIME NOT NULL,
            hora_cierre TIME NOT NULL,
            PRIMARY KEY(id)
            )";

        $bookTable = "CREATE TABLE IF NOT EXISTS Reserva(
            id INT AUTO_INCREMENT,
            id_usuario INT NOT NULL,
            id_recurso INT NOT NULL,
            num_personas INT NOT NULL,
            fecha DATETIME NOT NULL,
            PRIMARY KEY(id),
            FOREIGN KEY(id_usuario) REFERENCES Usuario(id),
            FOREIGN KEY(id_recurso) REFERENCES Recurso(id)
            )";
        $routeTable = "CREATE TABLE IF NOT EXISTS Ruta(
            id INT NOT NULL,
            transporte TINYTEXT,
            duracion INT,
            PRIMARY KEY(id),
            FOREIGN KEY(id) REFERENCES Recurso(id)
        )";
        $restaurantTable = "CREATE TABLE IF NOT EXISTS Restaurante(
            id INT,
            menu LONGTEXT,
            PRIMARY KEY(id),
            FOREIGN KEY(id) REFERENCES Recurso(id)
        )";

        if (!($db->query($userTable) === TRUE)) {
            echo "<h2>Error en la creación de la tabla Usuario: " . $db->error . "</h2>";
        }
        if (!($db->query($resourceTable) === TRUE)) {
            echo "<h2>Error en la creación de la tabla Recurso: " . $db->error . "</h2>";
        }
        if (!($db->query($availabilityTable) === TRUE)) {
            echo "<h2>Error en la creación de la tabla DisponibilidadHoraria: " . $db->error . "</h2>";
        }
        if (!($db->query($bookTable) === TRUE)) {
            echo "<h2>Error en la creación de la tabla Reserva: " . $db->error . "</h2>";
        }
        if (!($db->query($routeTable) === TRUE)) {
            echo "<h2>Error en la creación de la tabla Ruta: " . $db->error . "</h2>";
        }
        if (!($db->query($restaurantTable) === TRUE)) {
            echo "<h2>Error en la creación de la tabla Restaurante: " . $db->error . "</h2>";
        }

        $db->close();


    }

    private function populate()
    {
        $this->populateUsers();
        $this->populateResources();
        $this->populateAvailability();
        $this->populateBookings();
        $this->populateRoutes();
        $this->populateRestaurants();
    }

    private function populateUsers()
    {
        $db = $this->openConnection();

        $res = $db->query("Select COUNT(*) as total FROM Usuario");
        if ($res->fetch_assoc()['total'] > 0) {
            $db->close();
            return;
        }

        $csv = fopen("usuarios.csv", "r");

        while (($col = fgetcsv($csv, 10000, ",")) !== FALSE) {

            $usuario = $col[0];
            $nombre = $col[1];
            $apellidos = $col[2];
            $pswd = $col[3];

            $statement = $db->prepare("INSERT INTO Usuario (usuario, nombre, apellidos, pswd) VALUES (?,?,?,?)");
            $statement->bind_param('ssss', $usuario, $nombre, $apellidos, $pswd);
            $statement->execute();
            $statement->close();
        }
        $db->close();

    }
    private function populateResources()
    {
        $db = $this->openConnection();


        $res = $db->query("Select COUNT(*) as total FROM Recurso");
        if ($res->fetch_assoc()['total'] > 0) {
            $db->close();
            return;
        }

        $csv = fopen("recursos.csv", "r");

        while (($col = fgetcsv($csv, 10000, ",")) !== FALSE) {

            $nombre = $col[0];
            $descripcion = $col[1];
            $max_ocupacion = $col[2];
            $precio = $col[3];

            $statement = $db->prepare("INSERT INTO Recurso (nombre, descripcion, max_ocupacion, precio) VALUES (?,?,?,?)");
            $statement->bind_param('ssid', $nombre, $descripcion, $max_ocupacion, $precio);
            $statement->execute();
            $statement->close();
        }
        $db->close();

    }

    private function populateAvailability()
    {
        $db = $this->openConnection();


        $res = $db->query("Select COUNT(*) as total FROM DisponibilidadHoraria");
        if ($res->fetch_assoc()['total'] > 0) {
            $db->close();
            return;
        }

        $csv = fopen("disponibilidadHoraria.csv", "r");

        while (($col = fgetcsv($csv, 10000, ",")) !== FALSE) {

            $id_recurso = $col[0];
            $hora_apertura = $col[1];
            $hora_cierre = $col[2];

            $statement = $db->prepare("INSERT INTO DisponibilidadHoraria (id_recurso, hora_apertura, hora_cierre) VALUES (?,?,?)");
            $statement->bind_param('iss', $id_recurso, $hora_apertura, $hora_cierre);
            $statement->execute();
            $statement->close();
        }
        $db->close();

    }

    private function populateBookings()
    {
        $db = $this->openConnection();
        $res = $db->query("Select COUNT(*) as total FROM Reserva");
        if ($res->fetch_assoc()['total'] > 0) {
            $db->close();
            return;
        }

        $csv = fopen("reservas.csv", "r");

        while (($col = fgetcsv($csv, 10000, ",")) !== FALSE) {

            $id_usuario = $col[0];
            $id_recurso = $col[1];
            $num_personas = $col[2];
            $fecha = $col[3];

            $statement = $db->prepare("INSERT INTO Reserva (id_usuario, id_recurso, num_personas, fecha) VALUES (?,?,?,?)");
            $statement->bind_param('iiis', $id_usuario, $id_recurso, $num_personas, $fecha);
            $statement->execute();
            $statement->close();
        }
        $db->close();

    }

    private function populateRoutes()
    {
        $db = $this->openConnection();


        $res = $db->query("Select COUNT(*) as total FROM Ruta");
        if ($res->fetch_assoc()['total'] > 0) {
            $db->close();
            return;
        }

        $csv = fopen("rutas.csv", "r");

        while (($col = fgetcsv($csv, 10000, ",")) !== FALSE) {

            $id = $col[0];
            $transporte = $col[1];
            $duracion = $col[2];

            $statement = $db->prepare("INSERT INTO Ruta (id, transporte, duracion) VALUES (?,?,?)");
            $statement->bind_param('isi', $id, $transporte, $duracion);
            $statement->execute();
            $statement->close();
        }
        $db->close();

    }

    private function populateRestaurants()
    {

        $db = $this->openConnection();


        $res = $db->query("Select COUNT(*) as total FROM Restaurante");
        if ($res->fetch_assoc()['total'] > 0) {
            $db->close();
            return;
        }

        $csv = fopen("restaurantes.csv", "r");

        while (($col = fgetcsv($csv, 10000, ",")) !== FALSE) {

            $id = $col[0];
            $menu = $col[1];

            $statement = $db->prepare("INSERT INTO Restaurante (id, menu) VALUES (?,?)");
            $statement->bind_param('is', $id, $menu, );
            $statement->execute();
            $statement->close();
        }
        $db->close();
    }

    public function login($user, $passwd)
    {
        $db = $this->openConnection();

        $query = $db->prepare("SELECT * FROM Usuario WHERE usuario=? AND pswd=?");
        $query->bind_param('ss', $user, $passwd);
        $query->execute();

        $res = $query->get_result()->fetch_assoc();
        $id = ($res == null) ? null : $res['id'];

        $query->close();
        $db->close();

        return $id;
    }

    public function signUp($user, $name, $surname, $passwd)
    {
        $db = $this->openConnection();

        $query = $db->prepare("SELECT * FROM Usuario WHERE usuario=?");
        $query->bind_param('s', $user);
        $query->execute();

        $res = $query->get_result()->fetch_assoc();

        if (!isset($res)) { //No existe el nombre de usuario

            $insert = $db->prepare("INSERT INTO Usuario (usuario, nombre, apellidos, pswd) VALUES (?,?,?,?)");
            $insert->bind_param('ssss', $user, $name, $surname, $passwd);
            $insert->execute();
            $insert->close();
        }

        $query->close();
        $db->close();

        return $this->login($user, $passwd);
    }


    public function findAll($table)
    {
        $db = $this->openConnection();

        $query = $db->query("SELECT * FROM " . $table); // No se puede parametrizar el nombre de la tabla y no es entrada del usuario así que no es un riesgo

        $res = array();

        while ($row = $query->fetch_assoc()) {
            $res[] = $row; // Push row into res array
        }

        $query->close();
        $db->close();

        return $res;

    }

    public function findById($table, $id)
    {
        $db = $this->openConnection();

        $query = $db->prepare("SELECT * FROM " . $table . " where id = ?");
        $query->bind_param('i', $id);
        $query->execute();

        $res = array();

        $res = $query->get_result()->fetch_assoc();

        $query->close();
        $db->close();

        return $res;
    }

    public function findResourceById($id)
    {
        $db = $this->openConnection();

        $ruta = $this->findById("Ruta", $id);
        $restaurante = $this->findById("Restaurante", $id);

        if (isset($ruta)) {
            $query = "SELECT recurso.id, recurso.nombre, recurso.descripcion, recurso.max_ocupacion, recurso.precio, ruta.transporte, ruta.duracion
            FROM recurso, ruta
            where (recurso.id = ruta.id) AND recurso.id = ?";
        } else if (isset($restaurante)) {
            $query = "SELECT recurso.id, recurso.nombre, recurso.descripcion, recurso.max_ocupacion, recurso.precio, restaurante.menu
            FROM recurso, restaurante
            where (recurso.id = restaurante.id) AND recurso.id = ?";
        } else {
            $query = "SELECT * from recurso where id = ?";
        }

        $query = $db->prepare($query);
        $query->bind_param('s', $id);
        $query->execute();

        $res = $query->get_result()->fetch_assoc();

        $query->close();
        $db->close();

        return $res;

    }

    public function findAvailabilityByResourceId($id)
    {

        $db = $this->openConnection();

        $query = $db->prepare("SELECT * FROM DisponibilidadHoraria where id_recurso = ?");
        $query->bind_param('i', $id);
        $query->execute();

        $res = $query->get_result()->fetch_assoc();

        $query->close();
        $db->close();

        return $res;
    }
    public function findBookingsByResourceId($id)
    {

        $db = $this->openConnection();

        $query = $db->prepare("SELECT * FROM Reserva where id_recurso = ?");
        $query->bind_param('i', $id);
        $query->execute();

        $result = $query->get_result();
        $res = array();

        while ($row = $result->fetch_assoc()) {
            $res[] = $row; // Push row into res array
        }

        $query->close();
        $db->close();

        return $res;
    }

    public function findBookingsByUserId($id)
    {

        $db = $this->openConnection();

        $query = $db->prepare("SELECT * FROM Reserva where id_usuario = ?");
        $query->bind_param('i', $id);
        $query->execute();

        $result = $query->get_result();
        $res = array();

        while ($row = $result->fetch_assoc()) {
            $res[] = $row; // Push row into res array
        }

        $query->close();
        $db->close();

        return $res;
    }

    public function findOccupiedSpotsByResourceIdAndDate($id, $date)
    {

        $db = $this->openConnection();

        $query = $db->prepare("SELECT SUM(num_personas) as total FROM Reserva where id_recurso = ? and DATE(fecha)= DATE(?)");
        $query->bind_param('is', $id, $date);
        $query->execute();


        $res = array();

        $res = $query->get_result()->fetch_assoc();

        $query->close();
        $db->close();

        return $res['total'];
    }
    public function book($id_usuario, $id_recurso, $num_personas, $fecha)
    {
        $db = $this->openConnection();
        $insert = $db->prepare("INSERT INTO Reserva (id_usuario, id_recurso, num_personas, fecha) VALUES (?,?,?,?)");
        $insert->bind_param('iiis', $id_usuario, $id_recurso, $num_personas, $fecha);
        $insert->execute();

        $insert->close();
        $db->close();
    }

}
?>