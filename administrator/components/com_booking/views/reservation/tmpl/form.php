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

/* @var $this BookingViewReservation */

defined('_JEXEC') or die('Restricted access');

AHtml::title($this->reservation->id ? JText::sprintf('RESERVATION_NUM', $this->reservation->id) : JText::_('ADD_RESERVATION'), 'categories');
JToolBarHelper::save();
JToolBarHelper::apply();

if ($this->reservation->id) {
	JToolBarHelper::custom('detail', 'preview', 'preview', 'Detail', false);
}

JToolBarHelper::cancel();

JHTML::_('behavior.modal');

$config = AFactory::getConfig();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col width-50">
		<fieldset class="adminform">
    		<legend><?php echo JText::_('CUSTOMER'); ?></legend>
    		<table class="admintable">
    			<tr>
    				<td class="key"><label title="<?php echo $this->escape(JText::_('CUSTOMER')); ?>::<?php echo $this->escape(JText::_('CREATE_NEW_RESERVATION_INFO')); ?>" class="hasTip"><?php echo JText::_('CUSTOMER'); ?>:</label></td>
    				<td><?php echo JElementCustomer::fetchElement($this->reservation->customer); ?></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="title_before"><?php echo JText::_('TITLE_BEFORE'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="title_before" id="title_before" size="30" maxlength="255" value="<?php echo $this->reservation->title_before; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="firstname"><?php echo JText::_('FIRST_NAME'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="firstname" id="firstname" size="30" maxlength="255" value="<?php echo $this->reservation->firstname; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="middlename"><?php echo JText::_('MIDDLE_NAME'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="middlename" id="middlename" size="30" maxlength="255" value="<?php echo $this->reservation->middlename; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="surname"><?php echo JText::_('SURNAME'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="surname" id="surname" size="30" maxlength="255" value="<?php echo $this->reservation->surname; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="title_after"><?php echo JText::_('TITLE_AFTER'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="title_after" id="title_after" size="30" maxlength="255" value="<?php echo $this->reservation->title_after; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="company"><?php echo JText::_('COMPANY'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="company" id="company" size="30" maxlength="255" value="<?php echo $this->reservation->company; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="company_id"><?php echo JText::_('COMPANY_ID'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="company_id" id="company_id" size="30" maxlength="255" value="<?php echo $this->reservation->company_id; ?>" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label for="vat_id"><?php echo JText::_('VAT_ID'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="vat_id" id="vat_id" size="30" maxlength="255" value="<?php echo $this->reservation->vat_id; ?>" /></td>
    			</tr>    			    			
    			<?php foreach ($this->getCustomFields() as $field) { ?>
					<tr>
						<td class="key"><label for="<?php echo JArrayHelper::getValue($field, 'name'); ?>"><?php echo JArrayHelper::getValue($field, 'title'); ?>: </label></td>
    					<td><?php echo AHtml::getField($field, $this->reservation->fields); ?></td>
		    		</tr>
		    	<?php }
    				if (!$this->reservation->id) {
		    	?>
		    		<tr>
		    			<td class="key"><label for="notify_customer"><?php echo JText::_('NOTIFY_CUSTOMER_BY_E_MAIL'); ?>: </label></td>
		    			<td><input type="checkbox" name="notify_customer" id="notify_customer" value="1" /></td>
		    		</tr>
		    	<?php } ?>
    		</table>
    	</fieldset>
        
        <div class="clr"></div>
        
        <fieldset id="addMoreNames" class="adminform addMoreNames" style="display: <?php echo empty($this->reservation->more_names) ? 'none' : 'block'; ?>">
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

    	<div id="reservedItems">
    	
    	<?php if ($this->ajaxForItems) { ob_clean(); ob_start(); } ?>
    	
    	<?php 
    	$options = array();
    	$options[] = JHTML::_('select.option', '', JText::_('SELECT_LIST'));
		$options[] = JHTML::_('select.option', RESERVATION_TYPE_HOURLY, JText::_('HOURLY'));
		$options[] = JHTML::_('select.option', RESERVATION_TYPE_DAILY, JText::_('DAILY'));
		$options[] = JHTML::_('select.option', RESERVATION_TYPE_PERIOD, JText::_('PERIOD'));
						
    	if (count($this->reservedItems))
    		$k = 0;
    	    $ir = 0;
    		foreach ($this->reservedItems as $key => $reservedItem){
				/* @var $reservedItem TableReservationItems */
				TableReservationItems::display($reservedItem);
				
				$subject = $this->subjects[$reservedItem->subject];
				/* @var $subject TableSubject */
				TableSubject::prepare($subject);
				
				$id = empty($reservedItem->id) ? $k++ : $reservedItem->id;
    			?>
    	<fieldset class="adminform" id="reservationItem<?php echo $reservedItem->id ? $reservedItem->id : $key; ?>">		
    			
    	<?php if (!empty($reservedItem->boxIds)) { // adding new reservation ?>		
    		<?php foreach ($reservedItem->boxIds as $bid) { ?>
				<input type="hidden" name="boxIds[<?php echo $id?>][]" value="<?php echo $bid; ?>" />
			<?php } ?>
		<?php } else { // edit existing ?>
			<input type="hidden" name="boxIds[<?php echo $id?>][]" value="" />
		<?php } ?>
		<input type="hidden" name="ctype[<?php echo $id?>]" value="<?php echo @$reservedItem->ctype; ?>" />
    	
    	
    		<legend><?php echo $reservedItem->subject_title; ?></legend>
    		
    		<input type="hidden" name="id[<?php echo $id?>]" value="<?php echo $id; ?>">
    		<input type="hidden" name="subject[<?php echo $id?>]" value="<?php echo $reservedItem->subject; ?>">
    		<input type="hidden" name="rtype[<?php echo $id?>]" value="<?php echo $reservedItem->rtype; ?>">
    		
    		<table class="admintable">
    			<tr>	
    				<td></td>
    				<td class="aIconLegend aIconUnpublish">
   						<a href="javascript:removeReservationItem('<?php echo $reservedItem->id ? $reservedItem->id : $key; ?>', '<?php echo $this->reservation->id; ?>')" title=""><?php echo JText::_('REMOVE_ITEM'); ?></a>
    				</td>
    			</tr>    			
    			<tr>	
    				<td class="key"><label for="subject_title[<?php echo $id?>]"><?php echo JText::_('SUBJECT_TITLE'); ?>:</label></td>
    				<td><input class="text_area" type="text" name="subject_title[<?php echo $id?>]" id="subject_title[<?php echo $id?>]" size="30" maxlength="255" value="<?php echo $reservedItem->subject_title; ?>" /></td>
    			</tr>
    			<?php if ($config->parentsBookable == 2) { ?>
					<tr>	
    					<td class="key"><label for="subject_title[<?php echo $id?>]"><?php echo JText::_('SUB_ITEM'); ?>:</label></td>
    					<td>
    					    <?php echo BookingHelper::getSubjectSelectBox($reservedItem->sub_subject, "sub_subject[$id]", false, $reservedItem->subject); ?>
    					</td>
    				</tr>
    			<?php } ?>
    			<?php if ($reservedItem->rtype == RESERVATION_TYPE_PERIOD) { ?>
    				<tr>	
    					<td class="key"><label for="period_time_up[<?php echo $id?>]"><?php echo JText::_('PERIOD_TIME_UP'); ?>: </label></td>
    					<td><input type="text" name="period_time_up[<?php echo $id?>]" value="<?php echo $reservedItem->period_time_up?>" /></td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_time_down[<?php echo $id?>]"><?php echo JText::_('PERIOD_TIME_DOWN'); ?>: </label></td>
                        <td><input type="text" name="period_time_down[<?php echo $id?>]" value="<?php echo $reservedItem->period_time_down?>" /></td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_time_down[<?php echo $id?>]"><?php echo JText::_('PERIOD_TYPE'); ?>: </label></td>
    					<td>
    						<?php 
    							$options = array(JHtml::_('select.option', '', JText::_('SELECT_PERIOD_TYPE')));
    							$options[] = JHtml::_('select.option', PERIOD_TYPE_DAILY, JText::_('DAILY'));
    							$options[] = JHtml::_('select.option', PERIOD_TYPE_WEEKLY, JText::_('WEEKLY'));
    							$options[] = JHtml::_('select.option', PERIOD_TYPE_MONTHLY, JText::_('MONTHLY'));
    							$options[] = JHtml::_('select.option', PERIOD_TYPE_YEARLY, JText::_('YEARLY'));
    							echo JHtml::_('select.genericlist', $options, 'period_type['.$id.']', '', 'value', 'text', $reservedItem->period_type);
    					 	?>
                            <input type="hidden" name="period_rtype_id[<?php echo $id;?>]" value="<?php echo $reservedItem->period_rtype_id; ?>" />
                            <input type="hidden" name="period_price_id[<?php echo $id;?>]" value="<?php echo $reservedItem->period_price_id; ?>" />
    					 </td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_recurrence[<?php echo $id?>]"><?php echo JText::_('PERIOD_RECURRENCE'); ?>: </label></td>
    					<td><input type="text" name="period_recurrence[<?php echo $id?>]" id="period_recurrence[<?php echo $id?>]" size="30" maxlength="255" value="<?php echo $reservedItem->period_recurrence; ?>" /></td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_monday[<?php echo $id?>]"><?php echo JText::_('PERIOD_MONDAY'); ?>: </label></td>
    					<td>
    						<input type="hidden" name="period_monday[<?php echo $id?>]" value="0" />
    						<input type="checkbox" name="period_monday[<?php echo $id?>]" id="period_monday[<?php echo $id?>]" value="1" <?php if ($reservedItem->period_monday) { ?>checked="checked"<?php } ?> />
    					</td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_tuesday[<?php echo $id?>]"><?php echo JText::_('PERIOD_TUESDAY'); ?>: </label></td>
    					<td>
    						<input type="hidden" name="period_tuesday[<?php echo $id?>]" value="0" />
    						<input type="checkbox" name="period_tuesday[<?php echo $id?>]" id="period_tuesday[<?php echo $id?>]" value="1" <?php if ($reservedItem->period_tuesday) { ?>checked="checked"<?php } ?> />
    					</td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_wednesday[<?php echo $id?>]"><?php echo JText::_('PERIOD_WEDNESDAY'); ?>: </label></td>
    					<td>
    						<input type="hidden" name="period_wednesday[<?php echo $id?>]" value="0" />
    						<input type="checkbox" name="period_wednesday[<?php echo $id?>]" id="period_wednesday[<?php echo $id?>]" value="1" <?php if ($reservedItem->period_wednesday) { ?>checked="checked"<?php } ?> />
    					</td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_thursday[<?php echo $id?>]"><?php echo JText::_('PERIOD_THURSDAY'); ?>: </label></td>
    					<td>
    						<input type="hidden" name="period_thursday[<?php echo $id?>]" value="0" />
    						<input type="checkbox" name="period_thursday[<?php echo $id?>]" id="period_thursday[<?php echo $id?>]" value="1" <?php if ($reservedItem->period_thursday) { ?>checked="checked"<?php } ?> />
    					</td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_friday[<?php echo $id?>]"><?php echo JText::_('PERIOD_FRIDAY'); ?>: </label></td>
    					<td>
    						<input type="hidden" name="period_friday[<?php echo $id?>]" value="0" />
    						<input type="checkbox" name="period_friday[<?php echo $id?>]" id="period_friday[<?php echo $id?>]" value="1" <?php if ($reservedItem->period_friday) { ?>checked="checked"<?php } ?> />
    					</td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_saturday[<?php echo $id?>]"><?php echo JText::_('PERIOD_SATURDAY'); ?>: </label></td>
    					<td>
    						<input type="hidden" name="period_saturday[<?php echo $id?>]" value="0" />
    						<input type="checkbox" name="period_saturday[<?php echo $id?>]" id="period_saturday[<?php echo $id?>]" value="1" <?php if ($reservedItem->period_saturday) { ?>checked="checked"<?php } ?> />
    					</td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_sunday[<?php echo $id?>]"><?php echo JText::_('PERIOD_SUNDAY'); ?>: </label></td>
    					<td>
    						<input type="hidden" name="period_sunday[<?php echo $id?>]" value="0" />
    						<input type="checkbox" name="period_sunday[<?php echo $id?>]" id="period_sunday[<?php echo $id?>]" value="1" <?php if ($reservedItem->period_sunday) { ?>checked="checked"<?php } ?> />
    					</td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_month[<?php echo $id?>]"><?php echo JText::_('PERIOD_MONTH'); ?>: </label></td>
    					<td>
    						<?php 
    							$options = array(JHtml::_('select.option', '', JText::_('SELECT_PERIOD_MONTH')));
    							$options[] = JHtml::_('select.option', 1, JText::_('JANUARY'));
    							$options[] = JHtml::_('select.option', 2, JText::_('FEBRUARY'));
    							$options[] = JHtml::_('select.option', 3, JText::_('MARCH'));
    							$options[] = JHtml::_('select.option', 4, JText::_('APRIL'));
    							$options[] = JHtml::_('select.option', 5, JText::_('MAY'));
    							$options[] = JHtml::_('select.option', 6, JText::_('JUNE'));
    							$options[] = JHtml::_('select.option', 7, JText::_('JULY'));
    							$options[] = JHtml::_('select.option', 8, JText::_('AUGUST'));
    							$options[] = JHtml::_('select.option', 9, JText::_('SEPTEMBER'));
    							$options[] = JHtml::_('select.option', 10, JText::_('OCTOBER'));
    							$options[] = JHtml::_('select.option', 11, JText::_('NOVEMBER'));
    							$options[] = JHtml::_('select.option', 12, JText::_('DECEMBER'));
    							echo JHtml::_('select.genericlist', $options, 'period_month['.$id.']', '', 'value', 'text', $reservedItem->period_month);
    					 	?>
    					 </td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_week<?php echo $id?>"><?php echo JText::_('PERIOD_WEEK'); ?>: </label></td>
    					<td>
    						<?php 
    							$options = array(JHtml::_('select.option', '', JText::_('SELECT_PERIOD_WEEK')));
    							$options[] = JHtml::_('select.option', 1, JText::_('J1ST_WEEK'));
    							$options[] = JHtml::_('select.option', 2, JText::_('J2ND_WEEK'));
    							$options[] = JHtml::_('select.option', 3, JText::_('J3RD_WEEK'));
    							$options[] = JHtml::_('select.option', 4, JText::_('J4TH_WEEK'));
    							echo JHtml::_('select.genericlist', $options, 'period_week['.$id.']', '', 'value', 'text', $reservedItem->period_week);
    					 	?>
    					 </td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_day[<?php echo $id?>]"><?php echo JText::_('PERIOD_DAY'); ?>: </label></td>
    					<td>
    						<?php 
    							$options = array(JHtml::_('select.option', '', JText::_('SELECT_PERIOD_DAY')));
    							$options[] = JHtml::_('select.option', 1, JText::_('MONDAY'));
    							$options[] = JHtml::_('select.option', 2, JText::_('Tuesday'));
    							$options[] = JHtml::_('select.option', 3, JText::_('Wednesday'));
    							$options[] = JHtml::_('select.option', 4, JText::_('Thursday'));
    							$options[] = JHtml::_('select.option', 5, JText::_('Friday'));
    							$options[] = JHtml::_('select.option', 6, JText::_('Saturday'));
    							$options[] = JHtml::_('select.option', 7, JText::_('SUNDAY'));
    							echo JHtml::_('select.genericlist', $options, 'period_day['.$id.']', '', 'value', 'text', $reservedItem->period_day);
    					 	?>
    					 </td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_end[<?php echo $id?>]"><?php echo JText::_('PERIOD_END_TYPE'); ?>: </label></td>
    					<td>
    						<?php 
    							$options = array(JHtml::_('select.option', '', JText::_('SELECT_PERIOD_END_TYPE')));
    							$options[] = JHtml::_('select.option', PERIOD_END_TYPE_NO, JText::_('NO_END_DATE'));
    							$options[] = JHtml::_('select.option', PERIOD_END_TYPE_AFTER, JText::_('END_AFTER_OCCURRENCES'));
    							$options[] = JHtml::_('select.option', PERIOD_END_TYPE_DATE, JText::_('END_BY_DATE'));
    							echo JHtml::_('select.genericlist', $options, 'period_end['.$id.']', '', 'value', 'text', $reservedItem->period_end);
    					 	?>
    					 </td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_occurrences[<?php echo $id?>]"><?php echo JText::_('PERIOD_OCCURRENCES'); ?>: </label></td>
    					<td><input type="text" name="period_occurrences[<?php echo $id?>]" id="period_occurrences[<?php echo $id?>]" size="30" maxlength="255" value="<?php echo $reservedItem->period_occurrences; ?>" /></td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_date_up<?php echo $id?>"><?php echo JText::_('PERIOD_DATE_UP'); ?>: </label></td>
    					<td><?php echo JHtml::_('calendar', $reservedItem->period_date_up, 'period_date_up['.$id.']', 'period_date_up'.$id); ?></td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="period_date_down<?php echo $id?>"><?php echo JText::_('PERIOD_DATE_DOWN'); ?>: </label></td>
    					<td><?php echo JHtml::_('calendar', $reservedItem->period_date_down, 'period_date_down['.$id.']', 'period_date_down'.$id); ?></td>
    				</tr>
    			<?php } else { ?>
    				<tr>	
    					<td class="key"><label for="from[<?php echo $id?>]"><?php echo JText::_('FROM'); ?>:</label></td>
    					<td><?php if ($this->reservation->id)	
    								echo AHtml::getCalendar($reservedItem->from, 'from['.$id.']', 'from['.$id.']', ADATE_FORMAT_LONG, ADATE_FORMAT_LONG_CAL,'',true,false);
    							  else
    								echo AHtml::date($reservedItem->from, ADATE_FORMAT_LONG, 0); ?></td>
    				</tr>
    				<tr>	
    					<td class="key"><label for="to[<?php echo $id?>]"><?php echo JText::_('TO'); ?>:</label></td>
    					<td><?php if ($this->reservation->id)	
    								echo AHtml::getCalendar($reservedItem->to, 'to['.$id.']', 'to['.$id.']', ADATE_FORMAT_LONG, ADATE_FORMAT_LONG_CAL,'',true,false);
    							  else
    								echo AHtml::date($reservedItem->to, ADATE_FORMAT_LONG, 0); ?></td>
    				</tr>
    			<?php } ?>
    			<tr>	
    				<td class="key"><label for="capacity[<?php echo $id?>]"><?php echo JText::_('CAPACITY'); ?>:</label></td>
    				<td>
    					<select name="capacity[<?php echo $id?>]" id="capacity[<?php echo $id?>]" <?php if (!$this->reservation->id) { ?>onchange="refreshReservation()"<?php } ?>>
    						<?php for ($i = 1; $i <= $subject->total_capacity; $i++) { ?>
    							<option value="<?php echo $i; ?>"<?php if ($i == $reservedItem->capacity) { ?> selected="selected"<?php } ?>>
    								<?php echo $i; ?>
    							</option>
    						<?php } ?>
    					</select>
    				</td>
    			</tr>    			
    			<tr>	
    				<td class="key"><label for="price[<?php echo $id?>]"><?php echo ITEM_PRICE_TIP ?>:</label></td>
    				<td><input class="text_area" type="text" name="price[<?php echo $id?>]" id="price[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->price; ?>" onkeyup="ACommon.toFloat(this)" <?php if (! $this->reservation->id) { ?>disabled="disabled"<?php } ?> /><span class="currency"><?php echo $config->mainCurrency; ?></span></td>
    			</tr>    	
	    		<?php 
					foreach($subject->occupancy_types as $otype) {
						$min = $otype->type == 0 ? $subject->standard_occupancy_min : $subject->extra_occupancy_min;
						$max = $otype->type == 0 ? $subject->standard_occupancy_max : $subject->extra_occupancy_max;
				?>
						<tr>	
    						<td class="key"><label for="occupancy<?php echo $otype->id; ?>"><?php echo $otype->title; ?>: </label></td>
    						<td>
    							<?php if (!$this->reservation->id) { ?>
									<select name="occupancy[<?php echo $id?>][<?php echo $otype->id; ?>]" <?php if (!$this->reservation->id) { ?>onchange="refreshReservation()"<?php } ?>>
										<option value="0">-</option>
										<?php echo JHtml::_('select.options', array_combine(range($min, $max), range($min, $max)), '', '', $reservedItem->occupancy[$otype->id]['count']); ?>
									</select>
									<input type="text" name="occupancy[<?php echo $id?>][<?php echo $otype->id; ?>][total]" value="<?php echo $reservedItem->occupancy[$otype->id]['total']; ?>" size="1" disabled="disabled" />
									<span class="currency"><?php echo $config->mainCurrency; ?></span>
								<?php } else { ?>
									<select name="occupancy[<?php echo $id?>][<?php echo $otype->id; ?>][count]" <?php if (!$this->reservation->id) { ?>onchange="refreshReservation()"<?php } ?>>
										<option value="0">-</option>
										<?php echo JHtml::_('select.options', array_combine(range($min, $max), range($min, $max)), '', '', $reservedItem->occupancy[$otype->id]['count']); ?>
									</select>
									<?php if (!empty($reservedItem->occupancy[$otype->id])) { ?>
										<?php foreach ($reservedItem->occupancy[$otype->id] as $var => $val) { ?>
											<?php if ($var == 'total') { ?>
												<input type="text" name="occupancy[<?php echo $id?>][<?php echo $otype->id; ?>][<?php echo $var; ?>]" value="<?php echo $val; ?>" size="1" />
												<span class="currency"><?php echo $config->mainCurrency; ?></span>
											<?php } elseif ($var != 'count') { ?>
												<input type="hidden" name="occupancy[<?php echo $id?>][<?php echo $otype->id; ?>][<?php echo $var; ?>]" value="<?php echo $val; ?>" />
											<?php } ?>
										<?php } ?>
									<?php } ?>
								<?php } ?>
							</td>
						</tr>
				<?php } ?>    					
	    		<?php
	    			foreach ($reservedItem->supplementsRaw as $supplement) {
						$fullPrice = $value = $capacity = null;
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
						<tr>
							<td class="key">
								<label for="supplement<?php echo $supplement->id; ?>"><?php echo $supplement->title; ?>: </label>
							</td>
							<td>
								<fieldset class="radio">
									<?php echo BookingHelper::displaySupplementInput($supplement, $value, $capacity, $id, ($this->reservation->id ? '' : 'onchange="refreshReservation()"'));
									if ($fullPrice) { ?>
    									<span class="currency"><?php echo JText::_('FULL_PRICE') . ': ' . BookingHelper::displayPrice($fullPrice); ?></span>
    								<?php } ?>
								</fieldset>
							</td>
						</tr>	    			
	    		<?php } ?>
    			<tr>	
                    <td class="key"><label for="provision[<?php echo $id?>]"><?php echo JText::_('PROVISION'); ?>:</label></td>
    				<td><input class="text_area" type="text" name="provision[<?php echo $id?>]" id="provision[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->provision; ?>" onkeyup="ACommon.toFloat(this)" <?php if (! $this->reservation->id) { ?>disabled="disabled"<?php } ?> /><span class="currency"><?php echo $config->mainCurrency; ?></span></td>
    			</tr>                        
    			<tr>	
    				<td class="key"><label for="deposit[<?php echo $id?>]"><?php echo ITEM_DEPOSIT_TIP ?>:</label></td>
    				<td><input class="text_area" type="text" name="deposit[<?php echo $id?>]" id="deposit[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->deposit; ?>" onkeyup="ACommon.toFloat(this)" <?php if (! $this->reservation->id) { ?>disabled="disabled"<?php } ?> /><span class="currency"><?php echo $config->mainCurrency; ?></span></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="fullDeposit[<?php echo $id?>]"><?php echo FULL_DEPOSIT_TIP ?>:</label></td>
    				<td><input class="text_area" type="text" name="fullDeposit[<?php echo $id?>]" id="fullDeposit[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->fullDeposit; ?>" onkeyup="ACommon.toFloat(this)" <?php if (! $this->reservation->id) { ?>disabled="disabled"<?php } ?> /><span class="currency"><?php echo $config->mainCurrency; ?></span></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="priceExcludingTax[<?php echo $id?>]"><?php echo JText::_('TOTAL_PRICE_EXCLUDING_TAX'); ?>:</label></td>
    				<td><input class="text_area" type="text" name="priceExcludingTax[<?php echo $id?>]" id="priceExcludingTax[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo round(BookingHelper::getPriceExcludingTax(null, $reservedItem), 2); ?>" disabled="disabled" /><span class="currency"><?php echo $config->mainCurrency; ?></span></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="tax[<?php echo $id?>]"><?php echo JText::_('Tax'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="tax[<?php echo $id?>]" id="tax[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->tax; ?>" onkeyup="ACommon.toFloat(this)" <?php if (! $this->reservation->id) { ?>disabled="disabled"<?php } ?> /><span class="currency">%</span></td>
    			</tr>    				    		
    			<tr>	
    				<td class="key"><label for="fullPrice[<?php echo $id?>]"><?php echo FULL_PRICE_TIP ?>:</label></td>
    				<td><input class="text_area" type="text" name="fullPrice[<?php echo $id?>]" id="fullPrice[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->fullPrice; ?>" onkeyup="ACommon.toFloat(this)" <?php if (! $this->reservation->id) { ?>disabled="disabled"<?php } ?> /><span class="currency"><?php echo $config->mainCurrency; ?></span></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="fullPriceSupplements[<?php echo $id?>]"><?php echo FULL_PRICE_SUPPLEMENTS_TIP ?>:</label></td>
    				<td><input class="text_area" type="text" name="fullPriceSupplements[<?php echo $id?>]" id="fullPriceSupplements[<?php echo $id?>]" size="1" maxlength="255" value="<?php echo $reservedItem->fullPriceSupplements; ?>" onkeyup="ACommon.toFloat(this)" <?php if (! $this->reservation->id) { ?>disabled="disabled"<?php } ?> /><span class="currency"><?php echo $config->mainCurrency; ?></span></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="message[<?php echo $id?>]"><?php echo JText::_('Message') ?>:</label></td>
    				<td><textarea rows="4" cols="10" name="message[<?php echo $id?>]" id="message[<?php echo $id?>]" style="width: 100%"><?php echo $reservedItem->message; ?></textarea>
    			</tr>
    			<?php if (!$this->reservation->id) { ?>   
    				<tr>	
    					<td></td>
    					<td><button class="btn" onclick="return refreshReservation()"><?php echo JText::_('REFRESH'); ?></button></td>
    				</tr> 			
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
                                    <?php echo JText::_('FIRST_NAME'); ?>
                                    <input type="text" name="more_names[<?php echo $id; ?>][<?php echo $q; ?>][firstname]" id="more_names_firstname<?php echo $id.'-'.$q; ?>" value="<?php echo $this->escape(@$reservedItem->more_names[$q]->firstname); ?>" />  
                                </div>
                                <div class="field">
                                    <?php echo JText::_('SURNAME'); ?>
                                    <input type="text" name="more_names[<?php echo $id;?>][<?php echo $q; ?>][surname]" id="more_names_surname<?php echo $id.'-'.$q; ?>" value="<?php echo $this->escape(@$reservedItem->more_names[$q]->surname); ?>" />
                                </div>
                            </td>
                        </tr>
                <?php   }
                    } ?>                    
    		</table>
    	</fieldset>
		<?php } ?>
		
		<?php if ($this->ajaxForItems) $ajaxOutput['items'] = ob_get_clean(); ?>
		
    </div>
    
    </div>
    <div class="col width-50">
    	<fieldset class="adminform">
    		<legend><?php echo JText::_('CONTACT'); ?></legend>
    		<table class="admintable">
    			<tr>	
    				<td class="key"><label for="street"><?php echo JText::_('STREET'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="street" id="street" size="30" maxlength="255" value="<?php echo $this->reservation->street; ?>" /></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="city"><?php echo JText::_('CITY'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="city" id="city" size="30" maxlength="255" value="<?php echo $this->reservation->city; ?>" /></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="zip"><?php echo JText::_('ZIP'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="zip" id="zip" size="30" maxlength="255" value="<?php echo $this->reservation->zip; ?>" /></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="country"><?php echo JText::_('COUNTRY'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="country" id="country" size="30" maxlength="255" value="<?php echo $this->reservation->country; ?>" /></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="email"><?php echo JText::_('EMAIL'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="email" id="email" size="30" maxlength="255" value="<?php echo $this->reservation->email; ?>" /></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="telephone"><?php echo JText::_('TELEPHONE'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="telephone" id="telephone" size="30" maxlength="255" value="<?php echo $this->reservation->telephone; ?>" /></td>
    			</tr>
    			<tr>	
    				<td class="key"><label for="fax"><?php echo JText::_('FAX'); ?>: </label></td>
    				<td><input class="text_area" type="text" name="fax" id="fax" size="30" maxlength="255" value="<?php echo $this->reservation->fax; ?>" /></td>
    			</tr>
    			<tr>	
		    		<td class="key"><label for="note"><?php echo JText::_('NOTE'); ?>: </label></td>
		    		<td>
		    			<textarea name="note" id="note" cols="10" rows="10" style="width: 100%"><?php echo $this->reservation->note; ?></textarea>
		    		</td>
		    	</tr>
    		</table>
    	</fieldset>

    	<div id="reservationTotal">
    	
    	<?php if ($this->ajaxForItems) { ob_clean(); ob_start(); } ?>
    	
    	<fieldset class="adminform">
    		<legend><?php echo JText::_('RESERVATION_STATUS_AND_PAYMENT'); ?></legend>
    		
    		<table class="admintable">
    			<tr>	
    				<td class="key"><label><?php echo JText::_('PAYMENT_STATUS'); ?>: </label></td>
    				<td><?php echo JHtml::_('select.genericlist', BookingHelper::getPaymentStatuses(), 'paid', '', 'id', 'label', $this->reservation->paid); ?></td>
    			</tr>
    			<tr>	
    				<td class="key"><label><?php echo JText::_('RESERVATION_STATUS'); ?>: </label></td>
    				<td>
    				<?php if ($this->reservation->state === null) $this->reservation->state = RESERVATION_ACTIVE; ?>
						<select name="state" id="state">
							<option value="<?php echo RESERVATION_PRERESERVED; ?>" <?php if ($this->reservation->state == RESERVATION_PRERESERVED) { ?>selected="selected"<?php } ?>>
								<?php echo JText::_('PRE_RESERVED'); ?></option>
								<option value="<?php echo RESERVATION_ACTIVE; ?>" <?php if ($this->reservation->state == RESERVATION_ACTIVE) { ?>selected="selected"<?php } ?>>
								<?php echo JText::_('RESERVED'); ?></option>
								<option value="<?php echo RESERVATION_STORNED; ?>" <?php if ($this->reservation->state == RESERVATION_STORNED) { ?>selected="selected"<?php } ?>>
								<?php echo JText::_('CANCELLED'); ?></option>
								<option value="<?php echo RESERVATION_TRASHED; ?>" <?php if ($this->reservation->state == RESERVATION_TRASHED) { ?>selected="selected"<?php } ?>>
								<?php echo JText::_('TRASHED'); ?></option>
								<option value="<?php echo RESERVATION_CONFLICTED; ?>" <?php if ($this->reservation->state == RESERVATION_CONFLICTED) { ?>selected="selected"<?php } ?>>
								<?php echo JText::_('CONFLICTED'); ?></option>
						</select>
					</td>
    			</tr>
    			                <tr>
                    <td class="key"><label><?php echo JText::_('TOTAL_PROVISION'); ?>: </label></td>
                    <td><strong><?php echo BookingHelper::displayPrice($this->reservation->fullProvision); ?></strong></td>
                </tr>                
		   		<tr>
					<td class="key"><label><?php echo JText::_('DEPOSIT'); ?>: </label></td>
					<td><strong><?php echo BookingHelper::displayPrice($this->reservation->fullDeposit); ?></strong></td>
				</tr>                
				<tr>
			    	<td class="key"><label><?php echo JText::_('DEPOSIT_MUST_BE_PAID_BEFORE'); ?>: </label></td>
					<td><strong><?php echo $this->depositExpires; ?></strong></td>				
	    		</tr>                
                <tr>
                    <td class="key"><label><?php echo JText::_('TOTAL_PRICE_EXCLUDING_TAX'); ?>:</label></td>
	    			<td><strong><?php echo BookingHelper::displayPrice(BookingHelper::getPriceExcludingTax($this->reservation, $this->reservedItems)); ?></strong></td>    						
                </tr>                    
				<tr>
					<td class="key"><label><?php echo JText::_('TAX'); ?>: </label></td>
					<td><strong><?php echo BookingHelper::displayPrice(BookingHelper::getFullTax($this->reservedItems)); ?></strong></td>
				</tr>
    			<tr>
					<td class="key"><label><?php echo JText::_('TOTAL_PRICE'); ?>: </label></td>
					<td><strong><?php echo BookingHelper::displayPrice($this->reservation->fullPrice); ?></strong></td>
				</tr>                
    		</table>
    	</fieldset>
    	
    	<?php 
    		if ($this->ajaxForItems) { 
    			$ajaxOutput['total'] = ob_get_clean();
    			$ajaxOutput = json_encode($ajaxOutput);
    			die($ajaxOutput);
    		}
    	?>
    	
    	</div>
    	
   	</div>
   	<div class="clr">&nbsp;</div>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_RESERVATION; ?>"/>
	<input type="hidden" name="boxchecked" value="1"/>
	<input type="hidden" name="cid[]" value="<?php echo $this->reservation->id; ?>"/>
	<input type="hidden" name="task" value=""/>
	<?php echo JHTML::_('form.token'); ?>
</form>