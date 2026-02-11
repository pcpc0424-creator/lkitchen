<?php
/**
 * 러블리키친 갤러리 관리
 */
require_once 'config.php';
checkLogin();

$type = $_POST['type'] ?? $_GET['type'] ?? 'food';
// type 값 정규화 (sink 또는 food만 허용)
$type = ($type === 'sink') ? 'sink' : 'food';
$action = $_POST['action'] ?? $_GET['action'] ?? 'list';

// 디버깅: POST 데이터 로그
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Gallery POST - type: " . ($_POST['type'] ?? 'NOT SET') . ", action: " . ($_POST['action'] ?? 'NOT SET') . ", final type: " . $type);
}

// 갤러리 데이터 파일
$galleryFile = ($type === 'sink') ? 'sink_gallery.json' : 'food_gallery.json';
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

            header('Location: gallery.php?type=' . urlencode($type) . '&success=1');
            exit;
        }
    }
}

// 이미지 파일 업로드 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'upload') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'CSRF 토큰이 유효하지 않습니다.';
    } elseif (!isset($_FILES['gallery_files']) || empty($_FILES['gallery_files']['name'][0])) {
        $error = '업로드할 파일을 선택해주세요.';
    } else {
        $files = $_FILES['gallery_files'];
        $uploadPath = ($type === 'sink') ? UPLOAD_PATH_SINK : UPLOAD_PATH_FOOD;
        $webPath = ($type === 'sink') ? '/potopo' : '/pototo';
        $description = trim($_POST['upload_description'] ?? '');

        // 디렉토리 확인 및 생성
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fileCount = count($files['name']);
        $uploaded = 0;
        $errors = [];

        for ($i = 0; $i < $fileCount; $i++) {
            $name = $files['name'][$i];
            $tmpName = $files['tmp_name'][$i];
            $fileError = $files['error'][$i];
            $size = $files['size'][$i];

            if ($fileError !== UPLOAD_ERR_OK) {
                $errors[] = "{$name}: 업로드 실패";
                continue;
            }

            if ($size > MAX_FILE_SIZE) {
                $errors[] = "{$name}: 파일 크기가 10MB를 초과합니다.";
                continue;
            }

            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($extension, ALLOWED_EXTENSIONS)) {
                $errors[] = "{$name}: 허용되지 않는 파일 형식입니다.";
                continue;
            }

            $imageInfo = getimagesize($tmpName);
            if ($imageInfo === false) {
                $errors[] = "{$name}: 유효하지 않은 이미지 파일입니다.";
                continue;
            }

            $newFilename = generateGalleryFilename($name);
            $destination = $uploadPath . '/' . $newFilename;

            if (move_uploaded_file($tmpName, $destination)) {
                // 자동 리사이징 (최대 1600px, 비율 유지)
                resizeImage($destination, 1600, 85);

                $newImage = [
                    'id' => uniqid(),
                    'url' => SITE_URL . $webPath . '/' . $newFilename,
                    'description' => $description ?: $name,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                array_unshift($galleryData, $newImage);
                $uploaded++;
            } else {
                $errors[] = "{$name}: 파일 저장에 실패했습니다.";
            }
        }

        if ($uploaded > 0) {
            writeJsonData($galleryFile, $galleryData);
        }

        if (!empty($errors)) {
            $error = implode('<br>', $errors);
        }

        if ($uploaded > 0) {
            $successMsg = $uploaded . '개 파일이 업로드되었습니다.';
            if (!empty($errors)) {
                $successMsg .= ' (일부 오류 발생)';
            }
            header('Location: gallery.php?type=' . urlencode($type) . '&uploaded=' . $uploaded);
            exit;
        } else {
            // 업로드 실패해도 올바른 탭 유지
            header('Location: gallery.php?type=' . urlencode($type) . '&error=upload');
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

    header('Location: gallery.php?type=' . urlencode($type) . '&deleted=1');
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
    <link rel="stylesheet" href="css/admin.css?v=2">
    <link rel="icon" type="image/png" href="/수정/fhrh.png">
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
        .drop-zone {
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        .drop-zone:hover, .drop-zone.drag-over {
            border-color: #051535;
            background: #e8ecf4;
        }
        .file-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }
        .file-preview-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .file-preview-item img {
            width: 100%;
            height: 90px;
            object-fit: cover;
        }
        .file-preview-item .file-name {
            padding: 4px 8px;
            font-size: 0.7rem;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .file-preview-item .remove-file {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(255,0,0,0.8);
            color: #fff;
            border: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">
                    <img src="/수정/fhrh.png" alt="러블리키친">
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

                <?php if (isset($_GET['uploaded'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo (int)$_GET['uploaded']; ?>개 이미지가 업로드되었습니다.
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
                    <form method="POST" action="gallery.php">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="type" value="<?php echo $type; ?>">
                        <input type="hidden" name="action" value="add">
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

                <!-- 파일 업로드 폼 -->
                <div class="add-form">
                    <h3 style="margin-bottom: 16px;"><i class="fas fa-cloud-upload-alt"></i> 이미지 파일 업로드</h3>
                    <form method="POST" action="gallery.php" enctype="multipart/form-data" id="uploadForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="type" value="<?php echo $type; ?>">
                        <input type="hidden" name="action" value="upload">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="upload_description">설명 (선택, 모든 이미지에 적용)</label>
                                <input type="text" id="upload_description" name="upload_description" placeholder="이미지 설명">
                            </div>
                        </div>
                        <div class="drop-zone" id="dropZone">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #adb5bd; margin-bottom: 12px;"></i>
                            <p style="color: #666; margin-bottom: 8px;">이미지를 여기에 드래그하거나 클릭하여 선택</p>
                            <p style="color: #999; font-size: 0.8rem;">JPG, PNG, GIF, WebP / 최대 10MB / 다중 선택 가능</p>
                            <input type="file" name="gallery_files[]" id="galleryFiles" multiple accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">
                        </div>
                        <div id="filePreview" class="file-preview" style="display: none;"></div>
                        <button type="submit" class="btn-primary" id="uploadBtn" style="margin-top: 16px;" disabled>
                            <i class="fas fa-upload"></i> 업로드
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
                            <form method="POST" action="gallery.php" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $image['id']; ?>">
                                <input type="hidden" name="type" value="<?php echo $type; ?>">
                                <input type="hidden" name="action" value="delete">
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
    <script>
    (function() {
        // URL에서 현재 type 가져오기
        const urlParams = new URLSearchParams(window.location.search);
        const currentType = urlParams.get('type') || 'food';

        // 모든 폼의 type hidden input 업데이트
        document.querySelectorAll('input[name="type"]').forEach(input => {
            input.value = currentType;
        });

        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('galleryFiles');
        const filePreview = document.getElementById('filePreview');
        const uploadBtn = document.getElementById('uploadBtn');
        let selectedFiles = [];

        // 드롭존 클릭 → 파일 선택
        dropZone.addEventListener('click', () => fileInput.click());

        // 드래그 이벤트
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('drag-over');
        });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
            if (files.length > 0) {
                addFiles(files);
            }
        });

        // 파일 선택
        fileInput.addEventListener('change', () => {
            addFiles(Array.from(fileInput.files));
        });

        function addFiles(newFiles) {
            selectedFiles = selectedFiles.concat(newFiles);
            updatePreview();
            updateFileInput();
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updatePreview();
            updateFileInput();
        }

        function updateFileInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            fileInput.files = dt.files;
            uploadBtn.disabled = selectedFiles.length === 0;
        }

        function updatePreview() {
            if (selectedFiles.length === 0) {
                filePreview.style.display = 'none';
                filePreview.innerHTML = '';
                return;
            }
            filePreview.style.display = 'grid';
            filePreview.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const item = document.createElement('div');
                item.className = 'file-preview-item';
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                const name = document.createElement('div');
                name.className = 'file-name';
                name.textContent = file.name;
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-file';
                removeBtn.innerHTML = '&times;';
                removeBtn.onclick = () => removeFile(index);
                item.appendChild(img);
                item.appendChild(name);
                item.appendChild(removeBtn);
                filePreview.appendChild(item);
            });
        }
    })();
    </script>
</body>
</html>
