<?php

/**
 * Prices list model. Support for loading database data with apply filter.
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

define('PRICES_PREFIX', 'price-');

//import needed Joomla! libraries
jimport('joomla.application.component.model');
//impoert needed JoomLIB helpers
AImporter::helper('model', 'request');

class BookingModelPrices extends AModel
{
    
    /**
     * Main table
     * 
     * @var TablePrice
     */
    var $_table;

    function __construct()
    {
        parent::__construct();
        $this->_table = $this->getTable('price');
    }

    function getData($nativeOrder = false)
    {
    	if (empty($this->_data)) {
    		$query = $this->buildQuery(true, $nativeOrder);
    		$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
    	}
    	
    	//parse JSON to array;
    	foreach($this->_data as $id=>$price)
    	{
    		$r = new JRegistry($price->occupancy_price_modifier);
    		$this->_data[$id]->occupancy_price_modifier = $r->toArray();    		
    	}
    	
    	return $this->_data;
    }
    
    /**
     * Get MySQL loading query for prices list
     * 
     * @return string complet MySQL query
     */
    function buildQuery($filter = true, $nativeOrder = false)
    {
        $reservationTypeTable = &$this->getTable('reservationtype');
        /* @var $reservationTypeTable TableReservationType */
        
        $query = 'SELECT `price`.*, `reservation`.`type` AS `rtype` FROM `' . $this->_table->getTableName() . '` AS `price` ';
        $query .= 'LEFT JOIN `' . $reservationTypeTable->getTableName() . '` AS `reservation` ON `price`.`rezervation_type` = `reservation`.`id` ';
        $query .= $this->buildContentWhere();
        $query .= $this->buildContentOrderBy($nativeOrder);
        
        return $query;
    }

    /**
     * Get MySQL order criteria for prices list
     * 
     * @return string order criteria in MySQL format
     */
    function buildContentOrderBy($nativeOrder = false)
    {
        return ' ORDER BY ' . ($nativeOrder ? '`price`.`time_up` ASC, ' : '') . '`price`.`id` ASC ';
    }

    /**
     * Get MySQL filter criteria for prices list
     * 
     * @return string filter criteria in MySQL format
     */
    function buildContentWhere()
    {
        if (isset($this->_lists['subject']))
            $where[] = '`price`.`subject` = ' . (int) $this->_lists['subject'];
        if (isset($this->_lists['rtypes']) && count($this->_lists['rtypes'])) {
            JArrayHelper::toInteger($this->_lists['rtypes']);
            $where[] = '`rezervation_type` IN (' . implode(',', $this->_lists['rtypes']) . ')';
        }
        return empty($where) ? '' : ' WHERE ' . implode(' AND ', $where);
    }

    
    /**
     * Get MySQL loading query for prices list
     *
     * @return string complet MySQL query
     */
    function getExpire($reservation_id)
    {
    	$modelReservationItems = $this->getTable('reservationitems');
    	$modelReservationItems->init(array('reservation_item-reservation_id'=>$reservation_id));
    	$reservedItemsDb = $reservedItemsDb->getData();
    	
    	foreach ($reservedItemsDb as $reservedItem) {
    		$newItem = JTable::getInstance('ReservationItems','Table');
    		$newItem->id = $reservedItem->id;
    		$newItem->load();
    	
    		if (!isset($subjects[$reservedItem->subject])){ //create subject item
    			$newSubject = JTable::getInstance('Subject','Table');
    			$newSubject->id = $reservedItem->subject;
    			$newSubject->load();
    			$subjects[$reservedItem->subject] = $newSubject;
    		}
    	
    		unset($modelReservationSupplements->_data); //add suplements
    		$modelReservationSupplements->_lists['reservation'] = $reservedItem->id;
    		$newItem->supplements = &$modelReservationSupplements->getData();
    		$reservedItems[] = $newItem;
    	}
    	
    	$subject = &$this->getTable('subject');
    	/* @var $reservationTypeTable TableReservationType */
    
    	$query = 'SELECT `price`.`cancel_time`, `price`.`id` FROM `' . $this->_table->getTableName() . '` AS `price` ';
    	$query .= 'LEFT JOIN `' . $subject->getTableName() . '` AS `subject` ON `price`.`subject` = `subject`.`id` ';
    	$query .= 'WHERE `subject`.';
    
    	return $query;
    }
    
    /**
     * Store prices.
     * 
     * @param int $subject ID
     * @param array $data request
     */
    function store($subject, &$data)
    {

		foreach($data[PRICES_PREFIX.'value'] as $id=>$val) 
    	{
    		switch($data[PRICES_PREFIX.'expiration_setting'][$id]){
    			case CANCEL_NONE:
    				$data[PRICES_PREFIX.'cancel_time'][$id] = NULL;
    				break;
    			case CANCEL_IMMEDIATELY:
    				$data[PRICES_PREFIX.'cancel_time'][$id] = 0;
    				break;
    			case CANCEL_AFTER:
 
    				if($data[PRICES_PREFIX.'expiration_format'][$id] == EXPIRE_FORMAT_HOUR)
    				{
    					//max 23 hours
	    				if($data[PRICES_PREFIX.'cancel_time'][$id] >= 24)
	    					$data[PRICES_PREFIX.'cancel_time'][$id] = 23;
	    				
	    				$data[PRICES_PREFIX.'cancel_time'][$id] = ((int)$data[PRICES_PREFIX.'cancel_time'][$id])*60*60;
    				}
    				else if($data[PRICES_PREFIX.'expiration_format'][$id] == EXPIRE_FORMAT_DAY)
    				{
    					$data[PRICES_PREFIX.'cancel_time'][$id] = ((int)$data[PRICES_PREFIX.'cancel_time'][$id])*24*60*60;
    				}
    				
    				break;
    			case CANCEL_BEFORE:
    				$data[PRICES_PREFIX.'cancel_time'][$id] = -$data[PRICES_PREFIX.'cancel_time'][$id];
    				
    				if($data[PRICES_PREFIX.'expiration_format'][$id] == EXPIRE_FORMAT_HOUR)
    				{
    					//max 23 hours
    					if($data[PRICES_PREFIX.'cancel_time'][$id] >= 24)
    						$data[PRICES_PREFIX.'cancel_time'][$id] = 23;
    					 
    					$data[PRICES_PREFIX.'cancel_time'][$id] = ((int)$data[PRICES_PREFIX.'cancel_time'][$id])*60*60;
    				}
    				else if($data[PRICES_PREFIX.'expiration_format'][$id] == EXPIRE_FORMAT_DAY)
    				{
    					$data[PRICES_PREFIX.'cancel_time'][$id] = ((int)$data[PRICES_PREFIX.'cancel_time'][$id])*24*60*60;
    				}
    				
    				break;
    		}
    	}
    	JRequest::setVar(PRICES_PREFIX.'cancel_time',$data[PRICES_PREFIX.'cancel_time']);
    	JRequest::setVar(PRICES_PREFIX.'custom_color',$data[PRICES_PREFIX.'custom_color']);
        
    	parent::store($this->_db, $this->_table, array('subject' => $subject), PRICES_PREFIX, $data);
    }
}

?>