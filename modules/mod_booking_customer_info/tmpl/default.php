<?php 

/**
 * Display module with information about logged customer
 * and URLs to create new registration, login exists customer,
 * display customers profile and display customers reservations. 
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  modules
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

$user = &JFactory::getUser();
/* @var $user JUser */

$modelCustomer = new BookingModelCustomer();
$modelCustomer->setIdByUserId();

$isAdmin = $modelCustomer->isAdmin();

?>
<div class="customersInfo<?php echo $moduleclassSfx; ?>">
	<?php 
	if ($user->id) { 
		 if ($modelCustomer->getId()) { 
	?> 
			<div class="customersName">
				<strong><?php echo JText::_('CUSTOMER'); ?>: </strong>
				<a href="<?php echo JRoute::_(ARoute::editUser()); ?>" title="<?php echo JText::_('EDIT_USER_ACOUNT'); ?>">
					<?php echo $user->username; ?>
				</a>
			</div>
			<div class="customersProfile">
				<a href="<?php echo JRoute::_(ARoute::view(VIEW_CUSTOMER)); ?>" title="<?php echo JText::_('DISPLAY_CUTOMER_PROFILE'); ?>">
					<?php echo JText::_('PROFILE'); ?>
				</a>
			</div>
			<div class="customersReservations">
				<a href="<?php echo JRoute::_(ARoute::viewlayout(VIEW_RESERVATIONS, 'customer')); ?>" title="<?php echo JText::_('DISPLAY_CUSTOMER_RESERVATIONS'); ?>">
					<?php echo JText::_('RESERVATIONS'); ?>
				</a>
			</div>
		<?php 
		 	} elseif ($isAdmin) { 
		 ?>
			<div class="loggedAdmin">
				<strong><?php echo JText::_('LOGGED_ADMINISTRATOR'); ?></strong>
				<a href="<?php echo JRoute::_(ARoute::editUser()); ?>" title="<?php echo JText::_('EDIT_USER_ACOUNT'); ?>">
					<?php echo $user->username; ?>
				</a>
			</div>
			<div class="reservations">
				<a href="<?php echo JRoute::_(ARoute::viewlayout(VIEW_RESERVATIONS, 'admin')); ?>" title="<?php echo JText::_('MANAGE_RESERVATIONS'); ?>">
					<?php echo JText::_('MANAGE_RESERVATIONS'); ?>
				</a>
			</div>
		<?php } else { ?>
			<div class="noLoggedCustomerOrAdmin">
				<strong><?php echo JText::_('NO_LOGGED_CUSTOMER_OR_ADMIN'); ?></strong>
				<a href="<?php echo JRoute::_(ARoute::editUser()); ?>" title="<?php echo JText::_('EDIT_USER_ACOUNT'); ?>">
					<?php echo $user->username; ?>
				</a>
			</div>
		<?php } ?>
			<div class="customerLogout">
				<a href="<?php echo JRoute::_(ARoute::logoutUser()); ?>" title="<?php echo JText::_('LOGOUT_CUSTOMER_FROM_SYSTEM'); ?>">
					<?php echo JText::_('LOGOUT'); ?>				
				</a>
			</div>
	<?php } else { ?>
		<div class="customerNoLogged">
			<strong><?php echo JText::_('CUSTOMER_NO_LOGGED'); ?></strong>
		</div>
		<div class="customerLogin">
			<a href="<?php echo JRoute::_(ARoute::loginUser()); ?>" title="<?php echo JText::_('LOGIN_CUTOMER'); ?>">
				<?php echo JText::_('LOGIN'); ?>
			</a>
		</div>
		<div class="customerRegistration">
			<a href="<?php echo JRoute::_(ARoute::edit(CONTROLLER_CUSTOMER)); ?>" title="<?php echo JText::_('CREATE_NEW_CUSTOMER_REGISTRATION'); ?>">
				<?php echo JText::_('NEW_REGISTRATION'); ?>
			</a>
		</div>
	<?php } ?>
    <?php if ($reservedItems) { ?>
		<div class="customersCurrentReservation">
            <?php $title = JText::_('CURRENT_RESERVATION');
            $text = '';
            foreach ($reservedItems as $reservedItem) {
                $text .= htmlspecialchars($reservedItem->subject_title . ', '.  AHtml::interval($reservedItem));
                if ($reservedItem->fullPriceSupplements > 0) {
                    $text .= ', <strong>' . htmlspecialchars(BookingHelper::displayPrice($reservedItem->fullPriceSupplements)) . '</strong>';
                }
                $text .= '<br>';
            } 
            if (count($reservedItems) > 1 && $fullPrice > 0) {
                $text .= '<strong>' . JText::_('FULL_PRICE') . ': ' . BookingHelper::displayPrice($fullPrice) . '</strong>';
            }
            ?>
            <a href="<?php echo JRoute::_(ARoute::viewlayout(VIEW_RESERVATION, 'form')); ?>" title="<?php echo $title.'::'.$text; ?>" class="hasTip">
                <?php echo JText::_('CURRENT_RESERVATION') . ($fullPrice ? ': ' . BookingHelper::displayPrice($fullPrice) : ''); ?>
			</a>
		</div>
    <?php } ?>
</div>