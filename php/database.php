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
            PRIMARY KEY(id)
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
        $eventTable = "CREATE TABLE IF NOT EXISTS Evento(
            id INT NOT NULL AUTO_INCREMENT,
            id_recurso INT NOT NULL,
            fecha DATETIME NOT NULL,
            nombre VARCHAR(255) NOT NULL,
            descripcion LONGTEXT NOT NULL,
            PRIMARY KEY(id),
            CONSTRAINT recurso_fecha UNIQUE(id_recurso, fecha),
            FOREIGN KEY(id_recurso) REFERENCES Recurso(id)
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
            id_evento INT,
            id_recurso INT,
            num_personas INT NOT NULL,
            fecha DATETIME NOT NULL,
            PRIMARY KEY(id),
            FOREIGN KEY(id_usuario) REFERENCES Usuario(id),
            FOREIGN KEY(id_evento) REFERENCES Evento(id),
            FOREIGN KEY(id_recurso) REFERENCES Recurso(id),
            CHECK (
                (id_evento IS NOT NULL) OR (id_recurso IS NOT NULL)
                AND NOT (id_evento IS NOT NULL AND id_recurso IS NOT NULL)
                    )
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
        if (!($db->query($eventTable) === TRUE)) {
            echo "<h2>Error en la creación de la tabla Evento: " . $db->error . "</h2>";
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
        $this->populateEvents();
        $this->populateAvailability();
        $this->populateBookings();
        $this->populateRoutes();
        $this->populateRestaurants();
    }

    private function populateUsers()
    {
        $db = $this->openConnection();

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

    }
    private function populateResources()
    {
        $db = $this->openConnection();

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

    }

    private function populateEvents()
    {
        $db = $this->openConnection();

        $csv = fopen("eventos.csv", "r");

        while (($col = fgetcsv($csv, 10000, ",")) !== FALSE) {

            $id_recurso = $col[0];
            $fecha = $col[1];
            $nombre = $col[2];
            $descripcion = $col[3];
            
            $statement = $db->prepare("INSERT INTO Evento (id_recurso, fecha, nombre, descripcion) VALUES (?,?,?,?)");
            $statement->bind_param('isss', $id_recurso, $fecha, $nombre, $descripcion);
            $statement->execute();
            $statement->close();
        }

    }

    private function populateAvailability()
    {
        $db = $this->openConnection();

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

    }

    private function populateBookings()
    {
        $db = $this->openConnection();

        $csv = fopen("reservas.csv", "r");

        while (($col = fgetcsv($csv, 10000, ",")) !== FALSE) {

            $id_usuario = $col[0];
            $id_evento = ($col[1] == "NULL") ? null : $col[1];
            $id_recurso = ($col[2] == "NULL") ? null : $col[2];
            $num_personas = $col[3];
            $fecha = $col[4];
            
            $statement = $db->prepare("INSERT INTO Reserva (id_usuario, id_evento, id_recurso, num_personas, fecha) VALUES (?,?,?,?,?)");
            $statement->bind_param('iiiis', $id_usuario, $id_evento, $id_recurso, $num_personas, $fecha);
            $statement->execute();
            $statement->close();
        }

    }

    private function populateRoutes()
    {
        $db = $this->openConnection();

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

    }

    private function populateRestaurants(){
        $db = $this->openConnection();

        $csv = fopen("restaurantes.csv", "r");

        while (($col = fgetcsv($csv, 10000, ",")) !== FALSE) {

            $id = $col[0];
            $menu = $col[1];
            
            $statement = $db->prepare("INSERT INTO Restaurante (id, menu) VALUES (?,?)");
            $statement->bind_param('is', $id, $menu,);
            $statement->execute();
            $statement->close();
        }
    }
}
?>