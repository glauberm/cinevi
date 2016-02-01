<?php

/**
 * Weekly multi calendar template.
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



$items = $this->get('children');

if (empty($items))
	return;

$this->setting = new BookingCalendarSetting();

$defaultRids = array();

foreach ($items as $item) { 
	$item->days = BookingHelper::getWeekCalendar($item, $this->setting, IS_ADMIN ? $this->calendarnumweeks * 7 : 'week', $this->isAdmin);
	$defaultRids = array_merge($defaultRids, $item->rids);
}

$this->lists['rids'] = ARequest::getUserStateFromRequest('rids', $defaultRids, 'array');

?>
<div id="bookingCalendar">
<!--AJAX_bookingCalendar-->
<?php 
	echo $this->loadTemplate('bookitform');
?>
		<div class="clr"></div>
	<?php foreach ($items as $item) { ?>
		<fieldset class="radio col">
			<legend><?php echo $item->title; ?></legend>
			<?php $this->days->prices = $item->days->prices; ?>
			<?php $this->supplements = $item->supplements; ?>
			<div class="col">
				<?php echo $this->loadTemplate('supplements'); ?>
			</div>
			<div class="col">
				<?php echo $this->loadTemplate('prices'); ?>
			</div>
		</fieldset>		
<?php 		
	}
?>
		<div class="clr"></div>
<?php 	
	if (IS_ADMIN) {
?>
			
		<fieldset>
			<legend><?php echo JText::_('NUM_WEEKS_VISIBLE'); ?></legend>
			<input type="text" name="calendarnumweeks" id="calendarnumweeks" value="<?php echo $this->calendarnumweeks; ?>" size="1" onchange="this.form.submit()" />
			<button><?php echo JText::_('JSubmit'); ?></button>
		</fieldset>
		<?php 
	}

foreach (reset($items)->days->calendar as $i => $day) { ?>
	<fieldset>
		<legend><?php echo AHtml::date($day->date, ADATE_FORMAT_NICE, 0); ?></legend>
		<table class="adminlist boxes">
		<?php foreach ($items as $j => $item) { ?>
			
				<tr class="row<?php echo $j % 2; ?>">
					<td valign="top">
						<a href="<?php echo JRoute::_(ARoute::edit(CONTROLLER_SUBJECT, $item->id)); ?>">
							<?php echo $item->title; ?>
						</a>
					</td>
					<?php $bc = count($item->days->calendar[$i]->boxes);
						$bi = 0;
						foreach ($item->days->calendar[$i]->boxes as $k => $box) { 
							$bi ++; 
							$closedClassName = $box->closed ? ' closed hasTip' : '';
							$title = $box->closed ? $this->escape($box->closingDayTitle).'::'.$this->escape($box->closignDayText) : ''; ?>
							
						<td valign="top" class="<?php echo $closedClassName; ?>" title="<?php echo $title; ?>">
							<span>
								<?php echo $box->fromTime; ?>-<?php echo $box->toTime; ?>
								<div class="clr"></div>
								<?php if (!$box->closed) { ?>
									<?php foreach ($box->services as $l => $service) { ?>
										<?php if ($service->canReserve && in_array($service->rtypeId, $this->lists['rids'])) { ?>
											<span class="time price price<?php echo $service->priceIndex; ?>" id="<?php echo $service->idShort; ?>">
												<?php
													if ($this->userCanReserve)
														ADocument::setBoxParams($service, $service->i, $bc, $bi);
													if ($this->subject->display_capacity && $this->subject->total_capacity > 1)
														echo ($this->subject->total_capacity - $service->alreadyReserved);
												?>
											</span>
										<?php } ?>
									<?php } ?>
								<?php } ?>
							</span>
							<div class="clr"></div>
							<?php if ($this->subject->display_who_reserve && isset($box->customerName) && count($box->customerName)) {
											if(strip_tags(json_encode($box->customerName)) != json_encode($box->customerName)){
								?>
													<span class="customer" <?php if($config->colorCalendarBoxReserved){ echo 'style="background-color:'.$config->colorCalendarBoxReserved.'"'; }?>><?php echo AHtml::showUserInfo($box->customerName); ?></span>
											<?php }else{ ?>
													<span class="price hasTip customer" title="<?php echo AHtml::showUserInfo($box->customerName); ?>" <?php if($config->colorCalendarBoxReserved){ echo 'style="background-color:'.$config->colorCalendarBoxReserved.'"'; }?>> </span>
									<?php
		        							} 
										}
										if ($this->isAdmin && !empty($box->customerName)) {
											foreach ($box->customerName as $info) {
									?>
												<span class="customer"><a href="<?php echo JRoute::_(ARoute::detail(CONTROLLER_RESERVATION, $info['reservation_id'])); ?>" title="<?php echo $this->escape(JText::_('SHOW_RESERVATION')); ?>::<?php echo $info['reservation_id']; ?>" class="hasTip"><?php echo $info['name']; ?></a></span><br/>
									<?php
											} 
										}
								?>
						</td>
					<?php } ?>
				</tr>
		<?php } ?>
		</table>
	</fieldset>
<?php } ?>
<div class="calendarPagination">
	<?php if (! $this->setting->onCurrentWeek || IS_ADMIN) { ?>
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