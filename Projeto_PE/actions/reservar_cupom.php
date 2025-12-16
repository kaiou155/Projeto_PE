<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'associado') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['codigo'])) {
    $num_cupom = $_GET['codigo'];
    $cpf_associado = $_SESSION['id_usuario'];
    $data_reserva = date('Y-m-d');

    $check = $pdo->prepare("SELECT num_cupom FROM CUPOM_ASSOCIADO WHERE num_cupom = ?");
    $check->execute([$num_cupom]);

    if ($check->rowCount() == 0) {
        try {
            $sql = "INSERT INTO CUPOM_ASSOCIADO (num_cupom, cpf_associado, dta_cupom_associado, dta_uso_cupom_associado) 
                    VALUES (:cupom, :cpf, :data_reserva, NULL)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cupom' => $num_cupom, 
                ':cpf' => $cpf_associado, 
                ':data_reserva' => $data_reserva
            ]);
            echo "<script>alert('Cupom reservado com sucesso!'); window.location='../pages/meus_cupons.php';</script>";

        } catch (PDOException $e) {
            echo "Erro ao reservar: " . $e->getMessage();
        }
    } else {
        echo "<script>alert('Ops! Este cupom acabou de ser reservado por outra pessoa.'); window.location='../pages/buscar_ofertas.php';</script>";
    }

} else {
    header("Location: ../pages/meus_cupons.php");
}
?>