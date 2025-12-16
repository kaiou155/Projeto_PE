<?php
require_once '../config/conexao.php';

$mensagem = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];
    
    $dtn = $_POST['dtn'];
    $celular = $_POST['celular'];
    $endereco = $_POST['endereco'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $uf = $_POST['uf'];
    $cep = $_POST['cep'];

    if ($senha !== $confirma_senha) {
        $mensagem = "<div class='alert alert-danger'>As senhas não coincidem!</div>";
    } elseif (strlen($cpf) < 11) {
        $mensagem = "<div class='alert alert-warning'>CPF inválido (verifique os dígitos).</div>";
    } else {
        $checkSQL = "SELECT cpf_associado FROM ASSOCIADO WHERE cpf_associado = :cpf OR email_associado = :email";
        $stmtCheck = $pdo->prepare($checkSQL);
        $stmtCheck->execute([':cpf' => $cpf, ':email' => $email]);

        if ($stmtCheck->rowCount() > 0) {
            $mensagem = "<div class='alert alert-danger'>CPF ou E-mail já cadastrados!</div>";
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            try {
                $sql = "INSERT INTO ASSOCIADO (cpf_associado, nom_associado, dtn_associado, end_associado, bai_associado, cep_associado, cid_associado, uf_associado, cel_associado, email_associado, sen_associado) 
                        VALUES (:cpf, :nome, :dtn, :end, :bai, :cep, :cid, :uf, :cel, :email, :senha)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':cpf' => $cpf,
                    ':nome' => $nome,
                    ':dtn' => $dtn,
                    ':end' => $endereco,
                    ':bai' => $bairro,
                    ':cep' => $cep,
                    ':cid' => $cidade,
                    ':uf' => $uf,
                    ':cel' => $celular,
                    ':email' => $email,
                    ':senha' => $senhaHash 
                ]);

                $mensagem = "<div class='alert alert-success'>Cadastro realizado com sucesso! <a href='login.php'>Faça Login aqui</a></div>";
            } catch (PDOException $e) {
                $mensagem = "<div class='alert alert-danger'>Erro ao cadastrar: " . $e->getMessage() . "</div>";
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
    <title>Cadastro de Associado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4>Cadastro de Associado (Morador)</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $mensagem; ?>
                        
                        <form action="cadastro_associado.php" method="POST">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label>Nome Completo</label>
                                    <input type="text" name="nome" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>CPF (somente números)</label>
                                    <input type="text" name="cpf" class="form-control" maxlength="14" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label>Data de Nascimento</label>
                                    <input type="date" name="dtn" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>Celular</label>
                                    <input type="text" name="celular" class="form-control">
                                </div>
                            </div>

                            <h5 class="mt-4">Endereço</h5>
                            <div class="row mb-3">
                                <div class="col-md-10">
                                    <label>Logradouro</label>
                                    <input type="text" name="endereco" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label>UF</label>
                                    <input type="text" name="uf" class="form-control" maxlength="2">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-5">
                                    <label>Cidade</label>
                                    <input type="text" name="cidade" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label>Bairro</label>
                                    <input type="text" name="bairro" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>CEP</label>
                                    <input type="text" name="cep" class="form-control">
                                </div>
                            </div>

                            <h5 class="mt-4">Acesso</h5>
                            <div class="mb-3">
                                <label>E-mail</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label>Senha</label>
                                    <input type="password" name="senha" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Confirmar Senha</label>
                                    <input type="password" name="confirma_senha" class="form-control" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Cadastrar</button>
                            <a href="../index.html" class="btn btn-link w-100 mt-2">Voltar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
