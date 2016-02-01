<?php 

/**
 * Book it dialog template.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

JHTML::_('behavior.modal');

defined('_JEXEC') or die('Restricted access');

$config = AFactory::getConfig();

if ($this->userCanReserve) {
	
	$start = JText::sprintf('CLICK_IN_S_TO_SELECT_START_DATE_OF_YOUR_BOOKING', ($type = $this->calendar == 'monthly' ? JText::_('CALENDAR') : JText::_('SCHEDULE')));	
	
	?>
	
	<!-- Book it section -->
	
		<div class="bookInterval">
			<a id="calendar"></a>
			<?php if (!$config->multipleReservations) { ?>
				<?php if (IS_ADMIN) { ?>
					<h2><?php echo JText::_('CREATE_NEW_RESERVATION'); ?></h2>
				<?php } elseif($this->mode != 'change') { ?>
					<h2><?php echo JText::sprintf('BOOK_THIS_S_NOW', $this->template->name); ?></h2>
				<?php } ?>
				<div class="buttons">
	        	<div class="checkInfo checkInfoMessage" id="checkInfo"><?php echo $start; ?></div>
	  			<div class="checkTools">
	  				<a class="checkButton checkButtonActive" id="selectCheckInDay" href="javascript:Calendars.setOperation(<?php echo CHECK_OP_IN; ?>)">
	  					<?php echo JText::_('SELECT_CHECK_IN'); ?></a>
	  				<a class="checkButton checkButtonUnactive" id="selectCheckOutDay" href="javascript:Calendars.setOperation(<?php echo CHECK_OP_OUT; ?>)">
	  					<?php echo JText::_('SELECT_CHECK_OUT'); ?></a>
	  				<a class="checkButton resetButton" id="reset" href="javascript:Calendars.reset()">
	  					<?php echo JText::_('RESET'); ?></a>
	  				<div class="cleaner"></div>
	  			</div>
				</div>
				<div class="fromTo">
	  			<div class="cal">
	  				<label for="iFrom"><?php echo JText::_('CHECK_IN'); ?>: </label>
	  				<input type="text" name="iFrom" id="iFrom" value="" disabled="disabled" size="16" />
	  			</div>
	  			<div class="cal">
	  				<label for="fTo"><?php echo JText::_('CHECK_OUT'); ?>: </label>
	  				<input type="text" name="iTo" id="fTo" value="" disabled="disabled" size="16" />
	  			</div>
				</div>
				<div class="cleaner"></div>
			<?php } ?>
			<?php if ($config->locations && !empty($this->backurl)) {
					echo AHtml::locations($config->locations == 2, $this->backurl, false, true, true); ?> 
				<div class="cleaner"></div>
			<?php } ?>
			<div class="bookit">
				<div class="checkInfo checkInfoMessage" id="checkInfo"></div>
				<h2 id="total">&nbsp;</h2>
				<a class="checkButton bookitButton" id="bookIt" href="javascript:Calendars.bookIt()">
					<?php echo $this->mode == 'change' ? JText::_('CHANGE') : JText::_('BOOK_IT'); ?></a>
				<?php if ($config->showCapacity) { ?>
					<div class="capacity">
						<strong><?php echo JText::_('Quantity'); ?></strong>
                        <select name="capacity" id="capacity" onchange="Calendars.showTotal();Calendars.showOccupancy()" class="input-mini">
							<?php echo JHtml::_('select.options', array_combine(range($this->subject->minimum_capacity, $this->subject->total_capacity), range($this->subject->minimum_capacity, $this->subject->total_capacity)), '', '', $this->subject->minimum_capacity); ?>														
						</select>
						<label for="capacity"><?php echo $this->template->name; ?></label>
						<div class="clr"></div>
					</div>
				<?php } else { ?>
					<input type="hidden" name="capacity" id="capacity" value="1" />
				<?php 
					}
					if ($this->subject->standard_occupancy_max > 1 || $this->subject->extra_occupancy_max > 1) {
						foreach (range(1, $this->subject->total_capacity) as $i) {
							$latestOType = reset($this->occupancyTypes)->type;
				?>
						<div class="occupancy" id="capacity<?php echo $i; ?>occupancy"<?php if ($i > 1) { ?> style="height: 0px; overflow: hidden;"<?php } ?>>
							<fieldset>
								<legend><?php echo $latestOType == 0 ? JText::sprintf('QUANTITY_STANDARD_OCCUPANCY', $i, $this->template->name) : JText::sprintf('QUANTITY_EXTRA_OCCUPANCY', $i); ?></legend>
				<?php 
								$st = $ex = 0;				
								foreach($this->occupancyTypes as $otype) // occupancy types count
									$otype->type == 0 ? $st ++ : $ex ++; 
								
								foreach($this->occupancyTypes as $otype) {
									if ($otype->type == 0 && $st == 1) { // only standard occupancy type
										$min = $this->subject->standard_occupancy_min;
										$emptyOption = false;
									} else if ($otype->type == 1 && $ex == 1) { // only extra occupancy type
										$min = $this->subject->extra_occupancy_min;
										$emptyOption = false;
									} else {
										$min = 1;
										$emptyOption = true;
									}
									$max = $otype->type == 0 ? $this->subject->standard_occupancy_max : $this->subject->extra_occupancy_max;
				
									if ($otype->type == 1 && $latestOType == 0) {
				?>
										</fieldset>
										<fieldset>
											<legend><?php echo JText::sprintf('QUANTITY_EXTRA_OCCUPANCY', $i, $this->template->name); ?></legend>
				<?php 
									}
				?>				
									<select name="occupancy[<?php echo $i - 1; ?>][<?php echo $otype->id; ?>]" id="capacity<?php echo $i; ?>occupancy<?php echo $otype->id; ?>" onchange="Calendars.showTotal();" class="input-mini">
										<?php if ($emptyOption) { ?>
											<option value="0" selected="selected">-</option>
										<?php }
										echo JHtml::_('select.options', array_combine(range($min, $max), range($min, $max))); ?>										
									</select>
									<label for="capacity<?php echo $i; ?>occupancy<?php echo $otype->id; ?>"><?php echo $otype->title; ?></label>
				<?php 			
									$latestOType = $otype->type;
								}
				?>
							</fieldset>
							</div>
				<?php 
						}
					}					
				?>
			</div>
		</div>
<?php 
}
?>