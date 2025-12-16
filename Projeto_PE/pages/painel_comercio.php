<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'comercio') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Comércio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand">Empresa: <?php echo $_SESSION['nome_usuario']; ?></span>
            <a href="../actions/logout.php" class="btn btn-danger btn-sm">Sair</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Gestão de Promoções</h2>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Criar Promoção</h5>
                        <p class="card-text">Cadastre novos cupons de desconto.</p>
                        <a href="cadastrar_cupom.php" class="btn btn-light text-primary">Criar Novo</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
    <div class="card text-white bg-success mb-3">
        <div class="card-body">
            <h5 class="card-title">Consultar & Validar</h5> <p class="card-text">Veja seus cupons e registre o uso.</p>
            <a href="consultar_cupons.php" class="btn btn-light text-success">Acessar</a>
        </div>
    </div>
            </div>
            </div>
        </div>
    </div>
</body>
</html>