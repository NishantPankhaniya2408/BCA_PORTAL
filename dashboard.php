<?php
require_once __DIR__ . '/includes/auth.php';
require_student();
$student = current_student();
if (!$student) {
    flash_set('warning', 'Please login to continue.');
    redirect('/bca/login.php');
}

$semester = get_selected_student_semester($student);
$semesterOptions = get_student_semester_options();

$mat = $pdo->prepare('SELECT COUNT(*) FROM materials m JOIN subjects s ON s.id = m.subject_id WHERE s.semester = ?');
$mat->execute([$semester]);
$matCount = (int)$mat->fetchColumn();

$pap = $pdo->prepare('SELECT COUNT(*) FROM papers p JOIN subjects s ON s.id = p.subject_id WHERE s.semester = ?');
$pap->execute([$semester]);
$papCount = (int)$pap->fetchColumn();

$sub = $pdo->prepare('SELECT COUNT(*) FROM subjects WHERE semester = ?');
$sub->execute([$semester]);
$subCount = (int)$sub->fetchColumn();

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>
<section class="page-banner mb-4">
  <div class="row g-4 align-items-end">
    <div class="col-lg-8">
      <span class="eyebrow">Student Dashboard</span>
      <h1 class="page-title">Welcome, <?= e($student['name']) ?></h1>
      <p class="page-copy mb-0">Enrollment: <strong><?= e($student['enrollment_no']) ?></strong> &middot; Default Semester <?= e($student['semester']) ?> &middot; Viewing Semester <?= e($semester) ?> &middot; Batch <?= e($student['batch_year']) ?></p>
    </div>
    <div class="col-lg-4">
      <form method="get" class="surface-soft">
        <label class="form-label mb-1">Choose Semester</label>
        <select name="semester" class="form-select" onchange="this.form.submit()">
          <?php foreach ($semesterOptions as $option): ?>
            <option value="<?= $option ?>" <?= $option === $semester ? 'selected' : '' ?>>Semester <?= $option ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>
  </div>
</section>

<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="mini-stat"><span>Subjects (Sem <?= e($semester) ?>)</span><strong><?= $subCount ?></strong></div></div>
  <div class="col-md-4"><div class="mini-stat"><span>Study Materials</span><strong><?= $matCount ?></strong></div></div>
  <div class="col-md-4"><div class="mini-stat"><span>Question Papers</span><strong><?= $papCount ?></strong></div></div>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="feature-tile tile-primary">
      <div class="icon-box mb-3"><i class="bi bi-book"></i></div>
      <h5 class="fw-bold">Study Material</h5>
      <p class="mb-4">Browse notes, presentations, and PDFs uploaded for the semester you are viewing.</p>
      <a href="materials.php?semester=<?= $semester ?>" class="btn btn-brand">Open Materials</a>
    </div>
  </div>
  <div class="col-md-6">
    <div class="feature-tile tile-success">
      <div class="icon-box mb-3" style="color: var(--success); background: rgba(16, 185, 129, 0.08);"><i class="bi bi-file-earmark-text"></i></div>
      <h5 class="fw-bold">Question Papers</h5>
      <p class="mb-4">Practice with previous-year papers and refine exam preparation by subject and year.</p>
      <a href="papers.php?semester=<?= $semester ?>" class="btn btn-brand">Open Papers</a>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
