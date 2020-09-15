/**
 * Remove Video Overlay on click
 */
jQuery( function( $ ) {
	$( '.w-video.with_overlay' ).each( function() {
		$( this ).on( 'click', function( ev ) {
			ev.preventDefault();
			$( this ).removeClass( 'with_overlay' );
		} );
	} );
} );