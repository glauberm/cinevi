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

class BookingModelReservations extends AModel
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
     * Get MySQL loading query for rezervations list
     * 
     * @return string complet MySQL query
     */
    function buildQuery()
    {
        static $query;
        if (is_null($query)) {
            $subjectTable = &$this->getTable('subject');
            /* @var $subjectTable TableSubject */
            $customerTable = &$this->getTable('customer');
            /* @var $customerTable TableCustomer */
            $supplementTable = &$this->getTable('reservationsupplement');
            /* @var $supplementTable TableReservationSupplement */
            $itemsTable = &$this->getTable('reservationitems');
            /* @var $itemsTable TableReservationitems */
            $query = 'SELECT `reservation`.*, ';
            $query .= '`editorUser`.`name` as `editor`, `customer`.`checked_out` AS `customerCheckedOut`,';
            $query .= 'SUM(`items`.`fullPriceSupplements`) AS `reservationFullPrice`, ';
            $query .= 'SUM(`items`.`fullDeposit`) AS `reservationFullDeposit`, ';
            $query .= 'SUM(`items`.`capacity`) AS `fullCapacity`, ';
            $query .= '`creator`.`name` AS `creator`, `modifier`.`name` AS `modifier` ';
            $query .= 'FROM `' . $this->_table->getTableName() . '` AS `reservation` ';
            $query .= 'LEFT JOIN `#__users` AS `editorUser` ON `editorUser`.`id` = `reservation`.`checked_out` ';
            $query .= 'LEFT JOIN `' . $customerTable->getTableName() . '` AS `customer` ON `customer`.`id` = `reservation`.`customer` ';
            $query .= 'LEFT JOIN `' . $itemsTable->getTableName() . '` AS `items` ON `items`.`reservation_id` = `reservation`.`id` ';
            $query .= 'LEFT JOIN `' . $subjectTable->getTableName() . '` AS `subjects` ON `items`.`subject` = `subjects`.`id` ';
            $query .= 'LEFT JOIN `#__booking_reservation_period` AS `period` ON `period`.`reservation_item_id` = `items`.`id` ';
            $query .= 'LEFT JOIN `#__users` AS `creator` ON `reservation`.`created_by` = `creator`.`id` ';
            $query .= 'LEFT JOIN `#__users` AS `modifier` ON `reservation`.`modified_by` = `modifier`.`id` ';
            /* note: if you join supplements, SUMs are computed wrongly because thay are multiplied by supplements count
             * $query .= 'LEFT JOIN `' . $supplementTable->getTableName() . '` AS `supplement` ON `supplement`.`reservation` = `items`.`id` '; */ 
            $query .= $this->buildContentWhere();
            $query .= ' GROUP BY `reservation`.`id` ';
            if ($this->_lists['order'] == 'surname') {
            	$dir = $this->_lists['order_Dir'];
            	$query .= " ORDER BY `reservation`.`surname` $dir, `reservation`.`middlename` $dir, `reservation`.`firstname` $dir ";
            } else
            	$query .= $this->buildContentOrderBy();
        }
        
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
            foreach ($items as $item) {
                $subjectIDs[] = $item->subject;
            }
            $subjectIDs = array_unique($subjectIDs);
            $query = 'SELECT `id`, `title`, `alias` FROM ' . $subjectTable->getTableName() . ' WHERE `id` IN (' . implode(',', $subjectIDs) . ')';
            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList('id');
            foreach ($items as &$item) {
                if (isset($rows[$item->subject])) {
                    $row = &$rows[$item->subject];
                    $item->subjectTitle = $row['title'];
                    $item->subjectAlias = $row['alias'];
                }
            }
        }
    }

    /**
     * Get MySQL filter criteria for rezervations list
     * 
     * @return string filter criteria in MySQL format
     */
    function buildContentWhere()
    {
        $where = array();
        if (!empty($this->_lists['reservation-surname'])) {
        	$name = $this->_db->quote('%' . JString::strtolower($this->_lists['reservation-surname']) . '%');
        	$where[] = " (LOWER(`reservation`.`middlename`) LIKE $name OR LOWER(`reservation`.`surname`) LIKE $name) ";
        }
        $this->addStringProperty($where, 'items-subject_title');
        $cid = explode(',', JArrayHelper::getValue($this->_lists, 'reservation-id', '', 'string'));
        if (count($cid) > 1) {
            JArrayHelper::toInteger($cid);
            $where[] = '`reservation`.`id` IN (' . implode(', ', $cid) . ')';
        } else {
            $this->addIntProperty($where, 'reservation-id');
        }
        $this->addIntProperty($where, 'customer-id');
        
        $dbfrom = !empty($this->_lists['from']) ? $this->_lists['from'] : null;
        $dbto = !empty($this->_lists['to'])   ? $this->_lists['to']   : null;                
        $filtering = !empty($this->_lists['date_filtering']) ? $this->_lists['date_filtering'] : 1;
        
        if ($filtering == 1) {
            if ($dbfrom) {
                $dbfrom = $this->_db->q(AModel::datetime2save($dbfrom));
            }        
            if ($dbto) {                
                $dbto = $this->_db->q(AModel::datetime2save($dbto));                
            }            
            if ($dbfrom && $dbto) {
                $where[] = "((items.from >= $dbfrom AND items.to <= $dbto) OR (period.from >= $dbfrom AND period.to <= $dbto))";
            } elseif($dbfrom) {
                $where[] = "(items.from >= $dbfrom OR period.from >= $dbfrom)";
            } elseif($dbto) {
                $where[] = "(items.to <= $dbto OR period.to <= $dbto)";            
            }
        } else {
            if ($dbfrom) {
                $dbfrom = $this->_db->q(AModel::date2save($dbfrom) . '%');
            }        
            if ($dbto) {
                $dbto = $this->_db->q(AModel::date2save($dbto) . '%');
            }                        
            if ($dbfrom && $dbto) {                
                $where[] = "((items.from LIKE $dbfrom AND items.to LIKE $dbto) OR (period.from LIKE $dbfrom AND period.to LIKE $dbto))";
            } elseif($dbfrom) {
                $where[] = "(items.from LIKE $dbfrom OR period.from LIKE $dbfrom)";
            } elseif($dbto) {
                $where[] = "(items.to LIKE $dbto OR period.to LIKE $dbto)";            
            }            
        }
                
        if (isset($this->_lists['reservation_status']) && $this->_lists['reservation_status'] !== '')
        	$where[] = 'reservation.state = ' . $this->_lists['reservation_status'];
        if (isset($this->_lists['payment_status']) && $this->_lists['payment_status'] !== '')
        	$where[] = 'reservation.paid = ' . $this->_lists['payment_status'];
        
        //permission ACL for user
        AImporter::helper('user');
        //if backend user can manage only owns
        if(($id = AUser::onlyOwner()) && IS_ADMIN){
        	$where[] = "`subjects`.`user_id` = ".$id;
        }
        //for front-end administrators
        elseif(($id = AUser::onlyOwner()) && !empty($this->_lists['is_administrator']))
        		$where[] = "(`subjects`.`user_id` = ".$id." OR `reservation`.`created_by` = ".$id.")";
        
        if (empty($this->_lists['customer-id'])) 
        	$where[] = 'items.subject IN (' . implode(', ', AUser::manageReservations()) . ')';
        
        return $this->getWhere($where);
    }
}

?>