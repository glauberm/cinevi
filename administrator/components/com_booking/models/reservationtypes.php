<?php

/**
 * Reservation types list model. Support for loading database data with apply filter.
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

define('RTYPES_PREFIX', 'rtype-');

//import needed Joomla! libraries
jimport('joomla.application.component.model');
//import needed JoomLIB helpers
AImporter::helper('model');

class BookingModelReservationTypes extends AModel
{
    
    /**
     * Main table
     * 
     * @var TableReservationType
     */
    var $_table;
    
    var $_cache;

    function __construct()
    {
        parent::__construct();
        $this->_table = $this->getTable('reservationtype');
        $this->_cache = array();
    }

    /**
     * Get object static instance.
     * 
     * @return BookingModelReservationTypes	
     */
    function getObjectInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new BookingModelReservationTypes();
        }
        return $instance;
    }

    function getData()
    {
        $subjectId = $this->_lists['subject'];
        if (isset($this->_cache[$subjectId])) {
            return $this->_cache[$subjectId];
        } else {
        	$this->_data = null;
            $this->_cache[$subjectId] = &parent::getData();
        }
        return $this->_cache[$subjectId];
    }

    /**
     * Get MySQL loading query for reservation types list
     * 
     * @return string complet MySQL query
     */
    function buildQuery($filter = true)
    {
        $query = 'SELECT * FROM `' . $this->_table->getTableName() . '` ';
        $query .= $this->buildContentWhere();
        $query .= $this->buildContentOrderBy();
        return $query;
    }

    /**
     * Get MySQL order criteria for reservation types list
     * 
     * @return string order criteria in MySQL format
     */
    function buildContentOrderBy()
    {
        return ' ORDER BY `id` ASC ';
    }

    /**
     * Get MySQL filter criteria for reservation types list.
     * 
     * @return string filter criteria in MySQL format
     */
    function buildContentWhere()
    {
        if (isset($this->_lists['subject']))
            $where[] = '`subject` = ' . (int) $this->_lists['subject'] . ' ';
        if (isset($this->_lists['rids'])) {
            $rids = implode(',', $this->_lists['rids']);
            $where[] = '`id` IN (' . $rids . ')';
        }
        $where = empty($where) ? '' : ' WHERE ' . implode(' AND ', $where);
        return $where;
    }

    /**
     * Store reservation types.
     * 
     * @param int $subject ID
     * @param array $data request
     */
    function store($subject, $data)
    {
    	$native = 0;
   		foreach ($data[RTYPES_PREFIX . 'fix_from'] as $request => $item)
			foreach ($item as $index => $value)
				if ($index === 'fix_from_start') {
					$key = $value === 'old' ? $request : $native;
					$native = ($value === 'old' ? $request : $native) + 1;
				} else 
					$fixFrom[$key][] = $value;
		$data[RTYPES_PREFIX . 'fix_from'] = $fixFrom;
		JRequest::setVar(RTYPES_PREFIX . 'fix_from', $fixFrom);
        parent::store($this->_db, $this->_table, array('subject' => $subject), RTYPES_PREFIX, $data);
    }

    /**
     * Get shortest interval of subject reservation types.
     * 
     * @return int
     */
    function getShortestInterval()
    {
        $this->_db->setQuery('SELECT MIN(`time_unit`) FROM `' . $this->_table->getTableName() . '` WHERE `subject` = ' . (int) $this->_lists['subject']);
        return (int) $this->_db->loadResult();
    }
}

?>