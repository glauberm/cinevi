<?php

/**
 * Customers administration list template. 
 * Display browse table with advanced filter.
 * Set the toolbar for many operations with customers.
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

/* @var $this BookingViewReservations */



if (IS_ADMIN)  {
	BookingHelper::setSubmenu(2);
	JToolBarHelper::title(JText::_(COMPONENT_NAME).": ".JText::_('CUSTOMERS'), 'user.png');
	JToolBarHelper::addNew();
	if (JFactory::getUser()->authorise('booking.edit.customer', 'com_booking'))
		JToolBarHelper::editList();
	JToolBarHelper::divider();
	JToolBarHelper::deleteList('', 'trash', 'Trash');
	JToolBarHelper::custom('restore', 'restore.png', 'restore_f2.png', 'Restore', true);

	JToolBar::getInstance('toolbar')->appendButton('Confirm', 'Are you sure?', 'trash', 'Empty_Trash', 'emptyTrash', false, true);
	JToolBarHelper::divider();
	if (JFactory::getUser()->authorise('core.admin', 'com_booking'))
		JToolBarHelper::preferences('com_booking');
} 

$colspan = $this->selectable ? 9 : 10;

$editCustomer = JText::_('EDIT_CUSTOMER');
$titleEditAcount = JText::_('EDIT_CUSTOMER_USER_ACOUNT');

$userId = $this->user->id;

$orderDir = $this->lists['order_Dir'];
$order = $this->lists['order'];

$itemsCount = count($this->items);

$pagination = &$this->pagination;

if (ISJ3) {
	JHTML::_('dropdown.init');
	JHTML::_('formbehavior.chosen', 'select');
}

