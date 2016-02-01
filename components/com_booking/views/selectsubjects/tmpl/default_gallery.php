<?php

/**
 * Subject detail gallery template.
 * 
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

/* @var $this BookingViewSubject */

$config = AFactory::getConfig();
/* @var $config BookingConfig */
$document = JFactory::getDocument();
/* @var $document JDocument */

if ($config->displayGallery) {

	$images = BookingHelper::getSubjectImages($this->subject);
			
	if (!empty($images)) {
		if ($config->galleryStyle == 'slideshow') {
			ob_start();
?>
	   		var pg_count = <?php echo count($images); ?>;
	    	var pg_position = 0;
	    
	    	function moveLeft() {
	        	if (pg_position > 0)
	         		pg_position--;
	        	moveGallery();
	   		}
	    
	   		function moveRight() {
	       		if (pg_position < (pg_count - 5))
	         		pg_position++;
	        	moveGallery();
	   		}
	      
	   		function moveGallery() {
	        	var pg = document.getElementById('cbi-images');
	       		pg.style.left = pg_position * -<?php echo $config->galleryThumbWidth; ?> + 'px';
	  		}
<?php 
			$document->addScriptDeclaration(ob_get_clean());
?>
    		<div class="photogallery">
      			<div class="leftButton" onclick="moveLeft();"></div>
  				<div class="display">
  					<div id="cbi-images" class="images" style="width: <?php echo count($images) * $config->galleryThumbWidth; ?>px">
<?php
  						foreach  ($images as $image) {
  							$ipath = BookingHelper::getIPath($image); // image full path
  							$thumb = AImage::thumb($ipath, $config->galleryThumbWidth, $config->galleryThumbHeight); // thumnail
  							$slide = AImage::thumb($ipath, $config->galleryPreviewWidth, $config->galleryPreviewHeight); // preview
  							if ($thumb && $slide) { // both are required
?>	
  								<a href="<?php echo $slide; ?>" title="" rel="lightbox-atomium">
  								<img src="<?php echo $thumb; ?>" alt="" />
  								</a>
<?php
  							}
  						}
?>
  					</div>
  				</div>
     			<div class="rightButton" onclick="moveRight();"></div>
     			<div class="cleaner"></div>
    		</div>
<?php
		} else { // default squares
?>			
			<div class="photogallery">
  				<div id="cbi-images" class="images">
<?php
  					foreach  ($images as $image) {
  						$ipath = BookingHelper::getIPath($image); // image full path
  						$thumb = AImage::thumb($ipath, $config->galleryThumbWidth, $config->galleryThumbHeight); // thumnail
  						$slide = AImage::thumb($ipath, $config->galleryPreviewWidth, $config->galleryPreviewHeight); // preview
  						if ($thumb && $slide) { // both are required
?>	
  							<a href="<?php echo $slide; ?>" title="" rel="lightbox-atomium">
  							<img src="<?php echo $thumb; ?>" alt="" />
  							</a>
<?php
  						}
  					}
?>
  				</div>
     			<div class="cleaner"></div>
    		</div>
<?php
		}
	}
}
?>