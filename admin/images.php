<?php
/**
 * 러블리키친 관리자 - 이미지 관리
 */
require_once 'config.php';
checkLogin();

$type = $_GET['type'] ?? 'food';

// 이미지 목록 가져오기
function getImages($path, $webPath) {
    $images = [];
    if (is_dir($path)) {
        $pattern = $path . '/*.{jpg,jpeg,png,gif,webp}';
        foreach (glob($pattern, GLOB_BRACE) as $file) {
            $filename = basename($file);
            $images[] = [
                'filename' => $filename,
                'url' => SITE_URL . $webPath . '/' . $filename,
                'size' => filesize($file),
                'modified' => filemtime($file)
            ];
        }
        // 최신순 정렬
        usort($images, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });
    }
    return $images;
}

$foodImages = getImages(UPLOAD_PATH_FOOD, '/pototo');
$sinkImages = getImages(UPLOAD_PATH_SINK, '/potopo');
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>이미지 관리 | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png">
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
                        <a href="images.php" class="nav-link">
                            <i class="fas fa-images"></i>
                            <span>이미지 관리</span>
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
                    <h1 class="page-title">이미지 관리</h1>
                </div>
                <div class="header-right">
                    <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn-site">
                        <i class="fas fa-external-link-alt"></i>
                        사이트 보기
                    </a>
                </div>
            </header>

            <div class="content-body">
                <!-- 탭 메뉴 -->
                <div class="tabs">
                    <button class="tab-btn active" data-tab="food">
                        <i class="fas fa-utensils"></i>
                        음식물처리기 (<?php echo count($foodImages); ?>)
                    </button>
                    <button class="tab-btn" data-tab="sink">
                        <i class="fas fa-sink"></i>
                        싱크볼 (<?php echo count($sinkImages); ?>)
                    </button>
                </div>

                <!-- 음식물처리기 이미지 -->
                <div class="tab-content active" id="tab-food">
                    <div class="section">
                        <div class="section-header">
                            <h2 class="section-title">음식물처리기 이미지</h2>
                        </div>

                        <!-- 업로드 영역 -->
                        <div class="upload-zone" id="foodDropZone" data-type="food">
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <h3>이미지를 드래그하여 업로드</h3>
                            <p>또는 클릭하여 파일 선택</p>
                            <span class="upload-hint">JPG, PNG, GIF, WEBP (최대 10MB, 여러 장 선택 가능)</span>
                            <input type="file" class="file-input" multiple accept="image/*">
                        </div>

                        <!-- 업로드 진행 상태 -->
                        <div class="upload-progress" id="foodProgress" style="display: none;">
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                            <span class="progress-text">업로드 중...</span>
                        </div>

                        <!-- 이미지 그리드 -->
                        <div class="image-grid" id="foodImageGrid">
                            <?php foreach ($foodImages as $image): ?>
                            <div class="image-item" data-filename="<?php echo $image['filename']; ?>">
                                <div class="image-thumb">
                                    <img src="<?php echo $image['url']; ?>" alt="" loading="lazy">
                                </div>
                                <div class="image-info">
                                    <span class="image-name"><?php echo $image['filename']; ?></span>
                                    <span class="image-size"><?php echo round($image['size'] / 1024); ?>KB</span>
                                </div>
                                <div class="image-actions">
                                    <button class="btn-copy" onclick="copyImageUrl('<?php echo $image['url']; ?>')" title="URL 복사">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button class="btn-delete-img" onclick="deleteImage('<?php echo $image['filename']; ?>', 'food')" title="삭제">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- 싱크볼 이미지 -->
                <div class="tab-content" id="tab-sink">
                    <div class="section">
                        <div class="section-header">
                            <h2 class="section-title">싱크볼 이미지</h2>
                        </div>

                        <!-- 업로드 영역 -->
                        <div class="upload-zone" id="sinkDropZone" data-type="sink">
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <h3>이미지를 드래그하여 업로드</h3>
                            <p>또는 클릭하여 파일 선택</p>
                            <span class="upload-hint">JPG, PNG, GIF, WEBP (최대 10MB, 여러 장 선택 가능)</span>
                            <input type="file" class="file-input" multiple accept="image/*">
                        </div>

                        <!-- 업로드 진행 상태 -->
                        <div class="upload-progress" id="sinkProgress" style="display: none;">
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                            <span class="progress-text">업로드 중...</span>
                        </div>

                        <!-- 이미지 그리드 -->
                        <div class="image-grid" id="sinkImageGrid">
                            <?php foreach ($sinkImages as $image): ?>
                            <div class="image-item" data-filename="<?php echo $image['filename']; ?>">
                                <div class="image-thumb">
                                    <img src="<?php echo $image['url']; ?>" alt="" loading="lazy">
                                </div>
                                <div class="image-info">
                                    <span class="image-name"><?php echo $image['filename']; ?></span>
                                    <span class="image-size"><?php echo round($image['size'] / 1024); ?>KB</span>
                                </div>
                                <div class="image-actions">
                                    <button class="btn-copy" onclick="copyImageUrl('<?php echo $image['url']; ?>')" title="URL 복사">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button class="btn-delete-img" onclick="deleteImage('<?php echo $image['filename']; ?>', 'sink')" title="삭제">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- 토스트 메시지 -->
    <div class="toast" id="toast"></div>

    <script src="js/admin.js"></script>
</body>
</html>
