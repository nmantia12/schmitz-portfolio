/*!
 * imagesLoaded PACKAGED v4.1.4
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

!function(a,b){"function"==typeof define&&define.amd?define("ev-emitter/ev-emitter",b):"object"==typeof module&&module.exports?module.exports=b():a.EvEmitter=b()}("undefined"==typeof window?this:window,function(){function a(){}var b=a.prototype;return b.on=function(a,b){if(a&&b){var c=this._events=this._events||{},d=c[a]=c[a]||[];return-1==d.indexOf(b)&&d.push(b),this}},b.once=function(a,b){if(a&&b){this.on(a,b);var c=this._onceEvents=this._onceEvents||{},d=c[a]=c[a]||{};return d[b]=!0,this}},b.off=function(a,b){var c=this._events&&this._events[a];if(c&&c.length){var d=c.indexOf(b);return-1!=d&&c.splice(d,1),this}},b.emitEvent=function(a,b){var c=this._events&&this._events[a];if(c&&c.length){c=c.slice(0),b=b||[];for(var d=this._onceEvents&&this._onceEvents[a],e=0;e<c.length;e++){var f=c[e],g=d&&d[f];g&&(this.off(a,f),delete d[f]),f.apply(this,b)}return this}},b.allOff=function(){delete this._events,delete this._onceEvents},a}),function(a,b){"use strict";"function"==typeof define&&define.amd?define(["ev-emitter/ev-emitter"],function(c){return b(a,c)}):"object"==typeof module&&module.exports?module.exports=b(a,require("ev-emitter")):a.imagesLoaded=b(a,a.EvEmitter)}("undefined"==typeof window?this:window,function(b,c){function f(a,b){for(var c in b)a[c]=b[c];return a}function g(b){if(Array.isArray(b))return b;var c="object"==typeof b&&"number"==typeof b.length;return c?a.call(b):[b]}function j(a,b,c){if(!(this instanceof j))return new j(a,b,c);var d=a;return"string"==typeof a&&(d=document.querySelectorAll(a)),d?(this.elements=g(d),this.options=f({},this.options),"function"==typeof b?c=b:f(this.options,b),c&&this.on("always",c),this.getImages(),l&&(this.jqDeferred=new l.Deferred),void setTimeout(this.check.bind(this))):void m.error("Bad element for imagesLoaded "+(d||a))}function i(a){this.img=a}function k(a,b){this.url=a,this.element=b,this.img=new Image}var l=b.jQuery,m=b.console,a=Array.prototype.slice;j.prototype=Object.create(c.prototype),j.prototype.options={},j.prototype.getImages=function(){this.images=[],this.elements.forEach(this.addElementImages,this)},j.prototype.addElementImages=function(a){"IMG"==a.nodeName&&this.addImage(a),!0===this.options.background&&this.addElementBackgroundImages(a);var b=a.nodeType;if(b&&d[b]){for(var c,e=a.querySelectorAll("img"),f=0;f<e.length;f++)c=e[f],this.addImage(c);if("string"==typeof this.options.background){var g=a.querySelectorAll(this.options.background);for(f=0;f<g.length;f++){var h=g[f];this.addElementBackgroundImages(h)}}}};var d={1:!0,9:!0,11:!0};return j.prototype.addElementBackgroundImages=function(a){var b=getComputedStyle(a);if(b)for(var c,d=/url\((['"])?(.*?)\1\)/gi,e=d.exec(b.backgroundImage);null!==e;)c=e&&e[2],c&&this.addBackground(c,a),e=d.exec(b.backgroundImage)},j.prototype.addImage=function(a){var b=new i(a);this.images.push(b)},j.prototype.addBackground=function(a,b){var c=new k(a,b);this.images.push(c)},j.prototype.check=function(){function a(a,c,d){setTimeout(function(){b.progress(a,c,d)})}var b=this;return this.progressedCount=0,this.hasAnyBroken=!1,this.images.length?void this.images.forEach(function(b){b.once("progress",a),b.check()}):void this.complete()},j.prototype.progress=function(a,b,c){this.progressedCount++,this.hasAnyBroken=this.hasAnyBroken||!a.isLoaded,this.emitEvent("progress",[this,a,b]),this.jqDeferred&&this.jqDeferred.notify&&this.jqDeferred.notify(this,a),this.progressedCount==this.images.length&&this.complete(),this.options.debug&&m&&m.log("progress: "+c,a,b)},j.prototype.complete=function(){var a=this.hasAnyBroken?"fail":"done";if(this.isComplete=!0,this.emitEvent(a,[this]),this.emitEvent("always",[this]),this.jqDeferred){var b=this.hasAnyBroken?"reject":"resolve";this.jqDeferred[b](this)}},i.prototype=Object.create(c.prototype),i.prototype.check=function(){var a=this.getIsImageComplete();return a?void this.confirm(0!==this.img.naturalWidth,"naturalWidth"):(this.proxyImage=new Image,this.proxyImage.addEventListener("load",this),this.proxyImage.addEventListener("error",this),this.img.addEventListener("load",this),this.img.addEventListener("error",this),void(this.proxyImage.src=this.img.src))},i.prototype.getIsImageComplete=function(){return this.img.complete&&this.img.naturalWidth},i.prototype.confirm=function(a,b){this.isLoaded=a,this.emitEvent("progress",[this,this.img,b])},i.prototype.handleEvent=function(a){var b="on"+a.type;this[b]&&this[b](a)},i.prototype.onload=function(){this.confirm(!0,"onload"),this.unbindEvents()},i.prototype.onerror=function(){this.confirm(!1,"onerror"),this.unbindEvents()},i.prototype.unbindEvents=function(){this.proxyImage.removeEventListener("load",this),this.proxyImage.removeEventListener("error",this),this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},k.prototype=Object.create(i.prototype),k.prototype.check=function(){this.img.addEventListener("load",this),this.img.addEventListener("error",this),this.img.src=this.url;var a=this.getIsImageComplete();a&&(this.confirm(0!==this.img.naturalWidth,"naturalWidth"),this.unbindEvents())},k.prototype.unbindEvents=function(){this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},k.prototype.confirm=function(a,b){this.isLoaded=a,this.emitEvent("progress",[this,this.element,b])},j.makeJQueryPlugin=function(a){a=a||b.jQuery,a&&(l=a,l.fn.imagesLoaded=function(a,b){var c=new j(this,a,b);return c.jqDeferred.promise(l(this))})},j.makeJQueryPlugin(),j});

/**
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
 * Open source under the BSD License.
 *
 *  NOTE: jQuery.easing - This is a stripped-down version of up to 2 `easeOutExpo` and `easeInOutExpo` curves!
 */
