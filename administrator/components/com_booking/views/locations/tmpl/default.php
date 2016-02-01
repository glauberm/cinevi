<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	views
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die;

if (ISJ3) {
	JHTML::_('dropdown.init');
	JHTML::_('formbehavior.chosen', 'select');
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_booking&view=locations'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search btn-group pull-left">
			<label class="filter-search-lbl element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label> 
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo JText::_('JGLOBAL_TITLE'); ?>"/>
		</div>
		<div class="btn-group pull-left hidden-phone">	
			<button type="submit" class="btn">
				<i class="icon-search"></i>
				<?php echo ISJ3 ? '' : JText::_('JSEARCH_FILTER_SUBMIT'); ?>
			</button>
			<button type="button" class="btn" onclick="document.id('filter_search').value='';this.form.submit();">
				<i class="icon-remove"></i>
				<?php echo ISJ3 ? '' : JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>
		<div class="btn-group pull-right hidden-phone filter-select fltrt">
			<select name="filter_pick_up" id="filter_pick_up" class="input-medium" onchange="this.form.submit()">
				<option value="">- <?php echo JText::_('PICK_UP'); ?> -</option>
				<option value="1"<?php if ($this->state->get('filter.pick_up') === '1') { ?> selected="selected"<?php } ?>><?php echo JText::_('JYES'); ?></option>
				<option value="0"<?php if ($this->state->get('filter.pick_up') === '0') { ?> selected="selected"<?php } ?>><?php echo JText::_('JNO'); ?></option>
			</select>
			<select name="filter_drop_off" id="filter_drop_off" class="input-medium" onchange="this.form.submit()">
				<option value="">- <?php echo JText::_('DROP_OFF'); ?> -</option>
				<option value="1"<?php if ($this->state->get('filter.drop_off') === '1') { ?> selected="selected"<?php } ?>><?php echo JText::_('JYES'); ?></option>
				<option value="0"<?php if ($this->state->get('filter.drop_off') === '0') { ?> selected="selected"<?php } ?>><?php echo JText::_('JNO'); ?></option>
			</select>
	</fieldset>
	<div class="clr"></div>
	<table class="adminlist table-striped table">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'l.title', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'Pick Up', 'l.pick_up', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'Drop Off', 'l.drop_off', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'l.id', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter().(ISJ3 ? $this->pagination->getLimitBox() : ''); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
						<?php if ($item->checked_out) { ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'location.', true); ?>
						<?php } ?>
						<a href="<?php echo JRoute::_('index.php?option=com_booking&task=location.edit&id='.$item->id);?>">
							<?php echo $this->escape($item->title); ?></a>
					</td>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $item->pick_up, $i, 'locations.pick_up_', true, 'cb'); ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $item->drop_off, $i, 'locations.drop_off_', true, 'cb'); ?>
					</td>
					<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->state->get('list.direction')); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>