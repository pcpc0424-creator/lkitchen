/**
 * Product Detail Page JavaScript
 * 러블리키친 제품 상세 페이지 스크립트
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Gallery Swiper
    initGallerySwiper();
});

function initGallerySwiper() {
    // Thumbs Swiper
    const galleryThumbs = new Swiper('.gallery-thumbs-swiper', {
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: {
            320: {
                slidesPerView: 3,
                spaceBetween: 8,
            },
            480: {
                slidesPerView: 4,
                spaceBetween: 10,
            },
            768: {
                slidesPerView: 4,
                spaceBetween: 12,
            }
        }
    });

    // Main Gallery Swiper
    const galleryMain = new Swiper('.gallery-main-swiper', {
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        thumbs: {
            swiper: galleryThumbs,
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
    });
}
