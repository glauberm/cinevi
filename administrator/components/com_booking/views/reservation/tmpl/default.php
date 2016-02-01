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

AHtml::title(JText::sprintf('RESERVATION_NUM', $this->reservation->id), 'categories');

//append invoice icon
list($avail,$invoiceLink) = BookingHelper::getInvoiceLink($this->reservation->id);
if ($avail==1 || $avail==2){
	$bar = &JToolBar::getInstance('toolbar');
	if ($avail==1)
		$link = '<a title="'.JText::_('OPEN_INVOICE').'" href="javscript:void(0)" onclick="window.open(\''.$invoiceLink.'\',\'win2\', \'status=yes,toolbar=yes,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\');">';
	elseif ($avail==2)
		$link = '<a class="hasTip" title="'.JText::_('INVOICE_NOT_AVAILABLE').'::'.JText::_($invoiceLink).'" href="javscript:void(0)">';
	$bar->appendButton('Custom', $link.'<span style="background-image: url(\''.IMAGES .'icon-32-invoice.png\')"></span>'.JText::_('INVOICE').'</a>');
}

JToolBarHelper::custom('Edit', 'edit', 'edit', 'JACTION_EDIT', false);
JToolBarHelper::cancel();
JToolBar::getInstance('toolbar')->appendButton('Link', 'print', 'PRINT', JRoute::_(ARoute::detail(CONTROLLER_RESERVATION, $this->reservation->id, array('tmpl' => 'component', 'print' => 1))));

$config = AFactory::getConfig();

$print = JRequest::getInt('print');
if ($print)
    AImporter::css('print');