jQuery.easing.jswing=jQuery.easing.swing,jQuery.extend(jQuery.easing,{def:"easeOutExpo",easeOutExpo:function(b,c,d,a,e){return c==e?d+a:a*(-Math.pow(2,-10*c/e)+1)+d},easeInOutExpo:function(b,c,d,a,e){return 0==c?d:c==e?d+a:1>(c/=e/2)?a/2*Math.pow(2,10*(c-1))+d:a/2*(-Math.pow(2,-10*--c)+2)+d}});

/**
 * UpSolution Theme Core JavaScript Code
 *
 * @requires jQuery
 */
if ( window.$us === undefined ) {
	window.$us = {};
}

// NOTE: The variable is needed for the page-scroll.js file which changes only in menu.js
$us.mobileNavOpened = 0;

// The parameters that are in the code but not applied in the absence of a header
// When connecting header, correct parameters will be loaded
$us.header = {
	autoHide: false,
	bg: '',
	headerTop: 0,
	orientation: '',
	scrolledOccupiedHeight: 0,
	sticky_auto_hide: false,
	settings: {
		is_hidden: false
	},
	isEnableSticky: jQuery.noop
};

/**
 * Retrieve/set/erase dom modificator class <mod>_<value> for UpSolution CSS Framework
 * @param {String} mod Modificator namespace
 * @param {String} [value] Value
 * @returns {string|jQuery}
 */
jQuery.fn.usMod = function( mod, value ) {
	if ( this.length == 0 ) {
		return this;
	}
	// Remove class modificator
	if ( value === false ) {
		this.get( 0 ).className = this.get( 0 ).className.replace( new RegExp( '(^| )' + mod + '\_[a-zA-Z0-9\_\-]+( |$)' ), '$2' );
		return this;
	}
	var pcre = new RegExp( '^.*?' + mod + '\_([a-zA-Z0-9\_\-]+).*?$' ),
		arr;
	// Retrieve modificator
	if ( value === undefined ) {
		return ( arr = pcre.exec( this.get( 0 ).className ) ) ? arr[ 1 ] : false;
	}
	// Set modificator
	else {
		this.usMod( mod, false ).get( 0 ).className += ' ' + mod + '_' + value;
		return this;
	}
};

/**
 * Convert data from PHP to boolean the right way
 * @param {mixed} value
 * @returns {Boolean}
 */
$us.toBool = function( value ) {
	if ( typeof value == 'string' ) {
		return ( value == 'true' || value == 'True' || value == 'TRUE' || value == '1' );
	}
	if ( typeof value == 'boolean' ) {
		return value;
	}
	return !! parseInt( value );
};

