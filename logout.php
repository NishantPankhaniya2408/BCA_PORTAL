<?php
require_once __DIR__ . '/config/db.php';
unset($_SESSION['selected_semester'], $_SESSION['selected_semester_student_id']);
logout_user('student_id', '/bca/index.php', 'You have been logged out.');
