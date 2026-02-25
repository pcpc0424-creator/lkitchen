<?php
/**
 * 상담 신청 접수 처리 (빠른상담 페이지용)
 */

// CORS 헤더 설정 (같은 도메인에서만 허용)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: https://lkitchen.co.kr');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// 기본 설정
define('DATA_PATH', __DIR__ . '/data');
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/consultations');
define('UPLOAD_URL', '/uploads/consultations');
date_default_timezone_set('Asia/Seoul');

// 업로드 폴더 생성
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0777, true);
}

// CoolSMS 설정
define('COOLSMS_API_KEY', 'NCSANOHZHWVP0FMY');
define('COOLSMS_API_SECRET', 'R8441DXPRPGB59BK6KTWV7SP02X4XWDR');
define('COOLSMS_RECEIVER', '01090474987'); // 알림 받을 번호

// POST 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => '허용되지 않는 요청 방식입니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 데이터 받기
$name = trim($_POST['name'] ?? '');
// product가 배열인 경우(체크박스) 또는 문자열인 경우(기존 select) 처리
$productRaw = $_POST['product'] ?? '';
if (is_array($productRaw)) {
    $product = implode(', ', array_map('trim', $productRaw));
} else {
    $product = trim($productRaw);
}
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$daytime = trim($_POST['daytime'] ?? '');
$callTime = trim($_POST['callTime'] ?? '');
$sinkInfo = trim($_POST['sinkInfo'] ?? '');
$quoteData = trim($_POST['quoteData'] ?? '');
$source = trim($_POST['source'] ?? '일반');
$message = trim($_POST['message'] ?? '');

// 유효성 검사
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => '성함을 입력해주세요.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (empty($product)) {
    echo json_encode(['success' => false, 'message' => '관심 제품을 선택해주세요.'], JSON_UNESCAPED_UNICODE);
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

// 이미지 리사이징 (최대 크기 기준, 비율 유지)
function resizeImage($filePath, $maxDimension = 1600, $quality = 85) {
    $imageInfo = getimagesize($filePath);
    if ($imageInfo === false) return false;

    $origWidth = $imageInfo[0];
    $origHeight = $imageInfo[1];
    $mimeType = $imageInfo['mime'];

    if ($origWidth <= $maxDimension && $origHeight <= $maxDimension) {
        return true;
    }

    if ($origWidth >= $origHeight) {
        $newWidth = $maxDimension;
        $newHeight = (int) round($origHeight * ($maxDimension / $origWidth));
    } else {
        $newHeight = $maxDimension;
        $newWidth = (int) round($origWidth * ($maxDimension / $origHeight));
    }

    switch ($mimeType) {
        case 'image/jpeg': $srcImage = imagecreatefromjpeg($filePath); break;
        case 'image/png':  $srcImage = imagecreatefrompng($filePath); break;
        case 'image/gif':  $srcImage = imagecreatefromgif($filePath); break;
        case 'image/webp': $srcImage = imagecreatefromwebp($filePath); break;
        default: return false;
    }
    if (!$srcImage) return false;

    $dstImage = imagecreatetruecolor($newWidth, $newHeight);
    if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
        $transparent = imagecolorallocatealpha($dstImage, 0, 0, 0, 127);
        imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    $result = false;
    switch ($mimeType) {
        case 'image/jpeg': $result = imagejpeg($dstImage, $filePath, $quality); break;
        case 'image/png':  $result = imagepng($dstImage, $filePath, 8); break;
        case 'image/gif':  $result = imagegif($dstImage, $filePath); break;
        case 'image/webp': $result = imagewebp($dstImage, $filePath, $quality); break;
    }

    imagedestroy($srcImage);
    imagedestroy($dstImage);
    return $result;
}

// 이미지 업로드 처리 함수
function uploadImage($fileKey, $prefix) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$fileKey];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // 파일 타입 검사
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return null;
    }

    // 파일 확장자 결정
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    $ext = $extensions[$mimeType] ?? 'jpg';

    // 파일명 생성
    $filename = $prefix . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $filepath = UPLOAD_PATH . '/' . $filename;

    // 파일 이동
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // 자동 리사이징 (최대 1600px, 비율 유지)
        resizeImage($filepath, 1600, 85);
        return UPLOAD_URL . '/' . $filename;
    }

    return null;
}

