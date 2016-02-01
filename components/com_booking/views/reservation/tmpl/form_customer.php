<?php

/**
 * Reservation edit form template.
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

/* @var $this BookingViewReservation */



$titleSave = $this->escape(JText::_('SAVE_RESERVATION'));
$titleContinue = $this->escape(JText::_('SAVE_CONTINUE'));
$titleErase = $this->escape(JText::_('RESET_RESERVATION'));
$titleStorno = $this->escape(JText::_('BACK'));

$config = &AFactory::getConfig();
$document = &JFactory::getDocument();
/* @var $document JDocument */
$user = JFactory::getUser();

if (JFactory::getApplication()->getUserState('com_booking.object.last'))
	$backUrl = JFactory::getApplication()->getUserState('com_booking.object.last');
elseif (count($this->reservedItems)){ //if no relevant refereer (like from iframe add)
	$newItemSubject = $this->subjects[end($this->reservedItems)->subject];
	$backUrl = JRoute::_(ARoute::view(VIEW_SUBJECT, $newItemSubject->id, $newItemSubject->alias));
}
else
	$backUrl = JRoute::_(ARoute::view(VIEW_SUBJECTS));
	
$canContinue = $user->authorise('booking.reservation.create', 'com_booking') || ($user->guest && $config->loginBeforeReserving);

?>

<h1><?php echo JText::_('ADD_RESERVATION'); ?></h1>
<?php 
	if (empty($this->reservedItems)) { 
?>
    	<p><?php echo JText::_('YOU_HAVE_NO_ITEMS_TO_RESERVE'); ?></p>
<?php 
    	return;
	}
