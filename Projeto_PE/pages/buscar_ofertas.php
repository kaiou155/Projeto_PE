<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'associado') {
    header("Location: login.php");
    exit;
}

$id_categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

$stmtCat = $pdo->query("SELECT * FROM CATEGORIA ORDER BY nom_categoria");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT c.*, com.nom_fantasia_comercio, cat.nom_categoria 
        FROM CUPOM c
        JOIN COMERCIO com ON c.cnpj_comercio = com.cnpj_comercio
        JOIN CATEGORIA cat ON com.id_categoria = cat.id_categoria
        WHERE c.num_cupom NOT IN (SELECT num_cupom FROM CUPOM_ASSOCIADO)
        AND c.dta_termino_cupom >= CURDATE()";

if ($id_categoria) {
    $sql .= " AND cat.id_categoria = :cat";
}

$sql .= " ORDER BY c.dta_inicio_cupom DESC";

$stmt = $pdo->prepare($sql);
if ($id_categoria) {
    $stmt->execute([':cat' => $id_categoria]);
} else {
    $stmt->execute();
}
$cupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Ofertas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-success mb-4">
        <div class="container">
            <span class="navbar-brand">Ofertas Disponíveis</span>
            <a href="painel_associado.php" class="btn btn-light btn-sm text-success">Voltar ao Painel</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold">O que você procura?</label>
                    </div>
                    <div class="col-md-4">
                        <select name="categoria" class="form-select">
                            <option value="">Todas as Categorias</option>
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?php echo $cat['id_categoria']; ?>" <?php echo $id_categoria == $cat['id_categoria'] ? 'selected' : ''; ?>>
                                    <?php echo $cat['nom_categoria']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-success">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <?php if (count($cupons) > 0): ?>
                <?php foreach ($cupons as $cupom): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow border-success">
                            <div class="card-header bg-transparent border-success fw-bold text-success">
                                <?php echo htmlspecialchars($cupom['nom_categoria']); ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($cupom['tit_cupom']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($cupom['nom_fantasia_comercio']); ?></h6>
                                <p class="card-text display-4 text-center text-success fw-bold">
                                    -<?php echo number_format($cupom['per_desc_cupom'], 0); ?>%
                                </p>
                                <p class="text-center small text-muted">
                                    Válido até: <?php echo date('d/m/Y', strtotime($cupom['dta_termino_cupom'])); ?>
                                </p>
                            </div>
                            <div class="card-footer bg-transparent border-success text-center">
                                <a href="../actions/reservar_cupom.php?codigo=<?php echo $cupom['num_cupom']; ?>" class="btn btn-success w-100" onclick="return confirm('Confirmar reserva deste cupom?')">RESERVAR AGORA</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">Nenhuma oferta encontrada no momento para esta categoria.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>