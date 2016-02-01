<?php

/**
 * Subjects administration list template. 
 * Display browse table with advanced filter.
 * Set the toolbar for many operations with subjects.
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

/* @var $this BookingViewSubjects */

$config = AFactory::getConfig();



$bar = &JToolBar::getInstance('toolbar');

BookingHelper::setSubmenu(1);

JToolBarHelper::title(JText::_(COMPONENT_NAME).": ".JText::_('BOOKABLE_ITEMS'), 'object');

$bar->appendButton('Popup', 'new', 'New', ARoute::view(VIEW_SELECT_TEMPLATE, null, null, array('tmpl' => 'component')));
JToolBarHelper::editList();

JToolBarHelper::divider();

//is any item in state archived?
$archivedexists = false;
if(is_array($this->items))
	foreach($this->items as $item)
	{
		if($item->state == "-1")
		{
			$archivedexists = true;
			break;
		}
	}
if($archivedexists) JToolBarHelper::unarchiveList();
JToolBarHelper::archiveList();

JToolBarHelper::publishList();
JToolBarHelper::unpublishList();

JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', 'Copy');

JToolBarHelper::divider();

//is any item in state trashed?
$trashedexists = false;
if(is_array($this->items))
	foreach($this->items as $item)
	{
		if($item->state == "-2")
		{
			$trashedexists = true;
			break;
		}
	}
JToolBarHelper::deleteList('', 'trash', 'Trash');
if($trashedexists) JToolBarHelper::custom('restore', 'restore.png', 'restore_f2.png', 'Restore', true);
if($trashedexists) $bar->appendButton('Confirm', 'Are you sure?', 'trash', 'Empty_Trash', 'emptyTrash', false, true);

JToolBarHelper::divider();
if (JFactory::getUser()->authorise('core.admin', 'com_booking'))
	JToolBarHelper::preferences('com_booking');

$colspan = $this->selectable ? 8 : 12;

$editSubject = $this->escape(JText::_('EDIT_OBJECT'));
$editTemplate = $this->escape(JText::_('EDIT_TEMPLATE'));
$notFound = '- ' . JText::_('NOT_FOUND') . ' -';
$appendSubject = $this->escape(JText::_('APPEND_OBJECT'));

$userId = $this->user->id;

$orderDir = $this->lists['order_Dir'];
$order = $this->lists['order'];


$itemsCount = count($this->items);
if ($itemsCount > 2) {
	$itemsCount = 2;
}


$element = $this->type == SUBJECT_BOOKABLE || $this->type == SUBJECT_PARENT;

$ipath = &BookingHelper::getIPath();

$subjectTable = new TableSubject(($db = &JFactory::getDBO()));

