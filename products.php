<?php
/**
 * 러블리키친 제품 페이지
 * 관리자 페이지의 products.json 데이터와 연동
 */
require_once __DIR__ . '/includes/config.php';

// 제품 데이터 로드
$productsData = readJsonData('products.json');
$products = array_filter($productsData['products'] ?? [], function($p) {
    return $p['active'] ?? true;
});

define('SITE_URL', 'http://115.68.223.124/lovelykitchen');
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>제품 | 러블리키친 - 프리미엄 음식물처리기</title>
    <meta name="description" content="러블리키친 프리미엄 음식물처리기 제품 라인업. LK-750A, LK-900A, LK-1000A, LK-1000B 모델 소개">
    <meta name="keywords" content="음식물처리기, 러블리키친, LK-750A, LK-900A, LK-1000A, LK-1000B">

    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

    <!-- Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/products.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="http://115.68.223.124/lovelykitchen/수정/fhrh.png">
</head>
<body>
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-inner">
            <div class="preloader-logo">
                <img src="http://115.68.223.124/lovelykitchen/수정/fhrh.png" alt="Logo">
            </div>
            <div class="preloader-progress">
                <div class="preloader-bar"></div>
            </div>
        </div>
    </div>

    <!-- Scroll Progress -->
    <div class="scroll-progress" id="scrollProgress"></div>

    <!-- Cursor Effect -->
    <div class="cursor-dot" id="cursorDot"></div>
    <div class="cursor-outline" id="cursorOutline"></div>

    <!-- Header -->
    <header class="header scrolled" id="header">
        <div class="header-container">
            <a href="<?php echo SITE_URL; ?>/" class="logo">
                <img src="http://115.68.223.124/lovelykitchen/수정/fhrh.png" alt="Lovely Kitchen" class="logo-img">
            </a>

            <nav class="nav-desktop">
                <ul class="nav-menu">
                    <li><a href="<?php echo SITE_URL; ?>/회사소개/" class="nav-link">회사소개</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/친환경제품/" class="nav-link">친환경</a></li>
                    <li class="has-dropdown">
                        <a href="<?php echo SITE_URL; ?>/products.php" class="nav-link active">제품 <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="<?php echo SITE_URL; ?>/products.php">음식물처리기</a>
                            <a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a>
                            <a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a>
                        </div>
                    </li>
                    <li class="has-dropdown">
                        <a href="<?php echo SITE_URL; ?>/사진갤러리/" class="nav-link">갤러리 <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="<?php echo SITE_URL; ?>/사진갤러리/">음식물처리기 갤러리</a>
                            <a href="<?php echo SITE_URL; ?>/사진갤러리/싱크볼/">아콴테 싱크볼 갤러리</a>
                        </div>
                    </li>
                    <li class="has-dropdown">
                        <a href="<?php echo SITE_URL; ?>/음식물처리기-후기/" class="nav-link">구매후기 <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="<?php echo SITE_URL; ?>/음식물처리기-후기/">음식물처리기 후기</a>
                            <a href="<?php echo SITE_URL; ?>/싱크볼-후기/">싱크볼 후기</a>
                        </div>
                    </li>
                    <li class="has-dropdown">
                        <a href="<?php echo SITE_URL; ?>/질문과-답변/" class="nav-link">고객지원 <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a>
                            <a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="header-actions">
                <a href="tel:1661-9038" class="header-phone">
                    <i class="fas fa-phone"></i>
                    <span>1661-9038</span>
                </a>
                <a href="<?php echo SITE_URL; ?>/빠른상담/" class="btn-consultation">
                    <span>무료상담</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <button class="mobile-toggle" id="mobileToggle" aria-label="메뉴">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation -->
    <div class="mobile-nav" id="mobileNav">
        <div class="mobile-nav-header">
            <img src="http://115.68.223.124/lovelykitchen/수정/fhrh.png" alt="Logo" class="mobile-logo">
            <button class="mobile-close" id="mobileClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="mobile-nav-content">
            <ul class="mobile-menu">
                <li><a href="<?php echo SITE_URL; ?>/회사소개/">회사소개</a></li>
                <li><a href="<?php echo SITE_URL; ?>/친환경제품/">친환경</a></li>
                <li class="has-submenu">
                    <a href="#">제품 <i class="fas fa-plus"></i></a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/products.php">음식물처리기</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#">갤러리 <i class="fas fa-plus"></i></a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/사진갤러리/">음식물처리기 갤러리</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/사진갤러리/싱크볼/">아콴테 싱크볼 갤러리</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#">구매후기 <i class="fas fa-plus"></i></a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/">음식물처리기 후기</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/싱크볼-후기/">싱크볼 후기</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#">고객지원 <i class="fas fa-plus"></i></a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></li>
                    </ul>
                </li>
            </ul>
            <div class="mobile-contact">
                <a href="tel:1661-9038" class="mobile-phone">
                    <i class="fas fa-phone"></i> 1661-9038
                </a>
                <a href="<?php echo SITE_URL; ?>/빠른상담/" class="mobile-consult-btn">무료 상담 신청</a>
            </div>
        </nav>
    </div>
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <main>
        <!-- Page Hero -->
        <section class="page-hero">
            <div class="page-hero-bg"></div>
            <div class="page-hero-overlay"></div>
            <div class="container">
                <div class="page-hero-content" data-aos="fade-up">
                    <nav class="breadcrumb">
                        <a href="<?php echo SITE_URL; ?>/">홈</a>
                        <span><i class="fas fa-chevron-right"></i></span>
                        <span class="current">제품</span>
                    </nav>
                    <h1 class="page-title">음식물처리기</h1>
                    <p class="page-subtitle">완벽한 분쇄, 차원이 다른 프리미엄 음식물처리기</p>
                </div>
            </div>
        </section>

        <!-- Products Section -->
        <section class="products-page-section">
            <div class="container">
                <!-- Product Filter Tabs (Optional) -->
                <div class="products-filter" data-aos="fade-up">
                    <button class="filter-btn active" data-filter="all">전체</button>
                    <button class="filter-btn" data-filter="entry">엔트리</button>
                    <button class="filter-btn" data-filter="premium">프리미엄</button>
                    <button class="filter-btn" data-filter="flagship">플래그십</button>
                </div>

                <!-- Products Grid -->
                <div class="products-page-grid">
                    <?php
                    $delay = 100;
                    foreach ($products as $product):
                        $isBest = $product['is_best'] ?? false;
                        $category = $product['category'] ?? 'entry';
                        $badgeType = $product['badge_type'] ?? $category;
                    ?>
                    <div class="product-page-card<?php echo $isBest ? ' featured' : ''; ?>" data-category="<?php echo e($category); ?>" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <div class="product-page-badge <?php echo e($badgeType); ?>"><?php echo e($product['badge'] ?? strtoupper($category)); ?></div>
                        <?php if ($isBest): ?>
                        <div class="product-best-tag">
                            <i class="fas fa-crown"></i>
                            <span>인기 1위</span>
                        </div>
                        <?php endif; ?>
                        <div class="product-page-image">
                            <img src="<?php echo e($product['image']); ?>" alt="<?php echo e($product['model']); ?> <?php echo e($product['name']); ?>">
                            <div class="product-page-overlay">
                                <a href="<?php echo SITE_URL; ?>/products/<?php echo strtolower(e($product['model'])); ?>.html" class="product-detail-btn">
                                    <i class="fas fa-search-plus"></i>
                                    <span>상세보기</span>
                                </a>
                            </div>
                        </div>
                        <div class="product-page-content">
                            <div class="product-model"><?php echo e($product['model']); ?></div>
                            <h3 class="product-page-name"><?php echo e($product['name']); ?></h3>
                            <p class="product-page-subtitle"><?php echo e($product['name_en']); ?></p>
                            <div class="product-page-specs">
                                <span class="spec-item"><i class="fas fa-bolt"></i> <?php echo e($product['hp']); ?></span>
                                <span class="spec-item"><i class="fas <?php echo e($product['spec_icon'] ?? 'fa-cog'); ?>"></i> <?php echo e($product['spec_text'] ?? ''); ?></span>
                            </div>
                            <ul class="product-page-features">
                                <?php foreach ($product['features'] ?? [] as $feature): ?>
                                <li><i class="fas fa-check"></i> <?php echo e($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="product-page-price">
                                <span class="price-value"><?php echo e($product['price']); ?></span>
                                <span class="price-unit">원</span>
                            </div>
                            <div class="product-page-actions">
                                <a href="<?php echo SITE_URL; ?>/빠른상담/" class="btn-inquiry">
                                    <i class="fas fa-headset"></i>
                                    <span>상담문의</span>
                                </a>
                                <a href="tel:1661-9038" class="btn-call">
                                    <i class="fas fa-phone"></i>
                                    <span>전화문의</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                    $delay += 100;
                    endforeach;
                    ?>
                </div>
            </div>
        </section>

        <!-- Product Benefits Section -->
        <section class="products-benefits">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-tag">WHY LOVELY KITCHEN</span>
                    <h2 class="section-title">제품 선택에 도움을 드리고 싶습니다</h2>
                </div>
                <div class="benefits-grid">
                    <div class="benefit-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="benefit-icon">
                            <i class="fas fa-gem"></i>
                        </div>
                        <h3>프리미엄 자재와 맞춤 설계로 최고 품질의 제품을 제공</h3>
                        <p>러블리키친은 제품 설치시에 자재 하나하나 프리미엄 자재만을 사용하여 설치하며, 필요시 직접 디자인 / 설계 / 금형 제작하여 모든 고객님댁에 설치가 될수 있도록 하고 있습니다. 어디에 비교해도 자신 있습니다.</p>
                    </div>
                    <div class="benefit-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="benefit-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h3>5년 이상 경력의 전문가가 시공하는 차원 높은 음식물 분쇄기 설치</h3>
                        <p>오직 음식물 분쇄기 설치만 5년 이상 몸담았던 엔지니어만 있습니다. 차원이 다른 놀라운 시공 퀄리티를 경험해 보세요.</p>
                    </div>
                    <div class="benefit-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="benefit-icon">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <h3>음식물처리기 100% 이전 설치 가능</h3>
                        <p>러블리키친 음식물처리기는 100%에 가까운 설치 성공률을 자랑합니다. 다른뜻으로 100% 이전설치가 가능하다는 이야기 입니다. (지금 사용하고 있더라도 차후에 이전설치를 못해서 제품을 사용 못하는 경우 절대 없습니다.)</p>
                    </div>
                    <div class="benefit-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="benefit-icon">
                            <i class="fas fa-volume-mute"></i>
                        </div>
                        <h3>냄새없고 조용한 음식물 처리기</h3>
                        <p>러블리키친 음식물처리기는 고객님의 품격 있는 라이프스타일에 맞춰 개발되었습니다. 강력한 성능은 물론, 냄새와 소음까지 완벽하게 잡아 쾌적한 주방 환경을 약속합니다. 기대 이상으로 만족하실 것입니다.</p>
                    </div>
                    <div class="benefit-card benefit-card-contamination" data-aos="fade-up" data-aos-delay="500">
                        <div class="benefit-icon">
                            <i class="fas fa-biohazard"></i>
                        </div>
                        <h3>미생물 오염도 (ATP 기준)</h3>
                        <div class="contamination-content">
                            <ul class="contamination-stats">
                                <li>화장실 <strong>200</strong></li>
                                <li>쓰레기통 <strong>1,800</strong></li>
                                <li class="highlight">음식물쓰레기 <strong>9,300</strong></li>
                            </ul>
                            <div class="contamination-section">
                                <p class="section-label">상온 방치 시 세균 증식</p>
                                <ul class="bacteria-list">
                                    <li>10시간 후 <span class="num">1억 마리</span></li>
                                    <li>15시간 후 <span class="num">100억 마리</span></li>
                                </ul>
                            </div>
                            <div class="contamination-section warning-box">
                                <p class="section-label">아플라톡신이란?</p>
                                <p class="section-desc">오래된 음식물에서 발생하는<br>발암성 독성 곰팡이입니다.</p>
                                <p class="section-desc">독성이 강한 아플라톡신 B1은<br>피부를 통해 침투할 수 있습니다.</p>
                            </div>
                            <p class="contamination-note">※ 환경에 따라 차이가 있을 수 있음</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="cta-bg">
                <div class="cta-pattern"></div>
            </div>
            <div class="container">
                <div class="cta-content" data-aos="fade-up">
                    <span class="cta-tag">GET STARTED</span>
                    <h2 class="cta-title">
                        주방의 새로운 시대,<br>
                        <span class="gradient-text">지금 시작하세요</span>
                    </h2>
                    <p class="cta-desc">
                        음식물 처리기 설치만으로 거주환경의 커다란 발전을 경험하세요
                    </p>
                    <div class="cta-buttons">
                        <a href="<?php echo SITE_URL; ?>/빠른상담/" class="btn-primary large">
                            <span>무료 상담 신청</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="tel:1661-9038" class="btn-outline-light large">
                            <i class="fas fa-phone"></i>
                            <span>1661-9038</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-brand">
                        <a href="<?php echo SITE_URL; ?>/" class="footer-logo">
                            <img src="http://115.68.223.124/lovelykitchen/수정/fhrh.png" alt="Lovely Kitchen">
                        </a>
                        <p class="footer-tagline">완벽한 분쇄, 차원이 다른 프리미엄</p>
                    </div>

                    <div class="footer-links">
                        <h4>제품</h4>
                        <ul>
                            <li><a href="<?php echo SITE_URL; ?>/products.php">음식물처리기</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></li>
                        </ul>
                    </div>

                    <div class="footer-links">
                        <h4>고객지원</h4>
                        <ul>
                            <li><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/빠른상담/">빠른 상담</a></li>
                        </ul>
                    </div>

                    <div class="footer-contact">
                        <h4>연락처</h4>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <span class="label">고객센터</span>
                                <a href="tel:1661-9038" class="value">1661-9038</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="footer-info">
                    <p>러블리키친 총판 대표이사 성정호 ｜ 사업자등록번호 306-08-91986</p>
                </div>
                <div class="footer-copyright">
                    <p>&copy; 2024 LOVELY KITCHEN. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <div class="phone-inquiry-container" id="phoneInquiry">
        <button class="close-btn" onclick="document.getElementById('phoneInquiry').classList.add('hidden');">&times;</button>
        <a href="tel:1661-9038" class="phone-inquiry-float"><img decoding="async" src="https://lkitchen.co.kr/wp-content/uploads/2025/10/전화문의.png" alt="전화문의" width="222" height="202"></a>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/products.js"></script>
</body>
</html>
