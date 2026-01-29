<?php
/**
 * 아콴테 싱크볼 페이지
 * 관리자 페이지의 special.json 싱크볼 데이터와 연동
 */
require_once dirname(__DIR__) . '/includes/config.php';

// 싱크볼 데이터 로드
$specialData = readJsonData('special.json');
$sinkbowls = array_filter($specialData['sinkbowls'] ?? [], function($s) {
    return $s['active'] ?? true;
});

// 싱크볼 계산식 데이터 로드
$settings = readJsonData('settings.json');
$calcRules = $settings['sinkbowl_calc_rules'] ?? [
    'condition_min_top_height' => 580,
    'condition_max_hole_height' => 490,
    'rules' => [
        ['model' => '860', 'min_cabinet_width' => 790, 'max_hole_width' => 830],
        ['model' => '900', 'min_cabinet_width' => 850, 'max_hole_width' => 870],
        ['model' => '980', 'min_cabinet_width' => 940, 'max_hole_width' => 950]
    ]
];

// 뱃지 매핑
$badges = ['COMPACT', 'STANDARD', 'PREMIUM', 'FLAGSHIP'];
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>아콴테 싱크볼 | 러블리키친</title>
    <meta name="description" content="러블리키친 아콴테 프리미엄 싱크볼 시리즈 - AQ-860NE, AQ-900NE, AQ-980NE">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="icon" type="image/png" href="http://115.68.223.124/lovelykitchen/수정/fhrh.png">
    <style>
        .sink-hero {
            position: relative;
            padding: 180px 0 100px;
            background: linear-gradient(135deg, #051535 0%, #0a2150 100%);
            overflow: hidden;
            text-align: center;
        }
        .sink-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://lkitchen.co.kr/wp-content/uploads/2025/12/5-1024x768.jpg') center/cover no-repeat;
            opacity: 0.2;
        }
        .sink-hero .container {
            position: relative;
            z-index: 2;
        }
        .sink-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 1rem;
        }
        .sink-hero p {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.8);
        }

        .sink-products {
            padding: 80px 0;
            background: #f7fafc;
        }

        .sink-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        .sink-card-grid {
            display: flex;
            flex-wrap: wrap;
        }
        .sink-card-img {
            flex: 1;
            min-width: 300px;
        }
        .sink-card-img img {
            width: 100%;
            height: 100%;
            min-height: 350px;
            object-fit: cover;
            display: block;
        }
        .sink-card-info {
            flex: 1;
            min-width: 300px;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .sink-badge {
            display: inline-block;
            background: #3b82f6;
            color: #fff;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 15px;
            width: fit-content;
        }
        .sink-card h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #051535;
            margin-bottom: 10px;
        }
        .sink-card .subtitle {
            font-size: 1rem;
            color: #666;
            margin-bottom: 20px;
        }
        .sink-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #b91c1c;
            margin-bottom: 20px;
        }
        .sink-specs {
            margin-bottom: 25px;
        }
        .sink-specs h4 {
            font-size: 0.95rem;
            color: #333;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3b82f6;
            display: inline-block;
        }
        .sink-specs ul {
            list-style: none;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .sink-specs li {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #555;
        }
        .sink-specs li i {
            color: #3b82f6;
            width: 18px;
        }
        .sink-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #3b82f6;
            color: #fff;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            width: fit-content;
        }
        .sink-btn:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        /* 사이즈 가이드 섹션 */
        .size-guide {
            padding: 80px 0;
            background: #fff;
        }
        .size-guide-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
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
            background: #051535;
            color: #fff;
            font-weight: 600;
        }
        .size-guide-table tr:last-child td {
            border-bottom: none;
        }
        .size-guide-table tr:hover td {
            background: #f8fafc;
        }

        .sink-gallery {
            padding: 80px 0;
            background: #f7fafc;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 40px;
        }
        .gallery-grid img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 12px;
            transition: transform 0.3s ease;
        }
        .gallery-grid img:hover {
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .sink-hero h1 { font-size: 1.5rem; word-break: keep-all; line-height: 1.4; padding: 0 15px; }
            .sink-hero p { font-size: 0.9rem; word-break: keep-all; line-height: 1.6; padding: 0 20px; }
            .sink-card-grid { flex-direction: column !important; }
            .sink-card-info { padding: 25px; }
            .sink-card h3 { font-size: 1.3rem; word-break: keep-all; }
            .sink-specs ul { grid-template-columns: 1fr; }
            .gallery-grid { grid-template-columns: repeat(2, 1fr); }
            .size-guide-table { font-size: 0.85rem; }
            .size-guide-table th, .size-guide-table td { padding: 10px 8px; }
        }
    </style>
