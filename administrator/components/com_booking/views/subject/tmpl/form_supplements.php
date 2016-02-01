<?php

/**
 * Subject-supplements edit form template
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

$options[] = JHTML::_('select.option', SUPPLEMENT_TYPE_UNSELECT, JText::_('SELECT_LIST'));
$options[] = JHTML::_('select.option', SUPPLEMENT_TYPE_LIST, JText::_('LIST'));
$options[] = JHTML::_('select.option', SUPPLEMENT_TYPE_YESNO, JText::_('YESNO'));
$options[] = JHTML::_('select.option', SUPPLEMENT_TYPE_MANDATORY, JText::_('MANDATORY_SUPPLEMENT'));

$paids[] = JHTML::_('select.option', SUPPLEMENT_ONE_PRICE, JText::_('SUPPLEMENT_ONE_PRICE'));
$paids[] = JHTML::_('select.option', SUPPLEMENT_MORE_PRICES, JText::_('SUPPLEMENT_MORE_PRICES'));
$paids[] = JHTML::_('select.option', SUPPLEMENT_NO_PRICE, JText::_('SUPPLEMENT_NO_PRICE'));

$type[] = JHtml::_('select.option', DISCOUNT_TYPE_VALUE, $config->mainCurrency);
$type[] = JHtml::_('select.option', DISCOUNT_TYPE_PERCENT, '%');

?>
<div class="width-100">
	<fieldset class="adminform">
	    <legend>
	   		<span class="legend"><?php echo JText::_('SUPPLEMENTS'); ?></span>
	   	</legend>
	    <?php if (count($this->supplements)) { ?>
		    <div class="col">
		    	<?php
	    			$bar = &JToolBar::getInstance('toolsupplements');
	        		$bar->appendButton('ALink', 'new', 'Add', 'EditSubject.addSupplement()');
	        		$bar->appendButton('ALink', 'delete', 'Delete', 'EditSubject.removeSupplements()');
	        		echo $bar->render();
		    		/*
	        		echo '<div class="btn-group pull-left">
								<button type="button" class="btn" title="' . JText::_('ADD') . '" onclick="EditSubject.addSupplement(); return false;"><span class="icon-new"></span>New</button>
								<button type="button" class="btn" title="' . JText::_('DELETE') . '" onclick="EditSubject.removeSupplements(); return false;"><span class="icon-delete"></span>Delete</button>
							</div>';
					*/
	    		?>
	    		<div class="clr"></div>
				<table class="template" id="supplements">
					<thead>
						<tr>
							<th valign="top">&nbsp;</th>
							<th valign="top"><h3><?php echo JText::_('ID'); ?></h3></th>
							<th valign="top">
								<h3><?php echo JText::_('TITLE'); ?><span class="star"> *</span></h3>
							</th>
							<th valign="top"><h3><?php echo JText::_('TYPE'); ?><span class="star"> *</span></h3></th>
							<th valign="top"><h3><?php echo JText::_('PRICE'); ?><span class="star"> *</span></h3></th>
							<th valign="top"><h3><?php echo JText::_('MEMBER_DISCOUNT'); ?></h3></th>
						</tr>
					</thead>
					<tbody id="tsupplements">
					<?php 
						$scount = count($this->supplements);
						for ($i = 0; $i < $scount; $i++) {
							$supplement = &$this->supplements[$i];
							/* @var $supplement TableSupplement */
							TableSupplement::display($supplement);
							JFilterOutput::objectHTMLSafe($supplement);
							$id = $i ? $supplement->id : ''; 
						?>
							<tr <?php if (! $id) { echo 'id="supplement" style="display: none;"'; } ?>>
								<td class="check" valign="top">
									<input type="checkbox" class="inputCheckbox" name="scid[]" value="1"/>
								</td>
								<td class="id" valign="top">
									<?php echo $id ? $id : '<i>' . JText::_('NEW') . '</i>'; ?>
                                    <span class="drop-and-drag" title="<?php echo $this->escape(JText::_('DROP_AND_DRAG')); ?>"></span>
									<input type="hidden" name="<?php echo SUPPLEMENTS_PREFIX; ?>ordering[<?php echo $id; ?>]" value="<?php echo $supplement->ordering; ?>" />									
								</td>
								<td valign="top">
									<input type="text" name="<?php echo SUPPLEMENTS_PREFIX; ?>title[<?php echo $id; ?>]" value="<?php echo $supplement->title; ?>" class="title" />
									<label>
									    <?php echo JText::_('DESCRIPTION'); ?>
									    <br/>
										<textarea name="<?php echo SUPPLEMENTS_PREFIX; ?>description[<?php echo $id; ?>]" class="description" rows="1" cols="1"><?php echo $supplement->description; ?></textarea>
									</label>
								</td>
								<td valign="top">
									<?php echo JHTML::_('select.genericlist', $options, SUPPLEMENTS_PREFIX . 'type[' . $id . ']', 'onchange="EditSubject.setSupplementType(this)"', 'value', 'text', $supplement->type); ?>
									<div class="clr"></div>
									
									<input type="hidden" name="<?php echo SUPPLEMENTS_PREFIX; ?>empty[<?php echo $id; ?>]" value="<?php echo $supplement->empty; ?>" />
									<label class="inline" style="width: 210px;">
										<input type="checkbox" class="inputCheckbox" id="empty<?php echo $id; ?>" name="fake[]" <?php if ($supplement->empty == SUPPLEMENT_EMPTY_USE) { ?>checked="checked"<?php } if ($supplement->type != SUPPLEMENT_TYPE_LIST || ! $id) { ?>disabled="disabled"<?php } ?> onchange="ACommon.check(this);" />
										<span class="checkboxLabel"><?php echo JText::_('SUPPLEMENT_EMPTY_OPTION'); ?></span>
									</label>
									<label class="inline">
									     <?php echo JText::_('OPTIONS_SELECTBOX'); ?>
									   	 <br/>
										 <textarea name="<?php echo SUPPLEMENTS_PREFIX; ?>options[<?php echo $id; ?>]" class="description" rows="1" cols="15" <?php if ($supplement->type != SUPPLEMENT_TYPE_LIST) { ?>disabled="disabled"<?php } ?>><?php echo $supplement->options; ?></textarea>
									</label>
									<label class="inline" style="<?php if ($supplement->paid != SUPPLEMENT_MORE_PRICES) { ?>display: none;<?php } ?>">
                                        <?php echo JText::_('PRICES'); ?>
										<br/>
										<textarea name="<?php echo SUPPLEMENTS_PREFIX; ?>price[<?php echo $id; ?>]" class="price" rows="1" cols="10" <?php if ($supplement->paid != SUPPLEMENT_MORE_PRICES) { ?>style="display: none;"<?php } ?>><?php echo $supplement->price; ?></textarea>
									</label>
								</td>
								<td valign="top" nowrap="nowrap">
									<?php echo JHTML::_('select.genericlist', $paids, SUPPLEMENTS_PREFIX . 'paid[' . $id . ']', 'onchange="EditSubject.setSupplementPaid(this)"', 'value', 'text', $supplement->paid); ?>
									<label class="inline" <?php if ($supplement->paid != SUPPLEMENT_ONE_PRICE) { ?> style="display: none"<?php } ?>>
										<input type="text" class="number price" name="<?php echo SUPPLEMENTS_PREFIX; ?>price[<?php echo $id; ?>]" value="<?php echo $supplement->price; ?>"<?php if ($supplement->paid != SUPPLEMENT_ONE_PRICE) { ?> style="display: none"<?php } ?> onkeyup="ACommon.toFloat(this)" />
										<span class="priceLabel"><?php echo $config->mainCurrency; ?></span>
									</label>
									<div class="clr"></div>
									<label class="inline">
										<input type="hidden" name="<?php echo SUPPLEMENTS_PREFIX; ?>unit_multiply[<?php echo $id; ?>]" value="<?php echo $supplement->unit_multiply; ?>" />
										<input type="checkbox" class="inputCheckbox" id="unit_multiply<?php echo $id; ?>" name="fake[]" <?php if ($supplement->unit_multiply == SUPPLEMENT_UNIT_MULTIPLY) { ?>checked="checked"<?php } ?> onchange="ACommon.check(this);" />
										<span class="checkboxLabel"><?php echo JText::_('MULTIPLY_SUPPLEMENTS_PRICE_COUNT_UNITS'); ?></span>
									</label>
							
									<div class="clr"></div>
									<label class="inline hasTip" title="<?php echo $this->escape(JText::_('SUPPLEMENT_SURCHARGE_VALUE')); ?>::<?php echo $this->escape(JText::_('SUPPLEMENT_SURCHARGE_VALUE_TIP')); ?>">
										<span class="blockLabel"><?php echo JText::_('SUPPLEMENT_SURCHARGE_VALUE'); ?></span>
										<input type="text" name="<?php echo SUPPLEMENTS_PREFIX; ?>surcharge_value[<?php echo $id; ?>]" value="<?php echo $supplement->surcharge_value; ?>" id="surcharge_value<?php echo $id; ?>" class="number" onkeyup="ACommon.toFloat(this);" />
										<span class="priceLabel"><?php echo $config->mainCurrency; ?></span>
									</label>		
									<div class="clr"></div>
									<label class="inline">
										<span class="blockLabel"><?php echo JText::_('SUPPLEMENT_SURCHARGE_LABEL'); ?></span>
										<input type="text" name="<?php echo SUPPLEMENTS_PREFIX; ?>surcharge_label[<?php echo $id; ?>]" value="<?php echo $supplement->surcharge_label; ?>" id="surcharge_label<?php echo $id; ?>" />
									</label>
									
									<label><?php echo JText::_('CAPACITY'); ?><span class="star"> *</span></label>
									    <div class="clr"></div>
									    <label class="hasTip inline" title="<?php echo $this->escape(JText::_('SUPPLEMENT_NO_CAPACITY')); ?>::<?php echo $this->escape(JText::_('SUPPLEMENT_NO_CAPACITY_INFO')); ?>">
										<input type="radio" class="inputRadio" name="<?php echo SUPPLEMENTS_PREFIX; ?>capacity_multiply[<?php echo $id; ?>]" value="0" <?php if ($supplement->capacity_multiply == 0) { ?>checked="checked"<?php } ?> onclick="EditSubject.supplementManualCapacity(this)" />
										<span class="checkboxLabel"><?php echo JText::_('SUPPLEMENT_NO_CAPACITY'); ?></span>
									</label>
									<div class="clr"></div>
									<label class="hasTip inline" title="<?php echo $this->escape(JText::_('SUPPLEMENT_SUBJECT_CAPACITY')); ?>::<?php echo $this->escape(JText::_('SUPPLEMENT_SUBJECT_CAPACITY_INFO')); ?>">
										<input type="radio" class="inputRadio" name="<?php echo SUPPLEMENTS_PREFIX; ?>capacity_multiply[<?php echo $id; ?>]" value="1" <?php if ($supplement->capacity_multiply == 1) { ?>checked="checked"<?php } ?> onclick="EditSubject.supplementManualCapacity(this)" />
										<span class="checkboxLabel"><?php echo JText::_('SUPPLEMENT_SUBJECT_CAPACITY'); ?></span>
									</label>
									<div class="clr"></div>
									<label class="hasTip inline" title="<?php echo $this->escape(JText::_('SUPPLEMENT_MANUAL_CAPACITY_WITHMULTIPLY')); ?>::<?php echo $this->escape(JText::_('SUPPLEMENT_MANUAL_CAPACITY_WITHMULTIPLY_INFO')); ?>">
										<input type="radio" class="inputRadio" name="<?php echo SUPPLEMENTS_PREFIX; ?>capacity_multiply[<?php echo $id; ?>]" value="2" <?php if ($supplement->capacity_multiply == 2) { ?>checked="checked"<?php } ?> onclick="EditSubject.supplementManualCapacity(this)" />
										<span class="checkboxLabel"><?php echo JText::_('SUPPLEMENT_MANUAL_CAPACITY_WITHMULTIPLY'); ?></span>
									</label>
									<div class="clr"></div>
									<div class="supplementmanualcapacity" <?php if ($supplement->capacity_multiply != 2) { ?>style="display: none;"<?php } ?>>
										<label title="<?php echo $this->escape(JText::_('SUPPLEMENT_MIN_CAPACITY')); ?>::<?php echo $this->escape(JText::_('SUPPLEMENT_MIN_CAPACITY_INFO')); ?>" class="hasTip inline">
											<span class="inputLabel"><?php echo JText::_('SUPPLEMENT_MIN_CAPACITY'); ?></span> 
											<input class="number" type="text" name="<?php echo SUPPLEMENTS_PREFIX; ?>capacity_min[<?php echo $id; ?>]" value="<?php echo $supplement->capacity_min; ?>" onkeyup="ACommon.toInt(this);" />
										</label>
										<div class="clr"></div>		
										<label title="<?php echo $this->escape(JText::_('SUPPLEMENT_MAX_CAPACITY')); ?>::<?php echo $this->escape(JText::_('SUPPLEMENT_MAX_CAPACITY_INFO')); ?>" class="hasTip inline">
											<span class="inputLabel"><?php echo JText::_('SUPPLEMENT_MAX_CAPACITY'); ?></span> 
											<input class="number" type="text" name="<?php echo SUPPLEMENTS_PREFIX; ?>capacity_max[<?php echo $id; ?>]" value="<?php echo $supplement->capacity_max; ?>" onkeyup="ACommon.toInt(this);" />
										</label>
									</div>
								</td>
								<td valign="top" nowrap="nowrap">
									<table>
										<?php foreach ($this->get('usergroups') as $group) {
											$data = JArrayHelper::getValue($supplement->member_discount, $group->id, array(), 'array'); ?>
											<tr>
												<td><?php echo $group->title; ?></td>
												<td>
													<input type="text" name="<?php echo SUPPLEMENTS_PREFIX; ?>member_discount[<?php echo $group->id; ?>][value][<?php echo $id; ?>]" class="number" onkeyup="ACommon.toFloat(this, true)" value="<?php echo JArrayHelper::getValue($data, 'value'); ?>" />
												</td>
												<td>
													<?php echo JHTML::_('select.genericlist', $type, SUPPLEMENTS_PREFIX . 'member_discount[' . $group->id . '][type][' . $id . ']', 'class="inline"', 'value', 'text', JArrayHelper::getValue($data, 'type')); ?>
												</td>
											</tr>
										<?php } ?>
									</table>
								</td>
							</tr>
						<?php } ?>
							<tr id="supplement-empty" <?php if ($scount > 1) { ?>style="display: none;"<?php } ?>><td colspan="8" class="emptyList"><?php echo JText::_('EMPTY_SUPPLEMENTS_LIST'); ?></td></tr>
					</tbody>
				</table>			
			</div>
		<?php } ?>
	</fieldset>
</div>