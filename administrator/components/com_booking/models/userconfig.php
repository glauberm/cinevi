<?php

/**
 * Custom Config model. Support for database operations.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  models
 * @copyright	Copyright (C) 201# ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

// import needed Joomla! libraries
jimport('joomla.application.component.model');
// import needed JoomLIB helpers
AImporter::helper('model');
AImporter::helper('artio-update');

class BookingModelUserConfig extends AModel
{

	/**
	 * Store user custom component configuration into database.
	 * 
	 * @return boolean true/false if query success/unsuccess
	 */
	function store($data,$type = 'config')
    {    	

    	$data = json_encode($data['params']);
    	
        $ret = true;
       	if ($data) {
	        	$this->_db->setQuery('INSERT INTO `#__booking_user_config` (`user_id`, '.$this->_db->quoteName($type).') VALUES (' . JFactory::getUser()->id . ', ' . $this->_db->Quote($data) . ') ON DUPLICATE KEY UPDATE '.$this->_db->quoteName($type).' = ' . $this->_db->Quote($data));
	        	$ret &= $this->_db->query();
       	}
	    else {
	    	$ret = false;
	    	JError::raiseWarning(0, "User Config values was not saved!");
	    }
        return $ret;
    }
    
    /**
     * @param array() $data
     * @return int payment id
     */
    function storePayment($data)
    {  
    	$old = $this->load('payments',JFactory::getUser()->id);
    	$old = json_decode($old);
    	$paymentid = $data['id'];
    	$old->$paymentid = $data;
		$new['params'] = $old;
    	if($this->store($new,'payments'))
    		return $data['id'];
    }
    
    /**
     * @param int $id of payment
     * @return StdClass custom values for payment
     */
    function loadPayment($id)
    {
    	$data = $this->load('payments',JFactory::getUser()->id);
    	if(!$data)
    		return array();
    	$data = json_decode($data);
    	
    	if(property_exists($data,$id))
    		return $data->$id;
    	else
    		return array();
    }
    
    /**
     * @param int $id user
     * @return stdClass
     */
    function loadPaymentsByUser($id)
    {
    	$data = $this->load('payments',$id);
    	if(!$data)
    		return array();
    	$data = json_decode($data);
    
    	return $data;
    }
    
    
    /**
     * @param string $type column in db <'config','payments','calendar'>
     * @param int $id user
     * @return 
     */
    function load($type, $id = 0)
    {
    	if(!$id)
    		$id = JFactory::getUser()->id;

    	$db = &JFactory::getDBO();
    	/* @var $db JDatabaseMySQL */
    	$config['config'] = null;
    	try{
    		$db->setQuery('SELECT '.$db->quoteName($type).' as `config` FROM `#__booking_user_config` WHERE `user_id` = '.$id);
    		$config = $db->loadAssoc();
    	}catch(JDatabaseException $e){
    		ALog::addException($e,JLog::CRITICAL);
    	}
    	
    	return $config['config'];
    }

    function allDataForUser($id)
    {
    	$db = &JFactory::getDBO();
    	/* @var $db JDatabaseMySQL */
    	$db->setQuery('SELECT * FROM `#__booking_user_config` WHERE `user_id` = '.$id);
    	$config = $db->loadAssoc();
    
    	return $config;
    }
}

?>