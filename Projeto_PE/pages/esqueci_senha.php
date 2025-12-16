<?php
session_start();
require_once '../config/conexao.php';
require_once '../actions/enviar_email.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if ($email) {
        $sql = "SELECT 'associado' as tipo, email_associado as email FROM ASSOCIADO WHERE email_associado = :email
                UNION
                SELECT 'comercio' as tipo, email_comercio as email FROM COMERCIO WHERE email_comercio = :email";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            $token = bin2hex(random_bytes(32));
            $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour')); 
            $pdo->prepare("DELETE FROM RECUPERACAO_SENHA WHERE email_usuario = ?")->execute([$email]);
            
            $sqlInsert = "INSERT INTO RECUPERACAO_SENHA (email_usuario, token, data_expiracao) VALUES (?, ?, ?)";
            $pdo->prepare($sqlInsert)->execute([$email, $token, $expiracao]);

            $resultadoEnvio = enviarEmailRecuperacao($email, $token);

            if ($resultadoEnvio === true) {
                $mensagem = "<div class='alert alert-success'>
                                <strong>Sucesso!</strong> Enviamos um link de recuperação para <b>$email</b>.<br>
                                Verifique sua caixa de entrada (e também o SPAM).
                             </div>";
            } else {
                $mensagem = "<div class='alert alert-danger'>
                                Erro técnico ao enviar e-mail: $resultadoEnvio
                             </div>";
            }

        } else {
            $mensagem = "<div class='alert alert-warning'>Este e-mail não está cadastrado no sistema.</div>";
        }
    } else {
        $mensagem = "<div class='alert alert-danger'>Por favor, digite um e-mail válido.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Sistema de Cupons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
    <div class="card shadow card-recovery bg-white">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary">Recuperação</h3>
            <p class="text-muted small">Esqueceu sua senha? Não se preocupe.</p>
        </div>
        
        <?php echo $mensagem; ?>

        <form method="POST" action="esqueci_senha.php">
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Digite seu E-mail</label>
                <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="exemplo@email.com" required>
                <div class="form-text">Enviaremos um link seguro para você redefinir sua senha.</div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">Enviar Link</button>
            </div>
        </form>
        
        <hr class="my-4">
        
        <div class="text-center">
            <a href="login.php" class="text-decoration-none fw-bold">Voltar para o Login</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>