?>
<div class="reservation">	
    <?php if ($canContinue) { ?>
	    <div class="bookingToolbar">
			<a class="aIconToolSave tool save" title="<?php echo $titleSave; ?>" href="javascript:submitbutton('save')" ><?php echo $titleSave; ?></a>
			<?php if($config->moreReservations){?><a class="aIconToolApply tool save" title="<?php echo $titleContinue; ?>" href="javascript:submitbutton('store')" ><?php echo $titleContinue; ?></a><?php }?>
			<a class="aIconToolRestore tool cancel" title="<?php echo $titleStorno; ?>" href="<?php echo $backUrl; ?>"><?php echo $titleStorno; ?></a>
			<a class="aIconToolCancel tool cancel" title="<?php echo $titleErase; ?>" href="javascript:submitbutton('erase')" ><?php echo $titleErase; ?></a>
			<div class="clr"></div>
		</div>
	<?php } ?>
	<?php if (!$canContinue) { ?>
		<div class="customer">
			<fieldset class="radio">
	    		<legend><?php echo JText::_('CUSTOMER'); ?></legend>
	    		<p>
	    			<input type="radio" name="customer" id="bookign_customer_login" autocomplete="off" />
	    			<label for="bookign_customer_login"><?php echo JText::_('RETURNING_CUSTOMERS_PLEASE_LOG_IN'); ?></label>
	    		</p>
	    		<?php if ($config->enableRegistration && !$config->showRegistrationUnderLogin) { ?>
		    		<p>
		    			<input type="radio" name="customer" id="booking_customer_register" autocomplete="off" />
	    				<label for="booking_customer_register"><?php echo JText::_('NEW_PLEASE_REGISTER'); ?></label>
	    			</p>
	    		<?php } ?>
                <form action="<?php echo JRoute::_('index.php'); ?>" method="post" id="booking_customer_loginform" style="display: none">	
                    <table>
                        <tr>
                            <td>
                                <label for="booking_customer_username"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label>
                            </td>
                            <td>
                                <input type="text" name="booking_customer_username" id="booking_customer_username" value="" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="booking_customer_password"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
                            </td>
                            <td>
                                <input type="password" name="booking_customer_password" id="booking_customer_password" value="" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button id="booking_login_submit" class="button btn btn-primary"><?php echo JText::_('JLOGIN'); ?></button>	    
                                <script type="text/javascript">
                                    // <![CDATA[
                                    window.addEvent('domready', function() {
                                        document.id('bookign_customer_login').addEvent('click', function() {
                                            document.id('booking_customer_loginform').show();
                                        });
                                        document.id('booking_login_submit').addEvent('click', function() { 
                                            if (document.id('booking_customer_username').value.trim() == '' || document.id('booking_customer_password').value.trim() == '')
                                                alert("<?php echo JText::_('Add username and password', true); ?>");
                                            else
                                                new Request({
                                                    url: '<?php echo addslashes(JRoute::_('index.php?option=com_booking&task=user.login')); ?>',
                                                    method: 'post',
                                                    data: {
                                                        'username': document.id('booking_customer_username').value,
                                                        'password': document.id('booking_customer_password').value
                                                    },
                                                    onSuccess: function(html) {
                                                        html == 'OK' ? location.reload() : alert("<?php echo JText::_('JGLOBAL_AUTH_INVALID_PASS', true); ?>");
                                                    }
                                                }).send();
                                            return false;
                                        });
                                        <?php if ($config->enableRegistration && !$config->showRegistrationUnderLogin) { ?>
                                            document.id('booking_customer_register').addEvent('click', function() { 
                                                window.location.href = "<?php echo JRoute::_('index.php?option=com_booking&controller=customer&task=edit&return=reservation', false); ?>";
                                            });
                                        <?php } ?>
                                        <?php if (!$config->enableRegistration) { ?>
                                            document.id('bookign_customer_login').fireEvent('click').checked = true;	
                                        <?php } ?>
                                    });
                                    // ]]>
                                </script>				
                            </td>
                        </tr>
                    </table>
                </form>
                <?php if ($config->enableRegistration && $config->showRegistrationUnderLogin) {
                        AImporter::importView('customer');
                        $view = new BookingViewCustomer();
                        $view->setLayout('form');
                        JRequest::setVar('return', 'reservation');
                        JRequest::setVar('hideCancelButton', 1);
                        echo $view->display(); 
                } ?>
	    	</fieldset>
	    </div>
	<?php } ?>
    <?php if ($canContinue) { ?>
        <form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm" class="reservation">	
    <?php } ?>
	<?php if ($canContinue && ($config->rsTitleBefore || $config->rsFirstname || $config->rsMiddlename || $config->rsSurname || $config->rsTitleAfter || $config->rsCompany || $this->getCustomFields())) { ?>
		<div class="customer">
			<fieldset>
	    		<legend><?php echo JText::_('CUSTOMER'); ?></legend>
	    		<table>
                   <?php if ($config->fieldsPosition == 0) {
                            foreach ($this->getCustomFields() as $field) { ?>
                                <tr>
                                    <td class="key"><?php echo AHtml::displayLabel($document, $field['required'] == 2, $field['name'], $field['name'], $field['title']); ?></td>
                                    <td><?php echo AHtml::getField($field, $this->reservation->fields); ?></td>
                                </tr>
			    	<?php  } 
                        }
	    				if ($config->rsTitleBefore) { 
	    			?>
			    			<tr>
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsTitleBefore', 'title_before', 'TITLE_BEFORE'); ?></td>
			    				<td><input class="text_area" type="text" name="title_before" id="title_before" size="60" maxlength="255" value="<?php echo $this->reservation->title_before; ?>" /></td>
			    			</tr>
	    			<?php
	    				}
	    				if ($config->rsFirstname) { 
	    			?>
			    			<tr>
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsFirstname', 'firstname', 'FIRST_NAME'); ?></td>
			    				<td><input class="text_area" type="text" name="firstname" id="firstname" size="60" maxlength="255" value="<?php echo $this->reservation->firstname; ?>" /></td>
			    			</tr>
	    			<?php
	    				}
	    				if ($config->rsMiddlename) { 
	    			?>
			    			<tr>
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsMiddlename', 'middlename', 'MIDDLE_NAME'); ?></td>
			    				<td><input class="text_area" type="text" name="middlename" id="middlename" size="60" maxlength="255" value="<?php echo $this->reservation->middlename; ?>" /></td>
			    			</tr>
	    			<?php
	    				}
	    				if ($config->rsSurname) { 
	    			?>
			    			<tr>
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsSurname', 'surname', 'SURNAME'); ?></td>
			    				<td><input class="text_area" type="text" name="surname" id="surname" size="60" maxlength="255" value="<?php echo $this->reservation->surname; ?>" /></td>
			    			</tr>
	    			<?php
	    				}
	    				if ($config->rsTitleAfter) { 
	    			?>
			    			<tr>
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsTitleAfter', 'title_after', 'TITLE_AFTER'); ?></td>
			    				<td><input class="text_area" type="text" name="title_after" id="title_after" size="60" maxlength="255" value="<?php echo $this->reservation->title_after; ?>" /></td>
			    			</tr>
	    			<?php
	    				}
	    				if ($config->rsCompany) { 
	    			?>
			    			<tr>
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsCompany', 'company', 'COMPANY'); ?></td>
			    				<td><input class="text_area" type="text" name="company" id="company" size="60" maxlength="255" value="<?php echo $this->reservation->company; ?>" /></td>
			    			</tr>
	    			<?php 
	    				}
	    				if ($config->rsCompanyId) {
	    			?>
    						<tr>
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsCompanyId', 'company_id', 'COMPANY_ID'); ?></td>
			    				<td><input class="text_area" type="text" name="company_id" id="company_id" size="20" maxlength="255" value="<?php echo $this->reservation->company_id; ?>" /></td>
			    			</tr>
	    			<?php 
	    				}
	    				if ($config->rsVatId) {
   					?>
			    			<tr>
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsVatId', 'vat_id', 'VAT_ID'); ?></td>
			    				<td><input class="text_area" type="text" name="vat_id" id="vat_id" size="20" maxlength="255" value="<?php echo $this->reservation->vat_id; ?>" /></td>
			    			</tr>
	    			<?php }	    				
                        if ($config->fieldsPosition == 1) {
                            foreach ($this->getCustomFields() as $field) { ?>
                                <tr>
                                    <td class="key"><?php echo AHtml::displayLabel($document, $field['required'] == 2, $field['name'], $field['name'], $field['title']); ?></td>
                                    <td><?php echo AHtml::getField($field, $this->reservation->fields); ?></td>
                                </tr>
			    	<?php } 
                        } 
                        if ($config->rsMoreNames == 1) { ?>
                            <tr>
                                <td></td>
                                <td>
                                    <div class="addMore" id="addMoreButton" onclick="ViewReservation.addMoreNames()">
                                        <?php echo JText::_('ADD_MORE_CUSTOMERS'); ?>
                                    </div>
                                    <div id="addMoreNames" class="addMoreNames" style="display: none">
                                        <h3><?php echo JText::_('MORE_CUSTOMERS'); ?></h3>
                                        <input type="text" name="more_names[]" value="" />
                                        <input type="text" name="more_names[]" value="" />
                                        <input type="text" name="more_names[]" value="" />
                                        <div class="addNext" id="addNextButton" onclick="ViewReservation.addNextName()">
                                            <?php echo JText::_('ADD_NEXT'); ?>
                                        </div>
                                        <div class="hideAddMore" onclick="ViewReservation.hideAddMoreNames()">
                                            <?php echo JText::_('HIDE_ADD_MORE_CUSTOMERS'); ?>
                                        </div>                                        
                                    </div>
                                </td>
                            </tr>
                    <?php } ?>
	    		</table>
	    	</fieldset>
	    	<div class="clr">&nbsp;</div>
	    </div>
    <?php 
		}
		$z=0;
		$countReservedItems = count($this->reservedItems);
    	if ($canContinue && $countReservedItems)
    		foreach ($this->reservedItems as $reservedItem){
    			/* @var $reservedItem TableReservationItems */
				$id = $z++;
				$subject = $this->subjects[$reservedItem->subject];
				$capacity = $subject->display_capacity || $subject->total_capacity>1 || $reservedItem->capacity>1; //display capacity row
				$fullPrice = $reservedItem->fullPrice!=$reservedItem->price; //display full price
				$fullDeposit = $reservedItem->fullDeposit!=$reservedItem->deposit; //display full deposit
				$fullPriceSupplements = $reservedItem->fullPrice!=$reservedItem->fullPriceSupplements; //display full price with supplements
				$rows = 4 + count($reservedItem->supplements) + ($reservedItem->fullDeposit ? 1 : 0); //no of rows
		
	?>
    <div class="reservation">
    	<fieldset>
    		<legend>
    			<a href="<?php echo JRoute::_(ARoute::view(VIEW_SUBJECT, $subject->id, $subject->alias)); ?>" title="<?php echo $this->escape(JText::sprintf('DISPLAY_SUBJECT_S', $subject->title)); ?>">
    				<?php echo $subject->title; ?>
    			</a>
    		</legend>
    		<table class="reserved_item">
    			<tr>
    				<?php if (isset($reservedItem->key)) { ?>
    				<td></td><td></td>
    				<td rowspan="<?php echo $rows ?>" valign="top" width="18">
    					<a class="remove-link" href="<?php echo JRoute::_(ARoute::customUrl(array('key' => $reservedItem->key)).ARoute::controller(CONTROLLER_RESERVATION).ARoute::task('remove_item')); ?>">
    						<img src="<?php echo IMAGES?>icon-r-cancel.png" title="<?php echo JText::_('REMOVE_ITEM')?>" alt="<?php echo JText::_('REMOVE_ITEM')?>" border="0" />
    					</a>
    				</td>
    				<?php } ?>
    			</tr>
    					<?php if ($reservedItem->ctype == CTYPE_PERIOD) { ?>
    						<tr>
    							<td class="key"><?php echo JText::_('TIMEFRAME'); ?></td>
    							<td><?php echo AHtml::showRecurenceTimeframe($reservedItem); ?></td>
    						</tr>
    						<tr>
    							<td class="key"><?php echo JText::_('RECURRENCE_PATTERN'); ?></td>
    							<td><?php echo AHtml::showRecurencePattern($reservedItem); ?></td>
    						</tr>
    						<tr>
    							<td class="key"><?php echo JText::_('RANGE_OF_RECURRENCE'); ?></td>
    							<td><?php echo AHtml::showRecurenceRange($reservedItem); ?></td>
    						</tr>
    						<tr>
    							<td class="key"><?php echo JText::_('RECURRENCE_TOTAL'); ?></td>
    							<td><?php echo AHtml::showRecurrenceTotal($reservedItem); ?></td>
    						</tr>
    					<?php } else { ?>
		    				<tr>	
		    					<td class="key"><?php echo AHtml::intervalLabel($reservedItem); ?>: </td>
		    					<td><?php echo AHtml::interval($reservedItem); ?></td>
		    				</tr>
		    			<?php } ?>
    			<?php 
    				if (is_object($reservedItem->box)) {
    					
    					$capacity=false;
    					if ($subject->display_capacity || $subject->total_capacity>1) {
    						$capacity=true;
    						$max = $subject->total_capacity-$reservedItem->box->maxReserved;
    			?>
	    				<tr>	
							<td class="key"><label for="capacity[<?php echo $id?>]"><?php echo JText::_('CAPACITY'); ?>: </label></td>
			    			<td colspan="5">
			    				<?php if ($max>1 && empty($reservedItem->occupancy)) { // each item has capacity 1 if occupancy is used ?>
				    				<?php if ($max<=100) { ?>
				    				<select class="capacity" name="capacity[<?php echo $id?>]" id="capacity[<?php echo $id?>]" onchange="submitbutton('store')">
				    					<?php for ($i = 1; $i <= $max; $i++) { ?>
				    						<option value="<?php echo $i; ?>" <?php if ($i == $reservedItem->capacity) { ?>selected="selected"<?php } ?>><?php echo $i; ?></option>
				    					<?php } ?>
				    				</select>
				    				<?php } else { ?>
				    					<input class="capacity" name="capacity[<?php echo $id?>]" id="capacity[<?php echo $id?>]" value="<?php echo $reservedItem->capacity; ?>">
				    					<input class="capacity" type="button" value="<?php echo JText::_('REFRESH')?>" onclick="submitbutton('store')">
				    				<?php } ?>
			    				<?php  } else { ?>
			    				1
			    				<?php  } ?>
			    			</td>
			    		</tr>
		    	<?php 
    				}
    			?>
						<?php if ($config->showUnitPrice) { ?>
		    				<tr>	
		    					<td class="key"><?php echo ITEM_PRICE_TIP ?>:</td>
		    					<td>
			    					<?php echo BookingHelper::displayPrice($reservedItem->price, null, $reservedItem->tax); ?>
			    				</td>
			    			</tr>
			    		<?php } ?>
			    		<?php foreach ($reservedItem->occupancy as $occupancy) { ?>
			    			<?php if ($occupancy['count']) { ?>
		    					<tr>	
		    						<td class="key"><?php echo $occupancy['title']; ?>:</td>
									<td>
			    						<?php echo $occupancy['count']; ?>
			    						<?php if ($occupancy['total'] != 0) { ?>
			    							(<?php echo BookingHelper::displayPrice($occupancy['total'], null, $reservedItem->tax, true); ?>)
			    						<?php } ?>
			    					</td>
			    				</tr>
			    			<?php } ?>
			    		<?php } ?>
				    	<?php foreach ($reservedItem->supplements as $supplement) {
		    				/* @var $supplement TableSupplement */ ?>
		    				<tr>
		    					<td class="key hasTip" title="<?php echo BookingHelper::displaySupplementTooltip($supplement); ?>"><?php echo $supplement->title; ?>: </td>
			    				<td>
			    					<?php echo BookingHelper::displaySupplementValue($supplement, $reservedItem->tax, true, $id, true);
		    						if ($supplement->capacity_multiply != 2) { ?>
		    							<input type="hidden" name="supplements[<?php echo $id?>][<?php echo $supplement->id; ?>][1]" value="<?php echo $this->escape($supplement->capacity); ?>" />
		    						<?php } ?>
			    					<input type="hidden" name="supplements[<?php echo $id?>][<?php echo $supplement->id; ?>][0]" value="<?php echo $this->escape($supplement->value); ?>" />
			    				</td>
			    			</tr>
						<?php } ?>			  
                        <?php if (JFactory::getUser()->authorise('booking.reservations.manage', 'com_booking.subject.' . $subject->id) && $reservedItem->provision) { ?>
                            <tr>	
                                <td class="key"><?php echo JText::_('PROVISION'); ?>:</td>
		    					<td><?php echo BookingHelper::displayPrice($reservedItem->provision, null, $reservedItem->tax); ?></td>
		    				</tr>
                        <?php } ?>                                                        
			    		<?php if ($reservedItem->deposit && $config->showDepositPrice && $reservedItem->deposit != $reservedItem->fullDeposit) { ?>
		    				<tr>	
		    					<td class="key"><?php echo ITEM_DEPOSIT_TIP; ?>:</td>
		    					<td>
		    						<?php echo BookingHelper::displayPrice($reservedItem->deposit, null, $reservedItem->tax); ?>
		    					</td>
		    				</tr>
		    			<?php } ?>	
		    			<?php if ($reservedItem->fullDeposit && $config->showDepositPrice) { ?>
		    				<td class="key"><?php echo FULL_DEPOSIT_TIP; ?>:</td>
		    				<td>
		    					<?php echo BookingHelper::displayPrice($reservedItem->fullDeposit, null, $reservedItem->tax); ?>
		    				</td>
		    			<?php } ?>
						<?php if ($config->showPriceExcludingTax) { ?>
    						<tr>
		    					<td class="key"><?php echo JText::_('TOTAL_PRICE_EXCLUDING_TAX'); ?>:</td>
		    					<td>	
									<?php echo BookingHelper::displayPrice(BookingHelper::getPriceExcludingTax(null, $reservedItem)); ?>
								</td>    						
    						</tr>
		    			<?php } ?>  
    					<?php if ($config->showTax) { ?>
    						<tr>
		    					<td class="key"><?php echo BookingHelper::showTax($reservedItem->tax); ?>:</td>
		    					<td>
									<?php echo BookingHelper::displayPrice(BookingHelper::getTax($reservedItem->fullPriceSupplements, $reservedItem->tax)); ?>
								</td>    						
    						</tr>
		    			<?php } ?>
		    			<?php if ($config->showTotalPrice) { ?>
	    					<tr> 
	    						<td class="key"><?php echo FULL_PRICE_TIP; ?>:</td>
	    						<td>
		    						<?php echo BookingHelper::displayPrice($reservedItem->fullPriceSupplements ? $reservedItem->fullPriceSupplements : $reservedItem->fullPrice, null, $reservedItem->tax); ?>
		    					</td>
		    				</tr>
		    			<?php } ?>
   						<?php if (($countReservedItems == 1) && ($reservedItem->cancel_time !== null)) {?>
			    		<tr>
					    	<td class="key"><?php echo JText::_('DEPOSIT_MUST_BE_PAID_BEFORE'); ?>: </td>
							<td><strong><?php echo BookingHelper::formatExpiration($reservedItem->cancel_time,$reservedItem->from); ?></strong></td>				
			    		</tr>
   						<?php } ?>
                        <?php if ($config->allowCustomMessage) { ?>
                            <tr>	
                                <td class="key"><?php echo '<label for="message-' . $id . '">' . JText::_('Message') . ': </label>'; ?></td>
                                <td><input class="text_area" type="text" name="message[<?php echo $id;?>]" id="message-<?php echo $id; ?>" size="60" maxlength="255" value="" /></td>
                            </tr>
    				<?php } ?> 
    				<?php } ?> 
                    <?php if ($config->rsMoreNames > 1) {
                            $persons = $reservedItem->capacity - 1;
                            foreach ($reservedItem->occupancy as $occupancy) {
                                if ($occupancy['count']) {
                                    $persons += $occupancy['count'];
                                }
                            }
                            $inc = $reservedItem->occupancy ? 1 : 2;
                            for ($q = 0; $q < $persons; $q++) { ?>
                                <tr>
                                    <td class="key">
                                        <?php echo JText::sprintf('PERSON_NUM', ($q + $inc)); ?>:
                                    </td>
                                    <td class="more_names">
                                        <div class="field">
                                            <?php echo AHtml::displayLabel($document, true, '', 'more_names_firstname'.$id.'-'.$q, 'FIRST_NAME'); ?>
                                            <input type="text" name="more_names[<?php echo $id; ?>][<?php echo $q; ?>][firstname]" id="more_names_firstname<?php echo $id.'-'.$q; ?>" value="" />  
                                        </div>
                                        <div class="field">
                                            <?php echo AHtml::displayLabel($document, true, '', 'more_names_surname'.$id.'-'.$q, 'SURNAME'); ?>
                                            <input type="text" name="more_names[<?php echo $id;?>][<?php echo $q; ?>][surname]" id="more_names_surname<?php echo $id.'-'.$q; ?>" value="" />
                                        </div>
                                    </td>
                                </tr>
                    <?php   }
                        } ?>
    			</table>
    			
    			<?php foreach ($reservedItem->boxIds as $bid) { ?>
				<input type="hidden" name="boxIds[<?php echo $id?>][]" value="<?php echo $bid; ?>" />
				<?php } ?>

				<input type="hidden" name="ctype[<?php echo $id?>]" value="<?php echo $reservedItem->ctype; ?>" />
				<input type="hidden" name="subject[<?php echo $id?>]" value="<?php echo $reservedItem->subject; ?>" />
				
				<?php foreach ($reservedItem->occupancy as $occupancy) { ?>
					<input type="hidden" name="occupancy[<?php echo $id?>][<?php echo $occupancy['id']; ?>]" value="<?php echo $occupancy['count']; ?>" />
			    <?php } ?>
				
				<?php if ($reservedItem->ctype == CTYPE_PERIOD) { ?>
					<input type="hidden" name="period_rtype_id[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_rtype_id); ?>" />
					<input type="hidden" name="period_price_id[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_price_id); ?>" />
					<input type="hidden" name="period_time_up[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_time_up); ?>" />
    				<input type="hidden" name="period_time_down[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_time_down); ?>" />
    				<input type="hidden" name="period_type[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_type); ?>" />
    				<input type="hidden" name="period_recurrence[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_recurrence); ?>" />
    				<input type="hidden" name="period_monday[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_monday); ?>" />
    				<input type="hidden" name="period_tuesday[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_tuesday); ?>" />
    				<input type="hidden" name="period_wednesday[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_wednesday); ?>" />
    				<input type="hidden" name="period_thursday[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_thursday); ?>" />
    				<input type="hidden" name="period_friday[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_friday); ?>" />
    				<input type="hidden" name="period_saturday[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_saturday); ?>" />
    				<input type="hidden" name="period_sunday[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_sunday); ?>" />
    				<input type="hidden" name="period_month[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_month); ?>" />
					<input type="hidden" name="period_week[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_week); ?>" />
					<input type="hidden" name="period_day[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_day); ?>" />    				
    				<input type="hidden" name="period_date_up[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_date_up); ?>" />
    				<input type="hidden" name="period_end[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_end); ?>" />
    				<input type="hidden" name="period_occurrences[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_occurrences); ?>" />
    				<input type="hidden" name="period_date_down[<?php echo $id?>]" value="<?php echo $this->escape($reservedItem->period_date_down); ?>" />
				<?php } ?>
    		</fieldset>
    		<div class="clr">&nbsp;</div>
    	</div>
<?php  } ?>

