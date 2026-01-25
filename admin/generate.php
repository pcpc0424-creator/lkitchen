<?php
/**
 * 러블리키친 관리자 - 후기 페이지 생성
 */
require_once 'config.php';
checkLogin();

$message = '';
$messageType = '';

// 페이지 생성 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $generateType = $_POST['type'] ?? 'all';

    try {
        if ($generateType === 'food' || $generateType === 'all') {
            generateReviewPages('food');
        }
        if ($generateType === 'sink' || $generateType === 'all') {
            generateReviewPages('sink');
        }
        $message = '페이지가 성공적으로 생성되었습니다.';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = '페이지 생성 중 오류가 발생했습니다: ' . $e->getMessage();
        $messageType = 'error';
    }
}

/**
 * 후기 페이지 생성
 */
function generateReviewPages($type) {
    $config = [
        'food' => [
            'name' => '음식물처리기',
            'file' => 'food_reviews.json',
            'folder' => ROOT_PATH . '/음식물처리기-후기',
            'image_path' => '../pototo',
            'breadcrumb' => '음식물처리기 후기'
        ],
        'sink' => [
            'name' => '싱크볼',
            'file' => 'sink_reviews.json',
            'folder' => ROOT_PATH . '/싱크볼-후기',
            'image_path' => '../potopo',
            'breadcrumb' => '싱크볼 후기'
        ]
    ];

    $cfg = $config[$type];
    $reviews = readJsonData($cfg['file']);
    $itemsPerPage = 6;
    $totalPages = max(1, ceil(count($reviews) / $itemsPerPage));

    // 디렉토리 확인
    if (!is_dir($cfg['folder'])) {
        mkdir($cfg['folder'], 0755, true);
    }

    // 각 페이지 생성
    for ($page = 1; $page <= $totalPages; $page++) {
        $start = ($page - 1) * $itemsPerPage;
        $pageReviews = array_slice($reviews, $start, $itemsPerPage);
        $filename = $page === 1 ? 'index.html' : "page{$page}.html";
        $filepath = $cfg['folder'] . '/' . $filename;

        $html = generatePageHtml($type, $cfg, $pageReviews, $page, $totalPages);
        file_put_contents($filepath, $html);
    }
}

/**
 * 페이지 HTML 생성
 */
