<?php
/**
 * 러블리키친 갤러리 관리
 */
require_once 'config.php';
checkLogin();

$type = $_GET['type'] ?? 'food';
$action = $_GET['action'] ?? 'list';

// 갤러리 데이터 파일
$galleryFile = $type === 'food' ? 'food_gallery.json' : 'sink_gallery.json';
$galleryData = readJsonData($galleryFile);

// 이미지 추가 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'CSRF 토큰이 유효하지 않습니다.';
    } else {
        $imageUrl = trim($_POST['image_url'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($imageUrl)) {
            $error = '이미지 URL을 입력해주세요.';
        } else {
            $newImage = [
                'id' => uniqid(),
                'url' => $imageUrl,
                'description' => $description,
                'created_at' => date('Y-m-d H:i:s')
            ];

            array_unshift($galleryData, $newImage);
            writeJsonData($galleryFile, $galleryData);

            header('Location: gallery.php?type=' . $type . '&success=1');
            exit;
        }
    }
}

// 이미지 삭제 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    $id = $_POST['id'] ?? '';
    $galleryData = array_filter($galleryData, function($img) use ($id) {
        return $img['id'] !== $id;
    });
    $galleryData = array_values($galleryData);
    writeJsonData($galleryFile, $galleryData);

    header('Location: gallery.php?type=' . $type . '&deleted=1');
    exit;
}

// 새 문의 수 계산 (사이드바용)
$inquiries = readJsonData('inquiries.json');
$newInquiries = array_filter($inquiries, function($inquiry) {
    return $inquiry['status'] === 'new';
});

$pageTitle = $type === 'food' ? '음식물처리기 갤러리' : '싱크볼 갤러리';
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png">
    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .gallery-item {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
        }
        .gallery-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .gallery-item .item-info {
            padding: 12px;
        }
        .gallery-item .item-desc {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .gallery-item .item-date {
            font-size: 0.75rem;
            color: #999;
        }
        .gallery-item .delete-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(255,0,0,0.8);
            color: #fff;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .gallery-item:hover .delete-btn {
            opacity: 1;
        }
        .add-form {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }
        .form-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .tab-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            color: #333;
        }
        .tab-btn.active {
            background: #051535;
            color: #fff;
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
                    <li class="nav-item active">
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
                    <h1 class="page-title"><?php echo $pageTitle; ?> 관리</h1>
                </div>
                <div class="header-right">
                    <a href="<?php echo SITE_URL; ?>/사진갤러리/" target="_blank" class="btn-site">
                        <i class="fas fa-external-link-alt"></i>
                        갤러리 보기
                    </a>
                </div>
            </header>

            <div class="content-body">
                <!-- 탭 버튼 -->
                <div class="tab-buttons">
                    <a href="gallery.php?type=food" class="tab-btn <?php echo $type === 'food' ? 'active' : ''; ?>">
                        <i class="fas fa-utensils"></i> 음식물처리기 갤러리
                    </a>
                    <a href="gallery.php?type=sink" class="tab-btn <?php echo $type === 'sink' ? 'active' : ''; ?>">
                        <i class="fas fa-sink"></i> 싱크볼 갤러리
                    </a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> 이미지가 추가되었습니다.
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> 이미지가 삭제되었습니다.
                </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <!-- 이미지 추가 폼 -->
                <div class="add-form">
                    <h3 style="margin-bottom: 16px;"><i class="fas fa-plus-circle"></i> 새 이미지 추가</h3>
                    <form method="POST" action="gallery.php?type=<?php echo $type; ?>&action=add">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="image_url">이미지 URL *</label>
                                <input type="url" id="image_url" name="image_url" required placeholder="https://example.com/image.jpg">
                            </div>
                            <div class="form-group">
                                <label for="description">설명 (선택)</label>
                                <input type="text" id="description" name="description" placeholder="이미지 설명">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-plus"></i> 이미지 추가
                        </button>
                    </form>
                </div>

                <!-- 갤러리 그리드 -->
                <div class="section">
                    <h2 class="section-title">등록된 이미지 (<?php echo count($galleryData); ?>개)</h2>

                    <?php if (empty($galleryData)): ?>
                    <div class="empty-state">
                        <i class="fas fa-images"></i>
                        <p>등록된 이미지가 없습니다.</p>
                    </div>
                    <?php else: ?>
                    <div class="gallery-grid">
                        <?php foreach ($galleryData as $image): ?>
                        <div class="gallery-item">
                            <img src="<?php echo sanitize($image['url']); ?>" alt="<?php echo sanitize($image['description'] ?? ''); ?>" loading="lazy">
                            <div class="item-info">
                                <div class="item-desc"><?php echo sanitize($image['description'] ?? '설명 없음'); ?></div>
                                <div class="item-date"><?php echo $image['created_at'] ?? '-'; ?></div>
                            </div>
                            <form method="POST" action="gallery.php?type=<?php echo $type; ?>&action=delete" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $image['id']; ?>">
                                <button type="submit" class="delete-btn" onclick="return confirm('정말 삭제하시겠습니까?')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
