<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $st = $pdo->prepare('SELECT COUNT(*) FROM students WHERE id = ?');
    $st->execute([$deleteId]);

    if (!(int)$st->fetchColumn()) {
        flash_set('danger','Student not found.');
        redirect('students.php');
    }

    $st = $pdo->prepare('DELETE FROM students WHERE id = ?');
    $st->execute([$deleteId]);
    flash_set('success','Student deleted.');
    redirect('students.php');
}

// Handle add / edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int)($_POST['id'] ?? 0);
    $enroll    = trim($_POST['enrollment_no'] ?? '');
    $name      = trim($_POST['name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $semester  = (int)($_POST['semester'] ?? 0);
    $batch     = (int)($_POST['batch_year'] ?? 0);
    $password  = $_POST['password'] ?? '';

    if ($enroll === '') {
        flash_set('danger','Please enter an enrollment number.');
        redirect('students.php');
    }
    if ($name === '') {
        flash_set('danger','Please enter the student name.');
        redirect('students.php');
    }
    if ($semester < 1 || $semester > 6) {
        flash_set('danger','Please select a valid semester.');
        redirect('students.php');
    }
    if ($batch < 2000 || $batch > 2099) {
        flash_set('danger','Please enter a valid batch year.');
        redirect('students.php');
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash_set('danger','Please enter a valid email address.');
        redirect('students.php');
    }

    if ($id) {
        $st = $pdo->prepare('SELECT COUNT(*) FROM students WHERE id = ?');
        $st->execute([$id]);
        if (!(int)$st->fetchColumn()) {
            flash_set('danger','Student not found.');
            redirect('students.php');
        }
    }

    try {
        if ($id) {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $st = $pdo->prepare('UPDATE students SET enrollment_no=?,name=?,email=?,phone=?,semester=?,batch_year=?,password=? WHERE id=?');
                $st->execute([$enroll,$name,$email,$phone,$semester,$batch,$hash,$id]);
            } else {
                $st = $pdo->prepare('UPDATE students SET enrollment_no=?,name=?,email=?,phone=?,semester=?,batch_year=? WHERE id=?');
                $st->execute([$enroll,$name,$email,$phone,$semester,$batch,$id]);
            }
            flash_set('success','Student updated.');
        } else {
            if ($password === '') $password = 'student123';
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $st = $pdo->prepare('INSERT INTO students (enrollment_no,password,name,email,phone,semester,batch_year) VALUES (?,?,?,?,?,?,?)');
            $st->execute([$enroll,$hash,$name,$email,$phone,$semester,$batch]);
            flash_set('success','Student added.');
        }
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            flash_set('danger','Enrollment number already exists.');
        } else {
            flash_set('danger','Student could not be saved.');
        }
    }
    redirect('students.php');
}

$editing = null;
if (isset($_GET['edit'])) {
    $st = $pdo->prepare('SELECT * FROM students WHERE id=?');
    $st->execute([(int)$_GET['edit']]);
    $editing = $st->fetch();
    if (!$editing) {
        flash_set('danger','Student not found.');
        redirect('students.php');
    }
}

$search = trim($_GET['search'] ?? '');
$semester = (int)($_GET['semester'] ?? 0);

$query = 'SELECT * FROM students WHERE 1=1';
$params = [];

if ($search !== '') {
    $query .= ' AND (name LIKE ? OR enrollment_no LIKE ? OR email LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($semester >= 1 && $semester <= 6) {
    $query .= ' AND semester = ?';
    $params[] = $semester;
}

$query .= ' ORDER BY semester, enrollment_no';
$st = $pdo->prepare($query);
$st->execute($params);
$students = $st->fetchAll();

$pageTitle = 'Students';
include __DIR__ . '/_header.php';
?>
<div class="admin-page-head mb-4">
  <div>
    <span class="admin-kicker">Student Records</span>
    <h1 class="admin-page-title mb-1">Manage Students</h1>
    <p class="admin-page-text mb-0">Add, update, and organize student accounts with a cleaner form layout.</p>
  </div>
