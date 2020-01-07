import * as ScrollMagic from "scrollmagic";
import 'scrollmagic/scrollmagic/uncompressed/plugins/debug.addIndicators';
import { TweenMax, TimelineMax, Linear } from "gsap";
import { ScrollMagicPluginGsap } from "scrollmagic-plugin-gsap";

ScrollMagicPluginGsap(
  ScrollMagic,
  TweenMax,
  TimelineMax,
  Linear
);

jQuery(document).ready(function ($) {
	// init controller
	var controller = new ScrollMagic.Controller({globalSceneOptions: {triggerHook: "onEnter", duration: "200%"}});

	// build scenes
	new ScrollMagic.Scene({ triggerElement: ".parallax-image" })
    .setTween(".parallax-image > img", { y: "-25%", ease: Linear.easeNone })
    .addTo(controller);

});
