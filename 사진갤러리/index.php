<?php
require_once dirname(__DIR__) . '/includes/config.php';
$galleryData = getGalleryData('food');

// 기존 하드코딩된 이미지 목록
$defaultImages = [
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-107.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-108.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-109.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-110.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-111.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-112.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-113.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-114.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-115.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-116.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-117.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-118.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-119.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-120.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-3.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-4.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-5.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-6.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-7.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-8.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-9.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-10.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-11.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-12.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-13.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-14.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-15.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-16.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-17.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-18.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-19.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-20.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-21.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-22.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-23.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-24.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-26.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-27.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-28.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-29.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-30.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-31.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-32.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-33.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-34.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-35.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-36.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-37.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-38.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-40.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-41.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-42.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-44.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-45.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-46.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-47.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-48.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-49.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-50.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-51.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-52.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-53.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-54.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-55.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-56.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-57.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-58.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-59.jpg',
    'https://lkitchen.co.kr/wp-content/uploads/2025/04/1-60.jpg',
];
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>사진 갤러리 | <?php echo e($siteSettings['site_name']); ?></title>
    <meta name="description" content="러블리키친 시공 사진 갤러리">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="icon" type="image/png" href="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png">
    <style>
        /* 라이트박스 스타일 */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 10000;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .lightbox.active {
            display: flex;
            opacity: 1;
        }
        .lightbox-content {
            max-width: 90%;
            max-height: 90%;
            position: relative;
        }
        .lightbox-content img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 50%;
            color: #fff;
            font-size: 28px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .lightbox-close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }
        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 50%;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .lightbox-nav:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .lightbox-prev { left: 20px; }
        .lightbox-next { right: 20px; }
        .gallery-page-item {
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .gallery-page-item:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <div class="preloader" id="preloader"><div class="preloader-inner"><div class="preloader-logo"><img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Logo"></div><div class="preloader-progress"><div class="preloader-bar"></div></div></div></div>
    <div class="scroll-progress" id="scrollProgress"></div>

    <header class="header scrolled" id="header">
        <div class="header-container">
            <a href="<?php echo SITE_URL; ?>/" class="logo"><img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Lovely Kitchen" class="logo-img"></a>
            <nav class="nav-desktop">
                <ul class="nav-menu">
                    <li><a href="<?php echo SITE_URL; ?>/회사소개/" class="nav-link">회사소개</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/친환경제품/" class="nav-link">친환경</a></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/products.html" class="nav-link">제품 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/products.html">음식물처리기</a><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/사진갤러리/" class="nav-link active">갤러리 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/사진갤러리/">음식물처리기 갤러리</a><a href="<?php echo SITE_URL; ?>/사진갤러리/싱크볼/">아콴테 싱크볼 갤러리</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/" class="nav-link">구매후기 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/">음식물처리기 후기</a><a href="<?php echo SITE_URL; ?>/싱크볼-후기/">싱크볼 후기</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/질문과-답변/" class="nav-link">고객지원 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></div></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="tel:<?php echo e($siteSettings['phone']); ?>" class="header-phone"><i class="fas fa-phone"></i><span><?php echo e($siteSettings['phone']); ?></span></a>
                <a href="<?php echo SITE_URL; ?>/빠른상담/" class="btn-consultation"><span>무료상담</span><i class="fas fa-arrow-right"></i></a>
                <button class="mobile-toggle" id="mobileToggle" aria-label="메뉴"><span></span><span></span><span></span></button>
            </div>
        </div>
    </header>

    <div class="mobile-nav" id="mobileNav"><div class="mobile-nav-header"><img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Logo" class="mobile-logo"><button class="mobile-close" id="mobileClose"><i class="fas fa-times"></i></button></div><nav class="mobile-nav-content"><ul class="mobile-menu"><li><a href="<?php echo SITE_URL; ?>/회사소개/">회사소개</a></li><li><a href="<?php echo SITE_URL; ?>/친환경제품/">친환경</a></li><li class="has-submenu"><a href="#">제품 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/products.html">음식물처리기</a></li><li><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a></li><li><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></li></ul></li><li class="has-submenu"><a href="#">갤러리 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/사진갤러리/">음식물처리기 갤러리</a></li><li><a href="<?php echo SITE_URL; ?>/사진갤러리/싱크볼/">아콴테 싱크볼 갤러리</a></li></ul></li><li class="has-submenu"><a href="#">구매후기 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/">음식물처리기 후기</a></li><li><a href="<?php echo SITE_URL; ?>/싱크볼-후기/">싱크볼 후기</a></li></ul></li><li class="has-submenu"><a href="#">고객지원 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a></li><li><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></li></ul></li></ul><div class="mobile-contact"><a href="tel:<?php echo e($siteSettings['phone']); ?>" class="mobile-phone"><i class="fas fa-phone"></i> <?php echo e($siteSettings['phone']); ?></a><a href="<?php echo SITE_URL; ?>/빠른상담/" class="mobile-consult-btn">무료 상담 신청</a></div></nav></div>
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <main>
        <section class="page-hero"><div class="page-hero-bg"></div><div class="page-hero-overlay"></div><div class="container"><div class="page-hero-content" data-aos="fade-up"><nav class="breadcrumb"><a href="<?php echo SITE_URL; ?>/">홈</a><span><i class="fas fa-chevron-right"></i></span><span class="current">사진 갤러리</span></nav><h1 class="page-title">사진 갤러리</h1><p class="page-subtitle">러블리키친 시공 사진을 확인해보세요</p></div></div></section>

        <section class="content-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-tag">GALLERY</span>
                    <h2 class="section-title">시공 스토리</h2>
                    <p class="section-desc">러블리키친과 함께하는 깨끗한 변화</p>
                </div>

                <div class="gallery-page-grid" data-aos="fade-up">
                    <?php
                    // 관리자에서 추가한 이미지 먼저 표시
                    foreach ($galleryData as $image):
                    ?>
                    <div class="gallery-page-item"><img src="<?php echo e($image['url']); ?>" alt="<?php echo e($image['description'] ?? '시공 사진'); ?>" loading="lazy"></div>
                    <?php endforeach; ?>

                    <?php
                    // 기본 이미지 표시
                    foreach ($defaultImages as $imageUrl):
                    ?>
                    <div class="gallery-page-item"><img src="<?php echo e($imageUrl); ?>" alt="시공 사진" loading="lazy"></div>
                    <?php endforeach; ?>
                </div>

                <div class="cta-box" data-aos="fade-up">
                    <h3>나도 러블리키친으로 바꾸고 싶다면?</h3>
                    <p>지금 바로 상담받고 특별한 혜택을 받아보세요.</p>
                    <div class="cta-box-buttons">
                        <a href="<?php echo SITE_URL; ?>/빠른상담/" class="btn-primary"><span>상담 신청</span><i class="fas fa-arrow-right"></i></a>
                        <a href="tel:<?php echo e($siteSettings['phone']); ?>" class="btn-secondary"><i class="fas fa-phone"></i><span><?php echo e($siteSettings['phone']); ?></span></a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer"><div class="footer-top"><div class="container"><div class="footer-grid"><div class="footer-brand"><a href="<?php echo SITE_URL; ?>/" class="footer-logo"><img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Lovely Kitchen"></a><p class="footer-tagline">완벽한 분쇄, 차원이 다른 프리미엄</p></div><div class="footer-links"><h4>제품</h4><ul><li><a href="<?php echo SITE_URL; ?>/products.html">음식물처리기</a></li><li><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a></li><li><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></li></ul></div><div class="footer-links"><h4>고객지원</h4><ul><li><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a></li><li><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></li><li><a href="<?php echo SITE_URL; ?>/빠른상담/">빠른 상담</a></li></ul></div><div class="footer-contact"><h4>연락처</h4><div class="contact-item"><i class="fas fa-phone"></i><div><span class="label">고객센터</span><a href="tel:<?php echo e($siteSettings['phone']); ?>" class="value"><?php echo e($siteSettings['phone']); ?></a></div></div></div></div></div></div><div class="footer-bottom"><div class="container"><div class="footer-info"><p><?php echo e($siteSettings['company_name']); ?> 대표이사 <?php echo e($siteSettings['ceo_name']); ?> ｜ 사업자등록번호 <?php echo e($siteSettings['business_number']); ?></p></div><div class="footer-copyright"><p><?php echo e($siteSettings['footer_text']); ?></p></div></div></div></footer>

    <div class="phone-inquiry-container" id="phoneInquiry">
        <button class="close-btn" onclick="document.getElementById('phoneInquiry').classList.add('hidden');">&times;</button>
        <a href="tel:<?php echo e($siteSettings['phone']); ?>" class="phone-inquiry-float"><img decoding="async" src="<?php echo e($siteSettings['phone_image_url']); ?>" alt="전화문의" width="222" height="202"></a>
    </div>

    <!-- 라이트박스 -->
    <div class="lightbox" id="lightbox">
        <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
        <button class="lightbox-nav lightbox-prev" onclick="navigateLightbox(-1)"><i class="fas fa-chevron-left"></i></button>
        <div class="lightbox-content">
            <img id="lightbox-img" src="" alt="갤러리 이미지">
        </div>
        <button class="lightbox-nav lightbox-next" onclick="navigateLightbox(1)"><i class="fas fa-chevron-right"></i></button>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../js/main.js"></script>
    <script>
        // 라이트박스 기능
        const galleryItems = document.querySelectorAll('.gallery-page-item');
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        let currentIndex = 0;
        let images = [];

        // 이미지 배열 생성
        galleryItems.forEach((item, index) => {
            const img = item.querySelector('img');
            if (img) {
                images.push(img.src);
                item.addEventListener('click', () => openLightbox(index));
            }
        });

        function openLightbox(index) {
            currentIndex = index;
            lightboxImg.src = images[currentIndex];
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        }

        function navigateLightbox(direction) {
            currentIndex += direction;
            if (currentIndex < 0) currentIndex = images.length - 1;
            if (currentIndex >= images.length) currentIndex = 0;
            lightboxImg.src = images[currentIndex];
        }

        // 키보드 네비게이션
        document.addEventListener('keydown', (e) => {
            if (!lightbox.classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') navigateLightbox(-1);
            if (e.key === 'ArrowRight') navigateLightbox(1);
        });

        // 배경 클릭 시 닫기
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) closeLightbox();
        });
    </script>
</body>
</html>
