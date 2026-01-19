<?php
/**
 * 문의 접수 처리
 */

// CORS 헤더 설정 (같은 도메인에서만 허용)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://115.68.223.124');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// 기본 설정
define('DATA_PATH', __DIR__ . '/data');
date_default_timezone_set('Asia/Seoul');

// POST 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => '허용되지 않는 요청 방식입니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 데이터 받기
$name = trim($_POST['name'] ?? '');
$product = trim($_POST['product'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

// 유효성 검사
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => '성함을 입력해주세요.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (empty($product)) {
    echo json_encode(['success' => false, 'message' => '희망모델을 선택해주세요.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (empty($phone)) {
    echo json_encode(['success' => false, 'message' => '연락처를 입력해주세요.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// XSS 방지
function sanitize($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// JSON 파일 읽기
function readInquiries() {
    $filepath = DATA_PATH . '/inquiries.json';
    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
        return json_decode($content, true) ?: [];
    }
    return [];
}

// JSON 파일 쓰기
function writeInquiries($data) {
    $filepath = DATA_PATH . '/inquiries.json';
    return file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// 기존 문의 데이터 읽기
$inquiries = readInquiries();

// 새 문의 ID 생성
$newId = 1;
if (!empty($inquiries)) {
    $maxId = max(array_column($inquiries, 'id'));
    $newId = $maxId + 1;
}

// 새 문의 데이터 생성
$newInquiry = [
    'id' => $newId,
    'name' => sanitize($name),
    'product' => sanitize($product),
    'phone' => sanitize($phone),
    'message' => sanitize($message),
    'status' => 'new', // new, read, replied
    'created_at' => date('Y-m-d H:i:s'),
    'read_at' => null,
    'replied_at' => null,
    'reply' => null
];

// 배열에 추가
$inquiries[] = $newInquiry;

// 파일에 저장
if (writeInquiries($inquiries)) {
    echo json_encode(['success' => true, 'message' => '문의가 성공적으로 접수되었습니다.'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'message' => '문의 접수 중 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
}
