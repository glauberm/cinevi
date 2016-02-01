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

class BookingModelReservation extends AModel
{
    
    /**
     * Main table
     * 
     * @var TableReservation
     */
    var $_table;

    function __construct()
    {
        parent::__construct();
        $this->_table = $this->getTable('reservation');
    }
    
    /**
     * (non-PHPdoc)
     * @see AModel::getObject()
     */
    function getObject()
    {
    	$object = parent::getObject();
    	/* @var $object TableReservation */
    	$object->creator = '';
    	$object->modifier = '';
    	
    	if ($this->_id) {
    		$query = $this->getDbo()->getQuery(true);
    		$query->select('c.name AS creator, m.name AS modifier');
	    	$query->from('#__booking_reservation AS r');
	    	$query->leftJoin('#__users AS c ON r.created_by = c.id');
	    	$query->leftJoin('#__users AS m ON r.modified_by = m.id');
	    	$query->where('r.id = ' . (int) $this->_id);
	    	$data = $this->getDbo()->setQuery($query)->loadObject();
	    	
	    	$object->creator = $data->creator;
	    	$object->modifier = $data->modifier;
    	}
    	
    	return $object;
    }

    /**
     * Save reservation.
     * 
     * @param array $data request data
     * @return customer id if success, false in unsuccess
     */
    function store($data)
    {
    	//utc time for expiration
    	$data['book_time'] = JFactory::getDate()->toSql();//date("Y-m-d H:i:s");
    	
        if (! $this->_table->bind($data) || ! $this->_table->check() || ! $this->_table->store()) {
            return false;
        }
        
        return $this->_table->id;
    }

    /**
     * Set reservations as received.
     * 
     * @param array $cids
     * @return boolean
     */
    function receive($cids)
    {
        return $this->state('paid', $cids, RESERVATION_RECEIVE, RESERVATION_RECEIVE_DEPOSIT, RESERVATION_PENDING, RESERVATION_ONLINE_PENDING);
    }

    /**
     * Set reservations as received deposit.
     * 
     * @param array $cids
     * @return boolean
     */
    function receiveDeposit($cids)
    {
        return $this->state('paid', $cids, RESERVATION_RECEIVE_DEPOSIT, RESERVATION_PENDING, RESERVATION_RECEIVE, RESERVATION_ONLINE_PENDING);
    }

    /**
     * Set reservations as unreceived.
     * 
     * @param array $cids
     * @return boolean
     */
    function unreceive($cids)
    {
        return $this->state('paid', $cids, RESERVATION_PENDING, RESERVATION_RECEIVE, RESERVATION_RECEIVE_DEPOSIT, RESERVATION_ONLINE_PENDING);
    }
    
    /**
     * Status when payment is verified but pending
     * @param array $cids
     * @return boolean
     */
    function onlinePending($cids)
    {
    	return $this->state('paid', $cids, RESERVATION_ONLINE_PENDING, RESERVATION_PENDING, RESERVATION_RECEIVE, RESERVATION_RECEIVE_DEPOSIT);
    }    

    /**
     * Set reservations as storned.
     * 
     * @param array $cids
     * @return boolean
     */
    function storno($cids)
    {
        return $this->state('state', $cids, RESERVATION_STORNED, RESERVATION_ACTIVE, RESERVATION_TRASHED);
    }

    /**
     * Safe storno reservations. Control customer ID and current state.
     * Storno only active reservations.
     * Storno only reservations which haveno item expired.
     * 
     * @param int $customerId
     * @param array $cids
     * @return boolean
     */
    function stornoSafe($customerId, $cids)
    {
        if (count($cids)) {
        	
        	$return = true;
        	
        	$modelReservationItems = new BookingModelReservationItems();
        	$modelReservationItems->init(array());	
        	
        	$now = AModel::getNow();
        	
        	foreach ($cids as $cid) {
        		
        		$isExpired = false;
        		
        		$modelReservationItems->_lists['reservation_item-reservation_id']=$cid;
        		unset($modelReservationItems->_data);
        		$reservedItems = $modelReservationItems->getData();
        		
        		foreach ($reservedItems as $reservedItem)
        			if ($reservedItem->rtype != RESERVATION_TYPE_PERIOD)
        				if (strtotime($reservedItem->from) <= $now)
        					$isExpired=true;

        		if (!$isExpired) { //no item in reservation is expired
	        				
		            $query = 'UPDATE `' . $this->_table->getTableName() . '` SET `state` = ' . RESERVATION_STORNED;
		            $query .= ' WHERE `customer` = ' . (int) $customerId . ' AND `state` = ' . RESERVATION_ACTIVE;
		            $query .= ' AND `id` = ' . (int)$cid;
		            $this->_db->setQuery($query);
		            $return = $this->_db->query();
        		}
        	}
            return $return;
        }
        return false;
    }

