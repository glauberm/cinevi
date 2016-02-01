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
<form action="<?php echo JRoute::_('index.php?option=com_booking&id=' . (int) $this->item->id); ?>" method="post" id="adminForm" name="adminForm" class="form-validate">
	<div>
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('DETAILS'); ?>
			</legend>
			<div class="span10 form-horizontal">
				<ul class="adminformlist nav">
					<li><?php echo $this->form->getLabel('title'); ?> <?php echo $this->form->getInput('title'); ?></li>
					<li><?php echo $this->form->getLabel('pick_up'); ?> <?php echo $this->form->getInput('pick_up'); ?></li>
					<li><?php echo $this->form->getLabel('drop_off'); ?> <?php echo $this->form->getInput('drop_off'); ?></li>
				</ul>
			</div>
		</fieldset>
	</div>
	<div>
		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="return" value="<?php echo JRequest::getCmd('return');?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>