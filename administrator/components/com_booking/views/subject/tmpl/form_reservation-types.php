<?php

/**
 * Subject-rezervation-types edit form template.
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

$options[] = JHTML::_('select.option', '', JText::_('SELECT_LIST'));
$options[] = JHTML::_('select.option', RESERVATION_TYPE_HOURLY, JText::_('HOURLY'));
$options[] = JHTML::_('select.option', RESERVATION_TYPE_DAILY, JText::_('DAILY'));

$days[] = JHTML::_('select.option', 'mon', JText::_('MONDAY'));
$days[] = JHTML::_('select.option', 'tue', JText::_('Tuesday'));
$days[] = JHTML::_('select.option', 'wed', JText::_('Wednesday'));
$days[] = JHTML::_('select.option', 'thu', JText::_('Thursday'));
$days[] = JHTML::_('select.option', 'fri', JText::_('Friday'));
$days[] = JHTML::_('select.option', 'sat', JText::_('Saturday'));
$days[] = JHTML::_('select.option', 'sun', JText::_('SUNDAY'));

?>	
<div class="width-100" id="reservation-types">
	<fieldset class="adminform">
    	<legend class="hasTip" title="<?php echo $this->escape(JText::_('RESERVATION_TYPES')) . '::' . $this->escape(JText::_('RESERVATION_TYPES_TOP_INFO')); ?>">
    		<?php echo JText::_('RESERVATION_SETTINGS'); ?> 
    	</legend>
    	<div class="col">
    		<?php
    		//new, but unused
    		/*
    			$bar = &JToolBar::getInstance('r-types');
        		$bar->appendButton('ALink', 'new', 'Add', 'EditSubject.addRtype()');
        		$bar->appendButton('ALink', 'delete', 'Delete', 'EditSubject.removeRtypes()');
        		echo $bar->render();
        	*/
    		//old
    		/*
    		echo '<div class="btn-group pull-left">
				<button type="button" class="btn" title="' . JText::_('ADD') . '" onclick="EditSubject.addRtype(); return false;"><span class="icon-new"></span>New</button>
				<button type="button" class="btn" title="' . JText::_('DELETE') . '" onclick="EditSubject.removeRtypes(); return false;"><span class="icon-delete"></span>Delete</button>
			</div>';
			*/
    		?>
    		<div class="clr"></div>
    	<div class="col" style="width: 100%">
    		<fieldset class="radio">
    		<table class="template">
    			<tr>
    				<td>
    					<input type="checkbox" class="inputCheckbox" name="rlimit_set" id="rlimit_set" value="1"<?php if ($this->subject->rlimit_set) { ?> checked="checked"<?php } ?> onclick="EditSubject.setRLimit()" />
    					<label for="rlimit_set" class="hasTip" title="<?php echo $this->escape(JText::_('RESERVATION_LIMIT')) . '::' . $this->escape(JText::_('RESERVATION_LIMIT_INFO')); ?>"><?php echo JText::_('SET_RESERVATION_LIMIT'); ?></label>
    				</td>
    				<td colspan="2" >
    					<div id="rlimit_box"<?php if (! $this->subject->rlimit_set) { ?> style="display: none"<?php } ?>>
    						<label for="rlimit_count" style="float: left"><?php echo JText::_('RESERVATION_LIMIT_COUNT'); ?></label>
    						<input type="text" style="float: left" name="rlimit_count" id="rlimit_count" value="<?php echo $this->subject->rlimit_count; ?>" onkeyup="ACommon.toInt(this)" size="5" />
    						<label for="rlimit_days" style="float: left"><?php echo JText::_('RESERVATION_LIMIT_ITEMS'); ?></label>
    						<input type="text" style="float: left" name="rlimit_days" id="rlimit_days" value="<?php echo $this->subject->rlimit_days; ?>" onkeyup="ACommon.toInt(this)" size="5" />
    						<label style="float: left"><?php echo JText::_('RESERVATION_LIMIT_UNITS'); ?></label>
    					</div>
    				</td>
    			</tr>
    			<tr>
					<td colspan="3">
    					<input type="checkbox" class="inputCheckbox" name="price_overlay" id="price_overlay" value="1" <?php if ($this->subject->price_overlay == 1) { ?>checked="checked"<?php } ?> />
    					<label class="hasTip" for="price_overlay" title="<?php echo $this->escape(($title = JText::_('OVERLAY_PRICES'))) . '::' . $this->escape(JText::_('OVERLAY_PRICES_INFO')); ?>"><?php echo $title; ?></label>
    				</td>
    			</tr>
				<tr>
					<td colspan="3">
    					<input type="checkbox" class="inputCheckbox" name="display_only_one_rtype" id="display_only_one_rtype" value="1" <?php if ($this->subject->display_only_one_rtype == 1) { ?>checked="checked"<?php } ?> />
    					<label class="hasTip" for="display_only_one_rtype" title="<?php echo $this->escape(($title = JText::_('DISPLAY_ONLY_ONE_RTYPE'))) . '::' . $this->escape(JText::_('DISPLAY_ONLY_ONE_RTYPE_INFO')); ?>"><?php echo $title; ?></label>
    				</td>
    			</tr>
    			<tr>
    				<td>
    					<input type="checkbox" class="inputCheckbox" name="use_fix_shedule" id="use_fix_shedule" value="1" <?php if ($this->subject->use_fix_shedule) { ?>checked="checked"<?php } ?> onclick="document.id('shedule_from').style.display = document.id('shedule_to').style.display = this.checked ? '' : 'none'" />		
    					<label class="hasTip" for="use_fix_shedule" title="<?php echo $this->escape(($title = JText::_('USE_FIX_SHEDULE'))) . '::' . $this->escape(JText::_('USE_FIX_SHEDULE_INFO')); ?>"><?php echo $title; ?></label>
    				</td>
    				<td>
    					<div id="shedule_from"<?php if (! $this->subject->use_fix_shedule) { ?> style="display: none"<?php } ?>>
    						<label for="shedule_from"><?php echo JText::_('FROM'); ?><span class="star"> *</span></label>
    						<?php echo AHtml::getTimePicker($this->subject->shedule_from, 'shedule_from', false, '', true); ?>
    					</div>
    				</td>
    				<td>
    					<div id="shedule_to"<?php if (! $this->subject->use_fix_shedule) { ?> style="display: none"<?php } ?>>
    						<label for="shedule_to"><?php echo JText::_('TO'); ?><span class="star"> *</span></label>
    						<?php echo AHtml::getTimePicker($this->subject->shedule_to, 'shedule_to', false, '', true); ?>
    					</div>
    					<!-- 
    					<label for="reserving" class="hasTip" title="<?php echo $this->escape(JText::_('RESERVING')) . '::' . $this->escape(JText::_('RESERVING_INFO')); ?>"></label>
						<?php
    						$options2[] = JHTML::_('select.option', RESERVING_EXCLUSIVE, JText::_('RESERVING_EXCLUSIVE'));
    						$options2[] = JHTML::_('select.option', RESERVING_CHAIN, JText::_('RESERVING_CHAIN'));
    						$options2[] = JHTML::_('select.option', RESERVING_OVERLAP, JText::_('RESERVING_OVERLAP'));
    						echo JHTML::_('select.genericlist', $options2, 'reserving', '', 'value', 'text', $this->subject->reserving);
   						?>
    					-->
    				</td>
    			</tr> 
    			<tr>
    				<td>
    					<input type="checkbox" class="inputCheckbox" name="night_booking" id="night_booking" value="1" <?php if ($this->subject->night_booking) { ?>checked="checked"<?php } ?> onclick="document.id('night_booking_from').style.display = document.id('night_booking_to').style.display = this.checked ? '' : 'none'" />
    					<label class="hasTip" for="night_booking" title="<?php echo $this->escape(($title = JText::_('NIGHT_BOOKING'))) . '::' . $this->escape(JText::_('NIGHT_BOOKING_INFO')); ?>"><?php echo $title; ?></label>
    				</td>
    				<td>
    					<div id="night_booking_from"<?php if (! $this->subject->night_booking) { ?> style="display: none"<?php } ?>>
    						<label for="night_booking_from"><?php echo JText::_('CHECK_IN'); ?><span class="star"> *</span></label>
    						<?php echo AHtml::getTimePicker($this->subject->night_booking_from, 'night_booking_from', false, ''); ?>
    					</div>
    				</td>
    				<td>
    					<div id="night_booking_to"<?php if (! $this->subject->night_booking) { ?> style="display: none"<?php } ?>>
    						<label for="night_booking_to"><?php echo JText::_('CHECK_OUT'); ?><span class="star"> *</span></label>
    						<?php echo AHtml::getTimePicker($this->subject->night_booking_to, 'night_booking_to', false, ''); ?>
    					</div>
    				</td>
    			</tr>
    			<tr>
    				<td>
    					<label class="hasTip" for="min_limit" title="<?php echo $this->escape(($title = JText::_('MIN_RTYPE_LIMIT'))) . '::' . $this->escape(JText::_('MIN_RTYPE_LIMIT_INFO')); ?>"><?php echo $title; ?></label>
    				</td>
    				<td colspan="2">
    					<input type="text" name="min_limit" id="min_limit" value="<?php echo $this->subject->min_limit; ?>" onkeyup="ACommon.toInt(this)" class="number" size="5"/>
    				</td>
    			</tr>
			</table>
			</fieldset>
		</div>
		</div>
	</fieldset>
	<fieldset class="adminform">
    	<legend class="hasTip" title="<?php echo $this->escape(JText::_('RESERVATION_TYPES')) . '::' . $this->escape(JText::_('RESERVATION_TYPES_TOP_INFO')); ?>">
    		<?php echo JText::_('RESERVATION_TYPES'); ?> 
    	</legend>
    	<div class="col">
    		<?php
    			//new
	    		$bar = &JToolBar::getInstance('r-types');
	    		$bar->appendButton('ALink', 'new', 'Add', 'EditSubject.addRtype()');
	    		$bar->appendButton('ALink', 'delete', 'Delete', 'EditSubject.removeRtypes()');
	    		echo $bar->render();
    			//old
    			/*
    			$bar = &JToolBar::getInstance('r-types');
        		$bar->appendButton('Link', 'new', 'Add', 'javascript:EditSubject.addRtype();');
        		$bar->appendButton('Link', 'delete', 'Delete', 'javascript:EditSubject.removeRtypes();');
        		echo $bar->render();
        		*/
    		?>
    		<div class="clr"></div>
			<table class="template">
				<thead>
					<tr>
						<th rowspan="2" valign="top">&nbsp;</th>
						<th rowspan="2" valign="top"><h3><?php echo JText::_('ID'); ?></h3></th>
						<th rowspan="2" valign="top"><h3><?php echo JText::_('TITLE'); ?><span class="star"> *</span></h3></th>
						<th rowspan="2" valign="top"><h3><?php echo JText::_('TYPE'); ?><span class="star"> *</span></h3></th>
						<th rowspan="2" valign="top"><h3><?php echo JText::_('DESCRIPTION'); ?></h3></th>
						<th rowspan="2" valign="top"><h3><?php echo JText::_('LIMIT_RESTRICTIONS'); ?></h3></th>
						<!-- 
						<th rowspan="2" valign="top"><h3><?php echo JText::_('CAPACITY_UNIT'); ?></h3></th>
						 -->
						<th colspan="3" align="center" valign="top"><h3><?php echo JText::_('SETTINGS_ONLY_FOR_HOURLY_RESERVATION_TYPES'); ?></h3></th>
						<!-- 
						<th rowspan="2" valign="top"><h3><?php echo JText::_('SPECIAL_OFFER'); ?></h3></th>
						 -->
					</tr>
					<tr>
						<th valign="top"><h3><?php echo JText::_('TIME_UNIT'); ?><span class="star"> *</span></h3></th>
						<th valign="top"><h3><?php echo JText::_('GAP_TIME'); ?></h3></th>
					</tr>
				</thead>
				<tbody id="rtypes">
				<?php 
					$pcount = count($this->rtypes);
					for ($i = 0; $i < $pcount; $i++) {
						$rtype = &$this->rtypes[$i];
						/* @var $rtype TableReservationType */
						TableReservationType::display($rtype);
						$id = $i ? $rtype->id : ''; 
					?>
						<tr <?php if (! $id) { echo 'id="rtype" style="display: none;"'; } ?>>
							<td class="check" valign="top">
								<input type="checkbox" class="inputCheckbox" name="rcid[]" value="1"/>
							</td>
							<td class="id" valign="top">
								<?php echo $id ? $id : '<i>' . JText::_('NEW') . '</i>'; ?>
							</td>
							<td valign="top">
								<input type="text" name="<?php echo RTYPES_PREFIX; ?>title[<?php echo $id; ?>]" value="<?php echo $rtype->title; ?>" class="title"/>
							</td>
							<td valign="top">
								<?php echo JHTML::_('select.genericlist', $options, RTYPES_PREFIX . 'type[' . $id . ']', 'onchange="EditSubject.setReservationType(this)"', 'value', 'text', $rtype->type); ?>
							</td>
							<td valign="top">
								<textarea name="<?php echo RTYPES_PREFIX; ?>description[<?php echo $id; ?>]" class="description" rows="1" cols="50"><?php echo $rtype->description; ?></textarea>
							</td>
							<td valign="top">
								<div class="limitLegend" style="float: left">
									<label for="min<?php echo $rtype->id; ?>" class="hasTip" title="<?php echo $this->escape(JText::_('MIN_LIMIT')) . '::' . $this->escape(JText::_('MIN_LIMIT_INFO')); ?>"><?php echo JText::_('MIN_LIMIT'); ?></label>
									<input id="min<?php echo $rtype->id; ?>" type="text" name="<?php echo RTYPES_PREFIX; ?>min[<?php echo $id; ?>]" value="<?php echo $rtype->min; ?>" />
								</div>
								<div class="limitLegend" style="float: left">
									<label for="max<?php echo $rtype->id; ?>" class="hasTip" title="<?php echo $this->escape(JText::_('MAX_LIMIT')) . '::' . $this->escape(JText::_('MAX_LIMIT_INFO')); ?>"><?php echo JText::_('MAX_LIMIT'); ?></label>
									<input id="max<?php echo $rtype->id; ?>" type="text" name="<?php echo RTYPES_PREFIX; ?>max[<?php echo $id; ?>]" value="<?php echo $rtype->max; ?>" />
								</div>
								<div class="limitLegend">
									<label for="fix<?php echo $rtype->id; ?>" class="hasTip" title="<?php echo $this->escape(JText::_('FIX_LIMIT')) . '::' . $this->escape(JText::_('FIX_LIMIT_INFO')); ?>"><?php echo JText::_('FIX_LIMIT'); ?></label>
									<input id="fix<?php echo $rtype->id; ?>" type="text" name="<?php echo RTYPES_PREFIX; ?>fix[<?php echo $id; ?>]" value="<?php echo $rtype->fix; ?>" />
								</div>
								<div class="limitLegend">
									<label for="fix<?php echo $rtype->id; ?>" class="hasTip" title="<?php echo $this->escape(JText::_('FIX_FROM')) . '::' . $this->escape(JText::_('FIX_FROM_INFO')); ?>"><?php echo JText::_('FIX_FROM'); ?></label>
									<input type="hidden" name="<?php echo RTYPES_PREFIX; ?>fix_from[<?php echo (int) $id; ?>][fix_from_start]" value="<?php echo $id ? 'old' : 'new'; ?>" />
									<?php echo JHTML::_('select.genericlist', $days, RTYPES_PREFIX . 'fix_from[' . (int) $id . '][]', 'multiple="multiple" size="7"', 'value', 'text', $rtype->fix_from); ?>
								</div>					
                                <input type="hidden" name="<?php echo RTYPES_PREFIX; ?>book_fix_past[<?php echo $id; ?>]" value="0" />
								<label class="hasTip" title="<?php echo $this->escape(JText::_('BOOK_FIX_LIMIT_TO_THE_PAST')) . '::' . $this->escape(JText::_('BOOK_FIX_LIMIT_TO_THE_PAST_INFO')); ?>">									
									<input id="book_fix_past<?php echo $rtype->id; ?>" type="checkbox" name="<?php echo RTYPES_PREFIX; ?>book_fix_past[<?php echo $id; ?>]" value="1" <?php if ($rtype->book_fix_past) { ?>checked="checked"<?php } ?> />
                                    <span class="checkboxLabel"><?php echo JText::_('BOOK_FIX_LIMIT_TO_THE_PAST'); ?></span>
                                </label>
                                <input type="hidden" name="<?php echo RTYPES_PREFIX; ?>fix_multiply[<?php echo $id; ?>]" value="0" />                                
                                <label class="hasTip" title="<?php echo $this->escape(JText::_('FIX_MULTIPLY')) . '::' . $this->escape(JText::_('FIX_MULTIPLY_INFO')); ?>">									
									<input id="fix_multiply<?php echo $rtype->id; ?>" type="checkbox" name="<?php echo RTYPES_PREFIX; ?>fix_multiply[<?php echo $id; ?>]" value="1" <?php if ($rtype->fix_multiply) { ?>checked="checked"<?php } ?> />
                                    <span class="checkboxLabel"><?php echo JText::_('FIX_MULTIPLY'); ?></span>
                                </label>
							</td>
							<!-- 
							<td valign="top">
								<input type="text" name="<?php echo RTYPES_PREFIX; ?>capacity_unit[<?php echo $id; ?>]" value="<?php echo $rtype->capacity_unit; ?>" class="number" onkeyup="ACommon.toInt(this)" />
							</td>
							 -->
							<td class="time" valign="top">
								<input type="text" name="<?php echo RTYPES_PREFIX; ?>time_unit[<?php echo $id; ?>]" value="<?php echo $rtype->time_unit; ?>" class="number" style="float: none;" onkeyup="ACommon.toInt(this);EditSubject.setReservationGapType(document.id('gaptime<?php echo $id; ?>'));" />
								<br /><input type="checkbox" id="gaptime<?php echo $id; ?>" name="<?php echo RTYPES_PREFIX; ?>dynamic_gap_time[<?php echo $id; ?>]" <?php echo $rtype->dynamic_gap_time? 'checked="checked"':''; ?> onchange="EditSubject.setReservationGapType(this)" />
								<?php echo JText::_('DYNAMIC_GAP_TIME'); ?>
							</td>
							<td class="time" valign="top">
								<input type="text" name="<?php echo RTYPES_PREFIX; ?>gap_time[<?php echo $id; ?>]" value="<?php echo $rtype->gap_time; ?>" class="number" onkeyup="ACommon.toInt(this)" />
							</td>
							<!-- 
							<td valign="top" align="center">
								<input type="checkbox" name="<?php echo RTYPES_PREFIX; ?>special_offer[<?php echo $id; ?>]" value="1" <?php if ($rtype->special_offer) { ?>checked="checked"<?php } ?> class="inputCheckbox" />
							</td>
							 -->
						</tr>
					<?php } ?>
						<tr id="rtype-empty" <?php if ($pcount > 1) { ?>style="display: none;"<?php } ?>><td colspan="7" class="emptyList"><?php echo JText::_('EMPTY_RESERVATION_TYPES_LIST'); ?></td></tr>
				</tbody>
			</table>
		</div>
		<div class="clr"></div>
    </fieldset>
</div>    