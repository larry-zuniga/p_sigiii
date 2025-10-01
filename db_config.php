<?php
$host = "localhost";
$port = "5432";
$dbname = "p1_sigiii";
$user = "postgres";
$password = "itslarryyt";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión con la base de datos.");
}
?>

