<?php

/**
 * Reservation model. Support for database operations.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  models
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

//import needed JoomLIB helpers
AImporter::helper('booking', 'model');

class BookingModelReservationItem extends AModel
{
    
    /**
     * Main table
     * 
     * @var TableReservationItems
     */
    var $_table;

    function __construct()
    {
        parent::__construct();
        $this->_table = $this->getTable('reservationitems');
    }

    /**
     * Save item and it's supplements.
     * 
     * @param array $data request data including supplements property
     * @return customer id if success, false in unsuccess
     */
	function store($data)
    {
        $config = AFactory::getConfig();
        
    	if (empty($data['id']))
    		$this->_table->id = null; //dont ask why
    		
        if (! $this->_table->bind($data)) {
            return false;
        }    		
    		
        if ($config->parentsBookable == 2) {
            if (empty($this->_table->id) && empty($this->_table->sub_subject)) { // select one of children as sub subject
                
                $booked = JModelLegacy::getInstance('ReservationItems', 'BookingModel')->getSimpleData($this->_table->subject, $this->_table->from, $this->_table->to);
                $bookable = JModelLegacy::getInstance('Subjects', 'BookingModel')->init(array('parent' => $this->_table->subject, 'access' => AModel::getAccess()))->getData();
                
                foreach ($bookable as $i => $kid) {
                    foreach ($booked as $child) { // check if available sub subject is already booked 
                        if ($kid->id == $child->sub_subject || $kid->id == $this->_table->subject) { // sub subject is already booked or is parent
                            unset($bookable[$i]);
                            break;
                        }
                    }
                    if (isset($bookable[$i])) { // check if available sub subject is already closed
                        $closed = JModelLegacy::getInstance('Closingdays', 'BookingModel')->getSubjectClosingDays($kid->id); // sub subject closing days
                        foreach ($closed as $day) {
                            if ($day->down >= $this->_table->from && $day->up <= $this->_table->to) {
                                unset($bookable[$i]);
                                break;
                            }
                        }
                    }
                }
                
                if (!empty($bookable)) { // select sub item randomise
                    $kid = array_rand($bookable);
                    $this->_table->sub_subject = $bookable[$kid]->id;
                    $this->_table->sub_subject_title = $bookable[$kid]->title;
                }
            } else { // resave existing reservation item
                $subsubject = JModelLegacy::getInstance('Subject', 'BookingModel')->setId($this->_table->sub_subject)->getObject(); // search selected sub subject
                
                $this->_table->sub_subject = $subsubject ? $subsubject->id : null;
                $this->_table->sub_subject_title = $subsubject ? $subsubject->title : null;
                
                if (!empty($this->_table->sub_subject)) { 
                    $booked = JModelLegacy::getInstance('ReservationItems', 'BookingModel')->getSimpleData($this->_table->subject, $this->_table->from, $this->_table->to);
                    
                    foreach ($booked as $child) { // check if sub subject is already used in another reservation
                        if ($child->sub_subject == $this->_table->sub_subject && $child->id != $this->_table->id) {
                             JFactory::getApplication()->enqueueMessage(JText::sprintf('SUB_ITEM_USED', $this->_table->sub_subject_title, AHtml::interval($this->_table)), 'error');
                             $this->_table->sub_subject = $this->_table->sub_subject_title = null;       
                             break;
                        }
                    }
                }
                
                if (!empty($this->_table->sub_subject)) {
                    $closed = JModelLegacy::getInstance('Closingdays', 'BookingModel')->getSubjectClosingDays($this->_table->sub_subject);
                    foreach ($closed as $day) { // check if selected sub subject is closed
                        if ($day->down >= $this->_table->from && $day->up <= $this->_table->to) {
                            JFactory::getApplication()->enqueueMessage(JText::sprintf('SUB_ITEM_CLOSED', $this->_table->sub_subject_title, AHtml::interval($this->_table)), 'error');
                            $this->_table->sub_subject = $this->_table->sub_subject_title = null;
                            break;
                        }
                    }
                }
            }
        }
        
        $oldSubSubject = $this->_db->setQuery('SELECT sub_subject FROM #__booking_reservation_items WHERE id = ' . (int) $this->_table->id)->loadResult();
    		
        if (! $this->_table->check() || ! $this->_table->store()) {
            return false;
        }
        
        if (!empty($data['id']) && $oldSubSubject && $this->_table->sub_subject && $oldSubSubject != $this->_table->sub_subject) {
            // sub subject has been changed - alert managers
            if ($config->mailingChangeSubsubjectOld) {            
                $email = JModelLegacy::getInstance('Email', 'BookingModel')->getItem($config->mailingChangeSubsubjectOld);
                if ($email) {
                    $data = new stdClass();
                    $data->subject = $oldSubSubject;
                    $managers = AUser::getNotificationManagers('booking.reservations.manage', array($data), false);
                    JFactory::getApplication()->enqueueMessage($oldSubSubject);
                    if ($managers) {
                        JFactory::getApplication()->enqueueMessage(print_r($managers, true));
                        $email = JModelLegacy::getInstance('Email', 'BookingModel')->getItem($config->mailingChangeSubsubjectOld);
                        $email->subject = JModelLegacy::getInstance('Reservation', 'BookingModel')->replaceEmailSubject($email->subject, $this->_table);
                        $email->body = JModelLegacy::getInstance('Reservation', 'BookingModel')->replaceEmailBody($email->body, $this->_table, array());
                        JFactory::getMailer()->sendMail(JFactory::getApplication()->getCfg('mailfrom'), JFactory::getApplication()->getCfg('fromname'), $managers, $email->subject, $email->body, $email->mode);
                    }
                }
            }
            if ($config->mailingChangeSubsubjectNew) {
                $email = JModelLegacy::getInstance('Email', 'BookingModel')->getItem($config->mailingChangeSubsubjectNew);
                if ($email) {
                    $data = new stdClass();
                    $data->subject = $this->_table->sub_subject;
                    $managers = AUser::getNotificationManagers('booking.reservations.manage', array($data), false);
                    JFactory::getApplication()->enqueueMessage($this->_table->sub_subject);
                    if ($managers) {
                        JFactory::getApplication()->enqueueMessage(print_r($managers, true));
                        $email->subject = JModelLegacy::getInstance('Reservation', 'BookingModel')->replaceEmailSubject($email->subject, $this->_table);
                        $email->body = JModelLegacy::getInstance('Reservation', 'BookingModel')->replaceEmailBody($email->body, $this->_table, array());
                        JFactory::getMailer()->sendMail(JFactory::getApplication()->getCfg('mailfrom'), JFactory::getApplication()->getCfg('fromname'), $managers, $email->subject, $email->body, $email->mode);
                    }
                }
            }
        }
        
        JFactory::getCache('com_booking_acl', '')->clean();
        
        if (is_object($data))
        	ALog::add('Bad argument in admin/model/reservationitem.php store(). Argument is object, shoul be array',JLog::CRITICAL);

        if (array_key_exists('supplements',$data) && count($data['supplements']))  {
	        
	        $tableSupplement = &$this->getTable('reservationsupplement');
	        
	        /* @var $tableSupplement TableReservationSupplement */
	        $tableSupplement->reservation = $this->_table->id;
	        
	        // delete existing supplements - will save again
	        $query = $this->getDbo()->getQuery(true)->delete('#__booking_reservation_supplement')->where('reservation='.$this->_table->id);
	        $this->getDbo()->setQuery($query)->query();
	        
	        foreach ($data['supplements'] as $supplement) {
	        	
	            /* @var $supplement TableSupplement */
	            $tableSupplement->bind($supplement);
	            $tableSupplement->id = 0;
	            $tableSupplement->supplement = $supplement->id;
	            $tableSupplement->store();
	        }
        }
        
        return $this->_table->id;
    }
    
    /**
     * Remove one reservation item with all assets (supplement, period).
     * @param int $id reservation item id
     * @param int $rid reservation id
     * @return mixed false: disallow remove only item, true: removed 
     */
    public function removeitem($id, $rid)
    {
        if ($rid) { // saved reservation
            $id = $this->_db->q($id);
            $rid = $this->_db->q($rid);
            $this->_db->setQuery("SELECT COUNT(*) FROM #__booking_reservation_items WHERE reservation_id = $rid");
            if ($this->_db->loadResult() > 1) {
                return $this->_db->setQuery("DELETE i, s, p FROM #__booking_reservation_items AS i 
                                             LEFT JOIN #__booking_reservation_supplement AS s ON i.id = s.reservation 
    			                             LEFT JOIN #__booking_reservation_period AS p ON i.id = p.reservation_item_id
    			                             WHERE i.id = $id AND i.reservation_id = $rid")->query();
            }
        } else { // cart reservation
            $app = JFactory::getApplication();
            $cart = $app->getUserState(OPTION . '.user_reservation_items');
            if (count($cart) > 1 && isset($cart[$id])) {
                unset($cart[$id]);
                $app->setUserState(OPTION . '.user_reservation_items', $cart);
                return true;
            }            
        }
        return false;
    }
}