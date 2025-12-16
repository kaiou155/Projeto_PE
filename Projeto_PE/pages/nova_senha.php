<?php
session_start();
require_once '../config/conexao.php';

$mensagem = "";
$token_valido = false;
$email_recuperacao = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $sql = "SELECT email_usuario FROM RECUPERACAO_SENHA WHERE token = :token AND data_expiracao > NOW()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':token' => $token]);
    
    if ($stmt->rowCount() > 0) {
        $token_valido = true;
        $email_recuperacao = $stmt->fetchColumn();
    } else {
        $mensagem = "<div class='alert alert-danger'>Este link é inválido ou já expirou. Solicite uma nova recuperação.</div>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nova_senha'])) {
    $token_form = $_POST['token_form'];
    $email_form = $_POST['email_form'];
    
    $nova_senha = $_POST['nova_senha'];
    $confirma = $_POST['confirma_senha'];

    if ($nova_senha === $confirma) {
        $senhaHash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $up1 = $pdo->prepare("UPDATE ASSOCIADO SET sen_associado = ? WHERE email_associado = ?");
        $up1->execute([$senhaHash, $email_form]);
        
        $up2 = $pdo->prepare("UPDATE COMERCIO SET sen_comercio = ? WHERE email_comercio = ?");
        $up2->execute([$senhaHash, $email_form]);

        $pdo->prepare("DELETE FROM RECUPERACAO_SENHA WHERE email_usuario = ?")->execute([$email_form]);

        $mensagem = "<div class='alert alert-success'>Senha alterada com sucesso! <a href='login.php'>Clique aqui para entrar</a>.</div>";
        $token_valido = false;
    } else {
        $mensagem = "<div class='alert alert-danger'>As senhas não coincidem. Tente novamente.</div>";
        $token_valido = true; 
        $email_recuperacao = $email_form; 
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Nova Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
        <h4 class="text-center mb-3">Nova Senha</h4>
        
        <?php echo $mensagem; ?>

        <?php if ($token_valido): ?>
            <form method="POST" action="nova_senha.php">
                <input type="hidden" name="token_form" value="<?php echo htmlspecialchars($_GET['token'] ?? $_POST['token_form']); ?>">
                <input type="hidden" name="email_form" value="<?php echo $email_recuperacao; ?>">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Digite a Nova Senha</label>
                    <input type="password" name="nova_senha" class="form-control" required minlength="4">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Confirme a Nova Senha</label>
                    <input type="password" name="confirma_senha" class="form-control" required minlength="4">
                </div>
                
                <button type="submit" class="btn btn-success w-100">Salvar Nova Senha</button>
            </form>
        <?php endif; ?>
        
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Voltar ao Login</a>
        </div>
    </div>

</body>
</html>