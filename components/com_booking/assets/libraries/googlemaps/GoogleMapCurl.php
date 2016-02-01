<?php
require_once 'GoogleMap.php';

/*
 * override method fetchURL() to prevent error:
 * Warning: file_get_contents()  http:// wrapper is disabled in the server configuration by allow_url_fopen=0
 */

class GoogleMapCurlAPI extends GoogleMapAPI{
	/**
	 * fetch a URL. Override this method to change the way URLs are fetched.
	 *
	 * @param string $url
	 */
	function fetchURL($url) {
	
		//standart call
		if(ini_get('allow_url_fopen') ) {
			return parent::fetchURL($url);
		}
		//improved with cURL
		else {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
	
	}
}