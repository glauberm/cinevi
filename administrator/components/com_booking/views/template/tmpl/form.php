<?php

/**
 * Template edit form template
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

/* @var $this BookingViewTemplate */

AHtml::title('Template', 'template');
JToolBarHelper::save();
JToolBarHelper::apply();
if ($this->template->id) {
	JToolBarHelper::custom('copy','copy.png','copy_f2.png','Copy');
}
JToolBarHelper::cancel();

JHTML::_('behavior.modal');

?>
<form name="adminForm" id="adminForm" method="post" action="index.php">
	<div class="width-100">	
		<fieldset class="adminform">
	    	<legend><?php echo JText::_('DETAILS'); ?></legend>
	    	<table class="admintable">
    			<tr>
    				<td class="key"><label for="title" class="compulsory"><?php echo JText::_('TEMPLATE_NAME'); ?>:</label></td>
    				<td><input class="text_area" type="text" name="name" id="name" size="60" maxlength="255" value="<?php echo $this->template->name; ?>" /></td>
    			</tr>
    		</table>
    	</fieldset>
	</div>    	
    <?php include(JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'template' . DS . 'tmpl' . DS . 'form_calendars.php'); ?>
    <div class="width-100">
    	<fieldset class="adminform">
	    	<legend><?php echo JText::_('PROPERTIES'); ?></legend>
	    	<?php echo $this->properties->toolbar (); ?>
	    	<div class="clr"></div>
			<?php echo $this->properties->render (); ?>					
		</fieldset>
	</div>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_TEMPLATE; ?>"/>
	<input type="hidden" name="boxchecked" value="1"/>
	<input type="hidden" name="cid[]" value="<?php echo $this->template->id; ?>" id="cid"/>
	<input type="hidden" name="task" value=""/>
	<?php $task = JRequest::getCmd('task'); ?> 
    <?php if ($task == 'copy') { ?>
		<input type="hidden" name="copy" value="1"/>            
    <?php } ?>
	<?php echo JHTML::_('form.token'); ?>
</form>