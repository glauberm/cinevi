<?php

/**
 * Subject-prices edit form template
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

$config = &AFactory::getConfig();
/* @var $config BookingConfig */

$rtypes = $this->rtypes;
unset($rtypes[0]);

$checkThisDayInAllPrices = $this->escape(JText::_('CHECK_THIS_DAY_IN_ALL_PRICES'));
$checkAllDaysInThisPrice = $this->escape(JText::_('CHECK_ALL_DAYS_IN_THIS_PRICE'));
$checkAllDaysInAllPrices = $this->escape(JText::_('CHECK_ALL_DAYS_IN_ALL_PRICES'));

$weeks[] = JHtml::_('select.option', WEEK_EVERY, JText::_('EVERY_WEEKS'));
$weeks[] = JHtml::_('select.option', WEEK_EVEN, JText::_('ONLY_EVEN_WEEKS'));
$weeks[] = JHtml::_('select.option', WEEK_ODD, JText::_('ONLY_ODD_WEEKS'));

$deposittype[] = JHtml::_('select.option', DEPOSIT_TYPE_VALUE, $config->mainCurrency);
$deposittype[] = JHtml::_('select.option', DEPOSIT_TYPE_PERCENT, '%');

$voldistype[] = JHtml::_('select.option', DISCOUNT_TYPE_VALUE, $config->mainCurrency);
$voldistype[] = JHtml::_('select.option', DISCOUNT_TYPE_PERCENT, '%');

$voldisper[] = JHtml::_('select.option', DISCOUNT_PER_UNIT, JText::_('PEAR_RESERVATION_UNIT'));
$voldisper[] = JHtml::_('select.option', DISCOUNT_PER_RESERVATION, JText::_('PEAR_WHOLE_RESERVATION'));

$canceltimesettings[] = JHtml::_('select.option', CANCEL_NONE, JText::_('DISABLED'));
$canceltimesettings[] = JHtml::_('select.option', CANCEL_IMMEDIATELY, JText::_('ONLINE_PAYMENT_ONLY'));
$canceltimesettings[] = JHtml::_('select.option', CANCEL_AFTER, JText::_('AFTER_RESERVATION'));
$canceltimesettings[] = JHtml::_('select.option', CANCEL_BEFORE, JText::_('BEFORE_BOOKING'));

$canceltime[] = JHtml::_('select.option', EXPIRE_FORMAT_HOUR, JText::_('HOURS'));
$canceltime[] = JHtml::_('select.option', EXPIRE_FORMAT_DAY, JText::_('DAYS'));

JHtml::script('administrator/components/com_booking/assets/colorpicker/jscolor.js');

