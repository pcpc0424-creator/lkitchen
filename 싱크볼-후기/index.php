<?php
/**
 * 싱크볼 후기 페이지
 * 관리자 페이지의 sink_reviews.json 데이터와 연동
 */
require_once dirname(__DIR__) . '/includes/config.php';

// 후기 데이터 로드
$reviews = getReviewData('sink');

// 페이지네이션 설정
$perPage = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$totalReviews = count($reviews);
$totalPages = ceil($totalReviews / $perPage);
$page = min($page, max(1, $totalPages));
$offset = ($page - 1) * $perPage;
$currentReviews = array_slice($reviews, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>싱크볼 후기 | 러블리키친</title>
    <meta name="description" content="러블리키친 아콴테 싱크볼 고객 후기">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="../css/review.css">
    <link rel="icon" type="image/png" href="/수정/fhrh.png">
</head>
<body>
    <div class="preloader" id="preloader"><div class="preloader-inner"><div class="preloader-logo"><img src="/수정/fhrh.png" alt="Logo"></div><div class="preloader-progress"><div class="preloader-bar"></div></div></div></div>
    <div class="scroll-progress" id="scrollProgress"></div>

    <header class="header scrolled" id="header">
        <div class="header-container">
            <a href="<?php echo SITE_URL; ?>/" class="logo"><img src="/수정/fhrh.png" alt="Lovely Kitchen" class="logo-img"></a>
            <nav class="nav-desktop">
                <ul class="nav-menu">
                    <li><a href="<?php echo SITE_URL; ?>/회사소개/" class="nav-link">회사소개</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/친환경제품/" class="nav-link">친환경</a></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/products.php" class="nav-link">제품 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/products.php">음식물처리기</a><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/사진갤러리/" class="nav-link">갤러리 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/사진갤러리/">음식물처리기 갤러리</a><a href="<?php echo SITE_URL; ?>/사진갤러리/싱크볼/">아콴테 싱크볼 갤러리</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/" class="nav-link active">구매후기 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/">음식물처리기 후기</a><a href="<?php echo SITE_URL; ?>/싱크볼-후기/">싱크볼 후기</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/질문과-답변/" class="nav-link">고객지원 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></div></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="tel:1661-9038" class="header-phone"><i class="fas fa-phone"></i><span>1661-9038</span></a>
                <a href="<?php echo SITE_URL; ?>/빠른상담/" class="btn-consultation"><span>무료상담</span><i class="fas fa-arrow-right"></i></a>
                <button class="mobile-toggle" id="mobileToggle" aria-label="메뉴"><span></span><span></span><span></span></button>
            </div>
        </div>
    </header>

    <div class="mobile-nav" id="mobileNav"><div class="mobile-nav-header"><img src="/수정/fhrh.png" alt="Logo" class="mobile-logo"><button class="mobile-close" id="mobileClose"><i class="fas fa-times"></i></button></div><nav class="mobile-nav-content"><ul class="mobile-menu"><li><a href="<?php echo SITE_URL; ?>/회사소개/">회사소개</a></li><li><a href="<?php echo SITE_URL; ?>/친환경제품/">친환경</a></li><li class="has-submenu"><a href="#">제품 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/products.php">음식물처리기</a></li><li><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a></li><li><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></li></ul></li><li class="has-submenu"><a href="#">갤러리 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/사진갤러리/">음식물처리기 갤러리</a></li><li><a href="<?php echo SITE_URL; ?>/사진갤러리/싱크볼/">아콴테 싱크볼 갤러리</a></li></ul></li><li class="has-submenu"><a href="#">구매후기 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/">음식물처리기 후기</a></li><li><a href="<?php echo SITE_URL; ?>/싱크볼-후기/">싱크볼 후기</a></li></ul></li><li class="has-submenu"><a href="#">고객지원 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a></li><li><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></li></ul></li></ul><div class="mobile-contact"><a href="tel:1661-9038" class="mobile-phone"><i class="fas fa-phone"></i> 1661-9038</a><a href="<?php echo SITE_URL; ?>/빠른상담/" class="mobile-consult-btn">무료 상담 신청</a></div></nav></div>
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <main>
        <section class="page-hero"><div class="page-hero-bg"></div><div class="page-hero-overlay"></div><div class="container"><div class="page-hero-content" data-aos="fade-up"><nav class="breadcrumb"><a href="<?php echo SITE_URL; ?>/">홈</a><span><i class="fas fa-chevron-right"></i></span><span>구매후기</span><span><i class="fas fa-chevron-right"></i></span><span class="current">싱크볼 후기</span></nav><h1 class="page-title">싱크볼 후기</h1><p class="page-subtitle">아콴테 싱크볼을 사용하신 고객님들의 생생한 후기</p></div></div></section>

        <section class="review-section">
            <div class="container">
                <?php if (empty($currentReviews)): ?>
                <div class="no-reviews" style="text-align: center; padding: 60px 20px;">
                    <i class="fas fa-comment-slash" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
                    <p style="color: #888;">등록된 후기가 없습니다.</p>
                </div>
                <?php else: ?>
                <div class="review-grid-new">
                    <?php foreach ($currentReviews as $review): ?>
                    <div class="review-item <?php echo empty($review['images']) ? 'no-image' : ''; ?>">
                        <?php if (!empty($review['images']) && !empty($review['images'][0])): ?>
                        <div class="review-thumb">
                            <?php
                            $img = $review['images'][0];
                            $imagePath = (strpos($img, 'http') === 0) ? $img : '../potopo/' . rawurlencode($img);
                            ?>
                            <img src="<?php echo e($imagePath); ?>" alt="후기 이미지">
                        </div>
                        <?php endif; ?>
                        <div class="review-content">
                            <h3 class="review-title"><?php echo e($review['title'] ?? ''); ?></h3>
                            <p class="review-text"><?php echo e($review['content'] ?? ''); ?></p>
                            <span class="review-author"><?php echo e($review['author'] ?? ''); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="review-pagination">
                    <?php if ($page > 1): ?>
                    <a href="?page=1" class="page-link first">처음</a>
                    <a href="?page=<?php echo $page - 1; ?>" class="page-link prev"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>

                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                    <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="page-link next"><i class="fas fa-chevron-right"></i></a>
                    <a href="?page=<?php echo $totalPages; ?>" class="page-link last">마지막</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="footer"><div class="footer-top"><div class="container"><div class="footer-grid"><div class="footer-brand"><a href="<?php echo SITE_URL; ?>/" class="footer-logo"><img src="/수정/fhrh.png" alt="Lovely Kitchen"></a><p class="footer-tagline">완벽한 분쇄, 차원이 다른 프리미엄</p></div><div class="footer-links"><h4>제품</h4><ul><li><a href="<?php echo SITE_URL; ?>/products.php">음식물처리기</a></li><li><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a></li><li><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></li></ul></div><div class="footer-links"><h4>고객지원</h4><ul><li><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a></li><li><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></li><li><a href="<?php echo SITE_URL; ?>/빠른상담/">빠른 상담</a></li></ul></div><div class="footer-contact"><h4>연락처</h4><div class="contact-item"><i class="fas fa-phone"></i><div><span class="label">고객센터</span><a href="tel:1661-9038" class="value">1661-9038</a></div></div></div></div></div></div><div class="footer-bottom"><div class="container"><div class="footer-info"><p>러블리키친 총판 대표이사 성정호 ｜ 사업자등록번호 306-08-91986</p></div><div class="footer-copyright"><p>&copy; 2024 LOVELY KITCHEN. All Rights Reserved.</p></div></div></div></footer>

    <!-- Review Detail Modal -->
    <div class="review-modal-overlay" id="reviewModalOverlay"></div>
    <div class="review-modal" id="reviewModal">
        <button class="review-modal-close" id="reviewModalClose">
            <i class="fas fa-times"></i>
        </button>
        <div class="review-modal-content">
            <div class="review-modal-image" id="modalImageWrap">
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
            const modalImageWrap = document.getElementById('modalImageWrap');
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

                    // 이미지가 있으면 표시, 없으면 숨김
                    if (img) {
                        modalImage.src = img.src;
                        modalImageWrap.style.display = 'block';
                    } else {
                        modalImage.src = '';
                        modalImageWrap.style.display = 'none';
                    }

                    modalTitle.textContent = title ? title.textContent : '';
                    modalText.textContent = text ? text.textContent : '';
                    modalAuthor.textContent = author ? author.textContent : '';
                    modalAvatar.textContent = author ? author.textContent.charAt(0) : '';

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

    <div class="phone-inquiry-container" id="phoneInquiry">
        <button class="close-btn" onclick="document.getElementById('phoneInquiry').classList.add('hidden');">&times;</button>
        <a href="tel:1661-9038" class="phone-inquiry-float"><img decoding="async" src="https://lkitchen.co.kr/wp-content/uploads/2025/10/전화문의.png" alt="전화문의" width="222" height="202"></a>
    </div>
</body>
</html>
