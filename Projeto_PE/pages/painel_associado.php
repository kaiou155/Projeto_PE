<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'associado') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Associado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-success">
        <div class="container">
            <span class="navbar-brand">Olá, <?php echo $_SESSION['nome_usuario']; ?> (Associado)</span>
            <a href="../actions/logout.php" class="btn btn-danger btn-sm">Sair</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Meus Cupons</h2>
        <p>Aqui você poderá ver seus cupons reservados.</p>
        <div class="row">
    <div class="col-md-6">
        <div class="card bg-success text-white mb-3">
            <div class="card-body">
                <h5 class="card-title">Buscar Novos Cupons</h5>
                <p class="card-text">Encontre ofertas no comércio local.</p>
                <a href="buscar_ofertas.php" class="btn btn-light text-success">Pesquisar Ofertas</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-warning text-dark mb-3">
            <div class="card-body">
                <h5 class="card-title">Meus Cupons</h5>
                <p class="card-text">Veja seus códigos reservados.</p>
                <a href="meus_cupons.php" class="btn btn-dark text-warning">Minha Carteira</a>
            </div>
        </div>
    </div>
        </div>
    </div>
</body>
</html>