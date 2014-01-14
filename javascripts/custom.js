jQuery(function($) {
  $(document).ready(function() {

    // Let's make the secondary navigation sticky shall we
    $('.secHeader').stickUp();

    // Secondary navigation
    $(window).scroll(function() {
      if ($(window).scrollTop() > 90) {
        $('#mainHeader').css('padding', '1em 3%');
        $('#mainHeader h1.section-title').css('font-size', '1.8em');
      } else {
        $('#mainHeader').css('padding', '4em 3%');
        $('#mainHeader h1.section-title').css('font-size', '2.4em');
      }
     });
  });
});