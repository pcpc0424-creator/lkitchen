/**
 * Lovely Kitchen Premium - Advanced JavaScript
 * 최신 트렌드를 반영한 프리미엄 인터랙션
 */

// ============================================
// App Initialization
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    // Initialize all modules
    Preloader.init();
    Header.init();
    MobileNav.init();
    HeroSection.init();
    TypedText.init();
    CountUp.init();
    ProductCarousels.init();
    TestimonialSlider.init();
    PartnersSlider.init();
    ScrollProgress.init();
    FloatingButtons.init();
    QuickPopup.init();
    CustomCursor.init();

    // Initialize AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50,
            disable: false
        });
    }

    // Initialize GSAP ScrollTrigger (only for non-essential animations)
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
        // GSAPAnimations.init(); // Disabled - causes visibility issues
    }
});

// ============================================
// Preloader Module
// ============================================
const Preloader = {
    init() {
        const preloader = document.getElementById('preloader');
        if (!preloader) return;

        window.addEventListener('load', () => {
            setTimeout(() => {
                preloader.classList.add('hidden');
                document.body.style.overflow = '';
            }, 500);
        });

        // Fallback - hide after 3 seconds
        setTimeout(() => {
            preloader.classList.add('hidden');
            document.body.style.overflow = '';
        }, 3000);
    }
};

// ============================================
// Header Module
// ============================================
const Header = {
    header: null,
    lastScrollY: 0,

    init() {
        this.header = document.getElementById('header');
        if (!this.header) return;

        this.handleScroll();
        window.addEventListener('scroll', () => this.handleScroll(), { passive: true });
    },

    handleScroll() {
        const scrollY = window.scrollY;

        // Add/remove scrolled class
        if (scrollY > 50) {
            this.header.classList.add('scrolled');
        } else {
            this.header.classList.remove('scrolled');
        }

        this.lastScrollY = scrollY;
    }
};

// ============================================
// Mobile Navigation Module
// ============================================
const MobileNav = {
    nav: null,
    toggle: null,
    close: null,
    overlay: null,

    init() {
        this.nav = document.getElementById('mobileNav');
        this.toggle = document.getElementById('mobileToggle');
        this.close = document.getElementById('mobileClose');
        this.overlay = document.getElementById('mobileOverlay');

        if (!this.nav || !this.toggle) return;

        this.bindEvents();
    },

    bindEvents() {
        this.toggle.addEventListener('click', () => this.open());

        if (this.close) {
            this.close.addEventListener('click', () => this.closeNav());
        }

        if (this.overlay) {
            this.overlay.addEventListener('click', () => this.closeNav());
        }

        // Submenu toggle
        const submenus = this.nav.querySelectorAll('.has-submenu');
        submenus.forEach(item => {
            const link = item.querySelector('a');
            link.addEventListener('click', (e) => {
                e.preventDefault();
                item.classList.toggle('active');
            });
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.closeNav();
        });
    },

    open() {
        this.nav.classList.add('active');
        this.overlay.classList.add('active');
        this.toggle.classList.add('active');
        document.body.style.overflow = 'hidden';
    },

    closeNav() {
        this.nav.classList.remove('active');
        this.overlay.classList.remove('active');
        this.toggle.classList.remove('active');
        document.body.style.overflow = '';
    }
};

// ============================================
// Hero Section Module
// ============================================
const HeroSection = {
    slides: null,
    currentSlide: 0,
    interval: null,

    init() {
        this.slides = document.querySelectorAll('.hero-slide');
        if (this.slides.length === 0) return;

        this.startSlideshow();
    },

    startSlideshow() {
        this.interval = setInterval(() => {
            this.nextSlide();
        }, 6000);
    },

    nextSlide() {
        this.slides[this.currentSlide].classList.remove('active');
        this.currentSlide = (this.currentSlide + 1) % this.slides.length;
        this.slides[this.currentSlide].classList.add('active');
    }
};

