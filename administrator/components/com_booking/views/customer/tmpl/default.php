<?php

/**
 * Customer detail form template.
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

/* @var $this BookingViewCustomer */

AHtml::title('Customer', 'user');

if (JFactory::getUser()->authorise('booking.edit.customer', 'com_booking'))
	JToolBarHelper::custom('Edit', 'edit', 'edit', 'JACTION_EDIT', false);

$bar = &JToolBar::getInstance('toolbar');
$bar->appendButton('ALink', 'forward', 'Reservations', 'ViewCustomer.displayReservations()');

JToolBarHelper::cancel();

$config = &AFactory::getConfig();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col width-50">
		<fieldset class="adminform">
    		<legend><?php echo JText::_('DETAILS'); ?></legend>
    		<table class="admintable">
    			<tr>
    				<td class="key"><label><?php echo JText::_('NAME'); ?>:</label></td>
    				<td><?php echo BookingHelper::formatName($this->customer); ?></td>
    			</tr>
    			<tr>
    				<td class="key"><label><?php echo JText::_('COMPANY'); ?>:</label></td>
    				<td><?php echo $this->customer->company; ?></td>
    			</tr>
	    		<tr>
	    			<td class="key"><label><?php echo JText::_('STATE'); ?>:</label></td>
	        		<td>
	        			<?php
	        				switch ($this->customer->state) {
	        					case CUSTOMER_STATE_ACTIVE:
	        						echo JText::_('ACTIVE');
	        						break;
	        					case CUSTOMER_STATE_DELETED:
	        						echo JText::_('TRASHED');
	        						break;
	        				}
	        			?>
	        		</td>
	        	</tr>
	        	<?php if(is_array($config->rsExtra)){foreach ($config->rsExtra as $field) { ?>		 
		    		<tr>
		    			<td class="key"><label><?php echo $field['title']; ?>:</label></td>
		   				<td><?php echo AUtils::getArrayValue($this->customer->fields, $field['name'] . '.value'); ?></td>
		   			</tr>
		   		<?php }} ?>
    		</table>
    	</fieldset>
    	<fieldset class="adminform">
    		<legend><?php echo JText::_('CONTACT'); ?></legend>
    		<table class="admintable">
    			<tr>	
    				<td class="key"><label><?php echo JText::_('ADDRESS'); ?>:</label></td>
    				<td><?php echo BookingHelper::formatAddress($this->customer); ?></td>
    			</tr>
    			<tr>	
    				<td class="key"><label><?php echo JText::_('PHONE'); ?>:</label></td>
    				<td><?php echo $this->customer->telephone; ?></td>
    			</tr>
    			<tr>	
    				<td class="key"><label><?php echo JText::_('FAX'); ?>:</label></td>
    				<td><?php echo $this->customer->fax; ?></td>
    			</tr>
    		</table>
    	</fieldset>
    	<div class="clr">&nbsp;</div>
   	</div>
    	<?php if ($this->customer->id) { ?>
    		<div class="col width-50">
        		<fieldset class="adminform">
        			<legend><?php echo JText::_('SYSTEM_DATA'); ?></legend>
        			<table class="admintable">
        				<?php if ($this->user->id) { ?>
        					<tr>
        						<td class="key"><label><?php echo JText::_('USER'); ?>:</label></td>
        						<td>
        							<a href="<?php echo ARoute::editUser($this->user->id); ?>" title="<?php echo JText::_('EDIT_CUSTOMER_USER_ACOUNT'); ?>"><?php echo $this->user->username; ?></a>
    							</td>
    						</tr>
	    					<tr>
	        					<td class="key"><label><?php echo JText::_('BLOCK'); ?>:</label></td>
	        					<td>
	        						<?php
	        							switch ($this->user->block) {
	        								case CUSTOMER_USER_STATE_BLOCK:
	        									echo JText::_('JYES');
	        									break;
	        								case CUSTOMER_USER_STATE_ENABLED:
	        									echo JText::_('JNO');
	        									break;
	        							} 
	        						?>
	        					</td>
	        				</tr>		
	        				<tr>
	        					<td class="key"><label><?php echo JText::_('USER_TYPE'); ?>:</label></td>
	        					<td><?php echo ISJ16 ? $this->customer->usertype : JText::_($this->user->usertype); ?></td>
	        				</tr>	
        					<tr>	
        						<td class="key"><label><?php echo JText::_('EMAIL'); ?>:</label></td>
        						<td><?php echo BookingHelper::getEmailLink($this->user); ?></td>
        					</tr>
        					<tr>
        						<td class="key"><label><?php echo JText::_('REGISTER_DATE'); ?>:</label></td>
        						<td><?php echo AHtml::date($this->user->registerDate, ADATE_FORMAT_LONG); ?></td>
        					</tr>	
        					<tr>
        						<td class="key"><label><?php echo JText::_('LAST_VISIT_DATE'); ?>:</label></td>
        						<td><?php echo AHtml::date($this->user->lastvisitDate, ADATE_FORMAT_LONG); ?></td>
        					</tr>
        				<?php } else { ?>
        					<tr>
        						<td class="key"><label><?php echo JText::_('USER'); ?>:</label></td>
        						<td><?php echo JText::_('NOT_FOUND'); ?></td>
        					</tr>
        				<?php } ?>
        			</table>
        		</fieldset>
        		<div class="clr">&nbsp;</div>
        	</div>
        <?php } ?>
    <div class="clr">&nbsp;</div>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_CUSTOMER; ?>"/>
	<input type="hidden" name="boxchecked" value="1"/>
	<input type="hidden" name="cid[]" value="<?php echo $this->customer->id; ?>"/>
	<input type="hidden" name="task" value=""/>
	<!-- Use for display customers reservations -->
	<input type="hidden" name="filter_reservation-surname" value="<?php echo $this->customer->surname; ?>"/>
	<?php echo JHTML::_('form.token'); ?>
</form>