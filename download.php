<?php
require_once __DIR__ . '/includes/auth.php';
require_student();
$student = current_student();
if (!$student) {
    flash_set('warning', 'Please login to continue.');
    redirect('/bca/login.php');
}

$type = $_GET['type'] ?? '';
$id   = (int)($_GET['id'] ?? 0);

if (!in_array($type, ['material','paper'], true) || $id <= 0) {
    http_response_code(400); exit('Bad request');
}

if ($type === 'material') {
    $st = $pdo->prepare('SELECT file_name, title FROM materials WHERE id = ?');
    $folder = __DIR__ . '/uploads/materials/';
} else {
    $st = $pdo->prepare('SELECT file_name, title FROM papers WHERE id = ?');
    $folder = __DIR__ . '/uploads/papers/';
}
$st->execute([$id]);
$row = $st->fetch();
if (!$row) { http_response_code(404); exit('File not found'); }

$path = $folder . basename($row['file_name']);
if (!is_file($path) || !is_readable($path)) { http_response_code(404); exit('File missing on server'); }

$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$downloadName = preg_replace('/[^A-Za-z0-9._ -]/', '', $row['title']) ?: 'download';
$downloadName .= $extension !== '' ? '.' . $extension : '';
$mimeType = function_exists('mime_content_type') ? mime_content_type($path) : 'application/octet-stream';
if (!$mimeType) {
    $mimeType = 'application/octet-stream';
}

while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Description: File Transfer');
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . addcslashes($downloadName, "\"\\") . '"');
header('Content-Transfer-Encoding: binary');
header('X-Content-Type-Options: nosniff');
header('Expires: 0');
header('Cache-Control: private, must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
