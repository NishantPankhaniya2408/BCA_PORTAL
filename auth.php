<?php
require_once __DIR__ . '/../config/db.php';

function current_student() {
    global $pdo;
    $studentId = (int)($_SESSION['student_id'] ?? 0);
    if ($studentId <= 0) return null;

    $st = $pdo->prepare('SELECT * FROM students WHERE id = ?');
    $st->execute([$studentId]);
    $student = $st->fetch();

    return $student ?: null;
}

function current_admin() {
    global $pdo;
    $adminId = (int)($_SESSION['admin_id'] ?? 0);
    if ($adminId <= 0) return null;

    $st = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
    $st->execute([$adminId]);
    $admin = $st->fetch();

    return $admin ?: null;
}

function require_student() {
    if (!current_student()) {
        unset($_SESSION['student_id']);
        flash_set('warning', 'Please login to continue.');
        redirect('/bca/login.php');
    }
}

function require_admin() {
    if (!current_admin()) {
        unset($_SESSION['admin_id']);
        flash_set('warning', 'Admin login required.');
        redirect('/bca/admin/login.php');
    }
}

function get_student_semester_options() {
    return range(1, 6);
}

function get_selected_student_semester($student = null) {
    if ($student === null) {
        $student = current_student();
    }

    $options = get_student_semester_options();
    $defaultSemester = (int)($student['semester'] ?? $options[0]);
    if (!in_array($defaultSemester, $options, true)) {
        $defaultSemester = $options[0];
    }

    $studentId = (int)($student['id'] ?? 0);
    $storedStudentId = (int)($_SESSION['selected_semester_student_id'] ?? 0);
    $selectedSemester = 0;

    if (isset($_GET['semester'])) {
        $selectedSemester = (int)$_GET['semester'];
    } elseif ($studentId > 0 && $storedStudentId === $studentId) {
        $selectedSemester = (int)($_SESSION['selected_semester'] ?? 0);
    }

    if (!in_array($selectedSemester, $options, true)) {
        $selectedSemester = $defaultSemester;
    }

    $_SESSION['selected_semester'] = $selectedSemester;
    $_SESSION['selected_semester_student_id'] = $studentId;

    return $selectedSemester;
}
