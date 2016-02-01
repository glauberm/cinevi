<?php 

/**
 * Provision edit form template
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

$type = array();
$type[] = JHtml::_('select.option', PROVISION_TYPE_VALUE, $config->mainCurrency);
$type[] = JHtml::_('select.option', PROVISION_TYPE_PERCENT, '%');

?>
<div class="width-100">
	<fieldset class="adminform">
    	<legend><?php echo JText::_('PROVISIONS'); ?></legend>	    	
		<div class="col">
    		<h3 class="hasTip" title="<?php echo $this->escape(JText::_('AGENT_PROVISION')) . '::' . $this->escape(JText::_('AGENT_PROVISION_INFO')); ?>"><?php echo JText::_('AGENT_PROVISION'); ?></h3>
			<table class="template">
				<thead>
					<tr>
						<th><?php echo JText::_('USER_GROUP'); ?></th>
						<th colspan="2" class="hasTip" title="<?php echo $this->escape(JText::_('PROVISION')); ?>::<?php echo $this->escape(JText::_('PROVISION_TIP')); ?>"><?php echo JText::_('PROVISION'); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->get('agents') as $group) {
						$data = JArrayHelper::getValue($this->subject->agent_provision, $group->id, array(), 'array'); ?>
						<tr>
							<td><?php echo $group->title; ?></td>
							<td>
								<input type="text" name="agent_provision[<?php echo $group->id; ?>][value]" class="number" onkeyup="ACommon.toFloat(this, true)" value="<?php echo JArrayHelper::getValue($data, 'value'); ?>" />
							</td>
							<td>
								<?php echo JHTML::_('select.genericlist', $type, 'agent_provision[' . $group->id . '][type]', 'class="inline"', 'value', 'text', JArrayHelper::getValue($data, 'type')); ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</fieldset>
</div>