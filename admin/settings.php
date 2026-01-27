<?php
/**
 * 러블리키친 사이트 설정
 */
require_once 'config.php';
checkLogin();

// 설정 데이터 파일
$settingsFile = 'settings.json';
$settings = readJsonData($settingsFile);

// 기본 설정값
$defaultSettings = [
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
    'phone_image_url' => 'https://lkitchen.co.kr/wp-content/uploads/2025/10/전화문의.png',
    'sinkbowl_calc_image' => '/pototo/tkdlwlm.png'
];

$settings = array_merge($defaultSettings, $settings);

// 설정 저장 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'CSRF 토큰이 유효하지 않습니다.';
    } else {
        $newSettings = [
            'site_name' => trim($_POST['site_name'] ?? ''),
            'site_description' => trim($_POST['site_description'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'kakao_link' => trim($_POST['kakao_link'] ?? ''),
            'company_name' => trim($_POST['company_name'] ?? ''),
            'ceo_name' => trim($_POST['ceo_name'] ?? ''),
            'business_number' => trim($_POST['business_number'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'footer_text' => trim($_POST['footer_text'] ?? ''),
            'phone_image_url' => trim($_POST['phone_image_url'] ?? ''),
            'sinkbowl_calc_image' => trim($_POST['sinkbowl_calc_image'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        writeJsonData($settingsFile, $newSettings);
        $settings = $newSettings;
        $success = true;
    }
}

// 새 문의 수 계산 (사이드바용)
$inquiries = readJsonData('inquiries.json');
$newInquiries = array_filter($inquiries, function($inquiry) {
    return $inquiry['status'] === 'new';
});
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>사이트 설정 | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css?v=2">
    <link rel="icon" type="image/png" href="http://115.68.223.124/lovelykitchen/수정/fhrh.png">
    <style>
        .settings-form {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .settings-section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #eee;
        }
        .settings-section:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .settings-section h3 {
            font-size: 1.1rem;
            color: #051535;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .settings-section h3 i {
            color: #3498db;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        .preview-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .preview-box img {
            max-width: 150px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">
                    <img src="http://115.68.223.124/lovelykitchen/수정/fhrh.png" alt="러블리키친">
                </a>
                <span class="sidebar-title">관리자</span>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>대시보드</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="products.php" class="nav-link">
                            <i class="fas fa-box"></i>
                            <span>제품 관리</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="reviews.php?type=food" class="nav-link">
                            <i class="fas fa-utensils"></i>
                            <span>음식물처리기 후기</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="reviews.php?type=sink" class="nav-link">
                            <i class="fas fa-sink"></i>
                            <span>싱크볼 후기</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="images.php" class="nav-link">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>갤러리 관리</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="faq.php" class="nav-link">
                            <i class="fas fa-question-circle"></i>
                            <span>FAQ 관리</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="inquiries.php" class="nav-link">
                            <i class="fas fa-envelope"></i>
                            <span>문의 관리</span>
                            <?php if (count($newInquiries) > 0): ?>
                            <span class="nav-badge"><?php echo count($newInquiries); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="special.php" class="nav-link">
                            <i class="fas fa-tag"></i>
                            <span>특가페이지 관리</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="sinkbowl_calc.php" class="nav-link">
                            <i class="fas fa-calculator"></i>
                            <span>싱크볼 계산식</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="settings.php" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>사이트 설정</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>로그아웃</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">사이트 설정</h1>
                </div>
                <div class="header-right">
                    <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn-site">
                        <i class="fas fa-external-link-alt"></i>
                        사이트 보기
                    </a>
                </div>
            </header>

            <div class="content-body">
                <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> 설정이 저장되었습니다.
                </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <form method="POST" class="settings-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <!-- 기본 정보 -->
                    <div class="settings-section">
                        <h3><i class="fas fa-globe"></i> 기본 정보</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="site_name">사이트명</label>
                                <input type="text" id="site_name" name="site_name"
                                       value="<?php echo sanitize($settings['site_name']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="site_description">사이트 설명</label>
                                <input type="text" id="site_description" name="site_description"
                                       value="<?php echo sanitize($settings['site_description']); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- 연락처 정보 -->
                    <div class="settings-section">
                        <h3><i class="fas fa-phone-alt"></i> 연락처 정보</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">대표 전화번호</label>
                                <input type="text" id="phone" name="phone"
                                       value="<?php echo sanitize($settings['phone']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">이메일</label>
                                <input type="email" id="email" name="email"
                                       value="<?php echo sanitize($settings['email']); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="kakao_link">카카오톡 채널 링크</label>
                            <input type="url" id="kakao_link" name="kakao_link"
                                   value="<?php echo sanitize($settings['kakao_link']); ?>">
                        </div>
                    </div>

                    <!-- 회사 정보 -->
                    <div class="settings-section">
                        <h3><i class="fas fa-building"></i> 회사 정보</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="company_name">회사명</label>
                                <input type="text" id="company_name" name="company_name"
                                       value="<?php echo sanitize($settings['company_name']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="ceo_name">대표자명</label>
                                <input type="text" id="ceo_name" name="ceo_name"
                                       value="<?php echo sanitize($settings['ceo_name']); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="business_number">사업자등록번호</label>
                                <input type="text" id="business_number" name="business_number"
                                       value="<?php echo sanitize($settings['business_number']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="address">주소</label>
                                <input type="text" id="address" name="address"
                                       value="<?php echo sanitize($settings['address']); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- 푸터 설정 -->
                    <div class="settings-section">
                        <h3><i class="fas fa-copyright"></i> 푸터 설정</h3>
                        <div class="form-group">
                            <label for="footer_text">푸터 저작권 문구</label>
                            <input type="text" id="footer_text" name="footer_text"
                                   value="<?php echo sanitize($settings['footer_text']); ?>">
                        </div>
                    </div>

                    <!-- 전화문의 이미지 -->
                    <div class="settings-section">
                        <h3><i class="fas fa-image"></i> 전화문의 플로팅 이미지</h3>
                        <div class="form-group">
                            <label for="phone_image_url">이미지 URL</label>
                            <input type="url" id="phone_image_url" name="phone_image_url"
                                   value="<?php echo sanitize($settings['phone_image_url']); ?>">
                            <div class="preview-box">
                                <strong>미리보기:</strong><br>
                                <img src="<?php echo sanitize($settings['phone_image_url']); ?>" alt="전화문의 이미지" id="phoneImagePreview">
                            </div>
                        </div>
                    </div>

                    <!-- 싱크볼 사이즈 계산식 이미지 -->
                    <div class="settings-section">
                        <h3><i class="fas fa-calculator"></i> 싱크볼 사이즈 계산식 이미지</h3>
                        <div class="form-group">
                            <label for="sinkbowl_calc_image">이미지 경로 또는 URL</label>
                            <input type="text" id="sinkbowl_calc_image" name="sinkbowl_calc_image"
                                   value="<?php echo sanitize($settings['sinkbowl_calc_image'] ?? '/pototo/tkdlwlm.png'); ?>"
                                   placeholder="/pototo/tkdlwlm.png 또는 전체 URL">
                            <small style="color: #666; display: block; margin-top: 5px;">
                                이미지 관리에서 업로드한 이미지의 URL을 복사하여 붙여넣으세요.
                            </small>
                            <div class="preview-box">
                                <strong>미리보기:</strong><br>
                                <img src="<?php echo sanitize($settings['sinkbowl_calc_image'] ?? '/pototo/tkdlwlm.png'); ?>"
                                     alt="싱크볼 계산식" id="calcImagePreview"
                                     style="max-width: 100%; max-height: 300px;">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; padding: 15px; font-size: 1rem;">
                        <i class="fas fa-save"></i> 설정 저장
                    </button>
                </form>

                <?php if (isset($settings['updated_at'])): ?>
                <p style="text-align: center; color: #999; margin-top: 20px; font-size: 0.85rem;">
                    마지막 수정: <?php echo $settings['updated_at']; ?>
                </p>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
    <script>
        // 전화문의 이미지 미리보기
        document.getElementById('phone_image_url').addEventListener('input', function() {
            document.getElementById('phoneImagePreview').src = this.value;
        });

        // 싱크볼 계산식 이미지 미리보기
        document.getElementById('sinkbowl_calc_image').addEventListener('input', function() {
            document.getElementById('calcImagePreview').src = this.value;
        });
    </script>
</body>
</html>
