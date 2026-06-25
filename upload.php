<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

if (!isset($_FILES['photo'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nenhuma imagem foi enviada.']);
    exit;
}

$file = $_FILES['photo'];

if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Falha no envio da imagem.']);
    exit;
}

$maxSize = 10 * 1024 * 1024;
if (($file['size'] ?? 0) <= 0 || $file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'A imagem precisa ter até 10MB.']);
    exit;
}

$tmpPath = $file['tmp_name'] ?? '';
if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Upload inválido.']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = $finfo ? finfo_file($finfo, $tmpPath) : false;
if ($finfo) {
    finfo_close($finfo);
}

$allowedMimes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
];

if (!$mimeType || !isset($allowedMimes[$mimeType])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Formato de imagem não suportado.']);
    exit;
}

$uploadsDir = __DIR__ . '/uploads';
if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0755, true) && !is_dir($uploadsDir)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Não foi possível preparar a pasta de uploads.']);
    exit;
}

try {
    $basename = bin2hex(random_bytes(16));
} catch (Throwable) {
    $basename = uniqid('img_', true);
}

$filename = sprintf('%s.%s', $basename, $allowedMimes[$mimeType]);
$destination = $uploadsDir . '/' . $filename;

if (!move_uploaded_file($tmpPath, $destination)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Não foi possível salvar a imagem.']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Foto enviada com sucesso! 🎉',
    'file' => 'uploads/' . $filename,
]);
