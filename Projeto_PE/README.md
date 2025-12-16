# Sistema de Gerenciamento de Cupons (Projeto Prática Extensionista V)
Sistema desenvolvido em PHP e MySQL para gestão de cupons de desconto entre comércios e associados.

## Funcionalidades
- Cadastro de Comércios e Associados.
- Geração de Cupons (Comércio).
- Reserva de Cupons (Associado).
- Validação de Uso (Comércio).
- Recuperação de Senha com envio de E-mail.

## Tecnologias
- PHP 8.0+
- MySQL (MariaDB)
- Bootstrap 5
- PHPMailer

## Como rodar
1. Importe o arquivo `db_cupons.sql` no seu banco de dados.
2. Configure o arquivo `config/conexao.php` com suas credenciais.
3. Configure o arquivo `actions/enviar_email.php` com seu SMTP.