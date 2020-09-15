/**
 * $us.scroll
 *
 * ScrollSpy, Smooth scroll links and hash-based scrolling all-in-one
 *
 * @requires $us.canvas
 */
! function( $ ) {
	"use strict";

	function USScroll( options ) {

		// Setting options
		var defaults = {
			/**
			 * @param {String|jQuery} Selector or object of hash scroll anchors that should be attached on init
			 */
			attachOnInit: '\
				.menu-item a[href*="#"],\
				.menu-item[href*="#"],\
				a.w-btn[href*="#"]:not([onclick]),\
				.w-text a[href*="#"],\
				.vc_icon_element a[href*="#"],\
				.vc_custom_heading a[href*="#"],\
				a.w-grid-item-anchor[href*="#"],\
				.w-toplink,\
				.w-image a[href*="#"]:not([onclick]),\
				.w-iconbox a[href*="#"],\
				.w-comments-title a[href*="#"],\
				a.smooth-scroll[href*="#"]',
			/**
			 * @param {String} Classname that will be toggled on relevant buttons
			 */
			buttonActiveClass: 'active',
			/**
			 * @param {String} Classname that will be toggled on relevant menu items
			 */
			menuItemActiveClass: 'current-menu-item',
			/**
			 * @param {String} Classname that will be toggled on relevant menu ancestors
			 */
			menuItemAncestorActiveClass: 'current-menu-ancestor',
			/**
			 * @param {Number} Duration of scroll animation
			 */
			animationDuration: $us.canvasOptions.scrollDuration,
			/**
			 * @param {String} Easing for scroll animation
			 */
			animationEasing: $us.getAnimationName( 'easeInOutExpo' ),

			/**
			 * @param {String} End easing for scroll animation
			 */
			endAnimationEasing: $us.getAnimationName( 'easeOutExpo' )
		};
		this.options = $.extend( {}, defaults, options || {} );

		// Hash blocks with targets and activity indicators
		this.blocks = {};

		// Is scrolling to some specific block at the moment?
		this.isScrolling = false;

		// Boundable events
		this._events = {
			cancel: this.cancel.bind( this ), scroll: this.scroll.bind( this ), resize: this.resize.bind( this )
		};

		this._canvasTopOffset = 0;
		$us.$window.on( 'resize load', $us.debounce( this._events.resize, 10 ) );
		$us.timeout( this._events.resize, 75 );

		$us.$window.on( 'scroll', this._events.scroll );
		$us.timeout( this._events.scroll, 75 );

		if ( this.options.attachOnInit ) {
			this.attach( this.options.attachOnInit );
		}

		// Recount scroll positions on any content changes
		$us.$canvas.on( 'contentChange', this._countAllPositions.bind( this ) );

		// Recount scroll positions with lazyload content
		$us.$document.on( 'lazyload', this._countAllPositions.bind( this ) );

		// Handling initial document hash
		if ( document.location.hash && document.location.hash.indexOf( '#!' ) == - 1 ) {
			var hash = document.location.hash, scrollPlace = ( this.blocks[ hash ] !== undefined ) ? hash : undefined;
			if ( scrollPlace === undefined ) {
				try {
					var $target = $( hash );
					if ( $target.length != 0 ) {
						scrollPlace = $target;
					}
				}
				catch ( error ) {
					//Do not have to do anything here since scrollPlace is already undefined
				}

			}
			if ( scrollPlace !== undefined ) {
				// While page loads, its content changes, and we'll keep the proper scroll on each sufficient content
				// change until the page finishes loading or user scrolls the page manually
				var keepScrollPositionTimer = setInterval( function() {
					this.scrollTo( scrollPlace );
				}.bind( this ), 100 );
				var clearHashEvents = function() {
					// Content size still may change via other script right after page load
					$us.timeout( function() {
						clearInterval( keepScrollPositionTimer );
						$us.canvas.resize();
						this._countAllPositions();
						// The size of the content can be changed using another script, so we recount the waypoints
						if ( $us.hasOwnProperty( 'waypoints' ) ) {
							$us.waypoints._countAll();
						}
						this.scrollTo( scrollPlace );
					}.bind( this ), 100 );
					$us.$window.off( 'load touchstart mousewheel DOMMouseScroll touchstart', clearHashEvents );
				}.bind( this );
				$us.$window.on( 'load touchstart mousewheel DOMMouseScroll touchstart', clearHashEvents );
			}
		}
	}

	USScroll.prototype = {

		/**
		 * Count hash's target position and store it properly
		 *
		 * @param {String} hash
		 * @private
		 */
		_countPosition: function( hash ) {
			var targetTop = this.blocks[ hash ].target.offset().top,
				isFrame = $us.$body.hasClass( 'us_iframe' ),
				hiddenHeader = ( $us.header.settings.is_hidden !== undefined ),
				bottomHeader = ( $us.header.headerTop !== undefined );

			this.blocks[ hash ].top = Math.ceil( targetTop - this._canvasTopOffset );

			if ( isFrame || hiddenHeader ) {
				this._countBottomPosition( hash );
				return;
			}

			if ( bottomHeader || ( $us.header.headerTop > 0 && targetTop > $us.header.headerTop ) ) {
				this.blocks[ hash ].top -= parseInt( $us.header.scrolledOccupiedHeight );
			}

			// Scrolling scroll correction based on sticky elements
			var $sticky = this.blocks[ hash ].target.siblings('.type_sticky:first');
			if ( $sticky.length ) {
				var stickyHeight = $sticky.outerHeight( false );
				if ( $us.canvas.$firstSection.hasClass( 'type_sticky' ) ) {
					this.blocks[ hash ].top += parseInt( $us.header.scrolledOccupiedHeight );
				}
				this.blocks[ hash ].top -= parseInt( stickyHeight );
			}

			this._countBottomPosition( hash );
		},
		_countBottomPosition: function( hash ) {
			this.blocks[ hash ].bottom = this.blocks[ hash ].top + this.blocks[ hash ].target.outerHeight( false );
		},

		/**
		 * Count all targets' positions for proper scrolling
		 *
		 * @private
		 */
		_countAllPositions: function() {

			// Take into account #wpadminbar (and others possible) offset
			this._canvasTopOffset = $us.$canvas.offset().top;

			// Counting blocks
			for ( var hash in this.blocks ) {
				if ( ! this.blocks.hasOwnProperty( hash ) ) {
					continue;
				}
				this._countPosition( hash );
			}
		},

		/**
		 * Indicate scroll position by hash
		 *
		 * @param {String} activeHash
		 * @private
		 */
		_indicatePosition: function( activeHash ) {
			for ( var hash in this.blocks ) {
				if ( ! this.blocks.hasOwnProperty( hash ) ) {
					continue;
				}
				if ( this.blocks[ hash ].buttons !== undefined ) {
					this.blocks[ hash ].buttons.toggleClass( this.options.buttonActiveClass, hash === activeHash );
				}
				if ( this.blocks[ hash ].menuItems !== undefined ) {
					this.blocks[ hash ].menuItems.toggleClass( this.options.menuItemActiveClass, hash === activeHash );
				}
				if ( this.blocks[ hash ].menuAncestors !== undefined ) {
					this.blocks[ hash ].menuAncestors.removeClass( this.options.menuItemAncestorActiveClass );
				}
			}
			if ( this.blocks[ activeHash ] !== undefined && this.blocks[ activeHash ].menuAncestors !== undefined ) {
				this.blocks[ activeHash ].menuAncestors.addClass( this.options.menuItemAncestorActiveClass );
			}
		},

		/**
		 * Attach anchors so their targets will be listened for possible scrolls
		 *
		 * @param {String|jQuery} anchors Selector or list of anchors to attach
		 */
		attach: function( anchors ) {
			// Location pattern to check absolute URLs for current location
			var locationPattern = new RegExp( '^' + location.pathname.replace( /[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&" ) + '#' );

			var $anchors = $( anchors );
			if ( $anchors.length == 0 ) {
				return;
			}
			$anchors.each( function( index, anchor ) {
				var $anchor = $( anchor ), href = $anchor.attr( 'href' ), hash = $anchor.prop( 'hash' );
				// Ignoring ajax links
				if ( hash.indexOf( '#!' ) != - 1 ) {
					return;
				}
				// Checking if the hash is connected with the current page
				if ( ! ( // Link type: #something
					href.charAt( 0 ) == '#' || // Link type: /#something
					( href.charAt( 0 ) == '/' && locationPattern.test( href ) ) || // Link type:
					// http://example.com/some/path/#something
					href.indexOf( location.host + location.pathname + '#' ) > - 1 ) ) {
					return;
				}
				// Do we have an actual target, for which we'll need to count geometry?
				if ( hash != '' && hash != '#' ) {
					// Attach target
					if ( this.blocks[ hash ] === undefined ) {
						var $target = $( hash ), $type = '';

						// Don't attach anchors that actually have no target
						if ( $target.length == 0 ) {
							return;
						}
						// If it's the only row in a section, than use section instead
						if ( $target.hasClass( 'g-cols' ) && $target.parent().children().length == 1 ) {
							$target = $target.closest( '.l-section' );
						}
						// If it's tabs or tour item, then use tabs container
						if ( $target.hasClass( 'w-tabs-section' ) ) {
							var $newTarget = $target.closest( '.w-tabs' );
							if ( ! $newTarget.hasClass( 'accordion' ) ) {
								$target = $newTarget;
							}
							$type = 'tab';
						} else if ( $target.hasClass( 'w-tabs' ) ) {
							$type = 'tabs';
						}
						this.blocks[ hash ] = {
							target: $target, type: $type
						};
						this._countPosition( hash );
					}
					// Attach activity indicator
					if ( $anchor.parent().length > 0 && $anchor.parent().hasClass( 'menu-item' ) ) {
						var $menuIndicator = $anchor.closest( '.menu-item' );
						this.blocks[ hash ].menuItems = ( this.blocks[ hash ].menuItems || $() ).add( $menuIndicator );
						var $menuAncestors = $menuIndicator.parents( '.menu-item-has-children' );
						if ( $menuAncestors.length > 0 ) {
							this.blocks[ hash ].menuAncestors = ( this.blocks[ hash ].menuAncestors || $() ).add( $menuAncestors );
						}
					} else {
						this.blocks[ hash ].buttons = ( this.blocks[ hash ].buttons || $() ).add( $anchor );
					}
				}
				$anchor.on( 'click', function( event ) {
					event.preventDefault();
					this.scrollTo( hash, true );
					// If it's tabs
					if ( typeof this.blocks[ hash ] !== 'undefined' ) {
						var block = this.blocks[ hash ];
						if ( $.inArray( block.type, [ 'tab', 'tabs' ] ) !== - 1 ) {
							var $linkedSection = block.target.find( '.w-tabs-section[id="' + hash.substr( 1 ) + '"]' );
							if ( block.type === 'tabs' ) {
								$linkedSection = block.target.find( '.w-tabs-section:first' );
							} else if ( block.target.hasClass( 'w-tabs-section' ) ) {
								$linkedSection = block.target;
							}
							if ( $linkedSection.length && ( ! $linkedSection.hasClass( 'active' ) ) ) {
								var $header = $linkedSection.find( '.w-tabs-section-header' );
								$header.click();
							}
						} else if (
							block.menuItems !== undefined
							&& $.inArray( $us.$body.usMod('state'), [ 'mobiles', 'tablets' ] ) !== -1
							&& $us.$body.hasClass( 'header-show' )
						) {
							$us.$body.removeClass( 'header-show' );
						}
					}
				}.bind( this ) );
			}.bind( this ) );
		},

		/**
		 * Gets the place position.
		 *
		 * @param mixed place
		 * @return object
		 */
		getPlacePosition: function( place ) {
			var data = { newY: 0, type: '' };
			// Scroll to top
			if ( place === '' || place === '#' ) {
				data.newY = 0;
				data.placeType = 'top';
			}
			// Scroll by hash
			else if ( this.blocks[ place ] !== undefined ) {
				data.newY = this.blocks[ place ].top;
				data.placeType = 'hash';
			} else if ( place instanceof $ ) {
				if ( place.hasClass( 'w-tabs-section' ) ) {
					var newPlace = place.closest( '.w-tabs' );
					if ( ! newPlace.hasClass( 'accordion' ) ) {
						place = newPlace;
					}
				}
				data.newY = Math.floor( place.offset().top - this._canvasTopOffset );
				if ( $us.header.headerTop === undefined || ( $us.header.headerTop > 0 && place.offset().top > $us.header.headerTop ) ) {
					data.newY = data.newY - $us.header.scrolledOccupiedHeight;
				}
				data.placeType = 'element';
			} else {
				data.newY = Math.floor( place - this._canvasTopOffset );
				if ( $us.header.headerTop === undefined || ( $us.header.headerTop > 0 && place > $us.header.headerTop ) ) {
					data.newY = data.newY - $us.header.scrolledOccupiedHeight;
				}
			}

			return data;
		},

		/**
		 * Scroll page to a certain position or hash
		 *
		 * @param {Number|String|jQuery} place
		 * @param {Boolean} animate
		 */
		scrollTo: function( place, animate ) {
			var offset = this.getPlacePosition.call( this, place ),
				indicateActive = function() {
					if ( offset.type === 'hash' ) {
						this._indicatePosition( place );
					} else {
						this.scroll();
					}
				}.bind( this );

			if ( animate ) {
				// Fix for iPads since scrollTop returns 0 all the time
				if ( navigator.userAgent.match( /iPad/i ) != null && $( '.us_iframe' ).length && offset.type == 'hash' ) {
					$( place )[ 0 ].scrollIntoView( { behavior: "smooth", block: "start" } );
				}

				var scrollTop =  parseInt( $us.$window.scrollTop() ),
					scrollDirections = scrollTop < offset.newY
						? 'down'
						: 'up';

				if ( scrollTop === offset.newY  ) {
					return;
				}

				// Animate options
				var animateOptions = {
					duration: this.options.animationDuration,
					easing: this.options.animationEasing,
					start: function() {
						this.isScrolling = true;
					}.bind( this ),
					complete: function() {
						this.cancel.call( this );
					}.bind( this ),
					always: function() {
						this.isScrolling = false;
						indicateActive();
					}.bind( this ),
					// Update
					step: function( now, fx ) {
						// Checking the position of the element, the position may change if the leading elements were
						// loaded with a lazy load
						var newY = this.getPlacePosition( place ).newY;
						if ( $us.header.sticky_auto_hide && scrollDirections === 'down' ) {
							newY += parseInt( $us.header.scrolledOccupiedHeight );
						}
						if ( fx.end !== newY ) {
							$us.$htmlBody.stop( true, false ).animate( {
								scrollTop: newY + 'px'
							}, $.extend( animateOptions, { easing: this.options.endAnimationEasing } ) );
						}
					}.bind( this )
				};

				$us.$htmlBody.stop( true, false ).animate( {
					scrollTop: offset.newY + 'px'
				}, animateOptions );

				// Allow user to stop scrolling manually
				$us.$window.on( 'keydown mousewheel DOMMouseScroll touchstart', this._events.cancel );
			} else {
				$us.$htmlBody.stop( true, false ).scrollTop( offset.newY );
				indicateActive();
			}
		},

		/**
		 * Cancel scroll
		 */
		cancel: function() {
			$us.$htmlBody.stop( true, false );
			$us.$window.off( 'keydown mousewheel DOMMouseScroll touchstart', this._events.cancel );
			this.isScrolling = false;
		},

		/**
		 * Scroll handler
		 */
		scroll: function() {
			var scrollTop = parseInt( $us.$window.scrollTop() );

			// Safari negative scroller fix
			scrollTop = ( scrollTop >= 0 ) ? scrollTop : 0;
			if ( ! this.isScrolling ) {
				var activeHash;
				for ( var hash in this.blocks ) {
					if ( ! this.blocks.hasOwnProperty( hash ) ) {
						continue;
					}
					if ( scrollTop >= ( this.blocks[ hash ].top - 1 ) && scrollTop < ( this.blocks[ hash ].bottom - 1 ) ) {
						activeHash = hash;
						break;
					}
				}
				this._indicatePosition( activeHash );
			}
		},

		/**
		 * Resize handler
		 */
		resize: function() {
			// Delaying the resize event to prevent glitches
			$us.timeout( function() {
				this._countAllPositions();
				this.scroll();
			}.bind( this ), 150 );
			this._countAllPositions();
			this.scroll();
		}
	};

	$( function() {
		$us.scroll = new USScroll( $us.scrollOptions || {} );
	} );

}( jQuery );