?>	
<div class="width-100" id="fprices">
	<input type="hidden" name="<?php echo PRICES_PREFIX . 'date_up[]'; ?>" value="" />
	<input type="hidden" name="<?php echo PRICES_PREFIX . 'date_down[]'; ?>" value="" />
	<input type="hidden" name="<?php echo PRICES_PREFIX . 'time_up[]'; ?>" value="" />
	<input type="hidden" name="<?php echo PRICES_PREFIX . 'time_down[]'; ?>" value="" />
	<fieldset class="adminform">
    	<legend class="hasTip" title="<?php echo $this->escape(JText::_('PRICES')) . '::' . $this->escape(JText::_('PRICES_TOP_INFO')); ?>">
    		<?php echo JText::_('PRICES'); ?>
    	</legend>
    	<?php if (count($this->rtypes) > 1) { ?>
	    	<div>
	    		<?php
	    			$bar = &JToolBar::getInstance('prices');
        			$bar->appendButton('ALink', 'new', 'Add', 'EditSubject.addPrice()');
        			$bar->appendButton('ALink', 'delete', 'Delete', 'EditSubject.removePrices()');
        			echo $bar->render();
	    		?>
				
				<div class="clr"></div>
    			<table class="template">
					<tr>
						<td colspan="2">
							<label class="inline hasTip" for="book_over_timeliness" title="<?php echo $this->escape(($title = JText::_('BOOK_OVER_TIMELINESS'))) . '::' . $this->escape(JText::_('BOOK_OVER_TIMELINESS_INFO')); ?>">
    							<input type="checkbox" class="inputCheckbox" name="book_over_timeliness" id="book_over_timeliness" value="1" <?php if ($this->subject->book_over_timeliness == 1) { ?>checked="checked"<?php } ?> />
    							<span class="checkboxLabel"><?php echo $title; ?></span>
    						</label>
    					</td>
    				</tr>
    				<?php if ($config->usingPrices == PRICES_WITH_DEPOSIT) { ?>
	    				<tr>
	    					<td>
	    						<label class="hasTip" for="single_deposit" title="<?php echo $this->escape(($title = JText::_('SINGLE_DEPOSIT'))) . '::' . $this->escape(JText::_('SINGLE_DEPOSIT_INFO')); ?>"><?php echo $title; ?></label>
	    					</td>
	    					<td>
	    						<input class="number" type="text" name="single_deposit" id="single_deposit" value="<?php echo $this->escape($this->subject->single_deposit); ?>" onkeyup="ACommon.toFloat(this)" style="margin: 0px 0px 0px 5px;" />
	 							<?php echo JHtml::_('select.genericlist', $deposittype, 'single_deposit_type', 'style="margin: 0"', 'value', 'text', $this->subject->single_deposit_type); ?>
	 							<div class="clr" style="padding: 5px"></div>   						
	    						<input type="hidden" name="single_deposit_include_supplements" value="<?php echo $this->subject->single_deposit_include_supplements; ?>" />
	    						<label class="inline hasTip" title="<?php echo $this->escape(JText::_('INCLUDE_SUPPLEMENTS')); ?>::<?php echo $this->escape(JText::_('INCLUDE_SUPPLEMENTS_INFO')); ?>">
									<input type="checkbox" class="inputCheckbox" id="single_deposit_include_supplements" name="another_fake[]" <?php if ($this->subject->single_deposit_include_supplements) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" style="margin: 4px 0px 0px;" />
									<span class="checkboxLabel"><?php echo JText::_('INCLUDE_SUPPLEMENTS'); ?></span>
								</label>
	    					</td>
	    				</tr>
    				<?php } 
    				if ($config->usingPrices) { ?>
	    				<tr>
	    					<td>
	    						<label class="hasTip" for="tax_rate_id" title="<?php echo $this->escape(($title = JText::_('TAX_RATE'))) . '::' . $this->escape(JText::_('TAX_INFO')); ?>"><?php echo $title; ?></label>
	    					</td>
	    					<td>
	    						<?php
	    							$options = array(JHtml::_('select.option', '-1', JText::_('NO_TAX'))); 
	    							foreach ($config->taxRates as $id => $taxrate)
										$options[] = JHtml::_('select.option', $id, $taxrate[0]);
	    							echo JHtml::_('select.genericlist', $options, 'tax_rate_id', '', 'value', 'text', $this->subject->tax_rate_id);
	    						?>
	    					</td>
	    				</tr>
    				<?php } ?>
    			</table>
    			<div class="clr"></div>
				<table class="template">
					<thead>
						<tr>
							<th rowspan="2" valign="top">&nbsp;</th>
							<?php if ($config->usingPrices) { ?>
								<th rowspan="2" valign="top" width="50"><h3><?php echo JText::_('VALUE'); ?><span class="star"> *</span></h3></th>
							<?php }
							if ($config->usingPrices == PRICES_WITH_DEPOSIT) { ?>
								<th rowspan="2" align="center" valign="top"><h3><?php echo JText::_('DEPOSIT'); ?></h3></th>
							<?php } 
							if ($config->usingPrices) { ?>
								<th rowspan="2" align="center" valign="top" class="hasTip" title="<?php echo $this->escape(JText::_('TIME_DISCOUNTS')); ?>::<?php echo $this->escape(JText::_('TIME_DISCOUNTS_INFO')); ?>"><h3><?php echo JText::_('TIME_DISCOUNTS'); ?></h3></th>
								<?php if ($this->subject->show_occupancy) { ?>
									<th rowspan="2" valign="top" class="hasTip" title="<?php echo $this->escape(JText::_('OCCUPANCY_PRICE_MODIFIER_TIP_TITLE')); ?>::<?php echo $this->escape(JText::_('OCCUPANCY_PRICE_MODIFIER_TIP_TEXT')); ?>"><h3><?php echo JText::_('OCCUPANCY_PRICE_MODIFIER'); ?></h3></th>
								<?php }
								} ?>
							<th rowspan="2" valign="top"><h3><?php echo JText::_('RESERVATION_SETTINGS'); ?></h3></th>
							<th colspan="2" align="center" valign="top" width="450">
								<h3>
									<?php echo JText::_('DATE_RANGE'); ?>
									<br/>
									<?php echo JText::_('TIME_RANGE_ONLY_FOR_HOURLY_TYPE'); ?>
								</h3>
							</th>
						</tr>
						<tr>
							<th valign="top"><h3><?php echo JText::_('FROM'); ?><span class="star"> *</span></h3></th>
							<th valign="top"><h3><?php echo JText::_('TO'); ?><span class="star"> *</span></h3></th>
						</tr>
					</thead>
					<tbody id="tprices">
						<?php 
							$pcount = count($this->prices);					
							for ($i = 0; $i < $pcount; $i++) {
								$price = &$this->prices[$i];
								TablePrice::display($price);
								/* @var $price TablePrice */
								JFilterOutput::objectHTMLSafe($price);
								$id = $i ? $price->id : '';
							?>
							<tr <?php if (! $id) { ?>id="price1" style="display: none"<?php } ?> class="row<?php echo $i % 2; ?>">
								<td class="check" rowspan="2">
									<input type="checkbox" class="inputCheckbox" name="pcid[]" value="1"/>
								</td>
								<?php if ($config->usingPrices) { ?>
								<td rowspan="2" class="basic-price" valign="top">
									<label class="inline"> 
										<input type="text" name="<?php echo PRICES_PREFIX; ?>value[<?php echo $id; ?>]" value="<?php echo $price->value; ?>" class="number" onkeyup="ACommon.toFloat(this)" />
										<span class="priceLabel"><?php echo $config->mainCurrency;?></span>
									</label>
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>price_capacity_multiply[<?php echo $id; ?>]" value="<?php echo $price->price_capacity_multiply; ?>" />
									<label class="inline hasTip" title="<?php echo $this->escape(JText::_('MULTIPLY_CAPACITY')); ?>::<?php echo $this->escape(JText::_('PRICE_MULTIPLY_CAPACITY_INFO')); ?>">
										<input type="checkbox" class="inputCheckbox" id="<?php echo 'price_capacity_multiply' . $id; ?>" name="another_fake[]" <?php if ($price->price_capacity_multiply) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" /> 			
										<span class="checkboxLabel"><?php echo JText::_('MULTIPLY_CAPACITY'); ?></span>
									</label>									
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>price_standard_occupancy_multiply[<?php echo $id; ?>]" value="<?php echo $price->price_standard_occupancy_multiply; ?>" />
									<label class="inline hasTip" title="<?php echo $this->escape(JText::_('MULTIPLY_STANDARD_OCCUPANCY_TIP')); ?>">
										<input type="checkbox" class="inputCheckbox" id="<?php echo 'price_standard_occupancy_multiply' . $id; ?>" name="another_fake[]" <?php if ($price->price_standard_occupancy_multiply) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" /> 			
										<span class="checkboxLabel"><?php echo JText::_('MULTIPLY_STANDARD_OCCUPANCY'); ?></span>
									</label>		
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>price_extra_occupancy_multiply[<?php echo $id; ?>]" value="<?php echo $price->price_extra_occupancy_multiply; ?>" />
									<label class="inline hasTip" title="<?php echo $this->escape(JText::_('MULTIPLY_EXTRA_OCCUPANCY_TIP')); ?>">
										<input type="checkbox" class="inputCheckbox" id="<?php echo 'price_extra_occupancy_multiply' . $id; ?>" name="another_fake[]" <?php if ($price->price_extra_occupancy_multiply) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" /> 			
										<span class="checkboxLabel"><?php echo JText::_('MULTIPLY_EXTRA_OCCUPANCY'); ?></span>
									</label>									
								</td>
								<?php } 
								if ($config->usingPrices == PRICES_WITH_DEPOSIT) { ?>
								<td rowspan="2" valign="top" class="deposit-settings">
									<label class="inline">
										<input type="text" name="<?php echo PRICES_PREFIX; ?>deposit[<?php echo $id; ?>]" value="<?php echo $price->deposit; ?>" class="number" onkeyup="ACommon.toFloat(this)" id="deposit<?php echo $id; ?>" />
										<?php echo JHTML::_('select.genericlist', $deposittype, PRICES_PREFIX . 'deposit_type[' . $id . ']', '', 'value', 'text', $price->deposit_type); ?>
									</label>
									<div class="clr"></div>
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>deposit_multiply[<?php echo $id; ?>]" value="<?php echo $price->deposit_multiply; ?>" />
									<label class="inline hasTip" title="<?php echo JText::_('DEPOSIT_MULTIPLY'); ?>::<?php echo JText::_('DEPOSIT_MULTIPLY_INFO'); ?>">
										<input type="checkbox" class="inputCheckbox" name="another_fake[]" <?php if ($price->deposit_multiply) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" />
										<span class="checkboxLabel"><?php echo JText::_('DEPOSIT_MULTIPLY'); ?></span>
									</label>
									<div class="clr"></div>
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>deposit_capacity_multiply[<?php echo $id; ?>]" value="<?php echo $price->deposit_capacity_multiply; ?>" />
									<label class="inline hasTip" title="<?php echo $this->escape(JText::_('MULTIPLY_CAPACITY')); ?>::<?php echo $this->escape(JText::_('DEPOSIT_MULTIPLY_CAPACITY_INFO')); ?>">
										<input type="checkbox" class="inputCheckbox" id="<?php echo 'deposit_capacity_multiply' . $id; ?>" name="another_fake[]" <?php if ($price->deposit_capacity_multiply) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" /> 			
										<span class="checkboxLabel"><?php echo JText::_('MULTIPLY_CAPACITY'); ?></span>
									</label>
									<div class="clr"></div>
									
									
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>deposit_standard_occupancy_multiply[<?php echo $id; ?>]" value="<?php echo $price->deposit_standard_occupancy_multiply; ?>" />
									<label class="inline hasTip" title="<?php echo $this->escape(JText::_('MULTIPLY_STANDARD_OCCUPANCY_TIP')); ?>">									
										<input type="checkbox" class="inputCheckbox" id="<?php echo 'deposit_standard_occupancy_multiply' . $id; ?>" name="another_fake[]" <?php if ($price->deposit_standard_occupancy_multiply) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" /> 			
										<span class="checkboxLabel"><?php echo JText::_('MULTIPLY_STANDARD_OCCUPANCY'); ?></span>
									</label>
									<div class="clr"></div>									
									
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>deposit_extra_occupancy_multiply[<?php echo $id; ?>]" value="<?php echo $price->deposit_extra_occupancy_multiply; ?>" />
									<label class="inline hasTip" title="<?php echo $this->escape(JText::_('MULTIPLY_EXTRA_OCCUPANCY_TIP')); ?>">
										<input type="checkbox" class="inputCheckbox" id="<?php echo 'deposit_extra_occupancy_multiply' . $id; ?>" name="another_fake[]" <?php if ($price->deposit_extra_occupancy_multiply) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" /> 			
										<span class="checkboxLabel"><?php echo JText::_('MULTIPLY_EXTRA_OCCUPANCY'); ?></span>
									</label>
									<div class="clr"></div>
									
									
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>deposit_include_supplements[<?php echo $id; ?>]" value="<?php echo $price->deposit_include_supplements; ?>" />
									<label class="inline hasTip" title="<?php echo $this->escape(JText::_('INCLUDE_SUPPLEMENTS')); ?>::<?php echo $this->escape(JText::_('INCLUDE_SUPPLEMENTS_INFO')); ?>">
										<input type="checkbox" class="inputCheckbox" id="<?php echo 'deposit_include_supplements' . $id; ?>" name="another_fake[]" <?php if ($price->deposit_include_supplements) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" /> 			
										<span class="checkboxLabel"><?php echo JText::_('INCLUDE_SUPPLEMENTS'); ?></span>
									</label>
								</td>
								<?php }
								if ($config->usingPrices) { ?>
									<td rowspan="2" class="discountContainer">
										<table>
											<thead>
												<tr>
													<th class="hasTip" title="<?php echo $this->escape(JText::_('VOLUME')); ?>::<?php echo $this->escape(JText::_('VOLUME_TIP')); ?>"><?php echo JText::_('VOLUME'); ?></th>
													<th colspan="2" class="hasTip" title="<?php echo $this->escape(JText::_('DISCOUNT')); ?>::<?php echo $this->escape(JText::_('DISCOUNT_TIP')); ?>"><?php echo JText::_('DISCOUNT'); ?></th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<?php 
												array_unshift($price->volume_discount, array('count' => '', 'value' => '', 'type' => 0));
												foreach ($price->volume_discount as $vdi => $volDis) { ?>
													<tr<?php if (!$vdi) { ?> style="display: none" class="disrow"<?php } ?>>
														<td>
															<input type="text" name="dis_count[]" value="<?php echo JArrayHelper::getValue($volDis, 'count'); ?>" class="number" onkeyup="ACommon.toInt(this)" />
														</td>
														<td>
															<input type="text" name="dis_value[]" value="<?php echo JArrayHelper::getValue($volDis, 'value'); ?>" class="number" onkeyup="ACommon.toFloat(this, true)" />
														</td>
														<td>
															<?php echo JHTML::_('select.genericlist', $voldistype, 'dis_type[]', 'class="inline"', 'value', 'text', JArrayHelper::getValue($volDis, 'type')); ?>
														</td>
														<td rowspan="2">
															<button onclick="return EditSubject.removeDiscount(this, true)" class="transparent" title="<?php echo $this->escape(JText::_('REMOVE_DISCOUNT')); ?>">
																<?php echo JHtml::_('image', 'admin/publish_r.png', JText::_('REMOVE_DISCOUNT'), null, true); ?>
															</button>		
														</td>
													</tr>
													<tr<?php if (!$vdi) { ?> style="display: none" class="disrow"<?php } ?>>
														<td colspan="3">
															<?php echo JHTML::_('select.genericlist', $voldisper, 'dis_per[]', 'class="inline"', 'value', 'text',JArrayHelper::getValue($volDis, 'per')); ?>
														</td>
													</tr>
												<?php } ?>
												<?php if (count($price->volume_discount) == 1) { ?>
													<tr class="nonediscount"><td colspan="4"><?php echo JText::_('NONE_DISCOUNT'); ?></td></tr>
												<?php } ?>
											</tbody>
										</table>
										<button onclick="return EditSubject.addDiscount(this)" class="transparent" title="<?php echo $this->escape(JText::_('ADD_DISCOUNT')); ?>">
											<?php echo JHtml::_('image', 'admin/expandall.png', JText::_('ADD_DISCOUNT'), null, true); ?>
											<span><?php echo JText::_('ADD_DISCOUNT'); ?></span>
										</button>
										<input type="hidden" name="<?php echo PRICES_PREFIX; ?>volume_discount[<?php echo $id; ?>]" value="" />
									</td>
									<?php if ($this->subject->show_occupancy) { ?>
										<td rowspan="2">
											<?php foreach ($this->otypes as $otype) { ?>
												<label class="input">
													<span><?php echo $otype->title; ?></span>
													<input type="text" name="<?php echo PRICES_PREFIX; ?>occupancy_price_modifier[<?php echo $id; ?>][<?php echo $otype->id; ?>]" class="number" onkeyup="ACommon.toFloat(this, true)" value="<?php echo JArrayHelper::getValue($price->occupancy_price_modifier, $otype->id); ?>" /> 	
												</label>
											<?php } ?>
										</td>
									<?php }
									} ?>
								<td class="select" rowspan="2">
									<label>
										<span class="blockLabel"><?php echo JText::_('RESERVATION_TYPE'); ?></span>
										<?php echo AHtml::getFilterSelect(PRICES_PREFIX . 'rezervation_type[' . $id . ']', 'select', $rtypes, $price->rezervation_type, false, 'onchange="EditSubject.setPriceReservationType(this)"', 'id', 'fullTitle'); ?>
									</label>
									<label>
										<span class="blockLabel hasTip" title="<?php echo $this->escape(JText::_('RESERVATION_EXPIRATION')); ?>::<?php echo $this->escape(JText::_('RESERVATION_EXPIRATION_INFO')); ?>"><?php echo JText::_('RESERVATION_EXPIRATION'); ?></span>
										<?php echo JHTML::_('select.genericlist', $canceltimesettings, PRICES_PREFIX . 'expiration_setting[' . $id . ']', 'onchange="EditSubject.setPaymentExpirationType(this)"', 'value', 'text', BookingHelper::typeOfCancelTime($price->cancel_time)); ?>
										<br/>
										<span class="blockLabel"><?php echo JText::_('CANCEL_TIME'); ?></span>
										<input type="text" name="<?php echo PRICES_PREFIX; ?>cancel_time[<?php echo $id; ?>]" value="<?php echo BookingHelper::formatFromCancelTime($price->cancel_time); ?>" class="number" onkeyup="ACommon.toFloat(this)"/>
										<?php echo JHTML::_('select.genericlist', $canceltime, PRICES_PREFIX . 'expiration_format[' . $id . ']', '', 'value', 'text', BookingHelper::formatOfCancelTime($price->cancel_time)); ?>
									</label>
									<label>
										<span class="blockLabel"><?php echo JText::_('WEEK_AVAILABILITY'); ?></span>
										<?php echo JHTML::_('select.genericlist', $weeks, PRICES_PREFIX . 'week[' . $id . ']', '', 'value', 'text', $price->week); ?>
									</label>
									<label title="<?php echo JText::_('COLOR_OF_PRICE_FIELD_IN_CALENDAR'); ?>">
										<span class="blockLabel"><?php echo JText::_('COLOR_IN_CALENDAR'); ?></span>
										<input type="text" name="<?php echo PRICES_PREFIX; ?>custom_color[<?php echo $id; ?>]" value="<?php echo $price->custom_color; ?>" class="color {pickerPosition:'top',required:false}" size="7" />
									</label>
								</td>	
								<td class="date">
									<div>
										<?php if ($i)
											echo AHtml::getCalendar($price->date_up, PRICES_PREFIX . 'date_up[' . $id . ']', 'priceDateUp' . $price->id, ADATE_FORMAT_NORMAL, ADATE_FORMAT_NORMAL_CAL, '', false, 0); ?>
										<div class="clr"></div>
									</div>
									<div style="padding: 10px 0px 0px 0px">
										<?php if ($i)
											echo AHtml::getTimePicker($price->time_up, PRICES_PREFIX . 'time_up[' . $id . ']', false, '', true); ?>
										<div class="clr"></div>
									</div>
								</td>
								<td class="date">
									<div>
										<?php if ($i)
											echo AHtml::getCalendar($price->date_down, PRICES_PREFIX . 'date_down[' . $id . ']', 'priceDateDown' . $price->id, ADATE_FORMAT_NORMAL, ADATE_FORMAT_NORMAL_CAL, '', false, 0); ?>
										<div class="clr"></div>
									</div>
									<div style="padding: 10px 0px 0px 0px">
										<?php if ($i)
											echo AHtml::getTimePicker($price->time_down, PRICES_PREFIX . 'time_down[' . $id . ']', false, '', true); ?>
										<div class="clr"></div>
									</div>
								</td>								
							</tr>
							<tr <?php if (! $id) { ?>id="price2" style="display: none"<?php } ?> class="row<?php echo $i % 2; ?>">
								<td colspan="2">
									<select name="<?php echo PRICES_PREFIX; ?>time_range[<?php echo $id; ?>]" id="<?php echo PRICES_PREFIX; ?>time_range<?php echo $id; ?>">
										<option class="hasTip" value="<?php echo TIME_RANGE_ONE_DAY; ?>" title="<?php echo $this->escape(JText::_('IN_ONE_DAY')); ?>::<?php echo $this->escape(JText::_('In_one_Day_info')); ?>" <?php if ($price->time_range == TIME_RANGE_ONE_DAY) { ?>selected="selected"<?php } ?>><?php echo JText::_('IN_ONE_DAY'); ?></option>
										<option class="hasTip" value="<?php echo TIME_RANGE_OVER_MIDNIGHT; ?>" title="<?php echo $this->escape(JText::_('OVER_MIDNIGHT')); ?>::<?php echo $this->escape(JText::_('OVER_MIDNIGHT_INFO')); ?>" <?php if ($price->time_range == TIME_RANGE_OVER_MIDNIGHT) { ?>selected="selected"<?php } ?>><?php echo JText::_('OVER_MIDNIGHT'); ?></option>
										<option class="hasTip" value="<?php echo TIME_RANGE_OVER_WEEK; ?>" title="<?php echo $this->escape(JText::_('OVER_THE_WEEK')); ?>::<?php echo $this->escape(JText::_('OVER_THE_WEEK_INFO')); ?>" <?php if ($price->time_range == TIME_RANGE_OVER_WEEK) { ?>selected="selected"<?php } ?>><?php echo JText::_('OVER_THE_WEEK'); ?></option>
									</select>
									
									<label class="inline hasTip" title="<?php echo $this->escape(JText::_('Head_Piece')); ?>::<?php echo $this->escape(JText::_('Head_Piece_info')); ?>">
										<span class="inputLabel"><?php echo JText::_('Head_Piece'); ?></span>
										<input type="text" name="<?php echo PRICES_PREFIX; ?>head_piece[<?php echo $id; ?>]" value="<?php echo $price->head_piece; ?>" class="number" onkeyup="ACommon.toInt(this)" />
									</label>
									<label class="inline hasTip" title="<?php echo $this->escape(JText::_('Tail_Piece')); ?>::<?php echo $this->escape(JText::_('Tail_Piece_info')); ?>">
										<span class="inputLabel"><?php echo JText::_('Tail_Piece'); ?></span>
										<input type="text" name="<?php echo PRICES_PREFIX; ?>tail_piece[<?php echo $id; ?>]" value="<?php echo $price->tail_piece; ?>" class="number" onkeyup="ACommon.toInt(this)" />
									</label>
									<div class="clr" style="padding: 5px"></div>
									<h3 style="float: left; margin: 0px; padding: 1px 10px 0px 0px;"><?php echo JText::_('DAYS_AVAILABILITY'); ?>:</h3>
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>monday[<?php echo $id; ?>]" value="<?php echo $price->monday; ?>" />
									<label class="inline">
										<input type="checkbox" class="inputCheckbox" name="fake[]" <?php if ($price->monday) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" />
										<span class="checkboxDayLabel"><?php echo JText::_('MON'); ?></span>
									</label> 
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>tuesday[<?php echo $id; ?>]" value="<?php echo $price->tuesday; ?>" />
									<label class="inline">
										<input type="checkbox" class="inputCheckbox" name="fake[]" <?php if ($price->tuesday) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" />
										<span class="checkboxDayLabel"><?php echo JText::_('Tue'); ?></span>
									</label>
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>wednesday[<?php echo $id; ?>]" value="<?php echo $price->wednesday; ?>" />
									<label class="inline">
										<input type="checkbox" class="inputCheckbox" name="fake[]" <?php if ($price->wednesday) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" />
										<span class="checkboxDayLabel"><?php echo JText::_('Wed'); ?></span>
									</label>
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>thursday[<?php echo $id; ?>]" value="<?php echo $price->thursday; ?>" />
									<label class="inline">
										<input type="checkbox" class="inputCheckbox" name="fake[]" <?php if ($price->thursday) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" />
										<span class="checkboxDayLabel"><?php echo JText::_('Thu'); ?></span>
									</label>
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>friday[<?php echo $id; ?>]" value="<?php echo $price->friday; ?>" />
									<label class="inline">
										<input type="checkbox" class="inputCheckbox" name="fake[]" <?php if ($price->friday) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" />
										<span class="checkboxDayLabel"><?php echo JText::_('Fri'); ?></span>
									</label>
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>saturday[<?php echo $id; ?>]" value="<?php echo $price->saturday; ?>" />
									<label class="inline">
										<input type="checkbox" class="inputCheckbox" name="fake[]" <?php if ($price->saturday) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" />
										<span class="checkboxDayLabel"><?php echo JText::_('Sat'); ?></span>
									</label>
									<input type="hidden" name="<?php echo PRICES_PREFIX; ?>sunday[<?php echo $id; ?>]" value="<?php echo $price->sunday; ?>" />
									<label class="inline">
										<input type="checkbox" class="inputCheckbox" name="fake[]" <?php if ($price->sunday) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" />
										<span class="checkboxDayLabel"><?php echo JText::_('Sun'); ?></span>
									</label>
								</td>		
							</tr>
						<?php } ?>
						<tr id="price-empty" <?php if ($pcount > 1) { ?>style="display: none;"<?php } ?>><td colspan="16" class="emptyList"><?php echo JText::_('EMPTY_PRICES_LIST'); ?></td></tr>
					</tbody>
				</table>
				<div class="clr"></div>
			</div>
		<?php } else { ?>
			<p class="emptyParentList"><?php echo JText::_('ADD_RESERVATION_TYPES_AND_APPLY'); ?></p>
		<?php } ?>
    </fieldset>
</div>

<script type="text/javascript">
//<![CDATA[										
window.addEvent('domready', function(){ //make sure dom elements are loaded first
    $$('select[name^=price-expiration_setting]').each(function(item){EditSubject.setPaymentExpirationType(item);});
});
//]]>
</script>
