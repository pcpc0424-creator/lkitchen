<?php
/**
 * 러블리키친 관리자 - 후기 관리
 */
require_once 'config.php';
checkLogin();

$type = $_GET['type'] ?? 'food';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// 타입에 따른 설정
$typeConfig = [
    'food' => [
        'name' => '음식물처리기',
        'file' => 'food_reviews.json',
        'upload_path' => UPLOAD_PATH_FOOD,
        'image_url' => SITE_URL . '/pototo'
    ],
    'sink' => [
        'name' => '싱크볼',
        'file' => 'sink_reviews.json',
        'upload_path' => UPLOAD_PATH_SINK,
        'image_url' => SITE_URL . '/potopo'
    ]
];

$config = $typeConfig[$type] ?? $typeConfig['food'];
$reviews = readJsonData($config['file']);

// POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'save') {
        // 후기 저장
        $reviewData = [
            'id' => $_POST['id'] ?: uniqid(),
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'author' => $_POST['author'],
            'images' => json_decode($_POST['images'] ?? '[]', true),
            'created_at' => $_POST['created_at'] ?: date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // 기존 데이터 업데이트 또는 새로 추가
        $found = false;
        foreach ($reviews as $key => $review) {
            if ($review['id'] === $reviewData['id']) {
                $reviews[$key] = $reviewData;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $reviews[] = $reviewData;
        }

        writeJsonData($config['file'], $reviews);
        header('Location: reviews.php?type=' . $type . '&msg=saved');
        exit;
    }

    if ($postAction === 'delete') {
        $deleteId = $_POST['id'];
        $reviews = array_filter($reviews, function($r) use ($deleteId) {
            return $r['id'] !== $deleteId;
        });
        $reviews = array_values($reviews);
        writeJsonData($config['file'], $reviews);
        jsonResponse(true, '삭제되었습니다.');
    }
}

// 수정할 후기 데이터
$editReview = null;
if ($action === 'edit' && $id) {
    foreach ($reviews as $review) {
        if ($review['id'] === $id) {
            $editReview = $review;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['name']; ?> 후기 관리 | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css?v=2">
    <link rel="icon" type="image/png" href="http://115.68.223.124/lovelykitchen/수정/fhrh.png">
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
                    <li class="nav-item <?php echo $type === 'food' ? 'active' : ''; ?>">
                        <a href="reviews.php?type=food" class="nav-link">
                            <i class="fas fa-utensils"></i>
                            <span>음식물처리기 후기</span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo $type === 'sink' ? 'active' : ''; ?>">
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
                    <h1 class="page-title"><?php echo $config['name']; ?> 후기 관리</h1>
                </div>
                <div class="header-right">
                    <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn-site">
                        <i class="fas fa-external-link-alt"></i>
                        사이트 보기
                    </a>
                </div>
            </header>

            <div class="content-body">
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    후기가 저장되었습니다.
                </div>
                <?php endif; ?>

                <?php if ($action === 'list'): ?>
                <!-- 후기 목록 -->
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">후기 목록</h2>
                        <a href="reviews.php?type=<?php echo $type; ?>&action=add" class="btn-primary">
                            <i class="fas fa-plus"></i>
                            후기 추가
                        </a>
                    </div>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="80">이미지</th>
                                    <th>제목</th>
                                    <th width="100">작성자</th>
                                    <th width="150">작성일</th>
                                    <th width="120">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reviews)): ?>
                                <tr>
                                    <td colspan="5" class="empty-message">등록된 후기가 없습니다.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach (array_reverse($reviews) as $review): ?>
                                <tr data-id="<?php echo $review['id']; ?>">
                                    <td>
                                        <?php if (!empty($review['images'])): ?>
                                        <img src="<?php echo $config['image_url'] . '/' . $review['images'][0]; ?>" alt="" class="thumb-img">
                                        <?php else: ?>
                                        <div class="no-image"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo sanitize($review['title']); ?></td>
                                    <td><?php echo sanitize($review['author']); ?></td>
                                    <td><?php echo $review['created_at'] ?? '-'; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="reviews.php?type=<?php echo $type; ?>&action=edit&id=<?php echo $review['id']; ?>" class="btn-sm btn-edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn-sm btn-delete" onclick="deleteReview('<?php echo $review['id']; ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php else: ?>
                <!-- 후기 추가/수정 폼 -->
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title"><?php echo $editReview ? '후기 수정' : '후기 추가'; ?></h2>
                        <a href="reviews.php?type=<?php echo $type; ?>" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            목록으로
                        </a>
                    </div>

                    <form method="POST" action="" class="review-form" id="reviewForm">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?php echo $editReview['id'] ?? ''; ?>">
                        <input type="hidden" name="created_at" value="<?php echo $editReview['created_at'] ?? ''; ?>">
                        <input type="hidden" name="images" id="imagesInput" value="<?php echo htmlspecialchars(json_encode($editReview['images'] ?? [])); ?>">

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">제목 <span class="required">*</span></label>
                                <input type="text" name="title" class="form-input" value="<?php echo sanitize($editReview['title'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">작성자 <span class="required">*</span></label>
                                <input type="text" name="author" class="form-input" value="<?php echo sanitize($editReview['author'] ?? ''); ?>" placeholder="예: 김**" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">내용 <span class="required">*</span></label>
                                <textarea name="content" class="form-textarea" rows="6" required><?php echo sanitize($editReview['content'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">이미지</label>
                                <div class="image-upload-area" id="dropZone">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>이미지를 드래그하거나 클릭하여 업로드</p>
                                        <span>JPG, PNG, GIF, WEBP (최대 10MB)</span>
                                    </div>
                                    <input type="file" id="fileInput" multiple accept="image/*" style="display: none;">
                                </div>
                                <div class="image-preview-list" id="previewList">
                                    <?php if (!empty($editReview['images'])): ?>
                                    <?php foreach ($editReview['images'] as $img): ?>
                                    <div class="preview-item" data-filename="<?php echo $img; ?>">
                                        <img src="<?php echo $config['image_url'] . '/' . $img; ?>" alt="">
                                        <button type="button" class="remove-btn" onclick="removeImage(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary btn-lg">
                                <i class="fas fa-save"></i>
                                저장하기
                            </button>
                            <a href="reviews.php?type=<?php echo $type; ?>" class="btn-secondary btn-lg">
                                취소
                            </a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        const reviewType = '<?php echo $type; ?>';
        const uploadUrl = 'upload.php?type=' + reviewType;
    </script>
    <script src="js/admin.js"></script>
</body>
</html>
