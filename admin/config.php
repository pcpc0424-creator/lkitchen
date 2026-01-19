<?php
/**
 * 러블리키친 관리자 설정 파일
 */

// 세션 시작
session_start();

// 기본 설정
define('SITE_NAME', '러블리키친 관리자');
define('SITE_URL', 'http://115.68.223.124/lovelykitchen');
define('ADMIN_URL', SITE_URL . '/admin');
define('ROOT_PATH', dirname(__DIR__));
define('ADMIN_PATH', __DIR__);
define('DATA_PATH', ADMIN_PATH . '/data');

// 관리자 계정 (실제 운영 시 변경 필요)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'lovelykitchen2024!');

// 이미지 업로드 설정
define('UPLOAD_PATH_FOOD', ROOT_PATH . '/pototo');
define('UPLOAD_PATH_SINK', ROOT_PATH . '/potopo');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// 시간대 설정
date_default_timezone_set('Asia/Seoul');

// 에러 표시 (개발 시에만 true)
ini_set('display_errors', 0);
error_reporting(E_ALL);

/**
 * 로그인 체크
 */
function checkLogin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: ' . ADMIN_URL . '/index.php');
        exit;
    }
}

/**
 * JSON 데이터 읽기
 */
function readJsonData($filename) {
    $filepath = DATA_PATH . '/' . $filename;
    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
        return json_decode($content, true) ?: [];
    }
    return [];
}

/**
 * JSON 데이터 쓰기
 */
function writeJsonData($filename, $data) {
    $filepath = DATA_PATH . '/' . $filename;
    return file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * 안전한 파일명 생성
 */
function generateSafeFilename($originalName) {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $timestamp = date('Y-m-d-H-i-s');
    $random = substr(md5(uniqid()), 0, 8);
    return "review_{$timestamp}_{$random}.{$extension}";
}

/**
 * XSS 방지
 */
function sanitize($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

/**
 * CSRF 토큰 생성
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF 토큰 검증
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * 응답 JSON 반환
 */
function jsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
