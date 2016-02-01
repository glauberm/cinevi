<?php

/**
 * Prices list template.
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
/* @var $config BookingConfig */

if ($config->priceLayout == 'detailed_list') {
?>
	<h2><?php echo $this->subject->single_deposit ? JText::sprintf('PRICES_SINGLE_DEPOSIT', BookingHelper::displayPrice($this->subject->single_deposit, null, $this->subject->tax)) : JText::_('PRICES'); ?></h2>
	
	<?php if ($this->subject->discounts) { ?>
		<strong><?php echo JText::_('SINGLE_DISCOUNTS'); ?></strong><br/>
		<?php 
			foreach ($this->subject->discounts as $unitCount => $unitDiscount)
				$discounts[] = JText::sprintf('COUNT_UNITS_DISCOUNT', $unitCount, BookingHelper::displayPrice($unitDiscount, null, $this->subject->tax));
			echo implode('<br/>', $discounts);
		?>
	<?php } ?>
	
	<div class="prices">
		<?php if (count($this->days->prices)) { ?><input type="hidden" name="rids[]" value="0" /><?php } ?>
		<?php 
			//var_dump($this->days);
		foreach ($this->days->prices as $object)
			foreach ($object as $rtype) { 
				/* @var $rtype TableReservationType */
				if (!($isDaily = $rtype->type == RESERVATION_TYPE_DAILY) && $this->calendar == CTYPE_MONTHLY) {
		?>
					<input type="hidden" value="<?php echo $rtype->id; ?>" name="rids[]" id="rids<?php echo $rtype->id; ?>" />
		<?php
					continue;
				}
				$title = $isDaily ? JText::sprintf('RESERVATION_TYPE_INFO_SHORT', $rtype->title) : JText::sprintf('RESERVATION_TYPE_INFO', $rtype->title, $rtype->time_unit_orig);
		?>
				<div class="rtype">
					<h3 class="hasTip" title="<?php echo $this->escape($rtype->title) . '::' . $this->escape($rtype->description); ?>">
						<input type="<?php if ($this->subject->display_only_one_rtype) { ?>radio<?php } else { ?>checkbox<?php } ?>" value="<?php echo $rtype->id; ?>" name="rids[]" id="rids<?php echo $rtype->id; ?>"<?php if (in_array($rtype->id, $this->lists['rids'])) { ?> checked="checked"<?php } ?> onclick="this.form.submit()" />
						<label for="rids<?php echo $rtype->id; ?>"><?php echo $this->escape($title); ?></label>
					</h3>
		<?php 
						foreach ($rtype->prices as $priceIndex => $price) {
							/* @var $price TablePrice */
							$discounts = array();
							if ($isDaily){
								if ($price->deposit)
									$title = JText::sprintf('PRICE_INFO_DAILY_FULL', $rtype->title, $price->formatValue, $price->formatDeposit);
								else  	
									$title = JText::sprintf('PRICE_INFO_DAILY', $rtype->title, $price->formatValue);
								if (! $this->subject->discounts)
									foreach ($price->discounts as $unitCount => $unitDiscount)
										$discounts[] = JText::sprintf('COUNT_DAYS_DISCOUNT', $unitCount, BookingHelper::displayPrice($price->value - $unitDiscount, null, $this->subject->tax));	
							} else {
								if ($price->deposit)
									$title = JText::sprintf('PRICE_INFO_HOURLY_FULL', $rtype->title, $rtype->time_unit_orig, $price->formatValue, $price->formatDeposit);
								else  	
									$title = JText::sprintf('PRICE_INFO_HOURLY', $rtype->title, $rtype->time_unit_orig, $price->formatValue);
								if (! $this->subject->discounts)
									foreach ($price->discounts as $unitCount => $unitDiscount)
										$discounts[] = JText::sprintf('COUNT_HOURS_DISCOUNT', $unitCount, BookingHelper::displayPrice($price->value - $unitDiscount, null, $this->subject->tax));	
							}
							
							$title .= ' '.JText::_('PAYMENT_EXPIRE').': '.BookingHelper::formatExpiration($price->cancel_time,null);
							if ($price->value) {
		?>
								<div class="hasTip price price<?php echo $priceIndex; ?>" title="<?php echo $this->escape($price->formatPrice) . '::' . $this->escape($title); ?>"><?php echo $price->formatPrice; ?><?php echo count($discounts) ? '<br/>' . implode('<br/>', $discounts) : ''; ?></div>
		<?php 
							} else {
		?>
								<div class="price price<?php echo $priceIndex; ?>">&nbsp;</div>
		<?php
							}
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
} elseif ($config->priceLayout == 'brief_legend') {
	if (count($this->days->prices)) { 
		$rids = $this->lists['rids'];
		$rids[] = 0;
?>
		<ul class="prices">
			<li class="title"><?php echo JText::_('PRICES') ?></li>
<?php	
			foreach ($this->days->prices as $rtype) {
				$rids[] = $rtype->id;
				foreach ($rtype->prices as $pi => $price) {
?>
					<li>
						<span class="price price<?php echo $pi; ?>"><?php echo $price->formatPrice; ?></span>
					</li>
<?php			
				}
			}
?>
		</ul>
<?php
		foreach (array_unique($rids) as $rid) {
?> 
			<input type="hidden" value="<?php echo $rid; ?>" name="rids[]" id="rids<?php echo $rid; ?>" />
<?php		
		}	
	}  
} else { // hidden list
	if (count($this->days->prices)) { 
		$rids = $this->lists['rids'];
		$rids[] = 0;
		foreach ($this->days->prices as $object)
			foreach ($object as $rtype) $rids[] = $rtype->id;		
		foreach (array_unique($rids) as $rid) {
?>		
			<input type="hidden" value="<?php echo $rid; ?>" name="rids[]" id="rids<?php echo $rid; ?>" />
<?php		
		}
	}
}
?>