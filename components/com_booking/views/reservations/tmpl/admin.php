<?php
/**
 * Reservations administration list template. 
 * Display browse table with advanced filter.
 * Set the toolbar for many operations with reservations.
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

$config = AFactory::getConfig();
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
    		<?php if ($print) { ?>
    			window.print();
    	    <?php } ?>        	
    	});
    // ]]>
</script>
<h1><?php echo JText::_('MANAGE_RESERVATIONS'); ?></h1>
<a href="<?php echo JRoute::_(ARoute::view(VIEW_RESERVATIONS, '', '', array('layout' => 'admin', 'tmpl' => 'component', 'print' => 1))); ?>" target="_blank" class="noprint">
    <?php echo JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true); ?>
</a>
<form action="<?php echo JRoute::_(ARoute::viewlayout(VIEW_RESERVATIONS, 'admin')); ?>" method="post" name="adminForm" id="adminForm" class="registration">
	<div class="filter noprint">
		<div class="filterItem">
			<label for="filter_reservation-id" id="filter_resid_label"><?php echo JText::_('RES_NUM'); ?>: </label>
			<input type="text" name="filter_reservation-id" id="filter_reservation-id" onchange="this.form.submit();" value="<?php echo $this->escape(is_array($this->lists['reservation-id']) ? implode(',', $this->lists['reservation-id']) : $this->lists['reservation-id']); ?>" placeholder="<?php echo ISJ3 ? JText::_('RES_NUM') : ''; ?>" size="1" class="inputbox input-mini" />
			<label for="filter_reservation-surname" id="filter_surname_label"><?php echo JText::_('CUSTOMER'); ?>: </label>
			<input type="text" name="filter_reservation-surname" id="filter_reservation-surname" value="<?php echo $this->escape($this->lists['reservation-surname']); ?>" placeholder="<?php echo ISJ3 ? JText::_('CUSTOMER') : ''; ?>" size="15" class="inputbox input-medium" />		
			<label for="filter_items-subject_title" id="filter_subject_label"><?php echo JText::_('ITEM'); ?>: </label>
			<input type="text" name="filter_items-subject_title" id="filter_items-subject_title" size="15" class="inputbox input-medium" onchange="this.form.submit();" value="<?php echo $this->escape($this->lists['items-subject_title']); ?>"/>
		</div>
		<div class="filterItem">
            <?php
                $disFormat = $this->lists['date_filtering'] == 2 ? ADATE_FORMAT_NORMAL : ADATE_FORMAT_LONG;
                $sqlFormat = $this->lists['date_filtering'] == 2 ? ADATE_FORMAT_MYSQL_DATE_CAL : ADATE_FORMAT_MYSQL_DATETIME_CAL;                     
            ?>
			<label for="filter_from" id="filter_from_label"><?php echo JText::_('FROM'); ?>: </label>
			<?php echo AHtml::getCalendar($this->lists['from'], 'filter_from', 'filter_from', $disFormat, $sqlFormat, '', true, 0); ?>
			<label for="filter_to" id="filter_to_label"><?php echo JText::_('TO'); ?>: </label>
			<?php echo AHtml::getCalendar($this->lists['to'], 'filter_to', 'filter_to', $disFormat, $sqlFormat, '', true, 0); ?>
            <label for="date_filtering" id="date_filtering_label"><?php echo JText::_('DATE_FILTERING'); ?>: </label>
            <?php $options = array();
            $options[] = JHtml::_('select.option', 1, JText::_('INTERVAL'));
            $options[] = JHtml::_('select.option', 2, JText::_('EXACT_DATE'));
            echo JHtml::_('select.genericlist', $options, 'date_filtering', 'onchange="this.form.submit()" class="input-medium"', 'value', 'text', $this->lists['date_filtering']); ?>
		</div>
		<div class="filterItem">
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
			<select id="filter_payment_status" class="inputbox" onchange="this.form.submit()" name="filter_payment_status">
				<option value="">- <?php echo JText::_('PAYMENT_STATUS'); ?> -</option>
				<?php echo JHtmlSelect::options(BookingHelper::getPaymentStatuses(), 'id', 'label', $this->lists['payment_status']); ?>
			</select>
		</div>
		<div class="buttons">
			<input class="button btn btn-primary" type="submit" onclick="this.form.submit();" value="<?php echo $this->escape(JText::_('JSEARCH_FILTER_LABEL')); ?>" />
			<input class="button btn btn-primary" type="submit" onclick="this.form.reset.value=1; this.form.submit();" value="<?php echo $this->escape(JText::_('JSEARCH_FILTER_CLEAR')); ?>" />
		</div>
		<div class="clr"></div>
	</div>
	<div class="bookingToolbar noprint">
		<a class="aIconToolPublish tool publish" href="#" onclick="ViewReservations.task('receive')" title="<?php echo JText::_('RECEIVE', true); ?>"><?php echo JText::_('RECEIVE', true); ?></a>
		<a class="aIconToolPending tool pending" href="#" onclick="ViewReservations.task('receiveDeposit')" title="<?php echo JText::_('DEPOSIT', true); ?>"><?php echo JText::_('DEPOSIT', true); ?></a>
		<a class="aIconToolUnpublish tool unpublish" href="#" onclick="ViewReservations.task('unreceive')" title="<?php echo JText::_('UNRECEIVE', true); ?>"><?php echo JText::_('UNRECEIVE', true); ?></a>
		<a class="aIconToolApply tool apply" href="#" onclick="ViewReservations.task('active')" title="<?php echo JText::_('RESERVED', true); ?>"><?php echo JText::_('RESERVED', true); ?></a>
		<a class="aIconToolCancel tool cancel" href="#" onclick="ViewReservations.task('storno')" title="<?php echo JText::_('JCANCEL', true); ?>"><?php echo JText::_('JCANCEL', true); ?></a>
		<a class="aIconToolTrash tool trash" href="#" onclick="ViewReservations.task('trash')" title="<?php echo JText::_('JTRASH', true); ?>"><?php echo JText::_('JTRASH', true); ?></a>
        <a class="aIconToolTrash tool trash" href="#" onclick="if (confirm('<?php echo JText::_('ARE_YOU_SURE', true); ?>')) { ViewReservations.task('emptyTrash'); }" title="<?php echo JText::_('EMPTY_TRASH', true); ?>"><?php echo JText::_('EMPTY_TRASH', true); ?></a>
        <a class="aIconToolRestore tool restore" href="#" onclick="ViewReservations.task('restore')" title="<?php echo JText::_('RESTORE', true); ?>"><?php echo JText::_('RESTORE', true); ?></a>
		<div class="clr"></div>
	</div>
	<table class="category table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th width="1%" class="noprint">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th nowrap="nowrap">
					<span class="hasTip" title="<?php echo $this->escape(JText::_('RESERVATION_NUMBER')); ?>">
						<?php echo JText::_('RES_NUM'); ?>
					</span>
				</th>
				<th>
				    <?php echo JHTML::_('grid.sort', 'Customer', 'surname', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th>
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
				<th>
				    <?php echo JHTML::_('grid.sort', 'Price', 'reservationFullPrice', $this->lists['order_Dir'], $this->lists['order']); ?>
					<br/>
				    <?php echo JHTML::_('grid.sort', 'Deposit', 'reservationFullDeposit', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th colspan="2" nowrap="nowrap">
				    <?php echo JHTML::_('grid.sort', 'From', 'items-from', $this->lists['order_Dir'], $this->lists['order']); ?>
					<br/>
				    <?php echo JHTML::_('grid.sort', 'To', 'items-to', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th nowrap="nowrap" class="noprint">
					<span class="hasTip" title="<?php echo $this->escape(JText::_('PAYMENT_STATUS')); ?>"><?php echo JText::_('PAY_STAT'); ?></span>
					<br/>
				    <span class="hasTip" title="<?php echo $this->escape(JText::_('RESERVATION_STATUS')); ?>"><?php echo JText::_('RES_STAT'); ?></span>
				</th>
                <?php if ($config->showNoteColumn) { ?>
                    <th width="1%" class="noprint">
                        <?php echo JText::_('NOTE'); ?>
                    </th>
                <?php } ?>                
			</tr>
		</thead>
		<tbody>
			<?php if (empty($this->items)) { ?>
				<tr>
					<td colspan="15">
						<?php echo JText::_('NO_ITEMS_FOUND'); ?>
					</td>
				</tr>
			<?php 
				} else {
					foreach ($this->items as $i => $subject) {
				    	/* @var $subject TableReservation */
						foreach ($this->reservedItems[$subject->id] as $j => $reservedItem) {	
							/* @var $reservedItem TableReservationItems  */
							TableReservationItems::display($reservedItem);
				?>
				    <tr class="row<?php echo $i % 2; ?>">
				    	<?php if ($j==0){ ?>
				    	<td rowspan="<?php echo count($this->reservedItems[$subject->id]); ?>" class="noprint"><?php echo JHTML::_('grid.checkedout', $subject, $i); ?></td>
				    	<td rowspan="<?php echo count($this->reservedItems[$subject->id]); ?>">
				    		<a href="<?php echo JRoute::_(ARoute::view(VIEW_RESERVATION, null, null, array('cid' => $subject->id))); ?>" title="<?php echo $this->escape(JText::_('DISPLAY_RESERVATION')); ?>::<?php echo $subject->id; ?>" class="hasTip"><?php echo $subject->id; ?></a>				    		
				    	</td>
				    	<td rowspan="<?php echo count($this->reservedItems[$subject->id]); ?>">
				    		<?php echo BookingHelper::formatName($subject); ?>
				    	</td>
				    	<?php } ?>
				    	<?php if (!$reservedItem) { ?>
				    		<td colspan="5">No subject reserved</td>
				    	<?php } else { ?>
				    	<td>
				    		<?php if (!is_null($reservedItem->subject)) { ?>
				   				<a href="<?php echo JRoute::_(ARoute::view(VIEW_SUBJECT, $reservedItem->subject, $reservedItem->subjectAlias)); ?>" title="<?php echo $this->escape(sprintf(JText::_('DISPLAY_OBJECT_S', true), $reservedItem->subjectTitle)); ?>" class="hasTip">
				   					<?php echo $reservedItem->subject_title; ?>
				   				</a>
				   				<?php if ($reservedItem->sub_subject) ?>
				   					<br/>
				   				    <?php echo $reservedItem->sub_subject_title; ?>
				   			<?php } else { ?>
				   				<span title="<?php echo JText::_('SUBJECT_NOT_FOUND', true); ?>"><?php echo $subject->subject_title; ?></span>
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
				    	<td nowrap="nowrap">
				    		<?php echo BookingHelper::displayPrice($reservedItem->fullPriceSupplements); ?>
				    		<br/>
				    		<?php echo BookingHelper::displayPrice($reservedItem->fullDeposit); ?>
				    	</td>
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
				    	<?php } ?>
				   		<?php if ($j==0){ ?>
				   		<td rowspan="<?php echo count($this->reservedItems[$subject->id]); ?>" class="noprint">
				   			<?php echo AHtml::showPaymentTooltip($subject, $i); ?>
				    		<br/><br/>
				   			<?php 
        						switch ($subject->state) {
									case RESERVATION_PRERESERVED:
						            	if (! JTable::isCheckedOut($this->user->get('id'), $subject->checked_out)) { ?>
           									<span class="editlinktip hasTip aIcon aIconNew" title="<?php echo JText::_('PRE_RESERVED', true) . '::' . JText::_('CLICK_TO_MARK_AS_ACTIVE', true); ?>" onclick="listItemTask('cb<?php echo $i; ?>','active')" style="cursor: pointer">&nbsp;</span>			
           								<?php } else { ?>	
            								<span class="editlinktip hasTip aIcon aIconTick" title="<?php echo JText::_('PRE_RESERVED', true); ?>">&nbsp;</span>
            							<?php }
            							break;
						            case RESERVATION_ACTIVE:
						            	if (! JTable::isCheckedOut($this->user->get('id'), $subject->checked_out)) { ?>
           									<span class="editlinktip hasTip aIcon aIconTick" title="<?php echo JText::_('RESERVED', true) . '::' . JText::_('CLICK_TO_MARK_AS_CANCELLED', true); ?>" onclick="listItemTask('cb<?php echo $i; ?>','storno')" style="cursor: pointer">&nbsp;</span>			
           								<?php } else { ?>	
            								<span class="editlinktip hasTip aIcon aIconTick" title="<?php echo JText::_('RESERVED', true); ?>">&nbsp;</span>
            							<?php }
            							break;
							        case RESERVATION_STORNED:
							            if (! JTable::isCheckedOut($this->user->get('id'), $subject->checked_out)) { ?>
            								<span class="editlinktip hasTip aIcon aIconUnpublish" title="<?php echo JText::_('CANCELLED', true) . '::' . JText::_('CLICK_TO_MARK_AS_TRASHED', true); ?>" onclick="listItemTask('cb<?php echo $i; ?>','trash')" style="cursor: pointer">&nbsp;</span>			
            							<?php } else { ?>	
            								<span class="editlinktip hasTip aIcon aIconUnpublish" title="<?php echo JText::_('CANCELLED', true); ?>">&nbsp;</span>
            							<?php }
						                break;
						            case RESERVATION_TRASHED:
						            	if (! JTable::isCheckedOut($this->user->get('id'), $subject->checked_out)) { ?>
           									<span class="editlinktip hasTip aIcon aIconTrash" title="<?php echo JText::_('JTRASHED', true) . '::' . JText::_('CLICK_TO_MARK_AS_CONFLICTED', true); ?>" onclick="listItemTask('cb<?php echo $i; ?>','conflict')" style="cursor: pointer">&nbsp;</span>			
           								<?php } else { ?>	
           									<span class="editlinktip hasTip aIcon aIconTrash" title="<?php echo JText::_('JTRASHED', true); ?>">&nbsp;</span>
           								<?php }
						                break;
						         	case RESERVATION_CONFLICTED:
						               	if (! JTable::isCheckedOut($this->user->get('id'), $subject->checked_out)) { ?>
						            		<span class="editlinktip hasTip aIcon aIconNotice" title="<?php echo JText::_('CONFLICTED', true) . '::' . JText::_('CLICK_TO_MARK_AS_PRE_RESERVED', true); ?>" onclick="listItemTask('cb<?php echo $i; ?>','prereserved')" style="cursor: pointer">&nbsp;</span>			
						            	<?php } else { ?>	
						            		<span class="editlinktip hasTip aIcon aIconTrash" title="<?php echo JText::_('CONFLICTED', true); ?>">&nbsp;</span>
						            	<?php }
						                break;
       							} ?>
			    		</td>
                        <?php if ($config->showNoteColumn) { ?>
                            <td rowspan="<?php echo count($this->reservedItems[$subject->id]); ?>">
                                <?php if (JString::trim($subject->note)){
                                        echo JHtml::tooltip($subject->note); 
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
	<div class="reservations-legend noprint">
		<span class="aIconLegend aIconNew"><?php echo JText::_('PRE_RESERVED', true); ?><span class="aIconSeparator">&nbsp;</span></span>
		<span class="aIconLegend aIconTick"><?php echo JText::_('RESERVED', true); ?><span class="aIconSeparator">&nbsp;</span></span>
		<span class="aIconLegend aIconUnpublish"><?php echo JText::_('CANCELLED', true); ?><span class="aIconSeparator">&nbsp;</span></span>
		<span class="aIconLegend aIconTrash"><?php echo JText::_('TRASHED', true); ?><span class="aIconSeparator">&nbsp;</span></span>
		<span class="aIconLegend aIconNotice"><?php echo JText::_('CONFLICTED', true); ?></span>
	</div>
	<div class="clr"></div>
	<div class="reservations-legend noprint">
		<?php $i = 1; 
			  foreach (BookingHelper::getPaymentStatuses() as $status) { ?>
				<span class="aIconLegend <?php echo $status['icon']; ?>">
					<?php echo $status['label'];
					      if ($i ++ < count(BookingHelper::getPaymentStatuses())) { ?>
						<span class="aIconSeparator">&nbsp;</span>
					<?php } ?>
				</span>
		<?php } ?>
	</div>
	<div class="pagination noprint"><?php echo $this->pagination->getListFooter().(ISJ3 ? $this->pagination->getLimitBox() : ''); ?></div>
	<div class="clr"></div>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="layout" value="admin"/>
	<input type="hidden" name="reset" value="0"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_RESERVATION; ?>"/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
	<input type="hidden" name="<?php echo SESSION_TESTER; ?>" value="1"/>
	<?php echo JHTML::_('form.token'); ?>
</form>	