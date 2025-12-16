<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'associado') {
    header("Location: login.php");
    exit;
}

$cpf = $_SESSION['id_usuario'];
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'ativos';

$sql = "SELECT c.*, ca.dta_cupom_associado, ca.dta_uso_cupom_associado, com.nom_fantasia_comercio 
        FROM CUPOM_ASSOCIADO ca
        JOIN CUPOM c ON ca.num_cupom = c.num_cupom
        JOIN COMERCIO com ON c.cnpj_comercio = com.cnpj_comercio
        WHERE ca.cpf_associado = :cpf ";

if ($filtro == 'ativos') {
    $sql .= "AND ca.dta_uso_cupom_associado IS NULL AND c.dta_termino_cupom >= CURDATE()";
} elseif ($filtro == 'utilizados') {
    $sql .= "AND ca.dta_uso_cupom_associado IS NOT NULL";
} elseif ($filtro == 'vencidos') {
    $sql .= "AND ca.dta_uso_cupom_associado IS NULL AND c.dta_termino_cupom < CURDATE()";
}

$sql .= " ORDER BY c.dta_inicio_cupom DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':cpf' => $cpf]);
$meus_cupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Cupons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-success mb-4">
        <div class="container">
            <span class="navbar-brand">Minha Carteira</span>
            <a href="painel_associado.php" class="btn btn-light btn-sm text-success">Voltar</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="btn-group mb-4 w-100">
            <a href="?filtro=ativos" class="btn btn-outline-success <?php echo $filtro=='ativos'?'active':''; ?>">Ativos (Para usar)</a>
            <a href="?filtro=utilizados" class="btn btn-outline-secondary <?php echo $filtro=='utilizados'?'active':''; ?>">Já Utilizados</a>
            <a href="?filtro=vencidos" class="btn btn-outline-danger <?php echo $filtro=='vencidos'?'active':''; ?>">Vencidos</a>
        </div>

        <?php if (count($meus_cupons) > 0): ?>
            <div class="list-group">
                <?php foreach ($meus_cupons as $cupom): ?>
                    <div class="list-group-item list-group-item-action flex-column align-items-start shadow-sm mb-3 border rounded">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 text-success"><?php echo htmlspecialchars($cupom['tit_cupom']); ?></h5>
                            <small class="text-muted">Válido até: <?php echo date('d/m/Y', strtotime($cupom['dta_termino_cupom'])); ?></small>
                        </div>
                        <p class="mb-1">Comércio: <strong><?php echo htmlspecialchars($cupom['nom_fantasia_comercio']); ?></strong></p>
                        <hr>
                        <div class="text-center">
                            <p class="mb-1">Apresente este código no balcão:</p>
                            <h2 class="font-monospace fw-bold text-dark bg-warning p-2 rounded d-inline-block">
                                <?php echo $cupom['num_cupom']; ?>
                            </h2>
                        </div>
                        <small class="text-muted mt-2 d-block">Reservado em: <?php echo date('d/m/Y', strtotime($cupom['dta_cupom_associado'])); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Você não tem cupons nesta categoria. <a href="buscar_ofertas.php">Buscar ofertas agora!</a></div>
        <?php endif; ?>

    </div>
</body>
</html>