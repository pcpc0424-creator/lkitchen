<?php
/**
 * 러블리키친 특가페이지 관리
 */
require_once 'config.php';
checkLogin();

$specialData = readJsonData('special.json');
$message = '';
$messageType = '';

// POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_hero') {
        $specialData['hero']['badge'] = $_POST['hero_badge'] ?? '';
        $specialData['hero']['title'] = $_POST['hero_title'] ?? '';
        $specialData['hero']['subtitle'] = $_POST['hero_subtitle'] ?? '';

        if (writeJsonData('special.json', $specialData)) {
            $message = '히어로 섹션이 업데이트되었습니다.';
            $messageType = 'success';
        } else {
            $message = '저장 중 오류가 발생했습니다.';
            $messageType = 'error';
        }
    }

    if ($action === 'update_product') {
        $productId = (int)$_POST['product_id'];
        foreach ($specialData['products'] as &$product) {
            if ($product['id'] === $productId) {
                $product['model'] = $_POST['model'] ?? '';
                $product['name'] = $_POST['name'] ?? '';
                $product['name_en'] = $_POST['name_en'] ?? '';
                $product['image'] = $_POST['image'] ?? '';
                $product['tags'] = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
                $product['original_price'] = $_POST['original_price'] ?? '';
                $product['sale_price'] = $_POST['sale_price'] ?? '';
                $product['features'] = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
                $product['note'] = $_POST['note'] ?? '';
                $product['active'] = isset($_POST['active']);
                break;
            }
        }
        unset($product);

        if (writeJsonData('special.json', $specialData)) {
            $message = '제품 정보가 업데이트되었습니다.';
            $messageType = 'success';
        } else {
            $message = '저장 중 오류가 발생했습니다.';
            $messageType = 'error';
        }
    }

    if ($action === 'update_sinkbowl') {
        $sinkId = (int)$_POST['sink_id'];
        foreach ($specialData['sinkbowls'] as &$sink) {
            if ($sink['id'] === $sinkId) {
                $sink['model'] = $_POST['model'] ?? '';
                $sink['image'] = $_POST['image'] ?? '';
                $sink['price'] = $_POST['price'] ?? '';
                $sink['size'] = $_POST['size'] ?? '';
                $sink['features'] = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
                $sink['active'] = isset($_POST['active']);
                break;
            }
        }
        unset($sink);

        if (writeJsonData('special.json', $specialData)) {
            $message = '싱크볼 정보가 업데이트되었습니다.';
            $messageType = 'success';
        } else {
            $message = '저장 중 오류가 발생했습니다.';
            $messageType = 'error';
        }
    }

    if ($action === 'update_multitrap') {
        $specialData['multitrap']['badge'] = $_POST['multitrap_badge'] ?? '';
        $specialData['multitrap']['title'] = $_POST['multitrap_title'] ?? '';
        $specialData['multitrap']['description'] = $_POST['multitrap_description'] ?? '';
        $specialData['multitrap']['image'] = $_POST['multitrap_image'] ?? '';
        $specialData['multitrap']['features'] = array_filter(array_map('trim', explode("\n", $_POST['multitrap_features'] ?? '')));

        if (writeJsonData('special.json', $specialData)) {
            $message = '멀티트랩 섹션이 업데이트되었습니다.';
            $messageType = 'success';
        } else {
            $message = '저장 중 오류가 발생했습니다.';
            $messageType = 'error';
        }
    }

    if ($action === 'update_cta') {
        $specialData['cta']['title'] = $_POST['cta_title'] ?? '';
        $specialData['cta']['subtitle'] = $_POST['cta_subtitle'] ?? '';
        $specialData['cta']['phone'] = $_POST['cta_phone'] ?? '';

        if (writeJsonData('special.json', $specialData)) {
            $message = 'CTA 섹션이 업데이트되었습니다.';
            $messageType = 'success';
        } else {
            $message = '저장 중 오류가 발생했습니다.';
            $messageType = 'error';
        }
    }

    // 데이터 다시 로드
    $specialData = readJsonData('special.json');
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
    <title>특가페이지 관리 | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png">
    <style>
        .special-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .special-tab {
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
        .special-tab:hover {
            background: #e2e8f0;
        }
        .special-tab.active {
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
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        .product-preview {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .product-preview img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            background: #fff;
            border-radius: 8px;
            padding: 5px;
        }
        .product-preview-info h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .product-preview-info p {
            font-size: 0.85rem;
            color: #64748b;
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
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background: #dcfce7;
            color: #16a34a;
        }
        .alert-error {
            background: #fee2e2;
            color: #dc2626;
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
                    <li class="nav-item active">
                        <a href="special.php" class="nav-link">
                            <i class="fas fa-tag"></i>
                            <span>특가페이지 관리</span>
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
                    <h1 class="page-title">특가페이지 관리</h1>
                </div>
                <div class="header-right">
                    <a href="<?php echo SITE_URL; ?>/특가페이지/" target="_blank" class="btn-site">
                        <i class="fas fa-external-link-alt"></i>
                        특가페이지 보기
                    </a>
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo sanitize($_SESSION['admin_username']); ?></span>
                    </div>
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
                <div class="special-tabs">
                    <button class="special-tab active" data-tab="hero">히어로 섹션</button>
                    <button class="special-tab" data-tab="products">음식물처리기</button>
                    <button class="special-tab" data-tab="sinkbowls">싱크볼</button>
                    <button class="special-tab" data-tab="multitrap">멀티트랩</button>
                    <button class="special-tab" data-tab="cta">CTA 섹션</button>
                </div>

                <!-- Hero Section -->
                <div id="hero" class="tab-content active">
                    <form method="POST" class="form-card">
                        <input type="hidden" name="action" value="update_hero">
                        <div class="form-card-header">
                            <h3 class="form-card-title"><i class="fas fa-star"></i> 히어로 섹션</h3>
                        </div>
                        <div class="form-group">
                            <label>배지 텍스트</label>
                            <input type="text" name="hero_badge" value="<?php echo sanitize($specialData['hero']['badge'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>제목 (HTML 허용)</label>
                            <textarea name="hero_title" rows="3"><?php echo htmlspecialchars($specialData['hero']['title'] ?? ''); ?></textarea>
                            <small>예: &lt;span class="highlight"&gt;더 특별한 가격&lt;/span&gt;으로 만나는&lt;br&gt;러블리 키친</small>
                        </div>
                        <div class="form-group">
                            <label>부제목</label>
                            <input type="text" name="hero_subtitle" value="<?php echo sanitize($specialData['hero']['subtitle'] ?? ''); ?>">
                        </div>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> 저장하기</button>
                    </form>
                </div>

                <!-- Products Section -->
                <div id="products" class="tab-content">
                    <?php foreach ($specialData['products'] as $product): ?>
                    <form method="POST" class="form-card">
                        <input type="hidden" name="action" value="update_product">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                        <div class="form-card-header">
                            <h3 class="form-card-title">
                                <i class="fas fa-box"></i>
                                <?php echo sanitize($product['model']); ?> - <?php echo sanitize($product['name']); ?>
                            </h3>
                            <?php if ($product['active']): ?>
                            <span class="status-active">활성화</span>
                            <?php else: ?>
                            <span class="status-inactive">비활성화</span>
                            <?php endif; ?>
                        </div>

                        <div class="product-preview">
                            <img src="<?php echo sanitize($product['image']); ?>" alt="<?php echo sanitize($product['model']); ?>">
                            <div class="product-preview-info">
                                <h4><?php echo sanitize($product['model']); ?> <?php echo sanitize($product['name']); ?></h4>
                                <p>특별행사가: <?php echo sanitize($product['sale_price']); ?>원</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>모델명</label>
                                <input type="text" name="model" value="<?php echo sanitize($product['model']); ?>">
                            </div>
                            <div class="form-group">
                                <label>제품명 (한글)</label>
                                <input type="text" name="name" value="<?php echo sanitize($product['name']); ?>">
                            </div>
                            <div class="form-group">
                                <label>제품명 (영문)</label>
                                <input type="text" name="name_en" value="<?php echo sanitize($product['name_en']); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>이미지 URL</label>
                            <input type="url" name="image" value="<?php echo sanitize($product['image']); ?>">
                        </div>

                        <div class="form-group">
                            <label>태그 (쉼표로 구분)</label>
                            <input type="text" name="tags" value="<?php echo sanitize(implode(', ', $product['tags'])); ?>">
                            <small>예: #프리미엄, #베스트셀러, #추천</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>소비자가 (원)</label>
                                <input type="text" name="original_price" value="<?php echo sanitize($product['original_price']); ?>">
                            </div>
                            <div class="form-group">
                                <label>특별행사가 (원)</label>
                                <input type="text" name="sale_price" value="<?php echo sanitize($product['sale_price']); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>특징 (줄바꿈으로 구분)</label>
                            <textarea name="features" rows="5"><?php echo sanitize(implode("\n", $product['features'])); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>참고사항</label>
                            <input type="text" name="note" value="<?php echo sanitize($product['note'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" name="active" id="active_<?php echo $product['id']; ?>" <?php echo $product['active'] ? 'checked' : ''; ?>>
                                <label for="active_<?php echo $product['id']; ?>">특가페이지에 표시</label>
                            </div>
                        </div>

                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> 저장하기</button>
                    </form>
                    <?php endforeach; ?>
                </div>

                <!-- Sinkbowls Section -->
                <div id="sinkbowls" class="tab-content">
                    <?php foreach ($specialData['sinkbowls'] as $sink): ?>
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
                                <label>가격 (원)</label>
                                <input type="text" name="price" value="<?php echo sanitize($sink['price']); ?>">
                            </div>
                            <div class="form-group">
                                <label>크기</label>
                                <input type="text" name="size" value="<?php echo sanitize($sink['size']); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>이미지 URL</label>
                            <input type="url" name="image" value="<?php echo sanitize($sink['image']); ?>">
                        </div>

                        <div class="form-group">
                            <label>특징 (줄바꿈으로 구분)</label>
                            <textarea name="features" rows="5"><?php echo sanitize(implode("\n", $sink['features'])); ?></textarea>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" name="active" id="sink_active_<?php echo $sink['id']; ?>" <?php echo $sink['active'] ? 'checked' : ''; ?>>
                                <label for="sink_active_<?php echo $sink['id']; ?>">특가페이지에 표시</label>
                            </div>
                        </div>

                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> 저장하기</button>
                    </form>
                    <?php endforeach; ?>
                </div>

                <!-- Multitrap Section -->
                <div id="multitrap" class="tab-content">
                    <form method="POST" class="form-card">
                        <input type="hidden" name="action" value="update_multitrap">
                        <div class="form-card-header">
                            <h3 class="form-card-title"><i class="fas fa-cogs"></i> 멀티트랩 섹션</h3>
                        </div>
                        <div class="form-group">
                            <label>배지 텍스트</label>
                            <input type="text" name="multitrap_badge" value="<?php echo sanitize($specialData['multitrap']['badge'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>제목 (HTML 허용)</label>
                            <textarea name="multitrap_title" rows="2"><?php echo htmlspecialchars($specialData['multitrap']['title'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>설명</label>
                            <textarea name="multitrap_description" rows="3"><?php echo sanitize($specialData['multitrap']['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>이미지 URL</label>
                            <input type="url" name="multitrap_image" value="<?php echo sanitize($specialData['multitrap']['image'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>특징 (줄바꿈으로 구분)</label>
                            <textarea name="multitrap_features" rows="4"><?php echo sanitize(implode("\n", $specialData['multitrap']['features'] ?? [])); ?></textarea>
                        </div>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> 저장하기</button>
                    </form>
                </div>

                <!-- CTA Section -->
                <div id="cta" class="tab-content">
                    <form method="POST" class="form-card">
                        <input type="hidden" name="action" value="update_cta">
                        <div class="form-card-header">
                            <h3 class="form-card-title"><i class="fas fa-bullhorn"></i> CTA 섹션</h3>
                        </div>
                        <div class="form-group">
                            <label>제목</label>
                            <input type="text" name="cta_title" value="<?php echo sanitize($specialData['cta']['title'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>부제목</label>
                            <input type="text" name="cta_subtitle" value="<?php echo sanitize($specialData['cta']['subtitle'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>전화번호</label>
                            <input type="text" name="cta_phone" value="<?php echo sanitize($specialData['cta']['phone'] ?? ''); ?>">
                        </div>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> 저장하기</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
    <script>
        // Tab functionality
        document.querySelectorAll('.special-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.special-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });
    </script>
</body>
</html>