</head>
<body>
    <div class="preloader" id="preloader"><div class="preloader-inner"><div class="preloader-logo"><img src="http://115.68.223.124/lovelykitchen/수정/fhrh.png" alt="Logo"></div><div class="preloader-progress"><div class="preloader-bar"></div></div></div></div>
    <div class="scroll-progress" id="scrollProgress"></div>

    <header class="header scrolled" id="header">
        <div class="header-container">
            <a href="<?php echo SITE_URL; ?>/" class="logo"><img src="http://115.68.223.124/lovelykitchen/수정/fhrh.png" alt="Lovely Kitchen" class="logo-img"></a>
            <nav class="nav-desktop">
                <ul class="nav-menu">
                    <li><a href="<?php echo SITE_URL; ?>/회사소개/" class="nav-link">회사소개</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/친환경제품/" class="nav-link">친환경</a></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/products.php" class="nav-link active">제품 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/products.php">음식물처리기</a><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/사진갤러리/" class="nav-link">갤러리 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/사진갤러리/">음식물처리기 갤러리</a><a href="<?php echo SITE_URL; ?>/사진갤러리/싱크볼/">아콴테 싱크볼 갤러리</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/" class="nav-link">구매후기 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/">음식물처리기 후기</a><a href="<?php echo SITE_URL; ?>/싱크볼-후기/">싱크볼 후기</a></div></li>
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

    <div class="mobile-nav" id="mobileNav"><div class="mobile-nav-header"><img src="http://115.68.223.124/lovelykitchen/수정/fhrh.png" alt="Logo" class="mobile-logo"><button class="mobile-close" id="mobileClose"><i class="fas fa-times"></i></button></div><nav class="mobile-nav-content"><ul class="mobile-menu"><li><a href="<?php echo SITE_URL; ?>/회사소개/">회사소개</a></li><li><a href="<?php echo SITE_URL; ?>/친환경제품/">친환경</a></li><li class="has-submenu"><a href="#">제품 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/products.php">음식물처리기</a></li><li><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a></li><li><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></li></ul></li><li class="has-submenu"><a href="#">갤러리 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/사진갤러리/">음식물처리기 갤러리</a></li><li><a href="<?php echo SITE_URL; ?>/사진갤러리/싱크볼/">아콴테 싱크볼 갤러리</a></li></ul></li><li class="has-submenu"><a href="#">구매후기 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/">음식물처리기 후기</a></li><li><a href="<?php echo SITE_URL; ?>/싱크볼-후기/">싱크볼 후기</a></li></ul></li><li class="has-submenu"><a href="#">고객지원 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a></li><li><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></li></ul></li></ul><div class="mobile-contact"><a href="tel:1661-9038" class="mobile-phone"><i class="fas fa-phone"></i> 1661-9038</a><a href="<?php echo SITE_URL; ?>/빠른상담/" class="mobile-consult-btn">무료 상담 신청</a></div></nav></div>
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <main>
        <section class="sink-hero">
            <div class="container">
                <h1 data-aos="fade-up">아콴테 프리미엄 싱크볼 시리즈</h1>
                <p data-aos="fade-up" data-aos-delay="100">쉽지만 완벽한 선택, 집집마다 다른 주방에 꼭 맞는 사이즈</p>
            </div>
        </section>

        <section class="sink-products">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-tag">AQUANTE SINK SERIES</span>
                    <h2 class="section-title">싱크볼 라인업</h2>
                    <p class="section-desc">주방 환경에 맞는 다양한 사이즈의 프리미엄 싱크볼</p>
                </div>

                <?php
                $index = 0;
                foreach ($sinkbowls as $sink):
                    $isReverse = $index % 2 === 1;
                    $badge = $badges[$index % count($badges)] ?? 'PREMIUM';
                ?>
                <div class="sink-card" data-aos="fade-up">
                    <div class="sink-card-grid"<?php if ($isReverse): ?> style="flex-direction: row-reverse;"<?php endif; ?>>
                        <div class="sink-card-img">
                            <img src="<?php echo e($sink['image']); ?>" alt="<?php echo e($sink['model']); ?> 사각싱크볼">
                        </div>
                        <div class="sink-card-info">
                            <span class="sink-badge"><?php echo $badge; ?></span>
                            <h3><?php echo e($sink['model']); ?> 사각싱크볼</h3>
                            <p class="subtitle"><?php echo e($sink['size']); ?></p>
                            <p class="sink-price"><?php echo e($sink['price']); ?>원 (전문가무료시공포함)</p>
                            <div class="sink-specs">
                                <h4>제품 사양</h4>
                                <ul>
                                    <?php
                                    $icons = ['fa-ruler-combined', 'fa-layer-group', 'fa-shield-alt', 'fa-spray-can', 'fa-volume-mute', 'fa-tools'];
                                    foreach ($sink['features'] ?? [] as $i => $feature):
                                        $icon = $icons[$i % count($icons)];
                                    ?>
                                    <li><i class="fas <?php echo $icon; ?>"></i> <?php echo e($feature); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <a href="<?php echo SITE_URL; ?>/싱크볼/<?php echo strtolower($sink['model']); ?>.html" class="sink-btn">제품 상세보기 <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <?php
                $index++;
                endforeach;
                ?>
            </div>
        </section>

        <!-- 사이즈 선택 가이드 -->
        <section class="size-guide">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-tag">SIZE GUIDE</span>
                    <h2 class="section-title">싱크볼 사이즈 선택 가이드</h2>
                    <p class="section-desc">하부장 가로 사이즈에 맞는 싱크볼을 선택하세요</p>
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
                        <?php foreach ($calcRules['rules'] ?? [] as $rule): ?>
                        <tr>
                            <td><strong>AQ-<?php echo e($rule['model']); ?>NE</strong></td>
                            <td><?php echo e($rule['min_cabinet_width']); ?>mm 이상</td>
                            <td><?php echo e($rule['max_hole_width']); ?>mm 이하</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top: 20px; padding: 20px; background: #fef3c7; border-radius: 12px; text-align: center;" data-aos="fade-up">
                    <p style="color: #92400e; font-size: 0.9rem; margin: 0;">
                        <i class="fas fa-info-circle"></i>
                        상판세로가 <?php echo e($calcRules['condition_min_top_height'] ?? 580); ?>mm 미만이거나 타공세로가 <?php echo e($calcRules['condition_max_hole_height'] ?? 490); ?>mm 초과인 경우 상담이 필요합니다.
                    </p>
                </div>
            </div>
        </section>

        <section class="sink-gallery">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-tag">INSTALLATION GALLERY</span>
                    <h2 class="section-title">시공 스토리</h2>
                    <p class="section-desc">아콴테 싱크볼 설치 사례를 확인해보세요</p>
                </div>

                <div class="gallery-grid" data-aos="fade-up">
                    <img src="https://lkitchen.co.kr/wp-content/uploads/2025/09/8H2A9604-1-1024x683.jpg" alt="싱크볼 시공 사진" loading="lazy">
                    <img src="https://lkitchen.co.kr/wp-content/uploads/2025/09/8H2A9541-1-1024x683.jpg" alt="싱크볼 시공 사진" loading="lazy">
                    <img src="https://lkitchen.co.kr/wp-content/uploads/2025/09/8H2A9450-1-1024x683.jpg" alt="싱크볼 시공 사진" loading="lazy">
                    <img src="https://lkitchen.co.kr/wp-content/uploads/2025/09/8H2A9623-1024x683.jpg" alt="싱크볼 시공 사진" loading="lazy">
                    <img src="https://lkitchen.co.kr/wp-content/uploads/2025/09/8H2A9537-1024x683.jpg" alt="싱크볼 시공 사진" loading="lazy">
                </div>

                <div class="cta-box" data-aos="fade-up" style="margin-top: 60px;">
                    <h3>아콴테 싱크볼이 궁금하신가요?</h3>
                    <p>지금 바로 상담받고 특별한 혜택을 받아보세요.</p>
                    <div class="cta-box-buttons">
                        <a href="<?php echo SITE_URL; ?>/빠른상담/" class="btn-primary"><span>상담 신청</span><i class="fas fa-arrow-right"></i></a>
                        <a href="tel:1661-9038" class="btn-secondary"><i class="fas fa-phone"></i><span>1661-9038</span></a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer"><div class="footer-top"><div class="container"><div class="footer-grid"><div class="footer-brand"><a href="<?php echo SITE_URL; ?>/" class="footer-logo"><img src="http://115.68.223.124/lovelykitchen/수정/fhrh.png" alt="Lovely Kitchen"></a><p class="footer-tagline">완벽한 분쇄, 차원이 다른 프리미엄</p></div><div class="footer-links"><h4>제품</h4><ul><li><a href="<?php echo SITE_URL; ?>/products.php">음식물처리기</a></li><li><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a></li><li><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></li></ul></div><div class="footer-links"><h4>고객지원</h4><ul><li><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a></li><li><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></li><li><a href="<?php echo SITE_URL; ?>/빠른상담/">빠른 상담</a></li></ul></div><div class="footer-contact"><h4>연락처</h4><div class="contact-item"><i class="fas fa-phone"></i><div><span class="label">고객센터</span><a href="tel:1661-9038" class="value">1661-9038</a></div></div></div></div></div></div><div class="footer-bottom"><div class="container"><div class="footer-info"><p>러블리키친 총판 대표이사 성정호 ｜ 사업자등록번호 306-08-91986</p></div><div class="footer-copyright"><p>&copy; 2024 LOVELY KITCHEN. All Rights Reserved.</p></div></div></div></footer>

    <div class="phone-inquiry-container" id="phoneInquiry">
        <button class="close-btn" onclick="document.getElementById('phoneInquiry').classList.add('hidden');">&times;</button>
        <a href="tel:1661-9038" class="phone-inquiry-float"><img decoding="async" src="https://lkitchen.co.kr/wp-content/uploads/2025/10/전화문의.png" alt="전화문의" width="222" height="202"></a>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
