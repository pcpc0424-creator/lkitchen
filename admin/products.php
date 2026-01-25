<?php
/**
 * 러블리키친 제품 관리
 */
require_once 'config.php';
checkLogin();

// 제품 데이터 로드
$productsFile = 'products.json';
$productsData = readJsonData($productsFile);
$products = $productsData['products'] ?? [];

$message = '';
$messageType = '';

// POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 제품 추가
    if ($action === 'add_product') {
        $maxId = 0;
        foreach ($productsData['products'] as $p) {
            if ($p['id'] > $maxId) $maxId = $p['id'];
        }

        $newProduct = [
            'id' => $maxId + 1,
            'model' => trim($_POST['model'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'name_en' => trim($_POST['name_en'] ?? ''),
            'category' => trim($_POST['category'] ?? 'entry'),
            'badge' => trim($_POST['badge'] ?? 'NEW'),
            'badge_type' => trim($_POST['badge_type'] ?? 'entry'),
            'image' => trim($_POST['image'] ?? ''),
            'price' => trim($_POST['price'] ?? ''),
            'hp' => trim($_POST['hp'] ?? ''),
            'spec_icon' => trim($_POST['spec_icon'] ?? 'fa-bolt'),
            'spec_text' => trim($_POST['spec_text'] ?? ''),
            'features' => array_filter(array_map('trim', explode("\n", $_POST['features'] ?? ''))),
            'is_best' => isset($_POST['is_best']),
            'active' => isset($_POST['active'])
        ];

        $productsData['products'][] = $newProduct;

        if (writeJsonData($productsFile, $productsData)) {
            $message = '새 제품이 추가되었습니다.';
            $messageType = 'success';
            $products = $productsData['products'];
        } else {
            $message = '저장 중 오류가 발생했습니다.';
            $messageType = 'error';
        }
    }

    // 제품 수정
    if ($action === 'update_product') {
        $productId = (int)$_POST['product_id'];
        foreach ($productsData['products'] as &$product) {
            if ($product['id'] === $productId) {
                $product['model'] = trim($_POST['model'] ?? '');
                $product['name'] = trim($_POST['name'] ?? '');
                $product['name_en'] = trim($_POST['name_en'] ?? '');
                $product['category'] = trim($_POST['category'] ?? '');
                $product['badge'] = trim($_POST['badge'] ?? '');
                $product['badge_type'] = trim($_POST['badge_type'] ?? '');
                $product['image'] = trim($_POST['image'] ?? '');
                $product['price'] = trim($_POST['price'] ?? '');
                $product['hp'] = trim($_POST['hp'] ?? '');
                $product['spec_icon'] = trim($_POST['spec_icon'] ?? '');
                $product['spec_text'] = trim($_POST['spec_text'] ?? '');
                $product['features'] = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
                $product['is_best'] = isset($_POST['is_best']);
                $product['active'] = isset($_POST['active']);
                break;
            }
        }
        unset($product);

        if (writeJsonData($productsFile, $productsData)) {
            $message = '제품 정보가 저장되었습니다.';
            $messageType = 'success';
            $products = $productsData['products'];
        } else {
            $message = '저장 중 오류가 발생했습니다.';
            $messageType = 'error';
        }
    }

    // 제품 삭제
    if ($action === 'delete_product') {
        $productId = (int)$_POST['product_id'];
        $productsData['products'] = array_values(array_filter($productsData['products'], function($p) use ($productId) {
            return $p['id'] !== $productId;
        }));

        if (writeJsonData($productsFile, $productsData)) {
            $message = '제품이 삭제되었습니다.';
            $messageType = 'success';
            $products = $productsData['products'];
        } else {
            $message = '삭제 중 오류가 발생했습니다.';
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
    <title>제품 관리 | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css?v=2">
    <link rel="icon" type="image/png" href="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png">
    <style>
        .form-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .form-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
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
        .product-preview {
            display: flex;
            align-items: center;
            gap: 20px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        .product-preview img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            background: #fff;
            border-radius: 8px;
            padding: 10px;
        }
        .product-preview-info h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .product-preview-info p {
            font-size: 0.9rem;
            color: #64748b;
        }
        .product-preview-info .price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #3b82f6;
            margin-top: 8px;
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.3s ease;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        .form-group small {
            display: block;
            margin-top: 5px;
            font-size: 0.8rem;
            color: #94a3b8;
        }
        .checkbox-row {
            display: flex;
            gap: 30px;
            margin-bottom: 20px;
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
        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 10px;
        }
        .btn-delete:hover {
            background: #dc2626;
            color: #fff;
        }
        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: #fff;
            border: none;
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(34, 197, 94, 0.3);
        }
        .add-form {
            display: none;
            background: #f0fdf4;
            border: 2px dashed #22c55e;
        }
        .add-form.show {
            display: block;
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
        .badge-best {
            display: inline-block;
            padding: 4px 10px;
            background: #fef3c7;
            color: #b45309;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 10px;
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
                    <li class="nav-item active">
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
                    <li class="nav-item">
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
                    <h1 class="page-title">제품 관리</h1>
                </div>
                <div class="header-right">
                    <a href="<?php echo SITE_URL; ?>/products.html" target="_blank" class="btn-site">
                        <i class="fas fa-external-link-alt"></i>
                        제품 페이지 보기
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

                <!-- 제품 추가 버튼 -->
                <button type="button" class="btn-add" onclick="toggleAddForm()">
                    <i class="fas fa-plus"></i> 새 제품 추가
                </button>

                <!-- 제품 추가 폼 -->
                <form method="POST" class="form-card add-form" id="addProductForm">
                    <input type="hidden" name="action" value="add_product">
                    <div class="form-card-header">
                        <h3 class="form-card-title">
                            <i class="fas fa-plus-circle"></i> 새 제품 추가
                        </h3>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>모델명 *</label>
                            <input type="text" name="model" required placeholder="LK-XXX">
                        </div>
                        <div class="form-group">
                            <label>제품명 (한글) *</label>
                            <input type="text" name="name" required placeholder="제품명">
                        </div>
                        <div class="form-group">
                            <label>제품명 (영문)</label>
                            <input type="text" name="name_en" placeholder="PRODUCT NAME">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>카테고리</label>
                            <select name="category">
                                <option value="entry">엔트리</option>
                                <option value="premium">프리미엄</option>
                                <option value="flagship">플래그십</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>배지 텍스트</label>
                            <input type="text" name="badge" value="NEW" placeholder="NEW, BEST 등">
                        </div>
                        <div class="form-group">
                            <label>배지 타입</label>
                            <select name="badge_type">
                                <option value="entry">entry (파란색)</option>
                                <option value="best">best (노란색)</option>
                                <option value="premium">premium (보라색)</option>
                                <option value="flagship">flagship (빨간색)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>이미지 URL</label>
                        <input type="url" name="image" placeholder="https://...">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>가격 (원) *</label>
                            <input type="text" name="price" required placeholder="990,000">
                        </div>
                        <div class="form-group">
                            <label>마력</label>
                            <input type="text" name="hp" placeholder="1.0 HP">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>스펙 아이콘</label>
                            <input type="text" name="spec_icon" value="fa-bolt" placeholder="fa-bolt">
                        </div>
                        <div class="form-group">
                            <label>스펙 텍스트</label>
                            <input type="text" name="spec_text" placeholder="5년 무상 A/S">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>특징 (줄바꿈으로 구분)</label>
                        <textarea name="features" rows="4" placeholder="특징 1&#10;특징 2&#10;특징 3"></textarea>
                    </div>

                    <div class="checkbox-row">
                        <div class="checkbox-group">
                            <input type="checkbox" name="is_best" id="new_is_best">
                            <label for="new_is_best">인기 1위 표시</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" name="active" id="new_active" checked>
                            <label for="new_active">제품 페이지에 표시</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-save"><i class="fas fa-plus"></i> 제품 추가</button>
                    <button type="button" class="btn-delete" onclick="toggleAddForm()">취소</button>
                </form>

                <?php foreach ($products as $product): ?>
                <form method="POST" class="form-card">
                    <input type="hidden" name="action" value="update_product">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                    <div class="form-card-header">
                        <h3 class="form-card-title">
                            <i class="fas fa-box"></i>
                            <?php echo sanitize($product['model']); ?> <?php echo sanitize($product['name']); ?>
                            <?php if ($product['is_best']): ?>
                            <span class="badge-best"><i class="fas fa-crown"></i> 인기 1위</span>
                            <?php endif; ?>
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
                            <p><?php echo sanitize($product['name_en']); ?> | <?php echo sanitize($product['hp']); ?></p>
                            <p class="price"><?php echo sanitize($product['price']); ?>원</p>
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

                    <div class="form-row">
                        <div class="form-group">
                            <label>카테고리</label>
                            <select name="category">
                                <option value="entry" <?php echo $product['category'] === 'entry' ? 'selected' : ''; ?>>엔트리</option>
                                <option value="premium" <?php echo $product['category'] === 'premium' ? 'selected' : ''; ?>>프리미엄</option>
                                <option value="flagship" <?php echo $product['category'] === 'flagship' ? 'selected' : ''; ?>>플래그십</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>배지 텍스트</label>
                            <input type="text" name="badge" value="<?php echo sanitize($product['badge']); ?>" placeholder="ENTRY, BEST, PREMIUM 등">
                        </div>
                        <div class="form-group">
                            <label>배지 타입</label>
                            <select name="badge_type">
                                <option value="entry" <?php echo $product['badge_type'] === 'entry' ? 'selected' : ''; ?>>entry (파란색)</option>
                                <option value="best" <?php echo $product['badge_type'] === 'best' ? 'selected' : ''; ?>>best (노란색)</option>
                                <option value="premium" <?php echo $product['badge_type'] === 'premium' ? 'selected' : ''; ?>>premium (보라색)</option>
                                <option value="flagship" <?php echo $product['badge_type'] === 'flagship' ? 'selected' : ''; ?>>flagship (빨간색)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>이미지 URL</label>
                        <input type="url" name="image" value="<?php echo sanitize($product['image']); ?>">
                        <small>이미지 업로드에서 업로드 후 URL을 복사하여 붙여넣으세요</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>가격 (원)</label>
                            <input type="text" name="price" value="<?php echo sanitize($product['price']); ?>" placeholder="990,000">
                        </div>
                        <div class="form-group">
                            <label>마력</label>
                            <input type="text" name="hp" value="<?php echo sanitize($product['hp']); ?>" placeholder="1.0 HP">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>스펙 아이콘</label>
                            <input type="text" name="spec_icon" value="<?php echo sanitize($product['spec_icon']); ?>" placeholder="fa-shield-alt">
                            <small>FontAwesome 아이콘 클래스 (fa- 포함)</small>
                        </div>
                        <div class="form-group">
                            <label>스펙 텍스트</label>
                            <input type="text" name="spec_text" value="<?php echo sanitize($product['spec_text']); ?>" placeholder="5년 무상 A/S">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>특징 (줄바꿈으로 구분)</label>
                        <textarea name="features" rows="5"><?php echo sanitize(implode("\n", $product['features'] ?? [])); ?></textarea>
                    </div>

                    <div class="checkbox-row">
                        <div class="checkbox-group">
                            <input type="checkbox" name="is_best" id="best_<?php echo $product['id']; ?>" <?php echo $product['is_best'] ? 'checked' : ''; ?>>
                            <label for="best_<?php echo $product['id']; ?>">인기 1위 표시</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" name="active" id="active_<?php echo $product['id']; ?>" <?php echo $product['active'] ? 'checked' : ''; ?>>
                            <label for="active_<?php echo $product['id']; ?>">제품 페이지에 표시</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> 저장하기</button>
                    <button type="button" class="btn-delete" onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo sanitize($product['model']); ?>')">
                        <i class="fas fa-trash"></i> 삭제
                    </button>
                </form>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <!-- 삭제 확인용 숨김 폼 -->
    <form id="deleteForm" method="POST" style="display:none;">
        <input type="hidden" name="action" value="delete_product">
        <input type="hidden" name="product_id" id="deleteProductId">
    </form>

    <script src="js/admin.js"></script>
    <script>
        function toggleAddForm() {
            const form = document.getElementById('addProductForm');
            form.classList.toggle('show');
            if (form.classList.contains('show')) {
                form.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function deleteProduct(id, model) {
            if (confirm(model + ' 제품을 정말 삭제하시겠습니까?\n\n삭제된 제품은 복구할 수 없습니다.')) {
                document.getElementById('deleteProductId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
