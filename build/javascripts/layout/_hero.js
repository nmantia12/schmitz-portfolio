// Document ready
// eslint-disable-next-line no-undef
jQuery(document).ready(function ($) {

	const removeActiveHeros = () => {
		$(".hero__tab").removeClass("active");
		$(".hero__body").removeClass("active");
		$('nav [role="menu"] li').removeClass("active");
	}

	$(".hero__tab[data-tab-index]").hover(function() {
		const index = $(this).attr("data-tab-index");
		const navIndex = parseInt(index) - 1;
		removeActiveHeros();
    $(this).addClass("active");
		$(".hero__body[data-hero-index='" + index + "'").addClass("active");
	});

	$('[data-opens-menu]').click(function() {
		$('body').toggleClass('menu-open');
		$('nav [role="menu"]').removeClass("fade");
		$('nav [role="menu"] li').removeClass("active");
	});

	$('[role="menu"] .menu-main-nav-container li a').hover(function() {
		var index = $(this).attr('data-topic');
		removeActiveHeros();
		if (index) {
			$('nav [role="menu"]').addClass('fade');
			$(this).parent().addClass("active");
			$(".hero__tab[data-tab-index='" + index + "'").addClass("active");
			$(".hero__body[data-hero-index='" + index + "'").addClass("active");
		}
	});

	$('a[data-topic]').click(function (e) {
		var dataId = $(this).attr('data-topic');
		$.get('/wp-json/prisma/ajax_navigation/', {
			ajaxid: dataId
		}).done(function (response) {
			console.log(response);
			if (response.success) {
				$('nav [role="menu"] li, .hero__body, .hero__tab').css("pointer-events", "none");

				$("#toggle").prop("checked", false);
				removeActiveHeros();
				$('nav [role="menu"]').removeClass("fade");
				$('body').removeClass('menu-open');

				if (!$(".hero__body[data-hero-index='" + dataId + "'").hasClass('active')) {
					setTimeout(() => {
						$(".hero__tab[data-tab-index='" + dataId + "'").addClass("active");
						$(".hero__body[data-hero-index='" + dataId + "'").addClass("active");
						$('body').addClass('topic-open');
						$('#topic').html(response.content);
						$('nav [role="menu"] li, .hero__body, .hero__tab').css("pointer-events", "auto");
					}, 500);
				}
			}
		});
		e.preventDefault();
		// removeActiveHeros();
		// $("#topic").load('/app/themes/rabo_micosite/topic-' + index + '.php');
	});
});
