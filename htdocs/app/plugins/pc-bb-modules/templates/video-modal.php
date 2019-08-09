<?php 
//This template is used by both Beaver Builder components and normal modals.
$video_source = isset($settings) ? $settings->modal_video_source : $slide['video_source'];
?>
<?php if ( 'video_service' == $video_source ) {
  $video_service_url = isset($settings) ? $settings->modal_video_service_url : $slide['video_link'];
  $video_data = FLBuilderUtils::get_video_data( $video_service_url );
  $video_thumbnail = isset($settings) ? $settings->modal_video_fallback_src : $slide['video_thumbnail']['url'];
  $video_audio = isset($settings) ? $settings->modal_video_audio : true ;
?>
<div class="video-player"
data-fallback="<?php if ( isset( $video_thumbnail ) ) { echo $video_thumbnail; } ?>"
<?php if ( isset( $video_service_url ) ) : ?>
data-<?php echo $video_data['type']; ?>="<?php echo $video_service_url; ?>"
data-video-id="<?php echo $video_data['video_id']; ?>"
data-enable-audio="<?php echo $video_audio; ?>"
<?php if ( isset( $video_data['params'] ) ) : ?>
	<?php foreach ( $video_data['params'] as $key => $val ) : ?>
		data-<?php echo $key . '="' . $val . '"'; ?>
	<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>>
    <iframe src="https://www.youtube.com/embed/<?php echo $video_data['video_id']; ?>?rel=0" 
        frameborder="0" allow="autoplay; encrypted-media" allowfullscreen style="width: 560px; height: 315px;">
    </iframe>
</div>
<?php } ?>

<?php if ( 'wordpress' == $video_source ) { 
    $video_upload_mp4 = isset($settings) ? $settings->modal_video : $slide['video_mp4']['url'];
    $video_upload_webm = isset($settings) ? $settings->modal_video_webm : $slide['video_webm']['url'];
    $video_poster = isset($settings) ? $settings->modal_video_fallback_src : $slide['video_poster']['url'];
    $attr =  array(
      'mp4'      => $video_upload_mp4,
      'webm'     => $video_upload_webm,
      // 'flv'      => $video_flv,
      'poster'   => $video_poster,
      'preload'  => 'auto'
    );
?>
<div class="video-player">
  <?php echo wp_video_shortcode( $attr ); ?>
</div>
<?php } ?>

<?php if ( 'video_url' == $video_source ) { 
    $video_upload_mp4 = isset($settings) ? $settings->modal_video_url_mp4 : $slide['video_mp4_link'];
    $video_upload_webm = isset($settings) ? $settings->modal_video_url_webm : $slide['video_webm_link'];
    $video_poster = isset($settings) ? $settings->modal_video_fallback_src : $slide['video_poster']['url'];
    $attr =  array(
      'mp4'      => $video_upload_mp4,
      'webm'     => $video_upload_webm,
      // 'flv'      => $video_flv,
      'poster'   => $video_poster,
      'preload'  => 'auto'
    );
?>
<div class="video-player">
  <?php echo wp_video_shortcode( $attr ); ?>
</div>
<?php } ?>