    /**
     * Set reservations as trashed.
     * 
     * @param array $cids
     * @return boolean
     */
    function trash($cids)
    {
        return $this->state('state', $cids, RESERVATION_TRASHED, RESERVATION_ACTIVE, RESERVATION_STORNED, RESERVATION_CONFLICTED, RESERVATION_PRERESERVED);
    }
    
    /**
     * Set reservations as conflicted.
     *
     * @param array $cids
     * @return boolean
     */
    function conflict($cids)
    {
    	return $this->state('state', $cids, RESERVATION_CONFLICTED, RESERVATION_ACTIVE, RESERVATION_STORNED, RESERVATION_TRASHED, RESERVATION_PRERESERVED);
    }
    
    /**
     * Set reservations as pre-reserved.
     *
     * @param array $cids
     * @return boolean
     */
    function prereserved($cids)
    {
    	return $this->state('state', $cids, RESERVATION_PRERESERVED, RESERVATION_ACTIVE, RESERVATION_STORNED, RESERVATION_TRASHED, RESERVATION_CONFLICTED);
    }

    /**
     * Set reservations as actived.
     * 
     * @param $cids
     */
    function active($cids)
    {
    	$error = false;
    	foreach($cids as $id)
    	{
    	
    		//get reservation items for reservation
	    	$lists['reservation_item-reservation_id'] = $id;	        
	    	$modelReservations = new BookingModelReservationItems();
	    	$modelReservations->init($lists);
	    	$items = $modelReservations->getData();
	    	
	    	$modelSubject = new BookingModelSubject();
	    	
	    	$conflict = false;
	    	//process all items for current reservation
	    	foreach($items as $item)
	    	{
				//get all reservations for subject in same time interval
	    		$reservations = $modelReservations->getSimpleData($item->subject,$item->from,$item->to,true);
	    		
	    		//get total reserved capacity for subject in time interval
	    		$sum = 0;
	    		foreach($reservations as $reservation)
	    		{
	    			$sum += $reservation->capacity;
	    		}
	    		
	    		//get subject's total capacity and compare with sum
	    		if($modelSubject->setId($item->subject)->getObject()->total_capacity < $sum)
	    		{
	    			$conflict = true;
	    			$error = true;
	    			break;
	    		}
	    	}
	    	if($conflict){
	    		ALog::add("Conflict with capacity for reservation ".$id,JLog::INFO);
	    		$this->conflict(array($id));
	    	}
    	}
    	
    	if($error){
    		return false;
    	}

        return $this->state('state', $cids, RESERVATION_ACTIVE, RESERVATION_STORNED, RESERVATION_TRASHED, RESERVATION_CONFLICTED, RESERVATION_PRERESERVED);
    }

    /**
     * Set reservations as actived (untrashed).
     * 
     * @param array $cids
     * @return boolean
     */
    function restore($cids)
    {
        return $this->state('state', $cids, RESERVATION_ACTIVE, RESERVATION_TRASHED);
    }

