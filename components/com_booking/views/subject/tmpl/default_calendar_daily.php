<?php

/**
 * Daily calendar template.
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

$this->days = &BookingHelper::getDailyCalendar($this->subject, ($this->setting = new BookingCalendarSetting()), $this->isAdmin);

//$userCanReserve = $this->customer->id || $config->unRegisteregCanReserve;
$userCanReserve = $this->userCanReserve;

$noAvailableReservations = JText::_('NO_AVAILABLE_RESERVATIONS');
/* @var $noAvailableReservations string save translation in property for optimizing */
$bookFullDay = JText::_('BOOK_FULL_DAY');
/* @var $bookFullDay string save translation in property for optimizing */

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
?>
<div id="bookingCalendar">
<!--AJAX_bookingCalendar-->
<style type="text/css"><?php echo $string; ?></style>
<div>
<?php 
	if (($countDays = count($this->days))) {
		/* @var $countDays int count of days in request interval */ 
?>
 	<div class="dailyCalendar">
  		<?php 
  			$firstDay = &reset($this->days->calendar);
  			
  			if ($config->buttonPosition == 0) echo $this->loadTemplate('bookitform');
  		?>
  			<div class="clr"></div>
  		<?php
  			echo $this->loadTemplate('prices');
  			
      		if ($config->quickNavigator) {
    	?>
  				<div class="clr"></div>
  				<div class="quickNavigator">
  					<?php 
  						$mainframe = JFactory::getApplication();
  						/* @var $mainframe JApplication */
  						$current = JFactory::getDate();
  						/* @var $current JDate */
  						$futured = JFactory::getDate('+ ' . $config->calendarDeepDay . ' days');
  						/* @var $futured JDate */
  						if ($this->isAdmin) // admin can browse everything
 							ADocument::addDomreadyEvent('Calendars.dateBegin = "0";' . PHP_EOL . 'Calendars.dateEnd = "0";');
  						else // customer can browse present and future only
  							ADocument::addDomreadyEvent('Calendars.dateBegin = ' . $current->format('Ymd') . ';' . PHP_EOL . 'Calendars.dateEnd = ' . $futured->format('Ymd') . ';');
  						echo AHtml::getCustomCalendar($this->setting->requestDate, 'iday', 'iday', ADATE_FORMAT_MYSQL_DATE_CAL);
  					?>
  				</div>
  		<?php 
  			} 
  			foreach ($this->days->calendar as $day) {
  				/* @var $day BookingDay */	
  					
	  			$pricesPositions = array();
	  			$bi = 0;
	  			$bc = count($day->boxes);
				foreach ($day->boxes as $bkey => $box) {
					$bi ++;
					/* @var $box BookingTimeBox */
					$pricePosition = 0;
					foreach ($box->services as $skey => $service)
						/* @var $service BookingService */
						if (($service->canReserve = !(!in_array($service->rtypeId, $this->lists['rids']) || (!$service->canReserve)))) {
							if (! isset($pricesPositions[$service->priceIndex]) || $pricesPositions[$service->priceIndex] < $pricePosition)
								$pricesPositions[$service->priceIndex] = $pricePosition;
							$pricePosition++;	
							if ($service->rtype == RESERVATION_TYPE_DAILY)
								$box->haveDailyService = true;
						}
				}
		?>
			
				<div class="boxesDay">
  					<h2><?php echo AHtml::date($firstDay->date, ADATE_FORMAT_NICE, 0); ?></h2>
  					<table class="boxes">
  						<tr>
							<th>
                                <span class="time"><?php echo $config->timeIntervalStyle ? JText::_('FROM') : JText::_('FROM_TO'); ?></span>
								<?php 
									$count = count($pricesPositions);
									for ($i = 0; $i < $count; $i++) {
								?>
										<span class="price"></span>
								<?php 
									}
									if ($this->subject->display_who_reserve) { 
								?>
										<span class="customer"><?php echo JText::_('CUSTOMER'); ?></span>
								<?php 
									} 
								?>
							</th>
						</tr>
		<?php
  						foreach ($day->boxes as $box) {
  							/* @var $box BookingTimeBox */
							$usedBoxes = 0;
							if ($box->rtype == RESERVATION_TYPE_DAILY && !$box->haveDailyService)
								continue;
							
							$class = 'box';
							$title = '';
							$style = '';
							
							if ($box->engaged)
								$class .= ' reserved';
								
							if ($box->closed) {
								$class .= ' closed hasTip';
								$title .= $this->escape($box->closingDayTitle) . '::' . $this->escape($box->closignDayText);
							}
								
							if ($box->closed && $box->closignDayColor)
								$style = 'background-color: #' . $box->closignDayColor;
							else if ($box->engaged && $config->colorCalendarFieldReserved)
								$style = 'background-color: ' . $config->colorCalendarFieldReserved;
							else if (!$box->closed && $config->colorCalendarFieldFree)
								$style = 'background-color: ' . $config->colorCalendarFieldFree;
							
							$closedClassName = $box->closed ? ' closed hasTip' : '';
							$title = $box->closed ? $this->escape($box->closingDayTitle).'::'.$this->escape($box->closignDayText) : '';
							
		?>
							<tr>	
							<td class="<?php echo $class; ?>" style="<?php echo $style; ?>" title="<?php echo $title; ?>">
								<span class="time"><?php echo $box->rtype == RESERVATION_TYPE_DAILY ? $bookFullDay : (BookingHelper::displayTime($box->fromTime) . (!$config->timeIntervalStyle ? ' - ' . BookingHelper::displayTime($box->toTime) : '')); ?></span>
								<?php						
									$somePiece = false;
									if (!$box->closed) {								
										foreach ($box->services as $i => $service) {
											if ($service->canReserve) {
												/* @var $service BookingService */
												$emptyPricesCount = $pricesPositions[$service->priceIndex] - $usedBoxes;
												for ($i = 0; $i < $emptyPricesCount; $i++) {
								?>
													<span class="price"></span>
								<?php
												} 
												if (!$somePiece && ($service->headPiece || $service->tailPiece)) {
													$somePiece = true;
								?>
													<span class="pieces">
								<?php
												} 
												$usedBoxes += $emptyPricesCount + 1;
								?>
												<span class="price price<?php echo $service->priceIndex; ?><?php if ($service->tailPiece) { ?> tailPiece<?php } ?><?php if ($service->headPiece) { ?> headPiece<?php } ?>" id="<?php echo $service->idShort; ?>">
								<?php
													if ($userCanReserve && !$service->beforeFuture)
														$commands = ADocument::setBoxParams($service, $service->i, $bc, $bi);
													if ($this->subject->display_capacity && $this->subject->total_capacity>1)
														echo ($this->subject->total_capacity - $service->alreadyReserved);
								?>
												</span>
								<?php
								            } elseif (!($this->subject->display_who_reserve && isset($box->customerName) && count($box->customerName))) {
								?>												
												<span class="price"></span>
								<?php		
											}
											if ($somePiece && !(@$box->services[$i + 1]->tailPiece || @$box->services[$i + 1]->headPiece)) {
								?>
												</span><!-- end  <span class="pieces"> -->
								<?php 
											}
										} 
									}
                                    $this->box = $box;
                                    echo $this->loadTemplate('popup');
									if (!$this->popup && $this->subject->display_who_reserve && isset($box->customerName) && count($box->customerName)) {
										if(strip_tags(json_encode($box->customerName)) != json_encode($box->customerName)){
								?>
											<span class="customer" <?php if($config->colorCalendarBoxReserved){ echo 'style="background-color:'.$config->colorCalendarBoxReserved.'"'; }?>><?php echo AHtml::showUserInfo($box->customerName, $this->calendar); ?></span>
									<?php }else{
                                            if ($config->whoReserveShowType) // show customer name as text 
                                                echo AHtml::showUserInfo($box->customerName, $this->calendar); 
                                            else { // show customer name in tooltip ?>
												<span class="price hasTip customer" title="<?php echo AHtml::showUserInfo($box->customerName, $this->calendar); ?>" <?php if($config->colorCalendarBoxReserved){ echo 'style="background-color:'.$config->colorCalendarBoxReserved.'"'; }?>> </span>
								<?php
                                            }
		        						}
									}
									if ($box->closed && $box->closignDayShow) { ?>
										<span class="closed"><?php echo $box->closingDayTitle; ?></span>
									<?php } 
								?>
							</td>
						</tr>
					<?php 
  					}
  				}	
  			?>
  			</table>
  		</div>
  		<div class="clr"></div>
	</div>
</div>
	<div class="calendarPagination">
		<?php if (! $this->setting->onCurrentDay) { ?>
			<span class="previousPage">
				<a href="javascript:Calendars.dayNavigation(<?php echo $this->setting->previousDay; ?>,<?php echo $this->setting->previousMonth; ?>,<?php echo $this->setting->previousYear; ?>)"><?php echo JText::_('PREVIOUS_DAY'); ?></a>
			</span>
		<?php } ?>
		<span class="currentPage">
			<a href="javascript:Calendars.dayNavigation(<?php echo $this->setting->currentDay; ?>,<?php echo $this->setting->currentMonth; ?>,<?php echo $this->setting->currentYear; ?>)"><?php echo JText::_('CURRENT_DAY'); ?></a>
		</span>
		<?php if(! $this->setting->lastAllowPage) { ?>
			<span class="nextPage">
				<a href="javascript:Calendars.dayNavigation(<?php echo $this->setting->nextDay; ?>,<?php echo $this->setting->nextMonth; ?>,<?php echo $this->setting->nextYear; ?>)"><?php echo JText::_('NEXT_DAY'); ?></a>
			</span>
		<?php } ?>
	</div>
	<?php 
  		if ($config->buttonPosition == 1) echo $this->loadTemplate('bookitform');
  	?>
<?php } else { ?>
	<p><?php echo $noAvailableReservations; ?></p>
<?php } ?>
<!--/AJAX_bookingCalendar-->
</div>
<!--
AJAX_EVAL_BEGIN
	<?php if (!empty($commands))
		echo implode(PHP_EOL, (array) $commands);
	if ($config->quickNavigator)
		echo AHtml::getCustomCalendar($this->setting->requestDate, 'iday', 'iday', ADATE_FORMAT_MYSQL_DATE_CAL, '', true); ?>
AJAX_EVAL_END
-->