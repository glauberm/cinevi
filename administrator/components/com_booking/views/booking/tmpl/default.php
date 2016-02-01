<?php

/**
 * Component administration control panel template 
 * with buttons to open main parts of component.
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

	

/* @var $this BookingViewBooking */

JToolBarHelper::title(JText::_(COMPONENT_NAME).": ".JText::_('CONTROL_PANEL'), 'bookit');
BookingHelper::setSubmenu(0);
if (JFactory::getUser()->authorise('core.admin', 'com_booking'))
	JToolBarHelper::preferences('com_booking');

$data = JInstaller::parseXMLInstallFile(MANIFEST);
$config = AFactory::getConfig();

?>
<div>
	<div class="col" style="width: 65%">
   		<div id="cpanel" class="icons">
   		<?php 
   			echo JHtml::_('tabs.start', 'tabone', array('startOffset' => JRequest::getInt('startOffset'), 'useCookie' => JRequest::getInt('startOffset') == 0));
   			if (JFactory::getUser()->authorise('booking.reservations.manage', 'com_booking') || JFactory::getUser()->authorise('booking.view.customers', 'com_booking')) {
   				echo JHtml::_('tabs.panel', JText::_('RESERVATION_MANAGEMENT'), 'reservationmanagement');
   				if (JFactory::getUser()->authorise('booking.reservations.manage', 'com_booking'))
   					echo AHtml::getCPanelButton(JRoute::_(ARoute::view(VIEW_RESERVATIONS)), 'reservation', JText::_('RESERVATIONS'), true);
   				if (JFactory::getUser()->authorise('booking.view.customers', 'com_booking')) 
   					echo AHtml::getCPanelButton(JRoute::_(ARoute::view(VIEW_CUSTOMERS)), 'customer', JText::_('CUSTOMERS'), true); 
   		?>
   				<div class="clr"></div>
   		<?php } ?>	
   			
   		<?php 
   			if (JFactory::getUser()->authorise('booking.item.manage', 'com_booking')) {		
	   			echo JHtml::_('tabs.panel', JText::_('BOOKABLE_ITEMS'), 'bookableitems');
	   			echo AHtml::getCPanelButton(JRoute::_(ARoute::view(VIEW_SUBJECTS)), 'object', JText::_('BOOKABLE_ITEMS'), true);
	   			echo AHtml::getCPanelButton(JRoute::_(ARoute::view(VIEW_TEMPLATES)), 'template', JText::_('ITEM_TEMPLATES'), true);
	   			echo AHtml::getCPanelButton(JRoute::_(ARoute::view(VIEW_ADMINS)), 'cbadmins', JText::_('MANAGERS'), true);
	   			echo AHtml::getCPanelButton(JRoute::_(ARoute::view(VIEW_CLOSINGDAYS)), 'closingday', JText::_('CLOSING_DAYS'), true);
	   	?>
	   			<div class="clr"></div>
   		<?php } 
   		
   			echo JHtml::_('tabs.panel', JText::_('GOOGLE'), 'google');
   			echo AHtml::getCPanelButton(JRoute::_('index.php?option=com_booking&task=google.synchronizeevents'), 'googleevents', JText::_('GOOGLE_SYNCHRONIZE_EVENTS'), true, array(), JText::_('GOOGLE_SYNCHRONIZE_EVENTS_DESC'));
   			echo AHtml::getCPanelButton(JRoute::_('index.php?option=com_booking&task=google.loadcalendars'), 'googlecalendars', JText::_('GOOGLE_LOAD_CALENDARS'), true, array(), JText::_('GOOGLE_LOAD_CALENDARS_DESC'));
   		?>
   				<div class="clr"></div>
   		<?php 
   			if (JFactory::getUser()->authorise('core.admin', 'com_booking')) {	
	   		 	echo JHtml::_('tabs.panel', JText::_('CONFIGURATION'), 'configuration');
	   		 	echo AHtml::getCPanelButton($this->configRoute['route'], 'configuration', JText::_('Configuration'), true, $this->configRoute['params']);
	   				   			echo AHtml::getCPanelButton(JRoute::_(ARoute::view(VIEW_EMAILS)), 'email', JText::_('EMAIL_TEMPLATES'), true);
	   			echo AHtml::getCPanelButton(JRoute::_(ARoute::view(VIEW_ARTICLES)), 'article', JText::_('TERMS_ARTICLES'), true);
	   				   			echo AHtml::getCPanelButton(JRoute::_(ARoute::view(VIEW_LOCATIONS)), 'location', JText::_('LOCATIONS'), true);
	   				   			echo AHtml::getCPanelButton(JRoute::_(ARoute::view(VIEW_UPGRADE)), 'upgrade', JText::_('CHECK_UPDATES'), true);
	   				   	?>
	   			<div class="clr"></div>
   		<?php } ?>	
   			
   		<?php echo JHtml::_('tabs.panel', JText::_('HELP_AND_SUPPORT'), 'helpsupport'); ?>
   			<!-- Documentation -->
   			<?php echo AHtml::getCPanelButton($this->info['documentation'], 'documentation', JText::_('DOCUMENTATION'), true, array('target' => '_blank'), JText::_('DOCUMENTATION_DESC')); ?>
	        
	        <!-- Changelog -->
	        <?php echo AHtml::getCPanelButton(JRoute::_(ARoute::view2layout(null, 'changelog')), 'changelog', JText::_('CHANGELOG'), true, array(), JText::_('CHANGELOG_DESC')); ?>
	        
	        <!-- FAQs -->
	        <?php echo AHtml::getCPanelButton($this->info['faq'], 'faq', JText::_('FAQ'), true, array('target' => '_blank'), JText::_('FAQ_DESC')); ?>
	        
	        <!-- Tutorial Videos -->
	        <?php echo AHtml::getCPanelButton($this->info['video'], 'video', JText::_('TUTORIAL_VIDEOS'), true, array('target' => '_blank'), JText::_('TUTORIAL_VIDEOS_DESC')); ?>
	        
	        <!-- Support Forums -->
	        <?php echo AHtml::getCPanelButton($this->info['forum'], 'forum', JText::_('SUPPORT_FORUMS'), true, array('target' => '_blank'), JText::_('SUPPORT_FORUMS_DESC')); ?>
	        
	        <!-- Paid Support -->
	        <?php echo AHtml::getCPanelButton($this->info['paidsupport'], 'support', JText::_('PAID_SUPPORT'), true, array('target' => '_blank'), JText::_('PAID_SUPPORT_DESC')); ?>
	        
	        <div class="clr"></div>
   			<?php echo JHtml::_('tabs.end'); ?>
   		</div>
   		<?php if (JFactory::getUser()->authorise('booking.reservations.manage', 'com_booking')) { ?>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
        <fieldset class="form-horizontal">
		    <legend class="titlePage"><?php echo JText::_('ACTUAL_RESERVATIONS'); ?></legend>
			<div id="editcell">
				
				<table class="adminlist table table-striped" cellspacing="1">
					<thead>
						<tr>
							<th width="1%" nowrap="nowrap">
								<?php echo JHTML::_('grid.sort', 'Res_num', 'id', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							<th>
								<?php echo JHTML::_('grid.sort', 'Customer', 'surname', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							<th>
								<?php echo JHTML::_('grid.sort', 'Item', 'items-subject_title', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							<th width="1%" colspan="2">
								<?php echo JHTML::_('grid.sort', 'From', 'items-from', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							<th width="1%" colspan="2">
								<?php echo JHTML::_('grid.sort', 'To', 'items-from', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							<?php if ($config->usingPrices) { ?>
								<th width="1%" nowrap="nowrap">
					        		<?php echo JHTML::_('grid.sort', 'Price', 'reservationFullPrice', $this->lists['order_Dir'], $this->lists['order']); ?>
								</th>
								<th width="1%">
						        	<?php echo JHTML::_('grid.sort', 'Payment_Status', 'paid', $this->lists['order_Dir'], $this->lists['order']); ?>
								</th>
							<?php } ?>
							<th width="1%">
					        	<?php echo JHTML::_('grid.sort', 'Reservation_Status', 'state', $this->lists['order_Dir'], $this->lists['order']); ?>
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
						<?php if (count($this->items) == 0) { ?>
							<tr>
							    <td></td>
								<td colspan="10" class="emptyListInfo"><?php echo JText::_('NO_PENDING_RESERVATIONS'); ?></td>
							</tr>
						<?php } ?>
						<?php 
							foreach ($this->items as $i => $reservation) {							
								foreach ($this->reservedItems[$reservation->id] as $j => $reservedItem) {
								
							?>
							<tr class="row<?php echo $i % 2; ?>">
								<?php if ($j == 0){ ?>
					    		<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>">
					    			<a href="<?php echo JRoute::_(ARoute::detail(CONTROLLER_RESERVATION, $reservation->id)); ?>" title="<?php echo $this->escape(JText::_('SHOW_RESERVATION')); ?>::<?php echo $this->escape($reservation->id); ?>" class="hasTip">
					    				<?php echo $reservation->id; ?>
					    			</a>
					    		</td>
								<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>">
									<?php if ($reservation->customer) { ?>
					    				<a href="<?php echo JRoute::_(ARoute::detail(CONTROLLER_CUSTOMER, $reservation->customer)); ?>" title="<?php echo $this->escape(JText::_('SHOW_CUSTOMER')); ?>::<?php echo $this->escape(BookingHelper::formatName($reservation)); ?>" class="hasTip">
					    					<?php echo BookingHelper::formatName($reservation); ?>
					    				</a>
					    			<?php } else { ?>
					    				<span title="<?php echo $this->escape(JText::_('UNREGISTERED_CUSTOMER')); ?>">
					    					<?php echo BookingHelper::formatName($reservation); ?>
					    				</span>
					    			<?php } ?>
					    		</td>
					    		<?php } ?>
					    		<td>
					    			<a href="<?php echo JRoute::_(ARoute::edit(CONTROLLER_SUBJECT, $reservedItem->subject)); ?>" title="<?php echo $this->escape(JText::_('SHOW_ITEM')); ?>::<?php echo $this->escape($reservedItem->subject_title); ?>" class="hasTip">
					    				<?php echo $reservedItem->subject_title; ?>
					    			</a>
					    		</td>
					    		<td nowrap="nowrap" align="center" width="1%"><?php echo AHtml::date($reservedItem->from, ADATE_FORMAT_NORMAL, 0); ?></td>
				    			<td nowrap="nowrap" align="center" width="1%"><?php echo AHtml::date($reservedItem->from, ATIME_FORMAT_SHORT, 0); ?></td>
				    			<td nowrap="nowrap" align="center" width="1%"><?php echo AHtml::date($reservedItem->to, ADATE_FORMAT_NORMAL, 0); ?></td>
				    			<td nowrap="nowrap" align="center" width="1%"><?php echo AHtml::date($reservedItem->to, ATIME_FORMAT_SHORT, 0); ?></td>
					    		<?php if ($j == 0) { ?>
					    			<?php if ($config->usingPrices) { ?>
					    				<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>" nowrap="nowrap" align="right"><?php echo BookingHelper::displayPrice($reservation->reservationFullPrice); ?></td>
					    				<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>">
					    					<?php echo AHtml::renderReservationPaymentStateIcon($reservation);?>
					    				</td>
					    			<?php } ?>
					    		<td rowspan="<?php echo count($this->reservedItems[$reservation->id]); ?>">
					    			<?php echo AHtml::renderReservationStateIcon($reservation);?>
					    		</td>
					    		<?php } ?>
					    	</tr>
					    	<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="reset" value="0"/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
			<input type="hidden" name="help_controller" value="<?php echo CONTROLLER_RESERVATION; ?>"/>
			<?php echo JHTML::_('form.token'); ?>
		</fieldset>
		</form>
		<?php } ?>
	</div>			
	<div class="col width-35" style="width: 35%">
		<fieldset class="adminform">
			<legend>ARTIO Booking</legend>
			<table class="adminlist table table-striped">
				
				<tr>
					<th></th>
					<td>
						<p class="buy"><?php echo JText::_('THIS_IS_FREE_VERSION_WITH_OBJECTS_COUNT_LIMITED_TO_2'); ?></b></p>
		      		.	<a class="aIconBuy" target="_blank" href="http://www.artio.net/e-shop/bookit" title=""><?php echo JText::_('BUY_FULL_VERSION'); ?></a>
					</td>
				</tr>
				
		   		<tr>
					<th></th>
					<td>
		      			<a href="http://www.artio.net" target="_blank">
		          			<img src="<?php echo IMAGES; ?>logo-80.png" align="middle" alt="<?php echo COMPONENT_NAME; ?>" style="border: none; margin: 8px;" />
		        		</a>
					</td>
				</tr>
		   		<tr>
		      		<th width="120"></th>
		      		<td><a href="http://www.artio.net/joomla-extensions/booking-and-reservation" target="_blank">ARTIO Booking</a></td>
		   		</tr>	
		   		<tr>
		      		<th><?php echo JText::_('VERSION'); ?>:</th>
		      		<td><?php echo $data['version']; ?></td>
		   		</tr>
		   		<tr>
		      		<th><?php echo JText::_('DATE'); ?>:</th>
		      		<td><?php echo JHTML::date(isset($data['creationdate']) ? $data['creationdate'] : $data['creationDate'], ADATE_FORMAT_NORMAL); ?></td>
		   		</tr>
		   		<tr>
		      		<th valign="top"><?php echo JText::_('COPYRIGHT'); ?>:</th>
		      		<td><?php echo $data['copyright']; ?></td>
		   		</tr>
		   		<tr>
		      		<th><?php echo JText::_('AUTHOR'); ?>:</th>
		      		<td nowrap="nowrap"><a href="<?php echo $data['authorUrl']; ?>" target="_blank"><?php echo $data['author']; ?></a>,
		      		<a href="mailto:<?php echo $data['authorEmail']; ?>"><?php echo $data['authorEmail']; ?></a></td>
		   		</tr>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
</div>
