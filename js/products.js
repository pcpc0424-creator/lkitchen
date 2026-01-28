/**
 * Products Page JavaScript
 * 러블리키친 제품 페이지 스크립트
 */

document.addEventListener('DOMContentLoaded', function() {
    // Product Filter Functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('.product-page-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const filter = this.dataset.filter;

            productCards.forEach(card => {
                const category = card.dataset.category;

                // Remove animation classes
                card.classList.remove('show', 'hide');

                if (filter === 'all' || category === filter) {
                    card.classList.remove('hide');
                    card.classList.add('show');
                    card.style.display = 'block';
                } else {
                    card.classList.add('hide');
                    setTimeout(() => {
                        if (card.classList.contains('hide')) {
                            card.style.display = 'none';
                        }
                    }, 300);
                }
            });
        });
    });

    // Initialize all cards as visible
    productCards.forEach(card => {
        card.classList.add('show');
    });

    // Make product cards clickable (navigate to detail page)
    productCards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function(e) {
            // Don't navigate if clicking on buttons or links
            if (e.target.closest('.btn-inquiry') ||
                e.target.closest('.btn-call') ||
                e.target.closest('.product-detail-btn') ||
                e.target.closest('.swiper-pagination')) {
                return;
            }

            // Find the product link in this card
            const productLink = this.querySelector('.product-link');
            if (productLink) {
                window.location.href = productLink.href;
            }
        });
    });

    // Initialize Product Page Carousels
    const productPageCarousels = document.querySelectorAll('.product-page-carousel');
    productPageCarousels.forEach(carousel => {
        new Swiper(carousel, {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: carousel.querySelector('.swiper-pagination'),
                clickable: true,
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            }
        });
    });

    // Product Image Hover Effect Enhancement
    const productImages = document.querySelectorAll('.product-page-image');

    productImages.forEach(container => {
        container.addEventListener('mouseenter', function() {
            const images = this.querySelectorAll('.swiper-slide img');
            images.forEach(img => {
                img.style.transform = 'scale(1.08)';
            });
        });

        container.addEventListener('mouseleave', function() {
            const images = this.querySelectorAll('.swiper-slide img');
            images.forEach(img => {
                img.style.transform = 'scale(1)';
            });
        });
    });

    // Smooth scroll for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Price number animation on scroll
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };

    const priceObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const priceEl = entry.target.querySelector('.price-value');
                if (priceEl && !priceEl.classList.contains('animated')) {
                    animatePrice(priceEl);
                    priceEl.classList.add('animated');
                }
            }
        });
    }, observerOptions);

    productCards.forEach(card => {
        priceObserver.observe(card);
    });

    function animatePrice(element) {
        const finalValue = element.textContent.replace(/,/g, '');
        const duration = 1000;
        const startTime = performance.now();
        const startValue = 0;

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Easing function
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);

            const currentValue = Math.floor(startValue + (finalValue - startValue) * easeOutQuart);
            element.textContent = currentValue.toLocaleString();

            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }

        requestAnimationFrame(update);
    }

    // Add loading class removal for images
    const images = document.querySelectorAll('.product-page-image img');
    images.forEach(img => {
        if (img.complete) {
            img.classList.add('loaded');
        } else {
            img.addEventListener('load', function() {
                this.classList.add('loaded');
            });
        }
    });
});