// JSON 파일 읽기
function readConsultations() {
    $filepath = DATA_PATH . '/consultations.json';
    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
        return json_decode($content, true) ?: [];
    }
    return [];
}

// JSON 파일 쓰기
function writeConsultations($data) {
    $filepath = DATA_PATH . '/consultations.json';
    return file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// CoolSMS 문자 발송 함수
function sendSmsNotification($name, $product, $phone, $address = '', $source = '일반') {
    $apiKey = COOLSMS_API_KEY;
    $apiSecret = COOLSMS_API_SECRET;
    $receiver = COOLSMS_RECEIVER;

    // 메시지 내용 (90바이트 초과 시 LMS 자동 전환)
    // 특가 페이지 구분
    if ($source !== '일반') {
        $message = "[러블리키친 특가 상담]\n";
        $message .= "출처: {$source}\n";
    } else {
        $message = "[러블리키친 상담신청]\n";
    }
    $message .= "성함: {$name}\n";
    $message .= "모델: {$product}\n";
    $message .= "연락처: {$phone}";
    if (!empty($address)) {
        $message .= "\n주소: {$address}";
    }

    // CoolSMS API v4 호출
    $url = 'https://api.coolsms.co.kr/messages/v4/send';

    // 인증 정보 생성
    $date = date('Y-m-d\TH:i:s.v\Z');
    $salt = bin2hex(random_bytes(16));
    $signature = hash_hmac('sha256', $date . $salt, $apiSecret);

    $authHeader = "HMAC-SHA256 apiKey={$apiKey}, date={$date}, salt={$salt}, signature={$signature}";

    // 요청 데이터
    $data = [
        'message' => [
            'to' => $receiver,
            'from' => $receiver, // 발신번호 (사전 등록 필요)
            'text' => $message
        ]
    ];

    // cURL 요청
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . $authHeader
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 로그 기록 (선택사항)
    $logFile = DATA_PATH . '/sms_log.txt';
    $logData = date('Y-m-d H:i:s') . " | HTTP {$httpCode} | {$response}\n";
    file_put_contents($logFile, $logData, FILE_APPEND);

    return $httpCode === 200;
}

// 기존 상담 데이터 읽기
$consultations = readConsultations();

// 새 상담 ID 생성
$newId = 1;
if (!empty($consultations)) {
    $maxId = max(array_column($consultations, 'id'));
    $newId = $maxId + 1;
}

// 이미지 업로드 처리
$photoCountertop = uploadImage('photo_countertop', 'countertop');
$photoCabinet = uploadImage('photo_cabinet', 'cabinet');

// 새 상담 데이터 생성
$newConsultation = [
    'id' => $newId,
    'name' => sanitize($name),
    'product' => sanitize($product),
    'phone' => sanitize($phone),
    'address' => sanitize($address),
    'daytime' => sanitize($daytime),
    'callTime' => sanitize($callTime),
    'sinkInfo' => sanitize($sinkInfo),
    'quoteData' => $quoteData, // JSON 문자열 그대로 저장
    'source' => sanitize($source), // 일반, 특가-싱크볼 등
    'message' => sanitize($message),
    'photo_countertop' => $photoCountertop,
    'photo_cabinet' => $photoCabinet,
    'status' => 'new', // new, read, replied
    'created_at' => date('Y-m-d H:i:s'),
    'read_at' => null,
    'replied_at' => null,
    'reply' => null
];

// 배열에 추가
$consultations[] = $newConsultation;

// 파일에 저장
if (writeConsultations($consultations)) {
    // SMS 알림 발송 (실패해도 상담 접수는 성공 처리)
    sendSmsNotification($name, $product, $phone, $address, $source);

    echo json_encode(['success' => true, 'message' => '상담 신청이 성공적으로 접수되었습니다.'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'message' => '상담 신청 접수 중 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
}
