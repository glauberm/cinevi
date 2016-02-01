<?php
/**
 * Reservations list template.
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

$config = AFactory::getConfig();

/* @var $this BookingViewReservations */

JHTML::_('script', JURI::root(true) . 'components/com_booking/assets/libraries/mavsuggest/mavsuggest.js');
JHTML::_('stylesheet', JURI::root(true) . 'components/com_booking/assets/libraries/mavsuggest/mavsuggest.css');
if (ISJ3) {
	JHTML::_('dropdown.init');
	JHTML::_('formbehavior.chosen', 'select');
}
$print = JRequest::getInt('print');
if ($print)
    AImporter::css('print');
?>
<script type="text/javascript">
    // <![CDATA[
    	window.addEvent('domready', function() {
        	new MavSuggest.Request.JSON({
            	'elem': 'filter_items-subject_title',
            	'url': '<?php echo JRoute::_('index.php?option=com_booking&controller=subject&task=suggest', false); ?>'
        	});
    		<?php if ($print) { ?>
				window.print();
	        <?php } ?>        	        	
    	});
    // ]]>
</script>
<h1><?php echo JText::_('RESERVATION_LIST'); ?></h1>
<a href="<?php echo JRoute::_(ARoute::view(VIEW_RESERVATIONS, '', '', array('layout' => 'customer', 'tmpl' => 'component', 'print' => 1))); ?>" target="_blank" class="noprint">
    <?php echo JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true); ?>
