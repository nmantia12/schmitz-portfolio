/**
 * UpSolution Element: Grid
 */
;(function( $, undefined ) {
	"use strict";

	$us.WGrid = function( container, options ) {
		this.init( container, options );
	};

	$us.WGrid.prototype = {
		init: function( container, options ) {
			// Elements
			this.$container = $( container );
			// Built-in filters
			this.$filters = $( '.g-filters-item', this.$container );
			this.$items = $( '.w-grid-item', this.$container );
			this.$list = $( '.w-grid-list', this.$container );
			this.$loadmore = $( '.g-loadmore', this.$container );
			this.$pagination = $( '> .pagination', this.$container );
			this.$preloader = $( '.w-grid-preloader', this.$container );

			// Variables
			this.loading = false;
			this.changeUpdateState = false;
			this.gridFilter = null;

			this.curFilterTaxonomy = '';
			this.paginationType = this.$pagination.length
				? 'regular'
				: ( this.$loadmore.length ? 'ajax' : 'none' );
			this.filterTaxonomyName = this.$list.data( 'filter_taxonomy_name' )
				? this.$list.data( 'filter_taxonomy_name' )
				: 'category';

			// Prevent double init
			if ( this.$container.data( 'gridInit' ) == 1 ) {
				return;
			}
			this.$container.data( 'gridInit', 1 );

			var $jsonContainer = $( '.w-grid-json', this.$container );
			if ( $jsonContainer.is( '[onclick]' ) ) {
				this.ajaxData = $jsonContainer[ 0 ].onclick() || {};
				this.ajaxUrl = this.ajaxData.ajax_url || '';
				$jsonContainer.remove();
			}

			this.carouselSettings = this.ajaxData.carousel_settings;
			this.breakpoints = this.ajaxData.carousel_breakpoints || {};

			if ( $us.detectIE() == 11 ) {
				// Add object-fit support library for IE11
				$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/objectFitPolyfill.js', function() {
					objectFitPolyfill();

					// Bind objectFitPolyfill() event for IE11 on lazy load event
					$us.$document.on( 'lazyload', function() {
						objectFitPolyfill();
					} );
				} );
			}

			if ( this.$list.hasClass( 'owl-carousel' ) ) {
				$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/owl.carousel.js', function() {
					this.carouselOptions = {
						autoHeight: this.carouselSettings.autoHeight,
						autoplay: this.carouselSettings.autoplay,
						autoplayHoverPause: true,
						autoplayTimeout: this.carouselSettings.timeout,
						center: this.carouselSettings.center,
						dots: this.carouselSettings.dots,
						items: parseInt( this.carouselSettings.items ),
						lazyLoad: $us.lazyLoad || false,
						lazyLoadEager: 1,
						loop: this.carouselSettings.loop,
						mouseDrag: ! jQuery.isMobile,
						nav: this.carouselSettings.nav,
						navElement: 'div',
						navText: [ '', '' ],
						responsive: {},
						rewind: ! this.carouselSettings.loop,
						stagePadding: 0, // TODO: add padding offset option
						rtl: $( '.l-body' ).hasClass( 'rtl' ),
						slideBy: this.carouselSettings.slideby,
						slideTransition: this.carouselSettings.transition,
						smartSpeed: this.carouselSettings.speed
					};

					if ( this.carouselSettings.smooth_play == 1 ) {
						this.carouselOptions.slideTransition = 'linear';
						this.carouselOptions.autoplaySpeed = this.carouselSettings.timeout;
						this.carouselOptions.slideBy = 1;
					}

					if ( this.carouselSettings.carousel_fade ) {
						// https://owlcarousel2.github.io/OwlCarousel2/demos/animate.html
						$.extend( this.carouselOptions, {
							animateOut: 'fadeOut',
							animateIn: 'fadeIn',
						});
					}

					// Writing responsive params in a loop to prevent json conversion bugs
					$.each( this.breakpoints, function( breakpointWidth, breakpointArgs ) {
						if ( breakpointArgs !== undefined && breakpointArgs.items !== undefined ) {
							this.carouselOptions.responsive[breakpointWidth] = breakpointArgs;
							// Making sure items value is an integer
							this.carouselOptions.responsive[breakpointWidth]['items'] = parseInt( breakpointArgs.items );
						}
					}.bind( this ) );

					// Re-init containers with show more links after carousel init
					this.$list
						.on( 'initialized.owl.carousel', function( e ) {
							var $list = $( this );
							$( '[data-toggle-height]', e.currentTarget ).each( function( _, item ) {
								var usToggle = $( item ).data( 'usToggleMoreContent' );
								if ( usToggle instanceof $us.ToggleMoreContent ) {
									usToggle.initHeightCheck();
									$us.timeout( function() {
										$list.trigger( 'refresh.owl.carousel' );
									}, 1 );
								}
							} );
						} )
						// Disabling mouse Drag if there is a toggle link
						.on( 'mousedown.owl.core', function() {
							var $target = $( this );
							if ( $( '[data-toggle-height]', $target ).length && ! jQuery.isMobile ) {
								var owlCarousel = $target.data( 'owl.carousel' );
								owlCarousel.$stage.off( 'mousedown.owl.core' );
							}
						} );

					// https://owlcarousel2.github.io/OwlCarousel2/docs/started-welcome.html
					this.$list.owlCarousel( this.carouselOptions );

				}.bind( this ) );
			}

			if ( this.$container.hasClass( 'popup_page' ) ) {
				if ( this.ajaxData == undefined ) {
					return;
				}

				this.lightboxTimer = null;
				this.$lightboxOverlay = this.$container.find( '.l-popup-overlay' );
				this.$lightboxWrap = this.$container.find( '.l-popup-wrap' );
				this.$lightboxBox = this.$container.find( '.l-popup-box' );
				this.$lightboxContent = this.$container.find( '.l-popup-box-content' );
				this.$lightboxContentPreloader = this.$lightboxContent.find( '.g-preloader' );
				this.$lightboxContentFrame = this.$container.find( '.l-popup-box-content-frame' );
				this.$lightboxNextArrow = this.$container.find( '.l-popup-arrow.to_next' );
				this.$lightboxPrevArrow = this.$container.find( '.l-popup-arrow.to_prev' );
				this.$container.find( '.l-popup-closer' ).click( function() {
					this.hideLightbox();
				}.bind( this ) );

				this.$container.find( '.l-popup-box' ).click( function() {
					this.hideLightbox();
				}.bind( this ) );
				this.$container.find( '.l-popup-box-content' ).click( function( e ) {
					e.stopPropagation();
				}.bind( this ) );
				this.originalURL = window.location.href;
				this.lightboxOpened = false;

				if ( this.$list.hasClass( 'owl-carousel' ) ) {
					$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/owl.carousel.js', function() {
						this.initLightboxAnchors();
					}.bind( this ) );
				} else {
					this.initLightboxAnchors();
				}

				$( window ).on( 'resize', function() {
					if ( this.lightboxOpened && $us.$window.width() < $us.canvasOptions.disableEffectsWidth ) {
						this.hideLightbox();
					}
				}.bind( this ) );
			}

			if ( this.$list.hasClass( 'owl-carousel' ) ) {
				return;
			}

			if ( this.paginationType != 'none' || this.$filters.length ) {
				if ( this.ajaxData == undefined ) {
					return;
				}

				this.templateVars = this.ajaxData.template_vars || {};
				if ( this.filterTaxonomyName ) {
					this.initialFilterTaxonomy = this.$list.data( 'filter_default_taxonomies' )
						? this.$list.data( 'filter_default_taxonomies' ).split( ',' )
						: '';
					this.curFilterTaxonomy = this.initialFilterTaxonomy;
				}

				this.curPage = this.ajaxData.current_page || 1;
				this.perpage = this.ajaxData.perpage || this.$items.length;
				this.infiniteScroll = this.ajaxData.infinite_scroll || 0;
			}

			if ( this.$container.hasClass( 'with_isotope' ) ) {
				$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/isotope.js', function() {
					this.$list.imagesLoaded( function() {
						var smallestItemSelector,
							isotopeOptions = {
								itemSelector: '.w-grid-item',
								layoutMode: ( this.$container.hasClass( 'isotope_fit_rows' ) ) ? 'fitRows' : 'masonry',
								isOriginLeft: ! $( '.l-body' ).hasClass( 'rtl' )
							};

						if ( this.$container.hasClass( 'with_fadein' ) ) {
							isotopeOptions.hiddenStyle = {
								opacity: 0
							};
						} else if ( this.$container.hasClass( 'without_animation' ) ) {
							isotopeOptions.hiddenStyle = {};
						} else if ( this.$container.hasClass( 'with_afb' ) ) {
							isotopeOptions.hiddenStyle = {
								opacity: '0',
								transform: 'translateY(3rem)'
							};
						}

						if ( this.$list.find( '.size_1x1' ).length ) {
							smallestItemSelector = '.size_1x1';
						} else if ( this.$list.find( '.size_1x2' ).length ) {
							smallestItemSelector = '.size_1x2';
						} else if ( this.$list.find( '.size_2x1' ).length ) {
							smallestItemSelector = '.size_2x1';
						} else if ( this.$list.find( '.size_2x2' ).length ) {
							smallestItemSelector = '.size_2x2';
						}
						if ( smallestItemSelector ) {
							smallestItemSelector = smallestItemSelector || '.w-grid-item';
							isotopeOptions.masonry = { columnWidth: smallestItemSelector };
						}
						this.$list.isotope( isotopeOptions );

						this.$list.isotope();

						if ( this.paginationType == 'ajax' ) {
							this.initAjaxPagination();
						}
						$us.$canvas.on( 'lazyload contentChange', function() {
							this.$list.imagesLoaded( function() {
								this.$list.isotope( 'layout' );
							}.bind( this ) );
						}.bind( this ) );

					}.bind( this ) );
				}.bind( this ) );
			} else if ( this.paginationType == 'ajax' ) {
				this.initAjaxPagination();
			}

			this.$filters.each( function( index, filter ) {
				var $filter = $( filter ),
					taxonomy = $filter.data( 'taxonomy' );
				$filter.on( 'click', function() {
					if ( taxonomy != this.curFilterTaxonomy ) {
						if ( this.loading ) {
							return;
						}
						this.setState( 1, taxonomy );
						this.$filters.removeClass( 'active' );
						$filter.addClass( 'active' );
					}
				}.bind( this ) )
			}.bind( this ) );

			// This is necessary for interaction from the grid filter.
			if ( this.$container.closest('.l-main').length ) {
				$us.$body.on( 'us_grid.updateState', this._events.updateState.bind( this ) );
			}
		},
		_events: {
			/**
			 * Update Grid State
			 *
			 * @param EventObject e
			 * @param {string} params String of parameters from filters for the grid
			 * @param {number} page
			 * @param {object} gridFilter
			 * @return void
			 */
			updateState: function( e, params, page, gridFilter ) {
				if (
					! this.$container.is( '[data-grid-filter="true"]' )
					|| ! this.$container.hasClass( 'used_by_grid_filter' )
				) {
					return;
				}

				page = page || 1;
				this.changeUpdateState = true;
				this.gridFilter = gridFilter;

				if ( ! this.hasOwnProperty( 'templateVars' ) ) {
					this.templateVars = this.ajaxData.template_vars || {
						query_args: {}
					};
				}
				this.templateVars.us_grid_filter_params = params;
				if ( this.templateVars.query_args !== false ) {
					this.templateVars.query_args.paged = page;
				}

				// Query args for get item counts for Grid Filter
				this.templateVars.filters_query_args = gridFilter._queryArgs || {};
				this.setState( page );

				// Reset pagination
				if ( this.paginationType === 'regular' && /page(=|\/)/.test( location.href ) ) {
					var url = location.href.replace( /(page(=|\/))(\d+)(\/?)/, '$1' + page + '$2' );
					history.replaceState( document.title, document.title, url );
				}
			}
		},
		initLightboxAnchors: function() {
			this.$anchors = this.$list.find( '.w-grid-item-anchor' );
			this.$anchors.on( 'click', function( e ) {
				var $clicked = $( e.target ),
					$item = $clicked.closest( '.w-grid-item' ),
					$anchor = $item.find( '.w-grid-item-anchor' ),
					itemUrl = $anchor.attr( 'href' );
				if ( ! $item.hasClass( 'custom-link' ) ) {
					if ( $us.$window.width() >= $us.canvasOptions.disableEffectsWidth ) {
						e.stopPropagation();
						e.preventDefault();
						this.openLightboxItem( itemUrl, $item );
					}
				}
			}.bind( this ) );
		},
		// Pagination and Filters functions
		initAjaxPagination: function() {
			this.$loadmore.on( 'click', function() {
				if ( this.curPage < this.ajaxData.max_num_pages ) {
					this.setState( this.curPage + 1 );
				}
			}.bind( this ) );

			if ( this.infiniteScroll ) {
				$us.waypoints.add( this.$loadmore, '-70%', function() {
					if ( ! this.loading ) {
						this.$loadmore.click();
					}
				}.bind( this ) );
			}
		},
		setState: function( page, taxonomy ) {
			if ( this.loading && ! this.changeUpdateState ) {
				return;
			}

			if (
				page !== 1
				&& this.paginationType == 'ajax'
				&& this.none !== undefined
				&& this.none == true
			) {
				return;
			}

			this.none = false;
			this.loading = true;

			var $none = this.$container.find( '> .w-grid-none' );
			if ( $none.length ) {
				$none.hide();
			}

			// Create params for built-in filter
			if ( this.$filters.length && ! this.changeUpdateState ) {
				taxonomy = taxonomy || this.curFilterTaxonomy;
				if ( taxonomy == '*' ) {
					taxonomy = this.initialFilterTaxonomy;
				}

				if ( taxonomy != '' ) {
					var newTaxArgs = {
							'taxonomy': this.filterTaxonomyName,
							'field': 'slug',
							'terms': taxonomy
						},
						taxQueryFound = false;
					if ( this.templateVars.query_args.tax_query == undefined ) {
						this.templateVars.query_args.tax_query = [];
					} else {
						$.each( this.templateVars.query_args.tax_query, function( index, taxArgs ) {
							if ( taxArgs != null && taxArgs.taxonomy == this.filterTaxonomyName ) {
								this.templateVars.query_args.tax_query[ index ] = newTaxArgs;
								taxQueryFound = true;
								return false;
							}
						}.bind( this ) );
					}
					if ( ! taxQueryFound ) {
						this.templateVars.query_args.tax_query.push( newTaxArgs );
					}
				} else if ( this.templateVars.query_args.tax_query != undefined ) {
					$.each( this.templateVars.query_args.tax_query, function( index, taxArgs ) {
						if ( taxArgs != null && taxArgs.taxonomy == this.filterTaxonomyName ) {
							this.templateVars.query_args.tax_query[ index ] = null;
							return false;
						}
					}.bind( this ) );
				}
			}

			this.templateVars.query_args.paged = page;

			if ( this.paginationType == 'ajax' ) {
				if ( page == 1 ) {
					this.$loadmore.addClass( 'done' );
				} else {
					this.$loadmore.addClass( 'loading' );
				}
			}

			if ( this.paginationType != 'ajax' || page == 1 ) {
				this.$preloader.addClass( 'active' );
				if ( this.$list.data( 'isotope' ) ) {
					this.$list.isotope( 'remove', this.$container.find( '.w-grid-item' ) );
					this.$list.isotope( 'layout' );
				} else {
					this.$container.find( '.w-grid-item' ).remove();
				}
			}

			this.ajaxData.template_vars = JSON.stringify( this.templateVars );

			// Abort prev request
			if ( this.xhr !== undefined ) {
				this.xhr.abort();
			}

			this.xhr = $.ajax( {
				type: 'post',
				url: this.ajaxData.ajax_url,
				data: this.ajaxData,
				success: function( html ) {
					var $result = $( html ),
						$container = $( '.w-grid-list', $result ),
						$pagination = $( '.pagination > *', $result ),
						$items = $container.children(),
						isotope = this.$list.data( 'isotope' ),
						smallestItemSelector;

					$container.imagesLoaded( function() {
						this.beforeAppendItems( $items );
						//isotope.options.hiddenStyle.transform = '';
						$items.appendTo( this.$list );
						$container.html( '' );
						var $sliders = $items.find( '.w-slider' );
						this.afterAppendItems( $items );

						if ( isotope ) {
							isotope.insert( $items );
						}
						if ( $sliders.length ) {
							$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/royalslider.js', function() {
								$sliders.each( function( index, slider ) {
									$( slider ).wSlider().find( '.royalSlider' ).data( 'royalSlider' ).ev.on( 'rsAfterInit', function() {
										if ( isotope ) {
											this.$list.isotope( 'layout' );
										}
									} );
								}.bind( this ) );

							}.bind( this ) );
						}

						if ( isotope ) {
							if ( this.$list.find( '.size_1x1' ).length ) {
								smallestItemSelector = '.size_1x1';
							} else if ( this.$list.find( '.size_1x2' ).length ) {
								smallestItemSelector = '.size_1x2';
							} else if ( this.$list.find( '.size_2x1' ).length ) {
								smallestItemSelector = '.size_2x1';
							} else if ( this.$list.find( '.size_2x2' ).length ) {
								smallestItemSelector = '.size_2x2';
							}
							if ( isotope.options.masonry ) {
								isotope.options.masonry.columnWidth = smallestItemSelector || '.w-grid-item';
							}
							this.$list.isotope( 'layout' );
						}

						if ( this.paginationType == 'ajax' ) {
							//Check any tabs in loaded content
							if ( $items.find( '.w-tabs' ).length > 0 ) {
								//if post has tabs - init them
								$( '.w-tabs', $items ).each( function() {
									$( this ).wTabs();
								} );
							}

							if ( page == 1 ) {
								var $jsonContainer = $result.find( '.w-grid-json' );
								if ( $jsonContainer.length ) {
									var ajaxData = $jsonContainer[ 0 ].onclick() || {};
									this.ajaxData.max_num_pages = ajaxData.max_num_pages || this.ajaxData.max_num_pages;
								} else {
									this.ajaxData.max_num_pages = 1;
								}
							}

							if ( this.templateVars.query_args.paged >= this.ajaxData.max_num_pages || ! $items.length ) {
								this.$loadmore.addClass( 'done' );
							} else {
								this.$loadmore.removeClass( 'done' );
								this.$loadmore.removeClass( 'loading' );
							}

							if ( this.infiniteScroll ) {
								$us.waypoints.add( this.$loadmore, '-70%', function() {
									if ( ! this.loading ) {
										// check none
										this.$loadmore.click();
									}
								}.bind( this ) );
							}

							if ( $us.detectIE() == 11 ) {
								objectFitPolyfill();
							}

						} else if ( this.paginationType === 'regular' && this.changeUpdateState ) {
							// Pagination Link Correction
							$( 'a[href]', $pagination ).each( function( _, item ) {
								var $item = $( item ),
									pathname = location.pathname.replace( /((\/page.*)?)\/$/, '');
								$item.attr( 'href', pathname + $item.attr('href') );
							} );
							this.$pagination.html( $pagination );
						}

						if ( this.$container.hasClass( 'popup_page' ) ) {
							$.each( $items, function( index, item ) {
								var $loadedItem = $( item ),
									$anchor = $loadedItem.find( '.w-grid-item-anchor' ),
									itemUrl = $anchor.attr( 'href' );

								if ( ! $loadedItem.hasClass( 'custom-link' ) ) {
									$anchor.click( function( e ) {
										if ( $us.$window.width() >= $us.canvasOptions.disableEffectsWidth ) {
											e.stopPropagation();
											e.preventDefault();
											this.openLightboxItem( itemUrl, $loadedItem );
										}
									}.bind( this ) );
								}
							}.bind( this ) );
						}

						// The display a message in the absence of data
						if ( this.changeUpdateState && $result.find( '.w-grid-none' ).length ) {
							if ( ! $none.length ) {
								this.$container.prepend( $result.find( '.w-grid-none' ) );
							} else {
								$none.show();
							}
							this.none = true;
						}

						// Send the result to the filter grid
						if ( this.changeUpdateState && this.gridFilter ) {
							var $jsonData = $result.filter( '.w-grid-filter-json-data:first' );
							if ( $jsonData.length ) {
								this.gridFilter
									.trigger( 'us_grid_filter.update-items-amount', $jsonData[0].onclick() || {} );
							}
							$jsonData.remove();
						}

						// Resize canvas to avoid Parallax calculation issues
						$us.$canvas.resize();
						this.$preloader.removeClass( 'active' );
					}.bind( this ) );

					this.loading = false;

				}.bind( this ),
				error: function() {
					this.$loadmore.removeClass( 'loading' );
				}.bind( this )
			} );

			this.curPage = page;
			this.curFilterTaxonomy = taxonomy;
		},
		// Lightbox Functions
		_hasScrollbar: function() {
			return document.documentElement.scrollHeight > document.documentElement.clientHeight;
		},
		_getScrollbarSize: function() {
			if ( $us.scrollbarSize === undefined ) {
				var scrollDiv = document.createElement( 'div' );
				scrollDiv.style.cssText = 'width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;';
				document.body.appendChild( scrollDiv );
				$us.scrollbarSize = scrollDiv.offsetWidth - scrollDiv.clientWidth;
				document.body.removeChild( scrollDiv );
			}
			return $us.scrollbarSize;
		},
		openLightboxItem: function( itemUrl, $item ) {
			this.showLightbox();

			var $nextItem = $item.nextAll( 'article:visible:not(.custom-link)' ).first(),
				$prevItem = $item.prevAll( 'article:visible:not(.custom-link)' ).first();

			if ( $nextItem.length != 0 ) {
				this.$lightboxNextArrow.show();
				this.$lightboxNextArrow.attr( 'title', $nextItem.find( '.w-grid-item-title' ).text() );
				this.$lightboxNextArrow.off( 'click' ).click( function( e ) {
					var $nextItemAnchor = $nextItem.find( '.w-grid-item-anchor' ),
						nextItemUrl = $nextItemAnchor.attr( 'href' );
					e.stopPropagation();
					e.preventDefault();

					this.openLightboxItem( nextItemUrl, $nextItem );
				}.bind( this ) );
			} else {
				this.$lightboxNextArrow.attr( 'title', '' );
				this.$lightboxNextArrow.hide();
			}

			if ( $prevItem.length != 0 ) {
				this.$lightboxPrevArrow.show();
				this.$lightboxPrevArrow.attr( 'title', $prevItem.find( '.w-grid-item-title' ).text() );
				this.$lightboxPrevArrow.off( 'click' ).on( 'click', function( e ) {
					var $prevItemAnchor = $prevItem.find( '.w-grid-item-anchor' ),
						prevItemUrl = $prevItemAnchor.attr( 'href' );
					e.stopPropagation();
					e.preventDefault();

					this.openLightboxItem( prevItemUrl, $prevItem );
				}.bind( this ) );
			} else {
				this.$lightboxPrevArrow.attr( 'title', '' );
				this.$lightboxPrevArrow.hide();
			}

			if ( itemUrl.indexOf( '?' ) !== - 1 ) {
				this.$lightboxContentFrame.attr( 'src', itemUrl + '&us_iframe=1' );
			} else {
				this.$lightboxContentFrame.attr( 'src', itemUrl + '?us_iframe=1' );
			}

			// Replace window location with item's URL
			if (history.replaceState) {
				history.replaceState(null, null, itemUrl);
			}
			this.$lightboxContentFrame.off( 'load' ).on( 'load', function() {
				this.lightboxContentLoaded();
			}.bind( this ) );

		},
		lightboxContentLoaded: function() {
			this.$lightboxContentPreloader.css( 'display', 'none' );
			this.$lightboxContentFrame
				.contents()
				.find( 'body' )
				.off( 'keyup.usCloseLightbox' )
				.on( 'keyup.usCloseLightbox', function( e ) {
					if ( e.key === "Escape" ) {
						this.hideLightbox();
					}
				}.bind( this ) );
		},
		showLightbox: function() {
			clearTimeout( this.lightboxTimer );
			this.$lightboxOverlay.appendTo( $us.$body ).show();
			this.$lightboxWrap.appendTo( $us.$body ).show();
			this.lightboxOpened = true;

			this.$lightboxContentPreloader.css( 'display', 'block' );
			$us.$html.addClass( 'usoverlay_fixed' );

			if ( ! $.isMobile ) {
				// Storing the value for the whole popup visibility session
				this.windowHasScrollbar = this._hasScrollbar();
				if ( this.windowHasScrollbar && this._getScrollbarSize() ) {
					$us.$html.css( 'margin-right', this._getScrollbarSize() );
				}
			}
			this.lightboxTimer = setTimeout( function() {
				this.afterShowLightbox();
			}.bind( this ), 25 );
		},
		afterShowLightbox: function() {
			clearTimeout( this.lightboxTimer );

			this.$container.on( 'keyup', function( e ) {
				if ( this.$container.hasClass( 'popup_page' ) ) {
					if ( e.key === "Escape" ) {
						this.hideLightbox();
					}
				}
			}.bind( this ) );

			this.$lightboxOverlay.addClass( 'active' );
			this.$lightboxBox.addClass( 'active' );

			$us.$canvas.trigger( 'contentChange' );
			$us.$window.trigger( 'resize' );
		},
		hideLightbox: function() {
			clearTimeout( this.lightboxTimer );
			this.lightboxOpened = false;
			this.$lightboxOverlay.removeClass( 'active' );
			this.$lightboxBox.removeClass( 'active' );
			// Replace window location back to original URL
			if ( history.replaceState ) {
				history.replaceState( null, null, this.originalURL );
			}

			this.lightboxTimer = setTimeout( function() {
				this.afterHideLightbox();
			}.bind( this ), 500 );
		},
		afterHideLightbox: function() {
			this.$container.off( 'keyup' );
			clearTimeout( this.lightboxTimer );
			this.$lightboxOverlay.appendTo( this.$container ).hide();
			this.$lightboxWrap.appendTo( this.$container ).hide();
			this.$lightboxContentFrame.attr( 'src', 'about:blank' );
			$us.$html.removeClass( 'usoverlay_fixed' );
			if ( ! $.isMobile ) {
				if ( this.windowHasScrollbar ) {
					$us.$html.css( 'margin-right', '' );
				}
			}
		},
		/**
		 * Overloadable function for themes
		 * @param $items
		 */
		beforeAppendItems: function( $items ) {
		},

		afterAppendItems: function( $items ) {
		}

	};

	$.fn.wGrid = function( options ) {
		return this.each( function() {
			$( this ).data( 'wGrid', new $us.WGrid( this, options ) );
		} );
	};

	$( function() {
		$( '.w-grid' ).wGrid();
	} );

	$( '.w-grid-list' ).each( function() {
		var $list = $( this );
		if ( ! $list.find( '[ref=magnificPopupGrid]' ).length ) {
			return;
		}
		$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/magnific-popup.js', function() {
			var delegateStr = 'a[ref=magnificPopupGrid]:visible',
				popupOptions;
			if ( $list.hasClass( 'owl-carousel' ) ) {
				delegateStr = '.owl-item:not(.cloned) a[ref=magnificPopupGrid]';
			}
			popupOptions = {
				type: 'image',
				delegate: delegateStr,
				gallery: {
					enabled: true,
					navigateByImgClick: true,
					preload: [ 0, 1 ],
					tPrev: $us.langOptions.magnificPopup.tPrev, // Alt text on left arrow
					tNext: $us.langOptions.magnificPopup.tNext, // Alt text on right arrow
					tCounter: $us.langOptions.magnificPopup.tCounter // Markup for "1 of 7" counter
				},
				removalDelay: 300,
				mainClass: 'mfp-fade',
				fixedContentPos: true,
				callbacks: {
					beforeOpen: function() {
						var owlCarousel = $list.data('owl.carousel');
						if ( owlCarousel && owlCarousel.settings.autoplay ) {
							$list.trigger('stop.owl.autoplay');
						}
					},
					beforeClose: function() {
						var owlCarousel = $list.data('owl.carousel');
						if ( owlCarousel && owlCarousel.settings.autoplay ) {
							$list.trigger('play.owl.autoplay');
						}
					}
				}
			};
			$list.magnificPopup( popupOptions );
			if ( $list.hasClass( 'owl-carousel' ) ) {
				$list.on( 'initialized.owl.carousel', function( initEvent ) {
					var $currentList = $( initEvent.currentTarget ),
						items = {};
					$( '.owl-item:not(.cloned)', $currentList ).each( function( _, item ) {
						var $item = $( item ),
							id = $item.find( '[data-id]' ).data( 'id' );
						if ( !items.hasOwnProperty( id ) ) {
							items[ id ] = $item;
						}
					} );
					$currentList.on( 'click', '.owl-item.cloned', function( e ) {
						e.preventDefault();
						e.stopPropagation();
						var $target = $( e.currentTarget ),
							id = $target.find('[data-id]').data( 'id' );
						if ( items.hasOwnProperty( id ) ) {
							$( 'a[ref=magnificPopupGrid]', items[ id ] )
								.trigger( 'click' );
						}
					} );
				} );
			}
		} );

	} );
})( jQuery );