<?php if ($countReservedItems > 1 && ($config->showTotalPrice || $config->showTax || $this->reservation->fullDeposit)) { ?>    	
    <div class="reservation">
    	<fieldset>
    		<legend><?php echo JText::_('RESERVATION_STATUS_AND_PAYMENT'); ?></legend>
    		<table>
                <?php if (JFactory::getUser()->authorise('booking.reservations.manage', 'com_booking')) { ?>
                    <tr>
    					<td class="key"><?php echo JText::_('TOTAL_PROVISION'); ?>: </td>
    					<td><strong><?php echo BookingHelper::displayPrice($this->reservation->fullProvision); ?></strong></td>
    				</tr>
                <?php } ?>
    		    <?php if ($this->reservation->fullDeposit && $config->showDepositPrice) {?>
    				<tr>
    					<td class="key"><?php echo JText::_('DEPOSIT'); ?>: </td>
    					<td><strong><?php echo BookingHelper::displayPrice($this->reservation->fullDeposit); ?></strong></td>
    				</tr>
    				<tr>
			    		<td class="key"><?php echo JText::_('DEPOSIT_MUST_BE_PAID_BEFORE'); ?>: </td>
						<td><strong><?php echo $this->depositExpires; ?></strong></td>				
	    			</tr>
	    		<?php } ?>
    			<?php if ($config->showTax) { ?>
    				<tr>
		    			<td class="key"><?php echo JText::_('TAX'); ?>: </td>
						<td><strong><?php echo BookingHelper::displayPrice(BookingHelper::getFullTax($this->reservedItems)); ?></strong></td>				
    				</tr>
    			<?php } ?>	    		
    			<?php if ($config->showTotalPrice) { ?>
    				<tr>
    					<td class="key"><?php echo JText::_('TOTAL_PRICE'); ?>: </td>
    					<td><strong><?php echo BookingHelper::displayPrice($this->reservation->fullPrice); ?></strong></td>
    				</tr>
    			<?php } ?>
    		</table>
    	</fieldset>
   	</div>
<?php } ?>

