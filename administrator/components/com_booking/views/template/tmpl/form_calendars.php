<?php

/**
 * Template calendars edit form template.
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

/* @var $this BookingViewTemplate */



if (count($this->calendars)) { 
?>
<div class="width-100" id="calendars">
	<fieldset class="adminform">
    	<legend class="hasTip" title="<?php echo $this->escape(JText::_('CALENDARS')) . '::' . $this->escape(JText::_('CALENDARS_TOPINFO')); ?>">
    		<span class="legend"><?php echo JText::_('CALENDARS'); ?></span>
    	</legend>
    	<div class="clr"></div>
    	<?php if (ISJ3) { ?>
			<fieldset class="radio btn-group">
		<?php } ?>
		<table class="template">
			<?php 
				if (count($this->templateTable->calendars)) {
					$defaultCalendar = reset($this->templateTable->calendars);
					unset($this->templateTable->calendars[0]);
				} else
					$defaultCalendar = reset($this->calendars)->id;	
			?>
			<tr>
				<?php if (!ISJ3) { ?>
				<th>&nbsp;</th>
				<?php } ?>	
				<th>
					<h3 class="hasTip" title="<?php echo $this->escape(JText::_('DEFAULT')) . '::' . $this->escape(JText::_('SET_DEFAULT_CALENDAR')); ?>">
						<?php echo JText::_('DEFAULT'); ?>
					</h3>
				</th>			
				<th>
					<h3 class="hasTip" title="<?php echo $this->escape(JText::_('AVAILABLE')) . '::' . $this->escape(JText::_('AVAILABLE_CALENDARS')); ?>">
						<?php echo JText::_('AVAILABLE'); ?>
					</h3>
				</th>
				<!-- 
				<th>
					<h3 class="hasTip" title="<?php echo $this->escape(JText::_('SHORTEST_INTERVAL')) . '::' . $this->escape(JText::_('USE_SHORTEST_INTERVAL')); ?>">
						<?php echo JText::_('SHORTEST_INTERVAL'); ?>
					</h3>
				</th>
				-->
			</tr>
			<?php 
				$i = 0;
				$pcount = count($this->calendars);
			 	foreach ($this->calendars as $calendar) {
			 ?>
				<tr>
					<td>
						<label for="def<?php echo $i; ?>" class="hasTip" title="<?php echo $this->escape(JText::_($calendar->title)) . '::' . $this->escape(JText::_($calendar->description)); ?>"><?php echo JText::_($calendar->title); ?></label>
					<?php if (!ISJ3) { ?>
					</td>
					<td style="text-align: center" align="center">
					<?php } ?>
						<input type="radio" class="inputRadio" id="def<?php echo $i; ?>" name="calendar_default" value="<?php echo $calendar->id; ?>" <?php if (($isDefault = $defaultCalendar == $calendar->id)) { ?>checked="checked"<?php } ?> onclick="ACommon.calendarSelect(<?php echo $pcount; ?>)" />
					</td>
					<td style="text-align: center" align="center">		
						<input type="checkbox" class="inputCheckbox" id="cal<?php echo $i; ?>" name="calendars[]" value="<?php echo $calendar->id; ?>" onclick="ACommon.calendarSelect(<?php echo $pcount; ?>)" <?php if (($calChecked = in_array($calendar->id, $this->templateTable->calendars))) { ?>checked="checked"<?php } ?> <?php if ($isDefault) { ?>disabled="disabled"<?php } ?> />
					</td>
					<!-- 
					<td style="text-align: center" align="center">		
						<input type="checkbox" class="inputCheckbox" id="shi<?php echo $i; ?>" name="shortest_interval[]" value="<?php echo $calendar->id; ?>" onchange="ACommon.check(this);" <?php if (in_array($calendar->id, $this->templateTable->shortestInterval)) { ?>checked="checked"<?php } ?> <?php if (!$calChecked && !$isDefault) { ?>disabled="disabled"<?php } ?> />
					</td>
					-->
				</tr>
				<?php 
					$i++;
			 	} 
			 ?>
		</table>		
		<table class="template">			
				<tr>
					<td>
						<label class="hasTip" title="<?php echo JText::_("NUM_OF_MONTHS_WEEKS").'::'.JText::_("NUM_OF_MONTHS_WEEKS_INFO"); ?>"><?php echo JText::_("NUM_OF_MONTHS_WEEKS"); ?></label>
					</td>
					<td style="text-align: center">
						<input class="" type="text" name="num_months" id="num_months" size="2" maxlength="5" value="<?php echo $this->templateTable->numberOfMonths; ?>" />
    				</td>
				</tr>	
		</table>
		<?php if (ISJ3) { ?>
			</fieldset>
		<?php } ?>
	</fieldset>
</div>	
<?php } ?>