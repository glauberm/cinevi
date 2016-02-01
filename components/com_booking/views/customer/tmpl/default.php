<?php

/**
 * Customer detail form template.
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

/* @var $this BookingViewReservation */

$config = AFactory::getConfig();

?>
<h1><?php echo JText::_('CUSTOMER_PROFILE'); ?></h1>
<?php
if (isset($this->subject)) {
?>
	<div class="bookingToolbar">
		<a class="aIconToolBack tool back" href="<?php echo JRoute::_(ARoute::view(VIEW_SUBJECT, $this->subject->id, $this->subject->alias)); ?>" title="<?php echo $this->escape(JText::sprintf('BACK_TO_OBJECT', $this->subject->title)); ?>"><?php echo $this->escape(JText::sprintf('BACK_TO_OBJECT', $this->subject->title)); ?></a>
		<div class="clr">&nbsp;</div>
	</div>
<?php	
}
	$name = BookingHelper::formatName($this->customer);
	$company = JString::trim($this->customer->company);
	if ($name || $company) {
?>
		<div class="profile">
			<?php if (ISJ3) { ?>
				<ul class="btn-toolbar pull-right">
					<li class="btn-group">
						<a class="btn" href="<?php echo JRoute::_(ARoute::edit(CONTROLLER_CUSTOMER)); ?>">
						<span class="icon-user"></span> <?php echo JText::_('EDIT_PROFILE'); ?></a>
					</li>
				</ul>
			<?php } ?>
			<fieldset id="users-profile-core">
		    	<legend><?php echo JText::_('DETAILS'); ?></legend>
		    	<dl class="dl-horizontal">
		    		<?php 
                        if (is_array($config->rsExtra) && $config->fieldsPosition == 0) { 
						    foreach ($config->rsExtra as $field) {   
					?>		 
		    					<dt><?php echo $field['title']; ?>:</dt>
		   						<dd><?php echo AUtils::getArrayValue($this->customer->fields, $field['name'] . '.value'); ?>&nbsp;</dd>
		   			<?php 
						    }
						} 
		    			if ($name) {
		    		?>
		    				<dt><?php echo JText::_('NAME'); ?>:</dt>
		    				<dd><?php echo $name; ?></dd>
		    		<?php 
		    			}
		    			if ($company) {
		    		?>
		    				<dt><?php echo JText::_('COMPANY'); ?>:</dt>
		    				<dd><?php echo $company; ?></dd>
		    		<?php 
		    			}
		    		?>
		    		<?php 
						if (is_array($config->rsExtra) && $config->fieldsPosition == 1) { 
						    foreach ($config->rsExtra as $field) {   
					?>		 
		    					<dt><?php echo $field['title']; ?>:</dt>
		   						<dd><?php echo AUtils::getArrayValue($this->customer->fields, $field['name'] . '.value'); ?>&nbsp;</dd>
		   			<?php 
						    }
						} 
					?>
				</dl>
		    </fieldset>
<?php 
	}
	$address = BookingHelper::formatAddress($this->customer);
	$telephone = JString::trim($this->customer->telephone);
	$fax = JString::trim($this->customer->fax);
	if ($address || $telephone || $fax) {
?>
		    <fieldset id="users-profile-custom">
		    	<legend><?php echo JText::_('CONTACT'); ?></legend>
		    	<dl class="dl-horizontal">
		    		<?php 
		    			if ($address) {
		    		?>
		    				<dt><?php echo JText::_('ADDRESS'); ?>:</dt>
		    				<dd><?php echo BookingHelper::formatAddress($this->customer); ?></dd>
		    		<?php 
		    			}
		    			if ($telephone) {
		    		?>
		    				<dt><?php echo JText::_('PHONES'); ?>:</dt>
		    				<dd><?php echo $this->customer->telephone; ?></dd>
		    		<?php 
		    			}
		    			if ($fax) {
		    		?>
		    				<dt><?php echo JText::_('FAX'); ?>:</dt>
		    				<dd><?php echo $this->customer->fax; ?></dd>
		    		<?php 
		    			}
		    		?>
		    	</dl>
		    </fieldset>
<?php 
	}
?>
	<fieldset id="users-profile-custom">
        <legend><?php echo JText::_('SYSTEM_DATA'); ?></legend>
        <dl class="dl-horizontal">
        	<dt><?php echo JText::_('USER'); ?>:</dt>
        	<dd>
       			<a href="<?php echo ARoute::editUser($this->user->id); ?>" title="<?php echo JText::_('EDIT_USER_ACOUNT'); ?>"><?php echo $this->user->username; ?></a>
    		</dd>
       		<dt><?php echo JText::_('EMAIL'); ?>:</dt>
       		<dd><?php echo BookingHelper::getEmailLink($this->user); ?></dd>
       		<dt><?php echo JText::_('REGISTER_DATE'); ?>:</dt>
        	<dd><?php echo AHtml::date($this->user->registerDate, ADATE_FORMAT_LONG); ?></dd>
        	<dt><?php echo JText::_('LAST_VISIT_DATE'); ?>:</dt>
       		<dd><?php echo AHtml::date($this->user->lastvisitDate, ADATE_FORMAT_LONG); ?></dd>
        </dl>
    </fieldset>
	<?php if (!ISJ3 && JFactory::getUser()->id == $this->user->id) : ?>
		<a href="<?php echo JRoute::_(ARoute::edit(CONTROLLER_CUSTOMER)); ?>">
			<?php echo JText::_('EDIT_PROFILE'); ?></a>
	<?php endif; ?>
</div>	