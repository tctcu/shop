/*-----------------------------------------------------------------------------------

    Template Name: Lavaland Multipurpose  Landing Html5 Template
    Template URI: http://tf.itech-theme.com/lavaland-preview/
    Description: This is a Multipurpose Landing Html5 Template
    Author: cdibrandstudio
    Version: 1.0


    ---------------------------
        JS Index
    ---------------------------
    + Add ScrollUp Btn
    + Preloader
    + stickey Header
    + Humberger
    + Smoth Scroll
    + Active current Li
    + Swiper slider Activation
    + Parallax background
    + Class Toggling
    + Magnific Popup
    + Counter Up
    + slicknav
    + Firefly Activation / Canvas
    + Warm Canvas activation / Canvas
    + Isotop Masonary
    + countdown
    + google map activation

-----------------------------------------------------------------------------------*/

(function ($) {
    "use strict";

    /*================================
    Add ScrollUp Btn
    ==================================*/
    $("body").append("<a class='scroll_to_top' href='#top'><span class='fa fa-angle-up'></a>");
    $('body').attr('id', 'top');

    /*================================
    Preloader
    ==================================*/
    var preloader = $('#preloader');
    $(window).on('load', function () {
        preloader.fadeOut('slow', function () { $(this).remove(); });
    });

    /*================================
    stickey Header
    ==================================*/
    $(window).on('scroll', function () {
        var scroll = $(window).scrollTop(),
            mainHeader = $('.header-area'),
            scroll_to_top = $('.scroll_to_top');
        // sticky menu
        if (scroll > 100) {
            mainHeader.addClass("sticky-menu");
        } else {
            mainHeader.removeClass("sticky-menu");
        }

        // scrolltop
        if (scroll > 100) {
            scroll_to_top.addClass("active");
        } else {
            scroll_to_top.removeClass("active");
        }
    });

    /*================================
    Humberger
    ==================================*/
    $('.humberger-btn').on('click', function () {
        $(this).toggleClass('opened');
        $('.offset-menu').toggleClass('show_hide_menu');
    });

    $('.offset-inner ul li a').on('click', function () {
        $('.offset-menu').removeClass('show_hide_menu');
        $('.humberger-btn').removeClass('opened');
    });

    /*================================
    Smoth Scroll
    ==================================*/
    function smoothScrolling($links, $topGap) {
        var links = $links;
        var topGap = $topGap;

        links.on("click", function () {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $("[name=" + this.hash.slice(1) + "]");
                if (target.length) {
                    $("html, body").animate({
                        scrollTop: target.offset().top - topGap
                    }, 1000, "easeInOutExpo");
                    return false;
                }
            }
            return false;
        });
    }
    var mainHeaderHeight = $('.header-area').innerHeight();
    $(window).on("load", function () {
        smoothScrolling($("a.take-me[href^='#']"), mainHeaderHeight);
        smoothScrolling($(".offset-inner ul li a[href^='#']"), mainHeaderHeight);
        smoothScrolling($("a.scrl_me_down[href^='#']"), mainHeaderHeight);
        smoothScrolling($(".main-menu nav ul li a[href^='#']"), mainHeaderHeight);
        smoothScrolling($("a.scroll_to_top[href^='#']"), 0);
    });

    /*================================
    Active current Li
    ==================================*/
    $(window).on("scroll", function () {
        activeMenuItem($("#nav_mobile_menu"));
    });

    //function for active menuitem
    function activeMenuItem($links) {
        var top = $(window).scrollTop(),
            windowHeight = $(window).height(),
            documentHeight = $(document).height(),
            cur_pos = top + 2,
            sections = $("section"),
            nav = $links,
            nav_height = nav.outerHeight(),
            home = nav.find(" > ul > li:first");

        sections.each(function () {
            var top = $(this).offset().top - mainHeaderHeight,
                bottom = top + $(this).outerHeight();

            if (cur_pos >= top && cur_pos <= bottom) {
                nav.find("> ul > li > a").parent().removeClass("active");
                nav.find("a[href='#" + $(this).attr('id') + "']").parent().addClass("active");
            } else if (cur_pos === 2) {
                nav.find("> ul > li > a").parent().removeClass("active");
                home.addClass("active");
            } else if ($(window).scrollTop() + windowHeight > documentHeight - 400) {
                nav.find("> ul > li > a").parent().removeClass("active");
            }
        });
    }

    /*================================
    Swiper slider Activation
    ==================================*/
    // classes-carousel
    function classes_carousel() {
        var mySwiper = new Swiper('.classes-carousel', {
            speed: 400,
            loop: true,
            spaceBetween: 30,
            slidesPerView: 3,
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
            // Responsive breakpoints
            breakpoints: {
                640: {
                    slidesPerView: 1,
                    spaceBetween: 30
                },
                1024: {
                    slidesPerView: 2,
                    spaceBetween: 10
                }
            }
        });
    }
    classes_carousel();

    // testimonials-carousel
    function testimonials_carousel() {
        var mySwiper = new Swiper('.testimonials-carousel', {
            speed: 400,
            loop: true,
            grabCursor: true,
            slidesPerView: 1,
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            }
        });
    }
    testimonials_carousel();

    // photography slider
    function photography_slider() {
        var mySwiper = new Swiper('.photography-slider', {
            speed: 400,
            loop: true,
            parallax: true,
            grabCursor: true,
            slidesPerView: 1,
            // Navigation arrows
            navigation: {
                nextEl: '.ph-button-next',
                prevEl: '.ph-button-prev',
            },
            pagination: {
                el: '.ph-pagination',
                type: 'fraction',
            }

        });
    }
    photography_slider();

    // screen slider
    function screen_slides() {
        var mySwiper = new Swiper('.screen-slides', {
            speed: 400,
            loop: true,
            slidesPerView: 1,
            autoplay: {
                delay: 3000,
            }
        });
    }
    screen_slides();

    // screenshot slider
    function screenshot_carousel() {
        var mySwiper = new Swiper('.screenshot-carousel', {
            pagination: {
                el: '.screenshot-pagination',
                type: 'bullets',
                clickable: true,
            },
            autoplay: {
                delay: 3000,
            },
            speed: 1000,
            effect: 'coverflow',
            loop: true,
            centeredSlides: true,
            slidesPerView: 'auto',
            coverflowEffect: {
                rotate: 0,
                stretch: 80,
                depth: 200,
                modifier: 1,
                slideShadows: false,
            }
        });
    }
    screenshot_carousel();

    /*================================
    Parallax background
    ==================================*/
    function bgParallax() {
        if ($(".parallax").length) {
            $(".parallax").each(function () {
                var height = $(this).position().top;
                var resize = height - $(window).scrollTop();
                var parallaxSpeed = $(this).data("speed");
                var doParallax = -(resize / parallaxSpeed);
                var positionValue = doParallax + "px";
                var img = $(this).data("bg-image");

                $(this).css({
                    backgroundImage: "url(" + img + ")",
                    backgroundPosition: "50%" + positionValue,
                    backgroundSize: "cover",
                });

                if (window.innerWidth < 768) {
                    $(this).css({
                        backgroundPosition: "center center"
                    });
                }
            });
        }
    }
    bgParallax();
    $(window).on("scroll", function () {
        bgParallax();
    });

    /*================================
    Class Toggling
    ==================================*/

    $('.links-left a').on('mouseover', function () {
        $('.links-left a').removeClass('active');
        $(this).addClass('active');
    });

    $('.links-right a').on('mouseover', function () {
        $('.links-right a').removeClass('active');
        $(this).addClass('active');
    });

    /*================================
    Magnific Popup
    ==================================*/
    $('.expand-img').magnificPopup({
        type: 'image',
        gallery: {
            enabled: true
        }

    });

    $('.expand-video').magnificPopup({
        type: 'iframe',
        gallery: {
            enabled: true
        }

    });

    /*================================
    counter up
    ==================================*/
    $('.counter').counterUp({
        delay: 10,
        time: 1000
    });

    /*================================
    slicknav
    ==================================*/
    $('ul#navigation').slicknav({
        prependTo: "#mobile_menu"
    });

    /*================================
    Firefly Activation
    ==================================*/
    if ($('#slider_firefly').length) {
        window.onload = function () {
            firefly("slider_firefly", 100, "small", "#ffffff");
        };
    }

    /*================================
    Warm Canvas activation
    ==================================*/
    if ($('.warm-canvas').length) {
        $('.warm-canvas').glassyWorms({
            colors: ['#fff', '#c2c2c2'],
            useStyles: true,
            numParticles: 500,
            tailLength: 20,
            maxForce: 8,
            friction: 0.75,
            gravity: 9.81,
            interval: 3
            // colors: ['#000'],
            // element: $('<canvas class="worms"></canvas>')[0]
        });
    }

    /*================================
    Isotop Masonary
    ==================================*/
    // gallery masonary
    $('#container').imagesLoaded(function () {
        // init Isotope
        var $grid = $('.gallery-masonary').isotope({
            itemSelector: '.glry-item',
            percentPosition: true,
            masonry: {
                // use outer width of grid-sizer for columnWidth
                columnWidth: '.glry-item',
            }
        });
    });

    // photography work masonary
    $('#container').imagesLoaded(function () {
        // filter items on button click
        $('.prt-menu').on('click', 'button', function () {
            var filterValue = $(this).attr('data-filter');
            $grid.isotope({ filter: filterValue });
        });
        // init Isotope
        var $grid = $('.work-masonary').isotope({
            itemSelector: '.wrk-item',
            percentPosition: true,
            masonry: {
                // use outer width of grid-sizer for columnWidth
                columnWidth: '.wrk-item',
            }
        });
    });

    $('.prt-menu button').on('click', function (event) {
        $(this).siblings('.active').removeClass('active');
        $(this).addClass('active');
        event.preventDefault();
    });

    /*================================
    countdown
    ==================================*/
    $('[data-countdown]').each(function () {
        var $this = $(this),
            finalDate = $(this).data('countdown');
        $this.countdown(finalDate, function (event) {
            $this.html(event.strftime('<span class="cdown days"><span class="time-count">%-D</span> <p>Days</p></span> <span class="cdown hour"><span class="time-count">%-H</span> <p>Hour</p></span> <span class="cdown minutes"><span class="time-count">%M</span> <p>Min</p></span> <span class="cdown second"> <span><span class="time-count">%S</span> <p>Sec</p></span>'));
        });
    });

})(jQuery);

