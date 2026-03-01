<?php
/**
 * 러블리키친 특가페이지 - 싱크볼
 */
require_once __DIR__ . '/../includes/config.php';

// JSON 데이터 로드
$jsonFile = __DIR__ . '/../admin/data/special.json';
$specialData = [];
if (file_exists($jsonFile)) {
    $specialData = json_decode(file_get_contents($jsonFile), true) ?: [];
}

$sinkbowls = array_filter($specialData['sinkbowls'] ?? [], function($s) { return $s['active'] ?? false; });
$cta = $specialData['cta'] ?? [];
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>아콴테 싱크볼 특가 | 러블리키친 - 전문가 무료시공 포함</title>
    <meta name="description" content="러블리키친 특가페이지 - 아콴테 프리미엄 사각싱크볼, 전문가 무료시공 포함 특별가">
    <meta name="keywords" content="러블리키친, 특가, 싱크볼, 아콴테, 사각싱크볼, 무료시공">

    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

    <!-- Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/수정/fhrh.png">

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
            background: rgba(14, 165, 233, 0.2);
            border: 1px solid rgba(14, 165, 233, 0.5);
            color: #0ea5e9;
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
            color: #0ea5e9;
        }

        .special-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.7);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Deals Section */
        .deals-section {
            padding: 80px 0;
            background: #f8fafc;
        }

        .deals-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Sinkbowl Card Styles - 음식물처리기 스타일과 동일 */
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
            object-fit: cover;
            border-radius: 12px;
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

        .deal-tag.highlight {
            background: #fef3c7;
            color: #92400e;
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
            color: #0ea5e9;
            font-size: 0.9rem;
            margin-top: 3px;
        }

        .deal-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
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
            box-shadow: 0 10px 30px rgba(14, 165, 233, 0.3);
        }

        .deal-note {
            margin-top: 15px;
            font-size: 0.85rem;
            color: #94a3b8;
        }

        /* Size Guide Section */
        .size-guide-section {
            padding: 80px 0;
            background: #fff;
        }

        .section-divider {
            text-align: center;
            margin-bottom: 50px;
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

        .size-guide-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .size-guide-table th,
        .size-guide-table td {
            padding: 15px 20px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .size-guide-table th {
            background: #0ea5e9;
            color: #fff;
            font-weight: 600;
        }

        .size-guide-table tr:last-child td {
            border-bottom: none;
        }

        .size-guide-table tr:hover td {
            background: #f8fafc;
        }

        .calc-link-box {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            border-radius: 16px;
            padding: 30px;
            margin-top: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .calc-link-text {
            color: #fff;
        }

        .calc-link-text h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .calc-link-text p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
        }

        .calc-link-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            color: #0284c7;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .calc-link-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        /* CTA Section */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #051535 0%, #0a2558 100%);
            text-align: center;
        }

        .cta-content h2 {
            font-size: clamp(1.5rem, 3vw, 2.5rem);
            font-weight: 700;
            color: #fff;
            margin-bottom: 15px;
        }

        .cta-content p {
            color: rgba(255, 255, 255, 0.7);
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
            background: #0ea5e9;
            color: #fff;
        }

        .cta-btn.secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        /* Back Link */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.95rem;
            margin-bottom: 30px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #fff;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .special-hero {
                padding: 120px 0 60px;
            }

            .special-subtitle {
                font-size: 1rem;
                padding: 0 15px;
            }

            .deals-section {
                padding: 50px 0;
            }

            .deals-container {
                padding: 0 15px;
            }

            .deal-card {
                margin-bottom: 40px;
                border-radius: 15px;
            }

            .deal-image {
                padding: 25px;
            }

            .deal-image img {
                max-height: 250px;
            }

            .deal-model-name h3 {
                font-size: 1.3rem;
            }

            .deal-content {
                padding: 25px;
            }

            .deal-tags {
                gap: 6px;
            }

            .deal-tag {
                padding: 5px 10px;
                font-size: 0.75rem;
            }

            .deal-price-box {
                padding: 20px;
            }

            .original-price {
                font-size: 0.9rem;
            }

            .sale-price {
                font-size: 1.6rem;
            }

            .deal-features li {
                font-size: 0.88rem;
                padding: 8px 0;
            }

            .deal-btn {
                width: 100%;
                padding: 14px 24px;
            }

            .size-guide-section {
                padding: 50px 0;
            }

            .size-guide-table {
                font-size: 0.85rem;
            }

            .size-guide-table th,
            .size-guide-table td {
                padding: 10px 8px;
            }

            .calc-link-box {
                flex-direction: column;
                text-align: center;
            }

            .calc-link-btn {
                width: 100%;
                justify-content: center;
            }

            .cta-section {
                padding: 50px 0;
            }

            .cta-buttons {
                flex-direction: column;
                gap: 12px;
            }

            .cta-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .special-hero {
                padding: 100px 0 50px;
            }

            .special-badge {
                font-size: 0.8rem;
                padding: 6px 14px;
            }

            .deal-image {
                padding: 20px;
            }

            .deal-image img {
                max-height: 200px;
            }

            .deal-content {
                padding: 20px;
            }

            .sale-price {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-inner">
            <div class="preloader-logo">
                <img src="/수정/fhrh.png" alt="Logo">
            </div>
            <div class="preloader-progress">
                <div class="preloader-bar"></div>
            </div>
        </div>
    </div>

    <!-- Scroll Progress -->
    <div class="scroll-progress" id="scrollProgress"></div>

    <!-- Header -->
    <header class="header scrolled" id="header">
        <div class="header-container">
            <a href="<?php echo SITE_URL; ?>/" class="logo">
                <img src="/수정/fhrh.png" alt="Lovely Kitchen" class="logo-img">
            </a>

            <nav class="nav-desktop">
                <ul class="nav-menu">
                    <li><a href="<?php echo SITE_URL; ?>/회사소개/" class="nav-link">회사소개</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/친환경제품/" class="nav-link">친환경</a></li>
                    <li class="has-dropdown">
                        <a href="<?php echo SITE_URL; ?>/products.html" class="nav-link">제품 <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="<?php echo SITE_URL; ?>/products.html">음식물처리기</a>
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
            <img src="/수정/fhrh.png" alt="Logo" class="mobile-logo">
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
                        <li><a href="<?php echo SITE_URL; ?>/products.html">음식물처리기</a></li>
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
                    <a href="./" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                        <span>특가페이지 메인으로</span>
                    </a>
                    <div class="special-badge">
                        <i class="fas fa-sink"></i>
                        <span>SINKBOWL SPECIAL</span>
                    </div>
                    <h1 class="special-title">
                        <span class="highlight">아콴테 프리미엄</span><br>사각싱크볼
                    </h1>
                    <p class="special-subtitle">폭포수 수전 포함 | 전문가 무료 시공</p>
                </div>
            </div>
        </section>

        <!-- Deals Section - 싱크볼 -->
        <section class="deals-section">
            <div class="deals-container">
                <?php
                $badges = ['COMPACT', 'BEST', 'PREMIUM'];
                $index = 0;
                foreach ($sinkbowls as $sink):
                    $badge = $badges[$index % count($badges)] ?? 'PREMIUM';
                    $isReverse = $index % 2 === 1;
                ?>
                <div class="deal-card" data-aos="fade-up"<?php if ($isReverse): ?> style="direction: rtl;"<?php endif; ?>>
                    <div class="deal-image"<?php if ($isReverse): ?> style="direction: ltr;"<?php endif; ?>>
                        <img src="<?php echo e($sink['image']); ?>" alt="<?php echo e($sink['model']); ?> 사각싱크볼">
                        <div class="deal-model-name">
                            <h3><?php echo e($sink['model']); ?> 사각싱크볼</h3>
                            <p><?php echo e($sink['size'] ?? ''); ?></p>
                        </div>
                    </div>
                    <div class="deal-content"<?php if ($isReverse): ?> style="direction: ltr;"<?php endif; ?>>
                        <div class="deal-tags">
                            <span class="deal-tag"><?php echo $badge; ?></span>
                            <span class="deal-tag highlight">폭포수 수전 포함 | 전문가 무료 시공</span>
                        </div>
                        <div class="deal-price-box">
                            <p class="original-price">소비자가 <?php echo e($sink['original_price']); ?>원</p>
                            <p class="sale-price">특별행사가 <?php echo e($sink['sale_price']); ?>원</p>
                        </div>
                        <ul class="deal-features">
                            <?php
                            $icons = ['fa-ruler-combined', 'fa-layer-group', 'fa-shield-alt', 'fa-spray-can', 'fa-volume-mute'];
                            foreach ($sink['features'] ?? [] as $i => $feature):
                                $icon = $icons[$i % count($icons)];
                            ?>
                            <li><i class="fas <?php echo $icon; ?>"></i> <?php echo e($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="<?php echo strtolower($sink['model']); ?>.html" class="deal-btn">
                            <span>제품 상세보기</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <p class="deal-note">※ 폭포수 수전 포함, 배수구는 별도 구매</p>
                    </div>
                </div>
                <?php
                $index++;
                endforeach;
                ?>
            </div>
        </section>

        <!-- Size Guide Section -->
        <section class="size-guide-section">
            <div class="deals-container">
                <div class="section-divider" data-aos="fade-up">
                    <h2>싱크볼 사이즈 선택 가이드</h2>
                    <p>하부장 가로 사이즈에 맞는 싱크볼을 선택하세요</p>
                </div>

                <table class="size-guide-table" data-aos="fade-up">
                    <thead>
                        <tr>
                            <th>싱크볼 모델</th>
                            <th>최소 하부장 가로</th>
                            <th>최대 타공 가로</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>AQ-860NE</strong></td>
                            <td>790mm 이상</td>
                            <td>830mm 이하</td>
                        </tr>
                        <tr>
                            <td><strong>AQ-900NE</strong></td>
                            <td>850mm 이상</td>
                            <td>870mm 이하</td>
                        </tr>
                        <tr>
                            <td><strong>AQ-980NE</strong></td>
                            <td>940mm 이상</td>
                            <td>950mm 이하</td>
                        </tr>
                    </tbody>
                </table>

                <div class="calc-link-box" data-aos="fade-up">
                    <div class="calc-link-text">
                        <h3><i class="fas fa-calculator"></i> 견적 계산기</h3>
                        <p>치수를 입력하면 설치 가능한 싱크볼을 자동으로 추천해드립니다</p>
                    </div>
                    <a href="싱크볼-견적/" class="calc-link-btn">
                        <span>견적 계산하기</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div style="margin-top: 30px; padding: 20px; background: #fef3c7; border-radius: 12px; text-align: center;" data-aos="fade-up">
                    <p style="color: #92400e; font-size: 0.9rem; margin: 0;">
                        <i class="fas fa-info-circle"></i>
                        상판세로가 580mm 미만이거나 타공세로가 490mm 초과인 경우 상담이 필요합니다.
                    </p>
                </div>
            </div>
        </section>

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
                            <img src="/수정/fhrh.png" alt="Lovely Kitchen">
                        </a>
                        <p class="footer-tagline">완벽한 분쇄, 차원이 다른 프리미엄</p>
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
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="footer-info">
                    <p>러블리키친 총판 대표이사 성정호 | 사업자등록번호 306-08-91986</p>
                </div>
                <div class="footer-copyright">
                    <p>&copy; 2024 LOVELY KITCHEN. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
