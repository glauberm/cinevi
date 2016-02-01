<?php

/**
 * @copyright	  	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class BookingViewPopup extends JViewLegacy {

    public function display($tpl = null) {
        $user = JFactory::getUser();
        foreach ($this->get('subject') as $subject) {
            if ($user->authorise('booking.show.reservations.popup', 'com_booking') || $user->authorise('booking.show.reservations.popup', 'com_booking.subject.' . $subject)) {
                $this->reservation = $this->get('reservation');
                parent::display($tpl);
                return;
            }
        }
    }

}