ADocument::addScriptPropertyDeclaration('customerRoute', JRoute::_('index.php?option=com_booking&task=customer.ajax', false));

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="btn-group pull-left hidden-phone filter-search fltlft">
       		<label for="filter_search" class="filter-search-lbl element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" onchange="this.form.submit();" value="<?php echo $this->escape($this->lists['search']); ?>" placeholder="<?php echo JText::_('NAME_OR_USERNAME'); ?>" class="inputbox" />
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
		<div class="btn-group pull-right hidden-phone filter-select fltrt">
			<select name="filter_state" id="filter_state" class="input-medium" onchange="this.form.submit()">
				<option value="">- <?php echo JText::_('CUSTOMER_STATUS'); ?> -</option>
				<option value="<?php echo CUSTOMER_STATE_ACTIVE; ?>"<?php if ($this->lists['state'] === (string) CUSTOMER_STATE_ACTIVE) { ?> selected="selected"<?php } ?>><?php echo JText::_('ACTIVE'); ?></option>
				<option value="<?php echo CUSTOMER_STATE_BLOCK; ?>"<?php if ($this->lists['state'] === (string) CUSTOMER_STATE_BLOCK) { ?> selected="selected"<?php } ?>><?php echo JText::_('BLOCK'); ?></option>
				<option value="<?php echo CUSTOMER_STATE_DELETED; ?>"<?php if ($this->lists['state'] === (string) CUSTOMER_STATE_DELETED) { ?> selected="selected"<?php } ?>><?php echo JText::_('TRASHED'); ?></option>
			</select>
		</div>
	</fieldset>
	<div id="editcell">
		<table class="adminlist table-striped table category" cellspacing="1">
			<thead>
				<tr>
					<th width="1%">#</th>
					<?php if (! $this->selectable) { ?>
						<th width="1%">
							<input type="checkbox" class="inputCheckbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
						</th>
					<?php } ?>	
					<th>
				        <?php echo JHTML::_('grid.sort', 'Name', 'surname', $orderDir, $order); ?>
					</th>
					<th width="1%">
				        <?php echo JHTML::_('grid.sort', 'User', 'user-username', $orderDir, $order); ?>
					</th>
					<th width="1%">
				        <?php echo JText::_('STATE'); ?>
					</th>
					<th>
				        <?php echo JHTML::_('grid.sort', 'Address', 'city', $orderDir, $order); ?>
					</th>
					<th>
				        <?php echo JHTML::_('grid.sort', 'Company', 'company', $orderDir, $order); ?>
					</th>
					<th width="5%"><?php echo JText::_('EMAIL'); ?></th>
					<th width="1%">
				        <?php echo JHTML::_('grid.sort', 'ID', 'id', $orderDir, $order); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
    			<tr>
    				<td colspan="<?php echo $colspan; ?>">
    				    <?php echo $pagination->getListFooter().(ISJ3 ? $pagination->getLimitBox() : ''); ?>
    				</td>
    			</tr>
			</tfoot>
			<tbody>
				<?php if (! is_array($this->items) || ! $itemsCount) { ?>
					<tr><td colspan="<?php echo $colspan; ?>"><?php echo JText::_('NO_ITEMS_FOUND'); ?></td></tr>
				<?php } else { ?>
				    <?php for ($i = 0; $i < $itemsCount; $i++) { ?>
				    	<?php $subject = &$this->items[$i]; ?>
				    	<?php /* @var $item TableCustomer */ ?>
				   		<?php $name = BookingHelper::formatName($subject, true); ?> 
				    	<?php $isCheckedOut = JTable::isCheckedOut($userId, $subject->checked_out); ?>     
				    	<tr class="row<?php echo ($i % 2); ?>">
				    		<td  style="text-align: right; white-space: nowrap;"><?php echo number_format($pagination->getRowOffset($i), 0, '', ' '); ?></td>
				    		<?php if (! $this->selectable) { ?>
				    			<td class="checkboxCell"><?php echo JHTML::_('grid.checkedout', $subject, $i); ?></td>
				    		<?php } ?>
				    		<td>
				    			<?php if (! $this->selectable) { ?>	
				    				<?php if (! $isCheckedOut) { ?> 
				                		<span class="editlinktip hasTip" title="<?php echo $editCustomer; ?>::<?php echo $name; ?>">
											<a href="<?php echo JRoute::_(ARoute::detail(CONTROLLER_CUSTOMER, $subject->id)); ?>"><?php echo $name; ?></a>
						            	</span>
						        	<?php } else { ?> 
				    					<?php echo $name; ?> 
				    				<?php } ?>
						        <?php } else { ?>
									<a href="javascript:ListCustomers.fillCustomerCard(<?php echo (int) $subject->id; ?>);ListCustomers.select('<?php echo $subject->id; ?>','<?php echo $name; ?>');" title=""><?php echo $name; ?></a>
						        <?php } ?>
				    		</td>
				    		<td>
				    			<?php if (! $this->selectable) { ?>
				    				<a href="<?php echo JRoute::_(ARoute::editUser($subject->userId)); ?>" title="<?php echo $titleEditAcount; ?>"><?php echo $subject->username; ?></a>
				    			<?php } else { ?>
				    				<?php echo $subject->username; ?>
				    			<?php } ?>	
				    		</td>
				    		<td style="text-align: center;">
				    		<?php 
        						switch ($subject->state) {
							      	case CUSTOMER_STATE_ACTIVE:
							          	if (! JTable::isCheckedOut($this->user->get('id'), $subject->checked_out)) { ?>
            								<span class="editlinktip hasTip aIcon aIconTick" title="<?php echo $this->escape(JText::_('ACTIVE')) . '::' . $this->escape(JText::_('CLICK_TO_MARK_AS_BLOCKED')); ?>" onclick="listItemTask('cb<?php echo $i; ?>','block')" style="cursor: pointer;">&nbsp;</span>			
            							<?php } else { ?>	
            								<span class="editlinktip hasTip aIcon aIconTick" title="<?php echo $this->escape(JText::_('ACTIVE')); ?>">&nbsp;</span>
            							<?php }
            							break;
							       	case CUSTOMER_STATE_BLOCK:
							           	if (! JTable::isCheckedOut($this->user->get('id'), $subject->checked_out)) { ?>
            								<span class="editlinktip hasTip aIcon aIconUnpublish" title="<?php echo $this->escape(JText::_('BLOCKED')) . '::' . $this->escape(JText::_('CLICK_TO_MARK_AS_TRASHED')); ?>" onclick="listItemTask('cb<?php echo $i; ?>','trash')" style="cursor: pointer">&nbsp;</span>			
            							<?php } else { ?>	
            								<span class="editlinktip hasTip aIcon aIconUnpublish" title="<?php echo $this->escape(JText::_('BLOCKED')); ?>">&nbsp;</span>
            							<?php }
							           	break;
							      	case CUSTOMER_STATE_DELETED:
							          	if (! JTable::isCheckedOut($this->user->get('id'), $subject->checked_out)) { ?>
            								<span class="editlinktip hasTip aIcon aIconTrash" title="<?php echo $this->escape(JText::_('TRASHED')) . '::' . $this->escape(JText::_('CLICK_TO_MARK_AS_ACTIVE')); ?>" onclick="listItemTask('cb<?php echo $i; ?>','restore')" style="cursor: pointer">&nbsp;</span>			
            							<?php } else { ?>	
            								<span class="editlinktip hasTip aIcon aIconTrash" title="<?php echo $this->escape(JText::_('TRASHED')); ?>">&nbsp;</span>
            							<?php }
							           	break;
        						} ?>
				    		</td>
				    		<td><?php echo BookingHelper::formatAddress($subject); ?>&nbsp;</td>
				    		<td><?php echo $subject->company; ?>&nbsp;</td>
				    		<td><a href="mailto:<?php echo $subject->email; ?>" title="<?php echo $this->escape(JText::_('SEND_E_MAIL_TO')); ?>::<?php echo $this->escape($subject->email); ?>" class="hasTip"><?php echo $subject->email; ?></a></td>
				    		<td style="text-align: right; white-space: nowrap;"><?php echo number_format($subject->id, 0, '', ' '); ?></td>
				    	</tr>
				    <?php } ?>
				<?php } ?>
			</tbody>
		</table>
		<table align="center">
			<tr align="center">
				<td class="aIconLegend aIconTick"><?php echo JText::_('ACTIVE'); ?><span class="aIconSeparator">&nbsp;</span></td>
				<td class="aIconLegend aIconUnpublish"><?php echo JText::_('BLOCKED'); ?><span class="aIconSeparator">&nbsp;</span></td>
				<td class="aIconLegend aIconTrash"><?php echo JText::_('TRASHED'); ?></td>
			</tr>
		</table>
		<div class="clr">&nbsp;</div>
	</div>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="task" value="<?php echo JRequest::getCmd('task'); ?>"/>
	<?php $tmpl = JRequest::getCmd('tmpl'); ?>
	<?php if ($tmpl) { ?>
		<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>"/>
	<?php } ?>	
	<input type="hidden" name="reset" value="0"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_CUSTOMER; ?>"/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $orderDir; ?>"/>
	<input type="hidden" name="<?php echo SESSION_TESTER; ?>" value="1"/>
	<?php echo JHTML::_('form.token'); ?>
</form>	