// ============================================
// Typed Text Module
// ============================================
const TypedText = {
    element: null,
    texts: ['거주 환경의 커다란 발전을', '청결한 주방의 시작을', '편리한 생활의 완성을'],
    currentText: 0,
    charIndex: 0,
    isDeleting: false,
    typeSpeed: 100,
    deleteSpeed: 50,
    pauseTime: 2000,

    init() {
        this.element = document.getElementById('typedText');
        if (!this.element) return;

        setTimeout(() => this.type(), 1000);
    },

    type() {
        const current = this.texts[this.currentText];

        if (this.isDeleting) {
            this.element.textContent = current.substring(0, this.charIndex - 1);
            this.charIndex--;
        } else {
            this.element.textContent = current.substring(0, this.charIndex + 1);
            this.charIndex++;
        }

        let timeout = this.isDeleting ? this.deleteSpeed : this.typeSpeed;

        if (!this.isDeleting && this.charIndex === current.length) {
            timeout = this.pauseTime;
            this.isDeleting = true;
        } else if (this.isDeleting && this.charIndex === 0) {
            this.isDeleting = false;
            this.currentText = (this.currentText + 1) % this.texts.length;
            timeout = 500;
        }

        setTimeout(() => this.type(), timeout);
    }
};

// ============================================
// Count Up Animation Module
// ============================================
const CountUp = {
    init() {
        const counters = document.querySelectorAll('.stat-number[data-count]');
        if (counters.length === 0) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animate(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => observer.observe(counter));
    },

    animate(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;

        const update = () => {
            current += step;
            if (current < target) {
                element.textContent = Math.floor(current).toLocaleString();
                requestAnimationFrame(update);
            } else {
                element.textContent = target.toLocaleString();
            }
        };

        requestAnimationFrame(update);
    }
};

// ============================================
// Product Carousels Module
// ============================================
const ProductCarousels = {
    init() {
        const carousels = document.querySelectorAll('.product-carousel');
        if (carousels.length === 0 || typeof Swiper === 'undefined') return;

        carousels.forEach(carousel => {
            new Swiper(carousel, {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: true,
                autoplay: {
                    delay: 4000,
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
    }
};

// ============================================
// Testimonial Slider Module
// ============================================
const TestimonialSlider = {
    init() {
        const slider = document.querySelector('.testimonials-slider');
        if (!slider || typeof Swiper === 'undefined') return;

        new Swiper(slider, {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: slider.querySelector('.swiper-pagination'),
                clickable: true,
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                }
            }
        });
    }
};

// ============================================
// Partners Slider Module
// ============================================
const PartnersSlider = {
    init() {
        const slider = document.querySelector('.partners-slider');
        if (!slider || typeof Swiper === 'undefined') return;

        new Swiper(slider, {
            slidesPerView: 2,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 0,
                disableOnInteraction: false,
            },
            speed: 3000,
            freeMode: true,
            breakpoints: {
                480: { slidesPerView: 3 },
                768: { slidesPerView: 4 },
                1024: { slidesPerView: 5 },
                1200: { slidesPerView: 6 }
            }
        });
    }
};

// ============================================
// Scroll Progress Module
// ============================================
const ScrollProgress = {
    bar: null,

    init() {
        this.bar = document.getElementById('scrollProgress');
        if (!this.bar) return;

        window.addEventListener('scroll', () => this.update(), { passive: true });
    },

    update() {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const progress = (scrollTop / docHeight) * 100;
        this.bar.style.width = `${progress}%`;
    }
};

// ============================================
// Floating Buttons Module
// ============================================
const FloatingButtons = {
    scrollTopBtn: null,

    init() {
        this.scrollTopBtn = document.getElementById('scrollTop');
        if (!this.scrollTopBtn) return;

        window.addEventListener('scroll', () => this.handleScroll(), { passive: true });
        this.scrollTopBtn.addEventListener('click', () => this.scrollToTop());
    },

    handleScroll() {
        if (window.scrollY > 500) {
            this.scrollTopBtn.classList.add('visible');
        } else {
            this.scrollTopBtn.classList.remove('visible');
        }
    },

    scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
};

// ============================================
// Quick Popup Module
// ============================================
const QuickPopup = {
    popup: null,
    closeBtn: null,
    overlay: null,

    init() {
        this.popup = document.getElementById('quickPopup');
        this.closeBtn = document.getElementById('popupClose');
        this.overlay = document.getElementById('popupOverlay');

        if (!this.popup) return;

        // Check session storage
        if (!sessionStorage.getItem('popupClosed')) {
            setTimeout(() => this.show(), 5000);
        }

        this.bindEvents();
    },

    bindEvents() {
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.hide());
        }
        if (this.overlay) {
            this.overlay.addEventListener('click', () => this.hide());
        }
    },

    show() {
        this.popup.classList.add('active');
        this.overlay.classList.add('active');
    },

    hide() {
        this.popup.classList.remove('active');
        this.overlay.classList.remove('active');
        sessionStorage.setItem('popupClosed', 'true');
    }
};