// google map activation
function initMap() {
    // Styles a map in night mode.
    var map = new google.maps.Map(document.getElementById('google_map'), {
        center: { lat: 40.674, lng: -73.945 },
        scrollwheel: false,
        zoom: 12,
        styles: [{
            "elementType": "geometry",
            "stylers": [{
                "color": "#f5f5f5"
            }]
        },
        {
            "elementType": "labels.icon",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "elementType": "labels.text.fill",
            "stylers": [{
                "color": "#616161"
            }]
        },
        {
            "elementType": "labels.text.stroke",
            "stylers": [{
                "color": "#f5f5f5"
            }]
        },
        {
            "featureType": "administrative.land_parcel",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "administrative.land_parcel",
            "elementType": "labels.text.fill",
            "stylers": [{
                "color": "#bdbdbd"
            }]
        },
        {
            "featureType": "administrative.neighborhood",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "poi",
            "elementType": "geometry",
            "stylers": [{
                "color": "#eeeeee"
            }]
        },
        {
            "featureType": "poi",
            "elementType": "labels.text",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "poi",
            "elementType": "labels.text.fill",
            "stylers": [{
                "color": "#757575"
            }]
        },
        {
            "featureType": "poi.business",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "poi.park",
            "elementType": "geometry",
            "stylers": [{
                "color": "#e5e5e5"
            }]
        },
        {
            "featureType": "poi.park",
            "elementType": "labels.text.fill",
            "stylers": [{
                "color": "#9e9e9e"
            }]
        },
        {
            "featureType": "road",
            "elementType": "geometry",
            "stylers": [{
                "color": "#ffffff"
            }]
        },
        {
            "featureType": "road",
            "elementType": "labels",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "road",
            "elementType": "labels.icon",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "road.arterial",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "road.arterial",
            "elementType": "labels.text.fill",
            "stylers": [{
                "color": "#757575"
            }]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry",
            "stylers": [{
                "color": "#dadada"
            }]
        },
        {
            "featureType": "road.highway",
            "elementType": "labels",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "road.highway",
            "elementType": "labels.text.fill",
            "stylers": [{
                "color": "#616161"
            }]
        },
        {
            "featureType": "road.local",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "road.local",
            "elementType": "labels.text.fill",
            "stylers": [{
                "color": "#9e9e9e"
            }]
        },
        {
            "featureType": "transit",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "transit.line",
            "elementType": "geometry",
            "stylers": [{
                "color": "#e5e5e5"
            }]
        },
        {
            "featureType": "transit.station",
            "elementType": "geometry",
            "stylers": [{
                "color": "#eeeeee"
            }]
        },
        {
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [{
                "color": "#c9c9c9"
            }]
        },
        {
            "featureType": "water",
            "elementType": "labels.text",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "water",
            "elementType": "labels.text.fill",
            "stylers": [{
                "color": "#9e9e9e"
            }]
        }
        ]
    });
    var marker = new google.maps.Marker({
        position: map.getCenter(),
        animation: google.maps.Animation.BOUNCE,
        icon: 'assets/images/icon/location.png',
        map: map
    });
}