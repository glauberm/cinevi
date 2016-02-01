<?php

/**
 * Supplements list model. Support for loading database data with apply filter.
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

define('SUPPLEMENTS_PREFIX', 'supplements-');

//import needed Joomla! libraries
jimport('joomla.application.component.model');
//impoert needed JoomLIB helpers
AImporter::helper('model', 'request');

class BookingModelSupplements extends AModel
{
    
    /**
     * Main table
     * 
     * @var TableSupplement
     */
    var $_table;

    function __construct()
    {
        parent::__construct();
        $this->_table = $this->getTable('supplement');
    }

    /**
     * Get MySQL loading query for supplements list
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
     * Get MySQL order criteria for supplements list.
     * 
     * @return string order criteria in MySQL format
     */
    function buildContentOrderBy()
    {
        return ' ORDER BY `ordering` ASC ';
    }

    /**
     * Get MySQL filter criteria for supplements list.
     * 
     * @return string filter criteria in MySQL format
     */
    function buildContentWhere()
    {
        if (isset($this->_lists['subject']))
            $where[] = '`subject` = ' . (int) $this->_lists['subject'];
        if (isset($this->_lists['cids']) && count($this->_lists['cids'])) {
            JArrayHelper::toInteger($this->_lists['cids']);
            $where[] = '`id` IN (' . implode(',', $this->_lists['cids']) . ')';
        }
        return isset($where) ? ' WHERE ' . implode(' AND ', $where) : '';
    }

    /**
     * Store supplements.
     * 
     * @param int $subject ID
     * @param array $data request
     */
    function store($subject, &$data)
    {
    	$name = SUPPLEMENTS_PREFIX . 'member_discount';
    	$request = JArrayHelper::getValue($data, $name, array(), 'array');
    	$database = array();
	    
    	/* change request format (userGrouId => $itemName => $supplementId => $itemValue) 
    		into database format ($supplementId => $userGroupId => $itemName => $itemValue) */
    	foreach ($request as $userGroupId => $itemList)
    		foreach ($itemList as $itemName => $supplementList)
    			foreach ($supplementList as $supplementId => $itemValue)
    				$database[$supplementId][$userGroupId][$itemName] = $itemValue;
    	
    	JRequest::setVar($name, $database);
    	
        parent::store($this->_db, $this->_table, array('subject' => $subject), SUPPLEMENTS_PREFIX, $data);
    }
}

?>