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

if ($config->priceLayout == 'detailed_list') { ?>
 
 	<?php if ($this->subject->volume_discount && $config->showPermanentVolumeDiscount) { ?>
		<h2><?php echo JText::_('SINGLE_DISCOUNTS'); ?></h2>
		<div class="volumediscount">
			<?php foreach ($this->subject->volume_discount as $voldis) { ?>
				<div class="discount">
					<?php
						if ($voldis['type'] == DISCOUNT_TYPE_PERCENT) {
							if ($this->calendar != CTYPE_MONTHLY)
								echo JText::sprintf('COUNT_HOURS_DISCOUNT_PERCENT', $voldis['count'], $voldis['value']);
							else
								echo JText::sprintf('COUNT_DAYS_DISCOUNT_PERCENT', $voldis['count'], $voldis['value']);
						} else {
							if ($this->calendar != CTYPE_MONTHLY)
								echo JText::sprintf('COUNT_HOURS_DISCOUNT', $voldis['count'], BookingHelper::displayPrice($voldis['value'], null, $this->subject->tax));
							else
								echo JText::sprintf('COUNT_DAYS_DISCOUNT', $voldis['count'], BookingHelper::displayPrice($voldis['value'], null, $this->subject->tax));
						}
					?>	
				</div>
			<?php } ?>
			<div class="clr"></div>
		</div>
	<?php } ?>
 	<?php if ($this->subject->early_booking_discount && $config->showEarlyBookingDiscount) { ?>
		<h2><?php echo JText::_('EARLY_BOOKING_DISCOUNT'); ?></h2>
		<div class="volumediscount">
			<?php foreach ($this->subject->early_booking_discount as $voldis) { ?>
				<div class="discount">
					<?php
						if ($voldis['type'] == DISCOUNT_TYPE_PERCENT) {
							if ($this->calendar != CTYPE_MONTHLY)
								echo JText::sprintf('COUNT_HOURS_DISCOUNT_PERCENT', $voldis['count'], $voldis['value']);
							else
								echo JText::sprintf('COUNT_DAYS_DISCOUNT_PERCENT', $voldis['count'], $voldis['value']);
						} else {
							if ($this->calendar != CTYPE_MONTHLY)
								echo JText::sprintf('COUNT_HOURS_DISCOUNT', $voldis['count'], BookingHelper::displayPrice($voldis['value'], null, $this->subject->tax));
							else
								echo JText::sprintf('COUNT_DAYS_DISCOUNT', $voldis['count'], BookingHelper::displayPrice($voldis['value'], null, $this->subject->tax));
						}
					?>	
				</div>
			<?php } ?>
			<div class="clr"></div>
		</div>
	<?php } ?>	
 
<?php if ($this->subject->single_deposit) {
		if ($this->subject->single_deposit_type == DEPOSIT_TYPE_PERCENT) { 
?> 
			<h2><?php echo JText::sprintf('PRICES_SINGLE_DEPOSIT_PERCENT', $this->subject->single_deposit); ?></h2>
<?php 
		} else { 
?>
			<h2><?php echo JText::sprintf('PRICES_SINGLE_DEPOSIT', BookingHelper::displayPrice($this->subject->single_deposit, null, $this->subject->tax)); ?></h2>
<?php 
		}
	} else { 
?>
		<h2><?php echo JText::_('PRICES'); ?></h2>
<?php 
	} 
?>
	<div class="prices">
		<?php if (count($this->days->prices)) { ?><input type="hidden" name="rids[]" value="0" /><?php } ?>
		<?php 
			// search some rtype what is not shown on the page
			foreach ($this->reservationTypes as $drtype) { // rtype full list  
				foreach ($this->days->prices as $rtype) // rtype current list 
					if ($drtype->id == $rtype->id) // rtype is shown
						continue 2;
				if (in_array($drtype->id, $this->lists['rids'])) { // rtype is not shown but in session is selected - place as hidden to not lose selection ?>
					<input type="hidden" value="<?php echo $drtype->id; ?>" name="rids[]" />
				<?php }
			}
			foreach ($this->days->prices as $rtype) { 
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
					<h3>
						<input type="<?php if ($this->subject->display_only_one_rtype) { ?>radio<?php } else { ?>checkbox<?php } ?>" value="<?php echo $rtype->id; ?>" name="rids[]" id="rids<?php echo $rtype->id; ?>"<?php if (in_array($rtype->id, $this->lists['rids'])) { ?> checked="checked"<?php } ?> onchange="Calendars.requestNavigation()" />
						<label for="rids<?php echo $rtype->id; ?>" class="hasTip" title="<?php echo $this->escape($rtype->title) . '::' . $this->escape($rtype->description); ?>"><?php echo $this->escape($title); ?></label>
					</h3>
		<?php 
						$volDisCount = array(0); // search maximum number of volume discount in some price
						foreach ($rtype->prices as $price)
							$volDisCount[] = count($price->volume_discount);
						$volDisMax = max($volDisCount);
						foreach ($rtype->prices as $priceIndex => $price) {
							/* @var $price TablePrice */
							$discounts = array();
                            $fixMultipling = $rtype->fix > 0 ? $rtype->fix : 1;
							if ($isDaily){
								if ($price->deposit)
									$title = JText::sprintf('PRICE_INFO_DAILY_FULL', $rtype->title, $price->formatValue, $price->formatDeposit);
								else  	
									$title = JText::sprintf('PRICE_INFO_DAILY', $rtype->title, $price->formatValue);
								if (empty($this->subject->volume_discount))
									foreach ($price->volume_discount as $voldis) {
										if ($voldis['type'] == DISCOUNT_TYPE_PERCENT)
											$discounts[] = JText::sprintf('COUNT_DAYS_DISCOUNT_PERCENT', $voldis['count'] * $fixMultipling, $voldis['value']);
										else
											$discounts[] = JText::sprintf('COUNT_DAYS_DISCOUNT', $voldis['count'] * $fixMultipling, BookingHelper::displayPrice($voldis['value'], null, $this->subject->tax));
									}
							} else {
								if ($price->deposit)
									$title = JText::sprintf('PRICE_INFO_HOURLY_FULL', $rtype->title, $rtype->time_unit_orig, $price->formatValue, $price->formatDeposit);
								else  	
									$title = JText::sprintf('PRICE_INFO_HOURLY', $rtype->title, $rtype->time_unit_orig, $price->formatValue);
								if (empty($this->subject->volume_discount))
									foreach ($price->volume_discount as $voldis) {
										if ($voldis['type'] == DISCOUNT_TYPE_PERCENT)
											$discounts[] = JText::sprintf('COUNT_HOURS_DISCOUNT_PERCENT', $voldis['count'] * $fixMultipling, $voldis['value']);
										else
											$discounts[] = JText::sprintf('COUNT_HOURS_DISCOUNT', $voldis['count'] * $fixMultipling, BookingHelper::displayPrice($voldis['value'], null, $this->subject->tax));
									}
							}
							
							$title .= ' '.JText::_('PAYMENT_EXPIRE').': '.BookingHelper::formatExpiration($price->cancel_time,null);
							if ($price->value) {
		?>
								<div class="hasTip price price<?php echo $priceIndex; ?>" title="<?php echo $this->escape($price->formatPrice) . '::' . $this->escape($title); ?>">
									<?php echo $price->formatPrice;
										if (count($discounts)) { ?>
											<strong><?php echo JText::_('VOLUME_DISCOUNTS'); ?></strong>
											<?php for ($i = count($discounts); $i < $volDisMax; $i++) // we want the same number of rows in each price
												$discounts[] = '&nbsp;'; // add empty row if missing
											echo implode('<br/>', $discounts);
										} ?>
								</div>
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
} elseif ($config->priceLayout == 'brief_legend' || $config->priceLayout == 'extended_legend') {
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
						<span class="price price<?php echo $pi; ?>">
							<?php if ($config->priceLayout == 'extended_legend')
							    echo $rtype->title . ' ';
						    echo $price->formatPrice; ?>
						</span>
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
} elseif($config->priceLayout == 'table_list') { ?>
    <h2><?php echo JText::_('PRICES'); ?></h2>
    <div class="tableList">
        <?php foreach ($this->days->prices as $rtype) { ?>
            <?php foreach ($rtype->prices as $priceIndex => $price) { ?>
                <strong class="price price<?php echo $priceIndex; ?>">
                    <?php echo JString::trim($rtype->description) ? $rtype->description : $rtype->title; ?>
                    <span class="text">
                    <?php if (!empty($price->occupacyPrices)) {
                                foreach ($price->occupacyPrices as $occ) { ?>                         
                                    <?php if (!empty($occ->value)) { ?>
                                        <span class="value">
                                            <?php echo BookingHelper::displayPrice($occ->value); ?>
                                        </span>
                                    <?php echo JText::_('PER') . ' ' . $occ->title; ?>
                                        <br/>                        
                             <?php      }
                                }
                           } elseif (!empty($price->value)) { ?>
                                <span class="value">
                                    <?php echo BookingHelper::displayPrice($price->value); ?>
                                </span>
                    <?php } ?>
                    </span>
                </strong>          
        <?php }
        } ?>
    </div>
<?php } else { // hidden list
	if (count($this->days->prices)) { 
		$rids = $this->lists['rids'];
		$rids[] = 0;
		foreach ($this->days->prices as $rtype) $rids[] = $rtype->id;		
		foreach (array_unique($rids) as $rid) {
?>		
			<input type="hidden" value="<?php echo $rid; ?>" name="rids[]" id="rids<?php echo $rid; ?>" />
<?php		
		}
	}
}
?>