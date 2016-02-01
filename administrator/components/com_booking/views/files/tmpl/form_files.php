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

$bar = &JToolBar::getInstance('toolbar_files');
/* @var $bar JToolBar */
AImporter::helper('toolbar'.DS.'alink');

$bar->appendButton('Popup', 'new', 'Add', ARoute::safeURL(ARoute::view(VIEW_FILES, null, null, array('tmpl' => 'component', 'type' => AFILES_TYPE_MORE))), 800, 500);
$bar->appendButton('ALink', 'delete', 'Delete', 'AFiles.removeGallery()', 'filesGalleryRemove');

ADocument::addDomreadyEvent('AFiles.updateGalleryToolbar(false)');

echo $bar->render();
?>
<div id="filesGalleryCheckAll">
	<input type="checkbox" name="checkAllFilesGallery" id="checkAllFilesGallery" class="inputCheckbox" value="1" onclick="AFiles.checkAll(this, false)" />
	<label for="checkAllFilesGallery"><?php echo JText::_('CHECK_ALL'); ?></label>
	<div class="clr"></div>			
</div>
<div class="clr"></div>
<div id="files">
	<?php 

		foreach (BookingHelper::getSubjectFiles($this->subject) as $file) {
			
			$id = AFile::getId($file->origname);
			
	?>
		<div class="file pointer" id="fileGallerySource<?php echo $id; ?>" onclick="AFiles.mark(<?php echo $id; ?>,false)">
			<img src="<?php echo BookingHelper::getFileThumbnail($file->origname); ?>" title="<?php echo $file->origname?>"  />
			<span class="filename"><?php echo $file->origname?></span>
			<label><input type="checkbox" onchange="AFiles.updateFileParams('<?php echo $id?>',1);" id="fileGalleryShow<?php echo $id?>" <?php if ($file->show) echo 'checked' ?>> <?php echo JText::_('DISPLAY_ON_FRONTEND')?></label>
			<label><input type="checkbox" onchange="AFiles.updateFileParams('<?php echo $id?>',2);" id="fileGallerySend<?php echo $id?>" <?php if ($file->send) echo 'checked' ?>> <?php echo JText::_('SEND_WITH_RESERVATION')?></label>
		</div>
		<input type="hidden" name="files[]" value="<?php echo $this->escape($file->string); ?>" id="fileGalleryHidden<?php echo $id; ?>" />

	<?php		
		}
	?>	
	
</div>