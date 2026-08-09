<?php
session_start();
if (stripos((string)($_SESSION['user_role'] ?? ''), 'admin') !== false) {
    header('Location: admin_students.php');
    exit;
}
$portalRole = 'Student';
require __DIR__ . '/role_portal.php';
