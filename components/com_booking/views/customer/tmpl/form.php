<?php

/**
 * Customer edit form template.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	views
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

/* @var $this BookingViewCustomer */

$config = AFactory::getConfig();
$document = JFactory::getDocument();

if(!JFactory::getUser()->id && !$config->enableRegistration){
	JLog::add("New reservations are disabled",JLog::WARNING);
	$mainframe = JFactory::getApplication();
	$mainframe->redirect("./");
	exit;
}

?>
<h1><?php echo JText::_('CUSTOMER_PROFILE'); ?></h1>
<div class="profile-edit">
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="member-profile">
		<?php 
			if ($config->rsTitleBefore || $config->rsFirstname || $config->rsMiddlename || $config->rsSurname || $config->rsTitleAfter || $config->rsCompany) { 
		?>
				<fieldset>
		    		<legend><?php echo JText::_('DETAILS'); ?></legend>
		    		<dl class="dl-horizontal">
		    			<?php 
                            if (is_array($config->rsExtra) && $config->fieldsPosition == 0) { 
								foreach ($config->rsExtra as $field) { 
						?>		 
		    						<dt><?php echo AHtml::displayLabel($document, $field['required'] == 2, $field['name'], $field['name'], $field['title']); ?></dt>
		    						<dd><?php echo AHtml::getField($field, $this->customer->fields); ?></dd>
		   				<?php 
								}
							} 
		    				if ($config->rsTitleBefore) { 
		    			?>
		    					<dt><?php echo AHtml::displayLabel($document, $config, 'rsTitleBefore', 'title_before', 'TITLE_BEFORE'); ?></dt>
		    					<dd><input class="text_area" type="text" name="title_before" id="title_before" size="60" maxlength="255" value="<?php echo $this->customer->title_before; ?>" /></dd>
		    			<?php 
		    				}
		    				if ($config->rsFirstname) { 
		    			?>
			    				<dt><?php echo AHtml::displayLabel($document, $config, 'rsFirstname', 'firstname', 'FIRST_NAME'); ?></dt>
		    					<dd><input class="text_area" type="text" name="firstname" id="firstname" size="60" maxlength="255" value="<?php echo $this->customer->firstname; ?>" /></dd>
		    			<?php 
		    				}
		    				if ($config->rsMiddlename) {
		   				?>
			    				<dt><?php echo AHtml::displayLabel($document, $config, 'rsMiddlename', 'middlename', 'MIDDLE_NAME'); ?></dt>
		    					<dd><input class="text_area" type="text" name="middlename" id="middlename" size="60" maxlength="255" value="<?php echo $this->customer->middlename; ?>" /></dd>
		    			<?php 
		    				}
		    				if ($config->rsSurname) {
		   				?>
			    				<dt><?php echo AHtml::displayLabel($document, $config, 'rsSurname', 'surname', 'SURNAME'); ?></dt>
		    					<dd><input class="text_area" type="text" name="surname" id="surname" size="60" maxlength="255" value="<?php echo $this->customer->surname; ?>" /></dd>
		    			<?php 
		    				}
		    				if ($config->rsTitleAfter) {
		    			?>
			    				<dt><?php echo AHtml::displayLabel($document, $config, 'rsTitleAfter', 'title_after', 'TITLE_AFTER'); ?></dt>
		    					<dd><input class="text_area" type="text" name="title_after" id="title_after" size="60" maxlength="255" value="<?php echo $this->customer->title_after; ?>" /></dd>
		    			<?php 
		    				}
		    				if ($config->rsCompany) {
		   				?>
			    				<dt><?php echo AHtml::displayLabel($document, $config, 'rsCompany', 'company', 'COMPANY'); ?></dt>
		    					<dd><input class="text_area" type="text" name="company" id="company" size="60" maxlength="255" value="<?php echo $this->customer->company; ?>" /></dd>
		    			<?php 
		    				}
		   					if (is_array($config->rsExtra) && $config->fieldsPosition == 1) { 
								foreach ($config->rsExtra as $field) { 
						?>		 
		    						<dt><?php echo AHtml::displayLabel($document, $field['required'] == 2, $field['name'], $field['name'], $field['title']); ?></dt>
		    						<dd><?php echo AHtml::getField($field, $this->customer->fields); ?></dd>
		   				<?php 
								}
							} 
						?>
		    		</fieldset>
    	<?php
			} 
    		if ($this->customer->id) { 
    	?>
	        	<fieldset>
	        		<legend><?php echo JText::_('SYSTEM_DATA'); ?></legend>
	        		<dl class="dl-horizontal">
	        			<?php if ($this->user->id) { ?>
	        				<dt><?php echo JText::_('USER'); ?>:</dt>
	        				<dd>
	    						<a href="<?php echo ARoute::editUser($this->user->id); ?>" title=""><?php echo $this->user->username; ?></a>
		    				</dd>
		        			<dt><label for="email" class="required"><?php echo JText::_('EMAIL'); ?>: <span class="star"> *</span></label></dt>
	    	   				<dd><input class="text_area" type="text" name="email" id="email" size="60" maxlength="255" value="<?php echo $this->user->email; ?>" /></dd>
	    					<dt><?php echo JText::_('REGISTER_DATE'); ?>:</dt>
	       					<dd><?php echo AHtml::date($this->user->registerDate, ADATE_FORMAT_LONG); ?></dd>
	       					<dt><?php echo JText::_('LAST_VISIT_DATE'); ?>:</dt>
	    					<dd><?php echo AHtml::date($this->user->lastvisitDate, ADATE_FORMAT_LONG); ?></dd>
	        			<?php } else { ?>
	        				<dt><?php echo JText::_('USER'); ?>:</dt>
	        				<dd><?php echo JText::_('NOT_FOUND'); ?></dd>
	       				<?php } ?>
	       			</dl>
	        	</fieldset>
		<?php 
    		} else { 
		?>
        		<fieldset>
        			<legend><?php echo JText::_('USER_ACOUNT'); ?></legend>
        			<dl class="dl-horizontal">
	        			<?php 
	        				if (! JFactory::getUser()->id) {
	        			?>
			        			<dt><label for="username" class="required"><?php echo JText::_('USERNAME'); ?>: <span class="star">*</span></label></dt>
				        		<dd><input type="text" name="username" id="username" size="60" maxlength="255" value="<?php echo $this->user->username; ?>" class="text_area" /></dd>
				        		<dt><label for="email" class="required"><?php echo JText::_('EMAIL'); ?>: <span class="star">*</span></label></dt>
				    			<dd><input type="text" name="email" id="email" size="60" maxlength="255" value="<?php echo $this->user->email; ?>" class="text_area" /></dd>
				        		<dt><label for="password" class="required"><?php echo JText::_('NEW_PASSWORD'); ?>: <span class="star">*</span></label></dt>
			    	    		<dd><input type="password" name="password" id="password" size="40" value="<?php echo JRequest::getString('password'); ?>" class="text_area" autocomplete="off"/></dd>
			        			<dt><label for="password2" class="required"><?php echo JText::_('VERIFY_PASSWORD'); ?>: <span class="star">*</span></label></dt>
			        			<dd><input type="password" name="password2" id="password2" size="40" value="<?php echo JRequest::getString('password2'); ?>" class="text_area" autocomplete="off"/></dd>
	    				<?php 
							} else { 
						?>
				        		<dt><label><?php echo JText::_('NAME'); ?>: </label></dt>
				        		<dd><?php echo $user->name; ?></dd>
				        		<dt><label><?php echo JText::_('USERNAME'); ?>: </label></dt>
				        		<dd><?php echo $user->username; ?></dd>
			    	   			<dt><label><?php echo JText::_('EMAIL'); ?>: </label></dt>
			       				<dd><?php echo $user->email; ?></dd>
		    			<?php 
							} 
						?>
    				</dl>
    			</fieldset>
    	<?php 
			} 
 			if ($config->rsStreet || $config->rsCity || $config->rsZip || $config->rsCountry || $config->rsTelephone || $config->rsFax) { 
		?>
		    	<fieldset>
		    		<legend><?php echo JText::_('CONTACT'); ?></legend>
		    		<dl class="dl-horizontal">
		    			<?php 
		    				if ($config->rsStreet) { 
		    			?>
					    		<dt><?php echo AHtml::displayLabel($document, $config, 'rsStreet', 'street', 'STREET'); ?></dt>
		    					<dd><input class="text_area" type="text" name="street" id="street" size="60" maxlength="255" value="<?php echo $this->customer->street; ?>" /></dd>
		    			<?php 
		    				}
		    				if ($config->rsCity) {
			    		?>
								<dt><?php echo AHtml::displayLabel($document, $config, 'rsCity', 'city', 'CITY'); ?></dt>
			    				<dd><input class="text_area" type="text" name="city" id="city" size="60" maxlength="255" value="<?php echo $this->customer->city; ?>" /></dd>
		    			<?php 
		    				}
		    				if ($config->rsZip) {
			    		?>
								<dt><?php echo AHtml::displayLabel($document, $config, 'rsZip', 'zip', 'ZIP'); ?></dt>
			    				<dd><input class="text_area" type="text" name="zip" id="zip" size="60" maxlength="255" value="<?php echo $this->customer->zip; ?>" /></dd>
		    			<?php 
		    				}
		    				if ($config->rsCountry) {
			    		?>
						    	<dt><?php echo AHtml::displayLabel($document, $config, 'rsCountry', 'country', 'COUNTRY'); ?></dt>
			    				<dd><input class="text_area" type="text" name="country" id="country" size="60" maxlength="255" value="<?php echo $this->customer->country; ?>" /></dd>
			    		<?php 
			    			}
			    			if ($config->rsTelephone) {
		    			?>
								<dt><?php echo AHtml::displayLabel($document, $config, 'rsTelephone', 'telephone', 'TELEPHONE'); ?></dt>
		    					<dd><input class="text_area" type="text" name="telephone" id="telephone" size="60" maxlength="255" value="<?php echo $this->customer->telephone; ?>" /></dd>
			    		<?php 
			    			}
			    			if ($config->rsFax) {
		    			?>
		    					<dt><?php echo AHtml::displayLabel($document, $config, 'rsFax', 'fax', 'FAX'); ?></dt>
		    					<dd><input class="text_area" type="text" name="fax" id="fax" size="60" maxlength="255" value="<?php echo $this->customer->fax; ?>" /></dd>
			    		<?php 
			    			}
			    		?>
		    		</dl>
			    </fieldset>
   		<?php 
 			}
    	?>
		<div>
            <?php if (($captcha = BookingHelper::showCaptcha())) { ?>
                <fieldset>
                    <legend><?php echo JText::_('CAPTCHA'); ?></legend>
                    <dl class="dl-horizontal">
                        <dt>&nbsp;</dt>
                        <dd><?php echo $captcha; ?></dd>
                    </dl>
                </fieldset>
            <?php } ?>
			<button type="submit" class="btn validate" onclick="return ViewCustomerSubmit.submitbutton('save')">
				<span>
					<?php echo JText::_('JSUBMIT'); ?>
				</span>
			</button>
			<?php if (!JRequest::getInt('hideCancelButton')) {
                    echo JText::_('OR');
                    if ($this->customer->id)
                        $backLink = JRoute::_(ARoute::view(VIEW_CUSTOMER));
                    elseif (isset($this->subject))
                        $backLink = JRoute::_(ARoute::view(VIEW_SUBJECT, $this->subject->id, $this->subject->alias));
                    else
                        $backLink = 'javascript:history.go(-1)'; ?>
                    <a href="<?php echo $backLink; ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>
            <?php } ?>
		</div>
		<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
		<input type="hidden" name="controller" value="<?php echo CONTROLLER_CUSTOMER; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="return" value="<?php echo $this->escape(JRequest::getString('return')); ?>"/>
		<?php 
			if (isset($this->subject)) { 
		?>
				<input type="hidden" name="startSubjectId" value="<?php echo $this->subject->id; ?>"/>
		<?php 
			}
			echo JHTML::_('form.token'); 
		?>
	</form>
</div>
