(function($) {
  $(document).ready(function($) {
    if ($(".parallax-image").length) {
      // init controller
      var controller = new ScrollMagic.Controller({
        globalSceneOptions: { triggerHook: "onEnter", duration: "200%" }
      });

      // build scenes
      new ScrollMagic.Scene({ triggerElement: ".parallax-image" })
        .setTween(".parallax-image__img", {
          y: "-25%",
          ease: Linear.easeNone,
          duration: "200%"
        })
        .addTo(controller);
    }
  });

})(jQuery);