$us.getScript = function( url, callback ) {
	if ( ! $us.ajaxLoadJs ) {
		callback();
		return false;
	}

	if ( $us.loadedScripts === undefined ) {
		$us.loadedScripts = {};
		$us.loadedScriptsFunct = {};
	}

	if ( $us.loadedScripts[ url ] === 'loaded' ) {
		callback();
		return;
	} else if ( $us.loadedScripts[ url ] === 'loading' ) {
		$us.loadedScriptsFunct[ url ].push( callback );
		return;
	}

	$us.loadedScripts[ url ] = 'loading';
	$us.loadedScriptsFunct[ url ] = [];
	$us.loadedScriptsFunct[ url ].push( callback )

	var complete = function() {
		for ( var i = 0; i < $us.loadedScriptsFunct[ url ].length; i ++ ) {
			$us.loadedScriptsFunct[ url ][ i ]();
		}
		$us.loadedScripts[ url ] = 'loaded';
	};

	var options = {
		dataType: "script",
		cache: true,
		url: url,
		complete: complete
	};

	return jQuery.ajax( options );
};

// Detecting IE browser
$us.detectIE = function() {
	var ua = window.navigator.userAgent;

	var msie = ua.indexOf( 'MSIE ' );
	if ( msie > 0 ) {
		// IE 10 or older => return version number
		return parseInt( ua.substring( msie + 5, ua.indexOf( '.', msie ) ), 10 );
	}

	var trident = ua.indexOf( 'Trident/' );
	if ( trident > 0 ) {
		// IE 11 => return version number
		var rv = ua.indexOf( 'rv:' );
		return parseInt( ua.substring( rv + 3, ua.indexOf( '.', rv ) ), 10 );
	}

	var edge = ua.indexOf( 'Edge/' );
	if ( edge > 0 ) {
		// Edge (IE 12+) => return version number
		return parseInt( ua.substring( edge + 5, ua.indexOf( '.', edge ) ), 10 );
	}

	// other browser
	return false;
};

/**
 * Determines whether animation is available or not
 * @param {string} animationName The ease animation name
 * @param {string} defaultAnimationName The default animation name
 * @return {string}
 */
$us.getAnimationName = function( animationName, defaultAnimationName ) {
	if ( jQuery.easing.hasOwnProperty( animationName ) ) {
		return animationName;
	}
	return defaultAnimationName
		? defaultAnimationName
		: jQuery.easing._default;
};

// Fixing hovers for devices with both mouse and touch screen
if ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test( navigator.userAgent ) ) {
	jQuery.isMobile = true;
} else {
	jQuery.isMobile = ( navigator.platform == 'MacIntel' && navigator.maxTouchPoints > 1 );
}
jQuery( 'html' ).toggleClass( 'no-touch', ! jQuery.isMobile );
jQuery( 'html' ).toggleClass( 'ie11', $us.detectIE() == 11 );

/**
 * Commonly used jQuery objects
 */
! function( $ ) {
	$us.$window = $( window );
	$us.$document = $( document );
	$us.$html = $( 'html' );
	$us.$body = $( '.l-body:first' );
	$us.$htmlBody = $us.$html.add( $us.$body );
	$us.$canvas = $( '.l-canvas:first' );
}( jQuery );

// Extending Lazy Load
if ( jQuery.lazyLoadXT !== undefined && jQuery.lazyLoadXT.updateEvent !== undefined ) {
	jQuery.lazyLoadXT.updateEvent = jQuery.lazyLoadXT.updateEvent + ' click uslazyloadevent';
}

/**
 * $us.canvas
 *
 * All the needed data and functions to work with overall canvas.
 */
