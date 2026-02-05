<?php
/**
 * 문의 접수 처리
 */

// CORS 헤더 설정 (같은 도메인에서만 허용)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: https://lkitchen.co.kr');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// 기본 설정
define('DATA_PATH', __DIR__ . '/data');
date_default_timezone_set('Asia/Seoul');

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
$sinkInfo = trim($_POST['sinkInfo'] ?? '');
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

// CoolSMS 문자 발송 함수
function sendSmsNotification($name, $product, $phone, $address = '') {
    $apiKey = COOLSMS_API_KEY;
    $apiSecret = COOLSMS_API_SECRET;
    $receiver = COOLSMS_RECEIVER;

    // 메시지 내용 (90바이트 초과 시 LMS 자동 전환)
    $message = "[러블리키친 상담접수]\n";
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
    'address' => sanitize($address),
    'daytime' => sanitize($daytime),
    'sinkInfo' => sanitize($sinkInfo),
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
    // SMS 알림 발송 (실패해도 문의 접수는 성공 처리)
    sendSmsNotification($name, $product, $phone, $address);

    echo json_encode(['success' => true, 'message' => '문의가 성공적으로 접수되었습니다.'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'message' => '문의 접수 중 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
}
