<?php
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$email = filter_var($payload['email'] ?? '', FILTER_VALIDATE_EMAIL);
$name = trim((string)($payload['name'] ?? 'Student'));
$studentId = preg_replace('/[\r\n]+/', ' ', trim((string)($payload['studentId'] ?? '')));
$stage = trim((string)($payload['stage'] ?? ''));
$status = trim((string)($payload['status'] ?? ''));
$requirements = trim((string)($payload['requirements'] ?? ''));

if (!$email) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'A valid student email is required.']);
    exit;
}

$safeName = preg_replace('/[\r\n]+/', ' ', $name);
$subject = "IERB Progress Follow-up - {$studentId}";
$message = "Hello {$safeName},\n\nThis is an automated follow-up regarding your IERB progress.\nCurrent stage: {$stage}\nStatus: {$status}\n";
if ($requirements !== '') $message .= "Pending requirement: {$requirements}\n";
$message .= "\nPlease send your latest update to the RPMS office.\n\nThank you.";
$headers = "Content-Type: text/plain; charset=UTF-8\r\n";

if (!@mail($email, $subject, $message, $headers)) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'message' => 'The email server is not configured or did not accept the message.']);
    exit;
}

echo json_encode(['ok' => true, 'message' => 'Follow-up email sent successfully.']);
