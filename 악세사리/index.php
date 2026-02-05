<?php
require_once dirname(__DIR__) . '/includes/config.php';
$accessories = getAccessoriesData();
// active인 항목만 표시
$accessories = array_filter($accessories, function($a) {
    return $a['active'] ?? true;
});
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>악세사리 | 러블리키친</title>
    <meta name="description" content="러블리키친 음식물처리기 악세사리 - 스플래시가드, 거름망, 스크래퍼, 스토퍼">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="icon" type="image/png" href="/수정/fhrh.png">
    <style>
        .accessory-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 50px;
        }
        .accessory-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .accessory-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        }
        .accessory-img {
            height: 250px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .accessory-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 20px;
        }
        .accessory-img i {
            font-size: 4rem;
            color: #3b82f6;
            opacity: 0.8;
        }
        .accessory-info {
            padding: 30px;
        }
        .accessory-info h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #051535;
            margin-bottom: 10px;
        }
        .accessory-info p {
            font-size: 0.95rem;
            color: #666;
            line-height: 1.7;
            margin-bottom: 20px;
        }
        .accessory-price {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 15px;
            border-top: 1px solid #edf2f7;
        }
        .price-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #3b82f6;
        }
        .price-shipping {
            font-size: 0.85rem;
            color: #888;
        }
        .accessory-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #051535;
            color: #fff;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .accessory-btn:hover {
            background: #3b82f6;
        }
        .accessory-btn-group {
            display: flex;
            gap: 8px;
        }
        .accessory-btn-detail {
            background: #3b82f6;
        }
        .accessory-btn-detail:hover {
            background: #2563eb;
        }
        .accessory-img-link {
            display: block;
            text-decoration: none;
        }

        .accessory-notice {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            border-radius: 16px;
            padding: 30px;
            margin-top: 50px;
            display: flex;
            align-items: center;
            gap: 20px;
            color: #fff;
        }
        .accessory-notice i {
            font-size: 2.5rem;
            opacity: 0.9;
        }
        .accessory-notice-content h4 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .accessory-notice-content p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .accessory-grid {
                grid-template-columns: 1fr;
            }
            .accessory-info h3 {
                font-size: 1.1rem;
                word-break: keep-all;
                line-height: 1.4;
            }
            .accessory-info p {
                font-size: 0.88rem;
                word-break: keep-all;
                line-height: 1.65;
            }
            .accessory-notice {
                flex-direction: column;
                text-align: center;
            }
            .accessory-notice-content h4 {
                word-break: keep-all;
            }
            .accessory-notice-content p {
                word-break: keep-all;
                line-height: 1.6;
            }
        }
    </style>
