import $ from "jquery";
import "slick-carousel";
// Document ready
// eslint-disable-next-line no-undef

	$(".hero__nav-bar").slick({
    slidesToScroll: 1,
    slidesToShow: 3,
    vertical: true,
    draggable: true,
    verticalSwiping: true,
    loop: false,
    infinite: false,
		arrows: false,
		mobileFirst: true,
    responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 4,
          vertical: false,
          verticalSwiping: false
        }
      }
    ]
  });
