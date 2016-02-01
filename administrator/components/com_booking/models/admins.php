<?php

/**
 * Admins list model. Support for loading database data with apply filter.
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

class BookingModelAdmins extends AModel
{
    /**
     * Main table
     * 
     * @var TableAdmin
     */
    var $_table;

    function __construct()
    {
        parent::__construct();
        $this->_table = $this->getTable('admin');
    }

    /**
     * Get MySQL loading query for admins list.
     * 
     * @return string complet MySQL query
     */
    function buildQuery()
    {
        static $query;
        
        if (is_null($query)) {
            $tableCustomer = &$this->getTable('customer');
            /* @var $tableCustomer TableCustomer */
            //TODO select admins
            if (ISJ16) {
                $query = 'SELECT `user`.`id`, `user`.`name`, `user`.`username`, GROUP_CONCAT(`group`.`title`) AS `usertype`, `user`.`email`, `user`.`block`, ';
                $query .= '`admin`.`id` AS `isadmin`, `customer`.`id` AS `iscustomer` ';
            } else {
                $query = 'SELECT `user`.`id`, `user`.`name`, `user`.`username`, `user`.`usertype`, `user`.`email`, `user`.`block`, ';
                $query .= '`admin`.`id` AS `isadmin`, `customer`.`id` AS `iscustomer` ';
            }
            $query .= 'FROM `#__users` AS `user` ';
            $query .= 'LEFT JOIN `' . $this->_table->getTableName() . '` AS `admin` ON `user`.`id` = `admin`.`id` ';
            $query .= 'LEFT JOIN `' . $tableCustomer->getTableName() . '` AS `customer` ON `user`.`id` = `customer`.`user` ';
            if (ISJ16) {
                $query .= 'LEFT JOIN `#__user_usergroup_map` AS `map` ON `map`.`user_id` = `user`.`id` ';
                $query .= 'LEFT JOIN `#__usergroups` AS `group` ON `group`.`id` = `map`.`group_id` ';
            }
            $query .= $this->buildContentWhere();
            if (ISJ16)
                $query .= ' GROUP BY `user`.`id` ';
            $query .= $this->buildContentOrderBy();
        }
        
        return $query;
    }

    /**
     * Get MySQL filter criteria for admins list.
     * 
     * @return string filter criteria in MySQL format
     */
    function buildContentWhere()
    {
        $where = array();
        if ($this->_lists['search']) {
        	$search = $this->_db->quote('%' . JString::strtolower(JString::trim($this->_lists['search'])) . '%');
        	$where[] = "(LOWER(name) LIKE $search OR LOWER(username) LIKE $search)";
        }
        if ($this->_lists['global_manager'] === '1')
            $where[] = ' `admin`.`id` IS NOT NULL ';
        
        if ($this->_lists['global_manager'] === '0')
        	$where[] = ' `admin`.`id` IS NULL ';
        
        return empty($where) ? '' : ' WHERE ' . implode(' AND ', $where);
    }
}

?>