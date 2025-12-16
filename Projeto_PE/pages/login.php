<?php
session_start();
require_once '../config/conexao.php';

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario']; 
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo']; 

    if ($tipo == 'associado') {
        $sql = "SELECT cpf_associado, nom_associado, sen_associado FROM ASSOCIADO WHERE cpf_associado = :usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':usuario' => $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['sen_associado'])) {
            $_SESSION['id_usuario'] = $user['cpf_associado'];
            $_SESSION['nome_usuario'] = $user['nom_associado'];
            $_SESSION['tipo_usuario'] = 'associado';
            header("Location: painel_associado.php"); 
            exit;
        } else {
            $erro = "CPF ou senha inválidos!";
        }

    } elseif ($tipo == 'comercio') {
        $sql = "SELECT cnpj_comercio, nom_fantasia_comercio, sen_comercio FROM COMERCIO WHERE cnpj_comercio = :usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':usuario' => $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['sen_comercio'])) {
            $_SESSION['id_usuario'] = $user['cnpj_comercio'];
            $_SESSION['nome_usuario'] = $user['nom_fantasia_comercio'];
            $_SESSION['tipo_usuario'] = 'comercio';
            header("Location: painel_comercio.php"); 
            exit;
        } else {
            $erro = "CNPJ ou senha inválidos!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Cupons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow p-4" style="width: 400px;">
        <h3 class="text-center mb-4">Acessar Sistema</h3>
        
        <?php if($erro): ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label class="form-label">Selecione seu perfil:</label>
                <div class="d-flex justify-content-around">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo" value="associado" id="tipoAssoc" checked>
                        <label class="form-check-label" for="tipoAssoc">Associado (CPF)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo" value="comercio" id="tipoCom">
                        <label class="form-check-label" for="tipoCom">Comércio (CNPJ)</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Usuário (CPF ou CNPJ)</label>
                <input type="text" name="usuario" class="form-control" placeholder="Somente números" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>

            <div class="text-end mt-1">
                <a href="esqueci_senha.php" class="text-decoration-none small text-primary">
                Esqueci minha senha
                </a>
            </div>

            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <div class="text-center mt-3">
            <a href="../index.html" class="text-decoration-none">Voltar ao Início</a>
        </div>
    </div>

</body>
</html>