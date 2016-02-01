<?php

/**
 * Plugin creates automatically customers in ARTIO Booking.
 * 
 * @package ARTIO Booking Joomla's user plugin
 * @author ARTIO s.r.o.
 * @copyright ARTIO s.r.o.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgUserBooking extends JPlugin
{
    /**
     * Method is called after user data is stored in the database.
     *
     * @param 	array		holds the new user data
     * @param 	boolean		true if a new user is stored
     * @param	boolean		true if user was succesfully stored in the database
     * @param	string		message
     *
     * @return  void
     * @since   1.5
     */
    function onAfterStoreUser($user, $isnew, $success, $msg)
    {
        $this->_createCustomer($user, $isnew, $success, $msg);
    }

    /**
     * Utility method to act on a user after it has been saved.
     *
     * @param	array		$user		Holds the new user data.
     * @param	boolean		$isnew		True if a new user is stored.
     * @param	boolean		$success	True if user was succesfully stored in the database.
     * @param	string		$msg		Message.
     *
     * @return	void
     * @since	1.6
     */
    function onUserAfterSave($user, $isnew, $success, $msg)
    {
        $this->_createCustomer($user, $isnew, $success, $msg);
    }

    /**
     * Create cutomer account in Booking for every non blocked user.
     * 
     * @param array   $user    user data
     * @param boolean $isnew   new user
     * @param boolean $success succesfully stored
     * @param string  $msg     some message
     * @access private
     * @since 1.0.0
     * @return void
     */
    function _createCustomer($user, $isnew, $success, $msg)
    {
        if ($success && $isnew && JRequest::getString('option') != 'com_booking') {
        	
            $db = JFactory::getDbo();
            $app = JFactory::getApplication();
            
            JPlugin::loadLanguage('plg_user_booking', JPATH_ADMINISTRATOR);
            
            $query = 'SELECT COUNT(*) FROM `#__booking_customer` WHERE `user` = %d';
            $query = sprintf($query, $user['id']);
            $db->setQuery($query);
            
            if ($db->loadResult() == 0) {
            	
                $query = 'INSERT INTO `#__booking_customer` (`user`,`surname`,`state`) VALUES (%d, %s, 1)';
                $query = sprintf($query, $user['id'], $db->quote($user['name']));
                $db->setQuery($query);
                
                if ($db->query()) {
                    $app->enqueueMessage($this->params->get('msg'));
                }
            }
        }
    }
}
?>