</a>
<form action="<?php echo JRoute::_(ARoute::viewlayout(VIEW_RESERVATIONS, 'customer')); ?>" method="post" name="adminForm" id="adminForm" class="registration">
	<div class="filter noprint">
		<div class="filterItem">
			<label for="filter_reservation-id" id="filter_resid_label"><?php echo JText::_('RES_NUM'); ?>: </label>
			<input type="text" name="filter_reservation-id" id="filter_reservation-id" onchange="this.form.submit();" value="<?php echo $this->escape($this->lists['reservation-id']); ?>" placeholder="<?php echo ISJ3 ? JText::_('RES_NUM') : ''; ?>" size="1" class="inputbox input-mini" />
			<label for="filter_items-subject_title" id="filter_subject_label"><?php echo JText::_('ITEM'); ?>: </label>
			<input type="text" name="filter_items-subject_title" id="filter_items-subject_title" size="15" class="inputbox input-medium" onchange="this.form.submit();" value="<?php echo $this->escape($this->lists['items-subject_title']); ?>"/>
		</div>
		<div class="filterItem">			
			<label for="filter_from" id="filter_from_label"><?php echo JText::_('FROM'); ?>: </label>
			<?php echo AHtml::getCalendar($this->lists['from'], 'filter_from', 'filter_from', ADATE_FORMAT_LONG, ADATE_FORMAT_MYSQL_DATETIME_CAL, '', true, 0); ?>
			<label for="filter_to" id="filter_to_label"><?php echo JText::_('TO'); ?>: </label>
			<?php echo AHtml::getCalendar($this->lists['to'], 'filter_to', 'filter_to', ADATE_FORMAT_LONG, ADATE_FORMAT_MYSQL_DATETIME_CAL, '', true, 0); ?>
		</div>
		<div class="filterItem">
			<?php 
				$options = array();
				$options[] = JHtml::_('select.option', '', '- ' . JText::_('RESERVATION_STATUS') . ' -');
				$options[] = JHtml::_('select.option', RESERVATION_ACTIVE, JText::_('RESERVED'));
				$options[] = JHtml::_('select.option', RESERVATION_STORNED, JText::_('CANCELLED'));
				echo JHtml::_('select.genericlist', $options, 'filter_reservation_status', 'onchange="this.form.submit()"', 'value', 'text', $this->lists['reservation_status']);
			if ($config->showPaymentStatus) { ?>
				<select id="filter_payment_status" class="inputbox" onchange="this.form.submit()" name="filter_payment_status">
					<option value="">- <?php echo JText::_('PAYMENT_STATUS'); ?> -</option>
					<?php echo JHtml::_('select.options', BookingHelper::getPaymentStatuses(), 'id', 'label', $this->lists['payment_status']); ?>
				</select>
			<?php } ?>
		</div>
		<div class="buttons">
			<input class="button btn btn-primary" type="submit" onclick="this.form.submit();" value="<?php echo $this->escape(JText::_('GO')); ?>" />
			<input class="button btn btn-primary" type="submit" onclick="this.form.reset.value=1; this.form.submit();" value="<?php echo $this->escape(JText::_('RESET')); ?>" />
		</div>
		<div class="clr"></div>
	</div>
	<table class="category table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th nowrap="nowrap">
					<span class="hasTip" title="<?php echo $this->escape(JText::_('RESERVATION_NUMBER')); ?>">
						<?php echo JText::_('RES_NUM'); ?>
					</span>
				</th>
				<th nowrap="nowrap">
				    <?php echo JHTML::_('grid.sort', 'Item', 'items-subject_title', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th nowrap="nowrap">
					<span class="hasTip" title="<?php echo $this->escape(JText::_('CAPACITY')); ?>">
						<?php echo JText::_('CAP'); ?>
					</span>
				</th>
				<?php if ($config->showOccupancyColumn) { ?>
					<th nowrap="nowrap">
						<span class="hasTip" title="<?php echo $this->escape(JText::_('OCCUPANCY')); ?>">
						    <?php echo JText::_('OCC'); ?>
						</span>
					</th>
				<?php } ?>
                <?php if ($config->showSupplementsColumn) { ?>
					<th width="1%" nowrap="nowrap">
				        <?php echo JText::_('SUPPLEMENTS'); ?>
                    </th>
				<?php } ?>                                                                
				<?php if ($config->showTotalPrice || $config->showDepositPrice) { ?>
					<th nowrap="nowrap">
						<?php if ($config->showTotalPrice) {
				    		echo JHTML::_('grid.sort', 'Price', 'reservationFullPrice', $this->lists['order_Dir'], $this->lists['order']); ?>
				    		<?php if ($config->showDepositPrice) { ?>
								<br/>
							<?php }
							} ?>
						<?php if ($config->showDepositPrice) {
				    		echo JHTML::_('grid.sort', 'Deposit', 'reservationFullDeposit', $this->lists['order_Dir'], $this->lists['order']);
				    	} ?>
					</th>
				<?php } ?>				
				<th colspan="2" nowrap="nowrap">
				    <?php echo JHTML::_('grid.sort', 'From', 'items-from', $this->lists['order_Dir'], $this->lists['order']); ?>
					<br/>
				    <?php echo JHTML::_('grid.sort', 'To', 'items-to', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th nowrap="nowrap" class="noprint">
					<?php if ($config->showPaymentStatus) { ?>
				    	<span class="hasTip" title="<?php echo $this->escape(JText::_('PAYMENT_STATUS')); ?>"><?php echo JText::_('PAY_STAT'); ?></span>
						<br/>
					<?php } ?>
					<span class="hasTip" title="<?php echo $this->escape(JText::_('RESERVATION_STATUS')); ?>"><?php echo JText::_('RES_STAT'); ?></span>
				</th>
				<th nowrap="nowrap" class="noprint">
					<?php echo JText::_('JCancel'); ?>
				</th>                
			</tr>
		</thead>
		<tbody>
			<?php if (! is_array($this->items) || ! count($this->items)) { ?>
				<tr><td colspan="9"><?php echo JText::_('NO_ITEMS_FOUND'); ?></td></tr>
			<?php 
				} else {
					foreach ($this->items as $i => $reservation) {
						/* @var $item TableReservation */
						foreach ($this->reservedItems[$reservation->id] as $j => $reservedItem) {
							TableReservationItems::display($reservedItem);
			?>
					    <tr class="row<?php echo $i % 2; ?> cat-list-row<?php echo $i % 2; ?>">
					    	<?php if ($j == 0) { ?>
					    		<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" align="center">
					    			<a href="<?php echo JRoute::_(ARoute::view(VIEW_RESERVATION, null, null, array('cid' => $reservation->id))); ?>" title="<?php echo $this->escape(JText::_('DISPLAY_RESERVATION_DETAIL')); ?>"><?php echo $reservation->id; ?></a>
					    		</td>
					    	<?php } ?>
					    	<?php if (!$reservedItem) { ?>
					    		<td colspan="4">No subject reserved</td>
					    	<?php } else { ?>
						    	<td>
						    		<?php if (! is_null($reservedItem->subjectId)) { ?>
						    			<a href="<?php echo JRoute::_(ARoute::view(VIEW_SUBJECT, $reservedItem->subject, $reservedItem->subjectAlias)); ?>" title="<?php echo $this->escape(sprintf($this->escape(JText::_('DISPLAY_OBJECT_S')), $reservedItem->subject_title)); ?>"><?php echo $reservedItem->subject_title; ?></a>
						    		<?php } else { ?>
						    			<span title="<?php echo $this->escape(JText::_('SUBJECT_NOT_FOUND')); ?>"><?php echo $reservedItem->subject_title; ?></span>
						    		<?php } ?>
						    	</td>
						    	<td>
						    		<span class="badge badge-info"><?php echo $reservedItem->capacity; ?></span>
						    	</td>
						    	<?php if ($config->showOccupancyColumn) { ?>
						    		<td nowrap="nowrap">
				    				    <?php foreach ($reservedItem->occupancy as $oitem)
				    					    echo JArrayHelper::getValue($oitem, 'title') . ': ' . JArrayHelper::getValue($oitem, 'count') . '<br/>'; ?>
				    				</td>
				    			<?php } ?>
                                <?php if ($config->showSupplementsColumn) { ?>
                                    <td>
                                        <?php 
                                            if (!empty($this->reservedSupplements[$reservedItem->id])) {
                                                echo AHtml::showSupplementsColumn($this->reservedSupplements[$reservedItem->id]);
                                            }
                                        ?>
                                    </td>
                                <?php } ?>
						    	<?php if ($j == 0 && ($config->showTotalPrice || $config->showDepositPrice)) { ?>
						    		<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" nowrap="nowrap">
						    			<?php if ($config->showTotalPrice) { ?>
						    				<span class="badge badge-info"><?php echo BookingHelper::displayPrice($reservation->reservationFullPrice); ?></span>
						    				<?php if ($config->showDepositPrice) { ?>
						    					<br/><br/>
						    				<?php } ?> 
						    			<?php } ?>
						    			<?php if ($config->showDepositPrice) { ?>
						    				<span class="badge badge-info hasTip" title="<?php echo $this->escape(JText::_('EXPIRATION'))?>::<?php echo $this->escape($this->depositExpires[$reservation->id]); ?>"><?php echo BookingHelper::displayPrice($reservation->reservationFullDeposit); ?></span>
						    			<?php } ?>
						    		</td>
						    	<?php } ?>
						    	<?php if ($reservedItem->rtype == RESERVATION_TYPE_PERIOD) { ?>
						    		<td colspan="2"><?php echo AHtml::showRecurenceTimeframe($reservedItem).' '.AHtml::showRecurencePattern($reservedItem); ?></td>
						    	<?php } else { ?>
						    		<td>
						    			<?php echo AHtml::date($reservedItem->from, ADATE_FORMAT_NORMAL, 0); ?>
						    			<br/><br/>
						    			<?php echo AHtml::date($reservedItem->to, ADATE_FORMAT_NORMAL, 0); ?>
						    		</td>
						    		<td>
						    			<?php echo AHtml::date($reservedItem->from, ATIME_FORMAT_SHORT, 0); ?>
						    			<br/><br/>
						    			<?php echo AHtml::date($reservedItem->to, ATIME_FORMAT_SHORT, 0); ?>
						    		</td>
						    	<?php } ?>
						    	<?php if ($j == 0) { ?>
						    		<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" class="noprint">
						    			<?php if ($config->showPaymentStatus) { ?>
						    				<?php echo AHtml::renderReservationPaymentStateIcon($reservation); ?>
							    			<br/><br/>
							    		<?php } ?>
						    			<?php echo AHtml::renderReservationStateIcon($reservation); ?>
						    		</td>
						    		<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" class="noprint">
						    			<?php if (!$reservation->isExpired && $reservation->state == RESERVATION_ACTIVE) { ?> 
						    				<a title="<?php echo JText::_('CANCEL_RESERVATION'); ?>" class="aIcon aIconUnpublish" href="<?php echo JRoute::_('index.php?option=com_booking&controller=reservation&task=storno&cid[]=' . $reservation->id); ?>"/>
						    			<?php } ?>
						    		</td>
						    	<?php } ?>
					    	<?php } ?>
					    </tr>
				    <?php } ?>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
	<?php if ($this->customerHomepage) { ?>
		<a href="<?php echo $this->customerHomepage; ?>"><?php echo JText::_('BACK'); ?></a>
	<?php } ?>
	<div class="reservations-legend noprint">
			<span class="aIconLegend aIconTick"><?php echo $this->escape(JText::_('RESERVED')); ?><span class="aIconSeparator">&nbsp;</span></span>
			<span class="aIconLegend aIconUnpublish"><?php echo $this->escape(JText::_('CANCELLED')); ?><span class="aIconSeparator">&nbsp;</span></span>
			<span class="aIconLegend aIconExpired"><?php echo $this->escape(JText::_('EXPIRED')); ?></span>
	</div>
	<?php if ($config->showPaymentStatus) { ?>
		<div class="reservations-legend noprint">
			<?php $i = 1;
				  foreach (BookingHelper::getPaymentStatuses() as $status) { ?>
					<span class="aIconLegend <?php echo $status['icon']; ?>">
						<?php echo $this->escape($status['label']);
						      if ($i ++ < count(BookingHelper::getPaymentStatuses())) { ?>
							<span class="aIconSeparator">&nbsp;</span>
						<?php } ?>
					</span>
				<?php } ?>
		</div>
	<?php } ?>
    <?php if ($this->pagination->total > $this->pagination->minLimit) { ?>
   		<div class="pagination noprint"><?php echo $this->pagination->getListFooter().(ISJ3 ? $this->pagination->getLimitBox() : ''); ?></div> 
    <?php } ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="reset" value="0"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_RESERVATION; ?>"/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
	<input type="hidden" name="<?php echo SESSION_TESTER; ?>" value="1"/>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>	