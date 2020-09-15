<?php
/* Custom functions code goes here. */

// vimeo helper function
// Curl helper function
// based on this example
// https://github.com/vimeo/vimeo-api-examples/blob/master/simple-api/simple/simple.php

// detailed explanations are in this post:
// http://ms-studio.net/2012/notes/using-the-vimeo-api-in-wordpress/

function curl_get($url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	$return = curl_exec($curl);
	curl_close($curl);
	return $return;
}
