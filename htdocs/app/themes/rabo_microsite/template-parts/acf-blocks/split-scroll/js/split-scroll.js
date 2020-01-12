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

  var initializeBlock = function(block) {
		const $window = $(window);
		const sections = block.find(".split-scroll__inner .split-scroll__title");
		const inner = $(".split-scroll__inner");
		const sectionSceneDuration = $window.innerHeight() * 2;
		const sceneCount = sections.length;
		const blockSceneDuration = sectionSceneDuration * sceneCount;
		const blockSceneHeight =
      sectionSceneDuration * sceneCount + $window.innerHeight();
		const blockTop = block.offset().top;
		const totalY = blockTop + blockSceneDuration;
		var scrollPercent = 0;
    block.css("height", blockSceneHeight + "px");

		var st = $(this).scrollTop();
		if (st > blockTop && st <= totalY) {
      inner.addClass("fixed");
    }

		if (st < blockTop) {
			inner.removeClass("fixed");
			inner.css({
				"top": "0",
				"bottom": "auto"
			});
		}

		if (st > totalY) {
      inner.removeClass("fixed");
      inner.css({
        top: "auto",
        bottom: "0"
      });
		}

		for (let i = 0; i < sections.length; i++) {
			const switchPoint = sectionSceneDuration * i;
			const currY = $(this).scrollTop() - (blockTop + switchPoint);

			if (st > blockTop + switchPoint && st <= totalY) {
        const nthChild = i + 1;
        const activeSection = $(
          ".split-scroll__content-wrap:nth-child(" +
            nthChild +
            "), .split-scroll__img:nth-child(" +
            nthChild +
            ")"
        );
        $(".split-scroll__content-wrap, .split-scroll__img").removeClass(
          "active"
        );
        activeSection.addClass("active");
        scrollPercent = Math.ceil((currY / (sectionSceneDuration - 50)) * 100);
      }
    }
		lastScrollTop = st;

		$(".duration-bar").css("width", scrollPercent + "%");
	};

	var lastScrollTop = 0;
	$(window).on("scroll", function() {
		if ($(window).width() > 768) {
			$(".split-scroll").each(function() {
				initializeBlock($(this));
			});
		}
		if ($(window).width() <= 768) {
			$(".split-scroll__inner").removeClass("fixed");
			$(".split-scroll__content-wrap, .split-scroll__img").removeClass(
				"active"
			);
			$(".split-scroll").css('height', 'auto');
		}
  });

  // Initialize each block on page load (front end).
  $(document).ready(function() {
		$(
      ".split-scroll__content-wrap:first-child, .split-scroll__img:first-child"
		).addClass("active");
		if ($(window).width() > 768) {
			$(".split-scroll").each(function() {
				initializeBlock($(this));
			});
		}
		if ($(window).width() <= 768) {
			$(".split-scroll__inner").removeClass("fixed");
			$(".split-scroll__content-wrap, .split-scroll__img").removeClass(
				"active"
			);
			$(".split-scroll").css("height", "auto");
		}
  });

  // resize event handlers
  $(window).on("orientationchange", function() {
		if ($(window).width() > 768) {
			$(
				".split-scroll__content-wrap:first-child, .split-scroll__img:first-child"
			).addClass("active");
			$(".split-scroll").each(function() {
				initializeBlock($(this));
			});
		}
		if ($(window).width() <= 768) {
			$(".split-scroll__inner").removeClass("fixed");
			$(".split-scroll__content-wrap, .split-scroll__img").removeClass(
        "active"
			);
			$(".split-scroll").css("height", "auto");
		}
  });

  // var resizeId;
  $(window).on("resize", function() {
		if ($(window).width() > 768) {
			$(
        ".split-scroll__content-wrap:first-child, .split-scroll__img:first-child"
      ).addClass("active");
			$(".split-scroll").each(function() {
				initializeBlock($(this));
			});
		}
		if ($(window).width() <= 768) {
			$(".split-scroll__inner").removeClass("fixed");
			$(".split-scroll__content-wrap, .split-scroll__img").removeClass(
        "active"
			);
			$(".split-scroll").css("height", "auto");
		}
  });

  // Initialize dynamic block preview (editor).
  // if (window.acf) {
  //   window.acf.addAction(
  //     "render_block_preview/type=split-scroll",
  //     initializeBlock
  //   );
  // }
})(jQuery);
