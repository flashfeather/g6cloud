<?php
// Sempre responda em JSON
header('Content-Type: application/json; charset=utf-8');

// Captura e sanitiza os campos
$name           = trim($_POST['name']    ?? '');
$email          = trim($_POST['email']   ?? '');
$whatsapp       = trim($_POST['whatsapp'] ?? '');
$company        = trim($_POST['company'] ?? '');
$provedor       = trim($_POST['provedor'] ?? '');
$dor_principal  = trim($_POST['dor_principal'] ?? '');
$origem         = trim($_POST['origem'] ?? '');
$utm_source     = trim($_POST['utm_source'] ?? '');
$utm_campaign   = trim($_POST['utm_campaign'] ?? '');
$message        = trim($_POST['message'] ?? '');

// Validação básica de campos obrigatórios
if ($name === '' || $email === '' || $whatsapp === '' || $company === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, preencha todos os campos obrigatórios.'
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

// Proteção simples contra cabeçalho malformado
if (preg_match('/[\r\n]/', $name) || preg_match('/[\r\n]/', $email)) {
    echo json_encode([
        'success' => false,
        'message' => 'Dados inválidos.'
    ]);
    exit;
}

// URL do webhook
$webhookUrl = 'https://automacao.g6cloud.com.br/webhook/g6cloud-lead-diagnostico';

// Preparar dados para o webhook
$payload = json_encode([
    'name'           => $name,
    'email'          => $email,
    'whatsapp'       => $whatsapp,
    'company'        => $company,
    'provedor'       => $provedor,
    'dor_principal'  => $dor_principal,
    'origem'         => $origem,
    'utm_source'     => $utm_source,
    'utm_campaign'   => $utm_campaign,
    'message'        => $message
]);

// Enviar para o webhook via cURL
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 
                                      'CF-Access-Client-Id: be7676b2dd4bc4618e55c8f15dccbc7f.access',
                                      'CF-Access-Client-Secret: 0c1d74a1462930be2e19e0da1e6a3fe4f8ac79c9503e34b59c55b2d9d99b1275']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Verificar se a requisição foi bem-sucedida
echo json_encode([
    'success' => false,
    'message' => 'Erro ao enviar os dados. Por favor, tente novamente mais tarde.',
    'debug' => [
        'http_code' => $httpCode,
        'response' => $response,
        'curl_error' => $curlError
    ]
]);