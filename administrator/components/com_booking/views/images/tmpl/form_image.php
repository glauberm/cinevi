<?php 

/**
 * Main image template.
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

$bar = &JToolBar::getInstance('toolbar_image');
$bar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'toolbar');

/* @var $bar JToolBar */
$bar->appendButton('Popup', 'new', 'Add', ARoute::safeURL(ARoute::view(VIEW_IMAGES, null, null, array('tmpl' => 'component', 'type' => AIMAGES_TYPE_ONE))), 800, 400);
$bar->appendButton('ALink', 'delete', 'Delete', 'AImages.removeMain()', 'imageMainRemove');

echo $bar->render();
?>
<div class="clr"></div>
<img src="<?php echo $thumb = AImage::thumb(BookingHelper::getIPath($this->image), null, ADMIN_SET_IMAGES_WIDTH); ?>" alt="" id="imageMainSource" class="thumb<?php echo $thumb ? '' : ' blind'; ?>" />
<?php
	if (! $thumb)
		ADocument::addDomreadyEvent('AImages.hideRemoveMain()'); 
?>	
<input type="hidden" name="image" id="imageMainHidden" value="<?php echo $this->escape($this->image); ?>" />