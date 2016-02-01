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

if ($this->params->get('show_page_heading')) {
?>
	<h1><?php echo $this->params->get('page_heading'); ?></h1>
<?php } ?>
<form action="<?php echo JRoute::_('index.php?option=com_booking&view=closingdays'); ?>" method="post" name="adminForm" id="adminForm" <?php if ($this->params->get('pageclass_sfx')) { ?>class="<?php echo $this->params->get('pageclass_sfx'); ?>"<?php } ?>>
	<fieldset id="filter-bar">
		<div class="filter-search btn-group pull-left hidden-phone">
			<label class="filter-search-lbl element-invisible pull-left" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label> 
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo JText::_('TITLE'); ?>" />
		</div>
		<div class="filter-search btn-group pull-left hidden-phone">	
			<button type="submit" class="btn">
				<i class="icon-search"></i>
				<?php echo ISJ3 ? '' : JText::_('JSEARCH_FILTER_SUBMIT'); ?>
			</button>
			<button type="button" class="btn" onclick="document.id('filter_search').value='';this.form.submit();">
				<i class="icon-remove"></i>
				<?php echo ISJ3 ? '' : JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
        </div>
		<?php if (IS_SITE) { ?>
        <div class="btn-group pull-right hidden-phone formelm-buttons">
			<?php if ($this->user->authorise('core.create')) { ?>
        		<button type="button" onclick="Joomla.submitbutton('closingday.add')" class="btn btn-success">
        	        <?php echo JText::_('JACTION_CREATE') ?>
        		</button>
        	<?php }
            if ($this->user->authorise('core.edit')) { ?>
                <button type="button" onclick="document.adminForm.boxchecked.value == 0 ? alert('<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'); ?>') : Joomla.submitbutton('closingday.edit')" class="btn btn-primary">
        	        <?php echo JText::_('JACTION_EDIT') ?>
        		</button>
        	<?php }
            if ($this->user->authorise('core.delete')) { ?>
        		<button type="button" onclick="document.adminForm.boxchecked.value == 0 ? alert('<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'); ?>') : Joomla.submitbutton('closingdays.delete')" class="btn btn-danger">
        	        <?php echo JText::_('JACTION_DELETE') ?>
        		</button>
        	<?php } ?>
            </div>
	    <?php } ?>
	</fieldset>
	<div class="clr"></div>
	<table class="adminlist category table table-striped table-hover">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'c.title', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'Date_Up', 'c.date_up', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'Date_Down', 'c.date_down', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'Time_Up', 'c.time_up', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'Time_Down', 'c.time_down', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'c.id', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
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
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'closingdays.', true); ?>
						<?php } ?>
						<a href="<?php echo JRoute::_('index.php?option=com_booking&task=closingday.edit&id='.$item->id);?>">
							<?php echo $this->escape($item->title); ?></a>
					</td>
					<td nowrap="nowrap"><?php echo JHtml::date($item->date_up, ADATE_FORMAT_NORMAL); ?></td>
					<td nowrap="nowrap"><?php echo JHtml::date($item->date_down, ADATE_FORMAT_NORMAL); ?></td>
					<td nowrap="nowrap"><?php echo JHtml::date($item->time_up, ATIME_FORMAT); ?></td>
					<td nowrap="nowrap"><?php echo JHtml::date($item->time_down, ATIME_FORMAT); ?></td>
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