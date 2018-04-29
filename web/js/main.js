$(document).ready(function() {



  $("div.blog-post").hover(
    function() {
        $(this).find("div.content-hide").slideToggle("fast");
    },
    function() {
        $(this).find("div.content-hide").slideToggle("fast");
    }
  );

  $('.flexslider').flexslider({
		prevText: '',
		nextText: ''
	});

  $('.testimonails-slider').flexslider({
    animation: 'slide',
    slideshowSpeed: 5000,
    prevText: '',
    nextText: '',
    controlNav: false
  });

  $(function(){

  // Instantiate MixItUp:

  $('#Container').mixItUp();



  $(document).ready(function() {
      $(".fancybox").fancybox();

      $('.lang').hover(function(){
          $('div.lang').removeClass('round-border-bottom');
          $('.subLangs', this).slideDown();
          $('.subLangs').addClass('round-border-bottom');
        },function(){
          $('.subLangs', this).hide();
          $('.subLangs').removeClass('round-border-bottom');
          $(this).addClass('round-border-bottom');
      });

      $('.lang').click(function(){
          if ($('.subLangs').hasClass('round-border-bottom')) {
            $('.subLangs', this).hide();
            $('.subLangs').removeClass('round-border-bottom');
            $(this).addClass('round-border-bottom');
          } else {
            $(this).removeClass('round-border-bottom');
            $('.subLangs', this).slideDown();
            $('.subLangs').addClass('round-border-bottom');
            
          }
      });
    });
  });
});

