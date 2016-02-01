<?php

/**
 * Reservation detail template.
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

$config = &AFactory::getConfig();
$user = JFactory::getUser();
$print = JRequest::getInt('print');
if ($print)
    AImporter::css('print');
?>
<?php if ($print) { ?>
	<script type="text/javascript">
    // <![CDATA[
    	window.addEvent('domready', function() {
			window.print();
    	});
    // ]]>
	</script>
<?php } ?>
<h1><?php echo $this->reservation->id ? JText::sprintf('RESERVATION_NUM', $this->reservation->id) : JText::_('ADD_RESERVATION'); ?></h1>
<a href="<?php echo $this->printLink; ?>" target="_blank" class="noprint printButton">
    <?php echo JHtml::_('image', 'system/printButton.png', '', NULL, true); ?>
    <span><?php echo JText::_('PRINT_RESERVATION'); ?></span>
</a>
<?php if ($this->isAdmin) { ?>
    <a href="<?php echo $this->voucherLink; ?>" target="_blank" class="noprint printButton">
        <?php echo JHtml::_('image', 'system/printButton.png', '', NULL, true); ?>
        <span><?php echo JText::_('PRINT_VOUCHER'); ?></span>
    </a>
    <a href="<?php echo $this->pdfLink; ?>" target="_blank" class="noprint printButton">
        <?php echo JHtml::_('image', 'system/pdf_button.png', '', NULL, true); ?>
        <span><?php echo JText::_('DOWNLOAD_VOUCHER'); ?></span>
    </a>
<?php } ?>
<div class="bookingToolbar noprint">
	<?php if (!JFactory::getUser()->guest) { ?>
		<a class="aIconToolBack tool back" href="javascript:history.back()" title="<?php echo JText::_('BACK', true); ?>"><?php echo JText::_('BACK', true); ?></a>
	<?php } ?>
	<?php if ($this->isAdmin || $user->authorise('booking.reservations.manage', 'com_booking')) { ?>
		<a href="<?php echo JRoute::_(ARoute::edit(CONTROLLER_RESERVATION, $this->reservation->id)); ?>" class="aIconToolEdit tool"><?php echo JText::_('JACTION_EDIT'); ?></a>
	<?php } ?>
	<div class="clr">&nbsp;</div>
</div>
<div class="reservation">
	<?php 
    	$name = BookingHelper::formatName($this->reservation);
    	$company = JString::trim($this->reservation->company);
    	if ($name || $company || count($this->reservation->fields) || $this->reservation->company_id || $this->reservation->vat_id) { 
    ?>
			<fieldset>
		    	<legend><?php echo JText::_('CUSTOMER'); ?></legend>
		    	<table>
                    <?php if ($config->fieldsPosition == 0) {
                            foreach ($this->getCustomFields() as $field) { ?>		 
                                <tr>
                                    <td class="key"><?php echo $field['title']; ?>: </td>
                                    <td><?php echo AUtils::getArrayValue($this->reservation->fields, $field['name'] . '.value'); ?></td>
                                </tr>
                    <?php  } 
                        }
		    			if ($name) { 
		    		?>
		    				<tr>
		    					<td class="key"><?php echo JText::_('NAME'); ?>: </td>
		    					<td><?php echo $name; ?></td>
		    				</tr>
		    		<?php 
		    			} 
		    		 	if ($company) {		
		    		?>
		    				<tr>
		    					<td class="key"><?php echo JText::_('COMPANY'); ?>: </td>
		    					<td><?php echo $company; ?></td>
		    				</tr>
		    		<?php 
		    		 	}
		    		 	if ($this->reservation->company_id) {
    		 		?>
		    				<tr>
		    					<td class="key"><?php echo JText::_('COMPANY_ID'); ?>: </td>
		    					<td><?php echo $this->reservation->company_id; ?></td>
		    				</tr>
		    		<?php 
		    		 	}
		    		 	if ($this->reservation->vat_id) {
	   		 		?>
		    				<tr>
		    					<td class="key"><?php echo JText::_('VAT_ID'); ?>: </td>
		    					<td><?php echo $this->reservation->vat_id; ?></td>
		    				</tr>
		    		<?php }
                        if ($config->fieldsPosition == 1) {
                            foreach ($this->getCustomFields() as $field) { ?>		 
                                <tr>
                                    <td class="key"><?php echo $field['title']; ?>: </td>
                                    <td><?php echo AUtils::getArrayValue($this->reservation->fields, $field['name'] . '.value'); ?></td>
                                </tr>
                    <?php  } 
                        } ?>
                   </table>
                </fieldset>
                <?php if (!empty($this->reservation->more_names)) { ?>
                    <fieldset>
                        <legend>
                            <?php echo JText::_('MORE_CUSTOMERS'); ?>
                        </legend>
                        <table>
                            <?php foreach ($this->reservation->more_names as $name) { ?>
                                <tr>
                                    <td class="key"></td>
                                    <td>
                                        <?php echo $name; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </fieldset>
                <?php } ?>
    <?php 
    	}
    	
    	$z=0;
		$countReservedItems = count($this->reservedItems);
    	if ($countReservedItems)
    		foreach ($this->reservedItems as $reservedItem){
    			TableReservationItems::display($reservedItem);
				$id = $z++;
                $iID = (int) $reservedItem->id;
				$subject = $this->subjects[$reservedItem->subject];
				$capacity = $subject->display_capacity || $subject->total_capacity>1 || $reservedItem->capacity>1; //display capacity row
				$fullPrice = $reservedItem->fullPrice!=$reservedItem->price; //display full price
				$fullDeposit = $reservedItem->fullDeposit!=$reservedItem->deposit; //display full deposit
				$fullPriceSupplements = $reservedItem->fullPrice!=$reservedItem->fullPriceSupplements; //display full price with supplements
				
    ?>
    <fieldset>
    	<legend>
    		<?php if ($subject) { ?>
    			<a href="<?php echo JRoute::_(ARoute::view(VIEW_SUBJECT, $subject->id, $subject->alias)); ?>" title="<?php echo $this->escape(JText::_('DISPLAY_OBJECT')); ?>" id="itemTitle<?php echo $iID; ?>">
    				<?php echo $reservedItem->subject_title; ?>
    			</a>
    		<?php } else { ?>
    			<span id="itemTitle<?php echo $iID; ?>">
                    <?php echo $reservedItem->subject_title; ?>
                </span>
            <?php }
            if ($this->isAdmin) {
                echo $reservedItem->sub_subject_title; 
            }
            if ($user->authorise('booking.reservation.edit.item', 'com_booking')) { ?>
                <span id="changeItemSelect<?php echo $iID; ?>"></span>
                <a class="aIconEditInline aIconEdit noprint" href="javascript:ViewReservation.openChangeItem(<?php echo $iID; ?>)" id="openChangeItem<?php echo $iID; ?>"></a>
                <a class="aIconEditInline aIconTick" href="javascript:ViewReservation.changeItem(<?php echo $iID; ?>)" id="changeItem<?php echo $iID; ?>" style="display: none"></a>                
                <a class="aIconEditInline aIconUnpublish" href="javascript:ViewReservation.closeChangeItem(<?php echo $iID; ?>)" id="closeChangeItem<?php echo $iID; ?>" style="display: none"></a>
            <?php } ?>
    	</legend>
    	<table class="reserved_item">
    		<?php if ($reservedItem->rtype == RESERVATION_TYPE_PERIOD) { ?>
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
    				<td colspan="5">
                        <?php echo AHtml::interval($reservedItem);
                        if ($user->authorise('booking.reservation.edit.date', 'com_booking')) { ?>
                            <a class="aIconEditInline aIconEdit noprint" href="javascript:ViewReservation.openChangeDate(<?php echo (int) $reservedItem->subject; ?>, <?php echo (int) $iID; ?>, <?php echo (int) $reservedItem->rtype; ?>)" id="openChangeDate<?php echo $iID; ?>"></a>
                        <?php } ?>
                    </td>
    			</tr>
    		<?php } ?>
    		<?php if ($capacity){?>
    		<tr>	
    			<td class="key"><?php echo JText::_('CAPACITY'); ?>: </td>
   				<td colspan="5"><?php echo number_format($reservedItem->capacity, 0, '', ' '); ?></td>
   			</tr>
   			<?php } ?>
				<?php if ($config->showUnitPrice) { ?>
    				<tr>	
		    			<td class="key"><?php echo ITEM_PRICE_TIP ?>:</td>
	    				<td width="1*"><?php echo BookingHelper::displayPrice($reservedItem->price, null, $reservedItem->tax); ?></td>
	    			</tr>
	    		<?php } ?>
	    		<?php foreach ($reservedItem->occupancy as $occupancy) { ?>
					<tr>
						<td class="key"><?php echo $occupancy['title']; ?>: </td>
						<td>
			    			<?php echo $occupancy['count']; ?>
			    			<?php if ($occupancy['total'] != 0) { ?>
			    				(<?php echo BookingHelper::displayPrice($occupancy['total'], null, $reservedItem->tax, true); ?>)
			    			<?php } ?>
			    		</td>
  					</tr>
   				<?php } ?>
			<?php 
				foreach ($reservedItem->supplements as $supplement) {
					/* @var $supplement TableReservationSupplement */
			?>				
					<tr>
						<td class="key hasTip" title="<?php echo BookingHelper::displaySupplementTooltip($supplement); ?>"><?php echo $supplement->title; ?>: </td>
    					<td colspan="5"><?php echo BookingHelper::displaySupplementValue($supplement, $reservedItem->tax); ?></td>
    				</tr>
    			<?php } ?>   	
                <?php if (JFactory::getUser()->authorise('booking.reservations.manage', 'com_booking.subject.' . $subject->id) && $reservedItem->provision) { ?>
                    <tr>	
                        <td class="key"><?php echo JText::_('PROVISION'); ?>:</td>
                        <td><?php echo BookingHelper::displayPrice($reservedItem->provision, null, $reservedItem->tax); ?></td>
                    </tr>
                <?php } ?>                                                                                                                  <?php if ($reservedItem->deposit && $config->showDepositPrice && $reservedItem->deposit != $reservedItem->fullDeposit) { ?>
	    			<tr>	
	    				<td class="key"><?php echo ITEM_DEPOSIT_TIP ?>:</td>
	    				<td><?php echo BookingHelper::displayPrice($reservedItem->deposit, null, $reservedItem->tax); ?></td>
	    			</tr>
	    		<?php } ?>
	    		<?php if ($reservedItem->fullDeposit && $config->showDepositPrice) { ?>
	    			<tr>
	    				<td class="key"><?php echo FULL_DEPOSIT_TIP; ?>:</td>
	    				<td><?php echo BookingHelper::displayPrice($reservedItem->fullDeposit, null, $reservedItem->tax); ?></td>
	    			</tr>
    			<?php } ?>
	    		<?php if ($config->showPriceExcludingTax) { ?>
    				<tr>
		    			<td class="key"><?php echo JText::_('TOTAL_PRICE_EXCLUDING_TAX'); ?>:</td>
		    			<td nowrap="nowrap">
							<?php echo BookingHelper::displayPrice(BookingHelper::getPriceExcludingTax(null, $reservedItem)); ?>
						</td>    						
    				</tr>
		    	<?php } ?>
	    		<?php if ($config->showTax) { ?>
	    			<tr>
			    		<td class="key"><?php echo BookingHelper::showTax($reservedItem->tax); ?>:</td>
						<td nowrap="nowrap"><?php echo BookingHelper::displayPrice(BookingHelper::getTax($reservedItem->fullPriceSupplements, $reservedItem->tax)); ?></td>    						
	    			</tr>
	    		<?php } ?>
	    		<?php if ($config->showTotalPrice) { ?>
   					<tr>
   						<td class="key"><?php echo FULL_PRICE_TIP; ?>:</td>
    					<td nowrap="nowrap"><?php echo BookingHelper::displayPrice($reservedItem->fullPriceSupplements ? $reservedItem->fullPriceSupplements : $reservedItem->fullPrice, null, $reservedItem->tax); ?></td>
    				</tr>
    			<?php } ?>
    		<?php if (($countReservedItems == 1) && ($reservedItem->cancel_time !== null)) {?>
    		<tr>
		    	<td class="key"><?php echo JText::_('DEPOSIT_MUST_BE_PAID_BEFORE'); ?>: </td>
				<td><strong><?php echo BookingHelper::formatExpiration($reservedItem->cancel_time,$reservedItem->from); ?></strong></td>				
    		</tr>
    		<?php }?>
    		    <?php if ($reservedItem->message) { ?>
		    		<tr>	
    					<td class="key"><?php echo JText::_('Message'); ?></td>
    					<td><?php echo htmlspecialchars($reservedItem->message); ?></td>
    				</tr>
    			<?php } ?>
            <?php foreach ($reservedItem->more_names as $q => $name) { 
                $inc = $reservedItem->occupancy ? 1 : 2;
                ?>
                <tr>
                    <td class="key">
                        <?php echo JText::sprintf('PERSON_NUM', $q + $inc); ?>:
                    </td>
                    <td>
                        <?php echo implode(' ', (array) $name); ?>
                    </td>
                </tr>
            <?php } ?>                    
    	</table>
    </fieldset>
    <?php } ?>
    
    <fieldset>
    	<legend><?php echo JText::_('RESERVATION_STATUS_AND_PAYMENT'); ?></legend>
    		<table>
    		<?php if ($this->isAdmin && AHtml::date($this->reservation->created, ADATE_FORMAT_LONG)) { ?>
	    		<tr>
	    			<td class="key"><?php echo JText::_('CREATED'); ?>:</td>
	    			<td>
	    				<?php echo $this->reservation->creator ? $this->reservation->creator : JText::_('UNREGISTERED_CUSTOMER'); ?>
	    				<?php echo AHtml::date($this->reservation->created, ADATE_FORMAT_LONG); ?>
	    			</td>
	 			</tr>
   			<?php } ?>
   			<?php if ($this->isAdmin && AHtml::date($this->reservation->modified, ADATE_FORMAT_LONG)) { ?>
   				<tr>
   					<td class="key"><?php echo JText::_('MODIFIED'); ?></td>
   					<td>
   						<?php echo $this->reservation->modifier ? $this->reservation->modifier : JText::_('UNREGISTERED_CUSTOMER'); ?>
   						<?php echo AHtml::date($this->reservation->modified, ADATE_FORMAT_LONG); ?>
   					</td>
   				</tr>
   			<?php } ?>
   			<?php if ($config->showPaymentStatus) { ?>
        		<tr>	
    				<td class="key"><?php echo JText::_('PAYMENT_STATUS'); ?>: </td>
   					<td>
    					<?php 
    						echo BookingHelper::showReservationPaymentStateLabel($this->reservation->paid);
                            if ($this->reservation->paid == RESERVATION_RECEIVE_DEPOSIT && $config->choosePayAmount) {
                                echo ' '.BookingHelper::displayPrice($this->reservation->fullDeposit);
                            }
    					?>
					</td>
    			</tr>
    		<?php } ?>
    		<tr>	
    			<td class="key"><?php echo JText::_('RESERVATION_STATUS'); ?>: </td>
    			<td>
					<?php 
						echo BookingHelper::showReservationStateLabel($this->reservation->state);
					?>
				</td>
   			</tr>
   			<?php if ($countReservedItems>1) {?>
            <?php if (JFactory::getUser()->authorise('booking.reservations.manage', 'com_booking')) { ?>
                <tr>
                    <td class="key"><?php echo JText::_('TOTAL_PROVISION'); ?>: </td>
                    <td><strong><?php echo BookingHelper::displayPrice($this->reservation->fullProvision); ?></strong></td>
                </tr>
            <?php } ?>            
			<?php if ($this->reservation->fullDeposit && $config->showDepositPrice) { ?>
   				<tr>
					<td class="key"><?php echo JText::_('DEPOSIT'); ?>: </td>
					<td><strong><?php echo BookingHelper::displayPrice($this->reservation->fullDeposit); ?></strong></td>
				</tr>
			<?php } ?>
			<?php if ($config->showPriceExcludingTax) { ?>
   				<tr>
	    			<td class="key"><?php echo JText::_('TOTAL_PRICE_EXCLUDING_TAX'); ?>:</td>
	    			<td><strong><?php echo BookingHelper::displayPrice(BookingHelper::getPriceExcludingTax($this->reservation, $this->reservedItems)); ?></strong></td>    						
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
			<?php
			$globalexpiration = false;
			foreach ($this->reservedItems as $reservedItem){
				if ($reservedItem->cancel_time !== null)
				{
					$globalexpiration = true;
					break;
				}
			}
				
			if ($globalexpiration) {?>
			<tr>
		    	<td class="key"><?php echo JText::_('DEPOSIT_MUST_BE_PAID_BEFORE'); ?>: </td>
				<td><strong><?php echo $this->depositExpires; ?></strong></td>				
    		</tr>
    		<?php } ?>
			<?php } ?>
    	</table>
    </fieldset>
</div>


<?php 
	$adrress = BookingHelper::formatAddress($this->reservation);
	$email = BookingHelper::getEmailLink($this->reservation);
	$telephone = JString::trim($this->reservation->telephone);
	$fax = JString::trim($this->reservation->fax);
	$note = JString::trim($this->reservation->note);
	if ($adrress || $email || $telephone || $fax || $note) { 
?>
		<div class="reservation">
		    <fieldset>
		    	<legend><?php echo JText::_('CONTACT'); ?></legend>
		    	<table>
		    		<?php 
		    			if ($adrress) {
		    		?>
			    			<tr>	
			    				<td class="key"><?php echo JText::_('ADRRESS'); ?>: </td>
			    				<td><?php echo $adrress; ?></td>
			    			</tr>
		    		<?php 
		    			}
		    			if ($email) {
		    		?>
		    				<tr>	
		    					<td class="key"><?php echo JText::_('EMAIL'); ?>: </td>
		    					<td><?php echo $email; ?></td>
		    				</tr>
		    		<?php 
		    			}
		    			if ($telephone) {
		    		?>
				    		<tr>	
				   				<td class="key"><?php echo JText::_('PHONES'); ?>: </td>
				    			<td><?php echo $telephone; ?></td>
				    		</tr>
		    		<?php 
		    			}
		    			if ($fax) {
		    		?>
				    		<tr>	
				   				<td class="key"><?php echo JText::_('FAX'); ?>: </td>
				   				<td><?php echo $fax; ?></td>
				    		</tr>
				    <?php 
		    			}
						if ($note) {
		    		?>
				    		<tr>	
				   				<td class="key"><?php echo JText::_('NOTE'); ?>: </td>
				   				<td><?php echo $note; ?></td>
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
<div class="clr">&nbsp;</div>

<div class="bookingToolbar noprint">
	<?php 
	list($avail,$text) = BookingHelper::getInvoiceLink($this->reservation->id);
	if ($avail==1)
		echo '<a href="javascript:void(0)" class="aIconToolInvoice tool invoice" onclick="window.open(\''.$text.'\',\'win2\', \'status=yes,toolbar=yes,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\');">'.JText::_('INVOICE').'</a>';
	elseif ($avail==2)
		echo '<span href="javascript:void(0)" class="hasTip aIconToolInvoice tool invoice" title="'.JText::_('INVOICE_NOT_AVAILABLE').'::'.JText::_($text).'">'.JText::_('INVOICE_NOT_AVAILABLE').'</span>';
	?>
	<div class="clr"></div>
</div>