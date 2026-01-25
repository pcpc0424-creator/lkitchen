<?php
/**
 * 러블리키친 프론트엔드 공통 설정
 */

// 기본 설정
define('SITE_URL', 'http://115.68.223.124/lovelykitchen');
define('ADMIN_DATA_PATH', dirname(__DIR__) . '/admin/data');

// 시간대 설정
date_default_timezone_set('Asia/Seoul');

/**
 * JSON 데이터 읽기
 */
function readJsonData($filename) {
    $filepath = ADMIN_DATA_PATH . '/' . $filename;
    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
        return json_decode($content, true) ?: [];
    }
    return [];
}

/**
 * 사이트 설정 가져오기
 */
function getSiteSettings() {
    $settings = readJsonData('settings.json');

    // 기본값 설정
    $defaults = [
        'site_name' => '러블리키친',
        'site_description' => '프리미엄 음식물처리기 No.1',
        'phone' => '1661-9038',
        'kakao_link' => 'https://pf.kakao.com/_lovelykitchen',
        'company_name' => '러블리키친 총판',
        'ceo_name' => '성정호',
        'business_number' => '306-08-91986',
        'address' => '서울시 서초구 반포대로22길 35, 2층 2002호',
        'email' => '',
        'footer_text' => '© 2024 LOVELY KITCHEN. All Rights Reserved.',
        'phone_image_url' => 'https://lkitchen.co.kr/wp-content/uploads/2025/10/전화문의.png'
    ];

    return array_merge($defaults, $settings);
}

/**
 * FAQ 데이터 가져오기
 */
function getFaqData() {
    return readJsonData('faq.json');
}

/**
 * 갤러리 데이터 가져오기
 */
function getGalleryData($type = 'food') {
    $filename = $type === 'food' ? 'food_gallery.json' : 'sink_gallery.json';
    return readJsonData($filename);
}

/**
 * 후기 데이터 가져오기
 */
function getReviewData($type = 'food') {
    $filename = $type === 'food' ? 'food_reviews.json' : 'sink_reviews.json';
    return readJsonData($filename);
}

/**
 * 제품 데이터 가져오기
 */
function getProductsData() {
    $data = readJsonData('products.json');
    return $data['products'] ?? [];
}

/**
 * XSS 방지
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// 사이트 설정 전역 변수로 로드
$siteSettings = getSiteSettings();