! function( $ ) {
	"use strict";

	function USCanvas( options ) {

		// Setting options
		var defaults = {
			disableEffectsWidth: 900,
			responsive: true,
			backToTopDisplay: 100
		};
		this.options = $.extend( {}, defaults, options || {} );

		// Commonly used dom elements
		this.$header = $us.$canvas.find( '.l-header' );
		this.$main = $us.$canvas.find( '.l-main' );
		this.$sections = $us.$canvas.find( '.l-section' );
		this.$firstSection = this.$sections.first();
		this.$secondSection = this.$sections.eq( 1 );
		this.$fullscreenSections = this.$sections.filter( '.height_full' );
		this.$topLink = $( '.w-toplink' );

		// Canvas modificators
		this.type = $us.$canvas.usMod( 'type' );
		// Initial header position
		this._headerPos = this.$header.usMod( 'pos' );
		// Current header position
		this.headerPos = this._headerPos;
		this.headerInitialPos = $us.$body.usMod( 'headerinpos' );
		this.headerBg = this.$header.usMod( 'bg' );
		this.rtl = $us.$body.hasClass( 'rtl' );

		// Will be used to count fullscreen sections heights and proper scroll positions
		this.scrolledOccupiedHeight = 0;

		// Used to prevent resize events on scroll for Android browsers
		this.isScrolling = false;
		this.scrollTimeout = false;
		this.isAndroid = /Android/i.test( navigator.userAgent );

		// If in iframe...
		if ( $us.$body.hasClass( 'us_iframe' ) ) {
			// change links so they lead to main window
			$( 'a:not([target])' ).each( function() {
				$( this ).attr( 'target', '_parent' )
			} );
			// hide preloader
			jQuery( function( $ ) {
				var $framePreloader = $( '.l-popup-box-content .g-preloader', window.parent.document );
				$framePreloader.hide();
			} );
		}

		// Boundable events
		this._events = {
			scroll: this.scroll.bind( this ),
			resize: this.resize.bind( this )
		};

		$us.$window.on( 'scroll', this._events.scroll );
		$us.$window.on( 'resize load', this._events.resize );
		// Complex logics requires two initial renders: before inner elements render and after
		setTimeout( this._events.resize, 25 );
		setTimeout( this._events.resize, 75 );
	}

	USCanvas.prototype = {

		/**
		 * Scroll-driven logics
		 */
		scroll: function() {
			var scrollTop = parseInt( $us.$window.scrollTop() );

			// Show/hide go to top link
			this.$topLink.toggleClass( 'active', ( scrollTop >= this.winHeight * this.options.backToTopDisplay / 100 ) );

			if ( this.isAndroid ) {
				this.isScrolling = true;
				if ( this.scrollTimeout ) {
					clearTimeout( this.scrollTimeout );
				}
				this.scrollTimeout = setTimeout( function() {
					this.isScrolling = false;
				}.bind( this ), 100 );
			}
		},

		/**
		 * Resize-driven logics
		 */
		resize: function() {
			// Window dimensions
			this.winHeight = parseInt( $us.$window.height() );
			this.winWidth = parseInt( $us.$window.width() );

			// Disabling animation on mobile devices
			$us.$body.toggleClass( 'disable_effects', ( this.winWidth < this.options.disableEffectsWidth ) );

			// Vertical centering of fullscreen sections in IE 11
			var ieVersion = $us.detectIE();
			if ( ( ieVersion !== false && ieVersion == 11 ) && ( this.$fullscreenSections.length > 0 && ! this.isScrolling ) ) {
				var adminBar = $( '#wpadminbar' ),
					adminBarHeight = ( adminBar.length ) ? adminBar.height() : 0;
				this.$fullscreenSections.each( function( index, section ) {
					var $section = $( section ),
						sectionHeight = this.winHeight,
						isFirstSection = ( index == 0 && $section.is( this.$firstSection ) );
					// First section
					if ( isFirstSection ) {
						sectionHeight -= $section.offset().top;
					}
					// 2+ sections
					else {
						sectionHeight -= $us.header.scrolledOccupiedHeight + adminBarHeight;
					}
					if ( $section.hasClass( 'valign_center' ) ) {
						var $sectionH = $section.find( '.l-section-h' ),
							sectionTopPadding = parseInt( $section.css( 'padding-top' ) ),
							contentHeight = $sectionH.outerHeight(),
							topMargin;
						$sectionH.css( 'margin-top', '' );
						// Section was extended by extra top padding that is overlapped by fixed solid header and not
						// visible
						var sectionOverlapped = isFirstSection && $us.header.pos == 'fixed' && $us.header.bg != 'transparent' && $us.header.orientation != 'ver';
						if ( sectionOverlapped ) {
							// Part of first section is overlapped by header
							topMargin = Math.max( 0, ( sectionHeight - sectionTopPadding - contentHeight ) / 2 );
						} else {
							topMargin = Math.max( 0, ( sectionHeight - contentHeight ) / 2 - sectionTopPadding );
						}
						$sectionH.css( 'margin-top', topMargin || '' );
					}
				}.bind( this ) );
				$us.$canvas.trigger( 'contentChange' );
			}

			// If the page is loaded in iframe
			if ( $us.$body.hasClass( 'us_iframe' ) ) {
				var $frameContent = $( '.l-popup-box-content', window.parent.document ),
					outerHeight = $us.$body.outerHeight( true );
				if ( outerHeight > 0 && $( window.parent ).height() > outerHeight ) {
					$frameContent.css( 'height', outerHeight );
				} else {
					$frameContent.css( 'height', '' );
				}
			}

			// Fix scroll glitches that could occur after the resize
			this.scroll();
		}
	};

	$us.canvas = new USCanvas( $us.canvasOptions || {} );

}( jQuery );

/**
 * CSS-analog of jQuery slideDown/slideUp/fadeIn/fadeOut functions (for better rendering)
 */
