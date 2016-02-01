<?php 

/**
 * Weekly calendar template.
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

//$userCanReserve = $this->customer->id || $config->unRegisteregCanReserve;
$userCanReserve = $this->userCanReserve;

/*
$this->days = &BookingHelper::getWeekCalendar($this->subject, ($this->setting = new BookingCalendarSetting()));
$this->day[$this->subject->id] = $this->days;

$modelSubject = new BookingModelSubject();
$modelSubject->setId(2);
$s2 = $modelSubject->getObject();

$this->days2 = &BookingHelper::getWeekCalendar($s2, $this->setting);

$this->day[$s2->id] = $this->days2;
*/

$this->objects = new stdClass();
foreach($this->day as $o=>$object) {
	$this->objects->prices[$o] = $object->prices;
	
	foreach($object->calendar as $d=>$day) {
		$this->objects->calendar[$d][$o] = $day;
	}
	
	$this->objects->label[$o] = $object->label;
}
$this->days = null;
$this->days = $this->objects;
//var_dump($this->objects['calendar'][0][0]);

$this->lists['rids'][] = 3;
$this->lists['rids'][] = 4;


$countDays = count($this->days->calendar); 
/* @var $countDays int days count */

// saved translated text into properties for optimization
$noAvailableReservations = JText::_('NO_AVAILABLE_RESERVATIONS');
$isReserved = JText::_('IS_RESERVED');
$bookFullDay = JText::_('BOOK_FULL_DAY');
$leave = JText::_('LEAVE');


