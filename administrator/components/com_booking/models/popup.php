<?php

/**
 * @copyright	  	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class BookingModelPopup extends JModelLegacy {

    public function getReservation() {
        $db = JFactory::getDbo();
        return $db->setQuery('SELECT id, title_before, firstname, middlename, surname, title_after, street, city, country, zip, email, telephone FROM #__booking_reservation WHERE id = ' . $db->q(JRequest::getInt('id')))->loadObject();
    }

    public function getSubject() {
        $db = JFactory::getDbo();
        return $db->setQuery('SELECT subject FROM #__booking_reservation_items WHERE reservation_id = ' . $db->q(JRequest::getInt('id')) . ' GROUP BY subject')->loadColumn();
    }

}
