<?php
/**
 * 러블리키친 특가페이지
 */
define('SITE_URL', 'http://115.68.223.124/lovelykitchen');

// JSON 데이터 로드
$jsonFile = __DIR__ . '/../admin/data/special.json';
$specialData = [];
if (file_exists($jsonFile)) {
    $specialData = json_decode(file_get_contents($jsonFile), true) ?: [];
}

$hero = $specialData['hero'] ?? [];
$products = array_filter($specialData['products'] ?? [], function($p) { return $p['active'] ?? false; });
$sinkbowls = array_filter($specialData['sinkbowls'] ?? [], function($s) { return $s['active'] ?? false; });
$multitrap = $specialData['multitrap'] ?? [];
$cta = $specialData['cta'] ?? [];

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>특가페이지 | 러블리키친 - 특별한 가격으로 만나는 프리미엄</title>
    <meta name="description" content="러블리키친 특가페이지 - 더 특별한 가격으로 만나는 프리미엄 음식물처리기와 싱크볼">
    <meta name="keywords" content="러블리키친, 특가, 음식물처리기, 싱크볼, 할인, 프로모션">

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
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png">

    <style>
        /* Special Deals Page Styles */
        .special-hero {
            background: linear-gradient(135deg, #051535 0%, #0a2558 100%);
            padding: 160px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .special-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            background-size: 50px 50px;
            opacity: 0.5;
        }

        .special-hero-content {
            position: relative;
            z-index: 1;
        }

        .special-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 107, 107, 0.2);
            border: 1px solid rgba(255, 107, 107, 0.5);
            color: #ff6b6b;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .special-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            color: #fff;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .special-title .highlight {
            color: #60a5fa;
        }

        .special-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.7);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Product Cards */
        .deals-section {
            padding: 80px 0;
            background: #f8fafc;
        }

        .deals-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .deal-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            margin-bottom: 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        @media (max-width: 968px) {
            .deal-card {
                grid-template-columns: 1fr;
            }
        }

        .deal-image {
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .deal-image img {
            max-width: 100%;
            max-height: 350px;
            object-fit: contain;
            transition: transform 0.5s ease;
        }

        .deal-card:hover .deal-image img {
            transform: scale(1.05);
        }

        .deal-model-name {
            text-align: center;
            margin-top: 20px;
        }

        .deal-model-name h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #051535;
            margin-bottom: 5px;
        }

        .deal-model-name p {
            font-size: 1.1rem;
            color: #64748b;
        }

        .deal-content {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .deal-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }

        .deal-tag {
            background: #e0f2fe;
            color: #0369a1;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .deal-price-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .original-price {
            font-size: 1rem;
            color: #92400e;
            text-decoration: line-through;
            margin-bottom: 8px;
        }

        .sale-price {
            font-size: 2rem;
            font-weight: 800;
            color: #b91c1c;
        }

        .deal-features {
            list-style: none;
            padding: 0;
            margin: 0 0 25px 0;
        }

        .deal-features li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            color: #475569;
            font-size: 0.95rem;
        }

        .deal-features li:last-child {
            border-bottom: none;
        }

        .deal-features li i {
            color: #22c55e;
            font-size: 0.9rem;
            margin-top: 3px;
        }

        .deal-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #051535 0%, #0a2558 100%);
            color: #fff;
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .deal-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(5, 21, 53, 0.3);
        }

        .deal-note {
            margin-top: 15px;
            font-size: 0.85rem;
            color: #94a3b8;
        }

        /* Accessories Section */
        .accessories-info {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            margin-top: 20px;
            border: 2px dashed #e2e8f0;
        }

        .accessories-info h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 15px;
        }

        /* Sink Bowl Section */
        .sinkbowl-section {
            padding: 80px 0;
            background: #fff;
        }

        .section-divider {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-divider h2 {
            font-size: clamp(1.5rem, 3vw, 2.5rem);
            font-weight: 700;
            color: #051535;
            margin-bottom: 10px;
        }

        .section-divider p {
            color: #64748b;
            font-size: 1.1rem;
        }

        .sinkbowl-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }

        .sinkbowl-card {
            background: #f8fafc;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .sinkbowl-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .sinkbowl-image {
            height: 250px;
            overflow: hidden;
        }

        .sinkbowl-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .sinkbowl-card:hover .sinkbowl-image img {
            transform: scale(1.1);
        }

        .sinkbowl-content {
            padding: 30px;
        }

        .sinkbowl-model {
            font-size: 1.3rem;
            font-weight: 700;
            color: #051535;
            margin-bottom: 10px;
        }

        .sinkbowl-model .free-install {
            color: #f97316;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .sinkbowl-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: #b91c1c;
            margin-bottom: 20px;
        }

        .sinkbowl-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sinkbowl-features li {
            padding: 8px 0;
            color: #475569;
            font-size: 0.9rem;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .sinkbowl-features li i {
            color: #3b82f6;
            margin-top: 3px;
        }

        /* Multi Trap Section */
        .multitrap-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #051535 0%, #0a2558 100%);
            color: #fff;
        }

        .multitrap-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        @media (max-width: 968px) {
            .multitrap-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
        }

        .multitrap-image {
            position: relative;
        }

        .multitrap-image img {
            max-width: 100%;
            border-radius: 20px;
        }

        .multitrap-badge {
            display: inline-block;
            background: rgba(96, 165, 250, 0.2);
            color: #60a5fa;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .multitrap-title {
            font-size: clamp(1.5rem, 3vw, 2.2rem);
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .multitrap-desc {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .multitrap-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 600px) {
            .multitrap-features {
                grid-template-columns: 1fr;
            }
        }

        .multitrap-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
        }

        .multitrap-feature i {
            color: #22c55e;
            font-size: 1.2rem;
        }

        .multitrap-feature span {
            font-size: 0.95rem;
        }

        /* CTA Section */
        .cta-section {
            padding: 80px 0;
            background: #f8fafc;
            text-align: center;
        }

        .cta-content h2 {
            font-size: clamp(1.5rem, 3vw, 2.5rem);
            font-weight: 700;
            color: #051535;
            margin-bottom: 15px;
        }

        .cta-content p {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .cta-btn.primary {
            background: linear-gradient(135deg, #051535 0%, #0a2558 100%);
            color: #fff;
        }

        .cta-btn.secondary {
            background: #fff;
            color: #051535;
            border: 2px solid #051535;
        }

        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-inner">
            <div class="preloader-logo">
                <img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Logo">
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
                <img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Lovely Kitchen" class="logo-img">
            </a>

            <nav class="nav-desktop">
                <ul class="nav-menu">
                    <li><a href="<?php echo SITE_URL; ?>/회사소개/" class="nav-link">회사소개</a></li>
                    <li class="has-dropdown">
                        <a href="<?php echo SITE_URL; ?>/products.html" class="nav-link">제품 <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="<?php echo SITE_URL; ?>/products.html">음식물처리기</a>
                            <a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a>
                            <a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a>
                        </div>
                    </li>
                    <li class="has-dropdown">
                        <a href="<?php echo SITE_URL; ?>/질문과-답변/" class="nav-link">고객지원 <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a>
                            <a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a>
                        </div>
                    </li>
                    <li class="has-dropdown">
                        <a href="<?php echo SITE_URL; ?>/음식물처리기-후기/" class="nav-link">후기게시판 <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="<?php echo SITE_URL; ?>/음식물처리기-후기/">음식물처리기 후기</a>
                            <a href="<?php echo SITE_URL; ?>/싱크볼-후기/">싱크볼 후기</a>
                        </div>
                    </li>
                    <li class="has-dropdown">
                        <a href="<?php echo SITE_URL; ?>/사진갤러리/" class="nav-link">사진 갤러리 <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="<?php echo SITE_URL; ?>/사진갤러리/">음식물처리기 갤러리</a>
                            <a href="<?php echo SITE_URL; ?>/사진갤러리/">아콴테 싱크볼 갤러리</a>
                        </div>
                    </li>
                    <li><a href="<?php echo SITE_URL; ?>/친환경제품/" class="nav-link">친환경제품</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <a href="tel:<?php echo e($cta['phone'] ?? '010-2464-4987'); ?>" class="header-phone">
                    <i class="fas fa-phone"></i>
                    <span><?php echo e($cta['phone'] ?? '010-2464-4987'); ?></span>
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
            <img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Logo" class="mobile-logo">
            <button class="mobile-close" id="mobileClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="mobile-nav-content">
            <ul class="mobile-menu">
                <li><a href="<?php echo SITE_URL; ?>/회사소개/">회사소개</a></li>
                <li class="has-submenu">
                    <a href="#">제품 <i class="fas fa-plus"></i></a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/products.html">음식물처리기</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#">고객지원 <i class="fas fa-plus"></i></a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#">후기게시판 <i class="fas fa-plus"></i></a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/">음식물처리기 후기</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/싱크볼-후기/">싱크볼 후기</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#">사진 갤러리 <i class="fas fa-plus"></i></a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/사진갤러리/">음식물처리기 갤러리</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/사진갤러리/">아콴테 싱크볼 갤러리</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo SITE_URL; ?>/친환경제품/">친환경제품</a></li>
            </ul>
            <div class="mobile-contact">
                <a href="tel:<?php echo e($cta['phone'] ?? '010-2464-4987'); ?>" class="mobile-phone">
                    <i class="fas fa-phone"></i> <?php echo e($cta['phone'] ?? '010-2464-4987'); ?>
                </a>
                <a href="<?php echo SITE_URL; ?>/빠른상담/" class="mobile-consult-btn">무료 상담 신청</a>
            </div>
        </nav>
    </div>
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <main>
        <!-- Special Hero Section -->
        <section class="special-hero">
            <div class="container">
                <div class="special-hero-content" data-aos="fade-up">
                    <div class="special-badge">
                        <i class="fas fa-fire"></i>
                        <span><?php echo e($hero['badge'] ?? 'SPECIAL PRICE'); ?></span>
                    </div>
                    <h1 class="special-title">
                        <?php echo $hero['title'] ?? '<span class="highlight">더 특별한 가격</span>으로 만나는<br>러블리 키친'; ?>
                    </h1>
                    <p class="special-subtitle"><?php echo e($hero['subtitle'] ?? '제품별 스펙 자세히 보기'); ?></p>
                </div>
            </div>
        </section>

        <!-- Deals Section - 음식물처리기 -->
        <section class="deals-section">
            <div class="deals-container">
                <?php foreach ($products as $product): ?>
                <div class="deal-card" data-aos="fade-up">
                    <div class="deal-image">
                        <img src="<?php echo e($product['image']); ?>" alt="<?php echo e($product['model']); ?> <?php echo e($product['name']); ?>">
                        <div class="deal-model-name">
                            <h3><?php echo e($product['model']); ?> <?php echo e($product['name']); ?></h3>
                            <p>(<?php echo e($product['name_en']); ?>)</p>
                        </div>
                    </div>
                    <div class="deal-content">
                        <div class="deal-tags">
                            <?php foreach ($product['tags'] as $tag): ?>
                            <span class="deal-tag"><?php echo e($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="deal-price-box">
                            <p class="original-price">소비자가 <?php echo e($product['original_price']); ?>원</p>
                            <p class="sale-price">특별행사가 <?php echo e($product['sale_price']); ?>원</p>
                        </div>
                        <ul class="deal-features">
                            <?php foreach ($product['features'] as $feature): ?>
                            <li><i class="fas fa-check"></i> <?php echo e($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (!empty($product['note'])): ?>
                        <p class="deal-note"><?php echo e($product['note']); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($product['show_accessory_info'])): ?>
                        <div class="accessories-info">
                            <h4><i class="fas fa-info-circle"></i> 옵션 안내</h4>
                            <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 15px;">식기세척기(매립형) 또는 로봇청소기 직수 연결 사용시, 옵션 1번, 2번 모두 선택하셔야 합니다.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Multi Trap Section -->
        <section class="multitrap-section">
            <div class="deals-container">
                <div class="multitrap-content">
                    <div class="multitrap-image" data-aos="fade-right">
                        <img src="<?php echo e($multitrap['image'] ?? ''); ?>" alt="4-Way LK 멀티트랩" class="innovation-image">
                    </div>
                    <div class="multitrap-text" data-aos="fade-left">
                        <span class="multitrap-badge"><?php echo e($multitrap['badge'] ?? ''); ?></span>
                        <h2 class="multitrap-title">
                            <?php echo $multitrap['title'] ?? '국내 최초 4-WAY LK 멀티트랩!<br>러블리키친에서 직접 개발 및 생산'; ?>
                        </h2>
                        <p class="multitrap-desc">
                            <?php echo e($multitrap['description'] ?? ''); ?>
                        </p>
                        <div class="multitrap-features">
                            <?php foreach ($multitrap['features'] ?? [] as $feature): ?>
                            <div class="multitrap-feature">
                                <i class="fas fa-check-circle"></i>
                                <span><?php echo e($feature); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sinkbowl Section -->
        <?php if (!empty($sinkbowls)): ?>
        <section class="sinkbowl-section">
            <div class="deals-container">
                <div class="section-divider" data-aos="fade-up">
                    <h2>아콴테 프리미엄 사각싱크볼</h2>
                    <p>전문가 무료시공 포함 - 최대 20만원 절약!</p>
                </div>

                <div class="sinkbowl-grid">
                    <?php foreach ($sinkbowls as $index => $sink): ?>
                    <div class="sinkbowl-card" data-aos="fade-up" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
                        <div class="sinkbowl-image">
                            <img src="<?php echo e($sink['image']); ?>" alt="<?php echo e($sink['model']); ?> 싱크볼">
                        </div>
                        <div class="sinkbowl-content">
                            <h3 class="sinkbowl-model">
                                <?php echo e($sink['model']); ?>
                                <span class="free-install">전문가무료시공포함</span>
                            </h3>
                            <p class="sinkbowl-price">소비자가 <?php echo e($sink['price']); ?>원</p>
                            <ul class="sinkbowl-features">
                                <?php
                                $icons = ['fa-ruler-combined', 'fa-shield-alt', 'fa-paint-brush', 'fa-spray-can', 'fa-volume-mute'];
                                foreach ($sink['features'] as $i => $feature):
                                    $icon = $icons[$i % count($icons)];
                                ?>
                                <li><i class="fas <?php echo $icon; ?>"></i> <?php echo e($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <p style="text-align: center; margin-top: 30px; color: #64748b; font-size: 0.9rem;">
                    ※ 프리미엄 스텐레스 배수구 기본 포함이며, 수전은 별도 구매입니다.<br>
                    상품 설명 하단에서 선택 가능하며 타사 수전은 누수 이슈로 연결해드리지 않고 있습니다.
                </p>
            </div>
        </section>
        <?php endif; ?>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="deals-container">
                <div class="cta-content" data-aos="fade-up">
                    <h2><?php echo e($cta['title'] ?? '지금 바로 무료 상담 받아보세요'); ?></h2>
                    <p><?php echo e($cta['subtitle'] ?? '전문 상담원이 친절하게 안내해 드립니다'); ?></p>
                    <div class="cta-buttons">
                        <a href="tel:<?php echo e($cta['phone'] ?? '010-2464-4987'); ?>" class="cta-btn primary">
                            <i class="fas fa-phone"></i>
                            <span>전화 상담 <?php echo e($cta['phone'] ?? '010-2464-4987'); ?></span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/빠른상담/" class="cta-btn secondary">
                            <i class="fas fa-comment-dots"></i>
                            <span>빠른 상담 신청</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-top">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-brand">
                        <a href="<?php echo SITE_URL; ?>/" class="footer-logo">
                            <img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Lovely Kitchen">
                        </a>
                        <p class="footer-tagline">완벽한 분쇄, 차원이 다른 프리미엄</p>
                        <div class="footer-social">
                            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                            <a href="#" aria-label="Blog"><i class="fab fa-blogger-b"></i></a>
                            <a href="#" aria-label="KakaoTalk"><i class="fas fa-comment"></i></a>
                        </div>
                    </div>

                    <div class="footer-links">
                        <h4>제품</h4>
                        <ul>
                            <li><a href="<?php echo SITE_URL; ?>/products.html">음식물처리기</a></li>
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
                                <a href="tel:<?php echo e($cta['phone'] ?? '010-2464-4987'); ?>" class="value"><?php echo e($cta['phone'] ?? '010-2464-4987'); ?></a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <span class="label">이메일</span>
                                <a href="mailto:cs.lovelykitchen@gmail.com" class="value">cs.lovelykitchen@gmail.com</a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-fax"></i>
                            <div>
                                <span class="label">팩스</span>
                                <span class="value">070-4015-4515</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="footer-info">
                    <p>러블리키친 총판 대표이사 성정호 | 사업자등록번호 306-08-91986</p>
                    <p>본사: 서울시 서초구 반포대로22길 35, 2층 2002호</p>
                </div>
                <div class="footer-copyright">
                    <p>&copy; 2024 LOVELY KITCHEN. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
