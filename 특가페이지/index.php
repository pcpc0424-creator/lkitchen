<?php
/**
 * 러블리키친 특가페이지 - 제품 선택
 */
require_once __DIR__ . '/../includes/config.php';
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>특가페이지 | 러블리키친 - 신축 아파트 특판</title>
    <meta name="description" content="러블리키친 특가페이지 - 신축 아파트 특판 음식물처리기와 싱크볼을 특별한 가격으로 만나보세요">
    <meta name="keywords" content="러블리키친, 특가, 음식물처리기, 싱크볼, 신축아파트, 특판">

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

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/수정/fhrh.png">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans KR', sans-serif;
            background: linear-gradient(135deg, #051535 0%, #0a2558 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .select-header {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .select-header img {
            height: 40px;
        }

        /* Main Content */
        .select-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .select-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .select-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 107, 107, 0.2);
            border: 1px solid rgba(255, 107, 107, 0.5);
            color: #ff6b6b;
            padding: 10px 24px;
            border-radius: 30px;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .select-title h1 {
            font-size: clamp(1.8rem, 5vw, 2.8rem);
            font-weight: 700;
            color: #fff;
            line-height: 1.4;
            margin-bottom: 15px;
        }

        .select-title h1 .highlight {
            color: #60a5fa;
        }

        .select-title p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Product Selection Cards */
        .select-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            max-width: 900px;
            width: 100%;
        }

        @media (max-width: 768px) {
            .select-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                max-width: 400px;
            }
        }

        .select-card {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        .select-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
        }

        .select-card-image {
            height: 220px;
            overflow: hidden;
            position: relative;
        }

        .select-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .select-card:hover .select-card-image img {
            transform: scale(1.1);
        }

        .select-card-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #fff;
            z-index: 2;
        }

        .select-card-badge.disposer {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .select-card-badge.sinkbowl {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }

        .select-card-content {
            padding: 30px;
            text-align: center;
        }

        .select-card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -50px auto 20px;
            position: relative;
            z-index: 3;
            font-size: 1.5rem;
            color: #fff;
        }

        .select-card-icon.disposer {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .select-card-icon.sinkbowl {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }

        .select-card-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #051535;
            margin-bottom: 10px;
        }

        .select-card-desc {
            font-size: 0.95rem;
            color: #64748b;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .select-card-features {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .select-card-feature {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .select-card-feature.disposer {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .select-card-feature.sinkbowl {
            background: rgba(14, 165, 233, 0.1);
            color: #0284c7;
        }

        .select-card-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s ease;
        }

        .select-card-btn.disposer {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .select-card-btn.sinkbowl {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }

        .select-card:hover .select-card-btn {
            transform: scale(1.05);
        }

        /* Multi-button card styles */
        .select-card-multi {
            cursor: default;
        }

        .select-card-btns {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .select-card-btns .select-card-btn {
            text-decoration: none;
            justify-content: center;
        }

        .select-card-btn.sinkbowl-outline {
            background: transparent;
            border: 2px solid #0ea5e9;
            color: #0284c7;
        }

        .select-card-btn.sinkbowl-outline:hover {
            background: rgba(14, 165, 233, 0.1);
        }

        .select-card-multi:hover .select-card-btn {
            transform: none;
        }

        .select-card-btns .select-card-btn:hover {
            transform: scale(1.03);
        }

        /* Calculator Link */
        .calculator-link {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: rgba(239, 68, 68, 0.9);
            color: #fff;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            z-index: 4;
        }

        .calculator-link:hover {
            background: #dc2626;
            transform: scale(1.05);
        }

        /* Footer Info */
        .select-footer {
            padding: 30px 20px;
            text-align: center;
        }

        .select-footer-contact {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .select-footer-contact a {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }

        .select-footer-contact a:hover {
            color: #fff;
        }

        .select-footer-contact i {
            color: #60a5fa;
        }

        .select-footer p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
        }

        /* Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }

        /* Background Pattern */
        .bg-pattern {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.05)"/></svg>');
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 0;
        }

        .select-header,
        .select-container,
        .select-footer {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="bg-pattern"></div>

    <!-- Header -->
    <header class="select-header">
        <a href="<?php echo SITE_URL; ?>/">
            <img src="/수정/fhrh.png" alt="Lovely Kitchen">
        </a>
    </header>

    <!-- Main Content -->
    <main class="select-container">
        <div class="select-title" data-aos="fade-up">
            <div class="select-badge">
                <i class="fas fa-fire"></i>
                <span>신축 아파트 특판</span>
            </div>
            <h1><span class="highlight">원하시는 제품</span>을<br>선택해 주세요</h1>
            <p>러블리키친 특별 할인가로 만나보세요</p>
        </div>

        <div class="select-grid">
            <!-- 음식물처리기 카드 -->
            <a href="disposer.php" class="select-card" data-aos="fade-up" data-aos-delay="100">
                <div class="select-card-badge disposer">DISPOSER</div>
                <div class="select-card-image">
                    <img src="https://lkitchen.co.kr/wp-content/uploads/2025/03/Frame-98-1.png" alt="음식물처리기">
                </div>
                <div class="select-card-content">
                    <div class="select-card-icon disposer">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <h2 class="select-card-title">음식물처리기</h2>
                    <p class="select-card-desc">완벽한 분쇄력의<br>프리미엄 음식물처리기</p>
                    <div class="select-card-features">
                        <span class="select-card-feature disposer">LK-750A</span>
                        <span class="select-card-feature disposer">LK-900A</span>
                        <span class="select-card-feature disposer">LK-1000A</span>
                        <span class="select-card-feature disposer">LK-1000B</span>
                    </div>
                    <div class="select-card-btn disposer">
                        제품 보러가기
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </a>

            <!-- 싱크볼 카드 -->
            <div class="select-card select-card-multi" data-aos="fade-up" data-aos-delay="200">
                <div class="select-card-badge sinkbowl">SINKBOWL</div>
                <div class="select-card-image">
                    <img src="https://lkitchen.co.kr/wp-content/uploads/2025/09/8H2A9623-1024x683.jpg" alt="아콴테 싱크볼">
                </div>
                <div class="select-card-content">
                    <div class="select-card-icon sinkbowl">
                        <i class="fas fa-sink"></i>
                    </div>
                    <h2 class="select-card-title">아콴테 싱크볼</h2>
                    <p class="select-card-desc">전문가 무료시공 포함<br>프리미엄 사각싱크볼</p>
                    <div class="select-card-features">
                        <span class="select-card-feature sinkbowl">AQ-860NE</span>
                        <span class="select-card-feature sinkbowl">AQ-900NE</span>
                        <span class="select-card-feature sinkbowl">AQ-980NE</span>
                    </div>
                    <div class="select-card-btns">
                        <a href="/싱크볼/" class="select-card-btn sinkbowl">
                            제품 보러가기
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="싱크볼-견적/" class="select-card-btn sinkbowl-outline">
                            견적 계산기
                            <i class="fas fa-calculator"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="select-footer">
        <div class="select-footer-contact">
            <a href="tel:010-2464-4987">
                <i class="fas fa-phone"></i>
                <span>010-2464-4987</span>
            </a>
            <a href="mailto:cs.lovelykitchen@gmail.com">
                <i class="fas fa-envelope"></i>
                <span>cs.lovelykitchen@gmail.com</span>
            </a>
        </div>
        <p>&copy; 2024 LOVELY KITCHEN. All Rights Reserved.</p>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    </script>
</body>
</html>
