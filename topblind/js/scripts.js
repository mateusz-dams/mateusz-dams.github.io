$( document ).ready(function($) {
  $(window).scroll(function() {
    var sticky = $('body.front'),
        scroll = $(window).scrollTop();
  
    if (scroll > 0) sticky.removeClass('home');
    else sticky.addClass('home');
  });

  $('.hero__slider').slick({
      dots: true,
      arrows: false,
      autoplay: true,
      autoplaySpeed: 2000,
      pauseOnFocus: false,
      pauseOnHover: false,
  });

  $('.header__hamburger--open').click(function(){
      $('.header').addClass('active');
      $('.menu-mobile').slideDown('slow');
      $(this).hide();
      $('.header__hamburger--close').show();
  });

  $('.header__hamburger--close').click(function(){
      $('.menu-mobile').slideUp('slow', function(){
          $('.header').removeClass('active');
      });
      $(this).hide();
      $('.header__hamburger--open').show();
  });

  $('.footer__columns--nine').slick({
    dots: false,
    infinite: true,
    speed: 500,
    arrows: false,
    slidesToShow: 9,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 2000,
    pauseOnFocus: false,
    pauseOnHover: false,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 8,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 7,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 6,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 576,
        settings: {
          slidesToShow: 5,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 4,
          slidesToScroll: 1
        }
      }
    ]
  });
  
  if ($.fn.accordion) $('.accordion').accordion({
    "transitionSpeed": 400
  });
});

