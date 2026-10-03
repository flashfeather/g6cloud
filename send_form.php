<?php
declare(strict_types=1);

// Sempre responda em JSON
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function loadEnvironmentFile(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = array_map('trim', explode('=', $line, 2));
        if (!preg_match('/^[A-Z_][A-Z0-9_]*$/', $name) || getenv($name) !== false) {
            continue;
        }

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

// Em producao, prefira o arquivo fora da pasta publica. O caminho local e
// mantido como alternativa para desenvolvimento e hospedagens sem essa opcao.
loadEnvironmentFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');
loadEnvironmentFile(__DIR__ . DIRECTORY_SEPARATOR . '.env');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Metodo nao permitido.'
    ]);
    exit;
}

function fail(string $message, int $statusCode = 400): void
{
    http_response_code($statusCode);
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    exit;
}

function field(string $key, int $maxLength): string
{
    $value = trim((string)($_POST[$key] ?? ''));
    $length = function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    if ($length > $maxLength) {
        fail('Dados invalidos.');
    }
    return $value;
}

function clientIp(): string
{
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    return preg_replace('/[^0-9a-fA-F:\.]/', '', (string)$ip) ?: 'unknown';
}

function enforceRateLimit(string $ip): void
{
    $windowSeconds = 600;
    $maxAttempts = 5;
    $now = time();
    $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'g6cloud-form-rate';

    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }

    $file = $dir . DIRECTORY_SEPARATOR . hash('sha256', $ip) . '.json';
    $attempts = [];

    if (is_file($file)) {
        $decoded = json_decode((string)file_get_contents($file), true);
        if (is_array($decoded)) {
            $attempts = array_filter($decoded, static fn($time) => is_int($time) && $time > ($now - $windowSeconds));
        }
    }

    if (count($attempts) >= $maxAttempts) {
        fail('Muitas tentativas. Aguarde alguns minutos e tente novamente.', 429);
    }

    $attempts[] = $now;
    @file_put_contents($file, json_encode(array_values($attempts)), LOCK_EX);
}

enforceRateLimit(clientIp());

// Honeypot: usuarios reais nao preenchem este campo oculto.
if (field('website', 200) !== '') {
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem enviada com sucesso, em breve entraremos em contato.'
    ]);
    exit;
}

// Captura e valida campos
$name           = field('name', 120);
$email          = field('email', 180);
$whatsapp       = field('whatsapp', 40);
$phoneCountry   = preg_replace('/\D/', '', field('phone_country', 6));
if ($phoneCountry === '') {
    $phoneCountry = '55';
}
$whatsappDigits = preg_replace('/\D/', '', $whatsapp);
if (strpos($whatsappDigits, $phoneCountry) === 0 && strlen($whatsappDigits) > strlen($phoneCountry) + 5) {
    $whatsappDigits = substr($whatsappDigits, strlen($phoneCountry));
}
$company        = field('company', 160);
$provedor       = field('provedor', 60);
$dor_principal  = field('dor_principal', 80);
$canal_origem   = field('origem', 80);
$utm_source     = field('utm_source', 120);
$utm_campaign   = field('utm_campaign', 160);
$message        = field('message', 1200);
$calcProvider   = field('calc_provider', 60);
$calcMonthly    = field('calc_monthly_spend', 40);
$calcInstances  = field('calc_instances', 20);
$calcMaturity   = field('calc_maturity', 120);
$calcReservations = field('calc_reservations', 120);
$calcWaste      = field('calc_monthly_waste', 40);
$calcAnnual     = field('calc_annual_savings', 40);
$calcPayback    = field('calc_payback', 40);

if ($utm_source === '' && $canal_origem !== '') {
    $utm_source = $canal_origem;
}

if ($name === '' || $email === '' || $whatsapp === '' || $company === '') {
    fail('Por favor, preencha todos os campos obrigatorios.');
}

$whatsappLength = strlen($whatsappDigits);
$isInvalidBrazil = $phoneCountry === '55' && $whatsappLength !== 11;
$isInvalidNorthAmerica = $phoneCountry === '1' && $whatsappLength !== 10;
$isInvalidGeneric = !in_array($phoneCountry, ['55', '1'], true) && ($whatsappLength < 6 || $whatsappLength > 15);

if ($isInvalidBrazil || $isInvalidNorthAmerica || $isInvalidGeneric) {
    fail('Informe um WhatsApp valido para o pais selecionado.');
}

if ($phoneCountry === '55') {
    $whatsapp = sprintf(
        '+55 %s %s-%s',
        substr($whatsappDigits, 0, 2),
        substr($whatsappDigits, 2, 5),
        substr($whatsappDigits, 7, 4)
    );
} elseif ($phoneCountry === '1') {
    $whatsapp = sprintf(
        '+1 (%s) %s-%s',
        substr($whatsappDigits, 0, 3),
        substr($whatsappDigits, 3, 3),
        substr($whatsappDigits, 6, 4)
    );
} else {
    $whatsapp = '+' . $phoneCountry . ' ' . $whatsappDigits;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail('Email invalido.');
}

if (preg_match('/[\r\n]/', $name . $email . $company)) {
    fail('Dados invalidos.');
}

$webhookUrl = 'https://automacao.g6cloud.com.br/webhook/g6cloud-lead-diagnostico';

// Configure estas variaveis no servidor. Nao coloque segredos no repositorio.
$cfAccessClientId = getenv('CF_ACCESS_CLIENT_ID') ?: '';
$cfAccessClientSecret = getenv('CF_ACCESS_CLIENT_SECRET') ?: '';

if ($cfAccessClientId === '' || $cfAccessClientSecret === '') {
    error_log('Webhook lead error: missing Cloudflare Access credentials.');
    fail('Formulario temporariamente indisponivel. Por favor, tente novamente mais tarde.', 503);
}

$payload = [
    'source'         => 'site_form',
    'origem'         => 'landing_page',
    'name'           => $name,
    'email'          => $email,
    'phone_country'  => '+' . $phoneCountry,
    'whatsapp'       => $whatsapp,
    'company'        => $company,
    'provedor'       => $provedor,
    'dor_principal'  => $dor_principal,
    'canal_origem'   => $canal_origem,
    'utm_source'     => $utm_source,
    'utm_campaign'   => $utm_campaign,
    'message'        => $message,
    'calculator'     => [
        'provider'        => $calcProvider,
        'monthly_spend'   => $calcMonthly,
        'instances'       => $calcInstances,
        'maturity'        => $calcMaturity,
        'reservations'    => $calcReservations,
        'monthly_waste'   => $calcWaste,
        'annual_savings'  => $calcAnnual,
        'payback'         => $calcPayback
    ],
    'created_at'     => date(DATE_ATOM)
];

$jsonPayload = json_encode($payload);

if ($jsonPayload === false) {
    error_log('Webhook JSON encode error: ' . json_last_error_msg());
    fail('Erro ao enviar os dados. Por favor, tente novamente mais tarde.', 500);
}

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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

$responseData = $response === false ? null : json_decode($response, true);
$panelConfirmed = is_array($responseData) && ($responseData['success'] ?? false) === true;

if ($response !== false && in_array($httpCode, [200, 201], true) && $panelConfirmed) {
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem enviada com sucesso, em breve entraremos em contato.'
    ]);
    exit;
}

error_log(sprintf(
    'Webhook lead error: HTTP code=%s; response_length=%s; curl error=%s',
    $httpCode,
    $response === false ? 0 : strlen($response),
    $curlError
));

fail('Erro ao enviar os dados. Por favor, tente novamente mais tarde.', 502);
