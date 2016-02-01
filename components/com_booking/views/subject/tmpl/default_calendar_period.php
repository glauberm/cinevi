<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	views
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

/* @var $this BookingViewSubject */

// this calendar does not work with date - set only empty values
$this->setting = new stdClass();
$this->setting->day = $this->setting->month = $this->setting->year = $this->setting->week = '';

$cfg = AFactory::getConfig();
/* @var $cfg BookingConfig */

JHtml::_('behavior.modal');

?>
<div class="clr"></div>
<div id="bookingCalendar">
<!--AJAX_bookingCalendar-->
<div class="period">
	<h2><?php echo JText::_('PERIODIC_RESERVATION'); ?></h2>
	<fieldset class="timeframe">
		<legend><?php echo JText::_('TIMEFRAME'); ?></legend>
		<?php 
            $options = array(JHtml::_('select.option', '', JText::_('SELECT_TIMEFRAME')));
			
            foreach ($this->get('rtypes') as $rtype)
                if (!empty($rtype->prices))
                    foreach ($rtype->prices as $i => $price)
                        $options[] = JHtml::_('select.option', $i, $rtype->title . ' ' . JFactory::getDate($price->time_up)->format('H:i') . ' - ' . JFactory::getDate($price->time_down)->format('H:i') . ' ' . BookingHelper::displayPrice($price->value, null, $this->subject->tax));
			
            echo JHtml::_('select.genericlist', $options, 'period_timeframe', 'autocomplete="off"' . ($cfg->multiTimeFrame ? 'style="display: none"' : ''), 'value', 'text', ($cfg->multiTimeFrame ? 0 : null));
        		

            if ($cfg->multiTimeFrame) {
                
                $hours = array();
                foreach ($this->get('rtypes') as $rtype) {
                    foreach ($rtype->prices as $i => $price) {
                        $timeUp = $price->time_up;
                        $timeDown = $price->time_down == '00:00:00' ? '24:00:00' : $price->time_down;
                        if ($timeUp <= $timeDown) {
                            do {
                                $hours[] = JHtml::_('select.option', $timeUp, JFactory::getDate($timeUp)->format(ATIME_FORMAT));
                                $timeUp = JFactory::getDate($timeUp)->modify('+ ' . $rtype->time_unit . 'minutes')->format('H:i:s');                                
                                if ($timeUp == '00:00:00') {
                                    $hours[] = JHtml::_('select.option', $timeUp, JFactory::getDate($timeUp)->format(ATIME_FORMAT));
                                    break;
                                }
                            } while ($timeUp <= $timeDown);
                        }
                        break;
                    }
                    break;
                }
            
                $select = JHtml::_('select.option', '', '&ndash;&nbsp;'.JText::_('SELECT_CHECK_IN').'&nbsp;&ndash;');
                $periodTimeUp = $hours;
                array_pop($periodTimeUp);
                array_unshift($periodTimeUp, $select);
                echo JHtml::_('select.genericlist', $periodTimeUp, 'period_time_up', 'class="input-medium"', 'value', 'text', null, 'period_time_up_multi');
            
                $select = JHtml::_('select.option', '', '&ndash;&nbsp;'.JText::_('SELECT_CHECK_OUT').'&nbsp;&ndash;');
                $periodTimeDown = $hours;
                array_shift($periodTimeDown);
                array_unshift($periodTimeDown, $select);
                echo JHtml::_('select.genericlist', $periodTimeDown, 'period_time_down', 'class="input-medium"', 'value', 'text', null, 'period_time_down_multi'); ?>
        
                <input type="hidden" name="period_time_up_multi" id="period_time_up" value="" />
                <input type="hidden" name="period_time_down_multi" id="period_time_down" value="" />
            <?php } else { ?>     
                <input type="hidden" name="period_time_up" id="period_time_up" value="" />
                <input type="hidden" name="period_time_down" id="period_time_down" value="" />
            <?php } ?>
            <input type="hidden" name="period_rtype_id" id="period_rtype_id" value="<?php echo $rtype->id; ?>" />
            <input type="hidden" name="period_price_id" id="period_price_id" value="<?php echo $price->id; ?>" />
	</fieldset>
	<fieldset class="recurrence-pattern">
		<legend><?php echo JText::_('RECURRENCE_PATTERN'); ?></legend>
		<div class="type">
			<span <?php if (!$cfg->showRecurrencePatternDaily) { ?>style="display: none"<?php } ?>>
				<input type="radio" name="period_type" id="period_type_daily" value="<?php echo PERIOD_TYPE_DAILY; ?>" autocomplete="off" />
				<label for="period_type_daily"><?php echo JText::_('DAILY'); ?></label>
			</span>
			<span <?php if (!$cfg->showRecurrencePatternWeekly) { ?>style="display: none"<?php } ?>>
				<input type="radio" name="period_type" id="period_type_weekly" value="<?php echo PERIOD_TYPE_WEEKLY; ?>" autocomplete="off" />
				<label for="period_type_weekly"><?php echo JText::_('WEEKLY'); ?></label>
			</span>
			<span <?php if (!$cfg->showRecurrencePatternMonthly) { ?>style="display: none"<?php } ?>>
				<input type="radio" name="period_type" id="period_type_monthly" value="<?php echo PERIOD_TYPE_MONTHLY; ?>" autocomplete="off" />
				<label for="period_type_monthly"><?php echo JText::_('MONTHLY'); ?></label>
			</span>
			<span <?php if (!$cfg->showRecurrencePatternYearly) { ?>style="display: none"<?php } ?>>
				<input type="radio" name="period_type" id="period_type_yearly" value="<?php echo PERIOD_TYPE_YEARLY; ?>" autocomplete="off" />
				<label for="period_type_yearly"><?php echo JText::_('YEARLY'); ?></label>
			</span>
		</div>
		<div class="intensity" id="daily_weekly" style="display: none">
			<div id="recurrence">
				<label for="period_recurrence"><?php echo JText::_('RECUR_EVERY'); ?></label>
                <input type="text" name="period_recurrence" id="period_recurrence" value="" size="1" autocomplete="off"  class="input-mini"/>
				<label for="period_recurrence"><?php echo JText::_('WEEKS_ON'); ?></label>
			</div>
			<div class="clr"></div>
			<?php if ($cfg->firstDaySunday) { ?>
				<input type="checkbox" name="p" id="period_sunday_checkbox" disabled="disabled" />
				<input type="hidden" name="period_sunday" id="period_sunday_hidden" value="0" autocomplete="off" />
				<label for="period_sunday_checkbox"><?php echo JText::_('SUNDAY'); ?></label>
			<?php } ?>
			<input type="checkbox" name="p" id="period_monday_checkbox" disabled="disabled" />
			<input type="hidden" name="period_monday" id="period_monday_hidden" value="0" autocomplete="off" />
			<label for="period_monday_checkbox"><?php echo JText::_('MONDAY'); ?></label>
			
			<input type="checkbox" name="p" id="period_tuesday_checkbox" disabled="disabled" />
			<input type="hidden" name="period_tuesday" id="period_tuesday_hidden" value="0" autocomplete="off" />
			<label for="period_tuesday_checkbox"><?php echo JText::_('Tuesday'); ?></label>
			
			<input type="checkbox" name="p" id="period_wednesday_checkbox" disabled="disabled" />
			<input type="hidden" name="period_wednesday" id="period_wednesday_hidden" value="0" autocomplete="off" />
			<label for="period_wednesday_checkbox"><?php echo JText::_('Wednesday'); ?></label>
			
			<input type="checkbox" name="p" id="period_thursday_checkbox" disabled="disabled" />
			<input type="hidden" name="period_thursday" id="period_thursday_hidden" value="0" autocomplete="off" />
			<label for="period_thursday_checkbox"><?php echo JText::_('Thursday'); ?></label>
			
			<input type="checkbox" name="p" id="period_friday_checkbox" disabled="disabled" />
			<input type="hidden" name="period_friday" id="period_friday_hidden" value="0" autocomplete="off" />
			<label for="period_friday_checkbox"><?php echo JText::_('Friday'); ?></label>
			
			<input type="checkbox" name="p" id="period_saturday_checkbox" disabled="disabled" />
			<input type="hidden" name="period_saturday" id="period_saturday_hidden" value="0" autocomplete="off" />
			<label for="period_saturday_checkbox"><?php echo JText::_('Saturday'); ?></label>
			
			<?php if (!$cfg->firstDaySunday) { ?>
				<input type="checkbox" name="p" id="period_sunday_checkbox" disabled="disabled" />
				<input type="hidden" name="period_sunday" id="period_sunday_hidden" value="0" autocomplete="off" />
				<label for="period_sunday_checkbox"><?php echo JText::_('SUNDAY'); ?></label>
			<?php } ?>
		</div>
		<div class="intensity" id="monthly_yearly" style="display: none">
			<?php 
				$options = array(JHtml::_('select.option', '', '- ' . JText::_('SELECT_MONTH') . ' -'));
				
				$options[] = JHtml::_('select.option', 1,  JText::_('JANUARY'));
				$options[] = JHtml::_('select.option', 2,  JText::_('FEBRUARY'));
				$options[] = JHtml::_('select.option', 3,  JText::_('MARCH'));
				$options[] = JHtml::_('select.option', 4,  JText::_('APRIL'));
				$options[] = JHtml::_('select.option', 5,  JText::_('MAY'));
				$options[] = JHtml::_('select.option', 6,  JText::_('JUNE'));
				$options[] = JHtml::_('select.option', 7,  JText::_('JULY'));
				$options[] = JHtml::_('select.option', 8,  JText::_('AUGUST'));
				$options[] = JHtml::_('select.option', 9,  JText::_('SEPTEMBER'));
				$options[] = JHtml::_('select.option', 10, JText::_('OCTOBER'));
				$options[] = JHtml::_('select.option', 11, JText::_('NOVEMBER'));
				$options[] = JHtml::_('select.option', 12, JText::_('DECEMBER'));
				
				echo JHtml::_('select.genericlist', $options, 'period_month', 'class="input-medium"');
			
				$options = array(JHtml::_('select.option', '', '- ' . JText::_('SELECT_WEEK') . ' -'));
				
				$options[] = JHtml::_('select.option', 1, JText::_('J1ST_WEEK'));
				$options[] = JHtml::_('select.option', 2, JText::_('J2ND_WEEK'));
				$options[] = JHtml::_('select.option', 3, JText::_('J3RD_WEEK'));
				$options[] = JHtml::_('select.option', 4, JText::_('J4TH_WEEK'));
				
				echo JHtml::_('select.genericlist', $options, 'period_week', 'class="input-medium"');
				
				$options = array(JHtml::_('select.option', '', '- ' . JText::_('SELECT_DAY') . ' -'));
				
				$options[] = JHtml::_('select.option', 1, JText::_('MONDAY'));
				$options[] = JHtml::_('select.option', 2, JText::_('Tuesday'));
				$options[] = JHtml::_('select.option', 3, JText::_('Wednesday'));
				$options[] = JHtml::_('select.option', 4, JText::_('Thursday'));
				$options[] = JHtml::_('select.option', 5, JText::_('Friday'));
				$options[] = JHtml::_('select.option', 6, JText::_('Saturday'));
				$options[] = JHtml::_('select.option', 7, JText::_('SUNDAY'));
                
				echo JHtml::_('select.genericlist', $options, 'period_day', 'class="input-medium"');
				
			?>
		</div>
	</fieldset>
	<fieldset class="recurrence-range">
		<legend><?php echo JText::_('RANGE_OF_RECURRENCE'); ?></legend>
		<div class="begin">
			<label for="period_date_up"><?php echo JText::_('START'); ?></label>
			<?php echo JHtml::calendar('', 'period_date_up', 'period_date_up', '%Y-%m-%d', 'autocomplete="off"'); ?>
		</div>
		<div class="end">
			<div class="no-end" <?php if (!$cfg->showRangeOfRecurrenceNoEndDate) { ?>style="display: none"<?php } ?>>
				<input type="radio" name="period_end" id="period_end_no" value="<?php echo PERIOD_END_TYPE_NO; ?>" autocomplete="off" />
				<label for="period_end_no"><?php echo JText::_('NO_END_DATE'); ?></label>
				<div class="clr"></div>
			</div>
			<div class="end-after" <?php if (!$cfg->showRangeOfRecurrenceEndAfter) { ?>style="display: none"<?php } ?>>
				<input type="radio" name="period_end" id="period_occurrences" value="<?php echo PERIOD_END_TYPE_AFTER; ?>" autocomplete="off" />
				<label for="period_occurrences" id="period_occurrences_lbl"><?php echo JText::_('END_AFTER'); ?></label>
				<input type="text" name="period_occurrences" id="period_occurrences_occurrences" size="1" value="" disabled="disabled" autocomplete="off" class="input-mini "/>
				<label for="period_occurrences_occurrences"><?php echo JText::_('OCCURRENCES'); ?></label>
				<div class="clr"></div> 
			</div>
			<div class="end-by" <?php if (!$cfg->showRangeOfRecurrenceEndBy) { ?>style="display: none"<?php } ?>>
				<input type="radio" name="period_end" id="period_date_down" value="<?php echo PERIOD_END_TYPE_DATE; ?>" autocomplete="off" />
				<label for="period_date_down" id="period_date_down_lbl" ><?php echo JText::_('END_BY'); ?></label>
				<?php echo JHtml::calendar('', 'period_date_down', 'period_date_down_date', '%Y-%m-%d', 'disabled="disabled" autocomplete="off"'); ?>
				<div class="clr"></div>
			</div>
		</div>
		<div class="clr"></div>
		<?php if ($cfg->showCapacity) { ?>
			<div class="capacity">
				<label id="capacity"><?php echo JText::_('Quantity'); ?></label>
                <select name="capacity" id="capacity" onchange="Calendars.showTotal();Calendars.showOccupancy()" class="input-mini">
					<?php echo JHtml::_('select.options', array_combine(range($this->subject->minimum_capacity, $this->subject->total_capacity), range($this->subject->minimum_capacity, $this->subject->total_capacity)), '', '', $this->subject->minimum_capacity); ?>														
				</select>
				<label for="capacity"><?php echo $this->template->name; ?></label>
				<div class="clr"></div>
			</div>
		<?php } ?>
	</fieldset>
	<?php if (!empty($this->customer->id) || JFactory::getUser()->authorise('booking.reservation.create', 'com_booking')) { ?>
		<div class="tools">
			<div class="bookit bookInterval">
                <strong id="checkInfo"></strong>
				<a class="checkButton bookitButton" id="bookIt" href="javascript:Calendars.bookIt()">
					<?php echo JText::_('BOOK_IT'); ?></a>
			</div>
		</div>
	<?php } ?>
