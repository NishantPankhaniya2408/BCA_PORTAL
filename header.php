<?php
require_once __DIR__ . '/auth.php';

$student = current_student();
if (!$student && !empty($_SESSION['student_id'])) {
    unset($_SESSION['student_id']);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}
$flash = flash_get();
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$selectedSemester = $student ? get_selected_student_semester($student) : null;
$semesterQuery = $selectedSemester ? '?semester=' . $selectedSemester : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?>BCA Department Portal</title>
<meta name="description" content="Access BCA study materials, class notes, and previous year question papers instantly.">
<script>
  (function() {
    let theme = 'light';
    try {
      const saved = localStorage.getItem('bca-theme');
      theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    } catch (e) {
      try {
        theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      } catch (ex) {}
    }
    document.documentElement.setAttribute('data-theme', theme);
    document.documentElement.setAttribute('data-bs-theme', theme);
  })();
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="/bca/assets/css/style.css">
</head>
<body class="site-shell">
<nav class="navbar navbar-expand-lg site-navbar">
  <div class="container">
    <a class="navbar-brand site-brand" href="/bca/index.php">
      <span class="brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
      <span>
        <strong>BCA Portal</strong>
        <small>Department learning hub</small>
      </span>
    </a>
    <button class="navbar-toggler site-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="/bca/index.php">Home</a></li>
        <?php if ($student): ?>
          <li class="nav-item"><a class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="/bca/dashboard.php<?= $semesterQuery ?>">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link <?= $currentPage === 'materials.php' ? 'active' : '' ?>" href="/bca/materials.php<?= $semesterQuery ?>">Study Material</a></li>
          <li class="nav-item"><a class="nav-link <?= $currentPage === 'papers.php' ? 'active' : '' ?>" href="/bca/papers.php<?= $semesterQuery ?>">Question Papers</a></li>
          <li class="nav-item"><span class="nav-link nav-user"><i class="bi bi-person-circle"></i> <?= e($student['name']) ?></span></li>
          <li class="nav-item"><a class="nav-link" href="/bca/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link login-action <?= $currentPage === 'login.php' ? 'active' : '' ?>" href="/bca/login.php"><i class="bi bi-mortarboard-fill me-1"></i>Student Login</a></li>
          <li class="nav-item"><a class="nav-link login-action <?= $currentPage === 'login.php' ? 'active' : '' ?>" href="/bca/admin/login.php"><i class="bi bi-shield-lock-fill me-1"></i>Admin Login</a></li>
        <?php endif; ?>
        <li class="nav-item d-flex align-items-center ms-lg-2 mt-2 mt-lg-0">
          <button class="theme-toggle-btn" type="button" aria-label="Toggle theme">
            <i class="bi bi-moon-stars-fill"></i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<?php if ($flash): ?>
  <div class="container mt-3">
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
      <?= e($flash['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  </div>
<?php endif; ?>

<main class="container page-shell py-4">
