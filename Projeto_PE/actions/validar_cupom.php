<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'comercio') {
    die("Acesso negado.");
}

if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $dataHoje = date('Y-m-d');

    $sql = "UPDATE CUPOM_ASSOCIADO SET dta_uso_cupom_associado = :hoje 
            WHERE num_cupom = :codigo AND dta_uso_cupom_associado IS NULL";
    
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([':hoje' => $dataHoje, ':codigo' => $codigo])) {
        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Uso do cupom registrado com sucesso!'); window.location='../pages/consultar_cupons.php?filtro=utilizados';</script>";
        } else {
            echo "<script>alert('Erro: Este cupom não está reservado ou já foi utilizado.'); window.location='../pages/consultar_cupons.php';</script>";
        }
    } else {
        echo "Erro no banco de dados.";
    }
} else {
    header("Location: ../pages/consultar_cupons.php...");
}
?>