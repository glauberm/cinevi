<?php
/**
 * @package ARTIO Booking
 * @subpackage views
 * @copyright Copyright (C) 2014 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();
$config = AFactory::getConfig();
JHtml::_('behavior.modal');

$this->popup = false;
if ($this->getLayout() != 'manager') {
    if (!empty($this->box->customerName)) {
        foreach ((array) $this->box->customerName as $item) {
            $popup = $user->authorise('booking.show.reservations.popup', 'com_booking.subject.' . $item['item']);
            $page = $user->authorise('booking.show.reservations', 'com_booking.subject.' . $item['item']);

            if ($popup) {
                $class = array('price', 'customer', 'modal', 'popupTrigger');
                $title = $style = '';
                
                if ($page && !$config->whoReserveShowType) {
                    $title = 'title="' . $this->escape($item['name']) . '"';
                }
                
                $class = 'class="' . implode(' ', $class) . '"';
                
                if ($config->colorCalendarBoxReserved) {
                    $style = 'style="background-color: ' . $config->colorCalendarBoxReserved . '"';
                }
                ?> 
                <a href="<?php echo JRoute::_('index.php?option=com_booking&view=popup&tmpl=component&id=' . $item['reservation_id']); ?>" <?php echo $class . ' ' . $title . ' ' . $style; ?> rel="{handler: 'iframe', size: {x: 600, y: 600}}">
                    <?php
                    if ($page && $config->whoReserveShowType) {
                        echo $item['name'];
                    }
                    ?>
                </a>                                               
                <?php
                $this->popup = true;
            }
        }
    }
}