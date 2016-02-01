<?php

/**
 * Monthly calendar template.
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

/* @var $this BookingViewSubject */



$config = AFactory::getConfig();

$manager = $this->getLayout() == 'manager';

//$userCanReserve = $this->customer->id || $config->unRegisteregCanReserve;
$userCanReserve = $this->userCanReserve;
$this->setting = new BookingCalendarSetting();

//get number of shown months from template
if(empty($this->calendarnummonths))
{
	if(isset($this->templateTable->numberOfMonths) && $this->templateTable->numberOfMonths)
		$calMonths = $this->templateTable->numberOfMonths;
	else
		$calMonths = $config->calendarNumMonths;
	
}
else $calMonths = $this->calendarnummonths;
$calendarnummonths = $calMonths; // in the back-end admin has tool to change # of showed months

$this->days = &BookingHelper::getMonthlyCalendar($this->subject, $this->setting, $calendarnummonths, $this->isAdmin);
$week = $this->setting->week; 

ADocument::addDomreadyEvent('
Calendars.currentYear = "' . $this->setting->currentYear . '";
Calendars.currentMonth = "' . $this->setting->currentMonth . '";
Calendars.nextYear = "' . $this->setting->nextYear . '";
Calendars.nextMonth = "' . $this->setting->nextMonth . '";
Calendars.calendarType = "monthly";
');

//load csss colors for prices
//-----------------------
$colors = array();
foreach($this->days->prices as $prices)
{
	foreach($prices->prices as $priceIndex => $price)
	{
		$colors[$priceIndex] = $price->custom_color;
	}
}
$string = '';
foreach($colors as $priceIndex => $color)
{
	$string .= "#bookSetting .price".$priceIndex." {	background-color: ".$color.";} ";
}

$titleMakeReservation = JText::_('MAKE_S_RESERVATION_FOR_FULL_DAY_S_FOR_PRICE_S');
/* @var $titleMakeReservation string save translation into property for optimizing */
?>
<div id="bookingCalendar">
<!--AJAX_bookingCalendar-->
<style type="text/css"><?php echo $string; ?></style>
<?php
if ($config->buttonPosition == 0 && !$manager) echo $this->loadTemplate('bookitform');
?>
<div class="clr"></div>
<?php
if (!$manager)
    echo $this->loadTemplate('prices');  

if (!$this->isAdmin && $config->quickNavigator) { // customer has navigation in predefined length only
?>
	<div class="clr"></div>
	<div class="quickNavigator">
		<label for="imonth"><?php echo JText::_('SELECT_MONTH'); ?>: </label>
		<?php echo AHtml::getMonthsSelect('imonth', (int) $this->setting->lastMonth, $this->setting->lastYear, $config->calendarDeepMonth, 'onchange="Calendars.monthNavigation(this.value)"'); ?>
	</div>
<?php 
}

if ($this->isAdmin) { // admin has unlimited month and year navigation
?> 
	<div class="clr"></div>
	<div class="quickNavigator">
		<label for="imonth"><?php echo JText::_('SELECT_MONTH'); ?>: </label>
		<?php echo AHtml::monthYearPicker('imonth', $this->setting->lastMonth . '-' . $this->setting->lastYear); ?>
		<label for="calendarnummonths"><?php echo JText::_('NUMBER_OF_MONTHS'); ?></label>
		<input type="text" name="calendarnummonths" id="calendarnummonths" value="<?php echo $this->calendarnummonths; ?>" size="1" onchange="this.form.submit()"  class="input-mini" />
		<button><?php echo JText::_('SUBMIT'); ?></button>
	</div>
<?php } $monthNumber = 1; ?>
<div<?php if (false && $monthNumber++ < $this->setting->monthNumber) { ?> class="monthlyCalendarHidden"<?php } ?>>
<h2 class="subjectSubtitle calendarTitle"><?php echo ($this->setting->monthName . ' ' . $this->setting->lastYear); ?></h2>
<table id="top" class="monthlyCalendar">
	<!-- Days names header -->
	<?php
		ob_start(); 
		if ($this->subject->night_booking && $config->nightsStyle) { 
	?>
	<thead>
		<tr>
			<th><?php echo JText::_('WEEK'); ?></th>
			<?php if ($config->firstDaySunday) { ?>
				<th width="14%"><?php echo JText::_('SUN_TO_MON'); ?></th>
			<?php } ?>
			<th width="14%"><?php echo JText::_('MON_TO_TUE'); ?></th>
			<th width="14%"><?php echo JText::_('TUE_TO_WED'); ?></th>
			<th width="14%"><?php echo JText::_('WED_TO_THU'); ?></th>
			<th width="14%"><?php echo JText::_('THU_TO_FRI'); ?></th>
			<th width="14%"><?php echo JText::_('FRI_TO_SAT'); ?></th>
			<th width="14%"><?php echo JText::_('SAT_TO_SUN'); ?></th>
			<?php if (! $config->firstDaySunday) { ?>
				<th width="14%"><?php echo JText::_('SUN_TO_MON'); ?></th>
			<?php } ?>
		</tr>
	</thead>
	<?php } else { ?>
	<thead>
		<tr>
			<th><?php echo JText::_('WEEK'); ?></th>
			<?php if ($config->firstDaySunday) { ?>
				<th width="14%"><?php echo JText::_('Sun'); ?></th>
			<?php } ?>
			<th width="14%"><?php echo JText::_('MON'); ?></th>
			<th width="14%"><?php echo JText::_('Tue'); ?></th>
			<th width="14%"><?php echo JText::_('Wed'); ?></th>
			<th width="14%"><?php echo JText::_('Thu'); ?></th>
			<th width="14%"><?php echo JText::_('Fri'); ?></th>
			<th width="14%"><?php echo JText::_('Sat'); ?></th>
			<?php if (! $config->firstDaySunday) { ?>
				<th width="14%"><?php echo JText::_('Sun'); ?></th>
			<?php } ?>
		</tr>
	</thead>
	<?php 
		} 
		$head = ob_get_clean(); // save for next using
		echo $head;
	?>
	<!-- Body with months days -->
	<tbody>
	<tr>
			<td class="week">
				<span class="week"><?php echo $week ++; ?></span>
				<?php if ($config->enableResponsive) { ?>
					<span class="month"><?php //echo AHtml::date($day->date, ADATE_FORMAT_NICE_SHORT2, 0); ?></span>
				<?php } ?>
			</td>
		<?php 
			$pcount = count($this->days->calendar);
			$break = 0;
			$month = 1;
			for ($i = 0; $i < $pcount; $i++) {
				$day = $this->days->calendar[$i];
				/* @var $day BookingDay */
                $thisMonth = $this->setting->lastMonth + $month - 1;
                if ($thisMonth == 13) {
                    $thisMonth = 1;
                }
                if (!$config->hideNotCorrespondingDays || AHtml::date($day->date, 'm') == $thisMonth) {
				$firstBox = reset($day->boxes);
				$closed = is_object($firstBox) && $firstBox->closed;
				
				$class = 'day';
				$title = $style = '';
				
				if (!$manager) {
    				if ($day->engaged)
    					$class .= ' reserved';
    				
    				if ($closed) {
    					$class .= ' closed hasTip';
    					$title .= $this->escape($firstBox->closingDayTitle) . '::' . $this->escape($firstBox->closignDayText);
    				}
    
    				if ($closed && $firstBox->closignDayColor)
    					$style .= 'background-color: #' . $firstBox->closignDayColor;
    				else if ($day->engaged && $config->colorCalendarFieldReserved)
    					$style .= 'background-color: ' . $config->colorCalendarFieldReserved;
    				else if (!$closed && $config->colorCalendarFieldFree)
    					$style .= 'background-color: ' . $config->colorCalendarFieldFree;
				}
					
		?>
				<td class="<?php echo $class; ?>" style="<?php echo $style; ?>" title="<?php echo $title; ?>">
						<?php if ($config->enableResponsive) { ?>
							<span class="date"><?php echo AHtml::date($day->date, ADATE_FORMAT_NICE_SHORT_RESPONSIVE, 0); ?></span>
						<?php } elseif ($this->subject->night_booking && $config->nightsStyle) { ?>
							<span class="date" ><?php echo JText::sprintf('NIGHT_BOOKING_DATE', AHtml::date($day->date, ADATE_FORMAT_NICE_SHORT, 0), AHtml::date($day->nextDate, ADATE_FORMAT_NICE_SHORT, 0)); ?></span>
					<?php } else { ?>
						<span class="date" ><?php echo AHtml::date($day->date, ADATE_FORMAT_NICE_SHORT, 0); ?></span>
					<?php } ?>
		<?php
					if (! ($break && !$isLastWeek)) {
						foreach ($day->boxes as $box) {
							/* @var $box BookingTimeBox */
							if (!$box->closed) {
								foreach ($box->services as $service) {
									/* @var $service BookingService */
										if ($service->allowFixLimit || ($service->rtype == RESERVATION_TYPE_DAILY && (($config->bookCurrentDay && $day->Uts >= strtotime($this->setting->currentDate)) || ($this->isAdmin || $day->Uts > $this->setting->currentDayUTS)) && $service->canReserve && in_array($service->rtypeId, $this->lists['rids'])) && !$manager) {
										if ($userCanReserve && !$service->beforeFuture)
											$commands = ADocument::setBoxParams($service, $service->i, $pcount, $i);
		?>		
											<span class="price price<?php echo $service->notBeginsFixLimit ? 'Transparent' : $service->priceIndex; ?>" id="<?php echo $service->idShort; ?>">
		<?php 
										if ($this->subject->display_capacity && $this->subject->total_capacity>1)
											echo ($this->subject->total_capacity - $service->alreadyReserved);
		?>
										</span>
        <?php 
									}
								}
							}
						}	
                        $this->box = $day;
                        echo $this->loadTemplate('popup');
						if (!$this->popup && ($this->isAdmin || IS_ADMIN) && !empty($box->customerName) && !$manager) { // admin sees customer info always
							foreach ($box->customerName as $info) { 
		?>
								<span class="time"><a href="<?php echo JRoute::_(ARoute::detail(CONTROLLER_RESERVATION, $info['reservation_id'])); ?>" title="<?php echo $this->escape(JText::_('SHOW_RESERVATION')); ?>::<?php echo $info['reservation_id']; ?>" class="hasTip"><?php echo $info['name']; ?></a></span><br/>
		<?php 
							}
						}						
						if (!$this->popup && ((!$this->isAdmin && $this->subject->display_who_reserve) || $manager) && isset($day->customerName) && count($day->customerName)) {
							if(strip_tags(json_encode( $day->customerName)) != json_encode($day->customerName)){
        ?>
								<span class="customer" <?php if($config->colorCalendarBoxReserved){ echo 'style="background-color:'.$config->colorCalendarBoxReserved.'"'; }?>><?php echo AHtml::showUserInfo($day->customerName, $this->calendar); ?></span>                                
						<?php } else {
                                    if ($config->whoReserveShowType && !$manager) // show customer name as text 
                                        echo AHtml::showUserInfo($day->customerName, $this->calendar); 
                                    else { // show customer name in tooltip
                                    	if (!$manager) { ?>
											<span class="price hasTip customer" title="<?php echo AHtml::showUserInfo($day->customerName, $this->calendar); ?>" <?php if($config->colorCalendarBoxReserved){ echo 'style="background-color:'.$config->colorCalendarBoxReserved.'"'; }?>> </span>
										<?php } else {
										    
										    $items = array(); // group reservations by item
										    foreach ($day->customerName as $id => $data) {
										        $data['name'] = $data['full'];
										        $items[$data['item']][] = $data;
										    }
										        
										    foreach ($items as $ii => $item) { // search price color for reservation
										        foreach ($this->days->prices as $prices) {
										            foreach ($prices->prices as $pi =>$price) {
										                if ($price->subject == $ii) {
										                    $items[$ii][0]['color'] = $pi; // index of price color
										                    break;
										                }
										            }
										        }
										    }
										    
										    foreach ($items as $item) {
										       if (!$this->isAdmin) { ?>
													<span class="hasTip price price<?php echo $item[0]['color']; ?>" title="<?php echo AHtml::showUserInfo($item, (empty($this->calendar) ? 'monthly' : $this->calendar)); ?>">&nbsp;</span>										
		                                 <?php } else {
		                                            $cid = JArrayHelper::getColumn(array_merge($item), 'reservation_id'); // get reservation ids from list 
		                                            if (count($cid) > 1) { // more items - show filtered reservation list    
		                                                $url = ARoute::view(VIEW_RESERVATIONS, null, null, array('layout' => 'admin', 'filter_reservation-id' => implode(',', $cid)));
		                                            } else { // only item - show reservation detail
                                                        $url = ARoute::view(VIEW_RESERVATION, null, null, array('cid[]' => reset($cid))); 
										            } ?>    
		                                			<a href="<?php echo JRoute::_($url); ?>" class="hasTooltip price price1 price<?php echo $item[0]['color']; ?>" title="<?php echo AHtml::showUserInfo($item); ?>"> </a>
										<?php       }
		                                       }
										  }
                                     }
                               }
						 }
					}
					if ($closed && $firstBox->closignDayShow) { ?>
						<span class="closed"><?php echo $firstBox->closingDayTitle; ?></span>
					<?php }                
		?> 
				</td>
		<?php		
                } else { ?>
                    <td class="day"></td>
                <?php }
                if (date('m', strtotime($day->date)) != date('m', strtotime($day->nextDate)) && $i > 6) {
					$lastMonthDay = $i % 7;
					$break++; // last day in month
				}
		 
				if ($i % 7 == 6 && $pcount > $i + 1) { // end of week and next week is coming
					$isLastWeek = $pcount - $i == 8;
		?>
					</tr>
		<?php
					if ($break == 1) { // last of month hapened
						if ($lastMonthDay != 6) { // not last week day, week continues in next month
							$i -= 7; // repeat last week with start of next month
							$week --; // repeat last week number
							$break = -1; // set as -1 to ignore next step (repeating of end of this month in next month)
						} else
							$break = 0;
						$nextMonth = JFactory::getDate($this->setting->selected . '-01' . ' + ' . ($month++) . ' month');
						/* @var $nextMonth JDate */
		?>
						</table>
						</div>
						<div<?php if (false && $monthNumber++ < $this->setting->monthNumber) { ?> class="monthlyCalendarHidden"<?php } ?>>
						<h2 class="subjectSubtitle calendarTitle"><?php echo ($nextMonth->format('F') . ' ' . $nextMonth->format('Y')); ?></h2>
						<table class="monthlyCalendar">
		<?php
							echo $head;				
					}
		?>
					<tr>
							<td class="week">
								<span class="week"><?php echo $week ++; ?></span>
								<?php if ($config->enableResponsive) { ?>
									<span class="month"><?php //echo AHtml::date($day->date, ADATE_FORMAT_NICE_SHORT2, 0);; ?></span>
								<?php } ?>
							</td>
						
		<?php 
				}
			} 
		?>
	</tr>
	</tbody>
</table>
</div>
<!-- Calendar pagination -->
<div class="calendarPagination">
	<?php if (! $this->setting->onCurrentMonth || $this->isAdmin) { // admin can browse to the past, customer can browse to the future only ?>
		<span class="previousPage"> 
			<a href="javascript:Calendars.monthNavigation(<?php echo $this->setting->previousMonth; ?>,<?php echo $this->setting->previousYear; ?>)"><?php echo JText::_('PREVIOUS_MONTH'); ?></a>
		</span>
	<?php } ?>
	<span class="currentPage"> 
		<a href="javascript:Calendars.monthNavigation(<?php echo $this->setting->currentMonth; ?>,<?php echo $this->setting->currentYear; ?>)"><?php echo JText::_('CURRENT_MONTH'); ?></a>
	</span> 
	<?php if (! $this->setting->lastAllowPage) { ?>
		<span class="nextPage"> 
			<a href="javascript:Calendars.monthNavigation(<?php echo $this->setting->nearMonth; ?>,<?php echo $this->setting->nearYear; ?>)"><?php echo JText::_('NEXT_MONTH'); ?></a> 
		</span>
	<?php } ?>
</div>
<?php
	if ($config->buttonPosition == 1) echo $this->loadTemplate('bookitform');
?>
<!--/AJAX_bookingCalendar-->
</div>
<!--
AJAX_EVAL_BEGIN
	<?php if (!empty($commands))
		echo implode(PHP_EOL, (array) $commands); ?>
AJAX_EVAL_END
-->