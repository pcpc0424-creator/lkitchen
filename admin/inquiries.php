<?php
/**
 * 러블리키친 문의 관리 페이지
 */
require_once 'config.php';
checkLogin();

// 문의 데이터 읽기
$inquiries = readJsonData('inquiries.json');
$consultations = readJsonData('consultations.json');

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

// 출처별 분류
$allSources = [];
foreach ($inquiries as $inquiry) {
    $src = $inquiry['source'] ?? '일반';
    if (!isset($allSources[$src])) {
        $allSources[$src] = 0;
    }
    $allSources[$src]++;
}

// 필터 처리
$filterSource = $_GET['source'] ?? 'all';
if ($filterSource !== 'all') {
    $inquiries = array_filter($inquiries, function($inquiry) use ($filterSource) {
        return ($inquiry['source'] ?? '일반') === $filterSource;
    });
}

// 새 문의 수 계산 (필터 적용 후)
$newInquiries = array_filter($inquiries, function($inquiry) {
    return $inquiry['status'] === 'new';
});

// 전체 새 문의 수 (배지용)
$allNewCount = count(array_filter(readJsonData('inquiries.json'), function($inquiry) {
    return $inquiry['status'] === 'new';
}));

// 새 상담 수 계산
$newConsultations = array_filter($consultations, function($consultation) {
    return $consultation['status'] === 'new';
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
    <link rel="icon" type="image/png" href="/수정/fhrh.png">
    <style>
        .source-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .source-general {
            background: #d1fae5;
            color: #065f46;
        }
        .source-special {
            background: #dbeafe;
            color: #1e40af;
        }
        .filter-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>
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
                    <li class="nav-item active">
                        <a href="inquiries.php" class="nav-link">
                            <i class="fas fa-envelope"></i>
                            <span>문의 관리</span>
                            <?php if ($allNewCount > 0): ?>
                            <span class="nav-badge"><?php echo $allNewCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="consultations.php" class="nav-link">
                            <i class="fas fa-headset"></i>
                            <span>상담 신청</span>
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

                <!-- 출처별 필터 탭 -->
                <div class="filter-tabs" style="margin-bottom: 20px; display:flex; flex-wrap:wrap; gap:10px;">
                    <a href="?source=all" class="filter-tab <?php echo $filterSource === 'all' ? 'active' : ''; ?>" style="display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:25px; text-decoration:none; font-weight:500; font-size:0.9rem; transition:all 0.3s; <?php echo $filterSource === 'all' ? 'background:linear-gradient(135deg, #3b82f6, #2563eb); color:#fff; box-shadow:0 4px 15px rgba(59,130,246,0.3);' : 'background:#fff; color:#64748b; border:1px solid #e2e8f0;'; ?>">
                        <i class="fas fa-list"></i>
                        <span>전체</span>
                        <span style="background:<?php echo $filterSource === 'all' ? 'rgba(255,255,255,0.25)' : '#f1f5f9'; ?>; padding:2px 8px; border-radius:12px; font-size:0.8rem;"><?php echo array_sum($allSources); ?></span>
                    </a>
                    <?php foreach ($allSources as $sourceName => $sourceCount): ?>
                    <a href="?source=<?php echo urlencode($sourceName); ?>" class="filter-tab <?php echo $filterSource === $sourceName ? 'active' : ''; ?>" style="display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:25px; text-decoration:none; font-weight:500; font-size:0.9rem; transition:all 0.3s; <?php echo $filterSource === $sourceName ? 'background:linear-gradient(135deg, ' . ($sourceName === '일반' ? '#10b981, #059669' : '#0ea5e9, #0284c7') . '); color:#fff; box-shadow:0 4px 15px rgba(14,165,233,0.3);' : 'background:#fff; color:#64748b; border:1px solid #e2e8f0;'; ?>">
                        <i class="fas <?php echo $sourceName === '일반' ? 'fa-globe' : 'fa-tag'; ?>"></i>
                        <span><?php echo sanitize($sourceName); ?></span>
                        <span style="background:<?php echo $filterSource === $sourceName ? 'rgba(255,255,255,0.25)' : '#f1f5f9'; ?>; padding:2px 8px; border-radius:12px; font-size:0.8rem;"><?php echo $sourceCount; ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- 통계 -->
                <div class="stats-grid" style="margin-bottom: 30px;">
                    <div class="stat-card">
                        <div class="stat-icon inquiry">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo count($inquiries); ?></span>
                            <span class="stat-label"><?php echo $filterSource === 'all' ? '전체 문의' : sanitize($filterSource) . ' 문의'; ?></span>
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
                                    <th>출처</th>
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
                                    <td colspan="9" class="empty-message">접수된 문의가 없습니다.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($inquiries as $inquiry): ?>
                                <?php $inquirySource = $inquiry['source'] ?? '일반'; ?>
                                <tr class="<?php echo $inquiry['status'] === 'new' ? 'row-new' : ''; ?>">
                                    <td data-label="번호"><?php echo $inquiry['id']; ?></td>
                                    <td data-label="출처">
                                        <span class="source-badge <?php echo $inquirySource === '일반' ? 'source-general' : 'source-special'; ?>">
                                            <?php echo sanitize($inquirySource); ?>
                                        </span>
                                    </td>
                                    <td data-label="성함"><strong><?php echo sanitize($inquiry['name']); ?></strong></td>
                                    <td data-label="희망모델"><?php echo sanitize($inquiry['product']); ?></td>
                                    <td data-label="연락처">
                                        <a href="tel:<?php echo sanitize($inquiry['phone']); ?>" class="phone-link">
                                            <?php echo sanitize($inquiry['phone']); ?>
                                        </a>
                                    </td>
                                    <td data-label="문의사항" class="message-cell">
                                        <button type="button" class="btn-sm btn-view" onclick="showDetail(<?php echo htmlspecialchars(json_encode($inquiry, JSON_UNESCAPED_UNICODE)); ?>)">
                                            <i class="fas fa-eye"></i><span>상세<br>보기</span>
                                        </button>
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

    <!-- 상세보기 모달 -->
    <div id="detailModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
        <div class="modal-content" style="background:#fff; padding:30px; border-radius:12px; max-width:500px; width:90%; max-height:80vh; overflow-y:auto; position:relative;">
            <button onclick="closeDetail()" style="position:absolute; top:15px; right:15px; background:none; border:none; font-size:24px; cursor:pointer; color:#666;">&times;</button>
            <h3 style="margin-bottom:20px; color:#1e3a5f; border-bottom:2px solid #3b82f6; padding-bottom:10px;">
                <i class="fas fa-envelope-open-text"></i> 문의 상세 정보
            </h3>
            <div id="detailContent"></div>
        </div>
    </div>

    <script src="js/admin.js"></script>
    <script>
    function showDetail(inquiry) {
        const content = document.getElementById('detailContent');
        const source = inquiry.source || '일반';
        let html = '';

        // 출처 배지 (특가인 경우)
        if (source !== '일반') {
            html += '<div style="background:linear-gradient(135deg, #0ea5e9, #0284c7); color:#fff; padding:10px 15px; border-radius:8px; margin-bottom:15px; display:inline-flex; align-items:center; gap:8px; font-weight:600;">';
            html += '<i class="fas fa-tag"></i> ' + escapeHtml(source);
            html += '</div>';
        }

        html += '<table style="width:100%; border-collapse:collapse;">';

        const fields = [
            {key: 'id', label: '문의번호'},
            {key: 'source', label: '출처'},
            {key: 'name', label: '성함'},
            {key: 'product', label: '관심제품'},
            {key: 'phone', label: '연락처'},
            {key: 'address', label: '주소'},
            {key: 'callTime', label: '통화가능시간'},
            {key: 'daytime', label: '낮시간설치'},
            {key: 'sinkInfo', label: '싱크대정보'},
            {key: 'message', label: '문의메시지'},
            {key: 'created_at', label: '접수일시'}
        ];

        fields.forEach(function(field) {
            let value = inquiry[field.key] || '-';
            if (field.key === 'source' && !inquiry[field.key]) value = '일반';
            html += '<tr style="border-bottom:1px solid #eee;">';
            html += '<td style="padding:12px 10px; font-weight:600; color:#374151; width:100px; vertical-align:top;">' + field.label + '</td>';
            html += '<td style="padding:12px 10px; color:#1f2937;">' + escapeHtml(value) + '</td>';
            html += '</tr>';
        });

        html += '</table>';
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
