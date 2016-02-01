<?php 

/**
 * Discount edit form template
 *
 * @version	$Id$
 * @package	ARTIO Booking
 * @subpackage	views
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license   	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

/* @var $this BookingViewSubject */

$config = AFactory::getConfig();

$type[] = JHtml::_('select.option', DISCOUNT_TYPE_VALUE, $config->mainCurrency);
$type[] = JHtml::_('select.option', DISCOUNT_TYPE_PERCENT, '%');

$voldisper[] = JHtml::_('select.option', DISCOUNT_PER_UNIT, JText::_('PEAR_RESERVATION_UNIT'));
$voldisper[] = JHtml::_('select.option', DISCOUNT_PER_RESERVATION, JText::_('PEAR_WHOLE_RESERVATION'));

?>
<div class="width-100">
	<fieldset class="adminform">
    	<legend><?php echo JText::_('DISCOUNTS'); ?></legend>
    	<div class="col discountContainer">
	    	<h3 class="hasTip" title="<?php echo $this->escape(($title = JText::_('SINGLE_TIME_DISCOUNTS'))) . '::' . $this->escape(JText::_('SINGLE_TIME_DISCOUNTS_INFO')); ?>"><?php echo JText::_('SINGLE_TIME_DISCOUNTS'); ?></h3>
			<table class="template">
				<thead>
					<tr>
						<th class="hasTip" title="<?php echo $this->escape(JText::_('VOLUME')); ?>::<?php echo $this->escape(JText::_('VOLUME_TIP')); ?>"><?php echo JText::_('VOLUME'); ?></th>
						<th colspan="2" class="hasTip" title="<?php echo $this->escape(JText::_('DISCOUNT')); ?>::<?php echo $this->escape(JText::_('DISCOUNT_TIP')); ?>"><?php echo JText::_('DISCOUNT'); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php 
                        $volDises = array_filter((array) $this->subject->volume_discount);
						array_unshift($volDises, array('count' => '', 'value' => '', 'type' => 0));
						foreach ($volDises as $vdi => $volDis) { ?>
							<tr<?php if (!$vdi) { ?> style="display: none" class="disrow"<?php } ?>>
								<td>
									<input type="text" name="dis_count[]" value="<?php echo $volDis['count']; ?>" class="number" onkeyup="ACommon.toInt(this)" />
								</td>
								<td>
									<input type="text" name="dis_value[]" value="<?php echo $volDis['value']; ?>" class="number" onkeyup="ACommon.toFloat(this, true)" />
								</td>
								<td>
									<?php echo JHTML::_('select.genericlist', $type, 'dis_type[]', 'class="inline"', 'value', 'text', $volDis['type']); ?>
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
						<?php if (count($volDises) == 1) { ?>
							<tr class="nonediscount"><td colspan="4"><?php echo JText::_('NONE_DISCOUNT'); ?></td></tr>
						<?php } ?>
				</tbody>
			</table>
			<button onclick="return EditSubject.addDiscount(this)" class="transparent" title="<?php echo $this->escape(JText::_('ADD_DISCOUNT')); ?>">
				<?php echo JHtml::_('image', 'admin/expandall.png', JText::_('ADD_DISCOUNT'), null, true); ?>
				<span><?php echo JText::_('ADD_DISCOUNT'); ?></span>
			</button>
			<input type="hidden" name="volume_discount" value="" />
	</div>
	<div class="col" style="width: 50px">&nbsp;</div>
	<div class="col discountContainer">
    	<h3 class="hasTip" title="<?php echo $this->escape(($title = JText::_('EARLY_BOOKING_DISCOUNT'))) . '::' . $this->escape(JText::_('EARLY_BOOKING_DISCOUNT_INFO')); ?>"><?php echo JText::_('EARLY_BOOKING_DISCOUNT'); ?></h3>
		<table class="template">
			<thead>
				<tr>
					<th><?php echo JText::_('DAY_AMOUNT'); ?></th>
					<th colspan="2" class="hasTip" title="<?php echo $this->escape(JText::_('DISCOUNT')); ?>::<?php echo $this->escape(JText::_('DISCOUNT_TIP')); ?>"><?php echo JText::_('DISCOUNT'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php 
                    $earDises = array_filter((array) $this->subject->early_booking_discount);
					array_unshift($earDises, array('count' => '', 'value' => '', 'type' => 0));
					foreach ($earDises as $ebdi => $earDis) { ?>
						<tr<?php if (!$ebdi) { ?> style="display: none" class="disrow"<?php } ?>>
							<td>
								<input type="text" name="dis_count[]" value="<?php echo $earDis['count']; ?>" class="number" onkeyup="ACommon.toInt(this)" />
							</td>
							<td>
								<input type="text" name="dis_value[]" value="<?php echo $earDis['value']; ?>" class="number" onkeyup="ACommon.toFloat(this, true)" />
							</td>
							<td>
								<?php echo JHTML::_('select.genericlist', $type, 'dis_type[]', 'class="inline"', 'value', 'text', $earDis['type']); ?>
							</td>
							<td>
								<button onclick="return EditSubject.removeDiscount(this)" class="transparent" title="<?php echo $this->escape(JText::_('REMOVE_DISCOUNT')); ?>">
									<?php echo JHtml::_('image', 'admin/publish_r.png', JText::_('REMOVE_DISCOUNT'), null, true); ?>
								</button>		
							</td>
						</tr>
					<?php } ?>
					<?php if (count($earDises) == 1) { ?>
						<tr class="nonediscount"><td colspan="4"><?php echo JText::_('NONE_DISCOUNT'); ?></td></tr>
					<?php } ?>
				</tbody>
			</table>
			<button onclick="return EditSubject.addDiscount(this)" class="transparent" title="<?php echo $this->escape(JText::_('ADD_DISCOUNT')); ?>">
				<?php echo JHtml::_('image', 'admin/expandall.png', JText::_('ADD_DISCOUNT'), null, true); ?>
				<span><?php echo JText::_('ADD_DISCOUNT'); ?></span>
			</button>
			<input type="hidden" name="early_booking_discount" value="" />
		</div>
		<div class="col" style="width: 50px">&nbsp;</div>
		<div class="col">
    		<h3 class="hasTip" title="<?php echo $this->escape(JText::_('MEMBER_DISCOUNT')) . '::' . $this->escape(JText::_('MEMBER_DISCOUNT_INFO')); ?>"><?php echo JText::_('MEMBER_DISCOUNT'); ?></h3>
			<table class="template">
				<thead>
					<tr>
						<th><?php echo JText::_('USER_GROUP'); ?></th>
						<th colspan="2" class="hasTip" title="<?php echo $this->escape(JText::_('DISCOUNT')); ?>::<?php echo $this->escape(JText::_('DISCOUNT_TIP')); ?>"><?php echo JText::_('DISCOUNT'); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->get('usergroups') as $group) {
                        $memDis = (array) $this->subject->member_discount;
						$data = JArrayHelper::getValue($memDis, $group->id, array(), 'array'); ?>
						<tr>
							<td><?php echo $group->title; ?></td>
							<td>
								<input type="text" name="member_discount[<?php echo $group->id; ?>][value]" class="number" onkeyup="ACommon.toFloat(this, true)" value="<?php echo JArrayHelper::getValue($data, 'value'); ?>" />
							</td>
							<td>
								<?php echo JHTML::_('select.genericlist', $type, 'member_discount[' . $group->id . '][type]', 'class="inline"', 'value', 'text', JArrayHelper::getValue($data, 'type')); ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</fieldset>
</div>