    /**
     * Delete trashed reservations.
     * 
     * @return boolean
     */
    function emptyTrash()
    {
    	$this->_db->setQuery('DELETE r, i, p, s 
    			FROM #__booking_reservation AS r 
    			LEFT JOIN #__booking_reservation_items AS i ON r.id = i.reservation_id
    			LEFT JOIN #__booking_reservation_period AS p ON p.reservation_item_id = i.id 
    			LEFT JOIN #__booking_reservation_supplement AS s ON r.id = s.reservation 
    			WHERE r.state = ' . RESERVATION_TRASHED);
    	$this->_db->query();
    	return true;
    }

    function setPaymentType($id, $alias, $title)
    {
        $this->_db->setQuery('UPDATE `' . $this->_table->getTableName() . '` SET `payment_method_id` = ' . $this->_db->Quote($alias) . ',`payment_method_name` = ' . $this->_db->Quote($title) . ' WHERE `id` = ' . (int) $id);
        return $this->_db->query();
    }
    
    /**
     * Get full price for paing.
     *
     * @return int
     */
    function getFullPrice()
    {
    	return $this->_table->fullPrice;
    }
    
    /**
     * Get price when deposit is paid.
     *
     * @return int
     */
    function getFullPriceWithoutDeposit()
    {
    	return $this->_table->fullPrice - $this->_table->fullDeposit;
    }
    
    /**
     * Prepare e-mail subject to send. Replace variables by data.
     *
     * @param string $subject
     * @param TableReservation $reservation
     * @return string
     */
    function replaceEmailSubject($subject, &$reservation)
    {
		return str_replace('{ID}', $reservation->id, $subject);
    }
    
    /**
     * Prepare email body to send. Replace variables by data.
     *
     * @param string $body
     * @param TableReservation $reservation
     * @param array of TableReservationItems+supplements $items
     * @param string $status
     * @return string
     */
    function replaceEmailBody($body, &$reservation, $items, $status = '', $clenanup = false)
    {
        $config = AFactory::getConfig();
    	array_map('TableReservationItems::display', $items);
    	$matches = array();
    	if (preg_match_all('#\{OBJECTS\}(.*)\{\/OBJECTS\}#Uis',$body,$matches,PREG_SET_ORDER)) {
    		
    		foreach ($matches as $match) {
    		
	    		$pattern = $match[1];
	    		$replacements = array();
	    
	    		foreach ($items as $item) {
	    			/* @var $item TableReservationItems */
	    			$supplementsInfo = array();
	    			if (isset($item->supplements)) foreach ($item->supplements as $supplement)
	    				$supplementsInfo[] = $supplement->title . ': ' . BookingHelper::displaySupplementValue($supplement);
	    
	    			$replacement = $pattern;
	    			 
	    			$replacement = str_replace('{OBJECT TITLE}', $item->subject_title, $replacement);
	    			$replacement = str_replace('{DATE}', AHtml::interval($item, 0), $replacement);
	    			$replacement = str_replace('{DATE_UP}', AHtml::date($item->from, ADATE_FORMAT_NORMAL, 0), $replacement); // MAJU: its here only for compatibility purposes
	    			$replacement = str_replace('{DATE_FROM}', AHtml::date($item->from, ADATE_FORMAT_NORMAL, 0), $replacement);
	    			$replacement = str_replace('{DATE_DOWN}', AHtml::date($item->to, ADATE_FORMAT_NORMAL, 0), $replacement); // MAJU: its here only for compatibility purposes
	    			$replacement = str_replace('{DATE_TO}', AHtml::date($item->to, ADATE_FORMAT_NORMAL, 0), $replacement);
	    			$replacement = str_replace('{DATE_TIME_UP}', AHtml::date($item->from, ADATE_FORMAT_LONG, 0), $replacement); // MAJU: its here only for compatibility purposes
	    			$replacement = str_replace('{DATE_TIME_FROM}', AHtml::date($item->from, ADATE_FORMAT_LONG, 0), $replacement);
	    			$replacement = str_replace('{DATE_TIME_DOWN}', AHtml::date($item->to, ADATE_FORMAT_LONG, 0), $replacement); // MAJU: its here only for compatibility purposes
	    			$replacement = str_replace('{DATE_TIME_TO}', AHtml::date($item->to, ADATE_FORMAT_LONG, 0), $replacement);
	    			$replacement = str_replace('{QUANTITY}', $item->capacity, $replacement);
	    			
	    			$occupancy = array();
	    			foreach ($item->occupancy as $oi => $oitem) {
	    				if ($oitem['count']) {
	    					$occupancy[$oi] = $oitem['title'] . ': ' .$oitem['count'];
	    					if ($oitem['total'] != 0) {
	    				    	$occupancy[$oi] .= ' (' . BookingHelper::displayPrice($oitem['total'], null, $item->tax, true) . ')';
	    					}
	    				}
	    			}
					
	    			$replacement = str_replace('{OCCUPANCY}', implode('<br/>', $occupancy), $replacement);
	    			$replacement = str_replace('{PRICE}', BookingHelper::displayPrice($item->fullPrice, null, $item->tax), $replacement);
	    			$replacement = str_replace('{PRICEWITHSUPPLEMENTS}', BookingHelper::displayPrice($item->fullPriceSupplements, null, $item->tax), $replacement);
	    			$replacement = str_replace('{DEPOSIT}', BookingHelper::displayPrice($item->fullDeposit), $replacement);
	    			$replacement = str_replace('{TAX}', BookingHelper::displayPrice(BookingHelper::getTax($item->fullPriceSupplements, $item->tax)), $replacement);
	    			$replacement = str_replace('{SUPPLEMENTS}', implode('<br/>', $supplementsInfo), $replacement);
	    			$replacement = str_replace('{MESSAGE}', $item->message, $replacement);
	    			// marks for periodic reservations
	    			$replacement = str_replace('{TIMEFRAME}', AHtml::showRecurenceTimeframe($item), $replacement);
	    			$replacement = str_replace('{RECURRENCE PATTERN}', AHtml::showRecurencePattern($item), $replacement);
	    			$replacement = str_replace('{RANGE OF RECURRENCE}', AHtml::showRecurenceRange($item), $replacement);
	    			$replacement = str_replace('{RECURRENCE TOTAL}', $item->period_total, $replacement);
                    
                    $moreNames = is_string($item->more_names) ? json_decode($item->more_names) : $item->more_names;
                    $moreNamesRows = array();
                    foreach ((array) $moreNames as $q => $moreName) {
                        $moreNamesRows[] = JText::sprintf('PERSON_NUM', $q + 1) . ': ' . implode(' ', (array) $moreName);
                    }
                    $replacement = str_replace('{MORE_NAMES}', implode('<br/>', $moreNamesRows), $replacement);
                    
	    			$replacements[] = $replacement;
	    		}
	    		$body = str_replace($match[0], implode('', $replacements), $body);
    		}
    	}
    
    	list($fullPrice,$fullDeposit) = BookingHelper::countOverallPrice(null,$items);
    
    	$body = str_replace('{ID}', $reservation->id, $body);
    	$body = str_replace('{CREATED}', AHtml::date($reservation->created, ADATE_FORMAT_LONG), $body);
    	$body = str_replace('{STATUS}', $status, $body);
    	$body = str_replace('{FULLPRICE}', BookingHelper::displayPrice($fullPrice), $body);
    	$body = str_replace('{FULLDEPOSIT}', BookingHelper::displayPrice($fullDeposit), $body);
    	$body = str_replace('{FULLTAX}', BookingHelper::displayPrice(BookingHelper::getFullTax($items)), $body);
    	$body = str_replace('{CUSTOMER}', BookingHelper::formatName($reservation), $body);
    	$body = str_replace('{TITLE_BEFORE}', $reservation->title_before, $body);
    	$body = str_replace('{FIRSTNAME}', $reservation->firstname, $body);
    	$body = str_replace('{MIDDLENAME}', $reservation->middlename, $body);
    	$body = str_replace('{SURNAME}', $reservation->surname, $body);
    	$body = str_replace('{TITLE_AFTER}', $reservation->title_after, $body);
        $body = str_replace('{MORE_NAMES}', implode('<br/>', (is_string($reservation->more_names) ? json_decode($reservation->more_names) : $reservation->more_names)), $body);
    	$body = str_replace('{ADDRESS}', BookingHelper::formatAddress($reservation), $body);
    	$body = str_replace('{STREET}', $reservation->street, $body);
    	$body = str_replace('{CITY}', $reservation->city, $body);
    	$body = str_replace('{COUNTRY}', $reservation->country, $body);
    	$body = str_replace('{ZIP}', $reservation->zip, $body);
    	$body = str_replace('{COMPANY}', $reservation->company, $body);
    	$body = str_replace('{EMAIL}', $reservation->email, $body);
    	$body = str_replace('{TELEPHONE}', $reservation->telephone, $body);
    	$body = str_replace('{FAX}', $reservation->fax, $body);
    	$body = str_replace('{PAYMENT}', $reservation->payment_method_name, $body);
    	$body = str_replace('{PAYMENT_INFO}', $reservation->payment_method_info, $body);
    	$body = str_replace('{NOTE}', $reservation->note, $body);
    	$body = str_replace('{LINK}', JURI::base()."administrator/".ARoute::edit(VIEW_RESERVATION, $reservation->id), $body);
    	if (!empty($reservation->fields))
            $ufields = (array) @unserialize($reservation->fields);
    		foreach ($ufields as $field) {
                if ($field['value'] == 'jyes' || $field['value'] == 'jno')
                    $field['value'] = JText::_ ($field['value']);
                $body = str_replace('{' . JString::strtoupper($field['title']) . '}', $field['value'], $body);
            }
        foreach ($config->rsExtra as $field) // remove unused custom fields
            $body = str_replace('{' . JString::strtoupper($field['title']) . '}', '', $body);
    	
    		$body .= "\n\n" . BookingHelper::get();
    	
    
    	return $clenanup ? str_replace(array("\r\n\r\n", "\n\r\n\r", "\n\n"), array("\r\n", "\n\r", "\n"), JFilterOutput::cleanText($body)) : $body;
    }
    
    /**
     * Get items of similar type to change
     * 
     * @param int id reservation item id
     * @param bool $allowCurrent add into the list currently booked item as well
     * @return array
     */
    public function getChangeableItems($id, $allowCurrent = false) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $user = JFactory::getUser();
        $model = new BookingModelSubject();
        
        $null = $db->q($db->getNullDate());
        $date = $db->q(JFactory::getDate()->toSql());
        
        $query->select('s.parent, s.id, i.from, i.to')
              ->from('#__booking_reservation_items AS i')
              ->leftJoin('#__booking_subject AS s ON s.id = i.subject')
              ->where('i.id = ' . (int) $id);
        $item = $db->setQuery($query)->loadObject();
        
        if (empty($item)) {
            return false;
        }
        
        $query->clear()
              ->select('s.id, s.title, r.id AS rid, r.title AS rtitle')
              ->from('#__booking_subject AS s')
              ->leftJoin('#__booking_reservation_type AS r ON r.subject = s.id')
              ->where('s.parent = ' . (int) $item->parent)
              ->order('s.title, r.title');
        if (!$allowCurrent) {
            $query->where('s.id <> ' . (int) $item->id);
        }
        
        $items = $used = array();
        
        $candidates = $db->setQuery($query)->loadObjectList();
        $lastID = 0;
        foreach ($candidates as $candidate) {
            if ($candidate->id != $lastID) {
                $subject = $model->setId($candidate->id)->getObject();
                if (empty($subject->id)) { // not published subject
                    continue;
                }
                $calendar = BookingHelper::getCalendar($subject, $item->from, $item->to, true);
            }
            $lastID = $candidate->id;
            $boxIds = array();
            foreach ($calendar->calendar as $day) {
                if ($day->engaged || $day->closed) {
                    break 1; // full day not available
                }
                foreach ($day->boxes as $box) {
                    if ($box->closed || $box->engaged) {
                        break 2; // full time box not available
                    }
                    foreach ($box->services as $service) {
                        if ($service->rtypeId == $candidate->rid && !$service->canReserve) {
                            break 3; // time service not available
                        }
                        if (($service->fromDate . ':00') >= $item->from && ($service->toDate . ':00') <= $item->to) {
                            $boxIds[] = $service->id; // take only services in the interval
                        }
                    }
                }
            }
            if ($boxIds) {
                $value = array('id' => $candidate->id, 'boxIds' => $boxIds);
                $items[] = array('value' => json_encode($value), 'text' => ($candidate->title . ' / ' . $candidate->rtitle), 'id' => $candidate->id);
                $used[] = $candidate->id;
            }
        }
        
        return $items;
    }
    
