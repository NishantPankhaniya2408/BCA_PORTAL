<?php
// Database connection (PDO)
$DB_HOST = 'localhost';
$DB_NAME = 'bca_portal';
$DB_USER = 'root';
$DB_PASS = ''; // default for XAMPP

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    die('Database connection failed. Please check your database configuration.');
}

function ensure_subject_course_type_column(PDO $pdo): void {
    static $ensured = false;
    if ($ensured) {
        return;
    }

    $ensured = true;

    try {
        $columnCheck = $pdo->query("SHOW COLUMNS FROM subjects LIKE 'course_type'");
        if (!$columnCheck || !$columnCheck->fetch()) {
            $pdo->exec("ALTER TABLE subjects ADD COLUMN course_type VARCHAR(50) NOT NULL DEFAULT 'Theory' AFTER name");
        }
    } catch (PDOException $e) {
        error_log('Could not ensure subjects.course_type column: ' . $e->getMessage());
    }
}

ensure_subject_course_type_column($pdo);

if (session_status() === PHP_SESSION_NONE) {
    $sessionPath = __DIR__ . '/../tmp';
    if (!is_dir($sessionPath) && !mkdir($sessionPath, 0777, true) && !is_dir($sessionPath)) {
        error_log('Session directory could not be created: ' . $sessionPath);
        die('Session storage could not be initialized.');
    }
    if (!is_writable($sessionPath)) {
        error_log('Session directory is not writable: ' . $sessionPath);
        die('Session storage is not writable.');
    }
    if (!session_save_path($sessionPath)) {
        error_log('Failed to set session save path: ' . $sessionPath);
        die('Session storage could not be configured.');
    }
    if (!session_start()) {
        error_log('Failed to start session.');
        die('Session could not be started.');
    }
}


// Helpers
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function redirect($url) { header("Location: $url"); exit; }
function flash_set($t, $m) { $_SESSION['flash'] = ['type' => $t, 'msg' => $m]; }
function flash_get() {
    if (!empty($_SESSION['flash'])) { $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}
function logout_user($sessionKey, $redirectUrl, $message) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
    }

    // Rotate the session id so the old authenticated session cannot be reused.
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }

    flash_set('info', $message);
    redirect($redirectUrl);
}
function format_size($bytes) {
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}
