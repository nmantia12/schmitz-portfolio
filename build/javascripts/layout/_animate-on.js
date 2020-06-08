(function ($) {
	var initXvalue, initYvalue;

	// load animations
	$(document).ready(function () {
	});

	// scroll functions
	$(window).on('load resize scroll', function () {
		var animateEls = $(".slide-in, .scroll-animate, .grid article, .hero-block");
		if (animateEls.length) {
			animateEls.each(function () {
				const thisEl = $(this);
				const wt = window.pageYOffset;
				const wh = $(window).innerHeight();
				const wc = wt + wh / 2;
				const wb = wt + wh;
				const eh = $(this).outerHeight();
				const et = $(this).offset().top;
				const ec = et + eh / 2;
				const eb = et + eh;
				const range = (ec - wc) / (wh / 2 + eh / 2);

				if (et <= wb && eb > wt) {
					thisEl.addClass('active');
				}

				// scale on scroll. from 1 to 1.5
				if (et <= wb && ec > wc && thisEl.hasClass('scroll-animate__scale')) {
					const scale = 1 + (range / 2);
					thisEl.css({
						'transform': 'scale(' + scale + ')',
					});
				}
			});
		}
	});

})(jQuery);

