<?php
require_once '../config/conexao.php';

$mensagem = "";

$sqlCategorias = "SELECT * FROM CATEGORIA ORDER BY nom_categoria";
$stmtCat = $pdo->query($sqlCategorias);
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $razao = $_POST['razao'];
    $fantasia = $_POST['fantasia'];
    $cnpj = $_POST['cnpj'];
    $id_categoria = $_POST['categoria']; 
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirma = $_POST['confirma_senha'];
    
    $contato = $_POST['contato'];
    $endereco = $_POST['endereco'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $uf = $_POST['uf'];
    $cep = $_POST['cep'];

    if ($senha !== $confirma) {
        $mensagem = "<div class='alert alert-danger'>Senhas não conferem!</div>";
    } elseif (strlen($cnpj) < 14) {
        $mensagem = "<div class='alert alert-warning'>CNPJ inválido.</div>";
    } else {
        $check = $pdo->prepare("SELECT cnpj_comercio FROM COMERCIO WHERE cnpj_comercio = ? OR email_comercio = ?");
        $check->execute([$cnpj, $email]);
        
        if ($check->rowCount() > 0) {
             $mensagem = "<div class='alert alert-danger'>CNPJ ou E-mail já existe!</div>";
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            
            try {
                $sql = "INSERT INTO COMERCIO (cnpj_comercio, id_categoria, raz_social_comercio, nom_fantasia_comercio, end_comercio, bai_comercio, cep_comercio, cid_comercio, uf_comercio, con_comercio, email_comercio, sen_comercio) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$cnpj, $id_categoria, $razao, $fantasia, $endereco, $bairro, $cep, $cidade, $uf, $contato, $email, $senhaHash]);
                
                $mensagem = "<div class='alert alert-success'>Comércio cadastrado! <a href='login.php'>Faça Login</a></div>";
            } catch (PDOException $e) {
                $mensagem = "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Comércio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4>Cadastro de Comércio (Parceiro)</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $mensagem; ?>
                        
                        <form action="cadastro_comercio.php" method="POST">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label>Razão Social</label>
                                    <input type="text" name="razao" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Nome Fantasia</label>
                                    <input type="text" name="fantasia" class="form-control">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label>CNPJ (somente números)</label>
                                    <input type="text" name="cnpj" class="form-control" maxlength="18" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Categoria</label>
                                    <select name="categoria" class="form-select" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach($categorias as $cat): ?>
                                            <option value="<?php echo $cat['id_categoria']; ?>">
                                                <?php echo $cat['nom_categoria']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <h5 class="mt-4">Endereço e Contato</h5>
                            <div class="row mb-3">
                                <div class="col-md-8"><label>Endereço</label><input type="text" name="endereco" class="form-control"></div>
                                <div class="col-md-4"><label>Bairro</label><input type="text" name="bairro" class="form-control"></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><label>Cidade</label><input type="text" name="cidade" class="form-control"></div>
                                <div class="col-md-2"><label>UF</label><input type="text" name="uf" class="form-control" maxlength="2"></div>
                                <div class="col-md-3"><label>CEP</label><input type="text" name="cep" class="form-control"></div>
                                <div class="col-md-3"><label>Contato</label><input type="text" name="contato" class="form-control"></div>
                            </div>

                            <h5 class="mt-4">Acesso</h5>
                            <div class="mb-3">
                                <label>E-mail Corporativo</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6"><label>Senha</label><input type="password" name="senha" class="form-control" required></div>
                                <div class="col-md-6"><label>Confirmar Senha</label><input type="password" name="confirma_senha" class="form-control" required></div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Cadastrar Comércio</button>
                            <a href="../index.html" class="btn btn-link w-100 mt-2">Voltar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
