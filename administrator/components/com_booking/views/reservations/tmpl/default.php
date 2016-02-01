<?php

/**
 * Reservations administration list template. 
 * Display browse table with advanced filter.
 * Set the toolbar for many operations with reservations.
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

/* @var $this BookingViewReservations */


if (ISJ3) {
	JHTML::_('dropdown.init');
	JHTML::_('formbehavior.chosen', 'select');
}
	
BookingHelper::setSubmenu(3);

JToolBarHelper::title(JText::_(COMPONENT_NAME).": ".JText::_('RESERVATIONS'), 'categories');

JToolBarHelper::editList();
JToolBarHelper::divider();
JToolBarHelper::deleteList('', 'trash', 'Trash');
JToolBarHelper::custom('restore', 'restore.png', 'restore_f2.png', 'Restore', true);
JToolBar::getInstance('toolbar')->appendButton('Confirm', 'Are you sure?', 'trash', 'Empty_Trash', 'emptyTrash', false, true);
JToolBarHelper::divider();
JToolBar::getInstance('toolbar')->appendButton('Link', 'export', 'CSV', JRoute::_('index.php?option=com_booking&task=reservation.export&type=csv'));
JToolBar::getInstance('toolbar')->appendButton('Link', 'export', 'XLS', JRoute::_('index.php?option=com_booking&task=reservation.export&type=xls'));
JToolBar::getInstance('toolbar')->appendButton('Link', 'print', 'PRINT', JRoute::_(ARoute::view(VIEW_RESERVATIONS, '', '', array('tmpl' => 'component', 'print' => 1))));
JToolBarHelper::divider();
JToolBarHelper::back();
JToolBarHelper::divider();
if (JFactory::getUser()->authorise('core.admin', 'com_booking'))
	JToolBarHelper::preferences('com_booking');

$config = AFactory::getConfig();

JHTML::_('script', JURI::root(true) . 'components/com_booking/assets/libraries/mavsuggest/mavsuggest.js');
JHTML::_('stylesheet', JURI::root(true) . 'components/com_booking/assets/libraries/mavsuggest/mavsuggest.css');

$print = JRequest::getInt('print');
if ($print)
    AImporter::css('print');

