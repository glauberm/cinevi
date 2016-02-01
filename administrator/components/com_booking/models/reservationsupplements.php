<?php

/**
 * Reservation supplements list model. Support for loading database data with apply filter.
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

//import needed Joomla! libraries
jimport('joomla.application.component.model');
//impoert needed JoomLIB helpers
AImporter::helper('model', 'request');

class BookingModelReservationSupplements extends AModel
{
    
    /**
     * Main table
     * 
     * @var TableReservationSupplement
     */
    var $_table;

    function __construct()
    {
        parent::__construct();
        $this->_table = $this->getTable('reservationsupplement');
    }

    /**
     * Get MySQL loading query for reservation supplements list
     * 
     * @return string complet MySQL query
     */
    function buildQuery($filter = true)
    {
    	$supplementTable = &$this->getTable('supplement');
        /* @var $supplementTable TableReservationSupplement */
    	            
        $query  = 'SELECT `item_supplements`.*, `orig_supplements`.`capacity_multiply`, `orig_supplements`.`capacity_max`, `orig_supplements`.`unit_multiply`';
        $query .= ' FROM `' . $this->_table->getTableName() . '` AS `item_supplements` ';
        $query .= ' LEFT JOIN `' . $supplementTable->getTableName() . '` AS `orig_supplements` ON `item_supplements`.`supplement` = `orig_supplements`.`id`';
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
        return ' ORDER BY `id` ASC ';
    }

    /**
     * Get MySQL filter criteria for supplements list.
     * 
     * @return string filter criteria in MySQL format
     */
    function buildContentWhere()
    {
        if (isset($this->_lists['reservation']))
            $where[] = '`item_supplements`.`reservation` = ' . (int) $this->_lists['reservation'];
        return isset($where) ? ' WHERE ' . implode(' AND ', $where) : '';
    }
}

?>