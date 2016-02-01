<?php 

/**
 * Images upload and select browse window.
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
	
$action = ARequest::getUserStateFromRequest('action', UPLOAD_IMAGE_CLOSE_SET, 'int');

$type = ARequest::getUserStateFromRequest('type', AIMAGES_TYPE_ONE, 'int');

$mainframe = &JFactory::getApplication();
/* @var $mainframe JApplication */

if ($type == AIMAGES_TYPE_MORE)
	ADocument::addDomreadyEvent('AImages.init();');

ADocument::addScriptPropertyDeclaration('selectImage', JText::_('Select image', true));
	
$bar = &JToolBar::getInstance('toolbar_images_default');
/* @var $bar JToolBar */
$bar->appendButton('ALink', 'upload', 'UPLOAD', 'AImages.upload()','uploadbutton');
$bar->appendButton('ALink', 'new', 'CREATE_DIRECTORY', 'AImages.mkdir()','createdirbutton');
$bar->appendButton('ALink', 'delete', 'DELETE', 'AImages.remove()');

?>
<div id="imageBrowse">
	<form method="post" action="index.php" enctype="multipart/form-data" name="adminForm" id="adminForm">
		<fieldset>
			<legend><?php echo JText::_('Tools'); ?></legend>
			<div class="leftToolbar">
				<table>
					<tr>
						<td>
							<label for="image"><?php echo JText::_('Upload'); ?></label>
						</td>
						<td>
							<input type="file" name="image" id="image" accept="image/jpeg,image/png,image/pjpeg,image/gif" />
						</td>
					</tr>
					<tr>
						<td>
							<label for="dirname"><?php echo JText::_('NEW_DIRECTORY'); ?></label>
						</td>
						<td class="imagesFilter">
							<input type="text" name="dirname" id="dirname" value="" />
						</td>
					</tr>
				</table>
			</div>
			<div class="rigthToolbar"><?php echo $bar->render(); ?></div>
		</fieldset>
				<a href="javascript:AImages.changeDir('')" title=""><?php echo JText::_('ROOT'); ?></a>
		
		<?php	
			$beforeParts = array();
			foreach (explode(DS, $this->dir) as $part) {
				if (($part = JString::trim($part))) {
					$beforeParts[] = $part;
		?>
				
					/ <a href="javascript:AImages.changeDir('<?php echo $this->escape(JPath::clean(implode(DS, $beforeParts))); ?>')" title=""><?php echo $part; ?></a>
		<?php
				}
			}	
			
			if ($this->totalImage || $this->totalDir) { 
		?>
			
			<fieldset id="images">
				<legend><?php echo JText::_('AVAILABLE_IMAGES'); ?></legend>
				<?php
				
					$filter = new stdClass();
					
					$filter->limit = ARequest::getUserStateFromRequest('limit', 10, 'int');
					$filter->limitstart = ARequest::getUserStateFromRequest('limitstart', 0, 'int');
					$filter->total = $this->totalImage + $this->totalDir;

					AModel::checkBrowseFilter($filter);					
					$pagination = new JPagination($filter->total, $filter->limitstart, $filter->limit);
					
					for ($i = $filter->limitstart; $i < $filter->count; $i++) {
						if (isset($this->dirs[$i])) {
							$filter->limitstart ++;
							$dir = $this->dirs[$i];
							$id = AImage::getId($this->dir . DS . $dir);
				?>
								<span style="width: 85px; height: 85px; display: inline-block; position:relative;">
									<span class="aIcon aIconUnpublish aIconPointer imagedelete" style="display: none;" title="<?php echo $this->escape(JText::_('DELETE_FOLDER')); ?>">&nbsp;</span>
									<a class="dir" href="javascript:AImages.changeDir('<?php echo $this->escape(JPath::clean($this->dir . DS . $dir)); ?>')" title=""><?php echo $dir; ?></a>
									<input type="hidden" name="folders[]" id="imageFolderHidden<?php echo $id; ?>" value="<?php echo $dir; ?>" />
								</span>
				<?php
						}
					}
					$count = $filter->count - $this->totalDir;
					for ($i = ($filter->limitstart - $this->totalDir); $i < $count; $i++) {
						$image = $this->images[$i];
						
						$ipath = BookingHelper::getIPath();
						$ipath = JPath::clean($ipath . DS . $this->dir);
						
						$thumb = AImage::thumb(JPath::clean($ipath . DS . $image), null, ADMIN_SET_IMAGES_WIDTH);
						$id = AImage::getId($this->dir . DS . $image);
						$image = $this->escape($image);
						if ($thumb) { 
				?>
							<img src="<?php echo $thumb; ?>" alt="" title="<?php echo $image; ?>" class="thumb pointer" id="imageBrowserSource<?php echo $id; ?>" onclick="AImages.mark(<?php echo $id; ?>,true)" />
							<input type="hidden" name="images[]" id="imageBrowserHidden<?php echo $id; ?>" value="<?php echo JPath::clean($this->dir . DS . $image); ?>" />
				<?php 
						}
					} 
				?>
				<div class="listing">
	    			<?php echo $pagination->getListFooter().(ISJ3 ? $pagination->getLimitBox() : ''); ?>
	    			<div class="clr"></div>
	    		</div>
			</fieldset>
		<?php } ?>
		<input type="hidden" name="folder" value="" />
		<input type="hidden" name="option" value="<?php echo OPTION; ?>" />
		<input type="hidden" name="view" value="images" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="layout" value="upload" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="dir" value="<?php echo $this->escape($this->dir); ?>" />
	</form>
</div>
<script type="text/javascript">
	// <![CDATA[
	window.addEvent('domready', function() {
		// show delete folder icon
		$$('.dir').getParent().addEvent('mouseover', function(event) {
			this.getElement('.imagedelete').setStyles({
				'display':'inline-block',
				'position': 'absolute',
				'left': '20px'
			}).addEvent('click', function() {
				AImages.removefolder(this);
			});
		});

		// hide delete folder icon
		$$('.dir').getParent().addEvent('mouseout', function(event) {
			this.getElement('.imagedelete').hide();
		});

		// hide/show upload button 
		document.id('uploadbutton').setStyle('display', 'none');
		document.id('image').addEvent('change',function() {
			if (this.get('value') != '')
				document.id('uploadbutton').setStyle('display', 'inline');
		});

		// hide/show new dir button 
		document.id('createdirbutton').setStyle('display', 'none');
		document.id('dirname').addEvent('keyup', function() {
			if (this.get('value') != '')
				document.id('createdirbutton').setStyle('display', 'inline');
		});
	});
	// ]]>
</script>