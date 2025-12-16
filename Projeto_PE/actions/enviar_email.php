<?php
require_once '../config/PHPMailer/Exception.php';
require_once '../config/PHPMailer/PHPMailer.php';
require_once '../config/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function enviarEmailRecuperacao($emailDestino, $token) {
    $mail = new PHPMailer(true);

    try { 
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'cittakaio@gmail.com';
        $mail->Password   = 'djxw xtqh ltiu oqog';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('cittakaio@gmail.com', 'Sistema de Cupons');
        $mail->addAddress($emailDestino);

        $link = "http://localhost/Projeto_PE/pages/nova_senha.php?token=" . $token;

        $mail->isHTML(true);
        $mail->Subject = 'Recuperação de Senha';
        $mail->Body    = "
            <h2>Olá!</h2>
            <p>Recebemos um pedido para redefinir sua senha no Sistema de Cupons.</p>
            <p>Clique no link abaixo para criar uma nova senha:</p>
            <p><a href='$link' style='padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Redefinir Minha Senha</a></p>
            <p><small>Se não foi você, ignore este e-mail.</small></p>
        ";
        $mail->AltBody = "Acesse o link para recuperar: $link"; 

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Erro ao enviar: {$mail->ErrorInfo}";
    }
}
?>