// ============================================
// Custom Cursor Module
// ============================================
const CustomCursor = {
    dot: null,
    outline: null,

    init() {
        // Only on desktop with fine pointer
        if (window.matchMedia('(hover: hover) and (pointer: fine)').matches === false) return;

        this.dot = document.getElementById('cursorDot');
        this.outline = document.getElementById('cursorOutline');

        if (!this.dot || !this.outline) return;

        document.addEventListener('mousemove', (e) => this.move(e));

        // Add hover effect to interactive elements
        const interactives = document.querySelectorAll('a, button, .product-card, .gallery-item');
        interactives.forEach(el => {
            el.addEventListener('mouseenter', () => document.body.classList.add('cursor-hover'));
            el.addEventListener('mouseleave', () => document.body.classList.remove('cursor-hover'));
        });
    },

    move(e) {
        this.dot.style.left = `${e.clientX}px`;
        this.dot.style.top = `${e.clientY}px`;

        // Outline follows with slight delay
        setTimeout(() => {
            this.outline.style.left = `${e.clientX}px`;
            this.outline.style.top = `${e.clientY}px`;
        }, 50);
    }
};

// ============================================
// GSAP Animations Module
// ============================================
const GSAPAnimations = {
    init() {
        this.parallaxEffects();
        this.revealAnimations();
    },

    parallaxEffects() {
        // Hero parallax
        gsap.to('.hero-bg', {
            scrollTrigger: {
                trigger: '.hero',
                start: 'top top',
                end: 'bottom top',
                scrub: true
            },
            y: 100,
            ease: 'none'
        });

        // Innovation section image float
        gsap.to('.innovation-image', {
            scrollTrigger: {
                trigger: '.innovation-section',
                start: 'top bottom',
                end: 'bottom top',
                scrub: true
            },
            y: -50,
            ease: 'none'
        });
    },

    revealAnimations() {
        // Section headers
        gsap.utils.toArray('.section-header').forEach(header => {
            gsap.from(header, {
                scrollTrigger: {
                    trigger: header,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                },
                y: 50,
                opacity: 0,
                duration: 0.8,
                ease: 'power3.out'
            });
        });

        // Product cards stagger
        gsap.utils.toArray('.products-grid').forEach(grid => {
            const cards = grid.querySelectorAll('.product-card');
            gsap.from(cards, {
                scrollTrigger: {
                    trigger: grid,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                },
                y: 80,
                opacity: 0,
                duration: 0.6,
                stagger: 0.15,
                ease: 'power3.out'
            });
        });

        // Promise cards
        gsap.utils.toArray('.promise-grid').forEach(grid => {
            const cards = grid.querySelectorAll('.promise-card');
            gsap.from(cards, {
                scrollTrigger: {
                    trigger: grid,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                },
                y: 60,
                opacity: 0,
                duration: 0.5,
                stagger: 0.1,
                ease: 'power2.out'
            });
        });

        // Gallery items
        gsap.utils.toArray('.gallery-grid').forEach(grid => {
            const items = grid.querySelectorAll('.gallery-item');
            gsap.from(items, {
                scrollTrigger: {
                    trigger: grid,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                },
                scale: 0.8,
                opacity: 0,
                duration: 0.5,
                stagger: 0.05,
                ease: 'back.out(1.2)'
            });
        });
    }
};

// ============================================
// Smooth Scroll for Anchor Links
// ============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;

        const target = document.querySelector(targetId);
        if (target) {
            e.preventDefault();
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// ============================================
// Lazy Loading Images Enhancement
// ============================================
if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
        img.src = img.dataset.src || img.src;
    });
} else {
    // Fallback for browsers that don't support lazy loading
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
    document.body.appendChild(script);
}

// ============================================
// Performance Optimization - Debounce
// ============================================
function debounce(func, wait = 20) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ============================================
// Performance Optimization - Throttle
// ============================================
function throttle(func, limit = 100) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// ============================================
// Console Branding
// ============================================
console.log(
    '%c Lovely Kitchen %c Premium Website ',
    'background: linear-gradient(135deg, #e94560, #ff6b6b); color: white; padding: 10px 20px; font-size: 16px; font-weight: bold; border-radius: 5px 0 0 5px;',
    'background: #1a1a2e; color: white; padding: 10px 20px; font-size: 16px; border-radius: 0 5px 5px 0;'
);
