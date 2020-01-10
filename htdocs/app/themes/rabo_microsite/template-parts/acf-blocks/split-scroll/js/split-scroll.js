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
		var controller = new ScrollMagic.Controller({
      globalSceneOptions: { triggerHook: "onLeave" }
		});

		const sections = block.find(".split-scroll__section");
		const sectionSceneDuration = $(window).innerHeight();
		const sceneCount = sections.length + 1;
		const blockSceneDuration = sectionSceneDuration * sceneCount;
		block.css("height", blockSceneDuration + 'px');
		// var blockScene = new ScrollMagic.Scene({
    //   triggerElement: block[0],
    //   duration: blockSceneDuration
    // })
    //   .setPin(block[0])
    //   .setClassToggle(block[0], "block-active")
    //   .addIndicators({ name: "Block Scene" }) // add indicators (requires plugin)
    //   .addTo(controller);

		sections.each(function(index) {
			const thisEl = $(this)[0];
			var sectionScene = new ScrollMagic.Scene({
        triggerElement: thisEl,
        duration: sectionSceneDuration
      })
        .setPin(thisEl)
        .setClassToggle(thisEl, "section-active")
        .addIndicators({ name: "Section Scene" }) // add indicators (requires plugin)
        .addTo(controller);
		});
  };

  // Initialize each block on page load (front end).
  $(document).ready(function() {
    $(".split-scroll").each(function() {
      initializeBlock($(this));
    });
  });

  // resize event handlers
  // $(window).on("orientationchange", function() {
  //   $(".split-scroll").each(function() {
  //     initializeBlock($(this));
  //   });
  // });

  // var resizeId;
  // $(window).on("resize", function() {
  //   clearTimeout(resizeId);
  //   resizeId = setTimeout(doneResizing, 500);
  // });

  // function doneResizing() {
  //   $(".split-scroll").each(function() {
  //     initializeBlock($(this));
  //   });
  // }

  // Initialize dynamic block preview (editor).
  // if (window.acf) {
  //   window.acf.addAction(
  //     "render_block_preview/type=split-scroll",
  //     initializeBlock
  //   );
  // }
})(jQuery);
