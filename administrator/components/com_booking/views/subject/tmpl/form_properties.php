<?php

/**
 * Subject-properties edit form template
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


?>
<div class="width-100">
	<fieldset class="adminform">
    	<legend class="hasTip" title="<?php echo $this->escape(JText::_('PROPERTIES')) . '::' . $this->escape(JText::_('PROPERTIES_INFO')); ?>"><?php echo JText::_('PROPERTIES'); ?></legend>
    	<div class="clr"></div>
    	<table class="admintable width-100">
    			<tr>
    				<td class="key"><label><?php echo JText::_('SHOW_CALENDAR'); ?>:</label></td>
    				<td>
    					<fieldset class="radio btn-group">
    						<input type="radio" name="show_contact_form" id="show_contact_form0" value="<?php echo SUBJECT_SHOW_CONTACT_FORM; ?>" class="inputRadio" <?php if ($this->subject->show_contact_form == SUBJECT_SHOW_CONTACT_FORM) { ?> checked="checked" <?php } ?> onclick="showCalendar(false);"/>
    						<label for="show_contact_form0"><?php echo JText::_('JNO'); ?></label>  
    						<input type="radio" name="show_contact_form" id="show_contact_form1" value="<?php echo SUBJECT_SHOW_CALENDAR; ?>" class="inputRadio" <?php if ($this->subject->show_contact_form == SUBJECT_SHOW_CALENDAR) { ?> checked="checked" <?php } ?> onclick="showCalendar(true);"/>
    						<label for="show_contact_form1"><?php echo JText::_('JYES'); ?></label>
    					</fieldset>
    				</td>
    			</tr>
    			<tr>
    				<td class="key"><label><?php echo JText::_('SHOW_CUSTOMER_NAMES'); ?>:</label></td>
    				<td>
    					<fieldset class="radio btn-group">
    						<input type="radio" name="display_who_reserve" id="display_who_reserve" value="" class="inputRadio" <?php if ($this->subject->display_who_reserve === '') { ?> checked="checked" <?php } ?> />
    						<label for="display_who_reserve"><?php echo jtext::_('JGLOBAL_USE_GLOBAL'); ?></label>  
    						<input type="radio" name="display_who_reserve" id="display_who_reserve0" value="0" class="inputRadio" <?php if ($this->subject->display_who_reserve === '0') { ?> checked="checked" <?php } ?> />
    						<label for="display_who_reserve0"><?php echo JText::_('JNO'); ?></label>  
    						<input type="radio" name="display_who_reserve" id="display_who_reserve1" value="1" class="inputRadio" <?php if ($this->subject->display_who_reserve === '1') { ?> checked="checked" <?php } ?> />
    						<label for="display_who_reserve1"><?php echo JText::_('JYES'); ?></label>
    					</fieldset>
    				</td>
    			</tr>
    			<tr id="contactemail">
    				<td class="key"><label><?php echo JText::_('CONTACT_EMAIL'); ?>:</label></td>
    				<td>
    					<input type="text" name="contact_email" value="<?php echo ($this->subject->contact_email? $this->subject->contact_email : reset(AFactory::getConfig()->mailingManager)); ?>" size="60" />
    				</td>
    			</tr>
    	</table>
	</fieldset>
</div>
<?php 
$javascript = "
 		var showCalendar = function(show){
 		document.id('prices').style.display = document.id('calendars').style.display = document.id('reservation-types').style.display = show ? '' : 'none';
 		document.id('contactemail').style.display = show ? 'none':'';
};
 		";
// Add Javascript to hide calendar and prices
JFactory::getDocument()->addScriptDeclaration($javascript);
	
if ($this->subject->show_contact_form == SUBJECT_SHOW_CONTACT_FORM)
{
	//if claendar should be hidden, hide after page is loaded and tabs are generated
	//JFactory::getDocument()->addScriptDeclaration("showCalendar(false);");
	echo "<script type=\"text/javascript\">window.addEvent('load', function() {
    			showCalendar(false);
		});</script>";
} else {
		//hide email field
		echo "<script type=\"text/javascript\">window.addEvent('load', function() {
    			showCalendar(true);
		});</script>";
}
?>
						
<?php
AImporter::tpl('template', $this->_layout, 'calendars');

/* @var $this BookingViewSubject */

?>
<div class="width-100">		
	<fieldset class="adminform">
	    <legend class="hasTip" title="<?php echo $this->escape(JText::_('PROPERTIES')) . '::' . $this->escape(JText::_('PROPERTIES_INFO')); ?>"><?php echo JText::_('PROPERTIES'); ?></legend>
	    <?php
		    AImporter::helper('user');
		    if(!AUser::onlyOwner())
		    	echo $this->properties->toolbar();
	    ?>	
	    	<div class="clr"></div>
		<?php	    	
	    	echo $this->properties->render(); 
	    ?>
	</fieldset>
</div>