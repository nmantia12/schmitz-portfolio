(function($) {
  $(window).on( 'scroll', function() {
    if ($(".parallax-image, .parallax-effect").length && $(window).width() > 768) {
      $(".parallax-image, .parallax-effect").each(function() {
        const imageEl = $(this).children('img,svg');
        const wy = window.pageYOffset;
        const wh = $(window).innerHeight();
        const wc = wy + wh / 2;
        const wb = wy + wh;
        const eh = $(this).outerHeight();
        const ey = $(this).offset().top;
        const ec = ey + eh / 2;
        const eb = ey + eh;
        const da = imageEl.attr("data-parallax");
        const coefficient = da ? da : 300;
        if (ey <= wb && eb > wy) {
          const range = (ec - wc) / (wh / 2 + eh / 2);
          const pxValue = range * coefficient;
          imageEl.css({ "margin-top": pxValue + "px" });
        }
      });
    }
  });
})(jQuery);
