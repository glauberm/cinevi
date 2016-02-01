<?php

/**
 * Admins administration list template. 
 * Display browse table with advanced filter.
 * Set the toolbar for many operations with admins.
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

/* @var $this BookingViewAdmins */



BookingHelper::setSubmenu(6);

if (ISJ3) {
	JHTML::_('dropdown.init');
	JHTML::_('formbehavior.chosen', 'select');
}

JToolBarHelper::title(JText::_(COMPONENT_NAME).": ".JText::_('RESERVATION_MANAGERS'), 'cbadmins.png');
JToolBarHelper::custom('setAsAdmin','new','new','Make_Global_Manager');
JToolBarHelper::custom('setAsNoAdmin','cancel','cancel','Unset_from_Global_Managers');
JToolBarHelper::divider();
if (JFactory::getUser()->authorise('core.admin', 'com_booking'))
	JToolBarHelper::preferences('com_booking');

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
			<select name="filter_global_manager" id="filter_global_manager" class="input-medium" onchange="this.form.submit()">
				<option value="">- <?php echo JText::_('GLOBAL_MANAGER'); ?> -</option>
				<option value="1"<?php if ($this->lists['global_manager'] === '1') { ?> selected="selected"<?php } ?>><?php echo JText::_('JYES'); ?></option>
				<option value="0"<?php if ($this->lists['global_manager'] === '0') { ?> selected="selected"<?php } ?>><?php echo JText::_('JNO');  ?></option>
			</select>
		</div>
	</fieldset>
	<div id="editcell">
		<table class="adminlist table-striped table" cellspacing="1">
			<thead>
				<tr>
					<th width="1%" nowrap="nowrap">#</th>
					<th width="1%" nowrap="nowrap">
						<input type="checkbox" class="inputCheckbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th>
				        <?php echo JHTML::_('grid.sort', 'Name', 'name', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
					<th width="1%" nowrap="nowrap">
				        <?php echo JHTML::_('grid.sort', 'Username', 'username', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
					<th width="1%" nowrap="nowrap">
				        <?php echo JHTML::_('grid.sort', 'Group', 'usertype', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
					<th width="1%" nowrap="nowrap">
				        <?php echo JText::_('EMAIL'); ?>
					</th>
					<th width="1%" nowrap="nowrap">
				        <?php echo JHTML::_('grid.sort', 'Manager_Type', 'isadmin', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
    			<tr>
    				<td colspan="7">
    				    <?php echo $this->pagination->getListFooter().(ISJ3 ? $this->pagination->getLimitBox() : ''); ?>
    				</td>
    			</tr>
			</tfoot>
			<tbody>
				<?php if (! is_array($this->items) || ! count($this->items)) { ?>
					<tr><td colspan="7"><?php echo JText::_('NO_ITEMS_FOUND'); ?></td></tr>
				<?php } else { ?>
				    <?php 
				    	foreach ($this->items as $i => $item) {
				    		/* @var $item TableAdmin */ 
							$item->checked_out = 0; // required by JHtmlGrid::checkedOut 
				    ?>
				    	<tr class="row<?php echo ($i % 2); ?>">
				    		<td align="right"><?php echo $this->pagination->getRowOffset($i); ?></td>
				    		<td><?php echo JHTML::_('grid.checkedout', $item, $i); ?>
				    		</td>
				    		<td><a href="<?php echo JRoute::_(ARoute::editUser($item->id)); ?>" title="<?php echo $this->escape(JText::_('EDIT_USER_ACOUNT')); ?>::<?php echo $this->escape($item->name)?>" class="hasTip"><?php echo $item->name; ?></a></td>
				    		<td nowrap="nowrap"><?php echo $item->username; ?></td>
				    		<td nowrap="nowrap"><?php echo JText::_($item->usertype); ?></td>
				    		<td><a href="mailto:<?php echo $item->email; ?>" title="<?php echo $this->escape(JText::_('SEND_E_MAIL_TO')); ?>::<?php echo $this->escape($item->email); ?>" class="hasTip"><?php echo $item->email; ?></a></td>
				    		<td align="center"> 
				    			<?php if ($item->isadmin) { ?>
            						<a onclick="listItemTask('cb<?php echo $i; ?>','setAsNoAdmin')" href="javascript:void(0)" title="<?php echo $this->escape(JText::_('UNSET_FROM_GLOBAL_MANAGERS')); ?>::<?php echo $this->escape($item->name); ?>" class="hasTip">
            							<img src="<?php echo JURI::root(true); ?>/components/com_booking/assets/images/icon-16-language.png" alt="" />
            						</a>
								<?php } else { ?>
									<a onclick="listItemTask('cb<?php echo $i; ?>','setAsAdmin')" href="javascript:void(0)" title="<?php echo $this->escape(JText::_('MAKE_GLOBAL_MANAGER')); ?>::<?php echo $this->escape($item->name); ?>" class="hasTip">
            							<img src="<?php echo JURI::root(true); ?>/components/com_booking/assets/images/icon-16-disabled.png" alt="" />
            						</a>
            					<?php } ?>
				    		</td>
				    		
				    	</tr>
				    <?php } ?>
				<?php } ?>
			</tbody>
		</table>
		<table align="center">
			<tr align="center">
				<td style="background: url('<?php echo JURI::root(true); ?>/components/com_booking/assets/images/icon-16-language.png') no-repeat scroll left center transparent; padding: 0px 30px 0px 20px;">
					<?php echo JText::_('GLOBAL_MANAGER'); ?>
				</td>
				<td style="background: url('<?php echo JURI::root(true); ?>/components/com_booking/assets/images/icon-16-disabled.png') no-repeat scroll left center transparent; padding: 0px 30px 0px 20px;">
					<?php echo JText::_('NON_MANAGER'); ?>
				</td>
			</tr>
		</table>
	</div>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="task" value="<?php echo JRequest::getCmd('task'); ?>"/>
	<input type="hidden" name="reset" value="0"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_ADMIN; ?>"/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