?>
<script type="text/javascript">
    // <![CDATA[
    	window.addEvent('domready', function() {
        	new MavSuggest.Request.JSON({
            	'elem': 'filter_reservation-surname',
            	'url': '<?php echo JRoute::_('index.php?option=com_booking&controller=customer&task=suggest', false); ?>'
        	});
        	new MavSuggest.Request.JSON({
            	'elem': 'filter_items-subject_title',
            	'url': '<?php echo JRoute::_('index.php?option=com_booking&controller=subject&task=suggest', false); ?>'
        	});
        	var print = document.getElement('#toolbar-print a');
        	if (print)
        		print.setProperty('target', '_blank');
        	else {
        		var print = document.getElement('#toolbar-print button');
        		if (print)
					print.setProperty('onclick', "window.open('<?php echo JRoute::_(ARoute::view(VIEW_RESERVATIONS, '', '', array('tmpl' => 'component', 'print' => 1)), false); ?>')");
        	}
    		<?php if ($print) { ?>
        		window.print();
        	<?php } ?>
    	});
    // ]]>
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class="noprint">
		<div class="filter-search fltlft">
			<div class="btn-group pull-left hidden-phone fltlft">
				<label for="filter_reservation-id" class="filter-search-lbl element-invisible"><?php echo JText::_('RES_NUM'); ?>: </label>
				<input type="text" name="filter_reservation-id" id="filter_reservation-id" onchange="this.form.submit();" value="<?php echo $this->escape($this->lists['reservation-id']); ?>" placeholder="<?php echo ISJ3 ? JText::_('RES_NUM') : ''; ?>" size="5" style="width: auto" title="<?php echo $this->escape(JText::_('RESERVATION_NUMBER')); ?>" class="hasTip" />
		   		<label for="filter_reservation-surname" class="filter-search-lbl element-invisible"><?php echo JText::_('CUSTOMER'); ?>: </label>
				<input type="text" name="filter_reservation-surname" id="filter_reservation-surname" value="<?php echo $this->escape($this->lists['reservation-surname']); ?>" placeholder="<?php echo ISJ3 ? JText::_('CUSTOMER') : ''; ?>" style="width: auto" size="15" title="<?php echo $this->escape(JText::_('CUSTOMER')); ?>" class="hasTip" />
				<label for="filter_items-subject_title" class="filter-search-lbl element-invisible"><?php echo JText::_('ITEM'); ?>: </label>
				<input type="text" name="filter_items-subject_title" id="filter_items-subject_title" onchange="this.form.submit();" value="<?php echo $this->escape($this->lists['items-subject_title']); ?>" placeholder="<?php echo ISJ3 ? JText::_('ITEM') : ''; ?>" style="width: auto" size="15" title="<?php echo $this->escape(JText::_('ITEM')); ?>" class="hasTip" />
			</div>
			<div class="btn-group pull-left hidden-phone fltlft">
				<button onclick="this.form.submit();" class="btn">
					<i class="icon-search"></i>
					<?php echo ISJ3 ? '' : JText::_('JSEARCH_FILTER_SUBMIT'); ?>
				</button>
				<button onclick="this.form.reset.value=1; this.form.submit();" class="btn">
					<i class="icon-remove"></i>
					<?php echo ISJ3 ? '' : JText::_('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</div>
		</div>
		<div class="btn-group pull-right hidden-phone filter-select fltrt">
			<?php 
				$options = array();
				$options[] = JHtml::_('select.option', '', '- ' . JText::_('RESERVATION_STATUS') . ' -');
				$options[] = JHtml::_('select.option', RESERVATION_PRERESERVED, JText::_('PRE_RESERVED'));
				$options[] = JHtml::_('select.option', RESERVATION_ACTIVE, JText::_('RESERVED'));
				$options[] = JHtml::_('select.option', RESERVATION_STORNED, JText::_('CANCELLED'));
				$options[] = JHtml::_('select.option', RESERVATION_TRASHED, JText::_('TRASHED'));
				$options[] = JHtml::_('select.option', RESERVATION_CONFLICTED, JText::_('CONFLICTED'));
				echo JHtml::_('select.genericlist', $options, 'filter_reservation_status', 'onchange="this.form.submit()"', 'value', 'text', $this->lists['reservation_status']);
			?>
		</div>
		<div class="btn-group pull-right hidden-phone filter-select fltrt">
			<select id="filter_payment_status" onchange="this.form.submit()" name="filter_payment_status">
				<option value="">- <?php echo JText::_('PAYMENT_STATUS'); ?> -</option>
				<?php echo JHtml::_('select.options', BookingHelper::getPaymentStatuses(), 'id', 'label', $this->lists['payment_status']); ?>
			</select>
		</div>
		<div class="btn-group pull-right hidden-phone filter-select fltrt">
            <?php
                $disFormat = $this->lists['date_filtering'] == 2 ? ADATE_FORMAT_NORMAL : ADATE_FORMAT_LONG;
                $sqlFormat = $this->lists['date_filtering'] == 2 ? ADATE_FORMAT_MYSQL_DATE_CAL : ADATE_FORMAT_MYSQL_DATETIME_CAL;                     
            ?>
			<label for="filter_from" id="filter_from_label" style="display: inline-block; clear: none; float: left; margin: 0px 5px 0px 0px;"><?php echo JText::_('FROM'); ?>: </label>
			<?php echo AHtml::getCalendar($this->lists['from'], 'filter_from', 'filter_from', $disFormat, $sqlFormat, 'title="' . $this->escape(JText::_('FROM')) . '" class="hasTip"', true, 0); ?>
			<label for="filter_to" id="filter_to_label" style="display: inline-block; clear: none; float: left; margin: 0px 5px 0px 10px;"><?php echo JText::_('TO'); ?>: </label>
			<?php echo AHtml::getCalendar($this->lists['to'], 'filter_to', 'filter_to', $disFormat, $sqlFormat, '', true, 0); ?>                    <label for="date_filtering" id="date_filtering_label" class="filter-search-lbl element-invisible"><?php echo JText::_('DATE_FILTERING'); ?>: </label>
            <?php $options = array();
            $options[] = JHtml::_('select.option', 1, JText::_('INTERVAL'));
            $options[] = JHtml::_('select.option', 2, JText::_('EXACT_DATE'));
            echo JHtml::_('select.genericlist', $options, 'date_filtering', 'onchange="this.form.submit()"', 'value', 'text', $this->lists['date_filtering']); ?>
		</div>
	</fieldset>
	<div id="editcell">
		<table class="adminlist table-striped table" cellspacing="1">
			<thead>
				<tr>
					<th width="1%" nowrap="nowrap" class="noprint">#</th>
					<th width="1%" nowrap="nowrap" class="noprint">
						<input type="checkbox" class="inputCheckbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th width="1%" nowrap="nowrap">
				        <?php echo JHTML::_('grid.sort', 'Res_num', 'id', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
					<th>
				        <?php echo JHTML::_('grid.sort', 'Created', 'created', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
					<th>
				        <?php echo JHTML::_('grid.sort', 'Customer', 'surname', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
					<th width="1%" nowrap="nowrap">
				        <?php echo JText::_('EMAIL'); ?>
					</th>
					<th>
				        <?php echo JHTML::_('grid.sort', 'Item', 'items-subject_title', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
					<th width="1%" nowrap="nowrap">
						<span class="hasTip" title="<?php echo $this->escape(JText::_('CAPACITY')); ?>">
				            <?php echo JHTML::_('grid.sort', 'CAP', 'fullCapacity', $this->lists['order_Dir'], $this->lists['order']); ?>
				        </span>
					</th>
					<?php if ($config->showOccupancyColumn) { ?>
						<th width="1%" nowrap="nowrap">
							<span class="hasTip" title="<?php echo $this->escape(JText::_('OCCUPANCY')); ?>">
				                <?php echo JHTML::_('grid.sort', 'OCC', 'occupancy', $this->lists['order_Dir'], $this->lists['order']); ?>
				            </span>
						</th>
					<?php } ?>
					<?php if ($config->showSupplementsColumn) { ?>
						<th width="1%" nowrap="nowrap">
				            <?php echo JText::_('SUPPLEMENTS'); ?>
						</th>
					<?php } ?>                        
					<th width="1%" colspan="2">
				        <?php echo JHTML::_('grid.sort', 'From', 'items-from', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
					<th width="1%" colspan="2">
				        <?php echo JHTML::_('grid.sort', 'To', 'items-to', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
					<?php if ($config->usingPrices) { ?>
						<th width="1%" nowrap="nowrap">
				        	<?php echo JHTML::_('grid.sort', 'Price', 'reservationFullPrice', $this->lists['order_Dir'], $this->lists['order']); ?>
						</th>
					<?php }
					if ($config->usingPrices == PRICES_WITH_DEPOSIT) { ?>
						<th width="1%" nowrap="nowrap">
				        	<?php echo JText::_('DEPOSIT'); ?>
						</th>
					<?php }
					if ($config->usingPrices) { ?>
						<th width="1%" class="noprint">
				        	<?php echo JHTML::_('grid.sort', 'Payment_Status', 'paid', $this->lists['order_Dir'], $this->lists['order']); ?>
						</th>
					<?php } ?>
					<th width="1%" class="noprint">
				        <?php echo JHTML::_('grid.sort', 'Reservation_Status', 'state', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
                    <?php if ($config->showNoteColumn) { ?>
                        <th width="1%" class="noprint">
                            <?php echo JText::_('NOTE'); ?>
                        </th>
                    <?php } ?>
				</tr>
			</thead>
			<tfoot class="noprint">
    			<tr>
    				<td colspan="20">
    				    <?php echo $this->pagination->getListFooter().(ISJ3 ? $this->pagination->getLimitBox() : ''); ?>
    				</td>
    			</tr>
			</tfoot>
			<tbody>
				<?php if (! is_array($this->items) || ! count($this->items)) { ?>
					<tr>
						<td colspan="20">
							<?php echo JText::_('NO_ITEMS_FOUND'); ?>
						</td>
					</tr>
				<?php 
					} else {
						foreach ($this->items as $i => $reservation) {
				    		/* @var $reservation TableReservation */
							
							list($isInvoice, $invoiceLink) = BookingHelper::getInvoiceLink($reservation->id);
														
							foreach ($this->reservedItems[$reservation->id] as $j => $reservedItem) {
								/* @var $reservedItem TableReservationItems */
								TableReservationItems::display($reservedItem);
				?>
				    	<tr class="row<?php echo $i % 2; ?>">
				    	<?php if ($j == 0) { ?>
				    		<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" align="right" class="noprint"><?php echo $this->pagination->getRowOffset($i); ?></td>
				    		<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" class="checkboxCell noprint"><?php echo JHTML::_('grid.checkedout', $reservation, $i); ?></td>
				    		<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" align="right">
				    			<a href="<?php echo JRoute::_(ARoute::detail(CONTROLLER_RESERVATION, $reservation->id)); ?>" title="<?php echo $this->escape(JText::_('SHOW_RESERVATION')); ?>::<?php echo $reservation->id; ?>" class="hasTip"><?php echo $reservation->id; ?></a>
				    			<?php if ($isInvoice==1) { ?>
				    				<a href="javascript:void(0)" title="<?php echo JText::_('OPEN_INVOICE')?>" class="aIcon aIconInvoice aIconReservationInvoice noprint" onclick="window.open('<?php echo $invoiceLink ?>','win2', 'status=yes,toolbar=yes,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');"></a>
				    			<?php } elseif ($isInvoice==2) { ?>
				    				<a href="javascript:void(0)" title="<?php echo JText::_('INVOICE_NOT_AVAILABLE')?>::<?php echo JText::_($invoiceLink)?>" class="hasTip aIcon aIconInvoice aIconReservationInvoice noprint"></a>
				    			<?php } ?>
				    		</td>
				    		<?php 
				    			$tip = '';
				    			if (AHtml::date($reservation->created, ADATE_FORMAT_LONG)) {
				    				$tip .= '<tr><th>' . JText::_('CREATED') . '</th>';
				    				$tip .= '<td>' . ($reservation->creator ? $reservation->creator : JText::_('UNREGISTERED_CUSTOMER')) . '</td>';
				    				$tip .= '<td>' . AHtml::date($reservation->created, ADATE_FORMAT_LONG) . '</td></tr>';
				    			}
				    			if (AHtml::date($reservation->modified, ADATE_FORMAT_LONG)) {
				    				$tip .= '<tr><th>' . JText::_('MODIFIED') . '</th>';
				    				$tip .= '<td>' . ($reservation->modifier ? $reservation->modifier : JText::_('UNREGISTERED_CUSTOMER')) . '</td>';
				    				$tip .= '<td>' . AHtml::date($reservation->modified, ADATE_FORMAT_LONG) . '</td></tr>';
				    			}
				    			if ($tip)
				    				$tip = $this->escape('<table>' . $tip . '</table>');
				    		?>
							<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" align="right" <?php if ($tip) { ?>title="::<?php echo $tip; ?>" class="hasTip"<?php } ?>>							
				    			<?php echo AHtml::date($reservation->created, ADATE_FORMAT_LONG); ?></td>
				    		<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>">
				    			<?php 
				    				$tip = array(BookingHelper::formatName($reservation));
				    				if (JString::trim($reservation->telephone))
				    					$tip[] = JText::_('PHONE') . ': ' . $reservation->telephone;
				    				if (JString::trim($reservation->email))
				    					$tip[] = JText::_('EMAIL') . ': ' . $reservation->email;
				    				if ($reservation->customer) { 
								?>
				    				<a href="<?php echo JRoute::_(ARoute::detail(CONTROLLER_CUSTOMER, $reservation->customer)); ?>" title="<?php echo $this->escape(JText::_('SHOW_CUSTOMER')); ?>::<?php echo $this->escape(implode('<br/>', $tip)); ?>" class="hasTip"><?php echo BookingHelper::formatName($reservation); ?></a>
				    			<?php } else { ?>
				    				<span title="<?php echo $this->escape(JText::_('UNREGISTERED_CUSTOMER')); ?>::<?php echo $this->escape(implode('<br/>', $tip)); ?>" class="hasTip"><?php echo BookingHelper::formatName($reservation); ?></span>
				    			<?php } ?>
				    		</td>
				    		<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>">
				    			<?php if ($reservation->email) { ?>
				    				<?php if ($print) 
				    				    echo $reservation->email;
				    				 else {?>
				    					<a href="mailto:<?php echo $this->escape($reservation->email); ?>" class="hasTip aIcon aIconEmail" title="<?php echo $this->escape(JText::_('SEND_E_MAIL_TO')); ?>::<?php echo $this->escape($reservation->email); ?>"></a>
				    			<?php }
				    			    } ?>
				    		</td>
				    	<?php } ?>
				    	<?php if (!$reservedItem) { ?>
				    		<td colspan="6"><?php echo JText::_('NO_ITEM_RESERVED'); ?></td>
				    	<?php } else { ?>
				    		<td>
				    			<a href="<?php echo JRoute::_(ARoute::edit(CONTROLLER_SUBJECT, $reservedItem->subject)); ?>" title="<?php echo $this->escape(JText::_('SHOW_ITEM')); ?>::<?php echo $this->escape($reservedItem->subject_title); ?>" class="hasTip"><?php echo $reservedItem->subject_title; ?></a>
				    			<?php if ($reservedItem->sub_subject) { ?>
				    				<br/>
				    				<?php echo $reservedItem->sub_subject_title; ?>
				    			<?php } ?>
				    		</td>
				    		<td align="right" title="<?php echo $this->escape(JText::_('RESERVED_CAPACITY')); ?>::<?php echo $this->escape($reservedItem->capacity); ?>" class="hasTip"><?php echo $reservedItem->capacity; ?></td>
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
				    		<?php if ($reservedItem->rtype == RESERVATION_TYPE_PERIOD) { ?>
								<td colspan="4">
									<?php echo AHtml::showRecurenceTimeframe($reservedItem).' '.AHtml::showRecurencePattern($reservedItem); ?>
								</td>
				    		<?php } else { ?>
				    			<td align="center" width="1%"><?php echo AHtml::date($reservedItem->from, ADATE_FORMAT_NORMAL, 0); ?></td>
				    			<td align="center" width="1%"><?php echo AHtml::date($reservedItem->from, ATIME_FORMAT_SHORT, 0); ?></td>
				    			<td align="center" width="1%"><?php echo AHtml::date($reservedItem->to, ADATE_FORMAT_NORMAL, 0); ?></td>
				    			<td align="center" width="1%"><?php echo AHtml::date($reservedItem->to, ATIME_FORMAT_SHORT, 0); ?></td>
				    		<?php } ?>
				        <?php } ?>
				        <?php if ($j==0){ ?>
				        	<?php if ($config->usingPrices) { ?>
				    			<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" nowrap="nowrap" align="right"><?php echo BookingHelper::displayPrice($reservation->reservationFullPrice); ?></td>
				    		<?php }
				    		if ($config->usingPrices == PRICES_WITH_DEPOSIT) { ?>
				    			<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" nowrap="nowrap" align="right" title="<?php echo $this->escape(JText::_('EXPIRATION'))?>::<?php echo $this->escape($this->depositExpires[$reservation->id]); ?>" class="hasTip" style="padding-left: 20px; background-repeat: no-repeat; background-image: url('/components/com_booking/assets/images/icon-16-notice-note.png'); background-position: left center;"><?php echo BookingHelper::displayPrice($reservation->reservationFullDeposit); ?></td>
				    		<?php }
				    		if ($config->usingPrices) { ?>
				    			<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" align="center" class="noprint">
				    				<?php echo AHtml::showPaymentTooltip($reservation, $i); ?>
				    			</td>
				    		<?php } ?>
				    		<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" align="center" class="noprint">
				    			<?php 
        							switch ($reservation->state) {
										case RESERVATION_PRERESERVED:
							            	if (! JTable::isCheckedOut($this->user->get('id'), $reservation->checked_out)) { ?>
            									<span class="editlinktip hasTip aIcon aIconNew" title="<?php echo $this->escape(JText::_('PRE_RESERVED')) . '::' . $this->escape(JText::_('CLICK_TO_MARK_AS_ACTIVE')); ?>" onclick="listItemTask('cb<?php echo $i; ?>','active')" style="cursor: pointer">&nbsp;</span>			
            								<?php } else { ?>	
            									<span class="editlinktip hasTip aIcon aIconNew" title="<?php echo $this->escape(JText::_('PRE_RESERVED')); ?>">&nbsp;</span>
            								<?php }
							                break;
							            case RESERVATION_ACTIVE:
							            	if (! JTable::isCheckedOut($this->user->get('id'), $reservation->checked_out)) { ?>
            									<span class="editlinktip hasTip aIcon aIconTick" title="<?php echo $this->escape(JText::_('RESERVED')) . '::' . $this->escape(JText::_('CLICK_TO_MARK_AS_CANCELLED')); ?>" onclick="listItemTask('cb<?php echo $i; ?>','storno')" style="cursor: pointer;">&nbsp;</span>			
            								<?php } else { ?>	
            									<span class="editlinktip hasTip aIcon aIconTick" title="<?php echo $this->escape(JText::_('RESERVED')); ?>">&nbsp;</span>
            								<?php }
            								break;
							            case RESERVATION_STORNED:
							            	if (! JTable::isCheckedOut($this->user->get('id'), $reservation->checked_out)) { ?>
            									<span class="editlinktip hasTip aIcon aIconUnpublish" title="<?php echo $this->escape(JText::_('CANCELLED')) . '::' . $this->escape(JText::_('CLICK_TO_MARK_AS_TRASHED')); ?>" onclick="listItemTask('cb<?php echo $i; ?>','trash')" style="cursor: pointer">&nbsp;</span>			
            								<?php } else { ?>	
            									<span class="editlinktip hasTip aIcon aIconUnpublish" title="<?php echo $this->escape(JText::_('CANCELLED')); ?>">&nbsp;</span>
            								<?php }
							                break;
							            case RESERVATION_TRASHED:
							            	if (! JTable::isCheckedOut($this->user->get('id'), $reservation->checked_out)) { ?>
            									<span class="editlinktip hasTip aIcon aIconTrash" title="<?php echo $this->escape(JText::_('TRASHED')) . '::' . $this->escape(JText::_('CLICK_TO_MARK_AS_CONFLICTED')); ?>" onclick="listItemTask('cb<?php echo $i; ?>','conflict')" style="cursor: pointer">&nbsp;</span>			
            								<?php } else { ?>	
            									<span class="editlinktip hasTip aIcon aIconTrash" title="<?php echo $this->escape(JText::_('TRASHED')); ?>">&nbsp;</span>
            								<?php }
							                break;
							            case RESERVATION_CONFLICTED:
                							if (! JTable::isCheckedOut($this->user->get('id'), $reservation->checked_out)) { ?>
                            					<span class="editlinktip hasTip aIcon aIconNotice" title="<?php echo $this->escape(JText::_('CONFLICTED')) . '::' . $this->escape(JText::_('CLICK_TO_MARK_AS_PRE_RESERVED')); ?>" onclick="listItemTask('cb<?php echo $i; ?>','prereserved')" style="cursor: pointer">&nbsp;</span>			
                            				<?php } else { ?>	
                            					<span class="editlinktip hasTip aIcon aIconNotice" title="<?php echo $this->escape(JText::_('CONFLICTED')); ?>">&nbsp;</span>
                            				<?php }
                							 break;
        							} ?>
				    		</td>
                            <?php if ($config->showNoteColumn) { ?>
                                <td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" class="noprint">
                                    <?php if (JString::trim($reservation->note)){
                                            echo JHtml::tooltip($reservation->note); 
                                    } ?>
                                </td>
                            <?php } ?>
				    		<?php } ?>
				    	</tr>
				    <?php 
							}
				    	}
					}
					?>
			</tbody>
		</table>
		<table align="center" class="noprint">
			<tr align="center">
				<td class="aIconLegend aIconNew"><?php echo $this->escape(JText::_('PRE_RESERVED')); ?><span class="aIconSeparator">&nbsp;</span></td>
				<td class="aIconLegend aIconTick"><?php echo $this->escape(JText::_('RESERVED')); ?><span class="aIconSeparator">&nbsp;</span></td>
				<td class="aIconLegend aIconUnpublish"><?php echo $this->escape(JText::_('CANCELLED')); ?><span class="aIconSeparator">&nbsp;</span></td>
				<td class="aIconLegend aIconTrash"><?php echo $this->escape(JText::_('TRASHED')); ?><span class="aIconSeparator">&nbsp;</span></td>
				<td class="aIconLegend aIconNotice"><?php echo $this->escape(JText::_('CONFLICTED')); ?></td>
			</tr>
		</table>
		<div class="clr"></div>
		<table align="center" class="noprint">
			<tr align="center">
				<?php $i = 1;
					  foreach (BookingHelper::getPaymentStatuses() as $status) { ?>
						 <td class="aIconLegend <?php echo $status['icon']; ?>">
							<?php echo $this->escape($status['label']);
								  if ($i ++ != count(BookingHelper::getPaymentStatuses())) { ?>
									<span class="aIconSeparator">&nbsp;</span>
							<?php } ?>
						 </td>
				<?php } ?>
			</tr>
		</table>
	</div>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="reset" value="0"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_RESERVATION; ?>"/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
	<input type="hidden" name="<?php echo SESSION_TESTER; ?>" value="1"/>
	<?php echo JHTML::_('form.token'); ?>
</form>	
