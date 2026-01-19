/**
 * 러블리키친 관리자 JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }

    // Tab Functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.tab;

            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            this.classList.add('active');
            document.getElementById('tab-' + tabId).classList.add('active');
        });
    });

    // Image Upload - Drop Zone
    initializeDropZones();

    // Review Form Image Upload
    initializeReviewImageUpload();
});

/**
 * 드롭존 초기화
 */
function initializeDropZones() {
    const dropZones = document.querySelectorAll('.upload-zone');

    dropZones.forEach(zone => {
        const type = zone.dataset.type;
        const fileInput = zone.querySelector('.file-input');

        // 클릭 시 파일 선택
        zone.addEventListener('click', () => fileInput.click());

        // 드래그 이벤트
        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('dragover');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('dragover');
        });

        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files, type);
        });

        // 파일 선택 이벤트
        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files, type);
            e.target.value = ''; // 초기화
        });
    });
}

/**
 * 파일 업로드 처리
 */
function handleFiles(files, type) {
    if (files.length === 0) return;

    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }

    // 진행 상태 표시
    const progressEl = document.getElementById(type + 'Progress');
    const progressFill = progressEl?.querySelector('.progress-fill');
    const progressText = progressEl?.querySelector('.progress-text');

    if (progressEl) {
        progressEl.style.display = 'block';
        progressFill.style.width = '0%';
        progressText.textContent = '업로드 준비 중...';
    }

    // AJAX 업로드
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'upload.php?type=' + type, true);

    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable && progressFill) {
            const percent = (e.loaded / e.total) * 100;
            progressFill.style.width = percent + '%';
            progressText.textContent = '업로드 중... ' + Math.round(percent) + '%';
        }
    };

    xhr.onload = function() {
        if (progressEl) {
            progressEl.style.display = 'none';
        }

        try {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                showToast(response.message, 'success');
                // 페이지 새로고침으로 이미지 목록 갱신
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(response.message, 'error');
            }
        } catch (e) {
            showToast('업로드 중 오류가 발생했습니다.', 'error');
        }
    };

    xhr.onerror = function() {
        if (progressEl) progressEl.style.display = 'none';
        showToast('업로드에 실패했습니다.', 'error');
    };

    xhr.send(formData);
}

/**
 * 후기 폼 이미지 업로드 초기화
 */
function initializeReviewImageUpload() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const previewList = document.getElementById('previewList');
    const imagesInput = document.getElementById('imagesInput');

    if (!dropZone) return;

    // 클릭 시 파일 선택
    dropZone.addEventListener('click', () => fileInput.click());

    // 드래그 이벤트
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        uploadReviewImages(e.dataTransfer.files);
    });

    // 파일 선택
    fileInput.addEventListener('change', (e) => {
        uploadReviewImages(e.target.files);
        e.target.value = '';
    });
}

/**
 * 후기 이미지 업로드
 */
function uploadReviewImages(files) {
    if (files.length === 0) return;

    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }

    // 업로드 타입 결정
    const uploadUrl = typeof reviewType !== 'undefined' ? 'upload.php?type=' + reviewType : 'upload.php?type=food';

    fetch(uploadUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.files) {
            data.files.forEach(file => {
                addImagePreview(file.filename, file.url);
            });
            updateImagesInput();
            showToast(data.message, 'success');
        } else {
            showToast(data.message || '업로드 실패', 'error');
        }
    })
    .catch(error => {
        showToast('업로드 중 오류가 발생했습니다.', 'error');
    });
}

/**
 * 이미지 프리뷰 추가
 */
function addImagePreview(filename, url) {
    const previewList = document.getElementById('previewList');
    if (!previewList) return;

    const previewItem = document.createElement('div');
    previewItem.className = 'preview-item';
    previewItem.dataset.filename = filename;
    previewItem.innerHTML = `
        <img src="${url}" alt="">
        <button type="button" class="remove-btn" onclick="removeImage(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    previewList.appendChild(previewItem);
}

/**
 * 이미지 제거
 */
function removeImage(btn) {
    const previewItem = btn.closest('.preview-item');
    if (previewItem) {
        previewItem.remove();
        updateImagesInput();
    }
}

/**
 * 이미지 입력 필드 업데이트
 */
function updateImagesInput() {
    const previewList = document.getElementById('previewList');
    const imagesInput = document.getElementById('imagesInput');

    if (!previewList || !imagesInput) return;

    const images = [];
    previewList.querySelectorAll('.preview-item').forEach(item => {
        images.push(item.dataset.filename);
    });

    imagesInput.value = JSON.stringify(images);
}

/**
 * 후기 삭제
 */
function deleteReview(id) {
    if (!confirm('정말 이 후기를 삭제하시겠습니까?')) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);

    fetch(location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            // 행 제거
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) row.remove();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        showToast('삭제 중 오류가 발생했습니다.', 'error');
    });
}

/**
 * 이미지 삭제
 */
function deleteImage(filename, type) {
    if (!confirm('정말 이 이미지를 삭제하시겠습니까?')) return;

    const formData = new FormData();
    formData.append('delete', '1');
    formData.append('filename', filename);

    fetch('upload.php?type=' + type, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            // 이미지 아이템 제거
            const item = document.querySelector(`.image-item[data-filename="${filename}"]`);
            if (item) item.remove();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        showToast('삭제 중 오류가 발생했습니다.', 'error');
    });
}

/**
 * 이미지 URL 복사
 */
function copyImageUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        showToast('URL이 복사되었습니다.', 'success');
    }).catch(() => {
        // Fallback
        const textarea = document.createElement('textarea');
        textarea.value = url;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showToast('URL이 복사되었습니다.', 'success');
    });
}

/**
 * 토스트 메시지 표시
 */
function showToast(message, type = 'success') {
    let toast = document.getElementById('toast');

    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.className = 'toast ' + type;

    // 표시
    setTimeout(() => toast.classList.add('show'), 10);

    // 숨기기
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}
