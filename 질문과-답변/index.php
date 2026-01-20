<?php
require_once dirname(__DIR__) . '/includes/config.php';
$faqData = getFaqData();
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>질문과 답변 | <?php echo e($siteSettings['site_name']); ?></title>
    <meta name="description" content="러블리키친 음식물처리기 자주 묻는 질문과 답변">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="icon" type="image/png" href="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png">
    <style>
        .faq-section { margin-top: 60px; }
        .faq-list { max-width: 900px; margin: 0 auto; }
        .faq-item {
            background: #fff;
            border-radius: 12px;
            margin-bottom: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .faq-question {
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-weight: 600;
            color: #051535;
            transition: background 0.3s;
        }
        .faq-question:hover { background: #f8f9fa; }
        .faq-question i { transition: transform 0.3s; color: #3498db; }
        .faq-item.active .faq-question i { transform: rotate(180deg); }
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background: #f8f9fa;
        }
        .faq-answer-inner {
            padding: 20px 24px;
            color: #666;
            line-height: 1.8;
            white-space: pre-wrap;
        }
        .faq-item.active .faq-answer { max-height: 500px; }
        .faq-category {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-right: 10px;
            font-weight: 500;
        }
        .faq-empty {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .faq-empty i { font-size: 48px; margin-bottom: 20px; color: #ddd; }
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
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/products.html" class="nav-link">제품 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/products.html">음식물처리기</a><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/질문과-답변/" class="nav-link active">고객지원 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/" class="nav-link">후기게시판 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/">음식물처리기 후기</a><a href="<?php echo SITE_URL; ?>/싱크볼-후기/">싱크볼 후기</a></div></li>
                    <li class="has-dropdown"><a href="<?php echo SITE_URL; ?>/사진갤러리/" class="nav-link">사진 갤러리 <i class="fas fa-chevron-down"></i></a><div class="dropdown-menu"><a href="<?php echo SITE_URL; ?>/사진갤러리/">음식물처리기 갤러리</a><a href="<?php echo SITE_URL; ?>/사진갤러리/싱크볼/">아콴테 싱크볼 갤러리</a></div></li>
                    <li><a href="<?php echo SITE_URL; ?>/친환경제품/" class="nav-link">친환경제품</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/특가페이지/" class="nav-link" style="color: #ff6b6b; font-weight: 600;">특가페이지</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="tel:<?php echo e($siteSettings['phone']); ?>" class="header-phone"><i class="fas fa-phone"></i><span><?php echo e($siteSettings['phone']); ?></span></a>
                <a href="<?php echo SITE_URL; ?>/빠른상담/" class="btn-consultation"><span>무료상담</span><i class="fas fa-arrow-right"></i></a>
                <button class="mobile-toggle" id="mobileToggle" aria-label="메뉴"><span></span><span></span><span></span></button>
            </div>
        </div>
    </header>

    <div class="mobile-nav" id="mobileNav"><div class="mobile-nav-header"><img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Logo" class="mobile-logo"><button class="mobile-close" id="mobileClose"><i class="fas fa-times"></i></button></div><nav class="mobile-nav-content"><ul class="mobile-menu"><li><a href="<?php echo SITE_URL; ?>/회사소개/">회사소개</a></li><li class="has-submenu"><a href="#">제품 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/products.html">음식물처리기</a></li><li><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a></li><li><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></li></ul></li><li class="has-submenu"><a href="#">고객지원 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a></li><li><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></li></ul></li><li class="has-submenu"><a href="#">후기게시판 <i class="fas fa-plus"></i></a><ul class="submenu"><li><a href="<?php echo SITE_URL; ?>/음식물처리기-후기/">음식물처리기 후기</a></li><li><a href="<?php echo SITE_URL; ?>/싱크볼-후기/">싱크볼 후기</a></li></ul></li><li><a href="<?php echo SITE_URL; ?>/친환경제품/">친환경제품</a></li><li><a href="<?php echo SITE_URL; ?>/특가페이지/" style="color: #ff6b6b; font-weight: 600;">특가페이지</a></li></ul><div class="mobile-contact"><a href="tel:<?php echo e($siteSettings['phone']); ?>" class="mobile-phone"><i class="fas fa-phone"></i> <?php echo e($siteSettings['phone']); ?></a><a href="<?php echo SITE_URL; ?>/빠른상담/" class="mobile-consult-btn">무료 상담 신청</a></div></nav></div>
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <main>
        <section class="page-hero"><div class="page-hero-bg"></div><div class="page-hero-overlay"></div><div class="container"><div class="page-hero-content" data-aos="fade-up"><nav class="breadcrumb"><a href="<?php echo SITE_URL; ?>/">홈</a><span><i class="fas fa-chevron-right"></i></span><span>고객지원</span><span><i class="fas fa-chevron-right"></i></span><span class="current">질문과 답변</span></nav><h1 class="page-title">질문과 답변</h1><p class="page-subtitle">자주 묻는 질문에 대한 답변을 확인하세요</p></div></div></section>

        <section class="content-section">
            <div class="container">
                <?php if (!empty($faqData)): ?>
                <!-- FAQ 섹션 -->
                <div class="section-header" data-aos="fade-up">
                    <span class="section-tag">FAQ</span>
                    <h2 class="section-title">자주 묻는 질문</h2>
                    <p class="section-desc">고객님들이 자주 묻는 질문과 답변입니다</p>
                </div>

                <div class="faq-list" data-aos="fade-up">
                    <?php foreach ($faqData as $faq): ?>
                    <div class="faq-item">
                        <div class="faq-question">
                            <div>
                                <span class="faq-category"><?php echo e($faq['category'] ?? '일반'); ?></span>
                                <?php echo e($faq['question']); ?>
                            </div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-inner"><?php echo e($faq['answer']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="faq-section"></div>
                <?php endif; ?>

                <!-- 문의 폼 섹션 -->
                <div class="section-header" data-aos="fade-up">
                    <span class="section-tag">INQUIRY</span>
                    <h2 class="section-title">문의하기</h2>
                    <p class="section-desc">궁금한 점이 있으시면 문의해 주세요</p>
                </div>

                <div class="contact-form" data-aos="fade-up">
                    <form id="inquiryForm" action="#" method="post">
                        <div class="form-group">
                            <label for="name">성함 *</label>
                            <input type="text" id="name" name="name" placeholder="성함을 입력해주세요" required>
                        </div>
                        <div class="form-group">
                            <label for="product">희망모델 *</label>
                            <select id="product" name="product" required>
                                <option value="">선택해주세요</option>
                                <option value="750A">750A</option>
                                <option value="900A">900A</option>
                                <option value="1000A">1000A</option>
                                <option value="1000B">1000B</option>
                                <option value="악세사리">악세사리</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="phone">연락처 *</label>
                            <input type="tel" id="phone" name="phone" placeholder="연락처를 입력해주세요" required>
                        </div>
                        <div class="form-group">
                            <label for="message">문의사항 <span style="color: #888; font-weight: 400;">(180자 제한)</span></label>
                            <textarea id="message" name="message" placeholder="문의사항을 입력해주세요" maxlength="180"></textarea>
                        </div>
                        <button type="submit" class="form-submit" id="submitBtn">신청하기</button>
                    </form>
                    <div id="formMessage" style="display: none; padding: 15px; border-radius: 8px; margin-top: 15px; text-align: center;"></div>
                </div>

                <div class="cta-box" data-aos="fade-up">
                    <h3>더 궁금한 점이 있으신가요?</h3>
                    <p>전문 상담원이 친절하게 답변해 드립니다.</p>
                    <div class="cta-box-buttons">
                        <a href="<?php echo SITE_URL; ?>/빠른상담/" class="btn-primary"><span>상담 신청</span><i class="fas fa-arrow-right"></i></a>
                        <a href="tel:<?php echo e($siteSettings['phone']); ?>" class="btn-secondary"><i class="fas fa-phone"></i><span><?php echo e($siteSettings['phone']); ?></span></a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer"><div class="footer-top"><div class="container"><div class="footer-grid"><div class="footer-brand"><a href="<?php echo SITE_URL; ?>/" class="footer-logo"><img src="https://lkitchen.co.kr/wp-content/uploads/2024/08/logo.png" alt="Lovely Kitchen"></a><p class="footer-tagline">완벽한 분쇄, 차원이 다른 프리미엄</p></div><div class="footer-links"><h4>제품</h4><ul><li><a href="<?php echo SITE_URL; ?>/products.html">음식물처리기</a></li><li><a href="<?php echo SITE_URL; ?>/싱크볼/">아콴테 싱크볼</a></li><li><a href="<?php echo SITE_URL; ?>/악세사리/">악세사리</a></li></ul></div><div class="footer-links"><h4>고객지원</h4><ul><li><a href="<?php echo SITE_URL; ?>/질문과-답변/">질문과 답변</a></li><li><a href="<?php echo SITE_URL; ?>/a-s-지원/">A/S 서비스 지원</a></li><li><a href="<?php echo SITE_URL; ?>/빠른상담/">빠른 상담</a></li></ul></div><div class="footer-contact"><h4>연락처</h4><div class="contact-item"><i class="fas fa-phone"></i><div><span class="label">고객센터</span><a href="tel:<?php echo e($siteSettings['phone']); ?>" class="value"><?php echo e($siteSettings['phone']); ?></a></div></div></div></div></div></div><div class="footer-bottom"><div class="container"><div class="footer-info"><p><?php echo e($siteSettings['company_name']); ?> 대표이사 <?php echo e($siteSettings['ceo_name']); ?> ｜ 사업자등록번호 <?php echo e($siteSettings['business_number']); ?></p></div><div class="footer-copyright"><p><?php echo e($siteSettings['footer_text']); ?></p></div></div></div></footer>

    <a href="tel:<?php echo e($siteSettings['phone']); ?>" class="phone-inquiry-float" style="position: fixed; right: 10px; bottom: 10px; z-index: 9999;"><img decoding="async" src="<?php echo e($siteSettings['phone_image_url']); ?>" alt="전화문의" width="222" height="202"></a>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../js/main.js"></script>
    <script>
    // FAQ 아코디언
    document.querySelectorAll('.faq-question').forEach(function(question) {
        question.addEventListener('click', function() {
            const item = this.parentElement;
            const isActive = item.classList.contains('active');

            // 모든 항목 닫기
            document.querySelectorAll('.faq-item').forEach(function(faqItem) {
                faqItem.classList.remove('active');
            });

            // 클릭한 항목 토글
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });

    // 문의 폼 AJAX 제출
    document.getElementById('inquiryForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const submitBtn = document.getElementById('submitBtn');
        const formMessage = document.getElementById('formMessage');
        const originalBtnText = submitBtn.textContent;

        submitBtn.disabled = true;
        submitBtn.textContent = '전송 중...';

        const formData = new FormData(form);

        fetch('<?php echo SITE_URL; ?>/admin/submit_inquiry.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            formMessage.style.display = 'block';

            if (data.success) {
                formMessage.style.background = '#d1fae5';
                formMessage.style.color = '#065f46';
                formMessage.textContent = data.message;
                form.reset();
            } else {
                formMessage.style.background = '#fee2e2';
                formMessage.style.color = '#991b1b';
                formMessage.textContent = data.message;
            }

            setTimeout(() => {
                formMessage.style.display = 'none';
            }, 5000);
        })
        .catch(error => {
            formMessage.style.display = 'block';
            formMessage.style.background = '#fee2e2';
            formMessage.style.color = '#991b1b';
            formMessage.textContent = '네트워크 오류가 발생했습니다. 잠시 후 다시 시도해주세요.';

            setTimeout(() => {
                formMessage.style.display = 'none';
            }, 5000);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        });
    });
    </script>
</body>
</html>
