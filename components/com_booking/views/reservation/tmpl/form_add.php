<?php

/**
 * Reservation add form template. Displayed in iframe when clicked Book-It
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



$titleCheckout = $this->escape(JText::_('SAVE_CHECKOUT'));
$titleContinue = $this->escape(JText::_('SAVE_CONTINUE'));
$titleStorno = $this->escape(JText::_('BACK'));

$config = &AFactory::getConfig();
$document = &JFactory::getDocument();
/* @var $document JDocument */

$document->addStyleDeclaration('#main {min-height:100px!important}'); //override of stylesheet

//compare object in basket with new adding object - If is using custom config for owner of object, only same object can be reserved
$err = false;
$session = JFactory::getApplication()->getUserState('com_booking.user_reservation_items');
if(is_array($session) && AUser::$id) { //current subject has custom setting
	if($first = reset($session)) {	
		foreach($this->reservedItems as $item) {
			if($item->subject != $first['subject']) {
				$err = true;
				break;
			}
		}
	}
}
else if(is_array($session)) { //check custom config for subject in session
	if($first = reset($session)) {
		$model = new BookingModelSubject();
		$model->setId($first['subject']);
		if($user = $model->getObject()->user_id) {
			$model = new BookingModelUserConfig();
			if($data = $model->allDataForUser($user)) {
				$err = true;
			}
		}
	}
}
if($err) {
	$this->reservedItems = null;
	echo JText::_('CAN_NOT_RESERVE_WITH_EARLY_SELECTED_OBJECT_MAKE_A_NEW_RESERVATION');
}

if (count($this->reservedItems)){
	$newItemSubject = $this->subjects[end($this->reservedItems)->subject];
	$backUrl = JRoute::_(ARoute::view(VIEW_SUBJECT, $newItemSubject->id, $newItemSubject->alias));
}
else
	$backUrl = JRoute::_(ARoute::root());
