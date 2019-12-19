// Document ready
// eslint-disable-next-line no-undef
jQuery(document).ready(function ($) {
	$(".hero__tab[data-tab-index]").hover(function() {
		const index = $(this).attr("data-tab-index");
		const navIndex = parseInt(index) - 1;
    $(".hero__tab").removeClass("active");
		$(".hero__body").removeClass("active");
		$('nav [role="menu"] li').removeClass("active");
		// $('nav [role="menu"] li').each(function() {
		// 	if (navIndex === $(this).index()) {
    //     $(this).addClass("active");
    //   }
		// });
    $(this).addClass("active");
		$(".hero__body[data-hero-index='" + index + "'").addClass("active");
	});

	$('[data-opens-menu]').click(function() {
		$('body').toggleClass('menu-open');
		$('nav [role="menu"]').removeClass("fade");
		$('nav [role="menu"] li').removeClass("active");
	});

	$('nav [role="menu"] li').hover(function() {
		var index = $(this).index();
		index = index + 1;
		$('nav [role="menu"]').addClass('fade');
    $(".hero__tab").removeClass("active");
		$(".hero__body").removeClass("active");
		$('nav [role="menu"] li').removeClass("active");
		$(this).addClass("active");
		$(".hero__tab[data-tab-index='" + index + "'").addClass("active");
    $(".hero__body[data-hero-index='" + index + "'").addClass("active");
  });
});
