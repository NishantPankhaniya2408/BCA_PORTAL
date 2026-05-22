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

$subjectStmt = $pdo->prepare('SELECT * FROM subjects WHERE semester = ? ORDER BY code');
$subjectStmt->execute([$semester]);
$subjects = $subjectStmt->fetchAll();

$validSubjectIds = array_map(static fn($subject) => (int)$subject['id'], $subjects);
if ($subjectId && !in_array($subjectId, $validSubjectIds, true)) {
    $subjectId = 0;
}

$sql = 'SELECT m.*, s.name AS subject_name, s.code AS subject_code, s.course_type
        FROM materials m JOIN subjects s ON s.id = m.subject_id
        WHERE s.semester = ?';
$params = [$semester];
if ($subjectId) { $sql .= ' AND m.subject_id = ?'; $params[] = $subjectId; }
$sql .= ' ORDER BY m.uploaded_at DESC';
$st = $pdo->prepare($sql); $st->execute($params); $rows = $st->fetchAll();

$pageTitle = 'Study Material';
include __DIR__ . '/includes/header.php';
?>
<section class="page-banner mb-4">
  <span class="eyebrow">Study Resource Library</span>
  <h1 class="page-title"><i class="bi bi-book me-2"></i>Study Material</h1>
  <p class="page-copy mb-0">Filter by semester and subject to find lecture notes, presentations, and reference files quickly.</p>
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
          <option value="<?= $s['id'] ?>" <?= (int)$s['id'] === $subjectId ? 'selected' : '' ?>><?= e($s['code']).' - '.e($s['name']).' ('.e($s['course_type']).')' ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <div class="filter-summary">Showing <strong><?= count($rows) ?></strong> material<?= count($rows) === 1 ? '' : 's' ?> for Semester <?= e($semester) ?></div>
    </div>
  </div>
</form>

<?php if (empty($rows)): ?>
  <div class="alert alert-info">No study material uploaded yet for this selection.</div>
<?php else: ?>
<div class="table-card">
  <div class="table-responsive">
    <table class="table data-table align-middle mb-0">
      <thead><tr><th>#</th><th>Title</th><th>Subject</th><th>Size</th><th>Uploaded</th><th>Action</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $i => $r): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><strong><?= e($r['title']) ?></strong><?php if ($r['description']): ?><br><small class="text-muted"><?= e($r['description']) ?></small><?php endif; ?></td>
          <td><span class="badge bg-primary"><?= e($r['subject_code']) ?></span> <?= e($r['subject_name']) ?><br><small class="text-muted"><?= e($r['course_type'] ?? '') ?></small></td>
          <td><?= format_size($r['file_size']) ?></td>
          <td><?= date('d M Y', strtotime($r['uploaded_at'])) ?></td>
          <td><a href="download.php?type=material&id=<?= $r['id'] ?>" class="btn btn-brand btn-sm"><i class="bi bi-download"></i> Download</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