?>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm" class="reservation">
    <h1><?php echo JText::_('ADD_RESERVATION') ?></h1>

    <?php 
		$z=0;
		$countReservedItems = count($this->reservedItems);
    	if ($countReservedItems)
    		foreach ($this->reservedItems as $reservedItem){
    			/* @var $reservedItem TableReservationItems */
				$id = $z++;
				$subject = $this->subjects[$reservedItem->subject];
		
	?>
    <div class="reservation">
    	<fieldset>
    		<legend><?php echo $subject->title; ?></legend>
    		<table>
    			<tr>
    				<?php if (isset($reservedItem->key)) { ?>
    				<td rowspan="15" valign="top" width="1%">
    					<a href="<?php echo JRoute::_(ARoute::customUrl(array('key' => $reservedItem->key)).ARoute::controller(CONTROLLER_RESERVATION).ARoute::task('remove_item')); ?>">
    						<img src="<?php echo IMAGES?>icon-16-cancel.png" title="<?php echo JText::_('REMOVE_ITEM_FROM_RESERVATION')?>" alt="<?php echo JText::_('REMOVE_ITEM_FROM_RESERVATION')?>">
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
		    					<td class="key"><?php echo AHtml::intervalLabel($reservedItem,0); ?>: </td>
		    					<td><?php echo AHtml::interval($reservedItem,0); ?></td>
		    				</tr>
		    			<?php } ?>
    			<?php 
    				if (is_object($reservedItem->box)) {
    					
    					if ($subject->display_capacity || $subject->total_capacity>1) {
    						
    						$max = $subject->total_capacity-$reservedItem->box->maxReserved;
    			?>
	    				<tr>	
							<td class="key"><?php echo JText::_('CAPACITY'); ?>:</td>
			    			<td>
			    				<?php if ($max>1 && empty($reservedItem->occupancy)) { // each item has capacity 1 if occupancy is used ?>
				    				<?php if ($max<=100) { ?>
				    				<select class="capacity" name="capacity[<?php echo $id?>]" id="capacity[<?php echo $id?>]">
				    					<?php for ($i = 1; $i <= $max; $i++) { ?>
				    						<option value="<?php echo $i; ?>" <?php if ($i == $reservedItem->capacity) { ?>selected="selected"<?php } ?>><?php echo $i; ?></option>
				    					<?php } ?>
				    				</select>
				    				<?php } else { ?>
				    					<input class="capacity" name="capacity[<?php echo $id?>]" id="capacity[<?php echo $id?>]" value="<?php echo $reservedItem->capacity; ?>" style="float: right">
				    				<?php } ?>
			    				<?php } else { ?>
			    				1
			    				<?php } ?>
			    			</td>
			    		</tr>
		    	<?php 
    				}
    			?>		    			
						<?php if ($config->showUnitPrice) { ?>
		    				<tr>	
		    					<td class="key"><?php echo ITEM_PRICE_TIP ?>:</td>
			    				<td><?php echo BookingHelper::displayPrice($reservedItem->price, null, $reservedItem->tax); ?></td>
			    			</tr>
			    		<?php } ?>
			    		<?php foreach ($reservedItem->occupancy as $occupancy) { ?>
			    			<?php if ($occupancy['count']) { ?>
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
			    		<?php } ?>
			    		<?php foreach ($reservedItem->supplements as $supplement) {
	    					/* @var $supplement TableSupplement */ ?>
	    					<tr>
	    						<td class="key hasTip" title="<?php echo BookingHelper::displaySupplementTooltip($supplement); ?>"><?php echo $supplement->title; ?>: </td>
		    					<td>
		    						<?php echo BookingHelper::displaySupplementValue($supplement, $reservedItem->tax, true, $id); ?>
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
		    					<td class="key"><?php echo ITEM_DEPOSIT_TIP ?>:</td>
		    					<td><?php echo BookingHelper::displayPrice($reservedItem->deposit, null, $reservedItem->tax); ?></td>
		    				</tr>
    					<?php } ?>    					
    					<?php if ($reservedItem->fullDeposit && $config->showDepositPrice) { ?>
		    				<tr>	
		    					<td class="key"><?php echo FULL_DEPOSIT_TIP ?>:</td>
		    					<td><?php echo BookingHelper::displayPrice($reservedItem->fullDeposit, null, $reservedItem->tax); ?></td>
		    				</tr>
    					<?php } ?>
		    			<?php if ($config->showPriceExcludingTax) { ?>
    						<tr>
		    					<td class="key" nowrap="nowrap"><?php echo JText::_('TOTAL_PRICE_EXCLUDING_TAX'); ?>:</td>
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
		    					<td class="key"><?php echo JText::_('TOTAL_PRICE'); ?>: </td>
		    				<td><?php echo BookingHelper::displayPrice($reservedItem->fullPriceSupplements, null, $reservedItem->tax); ?></td>
		    				</tr>
		    			<?php } ?>
		    			<?php if ($reservedItem->cancel_time !== null) { ?>
		    			<tr>
		    				<td class="key"><?php echo JText::_('DEPOSIT_MUST_BE_PAID_BEFORE'); ?>: </td>
		    				<td><?php echo BookingHelper::formatExpiration($reservedItem->cancel_time,$reservedItem->from); ?></td>
						</tr>
						<?php } ?>
    				<?php } ?>
 
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

   	<div class="bookingToolbar">
   		<table>
   		<tr>
		<td><a class="aIconToolSave tool save" title="<?php echo $titleCheckout; ?>" href="javascript:submitbutton('add_checkout')" ><?php echo $titleCheckout; ?></a></td>
		<?php if($config->moreReservations){?><td><a class="aIconToolSave tool save" title="<?php echo $titleContinue; ?>" href="javascript:submitbutton('add_continue')" ><?php echo $titleContinue; ?></a></td><?php }?>
		<td><a class="aIconToolCancel tool save" title="<?php echo $titleStorno; ?>" href="javascript:void(0)" onclick="window.parent.SqueezeBox.close()" ><?php echo $titleStorno; ?></a></td>
		</tr>
		</table>
		
		<div class="clr"></div>
	</div>
	
	<input type="hidden" name="option" value="<?php echo OPTION; ?>" />
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_RESERVATION; ?>" />
	<input type="hidden" name="task" value="add_checkout" />
	<input type="hidden" name="view" value="reservation" />
	<input type="hidden" name="layout" value="form" />
	<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>" />
	<?php echo JHTML::_('form.token'); ?> 
</form> 