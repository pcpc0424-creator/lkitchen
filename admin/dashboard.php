<?php
/**
 * 러블리키친 관리자 대시보드
 */
require_once 'config.php';
checkLogin();

// 통계 데이터 수집
$foodReviews = readJsonData('food_reviews.json');
$sinkReviews = readJsonData('sink_reviews.json');
$inquiries = readJsonData('inquiries.json');

// 새 문의 수 계산
$newInquiries = array_filter($inquiries, function($inquiry) {
    return $inquiry['status'] === 'new';
});

// 이미지 수 계산 (갤러리 JSON 기준)
$foodImages = count(readJsonData('food_gallery.json'));
$sinkImages = count(readJsonData('sink_gallery.json'));
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>대시보드 | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css?v=2">
    <link rel="icon" type="image/png" href="http://115.68.223.124/lovelykitchen/수정/fhrh.png">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">
                    <img src="http://115.68.223.124/lovelykitchen/수정/fhrh.png" alt="러블리키친">
                </a>
                <span class="sidebar-title">관리자</span>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item active">
                        <a href="dashboard.php" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>대시보드</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="products.php" class="nav-link">
                            <i class="fas fa-box"></i>
                            <span>제품 관리</span>
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
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>갤러리 관리</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="faq.php" class="nav-link">
                            <i class="fas fa-question-circle"></i>
                            <span>FAQ 관리</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="inquiries.php" class="nav-link">
                            <i class="fas fa-envelope"></i>
                            <span>문의 관리</span>
                            <?php if (count($newInquiries) > 0): ?>
                            <span class="nav-badge"><?php echo count($newInquiries); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="special.php" class="nav-link">
                            <i class="fas fa-tag"></i>
                            <span>특가페이지 관리</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="sinkbowl_calc.php" class="nav-link">
                            <i class="fas fa-calculator"></i>
                            <span>싱크볼 계산식</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="settings.php" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>사이트 설정</span>
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
                    <h1 class="page-title">대시보드</h1>
                </div>
                <div class="header-right">
                    <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn-site">
                        <i class="fas fa-external-link-alt"></i>
                        사이트 보기
                    </a>
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo sanitize($_SESSION['admin_username']); ?></span>
                    </div>
                </div>
            </header>

            <div class="content-body">
                <!-- 통계 카드 -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon food">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo count($foodReviews); ?></span>
                            <span class="stat-label">음식물처리기 후기</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon sink">
                            <i class="fas fa-sink"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo count($sinkReviews); ?></span>
                            <span class="stat-label">싱크볼 후기</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon images">
                            <i class="fas fa-images"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo $foodImages + $sinkImages; ?></span>
                            <span class="stat-label">전체 이미지</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon inquiry">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo count($inquiries); ?></span>
                            <span class="stat-label">전체 문의</span>
                            <?php if (count($newInquiries) > 0): ?>
                            <span class="stat-new">(새 문의 <?php echo count($newInquiries); ?>건)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 빠른 작업 -->
                <div class="section">
                    <h2 class="section-title">빠른 작업</h2>
                    <div class="quick-actions">
                        <a href="reviews.php?type=food&action=add" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-info">
                                <h3>음식물처리기 후기 추가</h3>
                                <p>새로운 후기를 작성합니다</p>
                            </div>
                        </a>

                        <a href="reviews.php?type=sink&action=add" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-info">
                                <h3>싱크볼 후기 추가</h3>
                                <p>새로운 후기를 작성합니다</p>
                            </div>
                        </a>

                        <a href="images.php" class="action-card">
                            <div class="action-icon upload">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="action-info">
                                <h3>갤러리 관리</h3>
                                <p>드래그 앤 드롭으로 업로드</p>
                            </div>
                        </a>

                        <a href="generate.php" class="action-card">
                            <div class="action-icon generate">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                            <div class="action-info">
                                <h3>페이지 생성</h3>
                                <p>후기 페이지를 새로 생성합니다</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- 최근 후기 -->
                <div class="section">
                    <h2 class="section-title">최근 음식물처리기 후기</h2>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>제목</th>
                                    <th>작성자</th>
                                    <th>작성일</th>
                                    <th>관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recentFood = array_slice(array_reverse($foodReviews), 0, 5);
                                if (empty($recentFood)):
                                ?>
                                <tr>
                                    <td colspan="4" class="empty-message">등록된 후기가 없습니다.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($recentFood as $review): ?>
                                <tr>
                                    <td data-label="제목"><?php echo sanitize($review['title']); ?></td>
                                    <td data-label="작성자"><?php echo sanitize($review['author']); ?></td>
                                    <td data-label="작성일"><?php echo $review['created_at'] ?? '-'; ?></td>
                                    <td>
                                        <a href="reviews.php?type=food&action=edit&id=<?php echo $review['id']; ?>" class="btn-edit">수정</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="section">
                    <h2 class="section-title">최근 싱크볼 후기</h2>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>제목</th>
                                    <th>작성자</th>
                                    <th>작성일</th>
                                    <th>관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recentSink = array_slice(array_reverse($sinkReviews), 0, 5);
                                if (empty($recentSink)):
                                ?>
                                <tr>
                                    <td colspan="4" class="empty-message">등록된 후기가 없습니다.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($recentSink as $review): ?>
                                <tr>
                                    <td data-label="제목"><?php echo sanitize($review['title']); ?></td>
                                    <td data-label="작성자"><?php echo sanitize($review['author']); ?></td>
                                    <td data-label="작성일"><?php echo $review['created_at'] ?? '-'; ?></td>
                                    <td>
                                        <a href="reviews.php?type=sink&action=edit&id=<?php echo $review['id']; ?>" class="btn-edit">수정</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">최근 문의</h2>
                        <a href="inquiries.php" class="btn-secondary" style="padding: 8px 16px; font-size: 0.85rem;">전체 보기</a>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>성함</th>
                                    <th>희망모델</th>
                                    <th>연락처</th>
                                    <th>상태</th>
                                    <th>접수일</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recentInquiries = array_slice(array_reverse($inquiries), 0, 5);
                                if (empty($recentInquiries)):
                                ?>
                                <tr>
                                    <td colspan="5" class="empty-message">접수된 문의가 없습니다.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($recentInquiries as $inquiry): ?>
                                <tr class="<?php echo $inquiry['status'] === 'new' ? 'row-new' : ''; ?>">
                                    <td data-label="성함"><strong><?php echo sanitize($inquiry['name']); ?></strong></td>
                                    <td data-label="희망모델"><?php echo sanitize($inquiry['product']); ?></td>
                                    <td data-label="연락처"><?php echo sanitize($inquiry['phone']); ?></td>
                                    <td data-label="상태">
                                        <?php if ($inquiry['status'] === 'new'): ?>
                                        <span class="status-badge new">새 문의</span>
                                        <?php elseif ($inquiry['status'] === 'read'): ?>
                                        <span class="status-badge read">확인됨</span>
                                        <?php else: ?>
                                        <span class="status-badge replied">답변완료</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="접수일"><?php echo $inquiry['created_at'] ?? '-'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
