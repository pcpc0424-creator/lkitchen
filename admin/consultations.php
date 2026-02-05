<?php
/**
 * 러블리키친 상담 신청 관리 페이지
 */
require_once 'config.php';
checkLogin();

// 상담 데이터 읽기
$inquiries = readJsonData('consultations.json');

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
        writeJsonData('consultations.json', $inquiries);
        header('Location: consultations.php?msg=updated');
        exit;
    }

    if ($action === 'delete' && $inquiryId > 0) {
        $inquiries = array_filter($inquiries, function($inquiry) use ($inquiryId) {
            return $inquiry['id'] !== $inquiryId;
        });
        $inquiries = array_values($inquiries);
        writeJsonData('consultations.json', $inquiries);
        header('Location: consultations.php?msg=deleted');
        exit;
    }
}

// 새 상담 수 계산
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
    <title>상담 신청 관리 | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css?v=2">
    <link rel="icon" type="image/png" href="/수정/fhrh.png">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">
                    <img src="/수정/fhrh.png" alt="러블리키친">
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
                    <li class="nav-item">
                        <a href="inquiries.php" class="nav-link">
                            <i class="fas fa-envelope"></i>
                            <span>문의 관리</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="consultations.php" class="nav-link">
                            <i class="fas fa-headset"></i>
                            <span>상담 신청</span>
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
                    <h1 class="page-title">상담 신청 관리</h1>
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
                    <?php echo $_GET['msg'] === 'deleted' ? '상담 신청이 삭제되었습니다.' : '상담 상태가 업데이트되었습니다.'; ?>
                </div>
                <?php endif; ?>

                <!-- 통계 -->
                <div class="stats-grid" style="margin-bottom: 30px;">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo count($inquiries); ?></span>
                            <span class="stat-label">전체 상담</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo count($newInquiries); ?></span>
                            <span class="stat-label">새 상담</span>
                        </div>
                    </div>
                </div>

                <!-- 상담 목록 -->
                <div class="section">
                    <h2 class="section-title">상담 신청 목록</h2>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>번호</th>
                                    <th>성함</th>
                                    <th>관심제품</th>
                                    <th>연락처</th>
                                    <th>상세정보</th>
                                    <th>상태</th>
                                    <th>접수일</th>
                                    <th>관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($inquiries)): ?>
                                <tr>
                                    <td colspan="8" class="empty-message">접수된 상담 신청이 없습니다.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($inquiries as $inquiry): ?>
                                <tr class="<?php echo $inquiry['status'] === 'new' ? 'row-new' : ''; ?>">
                                    <td data-label="번호"><?php echo $inquiry['id']; ?></td>
                                    <td data-label="성함"><strong><?php echo sanitize($inquiry['name']); ?></strong></td>
                                    <td data-label="관심제품"><?php echo sanitize($inquiry['product']); ?></td>
                                    <td data-label="연락처">
                                        <a href="tel:<?php echo sanitize($inquiry['phone']); ?>" class="phone-link">
                                            <?php echo sanitize($inquiry['phone']); ?>
                                        </a>
                                    </td>
                                    <td data-label="상세정보" class="message-cell">
                                        <button type="button" class="btn-sm btn-view" onclick="showDetail(<?php echo htmlspecialchars(json_encode($inquiry, JSON_UNESCAPED_UNICODE)); ?>)">
                                            <i class="fas fa-eye"></i><span>상세<br>보기</span>
                                        </button>
                                    </td>
                                    <td data-label="상태">
                                        <?php if ($inquiry['status'] === 'new'): ?>
                                        <span class="status-badge new">새 상담</span>
                                        <?php elseif ($inquiry['status'] === 'read'): ?>
                                        <span class="status-badge read">확인됨</span>
                                        <?php else: ?>
                                        <span class="status-badge replied">완료</span>
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

    <!-- 상세보기 모달 -->
    <div id="detailModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
        <div class="modal-content" style="background:#f8f9fa; padding:0; border-radius:16px; max-width:600px; width:95%; max-height:90vh; overflow-y:auto; position:relative; box-shadow: 0 25px 50px rgba(0,0,0,0.25);">
            <div style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color:#fff; padding:20px 25px; border-radius:16px 16px 0 0; position:sticky; top:0; z-index:10;">
                <button onclick="closeDetail()" style="position:absolute; top:15px; right:20px; background:rgba(255,255,255,0.2); border:none; font-size:20px; cursor:pointer; color:#fff; width:35px; height:35px; border-radius:50%; display:flex; align-items:center; justify-content:center;">&times;</button>
                <h3 style="margin:0; font-size:1.3rem;"><i class="fas fa-headset"></i> 상담 신청서</h3>
                <p id="modalSubtitle" style="margin:5px 0 0; font-size:0.85rem; opacity:0.9;"></p>
            </div>
            <div id="detailContent" style="padding:20px;"></div>
        </div>
    </div>

    <script src="js/admin.js"></script>
    <script>
    function showDetail(inquiry) {
        const content = document.getElementById('detailContent');
        document.getElementById('modalSubtitle').textContent = '접수번호 #' + inquiry.id + ' | ' + inquiry.created_at;

        let html = '';

        // 고객 정보 섹션
        html += '<div style="background:#fff; border-radius:12px; padding:20px; margin-bottom:15px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">';
        html += '<h4 style="margin:0 0 15px; color:#1e3a5f; font-size:0.95rem; display:flex; align-items:center; gap:8px;"><i class="fas fa-user" style="color:#8b5cf6;"></i> 고객 정보</h4>';
        html += '<div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">';
        html += '<div><label style="font-size:0.75rem; color:#666; display:block; margin-bottom:4px;">성함</label><div style="font-size:1.1rem; font-weight:600; color:#1f2937;">' + escapeHtml(inquiry.name || '-') + '</div></div>';
        html += '<div><label style="font-size:0.75rem; color:#666; display:block; margin-bottom:4px;">연락처</label><div style="font-size:1.1rem; font-weight:600; color:#1f2937;"><a href="tel:' + inquiry.phone + '" style="color:#8b5cf6; text-decoration:none;">' + escapeHtml(inquiry.phone || '-') + '</a></div></div>';
        html += '</div>';
        html += '<div><label style="font-size:0.75rem; color:#666; display:block; margin-bottom:4px;"><i class="fas fa-calendar-alt" style="margin-right:4px;"></i>접수일시</label><div style="font-size:0.95rem; color:#1f2937; background:#f0f9ff; padding:10px 12px; border-radius:8px; border-left:3px solid #3b82f6;"><i class="fas fa-clock" style="color:#3b82f6; margin-right:6px;"></i>' + escapeHtml(inquiry.created_at || '-') + '</div></div>';
        html += '</div>';

        // 관심 제품 섹션
        html += '<div style="background:#fff; border-radius:12px; padding:20px; margin-bottom:15px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">';
        html += '<h4 style="margin:0 0 15px; color:#1e3a5f; font-size:0.95rem; display:flex; align-items:center; gap:8px;"><i class="fas fa-box" style="color:#8b5cf6;"></i> 관심 제품</h4>';
        html += '<div style="display:flex; flex-wrap:wrap; gap:8px;">';
        const products = (inquiry.product || '-').split(', ');
        products.forEach(function(p) {
            html += '<span style="background:linear-gradient(135deg, #8b5cf6, #7c3aed); color:#fff; padding:6px 14px; border-radius:20px; font-size:0.85rem; font-weight:500;">' + escapeHtml(p) + '</span>';
        });
        html += '</div></div>';

        // 설치 정보 섹션
        html += '<div style="background:#fff; border-radius:12px; padding:20px; margin-bottom:15px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">';
        html += '<h4 style="margin:0 0 15px; color:#1e3a5f; font-size:0.95rem; display:flex; align-items:center; gap:8px;"><i class="fas fa-map-marker-alt" style="color:#8b5cf6;"></i> 설치 정보</h4>';
        html += '<div style="margin-bottom:12px;"><label style="font-size:0.75rem; color:#666; display:block; margin-bottom:4px;">주소</label><div style="font-size:0.95rem; color:#1f2937; background:#f3f4f6; padding:10px 12px; border-radius:8px;">' + escapeHtml(inquiry.address || '-') + '</div></div>';
        html += '<div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">';
        html += '<div><label style="font-size:0.75rem; color:#666; display:block; margin-bottom:4px;">낮시간 설치</label><div style="font-size:0.95rem; color:#1f2937;"><span style="background:' + (inquiry.daytime === 'O' ? '#d1fae5' : '#fee2e2') + '; color:' + (inquiry.daytime === 'O' ? '#065f46' : '#991b1b') + '; padding:4px 10px; border-radius:6px; font-weight:500;">' + (inquiry.daytime === 'O' ? '가능 (O)' : inquiry.daytime === 'X' ? '불가능 (X)' : '-') + '</span></div></div>';
        if (inquiry.sinkInfo) {
            html += '<div><label style="font-size:0.75rem; color:#666; display:block; margin-bottom:4px;">싱크대 정보</label><div style="font-size:0.95rem; color:#1f2937;">' + escapeHtml(inquiry.sinkInfo) + '</div></div>';
        }
        html += '</div></div>';

        // 첨부 사진 섹션
        if (inquiry.photo_countertop || inquiry.photo_cabinet) {
            html += '<div style="background:#fff; border-radius:12px; padding:20px; margin-bottom:15px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">';
            html += '<h4 style="margin:0 0 15px; color:#1e3a5f; font-size:0.95rem; display:flex; align-items:center; gap:8px;"><i class="fas fa-camera" style="color:#8b5cf6;"></i> 첨부 사진</h4>';
            html += '<div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">';

            if (inquiry.photo_countertop) {
                html += '<div style="text-align:center;">';
                html += '<label style="font-size:0.75rem; color:#666; display:block; margin-bottom:8px;"><i class="fas fa-th-large"></i> 싱크대 상판</label>';
                html += '<a href="' + inquiry.photo_countertop + '" target="_blank" style="display:block;">';
                html += '<img src="' + inquiry.photo_countertop + '" style="width:100%; max-height:200px; object-fit:cover; border-radius:10px; border:2px solid #e5e7eb; cursor:pointer; transition:transform 0.2s;" onmouseover="this.style.transform=\'scale(1.02)\'" onmouseout="this.style.transform=\'scale(1)\'">';
                html += '</a></div>';
            }

            if (inquiry.photo_cabinet) {
                html += '<div style="text-align:center;">';
                html += '<label style="font-size:0.75rem; color:#666; display:block; margin-bottom:8px;"><i class="fas fa-door-open"></i> 하부장 내부</label>';
                html += '<a href="' + inquiry.photo_cabinet + '" target="_blank" style="display:block;">';
                html += '<img src="' + inquiry.photo_cabinet + '" style="width:100%; max-height:200px; object-fit:cover; border-radius:10px; border:2px solid #e5e7eb; cursor:pointer; transition:transform 0.2s;" onmouseover="this.style.transform=\'scale(1.02)\'" onmouseout="this.style.transform=\'scale(1)\'">';
                html += '</a></div>';
            }

            html += '</div></div>';
        }

        // 추가 문의사항
        if (inquiry.message) {
            html += '<div style="background:#fff; border-radius:12px; padding:20px; margin-bottom:15px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">';
            html += '<h4 style="margin:0 0 15px; color:#1e3a5f; font-size:0.95rem; display:flex; align-items:center; gap:8px;"><i class="fas fa-comment-alt" style="color:#8b5cf6;"></i> 추가 문의사항</h4>';
            html += '<div style="font-size:0.95rem; color:#1f2937; background:#f3f4f6; padding:12px 15px; border-radius:8px; line-height:1.6;">' + escapeHtml(inquiry.message) + '</div>';
            html += '</div>';
        }

        content.innerHTML = html;
        document.getElementById('detailModal').style.display = 'flex';
    }

    function closeDetail() {
        document.getElementById('detailModal').style.display = 'none';
    }

    function escapeHtml(text) {
        if (typeof text !== 'string') return text;
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // 모달 외부 클릭시 닫기
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) closeDetail();
    });

    // ESC 키로 닫기
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDetail();
    });
    </script>
</body>
</html>
