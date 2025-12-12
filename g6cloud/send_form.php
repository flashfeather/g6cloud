<?php
header('Content-Type: application/json; charset=utf-8');

// Configuração do email
$toEmail = 'g6cloud@g6cloud.com.br';
$toName  = 'G6 Cloud';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$subject || !$message) {
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
    exit;
}

// Validação básica de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email inválido.']);
    exit;
}

// Preparar o corpo do email
$body = "Nome: {$name}\nEmail: {$email}\n\nMensagem:\n{$message}";

// Headers para o email
$headers = "From: {$email}\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Enviar o email usando mail() nativa do PHP
if (mail($toEmail, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso em breve entraremos em contato.']);
} else {
    error_log('Mail error: Falha ao enviar email via mail()');
    echo json_encode(['success' => false, 'message' => 'Erro ao enviar email.']);
}