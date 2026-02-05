<?php
/**
 * 러블리키친 관리자 설정 파일
 */

// 세션 시작
session_start();

// 기본 설정
define('SITE_NAME', '러블리키친 관리자');
define('SITE_URL', 'https://lkitchen.co.kr');
define('ADMIN_URL', SITE_URL . '/admin');
define('ROOT_PATH', dirname(__DIR__));
define('ADMIN_PATH', __DIR__);
define('DATA_PATH', ADMIN_PATH . '/data');

// 관리자 계정 (실제 운영 시 변경 필요)
define('ADMIN_USERNAME', 'Admin');
define('ADMIN_PASSWORD', 'rich7744');

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
 * 안전한 파일명 생성 (후기용)
 */
function generateSafeFilename($originalName) {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $timestamp = date('Y-m-d-H-i-s');
    $random = substr(md5(uniqid()), 0, 8);
    return "review_{$timestamp}_{$random}.{$extension}";
}

/**
 * 안전한 파일명 생성 (갤러리용)
 */
function generateGalleryFilename($originalName) {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $timestamp = date('Y-m-d-H-i-s');
    $random = substr(md5(uniqid()), 0, 8);
    return "gallery_{$timestamp}_{$random}.{$extension}";
}

/**
 * 이미지 리사이징 (최대 크기 기준, 비율 유지)
 * @param string $filePath 이미지 파일 경로
 * @param int $maxDimension 최대 가로/세로 크기 (px)
 * @param int $quality JPEG/WebP 품질 (1-100)
 * @return bool 성공 여부
 */
function resizeImage($filePath, $maxDimension = 1600, $quality = 85) {
    $imageInfo = getimagesize($filePath);
    if ($imageInfo === false) return false;

    $origWidth = $imageInfo[0];
    $origHeight = $imageInfo[1];
    $mimeType = $imageInfo['mime'];

    // 이미 작으면 리사이징 불필요
    if ($origWidth <= $maxDimension && $origHeight <= $maxDimension) {
        return true;
    }

    // 비율 계산
    if ($origWidth >= $origHeight) {
        $newWidth = $maxDimension;
        $newHeight = (int) round($origHeight * ($maxDimension / $origWidth));
    } else {
        $newHeight = $maxDimension;
        $newWidth = (int) round($origWidth * ($maxDimension / $origHeight));
    }

    // 원본 이미지 로드
    switch ($mimeType) {
        case 'image/jpeg':
            $srcImage = imagecreatefromjpeg($filePath);
            break;
        case 'image/png':
            $srcImage = imagecreatefrompng($filePath);
            break;
        case 'image/gif':
            $srcImage = imagecreatefromgif($filePath);
            break;
        case 'image/webp':
            $srcImage = imagecreatefromwebp($filePath);
            break;
        default:
            return false;
    }

    if (!$srcImage) return false;

    // 리사이징
    $dstImage = imagecreatetruecolor($newWidth, $newHeight);

    // PNG/GIF 투명도 유지
    if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
        $transparent = imagecolorallocatealpha($dstImage, 0, 0, 0, 127);
        imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    // 저장
    $result = false;
    switch ($mimeType) {
        case 'image/jpeg':
            $result = imagejpeg($dstImage, $filePath, $quality);
            break;
        case 'image/png':
            $result = imagepng($dstImage, $filePath, 8);
            break;
        case 'image/gif':
            $result = imagegif($dstImage, $filePath);
            break;
        case 'image/webp':
            $result = imagewebp($dstImage, $filePath, $quality);
            break;
    }

    imagedestroy($srcImage);
    imagedestroy($dstImage);

    return $result;
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
