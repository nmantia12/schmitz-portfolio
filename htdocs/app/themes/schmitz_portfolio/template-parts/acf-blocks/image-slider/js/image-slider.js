(function($) {
  /**
   * initializeBlock
   *
   * Adds custom JavaScript to the block HTML.
   *
   * @date    15/4/19
   * @since   1.0.0
   *
   * @param   object $block The block jQuery element.
   * @param   object attributes The block attributes (only available when editing).
   * @return  void
   */
  const sliderSettings = {
    slidesToScroll: 1,
    slidesToShow: 1,
    swipe: true,
    touchMove: true,
    infinite: true,
    arrows: true,
    vertical: false,
    prevArrow:
      "<button type='button' class='slick-prev'><img src='/app/themes/schmitz_portfolio/assets/img/left_arrow.svg'/></button>",
    nextArrow:
      "<button type='button' class='slick-next'><img src='/app/themes/schmitz_portfolio/assets/img/right_arrow.svg'/></button>"
  };

  var initializeBlock = function($block) {
    $block.find(".image-slider").slick(sliderSettings);
	};

	var unInitializeBlock = function($block) {
		$block.find(".image-slider").slick("unslick");
	};

  // Initialize each block on page load (front end).
  $(document).ready(function() {
    $(".image-slider-wrap").each(function() {
      initializeBlock($(this));
    });
	});

  // resize event handlers
  $(window).on("orientationchange", function() {
    $(".image-slider-wrap").each(function() {
			unInitializeBlock($(this));
      initializeBlock($(this));
    });
  });

  var resizeId;
  $(window).on("resize", function() {
    clearTimeout(resizeId);
    resizeId = setTimeout(doneResizing, 500);
  });

  function doneResizing() {
    $(".image-slider-wrap").each(function() {
			unInitializeBlock($(this));
      initializeBlock($(this));
    });
	}

  // Initialize dynamic block preview (editor).
  if (window.acf) {
    window.acf.addAction(
      "render_block_preview/type=image-slider",
      initializeBlock
    );
  }
})(jQuery);
