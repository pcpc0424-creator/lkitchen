<?php
/**
 * 러블리키친 관리자 - 이미지 업로드 API
 */
require_once 'config.php';
checkLogin();

header('Content-Type: application/json; charset=utf-8');

$type = $_GET['type'] ?? 'food';

// 업로드 경로 설정
$uploadPath = ($type === 'sink') ? UPLOAD_PATH_SINK : UPLOAD_PATH_FOOD;
$webPath = ($type === 'sink') ? '/potopo' : '/pototo';

// 디렉토리 확인 및 생성
if (!is_dir($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}

// 파일 업로드 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    $files = $_FILES['files'];
    $uploadedFiles = [];
    $errors = [];

    // 파일 배열 정리
    $fileCount = is_array($files['name']) ? count($files['name']) : 1;

    for ($i = 0; $i < $fileCount; $i++) {
        $name = is_array($files['name']) ? $files['name'][$i] : $files['name'];
        $tmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
        $error = is_array($files['error']) ? $files['error'][$i] : $files['error'];
        $size = is_array($files['size']) ? $files['size'][$i] : $files['size'];

        // 에러 체크
        if ($error !== UPLOAD_ERR_OK) {
            $errors[] = "{$name}: 업로드 실패 (에러 코드: {$error})";
            continue;
        }

        // 파일 크기 체크
        if ($size > MAX_FILE_SIZE) {
            $errors[] = "{$name}: 파일 크기가 10MB를 초과합니다.";
            continue;
        }

        // 확장자 체크
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            $errors[] = "{$name}: 허용되지 않는 파일 형식입니다.";
            continue;
        }

        // 이미지 파일인지 확인
        $imageInfo = getimagesize($tmpName);
        if ($imageInfo === false) {
            $errors[] = "{$name}: 유효하지 않은 이미지 파일입니다.";
            continue;
        }

        // 안전한 파일명 생성
        $newFilename = generateSafeFilename($name);
        $destination = $uploadPath . '/' . $newFilename;

        // 파일 이동
        if (move_uploaded_file($tmpName, $destination)) {
            $uploadedFiles[] = [
                'filename' => $newFilename,
                'original' => $name,
                'size' => $size,
                'url' => SITE_URL . $webPath . '/' . $newFilename
            ];
        } else {
            $errors[] = "{$name}: 파일 저장에 실패했습니다.";
        }
    }

    // 응답
    $success = !empty($uploadedFiles);
    $message = $success
        ? count($uploadedFiles) . '개 파일이 업로드되었습니다.'
        : '업로드에 실패했습니다.';

    if (!empty($errors)) {
        $message .= ' (오류: ' . implode(', ', $errors) . ')';
    }

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'files' => $uploadedFiles,
        'errors' => $errors
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 파일 삭제 처리
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']))) {
    $filename = $_POST['filename'] ?? $_GET['filename'] ?? '';

    if (empty($filename)) {
        jsonResponse(false, '파일명이 지정되지 않았습니다.');
    }

    // 경로 조작 방지
    $filename = basename($filename);
    $filepath = $uploadPath . '/' . $filename;

    if (file_exists($filepath)) {
        if (unlink($filepath)) {
            jsonResponse(true, '파일이 삭제되었습니다.');
        } else {
            jsonResponse(false, '파일 삭제에 실패했습니다.');
        }
    } else {
        jsonResponse(false, '파일을 찾을 수 없습니다.');
    }
}

// 파일 목록 조회
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['list'])) {
    $files = [];
    $pattern = $uploadPath . '/*.{jpg,jpeg,png,gif,webp}';

    foreach (glob($pattern, GLOB_BRACE) as $file) {
        $filename = basename($file);
        $files[] = [
            'filename' => $filename,
            'url' => SITE_URL . $webPath . '/' . $filename,
            'size' => filesize($file),
            'modified' => date('Y-m-d H:i:s', filemtime($file))
        ];
    }

    // 최신순 정렬
    usort($files, function($a, $b) {
        return strtotime($b['modified']) - strtotime($a['modified']);
    });

    jsonResponse(true, '', $files);
}

jsonResponse(false, '잘못된 요청입니다.');