! function() {

	/**
	 * Remove the passed inline CSS attributes.
	 *
	 * Usage: $elm.resetInlineCSS('height', 'width');
	 */
	jQuery.fn.resetInlineCSS = function() {
		for ( var index = 0; index < arguments.length; index ++ ) {
			this.css( arguments[ index ], '' );
		}
		return this;
	};

	jQuery.fn.clearPreviousTransitions = function() {
		// Stopping previous events, if there were any
		var prevTimers = ( this.data( 'animation-timers' ) || '' ).split( ',' );
		if ( prevTimers.length >= 2 ) {
			this.resetInlineCSS( 'transition' );
			prevTimers.map( clearTimeout );
			this.removeData( 'animation-timers' );
		}
		return this;
	};
	/**
	 *
	 * @param {Object} css key-value pairs of animated css
	 * @param {Number} duration in milliseconds
	 * @param {Function} onFinish
	 * @param {String} easing CSS easing name
	 * @param {Number} delay in milliseconds
	 */
	jQuery.fn.performCSSTransition = function( css, duration, onFinish, easing, delay ) {
		duration = duration || 250;
		delay = delay || 25;
		easing = easing || 'ease';
		var $this = this,
			transition = [];

		this.clearPreviousTransitions();

		for ( var attr in css ) {
			if ( ! css.hasOwnProperty( attr ) ) {
				continue;
			}
			transition.push( attr + ' ' + ( duration / 1000 ) + 's ' + easing );
		}
		transition = transition.join( ', ' );
		$this.css( {
			transition: transition
		} );

		// Starting the transition with a slight delay for the proper application of CSS transition properties
		var timer1 = setTimeout( function() {
			$this.css( css );
		}, delay );

		var timer2 = setTimeout( function() {
			$this.resetInlineCSS( 'transition' );
			if ( typeof onFinish == 'function' ) {
				onFinish();
			}
		}, duration + delay );

		this.data( 'animation-timers', timer1 + ',' + timer2 );
	};

	// Height animations
	jQuery.fn.slideDownCSS = function( duration, onFinish, easing, delay ) {
		if ( this.length == 0 ) {
			return;
		}
		var $this = this;
		this.clearPreviousTransitions();
		// Grabbing paddings
		this.resetInlineCSS( 'padding-top', 'padding-bottom' );
		var timer1 = setTimeout( function() {
			var paddingTop = parseInt( $this.css( 'padding-top' ) ),
				paddingBottom = parseInt( $this.css( 'padding-bottom' ) );
			// Grabbing the "auto" height in px
			$this.css( {
				visibility: 'hidden',
				position: 'absolute',
				height: 'auto',
				'padding-top': 0,
				'padding-bottom': 0,
				display: 'block'
			} );
			var height = $this.height();
			$this.css( {
				overflow: 'hidden',
				height: '0px',
				opacity: 0,
				visibility: '',
				position: ''
			} );
			$this.performCSSTransition( {
				opacity: 1,
				height: height + paddingTop + paddingBottom,
				'padding-top': paddingTop,
				'padding-bottom': paddingBottom
			}, duration, function() {
				$this.resetInlineCSS( 'overflow' ).css( 'height', 'auto' );
				if ( typeof onFinish == 'function' ) {
					onFinish();
				}
			}, easing, delay );
		}, 25 );
		this.data( 'animation-timers', timer1 + ',null' );
	};
	jQuery.fn.slideUpCSS = function( duration, onFinish, easing, delay ) {
		if ( this.length == 0 ) {
			return;
		}
		this.clearPreviousTransitions();
		this.css( {
			height: this.outerHeight(),
			overflow: 'hidden',
			'padding-top': this.css( 'padding-top' ),
			'padding-bottom': this.css( 'padding-bottom' )
		} );
		var $this = this;
		this.performCSSTransition( {
			height: 0,
			opacity: 0,
			'padding-top': 0,
			'padding-bottom': 0
		}, duration, function() {
			$this.resetInlineCSS( 'overflow', 'padding-top', 'padding-bottom' ).css( {
				display: 'none'
			} );
			if ( typeof onFinish == 'function' ) {
				onFinish();
			}
		}, easing, delay );
	};

	// Opacity animations
	jQuery.fn.fadeInCSS = function( duration, onFinish, easing, delay ) {
		if ( this.length == 0 ) {
			return;
		}
		this.clearPreviousTransitions();
		this.css( {
			opacity: 0,
			display: 'block'
		} );
		this.performCSSTransition( {
			opacity: 1
		}, duration, onFinish, easing, delay );
	};
	jQuery.fn.fadeOutCSS = function( duration, onFinish, easing, delay ) {
		if ( this.length == 0 ) {
			return;
		}
		var $this = this;
		this.performCSSTransition( {
			opacity: 0
		}, duration, function() {
			$this.css( 'display', 'none' );
			if ( typeof onFinish == 'function' ) {
				onFinish();
			}
		}, easing, delay );
	};
}();

