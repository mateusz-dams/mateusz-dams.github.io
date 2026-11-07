$( document ).ready(function($) {
  $('.hero__slider').slick({
      dots: true,
      arrows: false,
      autoplay: true,
      autoplaySpeed: 2000,
      pauseOnFocus: true,
      pauseOnHover: true,
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
});

