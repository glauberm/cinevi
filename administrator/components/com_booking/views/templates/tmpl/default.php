<?php

/**
 * Templates administration list template. 
 * Display browse table with advanced filter.
 * Set the toolbar for many operations with templates.
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

/* @var $this BookingViewTemplates */

BookingHelper::setSubmenu(5);

JToolBarHelper::title(JText::_(COMPONENT_NAME).": ".JText::_('TEMPLATES'), 'template');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::divider();
JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', 'Copy');
JToolBarHelper::divider();
JToolBarHelper::deleteList('', 'trash', 'Trash');
JToolBarHelper::divider();
if (JFactory::getUser()->authorise('core.admin', 'com_booking'))
	JToolBarHelper::preferences('com_booking');

$orderDir = $this->lists['order_Dir'];
$order = $this->lists['order'];

$templatesCount = count($this->templates);

$titleEditTemplate = $this->escape(JText::_('EDIT_TEMPLATE'));
$appendTemplate = $this->escape(JText::_('APPEND_TEMPLATE'));

$search = $this->lists['search'];

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="btn-group pull-left hidden-phone filter-search fltlft">
			<label for="filter_search" class="filter-search-lbl element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="search" id="filter_search" onchange="this.form.submit();" value="<?php echo $this->escape($this->lists['search']); ?>" placeholder="<?php echo JText::_('TITLE'); ?>" class="inputbox" />
			
		</div>
		<div class="btn-group pull-left hidden-phone fltlft">
			<button class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
				<i class="icon-search"></i>
				<?php echo ISJ3 ? '' : JText::_('JSEARCH_FILTER_SUBMIT'); ?>
			</button>
			<button class="btn hasTooltip" onclick="this.form.reset.value=1;this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
				<i class="icon-remove"></i>
				<?php echo ISJ3 ? '' : JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>
	</fieldset>
	<div id="editcell">
		<table class="adminlist table-striped table" cellspacing="1">
			<thead>
				<tr>
					<th width="1%">#</th>
					<th width="1%">
						<input type="checkbox" class="inputCheckbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th class="title">
				        <?php echo JText::_('TITLE'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
    			<tr>
    				<td colspan="3">
    				    <?php echo $this->pagination->getListFooter().(ISJ3 ? $this->pagination->getLimitBox() : ''); ?>
    				</td>
    			</tr>
			</tfoot>
			<tbody>
				<?php if (! $templatesCount) { ?>
					<tr><td colspan="3" class="emptyListInfo"><?php echo JText::_('EMPTY_TEMPLATES_LIST_INFO'); ?></td></tr>
				<?php 
					} else {
						$count = 0;
						foreach ($this->templates as $i => $template) {

							/* @var $template ATemplate */
							if ($i < $this->lists['limitstart']) // display from selected offset
								continue;
							if ($i == $this->lists['limitstart'] + $this->lists['limit'] // end on selected limit 
								&& $this->lists['limit'] != 0)	// if not select all
				    			break;				    		
				    		$count++;
				    		$title = $this->escape($template->name);

				    		$input = isset($this->input) ? $this->input : 'false';
				    		$js = 'javascript:ListTemplates.select(' . $template->id . ',\'' . $title . '\',\'' . $title . '\',\''.$this->escape($input).'\')';
				    		
				    	?>
				    	<tr class="row<?php echo ($i % 2); ?>">
				    		<td  style="text-align: right; white-space: nowrap;">
				    			<?php echo number_format($this->pagination->getRowOffset($i), 0, '', ' '); ?>
				    		</td>
				    		<?php $template->checked_out = 0; ?>
				    		<td class="checkboxCell"><?php echo JHTML::_('grid.checkedout', $template, $i); ?></td>
				    		<td>
				    		<?php if (! $this->selectable) {?>
				    			<a href="<?php echo JRoute::_(ARoute::edit(CONTROLLER_TEMPLATE, $template->id)); ?>" title="<?php echo $titleEditTemplate . ' ' . $title; ?>">
				    				<?php echo $title; ?>
				    			</a>
				    		<?php } else {?>
				    			<a href="<?php echo $js; ?>" title="<?php echo $appendTemplate; ?>">
				    				<?php echo $title; ?>
				    			</a>
				    		<?php }?>
				    		</td>
				    	</tr>
				    <?php } ?>
				    <?php if (! $count) { ?>
				    	<tr><td colspan="3" class="emptyListInfo"><?php echo JText::_('NO_ITEMS_FOUND'); ?></td></tr>
				    <?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="reset" value="0"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_TEMPLATE; ?>"/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $orderDir; ?>"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
