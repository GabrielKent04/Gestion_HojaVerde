<?php

$host = 'localhost';
$dbname = 'sistema_gestion';
$username = 'root';
$password = '';
$charset = 'utf8mb4';
$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";


$options = 
[
 PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
 PDO::ATTR_EMULATE_PREPARES => false,
];

try
{
    $pdo = new PDO($dsn,$username,$password,$options);
}
catch(PDOException $informe)
{
 die("Error crítico de conexto a la Base de Datos: " . $informe->getMessage());
}












?>