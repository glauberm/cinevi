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

//get number of shown weeks (same for months) from template
if(isset($this->templateTable->numberOfMonths) && $this->templateTable->numberOfMonths)
	$calWeeks = $this->templateTable->numberOfMonths * 7;
else
	$calWeeks = $config->calendarNumWeeks * 7; // global config

$calendarnumweeks = $this->isAdmin ? $this->calendarnumweeks * 7 : $calWeeks;

$this->days = &BookingHelper::getWeekCalendar($this->subject, ($this->setting = new BookingCalendarSetting()), $calendarnumweeks, $this->isAdmin);

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
<?php
if ($countDays) {  
	
?>
  	<div class="weeklyCalendar">
    	<?php 
    		if ($config->buttonPosition == 0) echo $this->loadTemplate('bookitform');
    	?>
    	<div class="clr"></div>
    	<?php 
    		echo $this->loadTemplate('prices');
    		if (!$this->isAdmin && $config->quickNavigator) { // customer has predefined navigation only
    	?>
			<div class="clr"></div>
			<div id="caltop" class="quickNavigator">
				<label for="iweek"><?php echo JText::_('SELECT_WEEK'); ?></label>
				<?php echo AHtml::getWeekSelect('iweek', $this->setting->week, $this->setting->year, $config->calendarDeepWeek, 'onchange="Calendars.weekNavigation(this.value)"'); ?>
			</div>
		<?php 
    		}
    		if ($this->isAdmin) { // admin has unlimited # of weeks to show
		?>
				<div class="clr"></div>
				<div id="caltop" class="quickNavigator">
					<label for="calendarnumweeks" style="padding-left: 20px"><?php echo JText::_('NUM_WEEKS_VISIBLE'); ?></label>
					<input type="text" name="calendarnumweeks" id="calendarnumweeks" value="<?php echo $this->calendarnumweeks; ?>" size="1" onchange="this.form.submit()" />
					<button><?php echo JText::_('JSubmit'); ?></button>
				</div>		
		<?php 
			}
			$bc = 0;
			//count all boxes
			foreach($this->days->calendar as $j => $firstDay){
				@$bc += count($firstDay->boxes);
			}
			//actual box (max is bc)
			$bi = 0;
		?>
		<div id="weekDaysScroller">
			<div id="weekDays">
				<?php if ($config->weekStyle == 0) { ?>
					<?php ob_start(); ?>
						<div class="boxesDay">
							<h2>&nbsp;</h2>
							<table class="boxes">
								<tr>
									<th>
										<span class="time"><?php echo $config->timeIntervalStyle ? JText::_('FROM') : JText::_('FROM_TO'); ?></span>
										<?php 
											if (!$this->isAdmin && $this->subject->display_who_reserve) { 
										?>
												<span class="customer"><?php echo JText::_('Customer'); ?></span>
										<?php 
											} 
										?>
									</th>
								</tr>
								<?php foreach ($this->days->calendar[0]->boxes as $box) { ?>
									<tr>
										<td>
											<span class="time"><?php echo AHtml::date($box->fromTime, ATIME_FORMAT, 0) . (!$config->timeIntervalStyle ? ' - ' . AHtml::date($box->toTime, ATIME_FORMAT, 0) : ''); ?></span>
										</td>
									</tr>
								<?php } ?>
							</table>
						</div>
					<?php $begin = ob_get_contents(); ?>
				<?php } ?>
			<?php
		foreach ($this->days->calendar as $j => $firstDay) {
            if (in_array($firstDay->weekDayCode, $config->daysInWeekLayout)) {
                /* @var $firstDay BookingDay */

                $pricesPositions = array();
                foreach ($firstDay->boxes as $bkey => $box) {
                    /* @var $box BookingTimeBox */
                    $pricePosition = 0;
                    foreach ($box->services as $skey => $service)
                        /* @var $service BookingService */
                        if (($service->canReserve = !(!in_array($service->rtypeId, $this->lists['rids']) || (!$service->canReserve))) && !$service->tailPiece) {
                            if (!isset($pricesPositions[$service->priceIndex]) || $pricesPositions[$service->priceIndex] < $pricePosition)
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
                            <?php if ($config->weekStyle == 1) { ?>
                                <span class="time"><?php echo $config->timeIntervalStyle ? JText::_('FROM') : JText::_('FROM_TO'); ?></span>
                            <?php } ?>
                            </th>
                        </tr>
                        <?php
                            //$bc = count($firstDay->boxes);
                            foreach ($firstDay->boxes as $box) {
                                /* @var $box BookingTimeBox */
                                //increase actual box
                                $bi++;
                                $usedBoxes = 0;
                                //if ($box->rtype == RESERVATION_TYPE_DAILY && !$box->haveDailyService)
                                    //continue;

                                $class = 'box';
                                $title = $style = '';

                                if ($box->engaged)
                                    $class .= ' reserved';

                                if ($box->closed) {
                                    $class .= ' closed hasTip';
                                    $title .= $this->escape($box->closingDayTitle) . '::' . $this->escape($box->closignDayText);
                                }

                                if ($box->closed && $box->closignDayColor)
                                    $style .= 'background-color: #' . $box->closignDayColor;
                                else if ($box->engaged && $config->colorCalendarFieldReserved)
                                    $style .= 'background-color: ' . $config->colorCalendarFieldReserved;
                                else if (!$box->closed && $config->colorCalendarFieldFree)
                                    $style .= 'background-color: ' . $config->colorCalendarFieldFree;

                        ?>
                                <tr>	
                                    <td class="<?php echo $class; ?>" style="<?php echo $style; ?>" title="<?php echo $title; ?>">
                                        <?php if ($config->weekStyle == 1) { ?>
                                            <span class="time"><?php echo AHtml::date($box->fromTime, ATIME_FORMAT, 0) . (!$config->timeIntervalStyle ? ' - ' . AHtml::date($box->toTime, ATIME_FORMAT, 0) : ''); ?></span>
                                        <?php } ?>
                                        <?php					
                                            if (!$box->closed) {
                                                $somePiece = false;
                                                foreach ($box->services as $i => $service) {
                                                    /* @var $service BookingService */
                                                    $emptyPricesCount = isset($pricesPositions[$service->priceIndex]) ? ($pricesPositions[$service->priceIndex] - $usedBoxes) : 0;
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
                                                    if ($service->canReserve) {
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
                                                $this->box = $box;
                                                echo $this->loadTemplate('popup');
                                                if (!$this->popup && $this->subject->display_who_reserve && !$this->isAdmin && !empty($box->customerName)) {
                                                    if (strip_tags(json_encode($box->customerName)) != json_encode($box->customerName)) {
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
                                                if (!$this->popup && $this->isAdmin && !empty($box->customerName)) { // admin sees customer info always
                                                    foreach ($box->customerName as $info) {
                                        ?>
                                                        <a href="<?php echo JRoute::_(ARoute::detail(CONTROLLER_RESERVATION, $info['reservation_id'])); ?>" title="<?php echo $this->escape(JText::_('SHOW_RESERVATION')); ?>::<?php echo $info['reservation_id']; ?>" class="hasTip"><?php echo $info['name']; ?></a><br/>
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
                        ?>
                    </table>
                </div>			
		<?php 
            }
				if (($j + 1) % 7 == 0) { // after every week wrap page
		?>	
					
					<?php if ($j < count($this->days->calendar) - 1 && $config->weekStyle == 0) { ?>
						<div class="clr"></div>
						<?php echo $begin; ?>
					<?php } ?>
		<?php 	
				}
			} 
		?>
			</div>
		</div>
		<div class="clr"></div>
  </div>
<?php } else { ?>
	<p><?php echo $noAvailableReservations; ?></p>
<?php } ?>
<div class="calendarPagination">
	<?php if (! $this->setting->onCurrentWeek || $this->isAdmin) { // admin can browse to the past, customer can browse to the future only ?>
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