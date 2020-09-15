/**
 * UpSolution Element: Page Scroller
 */
;( function( $, undefined ) {
	"use strict";

	$us.PageScroller = function( container, options ) {
		this.init( container, options );
	};

	$us.PageScroller.prototype = {
		init: function( container, options ) {
			var defaults = {
				coolDown: 100,
				animationDuration: 1000,
				animationEasing: $us.getAnimationName( 'easeInOutExpo' )
			};

			this.options = $.extend( {}, defaults, options );

			this.$container = $( container );
			this._canvasTopOffset = $us.$canvas.offset().top;
			this.activeSection = 0;
			this.sections = [];

			this.initialSections = [];
			this.hiddenSections = [];
			this.currHidden = [];
			this.dots = [];
			this.scrolls = [];
			this.usingDots = false;
			this.isTouch = ( ( 'ontouchstart' in window ) || ( navigator.msMaxTouchPoints > 0 ) || ( navigator.maxTouchPoints ) );
			this.disableWidth = ( this.$container.data( 'disablewidth' ) !== undefined ) ? this.$container.data( 'disablewidth' ) : 768;
			this.hiddenClasses = {
				// Ultimate Addons classes and width limits
				'uvc_hidden-xs': [0, 479],
				'uvc_hidden-xsl': [480, 767],
				'uvc_hidden-sm': [768, 991],
				'uvc_hidden-md': [992, 1199],
				'uvc_hidden-ml': [1200, 1823],
				'uvc_hidden-lg': [1824, 99999], // 99999 max screen resolution

				// WPBakery classes
				'vc_hidden-xs': [0, 767],
				'vc_hidden-sm': [768, 991],
				'vc_hidden-md': [992, 1199],
				'vc_hidden-lg': [1200, 99999] // 99999 max screen resolution
			};

			if ( this.$container.data( 'speed' ) !== undefined ) {
				this.options.animationDuration = this.$container.data( 'speed' );
			}

			// Add header, only when it's not sticky and not transparent, because it occupies part of the screen in that case
			if ( $us.canvas.headerPos === 'static' && $us.header.orientation === 'hor' && $us.header.bg !== 'transparent' ) {
				$us.canvas.$header.each( function() {
					var $section = $us.canvas.$header,
						section = {
							$section: $section,
							area: 'header',
						};
					this._countPosition( section );
					this.sections.push( section );
					this.initialSections.push( section );
				}.bind( this ) );
			}

			// Adding canvas sections
			$us.$canvas.find( '.l-section' ).each( function( key, elm ) {

				// Exclude parent sections
				if ( $( '.l-section', elm ).length ) {
					return;
				}

				var $section = $( elm ),
					section = {
						$section: $section,
						hiddenBoundaries: [],
						area: 'content',
						isSticky: $section.hasClass( 'type_sticky' )
					},
					addedWidths = [];

				// Handle hidden sections
				hidden:
					for ( var i in this.hiddenClasses ) {
						if ( this.hiddenClasses.hasOwnProperty( i ) ) {
							var low = this.hiddenClasses[ i ][ 0 ],
								high = this.hiddenClasses[ i ][ 1 ];
							if ( $section.hasClass( i ) ) {
								var addedWidthLength = addedWidths.length,
									j;

								// Save added sections to exclude doubles
								addedWidths.push( [low, high] );

								// Exclude repeating widths
								for ( j = 0; j < addedWidthLength; j ++ ) {
									if ( addedWidths[ j ][ 0 ] === low && addedWidths[ j ][ 1 ] === high ) {
										break hidden;
									}
								}
								section.hiddenBoundaries.push( [low, high] );
								// Avoid doubles
								if ( this.hiddenSections.indexOf( key ) === - 1 ) {
									this.hiddenSections.push( key );
								}

							}
						}
					}

				this._countPosition( section );
				this.sections.push( section );
				this.initialSections.push( section );
			}.bind( this ) );

			// Adding footer sections
			$( '.l-footer > .l-section' ).each( function( key, elm ) {
				var $section = $( elm ),
					section = {
						$section: $section,
						area: 'footer',
						isSticky: $section.hasClass( 'type_sticky' )
					};
				this._countPosition( section );
				this.sections.push( section );
				this.initialSections.push( section );
			}.bind( this ) );

			// Adding dots for canvas sections
			this.$dotsContainer = this.$container.find( '.w-scroller-dots' );
			if ( this.$dotsContainer.length ) {
				this.usingDots = true;

				this.$firstDot = this.$dotsContainer.find( '.w-scroller-dot' ).first();
				this.redrawDots( true );
			}

			this._attachEvents();
			// Bondable events
			this._events = {
				scroll: this.scroll.bind( this ),
				resize: this.resize.bind( this )
			};

			$us.$canvas.on( 'contentChange', $us.debounce( this._events.resize, 5 ) );
			$us.$window.on( 'resize load', $us.debounce( this._events.resize, 5 ) );
			$us.$window.on( 'resize load scroll', $us.debounce( this._events.scroll, 5) );
			$us.timeout( this._events.resize, 100 );
		},
		isSectionHidden: function( section ) {
			if ( !this.initialSections[ section ].hiddenBoundaries || !this.initialSections[ section ].hiddenBoundaries.length ) {
				return false;
			}
			var currWidth = window.innerWidth,
				isHidden = false;

			for ( var i = 0; i < this.initialSections[ section ].hiddenBoundaries.length; i ++ ) {
				var low = this.initialSections[ section ].hiddenBoundaries[ i ][ 0 ],
					high = this.initialSections[ section ].hiddenBoundaries[ i ][ 1 ];

				if ( currWidth >= low && currWidth <= high ) {
					isHidden = true;
					break;
				}
			}

			return isHidden;
		},
		redrawDots: function( inited ) {
			if ( !this.$dotsContainer.length || !this.usingDots ) {
				return false;
			}
			// Clean up dots container
			this.$dotsContainer.html( '' );

			for ( var i = 0; i < this.sections.length; i ++ ) {
				// Don't add dots for footer sections
				if ( this.sections[ i ].area === 'footer' && !this.$container.data( 'footer-dots' ) ) {
					continue;
				}
				this.$firstDot.clone().appendTo( this.$dotsContainer );
			}

			this.$dots = this.$dotsContainer.find( '.w-scroller-dot' );
			this.$dots.each( function( key, elm ) {
				var $dot = $( elm );
				this.dots[ key ] = $dot;
				$dot
					.click( function() {
						this.scrollTo( key );
						this.$dots.removeClass( 'active' );
						$dot.addClass( 'active' );
					}.bind( this ) )
					// Control of the number of points
					.toggleClass( 'hidden', this.sections[ key ].isSticky && $us.$window.width() > $us.canvas.options.columnsStackingWidth );
			}.bind( this ) );
			if ( !! inited ) {
				this.dots[ this.activeSection ].addClass( 'active' );
			}
			this.$dotsContainer.addClass( 'show' );
		},
		recountSections: function() {
			if ( this.currHidden ) {
				// Set sections to initial state to extract hidden
				for ( var initialSection in this.initialSections ) {
					this.sections[ initialSection ] = this.initialSections[ initialSection ];
				}
			}

			// Loop backward to don't mess with the indexes
			for ( var i = this.hiddenSections.length - 1; i >= 0; i -- ) {
				var indexOfTheItem = this.currHidden.indexOf( this.hiddenSections[ i ] );

				if ( this.isSectionHidden( this.hiddenSections[ i ] ) ) {
					// Add to currently hidden if it wasn't added before
					if ( indexOfTheItem === - 1 ) {
						this.currHidden.push( this.hiddenSections[ i ] );
					}
					this.sections.splice( this.hiddenSections[ i ], 1 );
				} else {
					this.currHidden.splice( indexOfTheItem, 1 );
				}
			}

			this.redrawDots();
		},
		getScrollSpeed: function( number ) {
			var sum = 0,
				lastElements = this.scrolls.slice( Math.max( this.scrolls.length - number, 1 ) );

			for ( var i = 0; i < lastElements.length; i ++ ) {
				sum = sum + lastElements[ i ];
			}

			return Math.ceil( sum / number );
		},
		_attachEvents: function() {
			var that = this;

			function mouseWheelHandler( e ) {
				// Cancel processing if a modal window is open on the page
				if ( $us.$html.hasClass( 'usoverlay_fixed' ) ) {
					return;
				}
				e.preventDefault();
				var currentTime = new Date().getTime(),
					target = that.activeSection,
					direction = e.wheelDelta || - e.detail,
					speedEnd, speedMiddle, isAccelerating;

				if ( that.scrolls.length > 149 ) {
					that.scrolls.shift();
				}
				that.scrolls.push( Math.abs( direction ) );

				if ( ( currentTime - that.previousMouseWheelTime ) > that.options.coolDown ) {
					that.scrolls = [];
				}
				that.previousMouseWheelTime = currentTime;

				speedEnd = that.getScrollSpeed( 10 );
				speedMiddle = that.getScrollSpeed( 70 );
				isAccelerating = speedEnd >= speedMiddle;

				if ( isAccelerating ) {
					if ( direction < 0 ) {
						target ++;
					} else if ( direction > 0 ) {
						target --;
					}
					if ( that.sections[ target ] === undefined ) {
						return;
					}
					that.scrollTo( target );
					that.lastScroll = currentTime;
				}
			}

			$us.$document.off( 'mousewheel DOMMouseScroll MozMousePixelScroll' );
			document.removeEventListener( 'mousewheel', mouseWheelHandler );
			document.removeEventListener( 'DOMMouseScroll', mouseWheelHandler );
			document.removeEventListener( 'MozMousePixelScroll', mouseWheelHandler );
			$us.$canvas.off( 'touchstart touchmove' );

			if ( $us.$window.width() > this.disableWidth && $us.mobileNavOpened <= 0 && ( !$us.$html.hasClass( 'cloverlay_fixed' ) ) ) {
				document.addEventListener( 'mousewheel', mouseWheelHandler, { passive: false } );
				document.addEventListener( 'DOMMouseScroll', mouseWheelHandler, { passive: false } );
				document.addEventListener( 'MozMousePixelScroll', mouseWheelHandler, { passive: false } );

				if ( $.isMobile || this.isTouch ) {
					$us.$canvas.on( 'touchstart', function( event ) {
						var e = event.originalEvent;
						if ( typeof e.pointerType === 'undefined' || e.pointerType !== 'mouse' ) {
							this.touchStartY = e.touches[ 0 ].pageY;
						}
					}.bind( this ) );

					$us.$canvas.on( 'touchmove', function( event ) {
						event.preventDefault();

						var currentTime = new Date().getTime(),
							e = event.originalEvent,
							target = this.activeSection;

						this.touchEndY = e.touches[ 0 ].pageY;

						if ( Math.abs( this.touchStartY - this.touchEndY ) > ( $us.$window.height() / 50 ) ) {
							if ( this.touchStartY > this.touchEndY ) {
								target ++;
							} else if ( this.touchEndY > this.touchStartY ) {
								target --;
							}

							if ( this.sections[ target ] === undefined ) {
								return;
							}
							this.scrollTo( target );
							this.lastScroll = currentTime;
						}
					}.bind( this ) );
				}
			}

		},
		_countPosition: function( section ) {
			section.top = section.$section.offset().top - this._canvasTopOffset;
			if (
				( ( $us.header.headerTop === undefined && $us.header.settings.is_hidden === undefined ) || $us.header.headerTop > 0 )
				&& ! $us.header.autoHide
			) {
				section.top -= parseInt( $us.header.scrolledOccupiedHeight );
			}

			section.bottom = section.top + section.$section.outerHeight( false );
		},
		_countAllPositions: function() {
			for ( var section in this.sections ) {
				if ( this.sections[ section ].$section.length ) {
					this._countPosition( this.sections[ section ] );
				}
			}
		},
		scrollTo: function( target ) {
			var currentTime = new Date().getTime();
			if ( this.previousScrollTime !== undefined && ( currentTime - this.previousScrollTime < this.options.animationDuration ) ) {
				return;
			}
			this.previousScrollTime = currentTime;

			// The for dots points from sticky block
			if ( this.sections[ target ].isSticky && $us.$window.width() > $us.canvas.options.columnsStackingWidth ) {
				if ( target > this.activeSection ) {
					target += 1;
				} else {
					target -= 1;
				}
			}

			if ( this.usingDots ) {
				this.$dots.removeClass( 'active' );
				if ( this.dots[ target ] !== undefined ) {
					this.dots[ target ].addClass( 'active' );
				}
			}

			// For a header that has sticky and auto-hide enabled, add the height of the header when scrolling to the bottom,
			// this will allow not to recalculate the position of the page section when hide header
			var scrollTop = parseInt( this.sections[ target ][ 'top' ] );
			if (
				parseInt( this.sections[ target ][ 'top' ] ) > parseInt( $us.$window.scrollTop() )
				&& $us.header.isEnableSticky()
				&& $us.header.sticky_auto_hide
			) {
				scrollTop += $us.header.scrolledOccupiedHeight;
			}

			$us.$htmlBody.stop( true, false ).animate( {
				scrollTop: scrollTop + 'px'
			}, {
				duration: this.options.animationDuration,
				easing: this.options.animationEasing,
				always: function() {
					this.activeSection = target;
				}.bind( this )
			} );
		},
		resize: function() {
			this._attachEvents();
			this.recountSections();
			// Delaying the resize event to prevent glitches
			$us.timeout( this._countAllPositions.bind( this ), 150 );
			this._countAllPositions();
		},
		scroll: function( ) {
			var currentTime = new Date().getTime();
			if ( ( currentTime - this.lastScroll ) < ( this.options.coolDown + this.options.animationDuration ) ) {
				return;
			}
			$us.debounce( function() {
				var scrollTop = parseInt( $us.$window.scrollTop() );

				for ( var section in this.sections ) {
					if (
						scrollTop >= ( this.sections[ section ].top - 1 )
						&& scrollTop < ( this.sections[ section ].bottom - 1 )
					) {
						this.activeSection = section;
						// NOTE: Do not add break because everything should be checked!
					}
				}
				if ( this.usingDots ) {
					this.$dots.removeClass( 'active' );
					if ( this.dots[ this.activeSection ] !== undefined ) {
						this.dots[ this.activeSection ].addClass( 'active' );
					}
				}
			}.bind( this ), 500 )();
		}
	};

	$.fn.usPageScroller = function( options ) {
		return this.each( function() {
			$( this ).data( 'usPageScroller', new $us.PageScroller( this, options ) );
		} );
	};

	$( function() {
		// Delay to destination ult-vc-hide-row
		$us.timeout( function() {
			$( '.w-scroller' ).usPageScroller();
		}, 0 );
	} );
} )( jQuery );
