<?php
// Sempre responda em JSON
header('Content-Type: application/json; charset=utf-8');

// Configuração do email (destinatário)
$toEmail = 'g6cloud@g6cloud.com.br';
$toName  = 'G6 Cloud';

// Captura e sanitiza os campos
$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$phone   = trim($_POST['phone']   ?? '');
$company = trim($_POST['company'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validação básica de campos obrigatórios
if ($name === '' || $email === '' || $phone === '' || $company === '' || $subject === '' || $message === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, preencha todos os campos.'
    ]);
    exit;
}

// Validação de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email inválido.'
    ]);
    exit;
}

// (Opcional) proteção simples contra cabeçalho malformado
if (preg_match('/[\r\n]/', $name) || preg_match('/[\r\n]/', $email)) {
    echo json_encode([
        'success' => false,
        'message' => 'Dados inválidos.'
    ]);
    exit;
}

// Remetente do email (usar conta do domínio para evitar SPAM)
$fromEmail = 'g6cloud@g6cloud.com.br'; // ajuste para um email válido do seu domínio

// Corpo do email
$body = "Nome: {$name}\n";
$body .= "Email: {$email}\n";
$body .= "Telefone: {$phone}\n";
$body .= "Empresa: {$company}\n\n";
$body .= "Mensagem:\n{$message}\n";

// Headers para o email
$headers  = "From: {$toName} <{$fromEmail}>\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Enviar o email usando mail() nativa do PHP
if (@mail($toEmail, $subject, $body, $headers)) {
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem enviada com sucesso, em breve entraremos em contato.'
    ]);
} else {
    error_log('Mail error: Falha ao enviar email via mail()');
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao enviar email. Tente novamente mais tarde.'
    ]);
}