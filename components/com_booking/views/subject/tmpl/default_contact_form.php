<?php

/**
 * contact form form template.
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

$config = AFactory::getConfig();
$document = &JFactory::getDocument();

?>
			
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm" class="reservation">
<div class="customer">
			<fieldset>
	    		<legend><?php echo JText::_('CONTACT_FORM'); ?></legend>
	    		<table>

			    			<tr>
			    				<td class="key"><?php echo JText::_('CONTACT_FORM_EMAIL'); ?></td>
			    				<td><input class="text_area" type="text" name="email" id="email" size="30" maxlength="100" value="<?php if(JFactory::getUser()->id) echo JFactory::getUser()->email;?>" /></td>
			    			</tr>
			    			<tr>
			    				<td class="key"><?php echo JText::_('CONTACT_FORM_NAME'); ?></td>
			    				<td><input class="text_area" type="text" name="name" id="name" size="30" maxlength="100" value="" /></td>
			    			</tr>
			    			<tr>
			    				<td class="key"><?php echo JText::_('CONTACT_FORM_FROM'); ?></td>
			    				<td><?php echo AHtml::getCalendar('', 'date_from', 'datefrom', ADATE_FORMAT_NORMAL, ADATE_FORMAT_NORMAL_CAL, '', false); ?></td>
			    			</tr>
			    			<tr>
			    				<td class="key"><?php echo JText::_('CONTACT_FORM_TO'); ?></td>
			    				<td><?php echo AHtml::getCalendar('', 'date_to', 'dateto', ADATE_FORMAT_NORMAL, ADATE_FORMAT_NORMAL_CAL, '', false); ?></td>
			    			</tr>
			    			<tr>
			    				<td class="key"><?php echo JText::_('CONTACT_FORM_MESSAGE'); ?></td>
			    				<td><textarea class="text_area" type="text" name="message" id="message" rows="4" cols="50" ></textarea></td>
			    			</tr>
			    			<tr>
			    				<td class="key"></td>
			    				<td><input class="text_area" type="submit" name="send" id="send" value="<?php echo JText::_('CONTACT_FORM_SEND'); ?>" /></td>
			    			</tr>
	    			
	    		</table>
	    	</fieldset>
	    	<div class="clr">&nbsp;</div>
	    </div>


	<input type="hidden" name="controller" value="subject" />
	<input type="hidden" name="task" value="sendContactForm" />
	<input type="hidden" name="view" value="" />
	<input type="hidden" name="layout" value="default" />
	<input type="hidden" name="id" value="<?php echo $this->subject->id; ?>" />
</form> 