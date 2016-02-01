<?php

/**
 * Rezervations list model. Support for loading database data with apply filter.
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
AImporter::helper('request', 'model');

class BookingModelReservationItems extends AModel
{
    /**
     * Main table
     * 
     * @var TableReservationitems
     */
    var $_table;

    function __construct()
    {
        parent::__construct();
        $this->_table = $this->getTable('reservationitems');
    }

    /**
     * Get simple reservation items list. Reservations will have subject
     * with given value and will be active. Reservation items will be from added 
     * time limit. Joined are reservation table.
     * 
     * @param int $subject subject ID
     * @param string $from date from MySQL datetime
     * @param string $to date to MySQL datetime
     * @param int ignore item during reservation changing
     */
    function getSimpleData($subject, $from, $to, $changedReservationItemId = 0)
    {
        $config = AFactory::getConfig();
    	$reservationTable = &$this->getTable('reservation');
        $customerTable = &$this->getTable('customer');
        $subjectTable = &$this->getTable('subject');
        /* @var $reservationTable TableReservation */
                
        $query = 'SELECT `reservation`.*, `reservation_item`.*, `period`.`from` AS `period_from`, `period`.`to` AS `period_to`, `customer`.`user`, `subject`.`total_capacity` ';
        $query .= 'FROM `' . $this->_table->getTableName() . '` AS `reservation_item` ';
        $query .= 'LEFT JOIN `' . $reservationTable->getTableName() . '` AS `reservation` ON `reservation_item`.`reservation_id` = `reservation`.`id` ';
        $query .= 'LEFT JOIN `#__booking_reservation_period` AS `period` ON `period`.`reservation_item_id` = `reservation_item`.`id` ';
        $query .= 'LEFT JOIN `' . $customerTable->getTableName() . '` AS `customer` ON `reservation`.`customer` = `customer`.`id` ';
        $query .= 'LEFT JOIN `' . $subjectTable->getTableName() . '` AS `subject` ON `reservation_item`.`subject` = `subject`.`id` ';
        
        $family = array($subject);  // check current item
        
        if ($config->parentsBookable == 1) // check reservations for full family
			$family = reset($this->getFullFamily($family));
        
        JArrayHelper::toInteger($family);
        $family = array_filter($family);
        $query .= empty($family) ? 'WHERE 1' : 'WHERE `reservation_item`.`subject` IN (' . implode(', ', $family) . ')';
        
        if ($config->confirmReservation < 2)
        	$query .= ' AND (`reservation`.`state` = ' . RESERVATION_ACTIVE . ' OR `reservation`.`state` = ' . RESERVATION_PRERESERVED . ') AND ((`reservation_item`.`from` < ' . $this->_db->Quote($to) . ' AND `reservation_item`.`to` > ' . $this->_db->Quote($from) . ') OR (`period`.`from` < ' . $this->_db->Quote($to) . ' AND `period`.`to` > ' . $this->_db->Quote($from) . '))';
        else
        	$query .= ' AND (`reservation`.`state` = ' . RESERVATION_ACTIVE . ') AND ((`reservation_item`.`from` < ' . $this->_db->Quote($to) . ' AND `reservation_item`.`to` > ' . $this->_db->Quote($from) . ') OR (`period`.`from` < ' . $this->_db->Quote($to) . ' AND `period`.`to` > ' . $this->_db->Quote($from) . '))';
        	
        if ($changedReservationItemId) {
            $query .= ' AND `reservation_item`.`id` != ' . (int) $changedReservationItemId;
        }
        
        $this->_db->setQuery($query);
        $items = $this->_db->loadObjectList();
        foreach ($items as $item)
        	if ($item->period_from !== null && $item->period_to !== null) {
        		$item->from = $item->period_from;
        		$item->to = $item->period_to;
        		$item->rtype = RESERVATION_TYPE_HOURLY;
        	}
        return $items;	
    }
    
    /**
     * Get full families for current item list
     * 
     * @param array $items
     * @return array
     */
    function getFullFamily($items)
    {
    	$family = array();
    	foreach ($items as $item) {
    		$family[$item] = array($item);    
	    	$parent = $item;
	    	do { // check current item parents
	    		$parent = $this->_db->setQuery('SELECT `parent` FROM `#__booking_subject` WHERE `id` = ' . (int) $parent)->loadResult();
	    		if (!empty($parent))
	    			$family[$item][] = $parent;
	    	} while (!empty($parent));
	    	 
	    	$children = array($item);
	    	do { // check current item children
	    		JArrayHelper::toInteger($children);
	    		$children = $this->_db->setQuery('SELECT `id` FROM `#__booking_subject` WHERE `parent` IN (' . implode(', ', $children) . ')')->loadColumn();
	    		if (!empty($children))
	    			$family[$item] = array_merge($family[$item], $children);
	    	} while (!empty($children));
    	}
    	return $family;
    }

    /**
     * Get MySQL loading query for rezervations items list
     * 
     * @return string complet MySQL query
     */
    function buildQuery()
    {
            $reservationTable = &$this->getTable('reservation');
            /* @var $reservationTable TableReservation */
            $subjectTable = &$this->getTable('subject');
            /* @var $subjectTable TableSubject */
            $customerTable = &$this->getTable('customer');
            /* @var $customerTable TableCustomer */
            $supplementTable = &$this->getTable('reservationsupplement');
            /* @var $supplementTable TableReservationSupplement */
            $query = 'SELECT `reservation_item`.*, `subject`.`id` AS `subjectId`, ';
            $query .= '`subject`.`alias` AS `subjectAlias`, `subject`.`checked_out` AS `subjectCheckedOut` ';
            $query .= 'FROM `' . $this->_table->getTableName() . '` AS `reservation_item` ';
            $query .= 'LEFT JOIN `#__booking_reservation_period` AS `period` ON `reservation_item`.`id` = `period`.`reservation_item_id` ';
            $query .= 'LEFT JOIN `' . $reservationTable->getTableName() . '` AS `reservation` ON `reservation_item`.`reservation_id` = `reservation`.`id` ';
            $query .= 'LEFT JOIN `' . $subjectTable->getTableName() . '` AS `subject` ON `subject`.`id` = `reservation_item`.`subject` ';
            $query .= 'LEFT JOIN `' . $customerTable->getTableName() . '` AS `customer` ON `customer`.`id` = `reservation`.`customer` ';
            $query .= 'LEFT JOIN `' . $supplementTable->getTableName() . '` AS `supplement` ON `supplement`.`reservation` = `reservation_item`.`id` ';
            $query .= $this->buildContentWhere();
            $query .= ' GROUP BY `reservation_item`.`id` ';
            $query .= $this->buildContentOrderBy();
            
        return $query;
    }

    /**
     * Add subject title and alias translate by JoomFISH component. Array given as function parameter
     * must contain objects with variables 'subject' (subject ID) and 'subjectTitle' to add translate title
     * and 'subjectAlias' to add translate alias. 
     * 
     * @param array $items array of stdClasses with variables: subject, subjectTitle and subjectAlias
     */
    function addTitleTranslation(&$items)
    {
        $countItems = count($items);
        if ($countItems) {
            $subjectTable = &$this->getTable('subject');
            $subjectIDs = array();
            for ($i = 0; $i < $countItems; $i ++) {
                $item = &$items[$i];
                $subjectIDs[] = $item->subject;
            }
            $subjectIDs = array_unique($subjectIDs);
            $query = 'SELECT `id`, `title`, `alias` FROM ' . $subjectTable->getTableName() . ' WHERE `id` IN (' . implode(',', $subjectIDs) . ')';
            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList('id');
            for ($i = 0; $i < $countItems; $i ++) {
                $item = &$items[$i];
                if (isset($rows[$item->subject])) {
                    $row = &$rows[$item->subject];
                    $item->subjectTitle = $row['title'];
                    $item->subjectAlias = $row['alias'];
                }
            }
        }
    }

    /**
     * Get MySQL filter criteria for rezervation items list
     * 
     * @return string filter criteria in MySQL format
     */
    function buildContentWhere()
    {
        $where = array();
        $this->addIntProperty($where, 'reservation_item-reservation_id');
        
        if (!empty($this->_lists['items-subject_title'])){
            $where[] = 'LOWER(reservation_item.subject_title) LIKE ' . $this->_db->q('%' . JString::trim(JString::strtolower($this->_lists['items-subject_title'])) . '%');
        }
        
        $dbfrom = !empty($this->_lists['from']) ? $this->_lists['from'] : null;
        $dbto   = !empty($this->_lists['to'])   ? $this->_lists['to']   : null;                
        
        if (empty($this->_lists['date_filtering']) || $this->_lists['date_filtering'] == 1) {
            if ($dbfrom) {
                $dbfrom = $this->_db->q(AModel::datetime2save($dbfrom));
            }        
            if ($dbto) {                
                $dbto = $this->_db->q(AModel::datetime2save($dbto));                
            }            
            if ($dbfrom && $dbto) {
                $where[] = "((reservation_item.from >= $dbfrom AND reservation_item.to <= $dbto) OR (period.from >= $dbfrom AND period.to <= $dbto))";
            } elseif($dbfrom) {
                $where[] = "(reservation_item.from >= $dbfrom OR period.from >= $dbfrom)";
            } elseif($dbto) {
                $where[] = "(reservation_item.to <= $dbto OR period.to <= $dbto)";            
            }
        } else {
            if ($dbfrom) {
                $dbfrom = $this->_db->q(AModel::date2save($dbfrom) . '%');
            }        
            if ($dbto) {
                $dbto = $this->_db->q(AModel::date2save($dbto) . '%');
            }                        
            if ($dbfrom && $dbto) {                
                $where[] = "((reservation_item.from LIKE $dbfrom AND reservation_item.to LIKE $dbto) OR (period.from LIKE $dbfrom AND period.to LIKE $dbto))";
            } elseif($dbfrom) {
                $where[] = "(reservation_item.from LIKE $dbfrom OR period.from LIKE $dbfrom)";
            } elseif($dbto) {
                $where[] = "(reservation_item.to LIKE $dbto OR period.to LIKE $dbto)";            
            }  
        }
        
        return $this->getWhere($where);
    }
    
    /**
     * Search reservations in subject set limit.
     * 
     * @param TableCustomer $customer
     * @param TableSubject $subject
     * @param TableReservation $subject
     * @return boolean true or subject which limit was exceeded
     */
    function canReserveInLimit(&$customer, &$subjects, &$reservedItems)
    {
        if ($customer->id) {
        	
        	$mainframe = JFactory::getApplication();
        	$reservationTable = &$this->getTable('reservation');
        	
        	$usedSubjects = array();
        	$itemsBySubject = array();
        	$points = array();
        	
        	foreach ($reservedItems as $reservedItem){ //store items to array 
        		$subjectId = $reservedItem->subject;
        		
        		if (!$subjects[$subjectId]->rlimit_set) //only subject with reservation limit set
        			continue;
        		
        		$usedSubjects[$subjectId] = $subjectId;
        		
         		//widen reservation range by limit days  (must be 24/2 because .. just because)
        		$addRange = $subjects[$subjectId]->rlimit_days*3600*12;
        		
        		$item = array();
        		$item['from']=strtotime($reservedItem->from)-$addRange;
        		$item['to']=strtotime($reservedItem->to)+$addRange;
        		$item['capacity']=$reservedItem->capacity;
        		$item['db']=false;
        		
        		$points[$subjectId][$item['from']]=$item['from']; //add keypoints
        		$points[$subjectId][$item['to']]=$item['to'];
        		
        		$itemsBySubject[$subjectId][]=$item;
        	}
        	
        	foreach ($usedSubjects as $subjectId){
        		
        		$addRange = $subjects[$subjectId]->rlimit_days*3600*12; //limit/  2
        		
        		//get already reserved items for subject and user

	        	$query = 'SELECT `items`.`capacity`, `items`.`from`, `items`.`to` ';
	        	$query .= 'FROM `' . $this->_table->getTableName() . '` AS `items` ';
	        	$query .= 'LEFT JOIN `' . $reservationTable->getTableName() . '` AS `reservation` ON `items`.`reservation_id` = `reservation`.`id` ';
	        	$query .= 'WHERE `reservation`.`customer`= ' . $customer->id.' AND `items`.`subject`='.$subjectId;
	        	
	        	$this->_db->setQuery($query);
	        	$itemsDb = $this->_db->loadObjectList();
        	
        		foreach ($itemsDb as $itemDb) { //store reserved items to array just like items to reserve
	        			
	        		$item = array();
	        		$item['from']=strtotime($itemDb->from)-$addRange; //widen range
	        		$item['to']=strtotime($itemDb->to)+$addRange;
	        		$item['capacity']=$itemDb->capacity;
	        		$item['db']=true;
	        		
	        		$points[$subjectId][$item['from']]=$item['from']; //add keypoints
	        		$points[$subjectId][$item['to']]=$item['to'];
	        		
	        		$itemsBySubject[$subjectId][]=$item;
        		}
        		
        		foreach ($points[$subjectId] as $point){ //go through key points

        			$reserved=0; //number if reserved quantity by user in key point
        			$isDb=true;
        			
        			foreach ($itemsBySubject[$subjectId] as $item){
        				
        				if ($item['from']<=$point AND $item['to']>=$point){ //if point is within reserved interval, add to overall quantity
        					$reserved+=$item['capacity'];
        					
        					if (!$item['db']) //some item is not from db (=reserving now)
        						$isDb=false;
        				}
        			}

        			if ($reserved>$subjects[$subjectId]->rlimit_count && !$isDb) //allowed quantity exceeded at some reserving item
        			{
        				return $subjects[$subjectId];
        			}
        					
        		}

        	}
        	return true;
        }
        return true;
    }
    
    /**
     * Storno reservations with expired payment.
     * @return boolean
     */
    function stornoExpired()
    {
    
    	$items = $this->getAllUnpaid();

    	foreach($items as $item)
    	{
    		if ($this->checkExpiration($item))
    			$exipredId[] = $item->reservation_id;
    	}
    	
		if(isset($exipredId) && !empty($exipredId))
		{
			ALog::add("reservations expired: ".implode(",", $exipredId),JLog::NOTICE);
	    	$query = 'UPDATE `#__booking_reservation` SET `state` = ' . RESERVATION_STORNED;
	    	$query .= ' WHERE `id` IN ('.implode(",", $exipredId).')';
	    	$this->_db->setQuery($query);
	    	$return = $this->_db->query();
		}
		else
			return true;
    }
    
    //if is expired -> return true
    function checkExpiration($item)
    {
    	if($item->cancel_time === null)
    		return false;
    	
    	if($item->cancel_time == 0)
    		return true;
    	
    	if($item->cancel_time < 0)
    	{
    		if((BookingHelper::gmStrtotime($item->from) - abs((int)$item->cancel_time)) < time())
    			return true;
    	}
    	
    	if($item->cancel_time > 0)
    	{
    		if((BookingHelper::gmStrtotime($item->book_time) + ((int)$item->cancel_time)) < time())
    			return true;
    	}
    	
    	return false;   	
    }
    
    function getAllUnpaid()
    {
    	$query = 'SELECT `reservation`.`book_time`, `items`.* FROM `'.$this->_table->getTableName().'` AS `items` LEFT JOIN `#__booking_reservation` AS `reservation` ON `items`.`reservation_id` = `reservation`.`id` WHERE `reservation`.`state` > '.RESERVATION_STORNED.' AND `reservation`.`paid` = '.RESERVATION_PENDING;
    	return $this->_getList($query);
    
    }
}

?>