</div>

<div class="card admin-form-card mb-4">
  <div class="admin-form-top">
    <div class="admin-form-icon"><i class="bi bi-people-fill"></i></div>
    <div>
      <h5 class="fw-bold mb-1"><?= $editing ? 'Edit Student' : 'Add New Student' ?></h5>
      <p class="text-muted mb-0">Use the form below to maintain enrollment, contact, and semester details.</p>
    </div>
  </div>
  <div class="card-body p-4">
    <form method="post" class="row g-3">
      <input type="hidden" name="id" value="<?= e($editing['id'] ?? '') ?>">

      <div class="col-md-4">
        <label class="form-label">Enrollment No</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
          <input class="form-control" name="enrollment_no" value="<?= e($editing['enrollment_no'] ?? '') ?>" placeholder="e.g. BCA2023001" required>
        </div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Full Name</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input class="form-control" name="name" value="<?= e($editing['name'] ?? '') ?>" placeholder="Student full name" required>
        </div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input type="email" class="form-control" name="email" value="<?= e($editing['email'] ?? '') ?>" placeholder="student@example.com">
        </div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Phone</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-telephone"></i></span>
          <input class="form-control" name="phone" value="<?= e($editing['phone'] ?? '') ?>" placeholder="10-digit mobile number">
        </div>
      </div>

      <div class="col-md-3">
        <label class="form-label">Semester</label>
        <select class="form-select" name="semester" required>
          <?php for ($i=1;$i<=6;$i++): ?><option value="<?= $i ?>" <?= ($editing && (int)$editing['semester']===$i)?'selected':'' ?>>Semester <?= $i ?></option><?php endfor; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Batch Year</label>
        <input type="number" class="form-control" name="batch_year" min="2000" max="2099" value="<?= e($editing['batch_year'] ?? date('Y')) ?>" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-key"></i></span>
          <input type="text" class="form-control" name="password" placeholder="<?= $editing ? 'Leave blank to keep current password' : 'Default: student123' ?>">
        </div>
        <div class="form-text"><?= $editing ? 'Leave the password blank if you do not want to change it.' : 'If left empty while adding, the default password will be student123.' ?></div>
      </div>

      <div class="col-12 d-flex flex-wrap gap-2 pt-2">
        <button class="btn btn-primary px-4"><i class="bi bi-check2-circle me-1"></i><?= $editing ? 'Update Student' : 'Add Student' ?></button>
        <?php if ($editing): ?><a href="students.php" class="btn btn-outline-secondary px-4">Cancel</a><?php endif; ?>
      </div>
    </form>
  </div>
</div>

<div class="admin-table-card">
  <div class="admin-table-head">
    <h5 class="mb-0">Student List</h5>
    <span class="admin-table-count"><?= count($students) ?> total</span>
  </div>
  <form method="get" class="row g-2 p-3 border-bottom align-items-end m-0">
    <div class="col-md-5">
      <label class="form-label small fw-semibold text-muted mb-1">Search Students</label>
      <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Search name, enrollment, email..." value="<?= e($search) ?>">
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
        <a href="students.php" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
      <?php endif; ?>
    </div>
  </form>
  <div class="table-responsive">
    <table class="table table-hover admin-table mb-0">
      <thead><tr><th>#</th><th>Enrollment</th><th>Name</th><th>Sem</th><th>Batch</th><th>Email</th><th>Phone</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($students as $i => $s): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><strong><?= e($s['enrollment_no']) ?></strong></td>
        <td><?= e($s['name']) ?></td>
        <td><span class="badge text-bg-primary">Semester <?= e($s['semester']) ?></span></td>
        <td><?= e($s['batch_year']) ?></td>
        <td><?= e($s['email']) ?></td>
        <td><?= e($s['phone']) ?></td>
        <td>
          <div class="d-flex gap-2">
            <a href="?edit=<?= $s['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
            <a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this student?')"><i class="bi bi-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/_footer.php'; ?>
