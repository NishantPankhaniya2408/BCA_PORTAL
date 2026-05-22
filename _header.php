<?php
require_once __DIR__ . '/../config/db.php';
$admin = null;
if (!empty($_SESSION['admin_id'])) {
    $st = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
    $st->execute([$_SESSION['admin_id']]);
    $admin = $st->fetch();
}
$flash = flash_get();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin | <?= isset($pageTitle) ? e($pageTitle) : 'BCA Portal' ?></title>
<meta name="description" content="Administration panel for BCA Department Portal.">
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
<body class="admin-shell">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="/bca/admin/dashboard.php">
      <span class="brand-mark me-2" style="width: 32px; height: 32px; border-radius: 8px; font-size: 0.9rem; box-shadow: none;"><i class="bi bi-shield-lock"></i></span>
      BCA Admin
    </a>
    <div class="d-flex align-items-center gap-2">
      <button class="theme-toggle-btn me-2" type="button" aria-label="Toggle theme">
        <i class="bi bi-moon-stars-fill"></i>
      </button>
      <?php if ($admin): ?>
        <span class="text-light me-3"><i class="bi bi-person-circle"></i> <?= e($admin['name']) ?></span>
        <a href="/bca/admin/logout.php" class="btn btn-sm btn-outline-light">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<div class="container-fluid">
  <div class="row">
    <?php if ($admin): ?>
    <aside class="col-md-2 sidebar p-0">
      <a href="/bca/admin/dashboard.php" class="<?= $current==='dashboard.php'?'active':'' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="/bca/admin/students.php"  class="<?= $current==='students.php'?'active':'' ?>"><i class="bi bi-people"></i> Students</a>
      <a href="/bca/admin/subjects.php"  class="<?= $current==='subjects.php'?'active':'' ?>"><i class="bi bi-journal-bookmark"></i> Subjects</a>
      <a href="/bca/admin/materials.php" class="<?= $current==='materials.php'?'active':'' ?>"><i class="bi bi-book"></i> Study Material</a>
      <a href="/bca/admin/papers.php"    class="<?= $current==='papers.php'?'active':'' ?>"><i class="bi bi-file-earmark-text"></i> Question Papers</a>
      <a href="/bca/index.php" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View Site</a>
    </aside>
    <main class="col-md-10 p-4">
    <?php else: ?>
    <main class="col-12 p-4">
    <?php endif; ?>
      <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
          <?= e($flash['msg']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