if (ISJ3) {
	JHTML::_('dropdown.init');
	JHTML::_('formbehavior.chosen', 'select');
}

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
		<div class="btn-group pull-right hidden-phone filter-select fltrt">
			<?php 
				echo $this->lists['filter_parent']; 
				echo $this->lists['filter_template']; 
			?>
			<select name="filter_state" id="filter_state" class="input-medium" onchange="this.form.submit()">
				<option value="">- <?php echo JText::_('ITEM_STATUS'); ?> -</option>
				<option value="<?php echo SUBJECT_STATE_PUBLISHED; ?>"<?php if ($this->lists['state'] === (string) SUBJECT_STATE_PUBLISHED) { ?> selected="selected"<?php } ?>><?php echo JText::_('PUBLISHED'); ?></option>
				<option value="<?php echo SUBJECT_STATE_UNPUBLISHED; ?>"<?php if ($this->lists['state'] === (string) SUBJECT_STATE_UNPUBLISHED) { ?> selected="selected"<?php } ?>><?php echo JText::_('UNPUBLISHED'); ?></option>
				<option value="<?php echo SUBJECT_STATE_ARCHIVED; ?>"<?php if ($this->lists['state'] === (string) SUBJECT_STATE_ARCHIVED) { ?> selected="selected"<?php } ?>><?php echo JText::_('ARCHIVED'); ?></option>
				<option value="<?php echo SUBJECT_STATE_DELETED; ?>"<?php if ($this->lists['state'] === (string) SUBJECT_STATE_DELETED) { ?> selected="selected"<?php } ?>><?php echo JText::_('TRASHED'); ?></option>
			</select>
			<select name="filter_featured" id="filter_featured" class="input-medium" onchange="this.form.submit()">
				<option value="">- <?php echo JText::_('ITEM_FEATURED'); ?> -</option>
				<option value="<?php echo SUBJECT_FEATURED; ?>"<?php if ($this->lists['featured'] === (string) SUBJECT_FEATURED) { ?> selected="selected"<?php } ?>><?php echo JText::_('IS_FEATURED'); ?></option>
				<option value="<?php echo SUBJECT_NOFEATURED; ?>"<?php if ($this->lists['featured'] === (string) SUBJECT_NOFEATURED) { ?> selected="selected"<?php } ?>><?php echo JText::_('IS_NO_FEATURED'); ?></option>
			</select>
		</div>
	</fieldset>
	<?php if ($element) { ?>
		<p style="text-align: center"><strong style="color: red">
			<?php 
				if ($this->type == SUBJECT_PARENT) {
					echo JText::_('YOU_CAN_ONLY_SELECT_PARENT_SUBJECTS_FOR_SUBJECTS_LIST');
				} elseif ($this->type == SUBJECT_BOOKABLE && !$config->parentsBookable) {
					echo JText::_('YOU_CAN_ONLY_SELECT_NO_PARENT_SUBJECTS_FOR_SUBJECT_DETAIL');
				} 
			?>
		</strong></p>
	<?php } ?>
	<div id="editcell">
		<table class="adminlist table-striped table" cellspacing="1">
			<thead>
				<tr>
					<th width="1%">#</th>
					<?php if (! $this->selectable) { ?>
						<th width="1%">
														
							<input type="checkbox" class="inputCheckbox" name="toggle" value="" onclick="checkAll(2);" />
							
						</th>
					<?php } ?>
					<th width="1%">&nbsp;</th>
					<th>
				        <?php echo JHTML::_('grid.sort', 'Title', 'title', $orderDir, $order); ?>
					</th>
					<th><?php echo JText::_('TEMPLATE'); ?></th>
					<th width="1%">
				        <?php echo JHTML::_('grid.sort', 'State', 'state', $orderDir, $order); ?>
					</th>
					<th width="1%">
				        <?php echo JHTML::_('grid.sort', 'Featured', 'featured', $orderDir, $order); ?>
					</th>
					<?php if (! $this->selectable) { ?>
						<th width="12%">
				        	<?php 
				        		echo JHTML::_('grid.sort', 'Order', 'ordering', $orderDir, $order);
				        		if ($this->turnOnOrdering) {
									echo JHTML::_('grid.order', $this->items);
								} 
							?>
						</th>
					<?php } ?>	
					<th width="1%">
						<?php echo JHTML::_('grid.sort', 'Access', 'access', $orderDir, $order); ?>
					</th>
					<?php if (! $this->selectable) { ?>
						<th width="1%">
							<?php echo JHTML::_('grid.sort', 'Hits', 'hits', $orderDir, $order); ?>
						</th>
					<?php } ?>
					<th width="1%">
				        <?php echo JHTML::_('grid.sort', 'ID', 'id', $orderDir, $order); ?>
					</th>
                    <?php if (! $this->selectable) { ?>
                        <th width="1%">
                            <?php echo JText::_('ADD_RESERVATION'); ?>
                        </th>
                    <?php } ?>
				</tr>
			</thead>
			<tfoot>
    			<tr>
    				<td colspan="<?php echo $colspan; ?>">
    				    <?php 
    				    	    				    	
    				    	echo '&nbsp;';
    				    	
    				    ?>
    				</td>
    			</tr>
			</tfoot>
			<tbody>
				<?php if (! is_array($this->items) || ! $itemsCount && $this->tableTotal) { ?>
					<tr><td colspan="<?php echo $colspan; ?>" class="emptyListInfo"><?php echo JText::_('NO_ITEMS_FOUND'); ?></td></tr>
					
					
				<?php } elseif(! $this->tableTotal || !JFactory::getUser()->authorise('booking.item.manage', 'com_booking')) { ?>
				
				
					<tr><td colspan="<?php echo $colspan; ?>" class="emptyListInfo"><?php echo JText::_('EMPTY_OBJECT_LIST_INFO'); ?></td></tr>
				<?php
					} else {
												
						for ($i = 0; $i < 2; $i++) {
							if (! isset($this->items[$i])) {
								continue;
							}
						
				    		$subject = &$this->items[$i];
				    		$subjectTable->bind($subject);
				    		$title = $this->escape($subject->title);
				    		$titleEdit = $editSubject . '::' . $title;
				    		$template = &$this->templateHelper->getTemplateById($subject->template);
				    		$link = JRoute::_(ARoute::edit(CONTROLLER_SUBJECT, $subject->id));
				    		$input = isset($this->input) ? $this->input : 'false';
				    		$js = 'javascript:ListSubjects.select(' . $subject->id . ',\'' . $title . '\',\'' . $this->escape($subject->alias) . '\',\''.$this->escape($input).'\')';
				    		$isCheckedOut = JTable::isCheckedOut($userId, $subject->checked_out); 
				?>
				    	<tr class="row<?php echo ($i % 2); ?>">
				    		<td  style="text-align: right; white-space: nowrap;"><?php echo number_format($this->pagination->getRowOffset($i), 0, '', ' '); ?></td>
				    			<?php if (! $this->selectable) { ?>
				    				<td class="checkboxCell"><?php echo JHTML::_('grid.checkedout', $subject, $i); ?></td>
				    			<?php } ?>
				    		<td>
					    		<?php
					    			if (isset($subjectTable->image) && ($thumb = AImage::thumb($ipath . $subjectTable->image, 30, 30))) {
					    				if (! $this->selectable) {
					    					if ($isCheckedOut) {
					    		?>				    					
					    						<img src="<?php echo $thumb; ?>" alt="" />
					    		<?php 
					    					} else {
					    		?>
					    						<a href="<?php echo $link; ?>" title="<?php echo $titleEdit; ?>"><img src="<?php echo $thumb; ?>" alt="" /></a>
					    		<?php 
					    					}
					    				} else {
								?>
					    					<a href="<?php echo $js; ?>" title="<?php echo $appendSubject; ?>"><img src="<?php echo $thumb; ?>" alt="" /></a>
					    		<?php
					    				}
					    			} else 
					    				echo '&nbsp;'; 
					    		?>
				    		</td>
				    		<td>
				    			<?php 
				    				if (! $this->selectable) {
				    					if (! $isCheckedOut) { 
				    			?>
				                			<span class="editlinktip hasTip" title="<?php echo $titleEdit; ?>">
												<a href="<?php echo $link; ?>" title=""><?php echo $subject->treename; ?></a>
						            		</span>
						        <?php 
				    					} else                    
						        			echo $subject->treename;
						        	} else {
						        		if ((($this->type == SUBJECT_BOOKABLE && ($config->parentsBookable || $subject->children == 0)) || ($this->type == SUBJECT_PARENT && $subject->children > 0)) && $subject->state == SUBJECT_STATE_PUBLISHED) { 
						        ?>
						        			<a href="<?php echo $js; ?>" title="<?php echo $appendSubject; ?>"><?php echo $subject->treename; ?></a>
						        <?php 
						        		} else
						        			echo $subject->treename;
						        	} 
						        ?>
				    		</td>
				    		<td>
				    			<?php 
				    				if ($template && ! $element) { 
				    			?>
				    				<a href="<?php echo JRoute::_(ARoute::edit(CONTROLLER_TEMPLATE, $template->id)); ?>" title="<?php echo $editTemplate; ?>"><?php echo $template->name; ?></a>
				    			<?php 
				    				} elseif ($template)
				    					echo $template->name;
				    				else
				    					echo $notFound;
				    			?>
				    		</td>
				    		<td style="text-align: center;"><?php echo AHtml::state($subject, $i, ! $element); ?></td>
				    		<td class="center">
								<?php if ($subject->featured) {
                                    if (! $this->selectable) { ?>
                                        <a title="<?php echo JText::_('UNFEATURE'); ?>" onclick="return listItemTask('cb<?php echo $i; ?>', 'unfeature')" href="#">
                                    <?php }
                                            echo JHtml::_('image', 'admin/featured.png', JText::_('FEATURED'), null, true); 
                                        if (! $this->selectable) { ?>
                                        </a>
                                    <?php }
                                    } else { 
                                        if (! $this->selectable) { ?>
                                            <a title="<?php echo JText::_('FEATURE'); ?>" onclick="return listItemTask('cb<?php echo $i; ?>', 'feature')" href="#">
                                        <?php }
                                                echo JHtml::_('image', 'admin/disabled.png', JText::_('UNFEATURED'), null, true);
                                            if (! $this->selectable) { ?>
                                            </a>
                                        <?php }                                            
                                    } ?>
							</td>
				    		<?php if (! $this->selectable) { ?>
				    			<td class="order" align="right" style="text-align: right;"><?php echo AHtml::orderTree($this->items,$i, $this->pagination, $this->turnOnOrdering, $itemsCount); ?></td>
				    		<?php } ?>
				    		<td style="text-align: center;">
				    			<?php 
				    				if ($element)
				    					echo AHtml::noActiveAccess($subject, $i, $subject->state);
				    				else {
				    					if (ISJ16)
				    						echo $subject->groupname;
				    					else
				    						echo JHTML::_('grid.access', $subject, $i, $subject->state);
				    				} 
				    			?>
				    		</td>
				    		<?php if (! $this->selectable) { ?>
				    			<td style="text-align: right; white-space: nowrap;"><?php echo ((int) $subject->hits == 0) ? '-' : number_format($subject->hits, 0, '', ' '); ?></td>
				    		<?php } ?>
				    		<td style="text-align: right; white-space: nowrap;"><?php echo number_format($subject->id, 0, '', ' '); ?></td>
				    		<?php if (! $this->selectable) { ?>
				    			<td align="center">
				    				<?php echo JHtml::link(JRoute::_('index.php?option=com_booking&view=subject&layout=calendar&cid[]=' . $subject->id.'&id=' . $subject->id), JHtml::image('components/com_booking/assets/images/icon-16-calendar.png', JText::_('CALENDAR'))); ?>
				    			</td>
				    		<?php } ?>
				    	</tr>
				    <?php 
				    	}
					} 
					?>
			</tbody>
		</table>
		<table align="center">
			<tr align="center">
				<td class="aIconLegend aIconPending"><?php echo JText::_('PENDING'); ?><span class="aIconSeparator">&nbsp;</span></td>
				<td class="aIconLegend aIconPublished"><?php echo JText::_('PUBLISHED'); ?><span class="aIconSeparator">&nbsp;</span></td>
				<td class="aIconLegend aIconExpired"><?php echo JText::_('EXPIRED'); ?><span class="aIconSeparator">&nbsp;</span></td>
				<td class="aIconLegend aIconUnpublish"><?php echo JText::_('UNUBLISHED'); ?><span class="aIconSeparator">&nbsp;</span></td>
				<td class="aIconLegend aIconArchived"><?php echo JText::_('ARCHIVED'); ?><span class="aIconSeparator">&nbsp;</span></td>
				<td class="aIconLegend aIconTrash"><?php echo JText::_('TRASHED'); ?></td>
			</tr>
		</table>
		<div class="clr"></div>
	</div>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="task" value="<?php echo JRequest::getCmd('task'); ?>"/>
	<input type="hidden" name="type" value="<?php echo $this->type; ?>"/>
	<?php if (isset($this->input)) {?>
	<input type="hidden" name="input" value="<?php echo $this->input; ?>"/>
	<?php }?>
	<?php 
		$tmpl = JRequest::getCmd('tmpl');
		if ($tmpl) { 
	?>
			<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>"/>
	<?php 
		} 
	?>
	<input type="hidden" name="reset" value="0"/>
	<input type="hidden" name="template" value="0"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_SUBJECT; ?>"/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $orderDir; ?>"/>
	<input type="hidden" name="<?php echo SESSION_TESTER; ?>" value="1"/>
	<?php echo JHTML::_('form.token'); ?>
</form>	