?>
<script type="text/javascript">
    // <![CDATA[
    	window.addEvent('domready', function() {
        	var print = document.getElement('#toolbar-print a');
        	if (print)
        		print.setProperty('target', '_blank');
        	else {
        		var print = document.getElement('#toolbar-print button');
        		if (print)
					print.setProperty('onclick', "window.open('<?php echo JRoute::_(ARoute::detail(CONTROLLER_RESERVATION, $this->reservation->id, array('tmpl' => 'component', 'print' => 1)), false); ?>')");
        	}
    		<?php if (JRequest::getInt('print')) { ?>
        		window.print();
        	<?php } ?>
    	});
    // ]]>
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col width-50">
		<fieldset class="adminform">
    		<legend><?php echo JText::_('CUSTOMER'); ?></legend>
    		<table class="admintable">
    			<tr>
    				<td class="key"><label><?php echo JText::_('USER'); ?>:</label></td>
    				<td>
    					<?php if ($this->customer->id) { ?>
    						<a href="<?php echo ARoute::detail(CONTROLLER_CUSTOMER, $this->customer->id); ?>" title="<?php echo $this->escape(JText::_('DISPLAY_CUSTOMER_DETAIL')); ?>">
    							<?php echo BookingHelper::formatName($this->customer); ?>
    						</a>
    					<?php 
    						} else {
    							echo JText::_('UNREGISTERED');
    						} 
    					?>
    				</td>
    			</tr>
    			<tr>
    				<td class="key"><label><?php echo JText::_('NAME'); ?>:</label></td>
    				<td><?php echo BookingHelper::formatName($this->reservation); ?></td>
    			</tr>
    			<tr>
    				<td class="key"><label><?php echo JText::_('COMPANY'); ?>:</label></td>
    				<td><?php echo $this->reservation->company; ?></td>
    			</tr>
    			<tr>
    				<td class="key"><label><?php echo JText::_('COMPANY_ID'); ?>:</label></td>
    				<td><?php echo $this->reservation->company_id; ?></td>
    			</tr>
    			<tr>
    				<td class="key"><label><?php echo JText::_('VAT_ID'); ?>:</label></td>
    				<td><?php echo $this->reservation->vat_id; ?></td>
    			</tr>    			    			
    			<?php
    				if (is_array($this->reservation->fields)) { 
    					foreach ($this->reservation->fields as $field) { 
    			?>		 
		    		 		<tr>
		    					<td class="key"><label><?php echo $field['title']; ?>:</label></td>
		    					<td><?php echo $field['value']; ?></td>
		    				</tr>
		    	<?php 
    					}
    				}
    			?>
    		</table>
    	</fieldset>
        <?php if (!empty($this->reservation->more_names)) { ?>
            <fieldset class="adminform addMoreNames">
                <legend>
                    <?php echo JText::_('MORE_CUSTOMERS'); ?>
                </legend>
                <?php foreach ($this->reservation->more_names as $name) { ?>
                    <?php echo $name; ?>
                    <br/>
                <?php } ?>
            </fieldset>
        <?php }
         
    	$z=0;
		$countReservedItems = count($this->reservedItems);
    	if ($countReservedItems)
    		foreach ($this->reservedItems as $reservedItem){
    			/* @var $reservedItem TableReservationItems */
    			TableReservationItems::display($reservedItem);
				$id = $z++;
				$subject = $this->subjects[$reservedItem->subject];
				$capacity = $subject->display_capacity || $subject->total_capacity>1 || $reservedItem->capacity>1; //display capacity row
				$fullPrice = $reservedItem->fullPrice!=$reservedItem->price; //display full price
				$fullDeposit = $reservedItem->fullDeposit!=$reservedItem->deposit; //display full deposit
				$fullPriceSupplements = $reservedItem->fullPrice!=$reservedItem->fullPriceSupplements; //display full price with supplements
	?>
    	<fieldset class="adminform">
    		<legend>
    			<?php if ($subject->title) { ?>
    				<a href="<?php echo ARoute::edit(CONTROLLER_SUBJECT, $subject->id); ?>" title="<?php echo $this->escape(JText::_('DISPLAY_SUBJECT')); ?>">
    					<?php echo $reservedItem->subject_title; ?>
    				</a>
    			<?php } else
    					echo $reservedItem->subject_title;
    				    echo $reservedItem->sub_subject_title; ?>
    		</legend>
    		<table class="admintable reserved_item">
    			    			<?php if ($reservedItem->rtype == RESERVATION_TYPE_PERIOD) { ?>
    				<tr>
    					<td class="key"><label><?php echo JText::_('TIMEFRAME'); ?>:</label></td>
    					<td><?php echo AHtml::showRecurenceTimeframe($reservedItem); ?></td>
    				</tr>
    				<tr>
    					<td class="key"><label><?php echo JText::_('RECURRENCE_PATTERN'); ?>:</label></td>
    					<td><?php echo AHtml::showRecurencePattern($reservedItem); ?></td>
    				</tr>
    				<tr>
    					<td class="key"><label><?php echo JText::_('RANGE_OF_RECURRENCE'); ?>:</label></td>
    					<td><?php echo AHtml::showRecurenceRange($reservedItem); ?></td>
    				</tr>
    				<tr>
    					<td class="key"><label><?php echo JText::_('RECURRENCE_TOTAL'); ?>:</label></td>
    					<td><?php echo AHtml::showRecurrenceTotal($reservedItem); ?></td>
    				</tr>
    			<?php } else { ?>
    				<tr>	
    					<td class="key"><label><?php echo AHtml::intervalLabel($reservedItem); ?>:</label></td>
    					<td colspan="5"><?php echo AHtml::interval($reservedItem); ?></td>
    				</tr>
    			<?php } ?>
    			<?php if ($capacity){?>
    			<tr>	
    				<td class="key"><label><?php echo JText::_('CAPACITY'); ?>:</label></td>
    				<td colspan="5"><?php echo number_format($reservedItem->capacity, 0, '', ' '); ?></td>
    			</tr>
    			<?php } ?>    			
    			<?php if ($config->showUnitPrice) { ?>
    				<tr>	
		    			<td class="key"><label><?php echo ITEM_PRICE_TIP ?>:</label></td>
		    			<td><?php echo BookingHelper::displayPrice($reservedItem->price, null, $reservedItem->tax); ?></td>		    		
		    		</tr>
		    	<?php } ?>
		    	<?php foreach ($reservedItem->occupancy as $occupancy) { ?>
					<tr>
						<td class="key"><label><?php echo $occupancy['title']; ?>: </label></td>
						<td>
			    			<?php echo $occupancy['count']; ?>
			    			<?php if ($occupancy['total'] != 0) { ?>
			    				(<?php echo BookingHelper::displayPrice($occupancy['total'], null, $reservedItem->tax, true); ?>)
			    			<?php } ?>
			    		</td>
  					</tr>
   				<?php } ?>
    			<?php foreach ($reservedItem->supplements as $supplement) { ?>
					<?php /* @var $supplement TableReservationSupplement */ ?>
			
					<tr>
						<td class="key hasTip" title="<?php echo BookingHelper::displaySupplementTooltip($supplement); ?>"><label><?php echo $supplement->title; ?>: </label></td>
    					<td colspan="5"><?php echo BookingHelper::displaySupplementValue($supplement, $reservedItem->tax); ?></td>
    				</tr>
    			<?php } ?>   				
		    	<?php if ($reservedItem->provision) { ?>
		    		<tr>	
                        <td class="key"><label><?php echo JText::_('PROVISION'); ?>:</label></td>
		    			<td><?php echo BookingHelper::displayPrice($reservedItem->provision); ?></td>
		    		</tr>
		    	<?php } ?>                    
		    	<?php if ($reservedItem->deposit && $config->showDepositPrice) { ?>
		    		<tr>	
		    			<td class="key"><label><?php echo ITEM_DEPOSIT_TIP ?>:</label></td>
		    			<td><?php echo BookingHelper::displayPrice($reservedItem->deposit); ?></td>
		    		</tr>
		    	<?php } ?>
		    	<?php if ($reservedItem->fullDeposit && $config->showDepositPrice) { ?>
		    		<tr>
		    			<td class="key"><label><?php echo FULL_DEPOSIT_TIP; ?>:</label></td>
		    			<td><?php echo BookingHelper::displayPrice($reservedItem->fullDeposit); ?></td>
		    		</tr>
		    	<?php } ?>		    			
		    	<?php if ($config->showPriceExcludingTax) { ?>
    				<tr>
	    				<td class="key"><label><?php echo JText::_('TOTAL_PRICE_EXCLUDING_TAX'); ?>:</label></td>
	    				<td><?php echo BookingHelper::displayPrice(BookingHelper::getPriceExcludingTax(null, $reservedItem)); ?></td>    						
   					</tr>
   				<?php } ?>
   				<?php if ($config->showTax) { ?>
		    		<tr>
    					<td class="key"><label><?php echo BookingHelper::showTax($reservedItem->tax); ?>:</label>
    					<td><?php echo BookingHelper::displayPrice(BookingHelper::getTax($reservedItem->fullPriceSupplements, $reservedItem->tax)); ?></td>
    				</tr>
    			<?php } ?>
		    	<?php if ($config->showTotalPrice) { ?>
		    		<tr>	
    					<td class="key"><label><?php echo FULL_PRICE_TIP ?>:</label></td>
		    			<td><?php echo BookingHelper::displayPrice($reservedItem->fullPrice, null, $reservedItem->tax); ?></td>    				
    				</tr>
    				<tr>	
    					<td class="key"><label><?php echo FULL_PRICE_SUPPLEMENTS_TIP ?>:</label></td>
						<td><?php echo BookingHelper::displayPrice($reservedItem->fullPriceSupplements, null, $reservedItem->tax); ?></td>
    				</tr>
		    	<?php } ?>
		    	<?php if ($reservedItem->message) { ?>
    				<tr>	
	    				<td class="key"><label><?php echo JText::_('Message'); ?>:</label></td>
	    				<td><?php echo htmlspecialchars($reservedItem->message); ?></td>
	    			</tr>
	    		<?php } ?>
                <?php foreach ($reservedItem->more_names as $q => $name) { 
                    $inc = $reservedItem->occupancy ? 1 : 2;
                    ?>
                    <tr>
                        <td class="key">
                            <label>
                                <?php echo JText::sprintf('PERSON_NUM', $q + $inc); ?>:
                            </label>
                        </td>
                        <td>
                            <?php echo implode(' ', (array) $name); ?>
                                                    </td>
                    </tr>
                <?php } ?>                                        
    		</table>
    	</fieldset>
    	<?php } ?>
    </div>
    <div class="col width-50">
    	<fieldset class="adminform">
    		<legend><?php echo JText::_('CONTACT'); ?></legend>
    		<table class="admintable">
    			<tr>	
    				<td class="key"><label><?php echo JText::_('ADDRESS'); ?>:</label></td>
    				<td><?php echo BookingHelper::formatAddress($this->reservation); ?></td>
    			</tr>	
    			<tr>	
    				<td class="key"><label><?php echo JText::_('EMAIL'); ?>:</label></td>
    				<td><?php echo BookingHelper::getEmailLink($this->reservation); ?></td>
    			</tr>
    			<tr>	
    				<td class="key"><label><?php echo JText::_('TELEPHONE'); ?>:</label></td>
    				<td><?php echo $this->reservation->telephone; ?></td>
    			</tr>
    			<tr>	
    				<td class="key"><label><?php echo JText::_('FAX'); ?>:</label></td>
    				<td><?php echo $this->reservation->fax; ?></td>
    			</tr>
    			<tr>	
    				<td class="key"><label><?php echo JText::_('NOTE'); ?>:</label></td>
    				<td><?php echo $this->reservation->note; ?></td>
    			</tr>
    		</table>
    	</fieldset>
	    <fieldset class="adminform">
	    	<legend><?php echo JText::_('RESERVATION_STATUS_AND_PAYMENT'); ?></legend>
	    		<table class="admintable">
	    			<?php if (AHtml::date($this->reservation->created, ADATE_FORMAT_LONG)) { ?>
	    				<tr>
	    					<td class="key"><label><?php echo JText::_('CREATED'); ?>:</label></td>
	    					<td>
	    						<?php echo $this->reservation->creator ? $this->reservation->creator : JText::_('UNREGISTERED_CUSTOMER'); ?>
	    						<?php echo AHtml::date($this->reservation->created, ADATE_FORMAT_LONG); ?>
	    					</td>
	    				</tr>
	    			<?php } ?>
	    			<?php if (AHtml::date($this->reservation->modified, ADATE_FORMAT_LONG)) { ?>
	    				<tr>
	    					<td class="key"><label><?php echo JText::_('MODIFIED'); ?>:</label></td>
	    					<td>
	    						<?php echo $this->reservation->modifier ? $this->reservation->modifier : JText::_('UNREGISTERED_CUSTOMER'); ?>
	    						<?php echo AHtml::date($this->reservation->modified, ADATE_FORMAT_LONG); ?>
	    					</td>
	    				</tr>
	    			<?php } ?>
	    			<?php if ($config->usingPrices) { ?>
	    				<tr>	
	    					<td class="key"><label><?php echo JText::_('PAYMENT_STATUS'); ?>:</label></td>
	    					<td>
	    						<?php echo BookingHelper::showReservationPaymentStateLabel($this->reservation->paid); ?>
							</td>
	    				</tr>
	    			<?php } ?>
	    			<tr>	
	    				<td class="key"><label><?php echo JText::_('RESERVATION_STATUS'); ?>:</label></td>
	    				<td>
							<?php echo BookingHelper::showReservationStateLabel($this->reservation->state); ?>
						</td>
	    			</tr>
	    			                    <?php if ($this->reservation->fullProvision) { ?>
                        <tr>
                            <td class="key"><label><?php echo JText::_('TOTAL_PROVISION'); ?>: </label></td>
                            <td><strong><?php echo BookingHelper::displayPrice($this->reservation->fullProvision); ?></strong></td>
                        </tr>
                    <?php } ?>
					<?php if ($this->reservation->fullDeposit && $config->showDepositPrice) {?>
			   			<tr>
							<td class="key"><label><?php echo JText::_('DEPOSIT'); ?>: </label></td>
							<td><strong><?php echo BookingHelper::displayPrice($this->reservation->fullDeposit); ?></strong></td>
						</tr>
					<?php } ?>
					<?php if ($config->showPriceExcludingTax) { ?>
						<tr>
	    					<td class="key"><label><?php echo JText::_('TOTAL_PRICE_EXCLUDING_TAX'); ?>:</label></td>
	    					<td><strong><?php echo BookingHelper::displayPrice(BookingHelper::getPriceExcludingTax($this->reservation, $this->reservedItems)); ?></strong></td>    						
   						</tr>
   					<?php } ?>
   					<?php if ($config->showTax) { ?>
						<tr>
							<td class="key"><label><?php echo JText::_('TAX'); ?>: </label></td>
							<td><strong><?php echo BookingHelper::displayPrice(BookingHelper::getFullTax($this->reservedItems)); ?></strong></td>
						</tr>
					<?php } ?>
					<?php if ($config->showTotalPrice) { ?>
						<tr>
							<td class="key"><label><?php echo JText::_('TOTAL_PRICE'); ?>: </label></td>
							<td><strong><?php echo BookingHelper::displayPrice($this->reservation->fullPrice); ?></strong></td>
						</tr>
					<?php } ?>
					<?php if ($config->showDepositPrice) { ?>
						<tr>
				    		<td class="key"><label><?php echo JText::_('DEPOSIT_MUST_BE_PAID_BEFORE'); ?>: </label></td>
							<td><strong><?php echo $this->depositExpires; ?></strong></td>				
		    			</tr>	
		    		<?php } ?>
	    	</table>
	    </fieldset>    	
   	</div>
   	<div class="clr">&nbsp;</div>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_RESERVATION; ?>"/>
	<input type="hidden" name="boxchecked" value="1"/>
	<input type="hidden" name="cid[]" value="<?php echo $this->reservation->id; ?>"/>
	<input type="hidden" name="task" value=""/>
	<?php echo JHTML::_('form.token'); ?>
</form>