jQuery( function( $ ) {
	"use strict";

	if ( document.cookie.indexOf( 'us_cookie_notice_accepted=true' ) !== -1 ) {
		$( '.l-cookie' ).remove();
	} else {
		$( document ).on( 'click', '#us-set-cookie', function( e ) {
			e.preventDefault();
			e.stopPropagation();
			var d = new Date();
			d.setFullYear( d.getFullYear() + 1 );
			document.cookie = 'us_cookie_notice_accepted=true; expires=' + d.toUTCString() + '; path=/;' + ( location.protocol === 'https:' ? ' secure;' : '' );
			$( '.l-cookie' ).remove();
		} );
	}

	// Force popup opening on links with ref
	if ( $( 'a[ref=magnificPopup][class!=direct-link]' ).length != 0 ) {
		$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/magnific-popup.js', function() {
			$( 'a[ref=magnificPopup][class!=direct-link]' ).magnificPopup( {
				type: 'image',
				removalDelay: 300,
				mainClass: 'mfp-fade',
				fixedContentPos: true
			} );
		} );
	}

	// Hide background images until are loaded
	jQuery( '.l-section-img' ).each( function() {
		var $this = $( this ),
			img = new Image(),
			bgImg = $this.css( 'background-image' ) || '';

		// If the background image CSS seems to be valid, preload an image and then show it
		if ( bgImg.match( /url\(['"]*(.*?)['"]*\)/i ) ) {
			img.onload = function() {
				if ( ! $this.hasClass( 'loaded' ) ) {
					$this.addClass( 'loaded' );
				}
			};
			img.src = bgImg.replace( /url\(['"]*(.*?)['"]*\)/i, '$1' );
			// If we cannot parse the background image CSS, just add loaded class to the background tag so a background
			// image is shown anyways
		} else {
			$this.addClass( 'loaded' );
		}
	} );

	// Hide/Show video backgrounds under certain widths
	var $usSectionVideoContainer = $( '.l-section-video' );
	if ( $usSectionVideoContainer.length ) {
		$( window ).on( 'resize load', function() {
			$usSectionVideoContainer.each( function() {
				var $container = $( this );

				if ( ! $container.data( 'video-disable-width' ) ) {
					return false;
				}

				if ( window.innerWidth < parseInt( $container.data( 'video-disable-width' ) ) ) {
					$container.addClass( 'hidden' );
				} else {
					$container.removeClass( 'hidden' );
				}
			} );
		} );
	}


	/* YouTube/Vimeo background */
	var $usYTVimeoVideoContainer = $( '.with_youtube, .with_vimeo' );
	if ( $usYTVimeoVideoContainer.length ) {
		$( window ).on( 'resize load', function() {
			$usYTVimeoVideoContainer.each( function() {
				var $container = $( this ),
					$frame = $container.find( 'iframe' ).first(),
					cHeight = $container.innerHeight(),
					cWidth = $container.innerWidth(),
					fWidth = '',
					fHeight = '';

				if ( cWidth / cHeight < 16 / 9 ) {
					fWidth = cHeight * ( 16 / 9 );
					fHeight = cHeight;
				} else {
					fWidth = cWidth;
					fHeight = fWidth * ( 9 / 16 );
				}

				$frame.css( {
					'width': Math.round( fWidth ),
					'height': Math.round( fHeight ),
				} );
			} );
		} );
	}


} );

/**
 * Behaves the same as setTimeout except uses requestAnimationFrame() where possible for better performance
 * @param {function} fn The callback function
 * @param {int} delay The delay in milliseconds
 */
$us.timeout = function( fn, delay ) {
	var start = new Date().getTime(),
		handle = new Object();

	function loop() {
		var current = new Date().getTime(),
			delta = current - start;
		delta >= delay
			? fn.call()
			: handle.value = window.requestAnimationFrame( loop );
	};
	handle.value = window.requestAnimationFrame( loop );
	return handle;
};

/**
 * Behaves the same as clearTimeout except uses cancelRequestAnimationFrame() where possible for better performance
 * @param {int|object} fn The callback function
 */
$us.clearTimeout = function( handle ) {
	window.cancelAnimationFrame( handle.value );
};

/**
 * Returns a function, that, as long as it continues to be invoked, will not
 * be triggered. The function will be called after it stops being called for
 * N milliseconds. If `immediate` is passed, trigger the function on the
 * leading edge, instead of the trailing. The function also has a property 'clear'
 * that is a function which will clear the timer to prevent previously scheduled executions.
 *
 * @param {Function} function to wrap
 * @param {Number} timeout in ms (`100`)
 * @param {Boolean} whether to execute at the beginning (`false`)
 * @return {Function}
 */
$us.debounce = function( fn, wait, immediate ) {
	var timeout, args, context, timestamp, result;
	if ( null == wait ) wait = 100;
	function later() {
		var last = Date.now() - timestamp;
		if ( last < wait && last >= 0 ) {
			timeout = setTimeout( later, wait - last );
		} else {
			timeout = null;
			if ( ! immediate ) {
				result = fn.apply( context, args );
				context = args = null;
			}
		}
	};
	var debounced = function() {
		context = this;
		args = arguments;
		timestamp = Date.now();
		var callNow = immediate && ! timeout;
		if ( ! timeout ) timeout = setTimeout( later, wait );
		if ( callNow ) {
			result = fn.apply( context, args );
			context = args = null;
		}
		return result;
	};
	debounced.prototype = {
		clear: function() {
			if ( timeout ) {
				clearTimeout( timeout );
				timeout = null;
			}
		},
		flush: function() {
			if ( timeout ) {
				result = fn.apply( context, args );
				context = args = null;
				clearTimeout( timeout );
				timeout = null;
			}
		}
	};
	return debounced;
};

// Prototype mixin for all classes working with events
$us.mixins = {};
$us.mixins.Events = {
	/**
	 * Attach a handler to an event for the class instance
	 * @param {String} eventType A string containing event type, such as 'beforeShow' or 'change'
	 * @param {Function} handler A function to execute each time the event is triggered
	 */
	on: function( eventType, handler ) {
		if ( this.$$events === undefined ) {
			this.$$events = {};
		}
		if ( this.$$events[ eventType ] === undefined ) {
			this.$$events[ eventType ] = [];
		}
		this.$$events[ eventType ].push( handler );
		return this;
	},
	/**
	 * Remove a previously-attached event handler from the class instance
	 * @param {String} eventType A string containing event type, such as 'beforeShow' or 'change'
	 * @param {Function} [handler] The function that is to be no longer executed.
	 * @chainable
	 */
	off: function( eventType, handler ) {
		if ( this.$$events === undefined || this.$$events[ eventType ] === undefined ) {
			return this;
		}
		if ( handler !== undefined ) {
			var handlerPos = jQuery.inArray( handler, this.$$events[ eventType ] );
			if ( handlerPos != - 1 ) {
				this.$$events[ eventType ].splice( handlerPos, 1 );
			}
		} else {
			this.$$events[ eventType ] = [];
		}
		return this;
	},
	/**
	 * Execute all handlers and behaviours attached to the class instance for the given event type
	 * @param {String} eventType A string containing event type, such as 'beforeShow' or 'change'
	 * @param {Array} extraParameters Additional parameters to pass along to the event handler
	 * @chainable
	 */
	trigger: function( eventType, extraParameters ) {
		if ( this.$$events === undefined || this.$$events[ eventType ] === undefined || this.$$events[ eventType ].length == 0 ) {
			return this;
		}
		var params = ( arguments.length > 2 || ! jQuery.isArray( extraParameters ) ) ? Array.prototype.slice.call( arguments, 1 ) : extraParameters;
		// First argument is the current class instance
		params.unshift( this );
		for ( var index = 0; index < this.$$events[ eventType ].length; index ++ ) {
			this.$$events[ eventType ][ index ].apply( this.$$events[ eventType ][ index ], params );
		}
		return this;
	}
};

/**
 * $us.waypoints
 */
;(function( $, undefined ) {
	"use strict";
	function USWaypoints() {
		// Waypoints that will be called at certain scroll position
		this.waypoints = [];

		// Recount scroll waypoints on any content changes
		$us.$canvas
			.on( 'contentChange', this._countAll.bind( this ) );
		// Recount scroll waypoints with lazyload content
		$us.$document
			.on( 'lazyload', this._countAll.bind( this ) );
		$us.$window
			.on( 'resize load', this._events.resize.bind( this ) )
			.on( 'scroll', this._events.scroll.bind( this ) );
		$us.timeout( this._events.resize.bind( this ), 75 );
		$us.timeout( this._events.scroll.bind( this ), 75 );
	}
	USWaypoints.prototype = {
		// Handler's
		_events: {
			/**
			 * Scroll handler
			 */
			scroll: function() {
				var scrollTop = parseInt( $us.$window.scrollTop() );

				// Safari negative scroller fix
				scrollTop = ( scrollTop >= 0 ) ? scrollTop : 0;

				// Handling waypoints
				for ( var i = 0; i < this.waypoints.length; i ++ ) {
					if ( this.waypoints[ i ].scrollPos < scrollTop ) {
						this.waypoints[ i ].fn( this.waypoints[ i ].$elm );
						this.waypoints.splice( i, 1 );
						i --;
					}
				}
			},
			/**
			 * Resize handler
			 */
			resize: function() {
				// Delaying the resize event to prevent glitches
				$us.timeout( function() {
					this._countAll.call( this );
					this._events.scroll.call( this );
				}.bind( this ), 150 );
				this._countAll.call( this );
				this._events.scroll.call( this );
			}
		},
		/**
		 * Add new waypoint
		 *
		 * @param {jQuery} $elm object with the element
		 * @param {mixed} offset Offset from bottom of screen in pixels ('100') or percents ('20%')
		 * @param {Function} fn The function that will be called
		 */
		add: function( $elm, offset, fn ) {
			$elm = ( $elm instanceof $ ) ? $elm : $( $elm );
			if ( $elm.length == 0 ) {
				return;
			}
			if ( typeof offset != 'string' || offset.indexOf( '%' ) == - 1 ) {
				// Not percent: using pixels
				offset = parseInt( offset );
			}
			var waypoint = {
				$elm: $elm, offset: offset, fn: fn
			};
			this._count( waypoint );
			this.waypoints.push( waypoint );
		},
		/**
		 *
		 * @param {Object} waypoint
		 * @private
		 */
		_count: function( waypoint ) {
			var elmTop = waypoint.$elm.offset().top, winHeight = $us.$window.height();
			if ( typeof waypoint.offset == 'number' ) {
				// Offset is defined in pixels
				waypoint.scrollPos = elmTop - winHeight + waypoint.offset;
			} else {
				// Offset is defined in percents
				waypoint.scrollPos = elmTop - winHeight + winHeight * parseInt( waypoint.offset ) / 100;
			}
		},
		/**
		 * Count all targets for proper scrolling
		 *
		 * @private
		 */
		_countAll: function() {
			// Counting waypoints
			for ( var i = 0; i < this.waypoints.length; i ++ ) {
				this._count( this.waypoints[ i ] );
			}
		}
	};
	$us.waypoints = new USWaypoints;
})( jQuery );

;( function() {
	var lastTime = 0,
		vendors = ['ms', 'moz', 'webkit', 'o'];
	for ( var x = 0; x < vendors.length && ! window.requestAnimationFrame; ++ x ) {
		window.requestAnimationFrame = window[ vendors[ x ] + 'RequestAnimationFrame' ];
		window.cancelAnimationFrame = window[ vendors[ x ] + 'CancelAnimationFrame' ] || window[ vendors[ x ] + 'CancelRequestAnimationFrame' ];
	}
	if ( ! window.requestAnimationFrame ) {
		window.requestAnimationFrame = function( callback, element ) {
			var currTime = new Date().getTime(),
				timeToCall = Math.max( 0, 16 - ( currTime - lastTime ) ),
				id = window.setTimeout( function() {
					callback( currTime + timeToCall );
				}, timeToCall );
			lastTime = currTime + timeToCall;
			return id;
		};
	}
	if ( ! window.cancelAnimationFrame ) {
		window.cancelAnimationFrame = function( id ) {
			clearTimeout( id );
		};
	}
}() );

/*
 * Remove empty space before content for video post type with active preview
 */
if ( $us.$body.hasClass( 'single-format-video' ) ) {
	figure = $us.$body.find( 'figure.wp-block-embed div.wp-block-embed__wrapper' );
	if ( figure.length ) {
		figure.each( function() {
			if ( this.firstElementChild === null ) {
				this.remove();
			}
		} );
	}
}

/*
 * With "Show More" link, used in Text Block and Post Content elements
 */
! function( $, undefined ) {
	"use strict";

	$us.ToggleMoreContent = function( container ) {
		this.init( container );
	};
	$us.ToggleMoreContent.prototype = {
		init: function( container ) {
			// Element
			this.$container = $( container );
			this.$firstElm = $( '> *:first', this.$container );
			this.toggleHeight = this.$container.data( 'toggle-height' ) || 200;
			// Events
			this.$container.on( 'click', '.toggle-show-more, .toggle-show-less', function( e ) {
				e.preventDefault();
				e.stopPropagation();
				this.$container
					.toggleClass( 'show_content', $( e.target ).hasClass( 'toggle-show-more' ) );
				$us.timeout( function() {
					$us.$canvas.trigger( 'contentChange' );
				}, 1 );
			}.bind( this ) );
			if ( ! this.$container.closest( '.owl-carousel' ).length ) {
				// Init
				this.initHeightCheck.call( this );
			}
		},
		initHeightCheck: function() {
			// Set the height to the element in any unit of measurement and get the height in pixels
			var height = this.$firstElm.css( 'height', this.toggleHeight ).height();
			this.$firstElm.css( 'height', '' );
			var elmHeight = this.$firstElm.height();

			if ( elmHeight && elmHeight <= height ) {
				$( '.toggle-links', this.$container ).hide();
				this.$firstElm.css( 'height', '' );
				this.$container.removeClass( 'with_show_more_toggle' );
			} else {
				$( '.toggle-links', this.$container ).show();
				this.$firstElm.css( 'height', this.toggleHeight );
			}
		}
	};
	$.fn.usToggleMoreContent = function() {
		return this.each( function() {
			$( this ).data( 'usToggleMoreContent', new $us.ToggleMoreContent( this ) );
		} );
	};

	$( '[data-toggle-height]' ).usToggleMoreContent();
}( jQuery );

/*
 * Post Image object-fit polyfill for Internet Explorer
 */
! function( $, undefined ) {
	"use strict";
	if ( $us.detectIE() == 11 && $( '.post_image.has_ratio' ).length && ! $( '.w-grid' ).length ) {
		// Add object-fit support library for IE11
		$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/objectFitPolyfill.js', function() {
			objectFitPolyfill();
			// Bind objectFitPolyfill() event for IE11 on lazy load event
			$us.$document.on( 'lazyload', function() {
				objectFitPolyfill();
			} );
		} );

	}
}( jQuery );