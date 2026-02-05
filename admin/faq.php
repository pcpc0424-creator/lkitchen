<?php
/**
 * 러블리키친 FAQ 관리
 */
require_once 'config.php';
checkLogin();

$action = $_GET['action'] ?? 'list';
$editId = $_GET['id'] ?? null;

// FAQ 데이터 파일
$faqFile = 'faq.json';
$faqData = readJsonData($faqFile);

// FAQ 추가/수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'CSRF 토큰이 유효하지 않습니다.';
    } else {
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $category = trim($_POST['category'] ?? '일반');

        if (empty($question) || empty($answer)) {
            $error = '질문과 답변을 모두 입력해주세요.';
        } else {
            if ($action === 'edit' && $editId) {
                // 수정
                foreach ($faqData as &$faq) {
                    if ($faq['id'] === $editId) {
                        $faq['question'] = $question;
                        $faq['answer'] = $answer;
                        $faq['category'] = $category;
                        $faq['updated_at'] = date('Y-m-d H:i:s');
                        break;
                    }
                }
                unset($faq);
                writeJsonData($faqFile, $faqData);
                header('Location: faq.php?updated=1');
                exit;
            } else {
                // 추가
                $newFaq = [
                    'id' => uniqid(),
                    'question' => $question,
                    'answer' => $answer,
                    'category' => $category,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                array_unshift($faqData, $newFaq);
                writeJsonData($faqFile, $faqData);
                header('Location: faq.php?success=1');
                exit;
            }
        }
    }
}

// FAQ 삭제 처리
if ($action === 'delete' && $editId) {
    $faqData = array_filter($faqData, function($faq) use ($editId) {
        return $faq['id'] !== $editId;
    });
    $faqData = array_values($faqData);
    writeJsonData($faqFile, $faqData);
    header('Location: faq.php?deleted=1');
    exit;
}

// 수정할 FAQ 데이터 가져오기
$editFaq = null;
if ($action === 'edit' && $editId) {
    foreach ($faqData as $faq) {
        if ($faq['id'] === $editId) {
            $editFaq = $faq;
            break;
        }
    }
}

// 새 문의 수 계산 (사이드바용)
$inquiries = readJsonData('inquiries.json');
$consultations = readJsonData('consultations.json');
$newInquiries = array_filter($inquiries, function($inquiry) {
    return $inquiry['status'] === 'new';
});

// 새 상담 수 계산
$newConsultations = array_filter($consultations, function($consultation) {
    return $consultation['status'] === 'new';
});

$categories = ['일반', '제품', '설치', '배송', 'A/S', '기타'];
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ 관리 | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css?v=2">
    <link rel="icon" type="image/png" href="/수정/fhrh.png">
    <style>
        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .faq-item {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .faq-item .faq-question {
            font-weight: 600;
            font-size: 1.05rem;
            color: #051535;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .faq-item .faq-question i {
            color: #3498db;
        }
        .faq-item .faq-answer {
            color: #666;
            line-height: 1.7;
            padding-left: 28px;
            white-space: pre-wrap;
        }
        .faq-item .faq-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .faq-item .faq-category {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .faq-item .faq-actions {
            display: flex;
            gap: 8px;
        }
        .add-form {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }
        .form-group textarea {
            min-height: 120px;
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
                    <li class="nav-item active">
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
                    <h1 class="page-title">FAQ 관리</h1>
                </div>
                <div class="header-right">
                    <a href="<?php echo SITE_URL; ?>/질문과-답변/" target="_blank" class="btn-site">
                        <i class="fas fa-external-link-alt"></i>
                        질문과 답변 보기
                    </a>
                </div>
            </header>

            <div class="content-body">
                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> FAQ가 추가되었습니다.
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> FAQ가 수정되었습니다.
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> FAQ가 삭제되었습니다.
                </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <!-- FAQ 추가/수정 폼 -->
                <div class="add-form">
                    <h3 style="margin-bottom: 16px;">
                        <i class="fas fa-<?php echo $editFaq ? 'edit' : 'plus-circle'; ?>"></i>
                        <?php echo $editFaq ? 'FAQ 수정' : '새 FAQ 추가'; ?>
                    </h3>
                    <form method="POST" action="faq.php?action=<?php echo $editFaq ? 'edit&id=' . $editId : 'add'; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                        <div class="form-group">
                            <label for="category">카테고리</label>
                            <select id="category" name="category">
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo ($editFaq && $editFaq['category'] === $cat) ? 'selected' : ''; ?>>
                                    <?php echo $cat; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="question">질문 *</label>
                            <input type="text" id="question" name="question" required
                                   value="<?php echo $editFaq ? sanitize($editFaq['question']) : ''; ?>"
                                   placeholder="자주 묻는 질문을 입력하세요">
                        </div>

                        <div class="form-group">
                            <label for="answer">답변 *</label>
                            <textarea id="answer" name="answer" required
                                      placeholder="질문에 대한 답변을 입력하세요"><?php echo $editFaq ? sanitize($editFaq['answer']) : ''; ?></textarea>
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-<?php echo $editFaq ? 'save' : 'plus'; ?>"></i>
                                <?php echo $editFaq ? '수정 완료' : 'FAQ 추가'; ?>
                            </button>
                            <?php if ($editFaq): ?>
                            <a href="faq.php" class="btn-secondary">취소</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- FAQ 목록 -->
                <div class="section">
                    <h2 class="section-title">등록된 FAQ (<?php echo count($faqData); ?>개)</h2>

                    <?php if (empty($faqData)): ?>
                    <div class="empty-state">
                        <i class="fas fa-question-circle"></i>
                        <p>등록된 FAQ가 없습니다.</p>
                    </div>
                    <?php else: ?>
                    <div class="faq-list">
                        <?php foreach ($faqData as $faq): ?>
                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-question-circle"></i>
                                <?php echo sanitize($faq['question']); ?>
                            </div>
                            <div class="faq-answer"><?php echo nl2br(sanitize($faq['answer'])); ?></div>
                            <div class="faq-meta">
                                <span class="faq-category"><?php echo sanitize($faq['category'] ?? '일반'); ?></span>
                                <div class="faq-actions">
                                    <a href="faq.php?action=edit&id=<?php echo $faq['id']; ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i> 수정
                                    </a>
                                    <a href="faq.php?action=delete&id=<?php echo $faq['id']; ?>"
                                       class="btn-delete"
                                       onclick="return confirm('정말 삭제하시겠습니까?')">
                                        <i class="fas fa-trash"></i> 삭제
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
