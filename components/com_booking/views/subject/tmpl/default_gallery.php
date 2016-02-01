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
		//<![CDATA[
		
			/**
			 * Total number of images in the slideshow
			 */
			 
	   		var pg_count = <?php echo count($images); ?>;
	   		
	   		/**
	   		 * Current position in the slideshow
	   		 */
	    	var pg_position = 0;
	    	
	    	/**
	    	 * Duration of the slideshow tween in miliseconds
	    	 */
	    	var pg_duration = <?php echo $config->gallerySlideshowDuration; ?>;
	    	
	    	/**
	    	 * Number of images moved in one step
	    	 */
	    	var pg_shift = <?php echo $config->gallerySlideshowShift; ?>;
	    
	    	/**
	    	 * Move slideshow to the left
	    	 */
	    	function moveLeft() {
	        	if (pg_position > 0) {
	         		pg_position -= pg_shift;
	        		moveGallery(-1);
	        	}
	   		}
	    	/**
	    	 * Move slideshow to the right
	    	 */
	   		function moveRight() {
	       		if (pg_position < pg_count - pg_shift) {
	         		pg_position += pg_shift;
	        		moveGallery(1);
	        	}
	   		}
	      
	      	/**
	      	 * Move slideshow to the left or rigth
	      	 * @param shift int 1 => to the right, -1 => to the left
	      	 */
	   		function moveGallery(shift) {
	   			
	   			var gallery = document.id('cbi-images').getElements('img'); // all images in the slideshow
	   			
	   			var current = gallery[pg_position].getCoordinates(); // image at the current begining of the slideshow 
	   			var mate = gallery[pg_position - shift * pg_shift].getCoordinates(); // image at the next begining of the slideshow after move
				
				var base = document.id('cbi-images').style.left == "" ? 0 : document.id('cbi-images').style.left.toInt(); // number of the left current relative position of the slideshow 
					
    			new Fx.Tween('cbi-images', { // move sllideshow with Mootools Fx.Tween
    				duration: pg_duration,
    				property: 'left'
				}).start(base, base - Math.abs(current.left - mate.left) * shift);
	  		}
	  		
	  	//]]>
<?php 
			$document->addScriptDeclaration(ob_get_clean());
?>
    		<div id="photogallery" class="photogallery">
      			<?php if (!$config->enableResponsive) : ?><div class="leftButton" onclick="moveLeft();"></div><?php endif; ?>
  				<div id="photogallery-images" class="display">
  					<div id="cbi-images" class="images" style="width: <?php echo count($images) * $config->galleryThumbWidth + ((count($images) - 1) * 5); ?>px">
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
     			<?php if (!$config->enableResponsive) : ?><div class="rightButton" onclick="moveRight();"></div><?php endif; ?>
     			<?php if (!$config->enableResponsive) : ?><div class="cleaner"></div><?php endif; ?>
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