<?php
	?>
    
<?php	if ($canContinue && ($config->rsStreet || $config->rsCity || $config->rsZip || $config->rsCountry || $config->rsEmail || $config->rsTelephone || $config->rsFax)) { ?>
	    <div class="contact">
	    	<fieldset>
	    		<legend><?php echo JText::_('RESERVATION_CONTACT'); ?></legend>
	    		<table>
	    			<?php
	    				if ($config->rsStreet) { 
	    			?>
			    			<tr>	
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsStreet', 'street', 'STREET'); ?></td>
			    				<td><input class="text_area" type="text" name="street" id="street" size="60" maxlength="255" value="<?php echo $this->reservation->street; ?>" /></td>
			    			</tr>
	    			<?php
	    				}
	    				if ($config->rsCity) { 
	    			?>
			    			<tr>	
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsCity', 'city', 'CITY'); ?></td>
			    				<td><input class="text_area" type="text" name="city" id="city" size="60" maxlength="255" value="<?php echo $this->reservation->city; ?>" /></td>
			    			</tr>
	    			<?php
	    				} 
	    				if ($config->rsZip) { 
	    			?>
			    			<tr>	
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsZip', 'zip', 'ZIP'); ?></td>
			    				<td><input class="text_area" type="text" name="zip" id="zip" size="60" maxlength="255" value="<?php echo $this->reservation->zip; ?>" /></td>
			    			</tr>
	    			<?php
	    				} 
	    				if ($config->rsCountry) { 
	    			?>
			    			<tr>	
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsCountry', 'country', 'COUNTRY'); ?></td>
			    				<td><input class="text_area" type="text" name="country" id="country" size="60" maxlength="255" value="<?php echo $this->reservation->country; ?>" /></td>
			    			</tr>
	    			<?php
	    				} 
	    				if ($config->rsEmail) { 
	    			?>
			    			<tr>	
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsEmail', 'email', 'EMAIL'); ?></td>
			    				<td><input class="text_area" type="text" name="email" id="email" size="60" maxlength="255" value="<?php echo $this->reservation->email; ?>" /></td>
			    			</tr>
	    			<?php
	    				} 
	    				if ($config->rsTelephone) { 
	    			?>
			    			<tr>	
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsTelephone', 'telephone', 'TELEPHONE'); ?></td>
			    				<td><input class="text_area" type="text" name="telephone" id="telephone" size="60" maxlength="255" value="<?php echo $this->reservation->telephone; ?>" /></td>
			    			</tr>
	    			<?php
	    				} 
	    				if ($config->rsFax) { 
	    			?>
			    			<tr>	
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsFax', 'fax', 'FAX'); ?></td>
			    				<td><input class="text_area" type="text" name="fax" id="fax" size="60" maxlength="255" value="<?php echo $this->reservation->fax; ?>" /></td>
			    			</tr>
	    			<?php 
	    				} 				
	    				if ($config->rsNote) {
	    			?>
			    			<tr>	
			    				<td class="key"><?php echo AHtml::displayLabel($document, $config, 'rsNote', 'note', 'NOTE'); ?></td>
			    				<td>
			    					<textarea name="note" id="note" cols="50" rows="10"><?php echo $this->reservation->note; ?></textarea>
			    				</td>
			    			</tr>
	    			<?php 
	    				}
	    				if (($captcha = BookingHelper::showCaptcha())) { 
	    			?>
			    			<tr>	
			    				<td class="key"><label for="fax" class="compulsory"><?php echo JText::_('CAPTCHA'); ?>: </label></td>
			    				<td>
			    					<?php echo $captcha; ?>
								</td>
			    			</tr>
	    			<?php 
	    				}
	    			?>
				</table>
	    	</fieldset>
	    	<div class="clr">&nbsp;</div>
	   	</div>
   	<?php 
    	} 
    	// last box with note, captcha, payment method and terms
    	if ($canContinue && // test if one them is turn on
			($config->terms_of_contract_accept || $config->terms_of_privacy_accept) // one of terms
    	) {	
    ?>
	   	<div class="terms">
	   		<fieldset>
	    		<table>
	    			<?php 
	    			if ($config->terms_of_contract_accept || $config->terms_of_privacy_accept) {
	    				JHTML::_('behavior.modal');
	    			?>
	    					<tr>
	    						<td class="key"></td>
	    						<td>
	    							
	    							<?php if ($config->terms_of_contract_accept) { ?>
	    								<label>
	    								<input type="checkbox" name="accept_terms_of_contract" id="accept_terms_of_contract" value="1">
	    								<?php echo JText::_('I_ACCEPT')?> <a class="modal" href="#terms_of_contract"><?php echo $config->terms_of_contract->title; ?></a>.
	    								</label>
	    								
	    								<div style="display:none"><div id="terms_of_contract">
	    								<h2><?php echo $config->terms_of_contract->title; ?></h2>
	    								<?php echo $config->terms_of_contract->text; ?>
	    								</div></div>
	    							<?php } ?>
	    							<?php if ($config->terms_of_privacy_accept) { ?>
	    								<label>
	    								<input type="checkbox" name="accept_terms_of_privacy" id="accept_terms_of_privacy" value="1">
	    								<?php echo JText::_('I_ACCEPT')?> <a class="modal" href="#terms_of_privacy"><?php echo $config->terms_of_privacy->title; ?></a>.
	    								</label>
	    								
	    								<div style="display:none"><div id="terms_of_privacy">
	    								<h2><?php echo $config->terms_of_privacy->title; ?></h2>
	    								<?php echo $config->terms_of_privacy->text; ?>
	    								</div></div>
	    							<?php } ?>
	    						</td>
	    					</tr>
	    					
	    			<?php 
	    			}
	    			?>
	    		</table>
	    	</fieldset>
	    </div>
    <?php 
    	}
    ?>
    <?php if ($canContinue) { ?>
	   	<div class="bookingToolbar">
			<a class="aIconToolSave tool save" title="<?php echo $titleSave; ?>" href="javascript:submitbutton('save')" ><?php echo $titleSave; ?></a>
			<?php if($config->moreReservations){?><a class="aIconToolApply tool save" title="<?php echo $titleContinue; ?>" href="javascript:submitbutton('store')" ><?php echo $titleContinue; ?></a><?php }?>
			<a class="aIconToolRestore tool cancel" title="<?php echo $titleStorno; ?>" href="<?php echo $backUrl; ?>"><?php echo $titleStorno; ?></a>
			<a class="aIconToolCancel tool cancel" title="<?php echo $titleErase; ?>" href="javascript:submitbutton('erase')" ><?php echo $titleErase; ?></a>
			<div class="clr"></div>
		</div>	
        <input type="hidden" name="option" value="<?php echo OPTION; ?>" />
        <input type="hidden" name="controller" value="<?php echo CONTROLLER_RESERVATION; ?>" />
        <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
        <input type="hidden" name="task" value="save" />
        <input type="hidden" name="view" value="reservation" />
        <input type="hidden" name="layout" value="form" />
        <input type="hidden" name="month" value="<?php echo JRequest::getString('month'); ?>" />
        <input type="hidden" name="year" value="<?php echo JRequest::getString('year'); ?>" />
        <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>" />
        <?php 
            echo JHTML::_('form.token'); 
        ?>
        </form> 
    <?php } ?>
</div>