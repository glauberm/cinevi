<?php

/**
 * Select template dialog 
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

/* @var $this BookingViewSelectTemplate */
$templatesCount = count($this->templates);

ADocument::addScriptPropertyDeclaration('templatesCount', $templatesCount);

JToolBarHelper::title(JText::_('NEW_ITEM'), 'module');

$bar = &JToolBar::getInstance();
$bar->appendButton('ALink', 'new', 'USE_EXIST_TEMPLATE', 'SelectTemplate.select()');
$bar->appendButton('ALink', 'default', 'NEW_TEMPLATE', 'SelectTemplate.newTmp()');
$bar->appendButton('ALink', 'cancel', 'CANCEL', 'SelectTemplate.cancel()');
$bar->render();

echo AHtml::renderToolbarBox();
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php 
		$mainframe = &JFactory::getApplication();
		/* @var $mainframe JApplication */
		if (! $templatesCount) {
			$mainframe->enqueueMessage('<b class="info">' . JText::_('NO_TEMPLATES_FOUND_YOU_MUST_CREATE_NEW_OBJECT_WITH_NEW_TEMPLATE_CLICK_ON_BUTTON_NEW_TEMPLATE') . '</b>');
		} else { 
			$mainframe->enqueueMessage('<b class="info">' . JText::_('SELECT_EXISTING_TEMPLATE_AND_CLICK_ON_BUTTON_USE_EXIST_TEMPLATE_OR_CLICK_ON_BUTTON_NEW_TEMPLATE') . '</b>');
	?>
		<fieldset class="adminList">
			<legend><?php echo JText::_('EXIST_TEMPLATES'); ?></legend>
			<table>
				<?php 
					for ($i = 0; $i < $templatesCount; $i++) {
						$template = $this->templates[$i];
						/* @var $template ATemplate */
						$id = 'template' . $i; 
				?>
					<tr>
						<td>
							<input type="radio" class="inputRadio" id="<?php echo $id; ?>" name="template" value="<?php echo $template->id; ?>" />
						</td>
						<td>
							<label for="<?php echo $id; ?>"><?php echo $template->name; ?></label>							
						</td>
					</tr>
				<?php } ?>
			</table>
		</fieldset>
	<?php } ?>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>" />	
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_SUBJECT; ?>" />
	<input type="hidden" name="task" value="add" />
	<input type="hidden" name="templatesCount" value="<?php echo $templatesCount; ?>" />
</form>