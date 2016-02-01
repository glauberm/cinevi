<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	views
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die;

?>
<form action="<?php echo JRoute::_('index.php?option=com_booking&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div>
		<fieldset class="adminform" <?php if ($this->modal) { ?>style="width: 500px"<?php } ?>>
			<?php if ($this->modal) { ?>
				<button onclick="Joomla.submitbutton('article.save')"><?php echo JText::_('JSave'); ?></button>
				<button onclick="Joomla.submitbutton('article.apply')"><?php echo JText::_('JApply'); ?></button>
				<button onclick="Joomla.submitbutton('article.cancel')"><?php echo JText::_('JCancel'); ?></button>
			<?php } ?>
			<legend>
				<?php echo JText::_('DETAILS'); ?>
			</legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('title'); ?> <?php echo $this->form->getInput('title'); ?></li>
			</ul>
			<div class="clr"></div>
			<?php echo $this->form->getLabel('text'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('text'); ?>
			<div class="clr"></div>
		</fieldset>
	</div>
	<div>
		<?php if ($this->modal) { ?>
			<input type="hidden" name="tmpl" value="component" /> 
		<?php } ?>
		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="return" value="<?php echo JRequest::getCmd('return');?>" />
		<?php echo JHtml::_('form.token'); ?>		
	</div>
</form>
