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

$config = AFactory::getConfig();
$user = JFactory::getUser();

?>
<h1><?php echo $this->reservation->id ? JText::sprintf('RESERVATION_NUM', $this->reservation->id) : JText::_('ADD_RESERVATION'); ?></h1>
<div class="edit item-page">
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="bookingToolbar">
		<a class="aIconToolSave tool save" title="<?php echo JText::_('JSave', true); ?>" href="javascript:submitbutton('save')"><?php echo JText::_('JSave', true); ?></a>
		<a class="aIconToolApply tool apply" title="<?php echo JText::_('JApply', true); ?>" href="javascript:submitbutton('apply')"><?php echo JText::_('JApply', true); ?></a>
		<a class="aIconToolCancel tool cancel" title="<?php echo JText::_('JCancel', true); ?>" href="<?php echo JRoute::_(ARoute::view(VIEW_RESERVATIONS)); ?>"><?php echo JText::_('JCancel', true); ?></a>
		<div class="clr"></div>
	</div>
		<fieldset>
    		<legend><?php echo JText::_('CUSTOMER'); ?></legend>
    		<div class="formelm">	
    			<label><?php echo JText::_('CUSTOMER'); ?>:</label>
    			<?php echo JElementCustomer::fetchElement($this->reservation->customer); ?>
			</div>    			
    		<div class="formelm">
    			<label for="title_before"><?php echo JText::_('TITLE_BEFORE'); ?>: </label>
    			<input type="text" name="title_before" id="title_before" maxlength="255" value="<?php echo $this->reservation->title_before; ?>" />
    		</div>
    		<div class="formelm">
    			<label for="firstname"><?php echo JText::_('FIRST_NAME'); ?>: </label>
    			<input type="text" name="firstname" id="firstname" maxlength="255" value="<?php echo $this->reservation->firstname; ?>" />
    		</div>
    		<div class="formelm">
    			<label for="middlename"><?php echo JText::_('MIDDLE_NAME'); ?>: </label>
    			<input type="text" name="middlename" id="middlename" maxlength="255" value="<?php echo $this->reservation->middlename; ?>" />
    		</div>	
    		<div class="formelm">
    			<label for="surname"><?php echo JText::_('SURNAME'); ?>: </label>
    			<input type="text" name="surname" id="surname" maxlength="255" value="<?php echo $this->reservation->surname; ?>" />
    		</div>
    		<div class="formelm">
    			<label for="title_after"><?php echo JText::_('TITLE_AFTER'); ?>: </label>
    			<input type="text" name="title_after" id="title_after" maxlength="255" value="<?php echo $this->reservation->title_after; ?>" />
    		</div>
    		<div class="formelm">
    			<label for="company"><?php echo JText::_('COMPANY'); ?>: </label>
    			<input type="text" name="company" id="company" maxlength="255" value="<?php echo $this->reservation->company; ?>" />
    		</div>
    		<div class="formelm">
    			<label for="company_id"><?php echo JText::_('COMPANY_ID'); ?>: </label>
    			<input type="text" name="company_id" id="company_id" maxlength="255" value="<?php echo $this->reservation->company_id; ?>" />
    		</div>
    		<div class="formelm">
    			<label for="vat_id"><?php echo JText::_('VAT_ID'); ?>: </label>
    			<input type="text" name="vat_id" id="vat_id" maxlength="255" value="<?php echo $this->reservation->vat_id; ?>" />
    		</div>
    		<?php foreach ($this->getCustomFields() as $field) { ?>
	    		<div class="formelm">
			    	<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?>: </label>
			    	<?php echo AHtml::getField($field, $this->reservation->fields); ?>
			 	</div>
			<?php }
			if (!$this->reservation->id) { ?>
				<div class="formelm">
		    		<label for="notify_customer"><?php echo JText::_('NOTIFY_CUSTOMER_BY_E_MAIL'); ?>: </label>
		    		<input type="checkbox" name="notify_customer" id="notify_customer" value="1" />
		    	</div>
		    <?php } ?>
    	</fieldset>
        <?php if ($config->rsMoreNames == 1) { ?>
            <div class="addMore" id="addMoreButton" onclick="ViewReservation.addMoreNames()" style="display: <?php echo empty($this->reservation->more_names) ? 'block' : 'none'; ?>">
                <?php echo JText::_('ADD_MORE_CUSTOMERS'); ?>
            </div>
            <div class="clr"></div>
            <fieldset id="addMoreNames" class="addMoreNames" style="display: <?php echo empty($this->reservation->more_names) ? 'none' : 'block'; ?>">
                <legend><?php echo JText::_('MORE_CUSTOMERS'); ?></legend>
                <?php if (!empty($this->reservation->more_names)) {
                    foreach ($this->reservation->more_names as $name) { ?>
                        <input type="text" name="more_names[]" value="<?php echo $this->escape($name); ?>" />
                <?php }
                } else { ?>
                    <input type="text" name="more_names[]" value="" />
                    <input type="text" name="more_names[]" value="" />
                    <input type="text" name="more_names[]" value="" />
                <?php } ?>
                <div class="addNext" id="addNextButton" onclick="ViewReservation.addNextName()">
                    <?php echo JText::_('ADD_NEXT'); ?>
                </div>
                <div class="hideAddMore" onclick="ViewReservation.hideAddMoreNames()">
                    <?php echo JText::_('HIDE_ADD_MORE_CUSTOMERS'); ?>
                </div>                                        
            </fieldset>
        <?php } ?>
    	<div id="reservedItems">
    	<?php if ($this->ajaxForItems) { ob_clean(); ob_start(); } ?>
    <?php     	
    	$z=0;
		$countReservedItems = count($this->reservedItems);
    	if ($countReservedItems)
    		foreach ($this->reservedItems as $key => $reservedItem){
    			/* @var $reservedItem TableReservationItems */
    			TableReservationItems::display($reservedItem);
				$id = $z++;
                $iID = (int) $reservedItem->id;
				$subject = $this->subjects[$reservedItem->subject];

	?>
    	<fieldset id="reservationItem<?php echo $reservedItem->id ? $reservedItem->id : $key; ?>">
    		<legend><?php echo $reservedItem->subject_title; ?></legend>
    		
    		<input type="hidden" name="id[<?php echo $id?>]" value="<?php echo $reservedItem->id?>">
    		<input type="hidden" name="subject[<?php echo $id?>]" value="<?php echo $reservedItem->subject?>">
    		<input type="hidden" name="subject_title[<?php echo $id?>]" value="<?php echo $reservedItem->subject_title?>">
    		<input type="hidden" name="rtype[<?php echo $id?>]" value="<?php echo $reservedItem->rtype?>">
    		<input type="hidden" name="ctype[<?php echo $id?>]" value="<?php echo @$reservedItem->ctype; ?>" />
    		<?php if (!empty($reservedItem->boxIds)) { ?>
    			<?php foreach ($reservedItem->boxIds as $bid) { ?>
					<input type="hidden" name="boxIds[<?php echo $id?>][]" value="<?php echo $bid; ?>" />
				<?php } ?>
			<?php } else { ?>
				<input type="hidden" name="boxIds[<?php echo $id?>][]" value="" />
			<?php } ?>
    		
    		<div class="formelm">
    			<label></label>
   				<a class="aIconLegend aIconUnpublish" href="javascript:removeReservationItem('<?php echo $reservedItem->id ? $reservedItem->id : $key; ?>', '<?php echo (int) $this->reservation->id; ?>')" title=""><?php echo JText::_('REMOVE_ITEM'); ?></a>
   			</div>
	    	<div class="formelm">
	    		<label for="subject_title[<?php echo $id?>]"><?php echo JText::_('SUBJECT_TITLE'); ?>: </label>
                <input type="text" name="subject_title[<?php echo $id?>]" id="itemTitle<?php echo $iID; ?>" maxlength="255" value="<?php echo $reservedItem->subject_title; ?>" readonly="true" />
                
                <?php if ($this->reservation->id && $user->authorise('booking.reservation.edit.item', 'com_booking')) { ?>
                    <span id="changeItemSelect<?php echo $iID; ?>"></span>
                    <a class="aIconEditInline aIconEdit" href="javascript:ViewReservation.openChangeItem(<?php echo $iID; ?>)" id="openChangeItem<?php echo $iID; ?>"></a>
                    <a class="aIconEditInline aIconTick" href="javascript:ViewReservation.changeItem(<?php echo $iID; ?>)" id="changeItem<?php echo $iID; ?>" style="display: none"></a>
                    <a class="aIconEditInline aIconUnpublish" href="javascript:ViewReservation.closeChangeItem(<?php echo $iID; ?>)" id="closeChangeItem<?php echo $iID; ?>" style="display: none"></a>
                <?php } ?>
                
    		</div>
    		<?php if ($config->parentsBookable == 2) { ?>
				<div class="formelm">	
   					<label for="sub_subject-<?php echo $id?>"><?php echo JText::_('SUB_ITEM'); ?>:</label>
                    <?php echo BookingHelper::getSubjectSelectBox($reservedItem->sub_subject, "sub_subject[$id]", false, $reservedItem->subject); ?>
   				</div>
    	    <?php } ?>
    		<div class="formelm">
    			<label><?php echo AHtml::intervalLabel($reservedItem); ?>: </label>
                <?php echo AHtml::interval($reservedItem); ?>
                <input type="hidden" name="from[<?php echo $id; ?>]" value="<?php echo $reservedItem->from; ?>" />
                <input type="hidden" name="to[<?php echo $id; ?>]" value="<?php echo $reservedItem->to; ?>" />
                <?php if ($this->reservation->id && $user->authorise('booking.reservation.edit.date', 'com_booking')) { ?>
                    <a class="aIconEditInline aIconEdit" href="javascript:ViewReservation.openChangeDate(<?php echo (int) $reservedItem->subject; ?>, <?php echo (int) $iID; ?>, <?php echo (int) $reservedItem->rtype; ?>)" id="openChangeDate<?php echo $iID; ?>"></a>
                <?php } ?>
    		</div>    		
    		<div class="formelm">
    			<label for="capacity[<?php echo $id?>]"><?php echo JText::_('CAPACITY'); ?>: </label>
    			<select name="capacity[<?php echo $id?>]" id="capacity[<?php echo $id?>]" <?php if (!$this->reservation->id) { ?>onchange="refreshReservation()"<?php } ?>>
    				<?php for ($i = 1; $i <= $subject->total_capacity; $i++) { ?>
    					<option value="<?php echo $i; ?>"<?php if ($i == $reservedItem->capacity) { ?> selected="selected"<?php } ?>>
    						<?php echo $i; ?>
    					</option>
    				<?php } ?>
    			</select>
    		</div>
    		<div class="formelm">
    			<label for="price"><?php echo ITEM_PRICE_TIP ?>: </label>
    			<input type="text" name="price[<?php echo $id?>]" id="price[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->price; ?>" <?php if (!$this->reservation->id) { ?>disabled="disabled"<?php } ?> />
    			<?php echo $config->mainCurrency; ?>
    		</div>    		
    		<?php 
				foreach($subject->occupancy_types as $otype) {
					$min = $otype->type == 0 ? $subject->standard_occupancy_min : $subject->extra_occupancy_min;
					$max = $otype->type == 0 ? $subject->standard_occupancy_max : $subject->extra_occupancy_max;
			?>
				<div class="formelm">
					<label for="occupancy<?php echo $otype->id; ?>"><?php echo $otype->title; ?>: </label></td>
    					<?php if (!$this->reservation->id) { ?>
							<select name="occupancy[<?php echo $id?>][<?php echo $otype->id; ?>]" <?php if (!$this->reservation->id) { ?>onchange="refreshReservation()"<?php } ?>>
								<option value="0">-</option>
									<?php echo JHtml::_('select.options', array_combine(range($min, $max), range($min, $max)), '', '', $reservedItem->occupancy[$otype->id]['count']); ?>
							</select>
							<input type="text" name="occupancy[<?php echo $id?>][<?php echo $otype->id; ?>][total]" value="<?php echo $reservedItem->occupancy[$otype->id]['total']; ?>" size="1" disabled="disabled" />
							<?php echo $config->mainCurrency; ?>
						<?php } else { ?>
							<select name="occupancy[<?php echo $id?>][<?php echo $otype->id; ?>][count]" <?php if (!$this->reservation->id) { ?>onchange="refreshReservation()"<?php } ?>>
								<option value="0">-</option>
									<?php echo JHtml::_('select.options', array_combine(range($min, $max), range($min, $max)), '', '', $reservedItem->occupancy[$otype->id]['count']); ?>
							</select>
							<?php foreach ($reservedItem->occupancy[$otype->id] as $var => $val) { ?>
								<?php if ($var == 'total') { ?>
									<input type="text" name="occupancy[<?php echo $id?>][<?php echo $otype->id; ?>][<?php echo $var; ?>]" value="<?php echo $val; ?>" size="1" />
									<?php echo $config->mainCurrency; ?>
								<?php } elseif ($var != 'count') { ?>
									<input type="hidden" name="occupancy[<?php echo $id?>][<?php echo $otype->id; ?>][<?php echo $var; ?>]" value="<?php echo $val; ?>" />
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</div>
				<?php } ?>
    		<?php 
    			foreach ($reservedItem->supplementsRaw as $supplement) {
    				$value = $capacity = $fullPrice = null;
					foreach ($reservedItem->supplements as $item) {
						$toCompare = $this->reservation->id ? $item->supplement : $item->id;
						if ($toCompare == $supplement->id) {
							$value = $item->value;
							$capacity = $item->capacity;
							$fullPrice = $item->fullPrice;
							break;
						}
					}
				?>
					<div class="formelm">
						<label><?php echo $supplement->title; ?>: </label>
    					<?php echo BookingHelper::displaySupplementInput($supplement, $value, $capacity, $id, ($this->reservation->id ? '' : 'onchange="refreshReservation()"'));
    					if ($fullPrice)
    						echo JText::_('FULL') . ': ' . BookingHelper::displayPrice($fullPrice); ?>
    				</div>  	
    		<?php } ?>                                                                        
    		<div class="formelm">	
    			<label for="provision"><?php echo JText::_('PROVISION'); ?>: </label>
    			<input type="text" name="provision[<?php echo $id?>]" id="provision[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->provision; ?>" <?php if (!$this->reservation->id) { ?>disabled="disabled"<?php } ?> />
    			<?php echo $config->mainCurrency; ?>
    		</div>                                                                        
    		<div class="formelm">	
    			<label for="deposit"><?php echo ITEM_DEPOSIT_TIP ?>: </label>
    			<input type="text" name="deposit[<?php echo $id?>]" id="deposit[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->deposit; ?>" <?php if (!$this->reservation->id) { ?>disabled="disabled"<?php } ?> />
    			<?php echo $config->mainCurrency; ?>
    		</div>
    		<div class="formelm">
    			<label for="fullDeposit"><?php echo FULL_DEPOSIT_TIP ?>: </label>
    			<input type="text" name="fullDeposit[<?php echo $id?>]" id="fullDeposit[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->fullDeposit; ?>" <?php if (!$this->reservation->id) { ?>disabled="disabled"<?php } ?> />
    			<?php echo $config->mainCurrency; ?>
    		</div>
    		<div class="formelm">	
    			<label for="priceExcludingTax"><?php echo JText::_('TOTAL_PRICE_EXCLUDING_TAX'); ?>: </label>
    			<input type="text" name="priceExcludingTax[<?php echo $id?>]" id="priceExcludingTax[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo round(BookingHelper::getPriceExcludingTax(null, $reservedItem), 2); ?>" disabled="disabled" />
    			<?php echo $config->mainCurrency; ?>
    		</div>
    		<div class="formelm">	
    			<label for="tax"><?php echo JText::_('TAX'); ?>: </label>
    			<input type="text" name="tax[<?php echo $id?>]" id="tax[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->tax; ?>" <?php if (!$this->reservation->id) { ?>disabled="disabled"<?php } ?> />
    			%
    		</div>    		    		    		
    		<div class="formelm">
    			<label for="fullPriceSupplements"><?php echo FULL_PRICE_TIP ?>: </label>
    			<input type="text" name="fullPriceSupplements[<?php echo $id?>]" id="fullPriceSupplements[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->fullPriceSupplements; ?>" <?php if (!$this->reservation->id) { ?>disabled="disabled"<?php } ?> />
    			<?php echo $config->mainCurrency; ?>
    		</div>
    		<div class="formelm">
    			<label for="message[<?php echo $id?>]"><?php echo JText::_('Message') ?></label>
    			<textarea rows="5" cols="50" name="message[<?php echo $id?>]" id="message[<?php echo $id?>]"><?php echo $reservedItem->message; ?></textarea>
    		</div>
            <?php if ($config->rsMoreNames > 1) {
                $persons = $reservedItem->capacity - 1;
                foreach ($reservedItem->occupancy as $occupancy) {
                    if ($occupancy['count']) {
                        $persons += $occupancy['count'];
                    }
                }
                $inc = $reservedItem->occupancy ? 1 : 2;
                for ($q = 0; $q < $persons; $q++) { ?>
                    <div class="formelm more_names">
                        <label>
                            <?php echo JText::sprintf('PERSON_NUM', ($q + $inc)); ?>:
                        </label>
                        <div class="field">
                            <label for="more_names_firstname<?php echo $id.'-'.$q; ?>">
                                <?php echo JText::_('FIRST_NAME'); ?>
                            </label>
                            <input type="text" name="more_names[<?php echo $id; ?>][<?php echo $q; ?>][firstname]" id="more_names_firstname<?php echo $id.'-'.$q; ?>" value="<?php echo $this->escape(@$reservedItem->more_names[$q]->firstname); ?>" />  
                        </div>
                        <div class="field">
                            <label for="more_names_surname<?php echo $id.'-'.$q; ?>">
                                <?php echo JText::_('SURNAME'); ?>
                            </label>
                            <input type="text" name="more_names[<?php echo $id;?>][<?php echo $q; ?>][surname]" id="more_names_surname<?php echo $id.'-'.$q; ?>" value="<?php echo $this->escape(@$reservedItem->more_names[$q]->surname); ?>" />
                        </div>
                    </div>
            <?php   }
                } ?>      
    		<?php if (!$this->reservation->id) { ?>   
    			<div class="formelm">
    				<button class="button" onclick="return refreshReservation()"><?php echo JText::_('REFRESH'); ?></button>
    			</div> 			
    		<?php } ?>                
    	</fieldset>
    <?php } ?>
    <?php if ($this->ajaxForItems) $ajaxOutput['items'] = ob_get_clean(); ?>
    </div>
    <div id="reservationTotal">
    	<?php if ($this->ajaxForItems) { ob_clean(); ob_start(); } ?>
    	<fieldset>
    		<legend><?php echo JText::_('RESERVATION_STATUS_AND_PAYMENT'); ?></legend>
    		<div class="formelm">
    			<label for="fax"><?php echo JText::_('PAYMENT_STATUS'); ?>: </label>
    			<?php echo JHtml::_('select.genericlist', BookingHelper::getPaymentStatuses(), 'paid', '', 'id', 'label', $this->reservation->paid); ?>
			</div>
			<div class="formelm">    	
				<label for="fax"><?php echo JText::_('RESERVATION_STATUS'); ?>: </label>
				<select name="state" id="state">
					<option value="<?php echo RESERVATION_PRERESERVED; ?>" <?php if ($this->reservation->state == RESERVATION_PRERESERVED) { ?>selected="selected"<?php } ?>>
    					<?php echo JText::_('PRE_RESERVED'); ?></option>
    				<option value="<?php echo RESERVATION_ACTIVE; ?>" <?php if ($this->reservation->state == RESERVATION_ACTIVE) { ?>selected="selected"<?php } ?>>
    					<?php echo JText::_('RESERVED'); ?></option>
    				<option value="<?php echo RESERVATION_STORNED; ?>" <?php if ($this->reservation->state == RESERVATION_STORNED) { ?>selected="selected"<?php } ?>>
    					<?php echo JText::_('CANCELLED'); ?></option>
    				<option value="<?php echo RESERVATION_TRASHED; ?>" <?php if ($this->reservation->state == RESERVATION_TRASHED) { ?>selected="selected"<?php } ?>>
    					<?php echo JText::_('TRASHED'); ?></option>
    				<option value="<?php echo RESERVATION_CONFLICTED; ?>" <?php if ($this->reservation->state == RESERVATION_TRASHED) { ?>selected="selected"<?php } ?>>
    					<?php echo JText::_('CONFLICTED'); ?></option>
    			</select>
			</div>
    		            <div class="formelm">
                <label for="provision"><?php echo JText::_('TOTAL_PROVISION'); ?>: </label>
                <strong><?php echo BookingHelper::displayPrice($this->reservation->fullProvision); ?></strong>
            </div>
		   	<div class="formelm">
				<label><?php echo JText::_('DEPOSIT'); ?>: </label>
				<strong><?php echo BookingHelper::displayPrice($this->reservation->fullDeposit); ?></strong>
			</div>
			<div class="formelm">
			    <label><?php echo JText::_('DEPOSIT_MUST_BE_PAID_BEFORE'); ?>: </label>
				<strong><?php echo $this->depositExpires; ?></strong>				
	    	</div>			    		
			<div class="formelm">
				<label><?php echo JText::_('TAX'); ?>: </label>
				<strong><?php echo BookingHelper::displayPrice(BookingHelper::getFullTax($this->reservedItems)); ?></strong>
			</div>
    		<div class="formelm">
				<label><?php echo JText::_('TOTAL_PRICE'); ?>: </label>
				<strong><?php echo BookingHelper::displayPrice($this->reservation->fullPrice); ?></strong>
			</div>			
    	</fieldset>
       	<?php 
    		if ($this->ajaxForItems) { 
    			$ajaxOutput['total'] = ob_get_clean();
    			$ajaxOutput = json_encode($ajaxOutput);
    			die($ajaxOutput);
    		}
    	?>
    	</div>
    	<fieldset>
    		<legend><?php echo JText::_('CONTACT'); ?></legend>
    		<div class="formelm">
    			<label for="street"><?php echo JText::_('STREET'); ?>: </label>
    			<input type="text" name="street" id="street" maxlength="255" value="<?php echo $this->reservation->street; ?>" />
    		</div>
    		<div class="formelm">
    			<label for="city"><?php echo JText::_('CITY'); ?>: </label>
    			<input type="text" name="city" id="city" maxlength="255" value="<?php echo $this->reservation->city; ?>" />
    		</div>
    		<div class="formelm">
    			<label for="zip"><?php echo JText::_('ZIP'); ?>: </label>
    			<input type="text" name="zip" id="zip" maxlength="255" value="<?php echo $this->reservation->zip; ?>" />
    		</div>
    		<div class="formelm">
   				<label for="country"><?php echo JText::_('COUNTRY'); ?>: </label>
    			<input type="text" name="country" id="country" maxlength="255" value="<?php echo $this->reservation->country; ?>" />
    		</div>
    		<div class="formelm">
    			<label for="email"><?php echo JText::_('EMAIL'); ?>: </label>
    			<input type="text" name="email" id="email" maxlength="255" value="<?php echo $this->reservation->email; ?>" />
    		</div>
    		<div class="formelm">
    			<label for="telephone"><?php echo JText::_('TELEPHONE'); ?>: </label>
    			<input type="text" name="telephone" id="telephone" maxlength="255" value="<?php echo $this->reservation->telephone; ?>" />
    		</div>
    		<div class="formelm">
    			<label for="fax"><?php echo JText::_('FAX'); ?>: </label>
    			<input type="text" name="fax" id="fax" maxlength="255" value="<?php echo $this->reservation->fax; ?>" />
    		</div>
    		<div class="formelm">
		    	<label for="note"><?php echo JText::_('NOTE'); ?></label>
		    	<textarea name="note" id="note" cols="50" rows="5"><?php echo $this->reservation->note; ?></textarea>
		    </div>
    	</fieldset>
	<div class="bookingToolbar">
		<a class="aIconToolSave tool save" title="<?php echo JText::_('JSave', true); ?>" href="javascript:submitbutton('save')"><?php echo JText::_('JSave', true); ?></a>
		<a class="aIconToolApply tool apply" title="<?php echo JText::_('JApply', true); ?>" href="javascript:submitbutton('apply')"><?php echo JText::_('JApply', true); ?></a>
		<a class="aIconToolCancel tool cancel" title="<?php echo JText::_('JCancel', true); ?>" href="<?php echo JRoute::_(ARoute::view(VIEW_RESERVATIONS)); ?>"><?php echo JText::_('JCancel', true); ?></a>
		<div class="clr"></div>
	</div>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>" />
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_RESERVATION; ?>" />
	<input type="hidden" name="boxchecked" value="1" />
	<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $this->reservation->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>
</div>