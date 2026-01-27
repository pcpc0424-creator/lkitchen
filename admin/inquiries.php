<?php
/**
 * 러블리키친 문의 관리 페이지
 */
require_once 'config.php';
checkLogin();

// 문의 데이터 읽기
$inquiries = readJsonData('inquiries.json');

// 상태 업데이트 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $inquiryId = intval($_POST['id'] ?? 0);

    if ($action === 'mark_read' && $inquiryId > 0) {
        foreach ($inquiries as &$inquiry) {
            if ($inquiry['id'] === $inquiryId && $inquiry['status'] === 'new') {
                $inquiry['status'] = 'read';
                $inquiry['read_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        writeJsonData('inquiries.json', $inquiries);
        header('Location: inquiries.php?msg=updated');
        exit;
    }

    if ($action === 'delete' && $inquiryId > 0) {
        $inquiries = array_filter($inquiries, function($inquiry) use ($inquiryId) {
            return $inquiry['id'] !== $inquiryId;
        });
        $inquiries = array_values($inquiries);
        writeJsonData('inquiries.json', $inquiries);
        header('Location: inquiries.php?msg=deleted');
        exit;
    }
}

// 새 문의 수 계산
$newInquiries = array_filter($inquiries, function($inquiry) {
    return $inquiry['status'] === 'new';
});

// 역순 정렬 (최신순)
$inquiries = array_reverse($inquiries);
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>문의 관리 | <?php echo SITE_NAME; ?></title>
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
                    <li class="nav-item">
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
                    <li class="nav-item active">
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
                    <h1 class="page-title">문의 관리</h1>
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
                <?php if (isset($_GET['msg'])): ?>
                <div class="alert <?php echo $_GET['msg'] === 'deleted' ? 'alert-error' : 'alert-success'; ?>">
                    <i class="fas <?php echo $_GET['msg'] === 'deleted' ? 'fa-trash' : 'fa-check-circle'; ?>"></i>
                    <?php echo $_GET['msg'] === 'deleted' ? '문의가 삭제되었습니다.' : '문의 상태가 업데이트되었습니다.'; ?>
                </div>
                <?php endif; ?>

                <!-- 통계 -->
                <div class="stats-grid" style="margin-bottom: 30px;">
                    <div class="stat-card">
                        <div class="stat-icon inquiry">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo count($inquiries); ?></span>
                            <span class="stat-label">전체 문의</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo count($newInquiries); ?></span>
                            <span class="stat-label">새 문의</span>
                        </div>
                    </div>
                </div>

                <!-- 문의 목록 -->
                <div class="section">
                    <h2 class="section-title">문의 목록</h2>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>번호</th>
                                    <th>성함</th>
                                    <th>희망모델</th>
                                    <th>연락처</th>
                                    <th>문의사항</th>
                                    <th>상태</th>
                                    <th>접수일</th>
                                    <th>관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($inquiries)): ?>
                                <tr>
                                    <td colspan="8" class="empty-message">접수된 문의가 없습니다.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($inquiries as $inquiry): ?>
                                <tr class="<?php echo $inquiry['status'] === 'new' ? 'row-new' : ''; ?>">
                                    <td data-label="번호"><?php echo $inquiry['id']; ?></td>
                                    <td data-label="성함"><strong><?php echo sanitize($inquiry['name']); ?></strong></td>
                                    <td data-label="희망모델"><?php echo sanitize($inquiry['product']); ?></td>
                                    <td data-label="연락처">
                                        <a href="tel:<?php echo sanitize($inquiry['phone']); ?>" class="phone-link">
                                            <?php echo sanitize($inquiry['phone']); ?>
                                        </a>
                                    </td>
                                    <td data-label="문의사항" class="message-cell">
                                        <?php if (!empty($inquiry['message'])): ?>
                                        <span class="message-preview" title="<?php echo sanitize($inquiry['message']); ?>">
                                            <?php echo mb_substr(sanitize($inquiry['message']), 0, 30) . (mb_strlen($inquiry['message']) > 30 ? '...' : ''); ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="no-message">-</span>
                                        <?php endif; ?>
                                    </td>
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
                                    <td>
                                        <div class="btn-group">
                                            <?php if ($inquiry['status'] === 'new'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="mark_read">
                                                <input type="hidden" name="id" value="<?php echo $inquiry['id']; ?>">
                                                <button type="submit" class="btn-sm btn-edit" title="확인 처리">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $inquiry['id']; ?>">
                                                <button type="submit" class="btn-sm btn-delete" title="삭제">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
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
