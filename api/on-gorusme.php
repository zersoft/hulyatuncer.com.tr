<?php
/**
 * Hülya Tuncer – Aile ve İlişki Danışmanı
 * Ön Görüşme Talep API Endpoint
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Sadece POST isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Veriyi al (JSON veya Form-data)
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!is_array($input)) {
    $input = $_POST;
}

// Alanları filtrele ve temizle
$fullName = trim((string)($input['fullName'] ?? ''));
$phone = trim((string)($input['phone'] ?? ''));
$email = trim((string)($input['email'] ?? ''));
$topic = trim((string)($input['topic'] ?? 'Genel Danışmanlık'));
$participants = trim((string)($input['participants'] ?? 'Tek Kişi'));
$preferredTime = trim((string)($input['preferredTime'] ?? 'Fark Etmez / En Erken Zaman'));
$note = trim((string)($input['note'] ?? ''));
$source = trim((string)($input['source'] ?? 'website'));

// Zorunlu alan kontrolü
if (empty($fullName) || empty($phone)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Lütfen adınızı ve telefon numaranızı eksiksiz giriniz.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Güvenli referans ID oluştur
$refId = 'HT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 4));

$entry = [
    'refId' => $refId,
    'createdAt' => date('c'),
    'fullName' => $fullName,
    'phone' => $phone,
    'email' => $email,
    'topic' => $topic,
    'participants' => $participants,
    'preferredTime' => $preferredTime,
    'note' => $note,
    'source' => $source,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
];

// Dosyaya güvenle kaydet
$storageFile = __DIR__ . '/../data/talepler.json';
$allEntries = [];

if (file_exists($storageFile)) {
    $currentContent = @file_get_contents($storageFile);
    if (!empty($currentContent)) {
        $decoded = json_decode($currentContent, true);
        if (is_array($decoded)) {
            $allEntries = $decoded;
        }
    }
}

// Yeni talebi başa ekle
array_unshift($allEntries, $entry);

$fp = @fopen($storageFile, 'c+');
if ($fp) {
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        fwrite($fp, json_encode($allEntries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}

// Başarılı yanıt döndür
http_response_code(200);
echo json_encode([
    'success' => true,
    'refId' => $refId,
    'message' => 'Ön görüşme talebiniz başarıyla kaydedildi.',
    'data' => [
        'refId' => $refId,
        'fullName' => $fullName,
        'topic' => $topic,
        'preferredTime' => $preferredTime
    ]
], JSON_UNESCAPED_UNICODE);
