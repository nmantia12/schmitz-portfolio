( function( $ ) {
	FLBuilderSettingsConfig = 'undefined' === typeof FLBuilderSettingsConfig ? {} : FLBuilderSettingsConfig;
	$.extend( FLBuilderSettingsConfig, <?php echo json_encode( $settings ); ?> );
	if ( 'undefined' !== typeof FLBuilder ) {
		FLBuilder.triggerHook( 'settingsConfigLoaded' );
	}
} )( jQuery );
