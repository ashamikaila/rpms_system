<?php
session_start();
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST required.']);
    exit;
}
if (!isset($_SESSION['profile_csrf']) || !is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['profile_csrf'], $_POST['csrf'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Please refresh the page and try again.']);
    exit;
}
$name = is_string($_POST['name'] ?? null) ? trim($_POST['name']) : '';
if ($name === '' || strlen($name) > 120) {
    http_response_code(422);
    echo json_encode(['error' => 'Enter a display name of up to 120 characters.']);
    exit;
}
$_SESSION['user_name'] = $name;
echo json_encode(['name' => $name]);