function generatePageHtml($type, $cfg, $reviews, $currentPage, $totalPages) {
    $baseUrl = 'http://115.68.223.124/lovelykitchen';
    $typeUrl = $type === 'food' ? '음식물처리기-후기' : '싱크볼-후기';
    $subtitle = $type === 'food'
        ? '고객님들의 생생한 사용 후기를 확인하세요'
        : '아콴테 싱크볼을 사용하신 고객님들의 생생한 후기';

    ob_start();
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $cfg['name']; ?> 후기 | 러블리키친</title>
    <meta name="description" content="러블리키친 <?php echo $cfg['name']; ?> 고객 후기">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="../css/review.css">
    <link rel="icon" type="image/png" href="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png">
</head>
<body>
    <div class="preloader" id="preloader"><div class="preloader-inner"><div class="preloader-logo"><img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Logo"></div><div class="preloader-progress"><div class="preloader-bar"></div></div></div></div>
    <div class="scroll-progress" id="scrollProgress"></div>

    <header class="header scrolled" id="header">
        <div class="header-container">
            <a href="<?php echo $baseUrl; ?>/" class="logo"><img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Lovely Kitchen" class="logo-img"></a>
            <nav class="nav-desktop">
                <ul class="nav-menu">
                    <li><a href="<?php echo $baseUrl; ?>/회사소개/" class="nav-link">회사소개</a></li>
                    <li class="has-dropdown"><a href="<?php echo $baseUrl; ?>/products.html" class="nav-link">제품 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo $baseUrl; ?>/products.html">음식물처리기</a><a href="<?php echo $baseUrl; ?>/싱크볼/">아콴테 싱크볼</a><a href="<?php echo $baseUrl; ?>/악세사리/">악세사리</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo $baseUrl; ?>/질문과-답변/" class="nav-link">고객지원 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo $baseUrl; ?>/질문과-답변/">질문과 답변</a><a href="<?php echo $baseUrl; ?>/a-s-지원/">A/S 서비스 지원</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo $baseUrl; ?>/음식물처리기-후기/" class="nav-link active">후기게시판 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo $baseUrl; ?>/음식물처리기-후기/">음식물처리기 후기</a><a href="<?php echo $baseUrl; ?>/싱크볼-후기/">싱크볼 후기</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo $baseUrl; ?>/사진갤러리/" class="nav-link">사진 갤러리 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo $baseUrl; ?>/사진갤러리/">음식물처리기 갤러리</a><a href="<?php echo $baseUrl; ?>/사진갤러리/">아콴테 싱크볼 갤러리</a></div></li>
                    <li><a href="<?php echo $baseUrl; ?>/친환경제품/" class="nav-link">친환경제품</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="tel:1661-9038" class="header-phone"><i class="fas fa-phone"></i><span>1661-9038</span></a>
                <a href="<?php echo $baseUrl; ?>/빠른상담/" class="btn-consultation"><span>무료상담</span><i class="fas fa-arrow-right"></i></a>
                <button class="mobile-toggle" id="mobileToggle" aria-label="메뉴"><span></span><span></span><span></span></button>
            </div>
        </div>
    </header>

    <div class="mobile-nav" id="mobileNav"><div class="mobile-nav-header"><img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Logo" class="mobile-logo"><button class="mobile-close" id="mobileClose"><i class="fas fa-times"></i></button></div><nav class="mobile-nav-content"><ul class="mobile-menu"><li><a href="<?php echo $baseUrl; ?>/회사소개/">회사소개</a></li><li class="has-submenu"><a href="#">제품 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo $baseUrl; ?>/products.html">음식물처리기</a></li><li><a href="<?php echo $baseUrl; ?>/싱크볼/">아콴테 싱크볼</a></li><li><a href="<?php echo $baseUrl; ?>/악세사리/">악세사리</a></li></ul></li><li class="has-submenu"><a href="#">고객지원 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo $baseUrl; ?>/질문과-답변/">질문과 답변</a></li><li><a href="<?php echo $baseUrl; ?>/a-s-지원/">A/S 서비스 지원</a></li></ul></li><li class="has-submenu"><a href="#">후기게시판 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo $baseUrl; ?>/음식물처리기-후기/">음식물처리기 후기</a></li><li><a href="<?php echo $baseUrl; ?>/싱크볼-후기/">싱크볼 후기</a></li></ul></li><li><a href="<?php echo $baseUrl; ?>/친환경제품/">친환경제품</a></li></ul><div class="mobile-contact"><a href="tel:1661-9038" class="mobile-phone"><i class="fas fa-phone"></i> 1661-9038</a><a href="<?php echo $baseUrl; ?>/빠른상담/" class="mobile-consult-btn">무료 상담 신청</a></div></nav></div>
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <main>
        <section class="page-hero"><div class="page-hero-bg"></div><div class="page-hero-overlay"></div><div class="container"><div class="page-hero-content" data-aos="fade-up"><nav class="breadcrumb"><a href="<?php echo $baseUrl; ?>/">홈</a><span><i class="fas fa-chevron-right"></i></span><span>후기게시판</span><span><i class="fas fa-chevron-right"></i></span><span class="current"><?php echo $cfg['breadcrumb']; ?></span></nav><h1 class="page-title"><?php echo $cfg['name']; ?> 후기</h1><p class="page-subtitle"><?php echo $subtitle; ?></p></div></div></section>

        <section class="review-section">
            <div class="container">
                <div class="review-grid-new">
<?php foreach ($reviews as $review):
    $imageSrc = !empty($review['images']) ? $cfg['image_path'] . '/' . $review['images'][0] : '';
?>
                    <div class="review-item">
                        <div class="review-thumb">
                            <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="후기 이미지">
                        </div>
                        <div class="review-content">
                            <h3 class="review-title"><?php echo htmlspecialchars($review['title']); ?></h3>
                            <p class="review-text"><?php echo htmlspecialchars($review['content']); ?></p>
                            <span class="review-author"><?php echo htmlspecialchars($review['author']); ?></span>
                        </div>
                    </div>
<?php endforeach; ?>
                </div>

                <div class="review-pagination">
<?php for ($i = 1; $i <= $totalPages; $i++):
    $pageUrl = $i === 1 ? "{$baseUrl}/{$typeUrl}/" : "{$baseUrl}/{$typeUrl}/page{$i}.html";
    $activeClass = $i === $currentPage ? 'active' : '';
?>
                    <a href="<?php echo $pageUrl; ?>" class="page-link <?php echo $activeClass; ?>"><?php echo $i; ?></a>
<?php endfor; ?>
<?php if ($currentPage < $totalPages): ?>
                    <a href="<?php echo "{$baseUrl}/{$typeUrl}/page" . ($currentPage + 1) . ".html"; ?>" class="page-link next"><i class="fas fa-chevron-right"></i></a>
<?php endif; ?>
                    <a href="<?php echo "{$baseUrl}/{$typeUrl}/page{$totalPages}.html"; ?>" class="page-link last">마지막</a>
                </div>
            </div>
        </section>
    </main>

    <div class="counsel-float">
        <a href="tel:1661-9038" class="counsel-btn">
            <i class="fas fa-headset"></i>
            <div class="counsel-text">
                <span class="counsel-title">상담 문의 전화</span>
                <span class="counsel-sub">운영시간 상시</span>
                <span class="counsel-number">1661-9038</span>
            </div>
        </a>
    </div>

    <footer class="footer"><div class="footer-top"><div class="container"><div class="footer-grid"><div class="footer-brand"><a href="<?php echo $baseUrl; ?>/" class="footer-logo"><img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Lovely Kitchen"></a><p class="footer-tagline">완벽한 분쇄, 차원이 다른 프리미엄</p></div><div class="footer-links"><h4>제품</h4><ul><li><a href="<?php echo $baseUrl; ?>/products.html">음식물처리기</a></li><li><a href="<?php echo $baseUrl; ?>/싱크볼/">아콴테 싱크볼</a></li><li><a href="<?php echo $baseUrl; ?>/악세사리/">악세사리</a></li></ul></div><div class="footer-links"><h4>고객지원</h4><ul><li><a href="<?php echo $baseUrl; ?>/질문과-답변/">질문과 답변</a></li><li><a href="<?php echo $baseUrl; ?>/a-s-지원/">A/S 서비스 지원</a></li><li><a href="<?php echo $baseUrl; ?>/빠른상담/">빠른 상담</a></li></ul></div><div class="footer-contact"><h4>연락처</h4><div class="contact-item"><i class="fas fa-phone"></i><div><span class="label">고객센터</span><a href="tel:1661-9038" class="value">1661-9038</a></div></div></div></div></div></div><div class="footer-bottom"><div class="container"><div class="footer-info"><p>러블리키친 총판 대표이사 성정호 ｜ 사업자등록번호 306-08-91986</p></div><div class="footer-copyright"><p>&copy; 2024 LOVELY KITCHEN. All Rights Reserved.</p></div></div></div></footer>

    <!-- Review Detail Modal -->
    <div class="review-modal-overlay" id="reviewModalOverlay"></div>
    <div class="review-modal" id="reviewModal">
        <button class="review-modal-close" id="reviewModalClose">
            <i class="fas fa-times"></i>
        </button>
        <div class="review-modal-content">
            <div class="review-modal-image">
                <img id="modalImage" src="" alt="후기 이미지">
            </div>
            <div class="review-modal-body">
                <h2 class="review-modal-title" id="modalTitle"></h2>
                <p class="review-modal-text" id="modalText"></p>
                <div class="review-modal-author">
                    <div class="review-modal-author-avatar" id="modalAvatar"></div>
                    <div class="review-modal-author-info">
                        <span class="review-modal-author-name" id="modalAuthor"></span>
                        <span class="review-modal-author-badge"><i class="fas fa-check-circle"></i> 인증 구매</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reviewItems = document.querySelectorAll('.review-item');
            const modal = document.getElementById('reviewModal');
            const modalOverlay = document.getElementById('reviewModalOverlay');
            const modalClose = document.getElementById('reviewModalClose');
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('modalTitle');
            const modalText = document.getElementById('modalText');
            const modalAuthor = document.getElementById('modalAuthor');
            const modalAvatar = document.getElementById('modalAvatar');

            reviewItems.forEach(item => {
                item.addEventListener('click', function() {
                    const img = this.querySelector('.review-thumb img');
                    const title = this.querySelector('.review-title');
                    const text = this.querySelector('.review-text');
                    const author = this.querySelector('.review-author');

                    modalImage.src = img.src;
                    modalTitle.textContent = title.textContent;
                    modalText.textContent = text.textContent;
                    modalAuthor.textContent = author.textContent;
                    modalAvatar.textContent = author.textContent.charAt(0);

                    modal.classList.add('active');
                    modalOverlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });
            });

            function closeModal() {
                modal.classList.remove('active');
                modalOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            modalClose.addEventListener('click', closeModal);
            modalOverlay.addEventListener('click', closeModal);
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    closeModal();
                }
            });
        });
    </script>
