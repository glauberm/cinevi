<?php

/**
 * Customers list model. Support for loading database data with apply filter.
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

class BookingModelCustomers extends AModel
{
    /**
     * Main table
     * 
     * @var TableCustomer
     */
    var $_table;

    function __construct()
    {
        parent::__construct();
        $this->_table = $this->getTable('customer');
    }

    /**
     * Get MySQL loading query for customers list
     * 
     * @return string complet MySQL query
     */
    function buildQuery()
    {
        static $query;
        if (is_null($query)) {
            $query = 'SELECT `customer`.*, `user`.`id` AS `userId`, `user`.`username`, `user`.`email`, `user`.`block`, `editorUser`.`name` AS `editor` ';
            $query .= 'FROM `' . $this->_table->getTableName() . '` AS `customer` ';
            $query .= 'LEFT JOIN `#__users` AS `user` ON `user`.`id` = `customer`.`user` ';
            $query .= 'LEFT JOIN `#__users` AS `editorUser` ON `editorUser`.`id` = `customer`.`checked_out` ';
            $query .= $this->buildContentWhere();
            $query .= $this->buildContentOrderBy();
        }
        return $query;
    }

    /**
     * Get MySQL filter criteria for customers list
     * 
     * @return string filter criteria in MySQL format
     */
    function buildContentWhere()
    {
        $where = array();
        if ($this->_lists['search']) {
        	$search = $this->_db->quote('%' . JString::strtolower(JString::trim($this->_lists['search'])) . '%');
        	$where[] = "(LOWER(customer.surname) LIKE $search OR customer.city LIKE $search OR customer.city LIKE $search OR customer.company LIKE $search)";
        }
        if ($this->_lists['state'] !== '') 
        	$where[] = 'customer.state = ' . (int) $this->_lists['state'];
        return $this->getWhere($where);
    }
}

?>