</div>
<!--/AJAX_bookingCalendar-->
</div>
<script type="text/javascript">
	//<![CDATA[
		window.addEvent('domready', function() {			
                        document.id('period_date_down_date_img').hide(); // hide end date - user selects end type
			<?php 
				$items = $rtypeIds = $priceTimeUps = $priceTimeDowns = $priceIds = array();
				$i = 1;
				foreach ($this->get('rtypes') as $rtype)
					if (!empty($rtype->prices))
						foreach ($rtype->prices as $price) {
							$item = array();
							// availability in week days for each timeframe
							$item[] = "0:[$price->monday]";	
							$item[] = "1:[$price->tuesday]";
							$item[] = "2:[$price->wednesday]";
							$item[] = "3:[$price->thursday]";
							$item[] = "4:[$price->friday]";
							$item[] = "5:[$price->saturday]";
							$item[] = "6:[$price->sunday]";
								
							$items[] = $i . ':{' . implode(', ', $item) . '}';
								
							$rtypeIds[] = "$i:[$price->rezervation_type]";
							$priceIds[] = "$i:[$price->id]";
							$priceTimeUps[] = "$i:['$price->time_up']";
							$priceTimeDowns[] = "$i:['$price->time_down']";
							
							$i++;
						}
				echo 'var weekdays = {' . implode(', ', $items) . '};' . "\n";
				echo 'var rtypeIds = {' . implode(', ', $rtypeIds) . '};' . "\n"; // list of reservation types ids for each timeframe							
				echo 'var priceIds = {' . implode(', ', $priceIds) . '};' . "\n"; // list of prices ids for each timeframe
				echo 'var priceTimeUps = {' . implode(', ', $priceTimeUps) . '};' . "\n"; // list of prices time ups for each timeframe
				echo 'var priceTimeDowns = {' . implode(', ', $priceTimeDowns) . '};' . "\n"; // list of prices time downs each timeframe
			?>
			document.id('period_timeframe').addEvent('change', function() { // on change timeframe
				if (this.selectedIndex != '') {
					document.id('period_rtype_id').value  = rtypeIds[this.selectedIndex]; // selected reservation type in form hidden field
					document.id('period_price_id').value  = priceIds[this.selectedIndex]; // selected price in form hidden field
					document.id('period_time_up').value   = priceTimeUps[this.selectedIndex]; // selected time up in form hidden field
					document.id('period_time_down').value = priceTimeDowns[this.selectedIndex]; // selected time down in form hidden field
				}
				if (document.id('period_type_daily').checked)
					document.id('period_type_daily').fireEvent('click');

				if (document.id('period_type_weekly').checked)
					document.id('period_type_weekly').fireEvent('click');

				if (document.id('period_type_monthly').checked)
					document.id('period_type_monthly').fireEvent('click');

				if (document.id('period_type_yearly').checked)
					document.id('period_type_yearly').fireEvent('click');
			});
			document.id('period_type_daily').addEvent('click', function() {
				document.id('daily_weekly').show();
				document.id('recurrence').hide();
				document.id('monthly_yearly').hide();
				
				document.id('period_monday_checkbox').disabled = document.id('period_tuesday_checkbox').disabled = document.id('period_wednesday_checkbox').disabled = document.id('period_thursday_checkbox').disabled = document.id('period_friday_checkbox').disabled = document.id('period_saturday_checkbox').disabled = document.id('period_sunday_checkbox').disabled = true;

				document.id('period_monday_checkbox').checked    = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][0] == '1' : false;
				document.id('period_tuesday_checkbox').checked   = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][1] == '1' : false;
				document.id('period_wednesday_checkbox').checked = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][2] == '1' : false;
				document.id('period_thursday_checkbox').checked  = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][3] == '1' : false;
				document.id('period_friday_checkbox').checked    = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][4] == '1' : false;
				document.id('period_saturday_checkbox').checked  = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][5] == '1' : false;
				document.id('period_sunday_checkbox').checked    = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][6] == '1' : false;
				
				document.id('period_monday_hidden').value        = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][0] : 0;
				document.id('period_tuesday_hidden').value       = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][1] : 0;
				document.id('period_wednesday_hidden').value     = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][2] : 0;
				document.id('period_thursday_hidden').value      = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][3] : 0;
				document.id('period_friday_hidden').value        = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][4] : 0;
				document.id('period_saturday_hidden').value      = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][5] : 0;
				document.id('period_sunday_hidden').value        = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][6] : 0;

				document.id('period_recurrence').value = '1';
				document.id('period_month').value = document.id('period_week').value = document.id('period_day').value = '';
			});
			document.id('period_type_weekly').addEvent('click', function() {
				
				document.id('daily_weekly').show();
				document.id('recurrence').show();
				document.id('monthly_yearly').hide();
				
				document.id('period_monday_checkbox').disabled    = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][0] == '0' : true;
				document.id('period_tuesday_checkbox').disabled   = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][1] == '0' : true;
				document.id('period_wednesday_checkbox').disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][2] == '0' : true;
				document.id('period_thursday_checkbox').disabled  = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][3] == '0' : true;
				document.id('period_friday_checkbox').disabled    = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][4] == '0' : true;
				document.id('period_saturday_checkbox').disabled  = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][5] == '0' : true;
				document.id('period_sunday_checkbox').disabled    = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][6] == '0' : true;

				document.id('period_monday_checkbox').checked = document.id('period_tuesday_checkbox').checked = document.id('period_wednesday_checkbox').checked = document.id('period_thursday_checkbox').checked = document.id('period_friday_checkbox').checked = document.id('period_saturday_checkbox').checked = document.id('period_sunday_checkbox').checked = false;

				document.id('period_monday_hidden').value = document.id('period_tuesday_hidden').value = document.id('period_wednesday_hidden').value = document.id('period_thursday_hidden').value = document.id('period_friday_hidden').value = document.id('period_saturday_hidden').value = document.id('period_sunday_hidden').value = 0;

				document.id('period_recurrence').value = document.id('period_month').value = document.id('period_week').value = document.id('period_day').value = '';
				
			});
			document.id('period_type_monthly').addEvent('click', function() {
				document.id('daily_weekly').hide();
				document.id('monthly_yearly').show();
				document.id('period_month').hide();

				document.id('period_monday_checkbox').checked = document.id('period_tuesday_checkbox').checked = document.id('period_wednesday_checkbox').checked = document.id('period_thursday_checkbox').checked =document.id('period_friday_checkbox').checked = document.id('period_saturday_checkbox').checked = document.id('period_sunday_checkbox').checked = false;

				document.id('period_monday_hidden').value = document.id('period_tuesday_hidden').value = document.id('period_wednesday_hidden').value = document.id('period_thursday_hidden').value = document.id('period_friday_hidden').value = document.id('period_saturday_hidden').value = document.id('period_sunday_hidden').value = 0;

				document.id('period_recurrence').value = document.id('period_month').value = '';

				document.id('period_day').options[1].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][0] == '0' : false;
				document.id('period_day').options[2].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][1] == '0' : false;
				document.id('period_day').options[3].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][2] == '0' : false;
				document.id('period_day').options[4].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][3] == '0' : false;
				document.id('period_day').options[5].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][4] == '0' : false;
				document.id('period_day').options[6].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][5] == '0' : false;
				document.id('period_day').options[7].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][6] == '0' : false;
			});
			document.id('period_type_yearly').addEvent('click', function() {
				document.id('daily_weekly').hide();
				document.id('monthly_yearly').show();
				document.id('period_month').show();

				document.id('period_monday_checkbox').checked = document.id('period_tuesday_checkbox').checked = document.id('period_wednesday_checkbox').checked = document.id('period_thursday_checkbox').checked =document.id('period_friday_checkbox').checked = document.id('period_saturday_checkbox').checked = document.id('period_sunday_checkbox').checked = false;

				document.id('period_monday_hidden').value = document.id('period_tuesday_hidden').value = document.id('period_wednesday_hidden').value = document.id('period_thursday_hidden').value = document.id('period_friday_hidden').value = document.id('period_saturday_hidden').value = document.id('period_sunday_hidden').value = 0;

				document.id('period_recurrence').value = '';

				document.id('period_day').options[1].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][0] == '0' : false;
				document.id('period_day').options[2].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][1] == '0' : false;
				document.id('period_day').options[3].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][2] == '0' : false;
				document.id('period_day').options[4].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][3] == '0' : false;
				document.id('period_day').options[5].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][4] == '0' : false;
				document.id('period_day').options[6].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][5] == '0' : false;
				document.id('period_day').options[7].disabled = document.id('period_timeframe').selectedIndex ? weekdays[document.id('period_timeframe').selectedIndex][6] == '0' : false;
			});
			document.id('period_end_no').addEvent('click', function() {
				document.id('period_occurrences_occurrences').disabled = true;
                                    document.id('period_date_down_date_img').hide();
				document.id('period_occurrences_occurrences').value = '';
				document.id('period_date_down_date').value = '';
			});
			document.id('period_occurrences').addEvent('click', function() {
				document.id('period_occurrences_occurrences').disabled = false;
                                    document.id('period_date_down_date_img').hide();
				document.id('period_date_down_date').value = '';
			});
			document.id('period_date_down').addEvent('click', function() {
				document.id('period_occurrences_occurrences').disabled = true;
				document.id('period_date_down_date').disabled = false;
                                    document.id('period_date_down_date_img').show();
				document.id('period_occurrences_occurrences').value = '';
			});
			document.id('period_monday_checkbox').addEvent('click', function() {
				document.id('period_monday_hidden').value = this.checked ? 1 : 0;
			});
			document.id('period_tuesday_checkbox').addEvent('click', function() {
				document.id('period_tuesday_hidden').value = this.checked ? 1 : 0;
			});
			document.id('period_wednesday_checkbox').addEvent('click', function() {
				document.id('period_wednesday_hidden').value = this.checked ? 1 : 0;
			});
			document.id('period_thursday_checkbox').addEvent('click', function() {
				document.id('period_thursday_hidden').value = this.checked ? 1 : 0;
			});
			document.id('period_friday_checkbox').addEvent('click', function() {
				document.id('period_friday_hidden').value = this.checked ? 1 : 0;
			});
			document.id('period_saturday_checkbox').addEvent('click', function() {
				document.id('period_saturday_hidden').value = this.checked ? 1 : 0;
			});
			document.id('period_sunday_checkbox').addEvent('click', function() {
				document.id('period_sunday_hidden').value = this.checked ? 1 : 0;
			});
                        if (!document.id('bookIt'))
                            return;
			document.id('bookIt').addEvent('click', function() {
				if (document.id('period_timeframe').value == '') {
					alert("<?php echo JText::_('YOU_MUST_SELECT_TIMEFRAME', true); ?>");
					return false;
				}
                if (document.id('period_time_up_multi') && document.id('period_time_down_multi')) {
                    if (document.id('period_time_up_multi').value == '' || document.id('period_time_down_multi').value == '') {
                        alert("<?php echo JText::_('YOU_MUST_SELECT_CHECK_IN_AND_CHECK_OUT', true); ?>");
                        return false;
                    } else if (document.id('period_time_up_multi').value >= document.id('period_time_down_multi').value && document.id('period_time_down_multi').value!='00:00:00') {
                        alert("<?php echo JText::_('CHECK_IN_AND_CHECK_OUT_INVALID', true); ?>");
                        return false;
                    }
                }
				if (!(document.id('period_type_daily').checked || document.id('period_type_weekly').checked || document.id('period_type_monthly').checked || document.id('period_type_yearly').checked)) {
					alert("<?php echo JText::_('YOU_MUST_SELECT_RECURRENCE_TYPE', true); ?>");
					return false;
				}				
				if (document.id('period_type_weekly').checked && !document.id('period_recurrence').value.match(/^[1-9]+[0-9]*$/)){
					alert("<?php echo JText::_('RECUR_EVERY_WEEKS_MUST_BE_AN_INTEGER', true); ?>");
					return false;
				}
				if (document.id('period_type_weekly').checked && !(document.id('period_sunday_checkbox').checked || document.id('period_monday_checkbox').checked || document.id('period_tuesday_checkbox').checked || document.id('period_wednesday_checkbox').checked || document.id('period_thursday_checkbox').checked || document.id('period_friday_checkbox').checked || document.id('period_saturday_checkbox').checked)) {
					alert("<?php echo JText::_('YOU_MUST_SELECT_AT_LEAST_ONE_WEEK_DAY', true); ?>");
					return false;
				}
				if (document.id('period_type_yearly').checked && document.id('period_month').value == '') {
					alert("<?php echo JText::_('YOU_MUST_SELECT_A_MONTH', true); ?>");
					return false;
				}
				if ((document.id('period_type_monthly').checked || document.id('period_type_yearly').checked) && document.id('period_week').value == '') {
					alert("<?php echo JText::_('YOU_MUST_SELECT_A_WEEK', true); ?>");
					return false;
				}
				if ((document.id('period_type_monthly').checked || document.id('period_type_yearly').checked) && document.id('period_day').value == '') {
					alert("<?php echo JText::_('YOU_MUST_SELECT_A_DAY', true); ?>");
					return false;
				}        
				if (document.id('period_date_up').value == '') {
					alert("<?php echo JText::_('YOU_MUST_SELECT_START_DAY', true); ?>");
					return false;
				} else {
					var d = new Date();
					if (document.id('period_date_up').value < d.format('%Y-%m-%d')) {
						alert("<?php echo JText::_('START_DAY_CANNOT_PAST', true); ?>");
						return false;
					}
				}
				if (!(document.id('period_end_no').checked || document.id('period_occurrences').checked || document.id('period_date_down').checked)) {
					alert("<?php echo JText::_('YOU_MUST_SELECT_END_TYPE', true); ?>");
					return false;
				}
				if (document.id('period_occurrences').checked && !document.id('period_occurrences_occurrences').value.match(/^[1-9]+[0-9]*$/)) {
					alert("<?php echo JText::_('END_AFTER_OCCURRENCES_MUST_BE_AN_INTEGER', true); ?>");
					return false;
				}
				if (document.id('period_date_down').checked && document.id('period_date_down_date').value == '') {
					alert("<?php echo JText::_('YOU_MUST_SELECT_END_DATE', true); ?>");
					return false;
				}  
				if (document.id('period_date_up').value > document.id('period_date_down').value) {
					alert("<?php echo JText::_('DATE_RANGE_IS_INVALID', true); ?>");
					return false;
				}
				
				document.bookSetting.controller.value = 'reservation';
				document.bookSetting.task.value = 'addPeriod';
				return true;
			});		
		});
	//]]>
</script>
