<?php

/**
 * Supplements list template.
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

$select = JText::_('SELECT_LIST');
$config = AFactory::getConfig();
/* @var $config BookingConfig */

if (count($this->supplements)) {
?>
	<div class="clr"></div>
	<h2><?php echo JText::_('SUPPLEMENTS'); ?></h2>
	<div id="supplements">
<?php 
		foreach ($this->supplements as $supplement) {
			TableSupplement::prepare($supplement);
			/* @var $supplement TableSupplement */
?>	
			<div class="supplement">
				<h3 class="hasTip" title="<?php echo BookingHelper::displaySupplementTooltip($supplement); ?>"><label for="supplements<?php echo $supplement->id; ?>"><?php echo $supplement->title; ?></label></h3>
				<span class="field<?php if ($supplement->type == SUPPLEMENT_TYPE_YESNO) { ?> fieldBoolean<?php } ?>">
<?php 
					if ($supplement->type == SUPPLEMENT_TYPE_LIST) {
						$arr = array();
						if ($supplement->empty == SUPPLEMENT_EMPTY_USE)
							$arr[] = JHTML::_('select.option', '', $select);
						if (is_array($supplement->options))
							foreach ($supplement->options as $option)
								$arr[] = JHTML::_('select.option', $this->escape($option[0]), $this->escape($option[1] && $supplement->paid == SUPPLEMENT_MORE_PRICES ? JText::sprintf('SUPPLEMENT_LABEL_PRICE', $option[0], BookingHelper::displayPrice($option[1])) : $option[0]));
						if (count($arr)) {
							echo JHTML::_('select.genericlist', $arr, 'supplements[' . $supplement->id . '][0]','onchange="Supplements.changedSelect('.$supplement->id.')"','value','text',null,'supplements'.$supplement->id);
							if ($supplement->paid == SUPPLEMENT_MORE_PRICES) {
?>
								<span class="separator"></span>
<?php									
							}
						}
					} elseif ($supplement->type == SUPPLEMENT_TYPE_YESNO) {		 			
?>						
						<input type="checkbox" class="checkbox" name="supplements[<?php echo $supplement->id; ?>][0]" id="supplements<?php echo $supplement->id; ?>" value="1" 
						onclick="Supplements.changedCheckBox(<?php echo $supplement->id; ?>)"/>
<?php 
					}
?>
				</span>
<?php						
				if	($supplement->paid == SUPPLEMENT_NO_PRICE) {
?>						
					<span class="price">
<?php
						echo JText::_('FREE');
?>	
					</span>
<?php
				} 							
				if ($supplement->price && $supplement->paid == SUPPLEMENT_ONE_PRICE) {
?>						
					<span class="price">
<?php
						echo BookingHelper::displayPrice($supplement->price);
?>	
					</span>
<?php
				}	
					
				//supplement quantity select
				if ($supplement->capacity_multiply == 2 ){ ?>
					<span class="quantity_select hasTip" style="display:<?php echo $supplement->type == SUPPLEMENT_TYPE_LIST && $supplement->empty != SUPPLEMENT_EMPTY_USE ? 'inline' : 'none'?>;" 
					id="supplements_capacity<?php echo $supplement->id; ?>" title="<?php echo JText::_('QUANTITY') ?>::<?php echo JText::_('SELECT_SUPPLEMENT_QUANTITY')?>. <?php 
    					if ($supplement->capacity_max)
    						echo JText::sprintf('MAXIMAL_QUANTITY_IS', $supplement->capacity_max).'.';
    					?>">
    				<?php if ($supplement->capacity_max && $supplement->capacity_max<=100) { ?>
    				<select class="capacity" name="supplements[<?php echo $supplement->id; ?>][1]">
    					<option value="0"><?php echo JText::_('QUANTITY') ?></option>
    					<?php for ($i = 1; $i <= $supplement->capacity_max; $i++) { ?>
    						<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
    					<?php } ?>
    				</select>
    				<?php } else { ?>
    					<label><?php echo JText::_('QUANTITY') ?>: 
    					<input size="2" class="capacity" name="supplements[<?php echo $supplement->id; ?>][1]" value="0">
    					</label>
					<?php } ?>
					</span>
<?php 
				}
?>			
				<div class="clr"></div>
			</div>
<?php 
		}
?>
		<div class="clr"></div>
	</div>
<?php 
}
?>