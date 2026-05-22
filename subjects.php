<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $st = $pdo->prepare('SELECT COUNT(*) FROM subjects WHERE id = ?');
    $st->execute([$deleteId]);

    if (!(int)$st->fetchColumn()) {
        flash_set('danger','Subject not found.');
        redirect('subjects.php');
    }

    $st = $pdo->prepare('DELETE FROM subjects WHERE id = ?');
    $st->execute([$deleteId]);
    flash_set('success','Subject deleted.');
    redirect('subjects.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = (int)($_POST['id'] ?? 0);
    $code       = trim($_POST['code'] ?? '');
    $name       = trim($_POST['name'] ?? '');
    $courseType = trim($_POST['course_type'] ?? '');
    $sem        = (int)($_POST['semester'] ?? 0);

    if ($code === '') {
        flash_set('danger','Please enter a subject code.');
        redirect('subjects.php');
    }
    if ($name === '') {
        flash_set('danger','Please enter a subject name.');
        redirect('subjects.php');
    }
    if ($courseType === '') {
        flash_set('danger','Please enter the course type.');
        redirect('subjects.php');
    }
    if ($sem < 1 || $sem > 6) {
        flash_set('danger','Please select a valid semester.');
        redirect('subjects.php');
    }

    if ($id) {
        $st = $pdo->prepare('SELECT COUNT(*) FROM subjects WHERE id = ?');
        $st->execute([$id]);
        if (!(int)$st->fetchColumn()) {
            flash_set('danger','Subject not found.');
            redirect('subjects.php');
        }
    }

    try {
        if ($id) {
            $st = $pdo->prepare('UPDATE subjects SET code=?,name=?,course_type=?,semester=? WHERE id=?');
            $st->execute([$code,$name,$courseType,$sem,$id]);
            flash_set('success','Subject updated.');
        } else {
            $st = $pdo->prepare('INSERT INTO subjects (code,name,course_type,semester) VALUES (?,?,?,?)');
            $st->execute([$code,$name,$courseType,$sem]);
            flash_set('success','Subject added.');
        }
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            flash_set('danger','Subject code already exists.');
        } else {
            flash_set('danger','Subject could not be saved.');
        }
    }
    redirect('subjects.php');
}

$editing = null;
if (isset($_GET['edit'])) {
    $st = $pdo->prepare('SELECT * FROM subjects WHERE id=?');
    $st->execute([(int)$_GET['edit']]);
    $editing = $st->fetch();
    if (!$editing) {
        flash_set('danger','Subject not found.');
        redirect('subjects.php');
    }
}
$search = trim($_GET['search'] ?? '');
$semester = (int)($_GET['semester'] ?? 0);

$query = 'SELECT * FROM subjects WHERE 1=1';
$params = [];

if ($search !== '') {
    $query .= ' AND (code LIKE ? OR name LIKE ? OR course_type LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($semester >= 1 && $semester <= 6) {
    $query .= ' AND semester = ?';
    $params[] = $semester;
}

$query .= ' ORDER BY semester, code';
$st = $pdo->prepare($query);
$st->execute($params);
$rows = $st->fetchAll();

$pageTitle = 'Subjects';
include __DIR__ . '/_header.php';
?>
<div class="admin-page-head mb-4">
  <div>
    <span class="admin-kicker">Course Catalog</span>
    <h1 class="admin-page-title mb-1">Manage Subjects</h1>
    <p class="admin-page-text mb-0">Create semester-wise subjects and assign the right course type for each one.</p>
  </div>
</div>

<div class="card admin-form-card mb-4">
  <div class="admin-form-top">
    <div class="admin-form-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
    <div>
      <h5 class="fw-bold mb-1"><?= $editing ? 'Edit Subject' : 'Add Subject' ?></h5>
      <p class="text-muted mb-0">Keep your semester structure clear with properly named and typed subjects.</p>
    </div>
  </div>
  <div class="card-body p-4">
    <form method="post" class="row g-3">
      <input type="hidden" name="id" value="<?= e($editing['id'] ?? '') ?>">

      <div class="col-md-3">
        <label class="form-label">Code</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
          <input class="form-control" name="code" value="<?= e($editing['code'] ?? '') ?>" placeholder="e.g. BCA101" required>
        </div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Subject Name</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-book"></i></span>
          <input class="form-control" name="name" value="<?= e($editing['name'] ?? '') ?>" placeholder="Enter subject name" required>
        </div>
      </div>

      <div class="col-md-3">
        <label class="form-label">Course Type</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
          <input class="form-control" name="course_type" value="<?= e($editing['course_type'] ?? 'Theory') ?>" placeholder="Theory / Practical / Project" required>
        </div>
      </div>

      <div class="col-md-2">
        <label class="form-label">Semester</label>
        <select class="form-select" name="semester" required>
          <?php for ($i=1;$i<=6;$i++): ?><option value="<?= $i ?>" <?= ($editing && (int)$editing['semester']===$i)?'selected':'' ?>>Sem <?= $i ?></option><?php endfor; ?>
        </select>
      </div>

      <div class="col-12 d-flex flex-wrap gap-2 pt-2">
        <button class="btn btn-primary px-4"><i class="bi bi-check2-circle me-1"></i><?= $editing ? 'Update Subject' : 'Add Subject' ?></button>
        <?php if ($editing): ?><a href="subjects.php" class="btn btn-outline-secondary px-4">Cancel</a><?php endif; ?>
      </div>
    </form>
  </div>
</div>

<div class="admin-table-card">
  <div class="admin-table-head">
    <h5 class="mb-0">Subject List</h5>
    <span class="admin-table-count"><?= count($rows) ?> total</span>
  </div>
  <form method="get" class="row g-2 p-3 border-bottom align-items-end m-0">
    <div class="col-md-5">
      <label class="form-label small fw-semibold text-muted mb-1">Search Subjects</label>
      <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Search code, name, type..." value="<?= e($search) ?>">
      </div>
    </div>
    <div class="col-md-3">
      <label class="form-label small fw-semibold text-muted mb-1">Semester Filter</label>
      <select name="semester" class="form-select form-select-sm">
        <option value="">All Semesters</option>
        <?php for ($i=1; $i<=6; $i++): ?>
          <option value="<?= $i ?>" <?= $semester === $i ? 'selected' : '' ?>>Semester <?= $i ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
      <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
      <?php if ($search !== '' || $semester > 0): ?>
        <a href="subjects.php" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
      <?php endif; ?>
    </div>
  </form>
  <div class="table-responsive">
    <table class="table table-hover admin-table mb-0">
      <thead><tr><th>#</th><th>Code</th><th>Name</th><th>Course Type</th><th>Sem</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $i => $r): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><strong><?= e($r['code']) ?></strong></td>
        <td><?= e($r['name']) ?></td>
        <td><span class="badge rounded-pill text-bg-info"><?= e($r['course_type']) ?></span></td>
        <td><span class="badge text-bg-primary">Semester <?= e($r['semester']) ?></span></td>
        <td>
          <div class="d-flex gap-2">
            <a href="?edit=<?= $r['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
            <a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this subject? Related materials/papers will also be removed.')"><i class="bi bi-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/_footer.php'; ?>
