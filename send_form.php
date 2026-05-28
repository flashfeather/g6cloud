<?php
// Sempre responda em JSON
header('Content-Type: application/json; charset=utf-8');

// Captura e sanitiza os campos
$name           = trim($_POST['name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$whatsapp       = trim($_POST['whatsapp'] ?? '');
$company        = trim($_POST['company'] ?? '');
$provedor       = trim($_POST['provedor'] ?? '');
$dor_principal  = trim($_POST['dor_principal'] ?? '');
$utm_source     = trim($_POST['utm_source'] ?? '');
$utm_campaign   = trim($_POST['utm_campaign'] ?? '');
$message        = trim($_POST['message'] ?? '');

// Validacao basica de campos obrigatorios
if ($name === '' || $email === '' || $whatsapp === '' || $company === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, preencha todos os campos obrigatorios.'
    ]);
    exit;
}

// Validacao de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email invalido.'
    ]);
    exit;
}

// Protecao simples contra cabecalho malformado
if (preg_match('/[\r\n]/', $name) || preg_match('/[\r\n]/', $email)) {
    echo json_encode([
        'success' => false,
        'message' => 'Dados invalidos.'
    ]);
    exit;
}

// URL do webhook
$webhookUrl = 'https://automacao.g6cloud.com.br/webhook/g6cloud-lead-diagnostico';

// Headers do Cloudflare Access. Configure preferencialmente por variaveis de ambiente.
$cfAccessClientId = getenv('CF_ACCESS_CLIENT_ID') ?: 'SEU_CLIENT_ID_REAL.access';
$cfAccessClientSecret = getenv('CF_ACCESS_CLIENT_SECRET') ?: 'SEU_CLIENT_SECRET_REAL';

// Preparar dados para o webhook
$payload = [
    'source'         => 'site_form',
    'origem'         => 'landing_page',
    'name'           => $name,
    'email'          => $email,
    'whatsapp'       => $whatsapp,
    'company'        => $company,
    'provedor'       => $provedor,
    'dor_principal'  => $dor_principal,
    'utm_source'     => $utm_source,
    'utm_campaign'   => $utm_campaign,
    'message'        => $message,
    'created_at'     => date(DATE_ATOM)
];

$jsonPayload = json_encode($payload);

if ($jsonPayload === false) {
    error_log('Webhook JSON encode error: ' . json_last_error_msg());
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao enviar os dados. Por favor, tente novamente mais tarde.'
    ]);
    exit;
}

// Enviar para o webhook via cURL
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'CF-Access-Client-Id: ' . $cfAccessClientId,
    'CF-Access-Client-Secret: ' . $cfAccessClientSecret
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Verificar se a requisicao foi bem-sucedida
if ($response !== false && in_array($httpCode, [200, 201], true)) {
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem enviada com sucesso, em breve entraremos em contato.'
    ]);
    exit;
}

error_log(sprintf(
    'Webhook lead error: HTTP code=%s; response=%s; curl error=%s',
    $httpCode,
    $response === false ? '' : $response,
    $curlError
));

echo json_encode([
    'success' => false,
    'message' => 'Erro ao enviar os dados. Por favor, tente novamente mais tarde.'
]);
