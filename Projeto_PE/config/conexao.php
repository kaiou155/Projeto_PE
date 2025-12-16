<?php

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "db_cupons";

try{
    $pdo = new PDO("mysql:host=$host;dbname=$banco", $usuario, $senha);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // echo "Conexão realizada com sucesso!";

} catch(PDOException $e) {
    die("ERRO DE CONEXÃO: " . $e->getMessage());
}

?>