    /**
     * Do changing reservation item
     * 
     * @param int $id reservation item id
     * @param int $item bookable item id
     * @param string $from reservation from datetime
     * @param string $to reservation to datetime
     * @return boolean
     */
    public function changeItem($id, $data) {
        $data = json_decode($data);
        
        $modelSubject = new BookingModelSubject();
        $modelReservationItem = new BookingModelReservationItem();
        $modelSupplements = new BookingModelReservationSupplements();
        
        $subject = $modelSubject->setId($data->id)->getObject();
        $item = $modelReservationItem->setId($id)->getObject();
        $ctype = $item->rtype == RESERVATION_TYPE_DAILY ? CTYPE_MONTHLY : CTYPE_WEEKLY;
        
        $modelSupplements->_lists['reservation'] = $item->id; // load existing supplements
        $supplements = BookingHelper::loadSupplements($modelSupplements, $subject->id, $item->capacity, $modelSupplements->getData(), null, count($data->boxIds));
        
        if ($subject->id != $item->subject) { // change bookable item
            $data->occupancy = array();
            if ($subject->show_occupancy) {
                foreach ($subject->occupancy_types as $otype) { // load default item occupancy
                    if ($otype->type == 0 && $subject->standard_occupancy_min > 0) {
                        $data->occupancy[$otype->id] = $subject->standard_occupancy_min;
                    }
                    if ($otype->type == 1 && $subject->extra_occupancy_min > 0) {
                        $data->occupancy[$otype->id] = $subject->extra_occupancy_min;
                    }
                }
            }
        }        
        
        $box = BookingHelper::getReservedInterval($subject, $ctype, $data->boxIds, $supplements, $item->capacity, $item, $data->occupancy);
        if (!$box->canReserve) {
            return false;
        }
        
        TableReservationItems::bindFromBox($item, $box, $subject);
        
        if ($item->store()) {
            return $item->reservation_id;
        }
        return false;
    }
}

?>