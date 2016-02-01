<?php

/**
 * Customer model. Support for database operations.
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
//import needed JoomLIB helpers
AImporter::helper('booking', 'model','log');
//import needed tables
AImporter::table('admin');

class BookingModelCustomer extends AModel
{
    
    /**
     * Main table.
     * 
     * @var TableCustomer
     */
    var $_table;
    /**
     * Map user ids to customer ids.
     * 
     * @var array
     */
    var $_ids;

    function __construct()
    {
        parent::__construct();
        if (! class_exists('TableCustomer')) {
            AImporter::table('customer');
        }
        $this->_table = $this->getTable('customer');
    }

    function getObject()
    {
        if (ISJ16) {
            $query = 'SELECT `customer`.*, `user`.`email`, GROUP_CONCAT(`group`.`title`) AS `usertype` FROM `' . $this->_table->getTableName() . '` AS `customer` ';
            $query .= 'LEFT JOIN `#__user_usergroup_map` AS `map` ON `map`.`user_id` = `customer`.`user` ';
            $query .= 'LEFT JOIN `#__usergroups` AS `group` ON `group`.`id` = `map`.`group_id` ';
            $query .= 'LEFT JOIN `#__users` AS `user` ON `user`.`id` = `customer`.`user` ';
            $query .= 'WHERE `customer`.`id` = ' . (int) $this->_id;
            $query .= ' GROUP BY `customer`.`user` ';
            $this->_db->setQuery($query);
            if (($object = &$this->_db->loadObject())) {
                $this->_table->bind($object);
                $this->_table->usertype = $object->usertype;
                $this->_table->email = $object->email;
                return $this->_table;
            }
        }
        return parent::getObject();
    }

    /**
     * Set customer ID by logged user ID.
     * Get customer from logged user
     * Usefull only for front-end 
     */
    function setIdByUserId()
    {
        $user = &JFactory::getUser();
        /* @var $user JUser */
        if ($user->id && IS_SITE) {
            if (! isset($this->_ids[$user->id])) {
                $tableAdmin = &$this->getTable('admin');
                /* @var $tableAdmin TableAdmin */
                
                $query = 'SELECT `customer`.`id` ';
                $query .= 'FROM `' . $this->_table->getTableName() . '` AS `customer` ';
                $query .= 'LEFT JOIN `#__users` AS `user` ON `customer`.`user` = `user`.`id` ';
                $query .= 'LEFT JOIN `' . $tableAdmin->getTableName() . '` AS `admin` ON `admin`.`id` = `user`.`id` ';
                // is active customer
                $query .= 'WHERE ((`customer`.`user` = ' . $user->id . ' AND `customer`.`state` = ' . CUSTOMER_STATE_ACTIVE . ') ';
                // or is admin
                $query .= ' OR `admin`.`id` = ' . $user->id . ') ';
                // juser is active
                $query .= ' AND `user`.`block` = 0';
                
                $this->_db->setQuery($query);
                $this->_ids[$user->id] = (int) $this->_db->loadResult();
                if(!$this->_ids[$user->id])
                	ALog::add("BookingModelCustomer->setIdByUserId(): user with id ".$user->id." isn't active customer or admin",JLog::CRITICAL);
            }
            $this->setId($this->_ids[$user->id]);
        } else {
            $this->setId(0);
        }
    }

    /**
     * Get information about user is set as admin.
     * 
     * @return boolean
     */
    function isAdmin()
    {
        static $isAdmin;
        if (is_null($isAdmin)) {
            $isAdmin = false;
            $user = &JFactory::getUser();
            /* @var $user JUser */
            if ($user->id) {
                $tableAdmin = &$this->getTable('admin');
                /* @var $tableAdmin TableAdmin */
                $query = 'SELECT COUNT(*) FROM `' . $tableAdmin->getTableName() . '` WHERE `id` = ' . $user->id;
                $this->_db->setQuery($query);
                $count = (int) $this->_db->loadResult();
                $isAdmin = $count > 0;
            }
        }
        return $isAdmin;
    }
    
    /**
     * Logged user is customer or guest and not admin.
     * @return boolean
     */
    function isCustomer()
    {
    	static $isCustomer;
    	if (is_null($isCustomer))
    		$isCustomer = ($this->getId() || JFactory::getUser()->guest) && !$this->isAdmin();
    	return $isCustomer;
    }

    /**
     * Save customer.
     * 
     * @param array $data request data
     * @return customer id if success, false in unsuccess
     */
    function store($data)
    {
        $config = &AFactory::getConfig();
        /* @var $config BookingConfig */
        $user = &JFactory::getUser();
        /* @var $user JUser logged user */
        
        $id = (int) $data['id'];
        $this->_table->init();
        $this->_table->load($id);
        
        if (! $this->_table->bind($data)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        
        if (!$id && $user->id && IS_SITE)
        	// on frontend to become logged user as customer
            $this->_table->user = $user->id;
        
        unset($data['id']);
        
        if (JRequest::getInt('select_user') != 1) {
	        $cuser = new JUser($this->_table->user);
	        /* @var $cuser JUser customer user to update */
	        $cuser->bind($data);
	        $cuser->name = BookingHelper::formatName($this->_table);
	        
	        if (! $cuser->id) {
	        	// customer hasn't user - create
	            if (ISJ16)
	                $cuser->groups = array($config->customersUsergroup);
	            else {
	                $this->_db->setQuery('SELECT `value` FROM `#__core_acl_aro_groups` WHERE `id` = ' . (int) $config->customersUsergroup);
	                $cuser->usertype = $this->_db->loadResult();
	                $cuser->gid = $config->customersUsergroup;
	            }
	            $cuser->block = CUSTOMER_USER_STATE_ENABLED;
	            $cuser->sendEmail = CUSTOMER_SENDEMAIL;
	            $cuser->registerDate = null;
	        }
	        if (! $cuser->save()) {
	            $this->_errors = $cuser->getErrors();
	            return false;
	        }
	        $this->_table->user = $cuser->id;
        }
        
        if (! $this->_table->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        
        if (! $this->_table->store()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        
        return $this->_table->id;
    }

    /**
     * Block selected customers.
     *
     * @param $cids customers IDs
     * @return boolean success sign
     */
    function block($cids)
    {
    	return $this->state('state', $cids, CUSTOMER_STATE_BLOCK, CUSTOMER_STATE_ACTIVE);
    }
    
    /**
     * Trashed selected customers.
     * 
     * @param $cids customers IDs
     * @return boolean success sign
     */
    function trash($cids)
    {
        return $this->state('state', $cids, CUSTOMER_STATE_DELETED, CUSTOMER_STATE_ACTIVE, CUSTOMER_STATE_BLOCK);
    }

    /**
     * Restore selected customers.
     * 
     * @param $cids customers IDs
     * @return boolean success sign
     */
    function restore($cids)
    {
        return $this->state('state', $cids, CUSTOMER_STATE_ACTIVE, CUSTOMER_STATE_DELETED);
    }

    /**
     * Remove trashed customers and users accounts.
     * 
     * @return true if successfull
     */
    function emptyTrash()
    {
        $query = 'DELETE FROM ' . $this->_table->getTableName() . ' WHERE state = ' . CUSTOMER_STATE_DELETED;
        $this->_db->setQuery($query);
        return $this->_db->query();
    }
    
    function getFormFieldUser()
    {
    	JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR.'/models/forms'); // set destination directory of xml maniest
    	$form = JForm::getInstance('com_booking.customer', 'customer', array('control' => '', 'load_data' => true)); // load xml manifest
        $id = ARequest::getCid();
        if ($id) {
            $this->_db->setQuery('SELECT user FROM #__booking_customer WHERE id = '. (int) $id);
            $data['user'] = $this->_db->loadResult();
            $form->bind($data);
        }
    	/* @var $form JForm */
    	return $form->getInput('user');
    }
    
    function suggest($request) 
    {
    	$this->_db->setQuery('SELECT DISTINCT surname FROM #__booking_customer WHERE LOWER(surname) LIKE ' . $this->_db->quote('%' . JString::strtolower($request) . '%'));
    	return $this->_db->loadColumn();
    }
}

?>