//load csss colors for prices
//-----------------------
$colors = array();
foreach($this->days->prices as $prices)
{
	foreach($prices as $object)
		foreach($object->prices as $price)
		{
			$colors[] = $price->custom_color;
		}
}
$string = '';
foreach($colors as $i=>$color)
{
	$string .= "#bookSetting .price".$i." {	background-color: ".$color.";} ";
}
JFactory::getDocument()->addStyleDeclaration( $string );
//-------------------------
?>
<div id="bookingCalendar">
<!--AJAX_bookingCalendar-->
<?php 
if ($countDays) {  
	
?>
  	<div class="weeklyCalendar">
    	<?php 
    		echo $this->loadTemplate('bookitform');
    	?>
    	<div class="clr"></div>
    	<?php 
    		echo $this->loadTemplate('prices');
    		if ($config->quickNavigator) { 
    	?>
			<div class="clr"></div>
			<div id="caltop" class="quickNavigator">
				<label for="iweek"><?php echo JText::_('SELECT_WEEK'); ?></label>
				<?php echo AHtml::getWeekSelect('iweek', $this->setting->week, $this->setting->year, $config->calendarDeepWeek, 'onchange="Calendars.weekNavigation(this.value)"'); ?>
			</div>
		<?php 
    	}
    	//var_dump($this->days);
		foreach ($this->days->calendar as $firstDay) {
			/* @var $firstDay BookingDay */
			//var_dump($firstDay);
			$pricesPositions = array();
			foreach($firstDay as $object){
			foreach ($object->boxes as $bkey => $box) {
				/* @var $box BookingTimeBox */
				$pricePosition = 0;
				foreach ($box->services as $skey => $service)
					/* @var $service BookingService */
					if (($service->canReserve = !(!in_array($service->rtypeId, $this->lists['rids']) || (!$service->canReserve)))) {
						if (!isset($pricesPositions[$service->priceIndex]) || $pricesPositions[$service->priceIndex] < $pricePosition)
							$pricesPositions[$service->priceIndex] = $pricePosition;
						$pricePosition++;	
						if ($service->rtype == RESERVATION_TYPE_DAILY)
							$box->haveDailyService = true;
					}
			}
			}
	?>
			<div class="boxesDay">
				<h2><?php $day = reset($firstDay); echo AHtml::date($day->date, ADATE_FORMAT_NICE, 0); ?></h2>
				<table class="boxes">
					<tr>
					<th>
						
					</th>
						<?php
										
						$object = reset($firstDay);
							foreach ($object->boxes as $box){?>
						<th>
							<span class="Ttime" style="font-size: 8px; padding: 1px 2px;"><?php echo $box->rtype == RESERVATION_TYPE_DAILY ? $bookFullDay : ($box->fromTime . ' - ' . $box->toTime); ?></span>
						</th>
						<?php }
						

						/*
						for($i=0 ; $i<24 ; $i++){?>
						<th>
							<span class="Ttime"><?php echo $i; ?></span>
						</th>
						<?php } */
						?>
					</tr>
					<?php foreach ($firstDay as $objectId=>$object) {?>
					<tr>
					<td>
						<?php echo $this->days->label[$objectId];?>
					</td>
					<?php 
					//var_dump($firstDay);
					reset($object->boxes);
					//var_dump(key($object->boxes));
						for($i=0 ; $i<key($object->boxes) ; $i++){?>
						<td>
							<span class="Ttime"></span>
						</td>
						<?php }
								
						foreach ($object->boxes as $box) {
							/* @var $box BookingTimeBox */
							$usedBoxes = 0;
							
							//if ($box->rtype == RESERVATION_TYPE_DAILY && !$box->haveDailyService)
								//continue;
					?>
								
								<td class="box<?php if ($box->engaged) { ?> reserved<?php if($config->colorCalendarFieldReserved){ echo '"style="background-color:'.$config->colorCalendarFieldReserved; } }else if($config->colorCalendarFieldFree){ echo '" style="background-color:'.$config->colorCalendarFieldFree; } ?>">
									<?php					
										foreach ($box->services as $i => $service) {
											/* @var $service BookingService */
											$emptyPricesCount = isset($pricesPositions[$service->priceIndex]) ? ($pricesPositions[$service->priceIndex] - $usedBoxes) : 0;
											for ($i = 0; $i < $emptyPricesCount; $i++) {
									?>
												<span class="price"></span>
									<?php
											} 
											$usedBoxes += $emptyPricesCount + 1;
											if ($service->canReserve) {
									?>
												<span style="padding: 0px;width: 100%;" class="price price<?php echo $service->priceIndex; ?>" id="<?php echo $service->idShort; ?>">
									<?php
									//echo $service->i;
													if ($userCanReserve)
														ADocument::setBoxParams($service, $objectId);
													if ($this->subject->display_capacity && $this->subject->total_capacity>1)
														echo ($this->subject->total_capacity - $service->alreadyReserved);
									?>
												</span>
									<?php
											} else {
									?>			
												<span class="price"></span>
									<?php		
											}
										}
										if ($config->displayWhoReserve && isset($box->customerName) && count($box->customerName)) {
											if(strip_tags(json_encode($box->customerName)) != json_encode($box->customerName)){
									?>
													<span class="customer" <?php if($config->colorCalendarBoxReserved){ echo 'style="background-color:'.$config->colorCalendarBoxReserved.'"'; }?>><?php echo showUserInfo($box->customerName); ?></span>
											<?php }else{ ?>
													<span class="price hasTip customer" title="<?php echo showUserInfo($box->customerName); ?>" <?php if($config->colorCalendarBoxReserved){ echo 'style="background-color:'.$config->colorCalendarBoxReserved.'"'; }?>> </span>
									<?php
		        							} 
										}
									?>
								</td>
							
					<?php 
						} 
					?>
					</tr>
					<?php }?>
				</table>
			</div>			
		<?php 
			} 
		?>
		<div class="clr"></div>
  </div>
<?php } else { ?>
	<p><?php echo $noAvailableReservations; ?></p>
<?php } ?>
<div class="calendarPagination">
	<?php if (! $this->setting->onCurrentWeek) { ?>
		<span class="previousPage">
			<a href="javascript:Calendars.weekNavigation(<?php echo $this->setting->previousWeek; ?>,<?php echo $this->setting->previousYear; ?>)"><?php echo JText::_('PREVIOUS_WEEK'); ?></a>
		</span>
	<?php } ?>
	<span class="currentPage">
		<a href="javascript:Calendars.weekNavigation(<?php echo $this->setting->currentWeek; ?>,<?php echo $this->setting->currentYear; ?>)"><?php echo JText::_('CURRENT_WEEK'); ?></a>
	</span>
	<?php if (! $this->setting->lastAllowPage) { ?>
		<span class="nextPage">
			<a href="javascript:Calendars.weekNavigation(<?php echo $this->setting->nextWeek; ?>,<?php echo $this->setting->nextYear; ?>)"><?php echo JText::_('NEXT_WEEK'); ?></a> 
		</span>
	<?php } ?>
</div>
<!--/AJAX_bookingCalendar-->
</div>