

<?php if ( 'wordpress' == $settings->bg_video_source ) {
	// lifted from class-fl-builder-model.php and the process_row_settings function

	// Video Fallback Photo
	if ( ! empty( $settings->bg_video_fallback_src ) ) {
		$fallback = $settings->bg_video_fallback_src;
	} else {
		$fallback = '';
	}

	// Video MP4
	$mp4 = FLBuilderPhoto::get_attachment_data( $settings->bg_video );

	if ( $mp4 ) {
		$parts = explode( '.', $mp4->filename );
		$mp4->extension = array_pop( $parts );
		$settings->bg_video_data = $mp4;
		$settings->bg_video_data->fallback = $fallback;
	}

	// Video WebM
	$webm = FLBuilderPhoto::get_attachment_data( $settings->bg_video_webm );

	if ( $webm ) {
		$parts = explode( '.', $webm->filename );
		$webm->extension = array_pop( $parts );
		$settings->bg_video_webm_data = $webm;
		$settings->bg_video_webm_data->fallback = $fallback;
	}

	$vid_data = FLBuilderModel::get_row_bg_data( $module );

?>
<div class="fl-bg-video"
data-width="<?php if ( isset( $vid_data['mp4'] ) ) { echo $vid_data['mp4']->width;
} else { echo $vid_data['webm']->width;
} ?>"
data-height="<?php if ( isset( $vid_data['mp4'] ) ) { echo $vid_data['mp4']->height;
} else { echo $vid_data['webm']->height;
} ?>"
data-fallback="<?php if ( isset( $vid_data['mp4'] ) ) { echo $vid_data['mp4']->fallback;
} else { echo $vid_data['webm']->fallback;
} ?>"
<?php if ( isset( $vid_data['mp4'] ) ) : ?>
data-mp4="<?php echo $vid_data['mp4']->url; ?>"
data-mp4-type="video/<?php echo $vid_data['mp4']->extension; ?>"
<?php endif; ?>
<?php if ( isset( $vid_data['webm'] ) ) : ?>
data-webm="<?php echo $vid_data['webm']->url; ?>"
data-webm-type="video/<?php echo $vid_data['webm']->extension; ?>"
<?php endif; ?>></div>
<?php } ?>

<?php if ( 'video_url' == $settings->bg_video_source ) { ?>
<div class="fl-bg-video"
data-fallback="<?php if ( isset( $settings->bg_video_fallback_src ) ) { echo $settings->bg_video_fallback_src;} ?>"
<?php if ( isset( $settings->bg_video_url_mp4 ) ) : ?>
data-mp4="<?php echo $settings->bg_video_url_mp4; ?>"
data-mp4-type="video/mp4"
<?php endif; ?>
<?php if ( isset( $settings->bg_video_url_webm ) ) : ?>
data-webm="<?php echo $settings->bg_video_url_webm; ?>"
data-webm-type="video/webm"
<?php endif; ?>></div>
<?php } ?>

<?php if ( 'video_service' == $settings->bg_video_source ) {
	$video_data = FLBuilderUtils::get_video_data( $settings->bg_video_service_url ); ?>
<div class="fl-bg-video"
data-fallback="<?php if ( isset( $settings->bg_video_fallback_src ) ) { echo $settings->bg_video_fallback_src;} ?>"
<?php if ( isset( $settings->bg_video_service_url ) ) : ?>
data-<?php echo $video_data['type']; ?>="<?php echo $settings->bg_video_service_url; ?>"
data-video-id="<?php echo $video_data['video_id']; ?>"
data-enable-audio="no"
<?php if ( isset( $video_data['params'] ) ) : ?>
	<?php foreach ( $video_data['params'] as $key => $val ) : ?>
		data-<?php echo $key . '="' . $val . '"'; ?>
	<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>>
<div class="fl-bg-video-player"></div>
</div>
<?php } ?>
