<?php
require_once __DIR__ . '/includes/auth.php';
require_student();
$student = current_student();
if (!$student) {
    flash_set('warning', 'Please login to continue.');
    redirect('/bca/login.php');
}

$semester  = get_selected_student_semester($student);
$semesterOptions = get_student_semester_options();
$subjectId = isset($_GET['subject_id']) && $_GET['subject_id'] !== '' ? (int)$_GET['subject_id'] : 0;
$year      = isset($_GET['year']) && $_GET['year'] !== '' ? (int)$_GET['year'] : 0;

$subjectStmt = $pdo->prepare('SELECT * FROM subjects WHERE semester = ? ORDER BY code');
$subjectStmt->execute([$semester]);
$subjects = $subjectStmt->fetchAll();

$validSubjectIds = array_map(static fn($subject) => (int)$subject['id'], $subjects);
if ($subjectId && !in_array($subjectId, $validSubjectIds, true)) {
    $subjectId = 0;
}

$yearStmt = $pdo->prepare('SELECT DISTINCT p.exam_year
                           FROM papers p
                           JOIN subjects s ON s.id = p.subject_id
                           WHERE s.semester = ?
                           ORDER BY p.exam_year DESC');
$yearStmt->execute([$semester]);
$years = $yearStmt->fetchAll();

$validYears = array_map(static fn($paperYear) => (int)$paperYear['exam_year'], $years);
if ($year && !in_array($year, $validYears, true)) {
    $year = 0;
}

$sql = 'SELECT p.*, s.name AS subject_name, s.code AS subject_code, s.course_type
        FROM papers p JOIN subjects s ON s.id = p.subject_id
        WHERE s.semester = ?';
$params = [$semester];
if ($subjectId) { $sql .= ' AND p.subject_id = ?'; $params[] = $subjectId; }
if ($year)      { $sql .= ' AND p.exam_year = ?';  $params[] = $year; }
$sql .= ' ORDER BY p.exam_year DESC, p.uploaded_at DESC';
$st = $pdo->prepare($sql); $st->execute($params); $rows = $st->fetchAll();

$pageTitle = 'Question Papers';
include __DIR__ . '/includes/header.php';
?>
<section class="page-banner mb-4">
  <span class="eyebrow">Exam Preparation Archive</span>
  <h1 class="page-title"><i class="bi bi-file-earmark-text me-2"></i>Previous Year Question Papers</h1>
  <p class="page-copy mb-0">Use semester, subject, and year filters to narrow down the exact papers you want to review.</p>
</section>

<form class="filter-card mb-4" method="get">
  <div class="row g-3 align-items-end">
    <div class="col-md-3">
      <label class="form-label">Semester</label>
      <select name="semester" class="form-select" onchange="this.form.submit()">
        <?php foreach ($semesterOptions as $option): ?>
          <option value="<?= $option ?>" <?= $option === $semester ? 'selected' : '' ?>>Semester <?= $option ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-5">
      <label class="form-label">Subject</label>
      <select name="subject_id" class="form-select" onchange="this.form.submit()">
        <option value="">All Subjects</option>
        <?php foreach ($subjects as $s): ?>
          <option value="<?= $s['id'] ?>" <?= (int)$s['id']===$subjectId?'selected':'' ?>><?= e($s['code']).' - '.e($s['name']).' ('.e($s['course_type']).')' ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Year</label>
      <select name="year" class="form-select" onchange="this.form.submit()">
        <option value="">All Years</option>
        <?php foreach ($years as $y): ?>
          <option value="<?= $y['exam_year'] ?>" <?= (int)$y['exam_year']===$year?'selected':'' ?>><?= $y['exam_year'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <div class="filter-summary">Showing <strong><?= count($rows) ?></strong> paper<?= count($rows) === 1 ? '' : 's' ?></div>
    </div>
  </div>
</form>

<?php if (empty($rows)): ?>
  <div class="alert alert-info">No question papers uploaded yet for this selection.</div>
<?php else: ?>
<div class="table-card">
  <div class="table-responsive">
    <table class="table data-table align-middle mb-0">
      <thead><tr><th>#</th><th>Title</th><th>Subject</th><th>Year</th><th>Type</th><th>Size</th><th>Action</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $i => $r): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><strong><?= e($r['title']) ?></strong></td>
          <td><span class="badge bg-primary"><?= e($r['subject_code']) ?></span> <?= e($r['subject_name']) ?><br><small class="text-muted"><?= e($r['course_type']) ?></small></td>
          <td><?= e($r['exam_year']) ?></td>
          <td><span class="badge bg-secondary"><?= e($r['exam_type']) ?></span></td>
          <td><?= format_size($r['file_size']) ?></td>
          <td><a href="download.php?type=paper&id=<?= $r['id'] ?>" class="btn btn-brand btn-sm"><i class="bi bi-download"></i> Download</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
