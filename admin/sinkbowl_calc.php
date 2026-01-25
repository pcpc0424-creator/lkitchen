<?php
/**
 * 러블리키친 싱크볼 사이즈 계산식 관리
 */
require_once 'config.php';
checkLogin();

// special.json에서 싱크볼 데이터 로드
$specialFile = 'special.json';
$specialData = readJsonData($specialFile);
$sinkbowls = $specialData['sinkbowls'] ?? [];

// 설정 데이터 파일
$settingsFile = 'settings.json';
$settings = readJsonData($settingsFile);

// 계산식 기본값
$defaultCalcRules = [
    'condition_min_top_height' => 580,
    'condition_max_hole_height' => 490,
    'rules' => [
        [
            'model' => '860',
            'min_cabinet_width' => 790,
            'max_hole_width' => 830
        ],
        [
            'model' => '900',
            'min_cabinet_width' => 850,
            'max_hole_width' => 870
        ],
        [
            'model' => '980',
            'min_cabinet_width' => 940,
            'max_hole_width' => 950
        ]
    ],
    'impossible_conditions' => '하부장가로 <= 790 OR 타공가로 > 950 OR 타공세로 > 490',
    'consult_condition' => '상판세로 < 580'
];

$calcRules = $settings['sinkbowl_calc_rules'] ?? $defaultCalcRules;

// rules 배열이 없으면 기본값 사용
if (empty($calcRules['rules'])) {
    $calcRules['rules'] = $defaultCalcRules['rules'];
}
if (!isset($calcRules['condition_min_top_height'])) {
    $calcRules['condition_min_top_height'] = $defaultCalcRules['condition_min_top_height'];
}
if (!isset($calcRules['condition_max_hole_height'])) {
    $calcRules['condition_max_hole_height'] = $defaultCalcRules['condition_max_hole_height'];
}
if (!isset($calcRules['impossible_conditions'])) {
    $calcRules['impossible_conditions'] = $defaultCalcRules['impossible_conditions'];
}
if (!isset($calcRules['consult_condition'])) {
    $calcRules['consult_condition'] = $defaultCalcRules['consult_condition'];
}

$message = '';
$messageType = '';

// POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 싱크볼 제품 정보 수정
    if ($action === 'update_sinkbowl') {
        $sinkId = (int)$_POST['sink_id'];
        foreach ($specialData['sinkbowls'] as &$sink) {
            if ($sink['id'] === $sinkId) {
                $sink['model'] = $_POST['model'] ?? '';
                $sink['size'] = $_POST['size'] ?? '';
                $sink['price'] = $_POST['price'] ?? '';
                $sink['image'] = $_POST['image'] ?? '';
                $sink['features'] = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
                $sink['active'] = isset($_POST['active']);
                break;
            }
        }
        unset($sink);

        if (writeJsonData($specialFile, $specialData)) {
            $message = '싱크볼 정보가 저장되었습니다.';
            $messageType = 'success';
            // 데이터 다시 로드
            $specialData = readJsonData($specialFile);
            $sinkbowls = $specialData['sinkbowls'] ?? [];
        } else {
            $message = '저장 중 오류가 발생했습니다.';
            $messageType = 'error';
        }
    }

    // 계산식 규칙 수정
    if ($action === 'update_calc_rules') {
        $newCalcRules = [
            'condition_min_top_height' => (int)$_POST['condition_min_top_height'],
            'condition_max_hole_height' => (int)$_POST['condition_max_hole_height'],
            'rules' => [],
            'impossible_conditions' => trim($_POST['impossible_conditions'] ?? ''),
            'consult_condition' => trim($_POST['consult_condition'] ?? '')
        ];

        // 각 규칙 파싱
        for ($i = 0; $i < 3; $i++) {
            if (isset($_POST['rule_model'][$i])) {
                $newCalcRules['rules'][] = [
                    'model' => $_POST['rule_model'][$i],
                    'min_cabinet_width' => (int)$_POST['rule_min_cabinet'][$i],
                    'max_hole_width' => (int)$_POST['rule_max_hole'][$i]
                ];
            }
        }

        $settings['sinkbowl_calc_rules'] = $newCalcRules;
        $settings['updated_at'] = date('Y-m-d H:i:s');

        if (writeJsonData($settingsFile, $settings)) {
            $calcRules = $newCalcRules;
            $message = '계산식 규칙이 저장되었습니다.';
            $messageType = 'success';
        } else {
            $message = '저장 중 오류가 발생했습니다.';
            $messageType = 'error';
        }
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
    <title>싱크볼 사이즈 관리 | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css?v=2">
    <link rel="icon" type="image/png" href="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png">
    <style>
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }
        .tab-btn {
            padding: 12px 24px;
            background: #f1f5f9;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #64748b;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .tab-btn:hover {
            background: #e2e8f0;
        }
        .tab-btn.active {
            background: #3b82f6;
            color: #fff;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .form-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .form-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        .form-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: #475569;
            margin-bottom: 8px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.3s ease;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-group small {
            display: block;
            margin-top: 5px;
            font-size: 0.8rem;
            color: #94a3b8;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .btn-save {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(59, 130, 246, 0.3);
        }
        .status-active {
            display: inline-block;
            padding: 4px 10px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-inactive {
            display: inline-block;
            padding: 4px 10px;
            background: #fee2e2;
            color: #dc2626;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .preview-img {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
        }
        .calc-rule-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .calc-rule-box h4 {
            font-size: 0.95rem;
            color: #1e293b;
            margin-bottom: 15px;
        }
        .calc-preview {
            background: #1a1a2e;
            border-radius: 12px;
            padding: 25px;
            margin-top: 20px;
            color: #fff;
        }
        .calc-preview h4 {
            color: #60a5fa;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        .calc-preview .rule-line {
            padding: 8px 0;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .calc-preview .rule-line:last-child {
            border-bottom: none;
        }
        .calc-preview .condition {
            color: #94a3b8;
        }
        .calc-preview .result {
            color: #22c55e;
            font-weight: 600;
        }
        .calc-preview .warning {
            color: #f59e0b;
        }
        .calc-preview .error {
            color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">
                    <img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="러블리키친">
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
                        <a href="gallery.php" class="nav-link">
                            <i class="fas fa-images"></i>
                            <span>갤러리 관리</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="images.php" class="nav-link">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>이미지 업로드</span>
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
                    <li class="nav-item active">
                        <a href="sinkbowl_calc.php" class="nav-link">
                            <i class="fas fa-calculator"></i>
                            <span>싱크볼 계산식</span>
                        </a>
                    </li>
                    <li class="nav-item">
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
                    <h1 class="page-title">싱크볼 사이즈 관리</h1>
                </div>
                <div class="header-right">
                    <a href="<?php echo SITE_URL; ?>/싱크볼/" target="_blank" class="btn-site">
                        <i class="fas fa-external-link-alt"></i>
                        싱크볼 페이지 보기
                    </a>
                </div>
            </header>

            <div class="content-body">
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo sanitize($message); ?>
                </div>
                <?php endif; ?>

                <!-- Tabs -->
                <div class="tabs">
                    <button class="tab-btn active" data-tab="products">싱크볼 제품 사이즈</button>
                    <button class="tab-btn" data-tab="rules">사이즈 계산식 규칙</button>
                </div>

                <!-- 싱크볼 제품 사이즈 탭 -->
                <div id="tab-products" class="tab-content active">
                    <?php foreach ($sinkbowls as $sink): ?>
                    <form method="POST" class="form-card">
                        <input type="hidden" name="action" value="update_sinkbowl">
                        <input type="hidden" name="sink_id" value="<?php echo $sink['id']; ?>">

                        <div class="form-card-header">
                            <h3 class="form-card-title">
                                <i class="fas fa-sink"></i>
                                <?php echo sanitize($sink['model']); ?>
                            </h3>
                            <?php if ($sink['active']): ?>
                            <span class="status-active">활성화</span>
                            <?php else: ?>
                            <span class="status-inactive">비활성화</span>
                            <?php endif; ?>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>모델명</label>
                                <input type="text" name="model" value="<?php echo sanitize($sink['model']); ?>">
                            </div>
                            <div class="form-group">
                                <label>사이즈</label>
                                <input type="text" name="size" value="<?php echo sanitize($sink['size']); ?>" placeholder="860 × 525 × 210mm">
                            </div>
                            <div class="form-group">
                                <label>가격 (원)</label>
                                <input type="text" name="price" value="<?php echo sanitize($sink['price']); ?>" placeholder="480,000">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>이미지 URL</label>
                            <input type="url" name="image" value="<?php echo sanitize($sink['image']); ?>">
                            <?php if (!empty($sink['image'])): ?>
                            <img src="<?php echo sanitize($sink['image']); ?>" alt="미리보기" class="preview-img">
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label>특징 (줄바꿈으로 구분)</label>
                            <textarea name="features" rows="5"><?php echo sanitize(implode("\n", $sink['features'] ?? [])); ?></textarea>
                            <small>예: 860 × 525 × 210mm 와이드 사이즈</small>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" name="active" id="active_<?php echo $sink['id']; ?>" <?php echo $sink['active'] ? 'checked' : ''; ?>>
                                <label for="active_<?php echo $sink['id']; ?>">특가페이지에 표시</label>
                            </div>
                        </div>

                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> 저장하기</button>
                    </form>
                    <?php endforeach; ?>
                </div>

                <!-- 계산식 규칙 탭 -->
                <div id="tab-rules" class="tab-content">
                    <form method="POST" class="form-card">
                        <input type="hidden" name="action" value="update_calc_rules">

                        <div class="form-card-header">
                            <h3 class="form-card-title">
                                <i class="fas fa-calculator"></i>
                                싱크볼 사이즈 계산식
                            </h3>
                        </div>

                        <div class="calc-rule-box">
                            <h4><i class="fas fa-exclamation-triangle"></i> 전제 조건</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>상판세로 최소값 (mm)</label>
                                    <input type="number" name="condition_min_top_height" value="<?php echo $calcRules['condition_min_top_height']; ?>">
                                    <small>상판세로 >= 이 값이어야 시공 가능</small>
                                </div>
                                <div class="form-group">
                                    <label>타공세로 최대값 (mm)</label>
                                    <input type="number" name="condition_max_hole_height" value="<?php echo $calcRules['condition_max_hole_height']; ?>">
                                    <small>타공세로 <= 이 값이어야 시공 가능</small>
                                </div>
                            </div>
                        </div>

                        <?php foreach ($calcRules['rules'] as $i => $rule): ?>
                        <div class="calc-rule-box">
                            <h4><i class="fas fa-ruler"></i> <?php echo $rule['model']; ?> 모델 조건</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>결과 모델</label>
                                    <input type="text" name="rule_model[<?php echo $i; ?>]" value="<?php echo $rule['model']; ?>">
                                </div>
                                <div class="form-group">
                                    <label>하부장가로 최소값 (mm)</label>
                                    <input type="number" name="rule_min_cabinet[<?php echo $i; ?>]" value="<?php echo $rule['min_cabinet_width']; ?>">
                                    <small>하부장가로 > 이 값</small>
                                </div>
                                <div class="form-group">
                                    <label>타공가로 최대값 (mm)</label>
                                    <input type="number" name="rule_max_hole[<?php echo $i; ?>]" value="<?php echo $rule['max_hole_width']; ?>">
                                    <small>타공가로 <= 이 값</small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <div class="calc-rule-box">
                            <h4><i class="fas fa-ban"></i> 시공 불가 조건</h4>
                            <div class="form-group">
                                <label>시공 불가 조건 (텍스트)</label>
                                <input type="text" name="impossible_conditions" value="<?php echo sanitize($calcRules['impossible_conditions']); ?>">
                            </div>
                        </div>

                        <div class="calc-rule-box">
                            <h4><i class="fas fa-phone"></i> 상담 필요 조건</h4>
                            <div class="form-group">
                                <label>상담 필요 조건 (텍스트)</label>
                                <input type="text" name="consult_condition" value="<?php echo sanitize($calcRules['consult_condition']); ?>">
                            </div>
                        </div>

                        <!-- 계산식 미리보기 -->
                        <div class="calc-preview">
                            <h4><i class="fas fa-eye"></i> 계산식 미리보기</h4>
                            <div class="rule-line">
                                <span class="condition">전제 조건 (상판세로 >= <?php echo $calcRules['condition_min_top_height']; ?>, 타공세로 <= <?php echo $calcRules['condition_max_hole_height']; ?>)</span>
                            </div>
                            <?php foreach ($calcRules['rules'] as $rule): ?>
                            <div class="rule-line">
                                <span class="condition">하부장가로 > <?php echo $rule['min_cabinet_width']; ?>, 타공가로 <= <?php echo $rule['max_hole_width']; ?></span>
                                <span class="result"> = <?php echo $rule['model']; ?></span>
                            </div>
                            <?php endforeach; ?>
                            <div class="rule-line">
                                <span class="error"><?php echo sanitize($calcRules['impossible_conditions']); ?> = 시공 불가</span>
                            </div>
                            <div class="rule-line">
                                <span class="warning"><?php echo sanitize($calcRules['consult_condition']); ?> = 상담 필요</span>
                            </div>
                        </div>

                        <button type="submit" class="btn-save" style="margin-top: 20px;"><i class="fas fa-save"></i> 계산식 저장하기</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