</head>
<body>
    <div class="preloader" id="preloader"><div class="preloader-inner"><div class="preloader-logo"><img src="/수정/fhrh.png" alt="Logo"></div><div class="preloader-progress"><div class="preloader-bar"></div></div></div></div>
    <div class="scroll-progress" id="scrollProgress"></div>

    <header class="header scrolled" id="header">
        <div class="header-container">
            <a href="/" class="logo"><img src="/수정/fhrh.png" alt="Lovely Kitchen" class="logo-img"></a>
            <nav class="nav-desktop">
                <ul class="nav-menu">
                    <li><a href="/회사소개/" class="nav-link">회사소개</a></li>
                    <li><a href="/친환경제품/" class="nav-link">친환경</a></li>
                    <li class="has-dropdown"><a href="/products.html" class="nav-link active">제품 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="/products.html">음식물처리기</a><a href="/싱크볼/">아콴테 싱크볼</a><a href="/악세사리/">악세사리</a></div></li>
                    <li class="has-dropdown"><a href="/사진갤러리/" class="nav-link">갤러리 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="/사진갤러리/">음식물처리기 갤러리</a><a href="/사진갤러리/싱크볼/">아콴테 싱크볼 갤러리</a></div></li>
                    <li class="has-dropdown"><a href="/음식물처리기-후기/" class="nav-link">구매후기 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="/음식물처리기-후기/">음식물처리기 후기</a><a href="/싱크볼-후기/">싱크볼 후기</a></div></li>
                    <li class="has-dropdown"><a href="/질문과-답변/" class="nav-link">고객지원 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="/질문과-답변/">질문과 답변</a><a href="/a-s-지원/">A/S 서비스 지원</a></div></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="tel:1661-9038" class="header-phone"><i class="fas fa-phone"></i><span>1661-9038</span></a>
                <a href="/빠른상담/" class="btn-consultation"><span>무료상담</span><i class="fas fa-arrow-right"></i></a>
                <button class="mobile-toggle" id="mobileToggle" aria-label="메뉴"><span></span><span></span><span></span></button>
            </div>
        </div>
    </header>

    <div class="mobile-nav" id="mobileNav"><div class="mobile-nav-header"><img src="/수정/fhrh.png" alt="Logo" class="mobile-logo"><button class="mobile-close" id="mobileClose"><i class="fas fa-times"></i></button></div><nav class="mobile-nav-content"><ul class="mobile-menu"><li><a href="/회사소개/">회사소개</a></li><li><a href="/친환경제품/">친환경</a></li><li class="has-submenu"><a href="#">제품 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="/products.html">음식물처리기</a></li><li><a href="/싱크볼/">아콴테 싱크볼</a></li><li><a href="/악세사리/">악세사리</a></li></ul></li><li class="has-submenu"><a href="#">갤러리 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="/사진갤러리/">음식물처리기 갤러리</a></li><li><a href="/사진갤러리/싱크볼/">아콴테 싱크볼 갤러리</a></li></ul></li><li class="has-submenu"><a href="#">구매후기 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="/음식물처리기-후기/">음식물처리기 후기</a></li><li><a href="/싱크볼-후기/">싱크볼 후기</a></li></ul></li><li class="has-submenu"><a href="#">고객지원 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="/질문과-답변/">질문과 답변</a></li><li><a href="/a-s-지원/">A/S 서비스 지원</a></li></ul></li></ul><div class="mobile-contact"><a href="tel:1661-9038" class="mobile-phone"><i class="fas fa-phone"></i> 1661-9038</a><a href="/빠른상담/" class="mobile-consult-btn">무료 상담 신청</a></div></nav></div>
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <main>
        <section class="page-hero"><div class="page-hero-bg"></div><div class="page-hero-overlay"></div><div class="container"><div class="page-hero-content" data-aos="fade-up"><nav class="breadcrumb"><a href="/">홈</a><span><i class="fas fa-chevron-right"></i></span><span>제품</span><span><i class="fas fa-chevron-right"></i></span><span class="current">악세사리</span></nav><h1 class="page-title">악세사리</h1><p class="page-subtitle">음식물처리기의 완벽한 파트너, 맞춤형 액세서리</p></div></div></section>

        <section class="content-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-tag">ACCESSORIES</span>
                    <h2 class="section-title">음식물처리기 악세사리</h2>
                    <p class="section-desc">간편하고, 성능은 최대로! 러블리키친과 함께 사용하세요</p>
                </div>

                <div class="accessory-grid">
                    <?php $delay = 0; foreach ($accessories as $acc): ?>
                    <div class="accessory-card" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <a href="https://lkitchen.co.kr<?php echo e($acc['detail_url'] ?? '#'); ?>" class="accessory-img-link">
                            <div class="accessory-img">
                                <img src="<?php echo e($acc['image']); ?>" alt="<?php echo e($acc['name']); ?>">
                            </div>
                        </a>
                        <div class="accessory-info">
                            <h3><a href="https://lkitchen.co.kr<?php echo e($acc['detail_url'] ?? '#'); ?>" style="color: inherit; text-decoration: none;"><?php echo e($acc['name']); ?></a></h3>
                            <p><?php echo e($acc['description']); ?></p>
                            <div class="accessory-price">
                                <div>
                                    <span class="price-value"><?php echo e($acc['price']); ?>원</span>
                                    <span class="price-shipping"><?php echo e($acc['shipping'] ?? ''); ?></span>
                                </div>
                                <div class="accessory-btn-group">
                                    <?php if (!empty($acc['detail_url'])): ?>
                                    <a href="https://lkitchen.co.kr<?php echo e($acc['detail_url']); ?>" class="accessory-btn accessory-btn-detail">자세히 보기 <i class="fas fa-arrow-right"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($acc['store_url'])): ?>
                                    <a href="<?php echo e($acc['store_url']); ?>" target="_blank" class="accessory-btn">스토어 구매 <i class="fas fa-shopping-cart"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $delay += 100; endforeach; ?>
                </div>

                <div class="accessory-notice" data-aos="fade-up">
                    <i class="fas fa-info-circle"></i>
                    <div class="accessory-notice-content">
                        <h4>악세사리 구매 안내</h4>
                        <p>악세사리 구매를 원하시면 고객센터(1661-9038)로 문의하시거나 상담 신청을 해주세요. 음식물처리기와 함께 구매 시 배송비가 무료입니다.</p>
                    </div>
                </div>

                <div class="cta-box" data-aos="fade-up" style="margin-top: 40px;">
                    <h3>악세사리 구매 문의</h3>
                    <p>필요한 악세사리에 대해 상담해 드립니다.</p>
                    <div class="cta-box-buttons">
                        <a href="/빠른상담/" class="btn-primary"><span>상담 신청</span><i class="fas fa-arrow-right"></i></a>
                        <a href="tel:1661-9038" class="btn-secondary"><i class="fas fa-phone"></i><span>1661-9038</span></a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer"><div class="footer-top"><div class="container"><div class="footer-grid"><div class="footer-brand"><a href="/" class="footer-logo"><img src="/수정/fhrh.png" alt="Lovely Kitchen"></a><p class="footer-tagline">완벽한 분쇄, 차원이 다른 프리미엄</p></div><div class="footer-links"><h4>제품</h4><ul><li><a href="/products.html">음식물처리기</a></li><li><a href="/싱크볼/">아콴테 싱크볼</a></li><li><a href="/악세사리/">악세사리</a></li></ul></div><div class="footer-links"><h4>고객지원</h4><ul><li><a href="/질문과-답변/">질문과 답변</a></li><li><a href="/a-s-지원/">A/S 서비스 지원</a></li><li><a href="/빠른상담/">빠른 상담</a></li></ul></div><div class="footer-contact"><h4>연락처</h4><div class="contact-item"><i class="fas fa-phone"></i><div><span class="label">고객센터</span><a href="tel:1661-9038" class="value">1661-9038</a></div></div></div></div></div></div><div class="footer-bottom"><div class="container"><div class="footer-info"><p>러블리키친 총판 대표이사 성정호 ｜ 사업자등록번호 306-08-91986</p></div><div class="footer-copyright"><p>&copy; 2024 LOVELY KITCHEN. All Rights Reserved.</p></div></div></div></footer>

    <div class="phone-inquiry-container" id="phoneInquiry">
        <button class="close-btn" onclick="document.getElementById('phoneInquiry').classList.add('hidden');">&times;</button>
        <a href="tel:1661-9038" class="phone-inquiry-float"><img decoding="async" src="https://lkitchen.co.kr/wp-content/uploads/2025/10/전화문의.png" alt="전화문의" width="222" height="202"></a>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
