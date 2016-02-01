<?php

/**
 * Images gallery.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

/* @var $this JView */
$bar = &JToolBar::getInstance('toolbar_images');
/* @var $bar JToolBar */
$bar->appendButton('Popup', 'new', 'ADD_EXISTING_IMAGES', ARoute::safeURL(ARoute::view(VIEW_IMAGES, null, null, array('tmpl' => 'component', 'type' => AIMAGES_TYPE_MORE))), 800, 500);
$bar->appendButton('Popup', 'new', 'UPLOAD', ARoute::safeURL(ARoute::view(VIEW_IMAGES, null, null, array('layout'=>'upload','tmpl' => 'component', 'type' => AIMAGES_TYPE_MORE))), 800, 500);
$bar->appendButton('ALink', 'delete', 'DELETE', 'AImages.removeGallery()', 'imagesGalleryRemove');
$bar->appendButton('ALink', (ISJ3 ? 'star' : 'default'), 'PRIMARY', 'javascript:AImages.setDefault()', 'imagesGalleryDefault');
$bar->appendButton('ALink', 'publish', 'CHECK_ALL', 'javascript:AImages.checkAll(this, false, true)', 'imagesGalleryCheckAll');
$bar->appendButton('ALink', 'unpublish', 'UNCHECK_ALL', 'javascript:AImages.checkAll(this, false, false)', 'imagesGalleryUnCheckAll');

ADocument::addDomreadyEvent('AImages.updateGalleryToolbar(false)');

echo $bar->render();
?>
<div class="clr"></div>
<div id="images">
	<?php 
		foreach (BookingHelper::getSubjectImages($this->subject) as $image) {
			if (($thumb = AImage::thumb(BookingHelper::getIPath($image), null, ADMIN_SET_IMAGES_WIDTH))) {
	?>
				<div class="image_drag">
					<img src="<?php echo $thumb; ?>" class="thumb pointer<?php if ($this->subject->image == $image) { ?> thumbDefault<?php } ?>" alt="" id="imageGallerySource<?php echo ($id = AImage::getId($image)); ?>" onmouseup="AImages.mark(<?php echo $id; ?>,false)" />
					<input type="hidden" name="images[]" value="<?php echo $this->escape($image); ?>" id="imageGalleryHidden<?php echo $id; ?>" />
				</div>
	<?php		
			}
		}
	?>
</div>
<input type="hidden" name="image" id="image" value="<?php echo $this->subject->image; ?>" />
<script type="text/javascript">
	// <![CDATA[
		var sortables;
		window.addEvent("domready", function() {
			sortables = new Sortables(document.id("images"));
		});
	// ]]>
</script>