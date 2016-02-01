<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Quantity and Occupancy edit form template
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

?>
<div class="width-100">
	<div class="col width-50">
		<fieldset class="adminform">
    		<legend><?php echo JText::_('QUANTITY'); ?></legend>
    		<table class="admintable width-100">
    			<tr>
    				<td class="key"><label class="hasTip" title="<?php echo $this->escape(JText::_('QUANTITY')); ?>::<?php echo $this->escape(JText::_('QUANTITY_TIP')); ?>"><?php echo JText::_('QUANTITY'); ?><span class="star"> *</span></label></td>
    				<td class="occupancy"><fieldset class="radio">
	    				<input class="number" onkeyup="ACommon.toInt(this)" type="text" name="minimum_capacity" id="minimum_capacity" size="1" maxlength="10" value="<?php echo $this->subject->minimum_capacity; ?>" /><label class="hasTip" for="total_capacity" title="<?php echo $this->escape(JText::_('MINIMUM_CAPACITY')).'::'.$this->escape(JText::_('MINIMUM_CAPACITY_INFO')); ?>"><?php echo JText::_('Min'); ?></label>
    					<input class="number" onkeyup="ACommon.toInt(this)" type="text" name="total_capacity" id="total_capacity" size="1" maxlength="10" value="<?php echo $this->subject->total_capacity; ?>" /><label class="hasTip" for="minimum_capacity" title="<?php echo $this->escape(JText::_('TOTAL_CAPACITY')) . '::' . $this->escape(JText::_('TOTAL_CAPACITY_INFO')); ?>"><?php echo JText::_('Max'); ?></label>
    				</fieldset></td>
    			</tr>
    			<tr>
    				<td class="key"><label class="hasTip" title="<?php echo $this->escape(($title = JText::_('DISPLAY_CAPACITY'))) . '::' . $this->escape(JText::_('DISPLAY_CAPACITY_INFO')); ?>"><?php echo $title; ?></label></td>
    				<td>
    				   	<fieldset class="radio btn-group">
    						<input type="radio" name="display_capacity" id="display_capacity_0" value="0" class="inputRadio" <?php if (!$this->subject->display_capacity) { ?> checked="checked" <?php } ?> />
    						<label for="display_capacity_0"><?php echo jtext::_('JNO'); ?></label>
    						<input type="radio" name="display_capacity" id="fdisplay_capacity_1" value="1" class="inputRadio" <?php if ($this->subject->display_capacity) { ?> checked="checked" <?php } ?> />
    						<label for="fdisplay_capacity_1"><?php echo JText::_('JYES'); ?></label>
    					</fieldset>
    				</td>
    			</tr>
    		</table>
    	</fieldset>
   	</div>
   	<div class="col width-50">
    	<fieldset class="adminform">
    		<legend><?php echo JText::_('OCCUPANCY'); ?></legend>
    		<table>
    			<tr>
    				<td class="key"><label><?php echo JText::_('SHOW_OCCUPANCY'); ?></label></td>
    				<td>
    					<fieldset class="radio btn-group"">
    						<input type="radio" name="show_occupancy" id="hide_occupancy" value="0" class="btn-group inputRadio" <?php if (!$this->subject->show_occupancy) { ?>checked="checked"<?php } ?> onclick="EditSubject.hideOccupancy()" />
    						<label for="hide_occupancy"><?php echo JText::_('JNO'); ?></label>
    						<input type="radio" name="show_occupancy" id="show_occupancy" value="1" class="btn-group inputRadio" <?php if ($this->subject->show_occupancy) { ?>checked="checked"<?php } ?> onclick="EditSubject.showOccupancy()" />
    						<label for="show_occupancy"><?php echo JText::_('JYES'); ?></label>    						
    					</fieldset>
    				</td>
    			</tr>
    			<tr class="occupancyrow">
    				<td class="key"><label class="hasTip" title="<?php echo $this->escape(JText::_('STANDARD_OCCUPANCY')); ?>::<?php echo $this->escape(JText::_('STANDARD_OCCUPANCY_TIP')); ?>"><?php echo JText::_('STANDARD_OCCUPANCY'); ?></label></td>
    				<td class="occupancy"><fieldset class="radio">
	    				<input class="number" onkeyup="ACommon.toInt(this)" type="text" name="standard_occupancy_min" id="standard_occupancy_min" size="1" maxlength="10" value="<?php echo $this->subject->standard_occupancy_min; ?>" /><label for="standard_occupancy_min"><?php echo JText::_('Min'); ?></label>
    					<input class="number" onkeyup="ACommon.toInt(this)" type="text" name="standard_occupancy_max" id="standard_occupancy_max" size="1" maxlength="10" value="<?php echo $this->subject->standard_occupancy_max; ?>" /><label for="standard_occupancy_max"><?php echo JText::_('Max'); ?></label>
    				</fieldset></td>
    			</tr>
    			<tr class="occupancyrow">
    				<td class="key"><label class="hasTip" title="<?php echo $this->escape(JText::_('EXTRA_OCCUPANCY')); ?>::<?php echo $this->escape(JText::_('EXTRA_OCCUPANCY_TIP')); ?>"><?php echo JText::_('EXTRA_OCCUPANCY'); ?></label></td>
    				<td class="occupancy"><fieldset class="radio">
	    				<input class="number" onkeyup="ACommon.toInt(this)" type="text" name="extra_occupancy_min" id="extra_occupancy_min" size="1" maxlength="10" value="<?php echo $this->subject->extra_occupancy_min; ?>" /><label for="extra_occupancy_min"><?php echo JText::_('Min'); ?></label>
    					<input class="number" onkeyup="ACommon.toInt(this)" type="text" name="extra_occupancy_max" id="extra_occupancy_max" size="1" maxlength="10" value="<?php echo $this->subject->extra_occupancy_max; ?>" /><label for="extra_occupancy_max"><?php echo JText::_('Max'); ?></label>
    				</fieldset></td>
    			</tr>
    			<tr class="occupancyrow">
    				<td class="key"><label class="hasTip" title="<?php echo $this->escape(JText::_('STANDARD_OCCUPANCY_TYPES')); ?>::<?php echo $this->escape(JText::_('STANDARD_OCCUPANCY_TYPES_TIP')); ?>"><?php echo JText::_('STANDARD_OCCUPANCY_TYPES'); ?></label></td>
    				<td class="occupancy">
    					<?php foreach ($this->otypes as $otype) { 
    							if ($otype->type == 0) { ?>
    								<span id="otype<?php echo $otype->id; ?>">
    									<input type="text" name="<?php echo OTYPES_PREFIX; ?>title[<?php echo $otype->id; ?>]" value="<?php echo $otype->title; ?>" size="10" maxlength="100" />
    									<input type="hidden" name="<?php echo OTYPES_PREFIX; ?>type[<?php echo $otype->id; ?>]" value="0" />
    									<a href="javascript:EditSubject.removeOccupancyType('otype<?php echo $otype->id; ?>')" class="aIcon aIconUnpublish aIconInline"></a>
    								</span>
    					<?php } 
    						} ?>
    					<a href="javascript:EditSubject.addOccupancyType('ostandard', 0)" class="aIcon aIconNew aIconInline" id="ostandard"></a>
    				</td>
    			</tr>
    			<tr class="occupancyrow">
    				<td class="key"><label class="hasTip" title="<?php echo $this->escape(JText::_('EXTRA_OCCUPANCY_TYPES')); ?>::<?php echo $this->escape(JText::_('EXTRA_OCCUPANCY_TYPES_TIP')); ?>"><?php echo JText::_('EXTRA_OCCUPANCY_TYPES'); ?></label></td>
    				<td class="occupancy">
    					<?php foreach ($this->otypes as $otype) { 
    							if ($otype->type == 1) { ?>
    								<span id="otype<?php echo $otype->id; ?>">
    									<input type="text" name="<?php echo OTYPES_PREFIX; ?>title[<?php echo $otype->id; ?>]" value="<?php echo $otype->title; ?>" size="10" maxlength="100" />
    									<input type="hidden" name="<?php echo OTYPES_PREFIX; ?>type[<?php echo $otype->id; ?>]" value="1" />
    									<a href="javascript:EditSubject.removeOccupancyType('otype<?php echo $otype->id; ?>')" class="aIcon aIconUnpublish aIconInline"></a>
    								</span>
    					<?php } 
    						} ?>
    					<a href="javascript:EditSubject.addOccupancyType('oextra', 1)" class="aIcon aIconNew aIconInline" id="oextra"></a>
    				</td>
    			</tr>    			
    		</table>
    	</fieldset>
    </div>
    <div class="clr"></div>
</div>   