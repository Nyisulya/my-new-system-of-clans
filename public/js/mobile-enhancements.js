/**
 * Mobile Enhancements for Clan Management System
 * Touch gestures, dynamic behaviors, and mobile-specific interactions
 */

(function ($) {
    'use strict';

    // Detect mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const isTablet = /iPad|Android/i.test(navigator.userAgent) && window.innerWidth >= 768;

    /**
     * Initialize all mobile enhancements
     */
    function initMobileEnhancements() {
        if (isMobile || isTablet) {
            addMobileBodyClass();
            enhanceTableResponsiveness();
            improveFormExperience();
            optimizeNavigation();
            handleOrientationChange();
            addSwipeGestures();
            optimizeModals();
            enhanceTouchFeedback();
        }
    }

    /**
     * Add mobile class to body for CSS targeting
     */
    function addMobileBodyClass() {
        if (isMobile) {
            $('body').addClass('is-mobile');
        }
        if (isTablet) {
            $('body').addClass('is-tablet');
        }
    }

    /**
     * Enhance table responsiveness
     * Convert tables to cards on very small screens
     */
    function enhanceTableResponsiveness() {
        // Add table-responsive wrapper if not exists
        $('.table').each(function () {
            if (!$(this).parent().hasClass('table-responsive')) {
                $(this).wrap('<div class="table-responsive"></div>');
            }
        });

        // Optional: Convert tables to cards on mobile
        if (window.innerWidth < 576) {
            convertTablesToCards();
        }
    }

    /**
     * Convert tables to card layout on very small screens
     */
    function convertTablesToCards() {
        $('.table:not(.no-mobile-cards)').each(function () {
            const $table = $(this);
            const headers = [];

            // Get headers
            $table.find('thead th').each(function () {
                headers.push($(this).text().trim());
            });

            // Add data-label attributes
            $table.find('tbody tr').each(function () {
                $(this).find('td').each(function (index) {
                    if (headers[index]) {
                        $(this).attr('data-label', headers[index]);
                    }
                });
            });

            // Add mobile cards class
            $table.addClass('table-mobile-cards');
        });
    }

    /**
     * Improve form experience on mobile
     */
    function improveFormExperience() {
        // Auto-scroll to validation errors
        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }

        // Improve Select2 on mobile
        if ($.fn.select2) {
            $('.select2').on('select2:open', function () {
                // Add mobile class to dropdown
                $('.select2-dropdown').addClass('select2-mobile');

                // Scroll to keep dropdown in view
                setTimeout(function () {
                    const $dropdown = $('.select2-dropdown');
                    if ($dropdown.length) {
                        $('html, body').animate({
                            scrollTop: $dropdown.offset().top - 100
                        }, 300);
                    }
                }, 100);
            });
        }

        // Make file inputs more user-friendly
        $('input[type="file"]').on('change', function () {
            const fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Choose file');
        });

        // Add loading state to form submissions
        $('form').on('submit', function () {
            const $form = $(this);
            const $submitBtn = $form.find('[type="submit"]');

            if (!$submitBtn.prop('disabled')) {
                $submitBtn.prop('disabled', true);
                $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                // Re-enable after 5 seconds as failsafe
                setTimeout(function () {
                    $submitBtn.prop('disabled', false);
                    $submitBtn.html($submitBtn.data('original-text') || 'Submit');
                }, 5000);
            }
        });
    }

    /**
     * Optimize navigation for mobile
     */
    function optimizeNavigation() {
        // Close sidebar when clicking outside on mobile
        $(document).on('click', function (e) {
            if (window.innerWidth < 992) {
                if (!$(e.target).closest('.main-sidebar, .navbar-nav').length) {
                    if ($('body').hasClass('sidebar-open')) {
                        $('body').removeClass('sidebar-open');
                    }
                }
            }
        });

        // Smooth scroll to top button
        if ($('.scroll-to-top').length === 0) {
            $('body').append('<button class="btn btn-primary scroll-to-top" style="display:none; position:fixed; bottom:20px; right:20px; z-index:1000; border-radius:50%; width:50px; height:50px;"><i class="fas fa-arrow-up"></i></button>');
        }

        $(window).scroll(function () {
            if ($(this).scrollTop() > 300) {
                $('.scroll-to-top').fadeIn();
            } else {
                $('.scroll-to-top').fadeOut();
            }
        });

        $('.scroll-to-top').on('click', function () {
            $('html, body').animate({ scrollTop: 0 }, 600);
            return false;
        });
    }

    /**
     * Handle orientation changes
     */
    function handleOrientationChange() {
        let lastOrientation = window.orientation || 0;

        $(window).on('orientationchange resize', function () {
            const currentOrientation = window.orientation || 0;

            if (currentOrientation !== lastOrientation) {
                lastOrientation = currentOrientation;

                // Refresh maps if present
                if (typeof window.memberMap !== 'undefined') {
                    setTimeout(function () {
                        window.memberMap.invalidateSize();
                    }, 300);
                }

                // Refresh charts if present
                if (typeof Chart !== 'undefined') {
                    Chart.helpers.each(Chart.instances, function (instance) {
                        instance.resize();
                    });
                }

                // Re-calculate table responsiveness
                enhanceTableResponsiveness();
            }
        });
    }

    /**
     * Add swipe gestures for galleries and carousels
     */
    function addSwipeGestures() {
        let touchStartX = 0;
        let touchEndX = 0;

        $('.gallery, .carousel, .swipeable').on('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
        });

        $('.gallery, .carousel, .swipeable').on('touchend', function (e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe($(this));
        });

        function handleSwipe($element) {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    // Swipe left - next
                    $element.find('.carousel-control-next, .next').trigger('click');
                } else {
                    // Swipe right - previous
                    $element.find('.carousel-control-prev, .prev').trigger('click');
                }
            }
        }
    }

    /**
     * Optimize modals for mobile
     */
    function optimizeModals() {
        $('.modal').on('shown.bs.modal', function () {
            // Prevent body scroll when modal is open
            $('body').addClass('modal-open-mobile');

            // Adjust modal position on mobile
            if (window.innerWidth < 768) {
                const $modal = $(this);
                const $modalDialog = $modal.find('.modal-dialog');

                // Center modal vertically
                const windowHeight = $(window).height();
                const modalHeight = $modalDialog.height();

                if (modalHeight < windowHeight) {
                    $modalDialog.css({
                        'margin-top': ((windowHeight - modalHeight) / 2) + 'px'
                    });
                }
            }
        });

        $('.modal').on('hidden.bs.modal', function () {
            $('body').removeClass('modal-open-mobile');
        });
    }

    /**
     * Enhance touch feedback for buttons
     */
    function enhanceTouchFeedback() {
        // Add active state for touch
        $('.btn, .card, .list-group-item, .nav-link').on('touchstart', function () {
            $(this).addClass('touch-active');
        });

        $('.btn, .card, .list-group-item, .nav-link').on('touchend touchcancel', function () {
            const $el = $(this);
            setTimeout(function () {
                $el.removeClass('touch-active');
            }, 150);
        });

        // Add CSS for touch-active state
        if ($('#touch-feedback-styles').length === 0) {
            $('head').append('<style id="touch-feedback-styles">.touch-active { opacity: 0.7; transform: scale(0.98); transition: all 0.1s ease; }</style>');
        }
    }

    /**
     * Optimize images for mobile
     */
    function optimizeImages() {
        // Lazy load images on mobile
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img.lazy').forEach(function (img) {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Add pull-to-refresh functionality
     */
    function addPullToRefresh() {
        let startY = 0;
        let currentY = 0;
        let pulling = false;

        $(window).on('touchstart', function (e) {
            if (window.pageYOffset === 0) {
                startY = e.touches[0].pageY;
                pulling = true;
            }
        });

        $(window).on('touchmove', function (e) {
            if (!pulling) return;

            currentY = e.touches[0].pageY;
            const distance = currentY - startY;

            if (distance > 0 && distance < 100) {
                e.preventDefault();
            }
        });

        $(window).on('touchend', function () {
            if (!pulling) return;

            const distance = currentY - startY;

            if (distance > 100) {
                // Reload page
                location.reload();
            }

            pulling = false;
            startY = 0;
            currentY = 0;
        });
    }

    /**
     * Debug helper for mobile development
     */
    function mobileDebug() {
        if (window.location.search.includes('debug=mobile')) {
            const debugInfo = {
                userAgent: navigator.userAgent,
                isMobile: isMobile,
                isTablet: isTablet,
                screenWidth: window.screen.width,
                screenHeight: window.screen.height,
                windowWidth: window.innerWidth,
                windowHeight: window.innerHeight,
                devicePixelRatio: window.devicePixelRatio
            };

            console.log('Mobile Debug Info:', debugInfo);

            // Show debug overlay
            $('body').prepend(`
                <div style="position:fixed; top:0; left:0; right:0; background:rgba(0,0,0,0.9); color:#fff; padding:10px; z-index:9999; font-size:12px; max-height:200px; overflow:auto;">
                    <strong>Mobile Debug</strong><br>
                    Screen: ${debugInfo.screenWidth}x${debugInfo.screenHeight}<br>
                    Window: ${debugInfo.windowWidth}x${debugInfo.windowHeight}<br>
                    DPR: ${debugInfo.devicePixelRatio}<br>
                    Mobile: ${debugInfo.isMobile ? 'Yes' : 'No'}<br>
                    Tablet: ${debugInfo.isTablet ? 'Yes' : 'No'}
                </div>
            `);
        }
    }

    // Initialize when document is ready
    $(document).ready(function () {
        initMobileEnhancements();
        optimizeImages();
        mobileDebug();

        // Add helpful console message
        if (isMobile || isTablet) {
            console.log('✓ Mobile enhancements loaded');
        }
    });

    // Re-initialize on AJAX content load (for SPAs or dynamic content)
    $(document).on('ajaxComplete', function () {
        enhanceTableResponsiveness();
        improveFormExperience();
    });

})(jQuery);
