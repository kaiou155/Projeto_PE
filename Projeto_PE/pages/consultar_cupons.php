<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'comercio') {
    header("Location: login.php");
    exit;
}

$cnpj = $_SESSION['id_usuario'];
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'ativos'; 
$sql = "SELECT c.*, ca.dta_uso_cupom_associado, ca.cpf_associado 
        FROM CUPOM c 
        LEFT JOIN CUPOM_ASSOCIADO ca ON c.num_cupom = ca.num_cupom 
        WHERE c.cnpj_comercio = :cnpj ";

if ($filtro == 'ativos') {
    $sql .= "AND c.dta_termino_cupom >= CURDATE() AND ca.dta_uso_cupom_associado IS NULL";
} elseif ($filtro == 'utilizados') {
    $sql .= "AND ca.dta_uso_cupom_associado IS NOT NULL";
} elseif ($filtro == 'vencidos') {
    $sql .= "AND c.dta_termino_cupom < CURDATE() AND ca.dta_uso_cupom_associado IS NULL";
}

$sql .= " ORDER BY c.dta_inicio_cupom DESC, c.tit_cupom ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':cnpj' => $cnpj]);
$cupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand">Consultar Cupons</span>
            <a href="painel_comercio.php" class="btn btn-light btn-sm text-primary">Voltar</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Filtrar por:</label>
                    </div>
                    <div class="col-auto">
                        <select name="filtro" class="form-select" onchange="this.form.submit()">
                            <option value="ativos" <?php echo $filtro=='ativos'?'selected':''; ?>>Cupons Ativos</option>
                            <option value="utilizados" <?php echo $filtro=='utilizados'?'selected':''; ?>>Cupons Já Utilizados</option>
                            <option value="vencidos" <?php echo $filtro=='vencidos'?'selected':''; ?>>Vencidos e Não Usados</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <?php if (count($cupons) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>Promoção</th>
                                    <th>Código (Hash)</th>
                                    <th>Validade</th>
                                    <th>Desconto</th>
                                    <th>Status / Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cupons as $cupom): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($cupom['tit_cupom']); ?></td>
                                        <td class="font-monospace fw-bold"><?php echo $cupom['num_cupom']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($cupom['dta_termino_cupom'])); ?></td>
                                        <td><?php echo number_format($cupom['per_desc_cupom'], 0); ?>%</td>
                                        <td>
                                            <?php if ($filtro == 'utilizados'): ?>
                                                <span class="badge bg-secondary">Usado em <?php echo date('d/m/Y', strtotime($cupom['dta_uso_cupom_associado'])); ?></span>
                                                <small class="d-block text-muted">por CPF final ...<?php echo substr($cupom['cpf_associado'], -3); ?></small>
                                            
                                            <?php elseif ($filtro == 'vencidos'): ?>
                                                <span class="badge bg-danger">Expirado</span>
                                            
                                            <?php else: ?>
                                                <?php if($cupom['cpf_associado']): ?>
                                                    <span class="badge bg-warning text-dark">Reservado</span>
                                                    <a href="../actions/validar_cupom.php?codigo=<?php echo $cupom['num_cupom']; ?>" class="btn btn-sm btn-success ms-2">Registrar Uso</a>
                                                <?php else: ?>
                                                    <span class="badge bg-info text-dark">Disponível</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">Nenhum cupom encontrado para este filtro.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>