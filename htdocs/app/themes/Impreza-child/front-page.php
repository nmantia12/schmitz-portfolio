<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 */
require(dirname(__DIR__) . '/vendor/autoload.php');
use Vimeo\Vimeo;

$client = new Vimeo("{client_id}", "{client_secret}", "{access_token}");

$response = $client->request('/tutorial', array(), 'GET');
print_r($response);


$vimeo_key = 'https://vimeo.com/437619920';
if ($vimeo_key) {

	// YES, we have something...
	// lets build the vimeo query

	// fix the URLs for every case
	// we want ONLY the NUMBER...

	$vimeo_key = str_replace("http://vimeo.com/", "", $vimeo_key);
	$vimeo_key = str_replace("https://vimeo.com/", "", $vimeo_key);
	$vimeo_key = rtrim($vimeo_key,"/");
	$api_endpoint = 'http://vimeo.com/api/v2/video/' . $vimeo_key .'.xml';
	$videos = simplexml_load_string(curl_get($api_endpoint));
	?>

	<div class="list-item list-item-video list-grid-system">
		<a href="https://vimeo.com/makerschmitz/<?php echo $vimeo_key; ?>" data-vimeo="<?php echo $vimeo_key; ?>" target="_blank" title="<?php the_title(); ?>" id="post-<?php the_ID(); ?>" class="list-item-inside dblock jstrigger-vimeo unstyled">
			<div class="vimeo-play-icon"></div>
			<?php foreach ($videos->video as $video): ?>
				<img class="vimeo-thumbnail" src="<?php echo $video->thumbnail_medium ?>" />
			<?php endforeach ?>
		</a>

		<p class="list-item-title small-font">
			<?php
			echo '(some method to get the title of this item)';
			?>
		</p>
	</div>

	<?php

} // if there is no meta field...

else {

// nothing happens

}
?>

<script>
/*
 * 2: AJAX - play vimeo
 ****************************************************
This script does the following:
When the user clicks the thumbnail, it gets replaced by an iframe where the video starts playing.
 */


jQuery(function($) {
 $('.jstrigger-vimeo').click(function(){ // the trigger action
   var vimeokey = $(this).data('vimeo');
   // 240 x 180px
   $(this).replaceWith('<iframe src="http://player.vimeo.com/video/' + vimeokey + '?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff&amp;autoplay=1" width="240" height="180" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen class="list-item-inside dblock"></iframe>');
   return false;
 });
});

</script>
