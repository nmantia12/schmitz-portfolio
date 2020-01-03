import $ from "jquery";
import "slick-carousel";
// Document ready
// eslint-disable-next-line no-undef

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
          slidesToShow: 4,
          vertical: false,
          verticalSwiping: false
        }
      }
    ]
  };

	slickEl.slick(settings);

	slickEl.on("init reInit beforeChange", function(
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

  const reinit = () => {
    slickEl.slick("unslick");
    slickEl.slick(settings);
  };

  // resize event handlers
  $(window).on("orientationchange", function() {
    reinit();
  });

  $(window).on("resize", function() {
    reinit();
  });
