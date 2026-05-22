<?php
require_once __DIR__ . '/includes/auth.php';

$currentStudent = current_student();
if ($currentStudent) {
    redirect('/bca/dashboard.php');
}
if (!empty($_SESSION['student_id'])) {
    unset($_SESSION['student_id']);
}

$error = '';
$enrollment = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment = trim($_POST['enrollment_no'] ?? '');
    $password   = $_POST['password'] ?? '';

    if ($enrollment === '' || $password === '') {
        $error = 'Please enter enrollment number and password.';
    } else {
        $st = $pdo->prepare('SELECT * FROM students WHERE enrollment_no = ?');
        $st->execute([$enrollment]);
        $stu = $st->fetch();
        if ($stu && password_verify($password, $stu['password'])) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
            $_SESSION['student_id'] = $stu['id'];
            $_SESSION['selected_semester'] = (int)$stu['semester'];
            $_SESSION['selected_semester_student_id'] = (int)$stu['id'];
            flash_set('success', 'Welcome, ' . $stu['name'] . '!');
            redirect('/bca/dashboard.php');
        } else {
            $error = 'Invalid enrollment number or password.';
        }
    }
}
$pageTitle = 'Student Login';
include __DIR__ . '/includes/header.php';
?>
<section class="login-shell">
  <div class="row g-4 align-items-stretch w-100" style="max-width: 1000px;">
    <div class="col-lg-5">
      <div class="login-info d-flex flex-column h-100">
        <div>
          <span class="eyebrow">Student Access</span>
          <h1 class="login-title">Sign in to your semester workspace.</h1>
          <p class="login-text">Open notes, download subject materials, and practice with previous-year question papers from one organized dashboard.</p>
        </div>
        <div class="login-points my-auto">
          <div><i class="bi bi-check2-circle"></i> Semester-specific browsing</div>
          <div><i class="bi bi-check2-circle"></i> Quick file downloads</div>
          <div><i class="bi bi-check2-circle"></i> Secure enrollment-based login</div>
        </div>
        <div class="showcase-card mt-auto">
          <span>Academics Online</span>
          <strong>All BCA semesters preloaded &amp; ready</strong>
        </div>
        <div class="showcase-orb orb-one"></div>
        <div class="showcase-orb orb-two"></div>
      </div>
    </div>
    <div class="col-lg-7">
      <div class="login-panel">
        <div class="text-center mb-4">
          <div class="icon-box mx-auto mb-3"><i class="bi bi-person-badge"></i></div>
          <h3 class="fw-bold mb-2">Student Login</h3>
          <p class="text-muted mb-0">Use your enrollment number to sign in.</p>
        </div>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post" novalidate>
          <div class="mb-3">
            <label class="form-label">Enrollment Number</label>
            <input type="text" name="enrollment_no" class="form-control form-control-lg" placeholder="e.g. BCA2023001" value="<?= e($enrollment) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control form-control-lg" required>
          </div>
          <button class="btn btn-brand btn-lg w-100"><i class="bi bi-box-arrow-in-right"></i> Login</button>
        </form>
        <div class="text-center mt-3">
          <small class="text-muted">Demo: BCA2023001 / student123</small>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
