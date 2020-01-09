import $ from "jquery";
import "slick-carousel";
// Document ready
// eslint-disable-next-line no-undef

const slideNum = document.getElementsByClassName("hero__tab").length;

  const slickEl = $(".hero__nav-bar");
  const settings = {
    slidesToScroll: 1,
    slidesToShow: 1,
    vertical: true,
    draggable: true,
    verticalSwiping: true,
    verticalScrolling: true,
    loop: false,
    swipe: true,
    touchMove: true,
    infinite: false,
    arrows: false,
    mobileFirst: true,
    responsive: [
      {
        breakpoint: 768,
        settings: {
          slidesToShow: parseInt(slideNum),
          vertical: false,
          verticalSwiping: false
        }
      }
    ]
  };

	slickEl.slick(settings);

	slickEl.on("beforeChange", function(
    event,
    slick,
    currentSlide,
    nextSlide
  ) {
    //currentSlide is undefined on init -- set it to 0 in this case (currentSlide is 0 based)
    var i = (currentSlide ? currentSlide : 0) + 1;
    const topicId = $(
      '.hero__nav-bar [data-slick-index="' + nextSlide + '"] a'
    ).attr("data-topic");
    $(".hero__tab, nav [role='menu'] li, .hero__body").removeClass("active");
    $(".hero__tab[data-tab-index='" + topicId + "'").addClass("active");
    $(".hero__body[data-hero-index='" + topicId + "'").addClass("active");
  });

	// resize event handlers
  $(window).on("orientationchange", function() {
    if ($(window).width() <= 768) {
      slickEl.slick("unslick");
      slickEl.slick(settings);
    }
  });

		var resizeId;
  $(window).on("resize", function() {
		clearTimeout(resizeId);
		resizeId = setTimeout(doneResizing, 500);
	});

	function doneResizing() {
    if ($(window).width() <= 768) {
      slickEl.slick("unslick");
      slickEl.slick(settings);
    }
  }
