<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'comercio') {
    header("Location: login.php");
    exit;
}

$mensagem = "";

function gerarHashCupom() {
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 12));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $desconto = $_POST['desconto']; 
    $quantidade = (int)$_POST['quantidade'];
    
    $cnpj_comercio = $_SESSION['id_usuario'];
    $data_emissao = date('Y-m-d'); 

    if (strtotime($data_inicio) > strtotime($data_fim)) {
        $mensagem = "<div class='alert alert-danger'>A data de início não pode ser maior que a data fim!</div>";
    } elseif ($quantidade <= 0) {
        $mensagem = "<div class='alert alert-danger'>A quantidade deve ser maior que zero.</div>";
    } else {
        
        try {
            $pdo->beginTransaction();

            $sql = "INSERT INTO CUPOM (num_cupom, tit_cupom, cnpj_comercio, dta_emissao_cupom, dta_inicio_cupom, dta_termino_cupom, per_desc_cupom) 
                    VALUES (:hash, :titulo, :cnpj, :emissao, :inicio, :fim, :desc)";
            
            $stmt = $pdo->prepare($sql);

            for ($i = 0; $i < $quantidade; $i++) {
                $hashUnico = gerarHashCupom();
                $stmt->execute([
                    ':hash' => $hashUnico,
                    ':titulo' => $titulo,
                    ':cnpj' => $cnpj_comercio,
                    ':emissao' => $data_emissao,
                    ':inicio' => $data_inicio,
                    ':fim' => $data_fim,
                    ':desc' => $desconto
                ]);
            }

            $pdo->commit(); 
            $mensagem = "<div class='alert alert-success'>Sucesso! $quantidade cupons gerados para a promoção '$titulo'.</div>";

        } catch (PDOException $e) {
            $pdo->rollBack(); 
            $mensagem = "<div class='alert alert-danger'>Erro ao gerar cupons: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Cupons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand">Nova Campanha</span>
            <a href="painel_comercio.php" class="btn btn-light btn-sm text-primary">Voltar ao Painel</a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Cadastrar Promoção</h4>
                        <?php echo $mensagem; ?>

                        <form method="POST" action="cadastrar_cupom.php">
                            <div class="mb-3">
                                <label class="form-label">Título da Promoção</label>
                                <input type="text" name="titulo" class="form-control" placeholder="Ex: Queima de Estoque de Verão" required maxlength="25">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Data Início</label>
                                    <input type="date" name="data_inicio" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Data Fim (Validade)</label>
                                    <input type="date" name="data_fim" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Percentual de Desconto (%)</label>
                                    <input type="number" step="0.01" name="desconto" class="form-control" placeholder="10.00" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Quantidade de Cupons</label>
                                    <input type="number" name="quantidade" class="form-control" placeholder="Ex: 50" required min="1">
                                    <div class="form-text">O sistema irá gerar códigos únicos para cada cupom.</div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Gerar Cupons</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>