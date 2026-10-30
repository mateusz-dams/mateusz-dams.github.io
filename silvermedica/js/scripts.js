$( document ).ready(function($) {
    $('.header__hamburger--open').click(function(){
        $('.menu-mobile').slideDown('slow');
        $(this).hide();
        $('.header__hamburger--close').show();
    });

    $('.header__hamburger--close').click(function(){
        $('.menu-mobile').slideUp('slow');
        $(this).hide();
        $('.header__hamburger--open').show();
    });

    $('.sales__slider').slick({
        dots: true,
        arrows: true,
        autoplay: true,
        autoplaySpeed: 2000,
        pauseOnFocus: false,
        pauseOnHover: false,
        prevArrow: $('.sales__arrow--prev'),
        nextArrow: $('.sales__arrow--next'),
        appendDots: $('.sales__dots')
    });

    if ($.fn.accordion) $('.accordion').accordion({
        "transitionSpeed": 400
    });

    // $('img.svg-convert').each(function () {
    //     var $img = jQuery(this);
    //     var imgID = $img.attr('id');
    //     var imgClass = $img.attr('class');
    //     var imgURL = $img.attr('src');
    //     jQuery.get(imgURL, function (data) {
    //       var $svg = jQuery(data).find('svg');
    //       if (typeof imgID !== 'undefined') {
    //         $svg = $svg.attr('id', imgID);
    //       }
    //       if (typeof imgClass !== 'undefined') {
    //         $svg = $svg.attr('class', imgClass + ' replaced-svg');
    //       }
    //       $svg = $svg.removeAttr('xmlns:a');
    //       $img.replaceWith($svg);
    //     }, 'xml');
    //   });
});