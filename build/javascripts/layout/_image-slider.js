import $ from "jquery";
import "slick-carousel";
// Document ready
// eslint-disable-next-line no-undef
$(document).ready(function() {
  const imageSLider = $(".image-slider");
  const sliderSettings = {
    slidesToScroll: 1,
    slidesToShow: 1,
    swipe: true,
    touchMove: true,
    infinite: true,
    arrows: true,
    vertical: false,
    prevArrow:
      "<button type='button' class='slick-prev'><img src='/app/themes/rabo_microsite/assets/img/left_arrow.svg'/></button>",
    nextArrow:
      "<button type='button' class='slick-next'><img src='/app/themes/rabo_microsite/assets/img/right_arrow.svg'/></button>"
  };

  imageSLider.slick(sliderSettings);

  // resize event handlers
  $(window).on("orientationchange", function() {
    imageSLider.slick("unslick");
    imageSLider.slick(sliderSettings);
  });

  var resizeId;
  $(window).on("resize", function() {
    clearTimeout(resizeId);
    resizeId = setTimeout(doneResizing, 500);
  });

  function doneResizing() {
    imageSLider.slick("unslick");
    imageSLider.slick(sliderSettings);
  }
});
