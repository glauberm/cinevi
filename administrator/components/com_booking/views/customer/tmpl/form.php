<?php

/**
 * Customer edit form template.
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

JToolBarHelper::save();
JToolBarHelper::apply();

if ($this->customer->id) {
	JToolBarHelper::custom('detail', 'preview', 'preview', 'Detail', false);
	$bar = &JToolBar::getInstance('toolbar');
	$bar->appendButton('ALink', 'forward', 'Reservations', 'ViewCustomer.displayReservations()');
}

JToolBarHelper::cancel();

$config = &AFactory::getConfig();
	
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col width-50">
		<fieldset class="adminform">
    		<legend><?php echo JText::_('DETAILS'); ?></legend>
    		<table class="admintable">
    			<tr>
    				<td class="key"><label for="title_before"><?php echo JText::_('TITLE_BEFORE'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="title_before" id="title_before" value="<?php echo $this->customer->title_before; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="firstname" class="required"><?php echo JText::_('FIRST_NAME'); ?>: <span class="star">*</span></label></td>
    				<td><input class="text_area" type="text" name="firstname" id="firstname" value="<?php echo $this->customer->firstname; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="middlename"><?php echo JText::_('MIDDLE_NAME'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="middlename" id="middlename" value="<?php echo $this->customer->middlename; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="surname" class=""required""><?php echo JText::_('SURNAME'); ?>: <span class="star">*</span></label></td>
    				<td><input class="text_area" type="text" name="surname" id="surname" value="<?php echo $this->customer->surname; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="title_after"><?php echo JText::_('TITLE_AFTER'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="title_after" id="title_after" value="<?php echo $this->customer->title_after; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="company"><?php echo JText::_('COMPANY'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="company" id="company" value="<?php echo $this->customer->company; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label><?php echo JText::_('STATE'); ?>: </label></td>
        			<td>
        				<fieldset class="radio btn-group">
							<input type="radio" class="inputRadio" name="state" value="<?php echo CUSTOMER_STATE_ACTIVE; ?>" id="state_active" <?php if ($this->customer->state == CUSTOMER_STATE_ACTIVE) echo 'checked="checked"'; ?>/>
							<label for="state_active" style="display: inline; clear: none; min-width: 0px;"><?php echo JText::_('ACTIVE'); ?></label>
							<input type="radio" class="inputRadio" name="state" value="<?php echo CUSTOMER_STATE_DELETED; ?>" id="state_deleted" <?php if ($this->customer->state == CUSTOMER_STATE_DELETED) echo 'checked="checked"'; ?>/>
							<label for="state_deleted" style="display: inline; clear: none; min-width: 0px;"><?php echo JText::_('TRASHED'); ?></label>        						
						</fieldset>            					    
        			</td>
        		</tr>
        		<?php if(is_array($config->rsExtra)){foreach ($config->rsExtra as $field) { ?>		 
		    		<tr>
		    			<td class="key"><label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?>:</label></td>
		    			<td><?php echo AHtml::getField($field, $this->customer->fields); ?></td>
		   			</tr>
		   		<?php }} ?>
    		</table>
    	</fieldset>
    	<?php if ($this->customer->id) { ?>
        	<fieldset class="adminform">
        		<legend><?php echo JText::_('SYSTEM_DATA'); ?></legend>
        		<table class="admintable">
        			<?php if ($this->user->id) { ?>
        				<tr>
        					<td class="key"><label><?php echo JText::_('USER'); ?>: </label></td>
                            <td><?php echo $this->get('FormFieldUser'); ?></td>
    					</tr>
	    					<tr>
	        					<td class="key"><label><?php echo JText::_('BLOCK'); ?>: </label></td>
	        					<td>
	        						<fieldset class="radio btn-group">
	        							<input type="radio" class="inputRadio" name="block" value="<?php echo CUSTOMER_USER_STATE_BLOCK; ?>" id="block_yes" <?php if ($this->user->block == CUSTOMER_USER_STATE_BLOCK) echo 'checked="checked"'; ?>/>
	        							<label for="block_yes" style="display: inline; clear: none; min-width: 0px;"><?php echo JText::_('JYES'); ?></label>
	        							<input type="radio" class="inputRadio" name="block" value="<?php echo CUSTOMER_USER_STATE_ENABLED; ?>" id="block_no" <?php if ($this->user->block == CUSTOMER_USER_STATE_ENABLED) echo 'checked="checked"'; ?>/>
	       								<label for="block_no" style="display: inline; clear: none; min-width: 0px;"><?php echo JText::_('JNO'); ?></label>
	       							</fieldset>
	       						</td>
	       					</tr>		
	       					<tr>
	       						<td class="key"><label><?php echo JText::_('USER_TYPE'); ?>: </label></td>
	       						<td><label><?php echo ISJ16 ? $this->customer->usertype : JText::_($this->user->usertype); ?></label></td>
	       					</tr>	
       					<tr>	
       						<td class="key"><label for="email" class="required"><?php echo JText::_('EMAIL'); ?>: <span class="star">*</span></label></td>
       						<td><input class="text_area" type="text" name="email" id="email" value="<?php echo $this->user->email; ?>" /></td>
       					</tr>
        				<tr>
        					<td class="key"><label><?php echo JText::_('REGISTER_DATE'); ?>: </label></td>
        					<td><label><?php echo AHtml::date($this->user->registerDate, ADATE_FORMAT_LONG); ?></label></td>
        				</tr>	
        				<tr>
       						<td class="key"><label><?php echo JText::_('LAST_VISIT_DATE'); ?>: </label></td>
       						<td><label><?php echo AHtml::date($this->user->lastvisitDate, ADATE_FORMAT_LONG); ?></label></td>
       					</tr>
       				<?php } else { ?>
        				<tr>
        					<td class="key"><label><?php echo JText::_('USER'); ?>: </label></td>
        					<td><?php echo $this->get('FormFieldUser'); ?></td>
       					</tr>
       				<?php } ?>
        		</table>
        	</fieldset>
        <?php } else { ?>
        	<fieldset class="adminform">
        		<legend><?php echo JText::_('USER_ACOUNT'); ?></legend>
        		<table class="admintable">
        			<tr>
        				<td class="key"></td>
        				<td>
        					<fieldset class="radio btn-group">
        						<input type="radio" name="select_user" id="select_existing_user" value="1" onclick="ViewCustomer.selectExistingUser()" autocomplete="off" />
        						<label for="select_existing_user"><?php echo JText::_('EXISTING'); ?></label>
        						<input type="radio" name="select_user" id="select_new_user" value="2" onclick="ViewCustomer.selectNewUser()" autocomplete="off" />
        						<label for="select_new_user"><?php echo JText::_('NEW'); ?></label>
        					</fieldset>
        				</td>
        			</tr>
        			<tr id="user1" style="display: none">
        				<td class="key"></td>
        				<td><?php echo $this->get('FormFieldUser'); ?></td>
    				</tr>
        			<tr id="user2" style="display: none">
        				<td class="key"><label for="username" class="compulsory"><?php echo JText::_('USERNAME'); ?>: </label></td>
        				<td><input type="text" name="username" id="username" value="<?php echo $this->user->username; ?>" class="inputbox" autocomplete="off"/></td>
    				</tr>
    				<tr id="user3" style="display: none">
        				<td class="key"><label for="email" class="compulsory"><?php echo JText::_('EMAIL'); ?>: </label></td>
        				<td><input type="text" name="email" id="email" value="<?php echo $this->user->email; ?>" class="inputbox" autocomplete="off"/></td>
    				</tr>
    				<tr id="user4" style="display: none">
        				<td class="key"><label for="password" class="compulsory"><?php echo JText::_('NEW_PASSWORD'); ?>: </label></td>
        				<td><input type="password" name="password" id="password" size="30" value="<?php echo JRequest::getString('password'); ?>" class="inputbox" autocomplete="off"/></td>
    				</tr>
    				<tr id="user5" style="display: none">
        				<td class="key"><label for="password2" class="compulsory"><?php echo JText::_('VERIFY_PASSWORD'); ?>: </label></td>
        				<td><input type="password" name="password2" id="password2" size="30" value="<?php echo JRequest::getString('password2'); ?>" class="inputbox" autocomplete="off"/></td>
    				</tr>
    			</table>
    		</fieldset>
        <?php } ?>
        <div class="clr"></div>
    </div>
    <div class="col width-50">	
    	<fieldset class="adminform">
    		<legend><?php echo JText::_('CONTACT'); ?></legend>
    		<table class="admintable">
    			<tr>	
    				<td class="key"><label for="street"><?php echo JText::_('STREET'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="street" id="street" value="<?php echo $this->customer->street; ?>" /></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="city"><?php echo JText::_('CITY'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="city" id="city" value="<?php echo $this->customer->city; ?>" /></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="zip"><?php echo JText::_('ZIP'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="zip" id="zip" value="<?php echo $this->customer->zip; ?>" /></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="country"><?php echo JText::_('COUNTRY'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="country" id="country" value="<?php echo $this->customer->country; ?>" /></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="telephone"><?php echo JText::_('PHONES'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="telephone" id="telephone" value="<?php echo $this->customer->telephone; ?>" /></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="fax"><?php echo JText::_('FAX'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="fax" id="fax" value="<?php echo $this->customer->fax; ?>" /></td>
    			</tr>
    		</table>
    	</fieldset>
    	<div class="clr"></div>
   	</div>
   	<div class="clr"></div>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_CUSTOMER; ?>"/>
	<input type="hidden" name="task" value="save"/>
	<input type="hidden" name="boxchecked" value="1"/>
	<input type="hidden" name="cid[]" value="<?php echo $this->customer->id; ?>"/>
	<!-- Use for display customers reservations -->
	<input type="hidden" name="filter_reservation-surname" value="<?php echo $this->customer->surname; ?>"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
<script type="text/javascript">
    // <![CDATA[
    <?php if (ISJ3) { ?>
        function jSelectUser_user(id, title) {
            var old_id = document.getElementById("user_id").value;
            if (old_id != id) {
                document.getElementById("user_id").value = id;
                document.getElementById("user").value = title;
                document.getElementById("user").className = document.getElementById("user").className.replace(" invalid" , "");
                bSelectUser(id);
            }
            jModalClose();
        }
    <?php } else { ?>
        function jSelectUser_user(id, title) {
            var old_id = document.getElementById("user_id").value;
            if (old_id != id) {
                document.getElementById("user_id").value = id;
                document.getElementById("user_name").value = title;
                bSelectUser(id);
            }
            SqueezeBox.close();            
        }
    <?php } ?>
    /**
     * Update customer e-mail with user change.
     * @param {int} id new user id
     */
    function bSelectUser(id) {
        new Request({
            url: "<?php echo JRoute::_('index.php?option=com_booking&controller=customer&task=getUserData', false); ?>",
            method: "get",
            data: {
                id: id
            },
            onSuccess: function(user) {
                user = JSON.parse(user);
                document.id("email").value = user.email;
            }
        }).send();
    }
    // ]]>
</script>