</body>
</html>
<?php
    return ob_get_clean();
}
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>페이지 생성 | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css?v=2">
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
                    <li class="nav-item">
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
                    <h1 class="page-title">페이지 생성</h1>
                </div>
            </header>

            <div class="content-body">
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo sanitize($message); ?>
                </div>
                <?php endif; ?>

                <div class="section">
                    <h2 class="section-title">후기 페이지 생성</h2>
                    <p class="section-desc">관리자에서 등록한 후기 데이터를 기반으로 HTML 페이지를 생성합니다.</p>

                    <div class="generate-cards">
                        <form method="POST" action="" class="generate-card">
                            <input type="hidden" name="type" value="food">
                            <div class="generate-icon food">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <h3>음식물처리기 후기</h3>
                            <p>음식물처리기 후기 페이지를 새로 생성합니다.</p>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-sync-alt"></i>
                                생성하기
                            </button>
                        </form>

                        <form method="POST" action="" class="generate-card">
                            <input type="hidden" name="type" value="sink">
                            <div class="generate-icon sink">
                                <i class="fas fa-sink"></i>
                            </div>
                            <h3>싱크볼 후기</h3>
                            <p>싱크볼 후기 페이지를 새로 생성합니다.</p>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-sync-alt"></i>
                                생성하기
                            </button>
                        </form>

                        <form method="POST" action="" class="generate-card all">
                            <input type="hidden" name="type" value="all">
                            <div class="generate-icon all">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <h3>전체 페이지 생성</h3>
                            <p>모든 후기 페이지를 한 번에 생성합니다.</p>
                            <button type="submit" class="btn-primary btn-lg">
                                <i class="fas fa-sync-alt"></i>
                                전체 생성하기
                            </button>
                        </form>
                    </div>

                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>안내</strong>
                            <p>페이지 생성 시 기존 후기 페이지가 덮어씌워집니다. 후기를 추가하거나 수정한 후에 페이지를 생성해주세요.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
