<?php

/**
 * Support for Joomla! users.
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

AImporter::model('userconfig');

class AUser
{
	/**
	 * @var int id of user with custom config (frontend)
	 */
	public static $id = null;
	
	function __construct($id = 0)
	{
		if($id)
			self::$id = $id;
	}
	
	/**
	 * Get all available user groups.
	 *
	 * @return array list of usergroups titles
	 */
	function getUserGroups()
	{
	
		$db = &JFactory::getDBO();
		/* @var $db JDatabaseMySQL */
		$db->setQuery('SELECT `title` FROM `#__usergroups` ORDER BY `id`');
		$usergroups = &$db->loadAssocList();
		return $usergroups;
	}
	
	/**
	 * @return number of current user, if has limitation only for own objects
	 */
	function onlyOwner()
	{
		if(!JFactory::getUser()->authorise('booking.item.manage', 'com_booking'))
			return JFactory::getUser()->id;
		else
			return 0;
	}
	
	/**
	 * @return stdClass with custom global cofiguration for user AUser::$id
	 */
	function globalConfig()
	{
		$userconfig = new BookingModelUserConfig();
		return $userconfig->load('config',self::$id);
	}
	
	/**
     * @return stdClass with payments and custom config values
     */
    function paymentConfig()
    {
    	$userconfig = new BookingModelUserConfig();
    	return $userconfig->loadPaymentsByUser(self::$id);
    }
	
	function templateConfig()
	{
		$userconfig = new BookingModelUserConfig();
		return $userconfig->load('calendar',self::$id);
	}
	
	/**
	 * Can user manage reservations of some objects.
	 * @return array list of allowed objects
	 */
	function manageReservations()
	{
		static $allowed;
		
		if (is_null($allowed)) {

			$user = JFactory::getUser();
			
			$cache = JFactory::getCache('com_booking_acl', '');
			$cacheId = 'reservations.manage.'.$user->get('id');
			
			$cached = $cache->get($cacheId);
			if ($cached)
				$allowed = $cached;
			else {
				$allowed = array(0);
				
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)->select('subject')->from('#__booking_reservation_items')->group('subject');
				$db->setQuery($query);
				$cid = $db->loadColumn();
			
				foreach ($cid as $id)
					if ($user->authorise('booking.reservations.manage', 'com_booking.subject.'.$id))
						$allowed[] = $id;
				
				$cache->store($allowed, $cacheId);
			}
		}
		return $allowed;
	}
	
	/**
	 * Get list of user email allowed in some rule.
	 * @param string $action
	 * @param string $assetname
	 * @return array
	 */
	function getActionReceivers($action, $assetname)
	{
		static $cache;
		$cacheKey = $action.'.'.$assetname;
		
		if (isset($cache[$cacheKey]))
			return $cache[$cacheKey];
		
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)->select('id')->from('#__usergroups');
		$usergroups = $db->setQuery($query)->loadColumn();
		
		$allowedGroups = array();
		$allowedUsers = array();
		
		foreach ($usergroups as $usergroup) // search usergroups allowed in rule
			if (JAccess::checkGroup($usergroup, $action, $assetname))
				$allowedGroups[] = $usergroup;
		
		if (!empty($allowedGroups)) { // search user email in allowed usergroups
			$query = $db->getQuery(true)->select('email')->from('#__users AS u')->join('', '#__user_usergroup_map AS m ON u.id = m.user_id')->where('m.group_id IN ('.implode(', ', $allowedGroups).')');
			
			$allowedUsers = $db->setQuery($query)->loadColumn();
		}
			
		$cache[$cacheKey] = $allowedUsers;
		
		return $allowedUsers;
	}
	
	function userExists($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)->select('COUNT(*)')->from('#__users')->where('id='.(int)$id);
		return $db->setQuery($query)->loadResult() === '1';
	}
	
	/**
     * Get managers of reservation allowed to receive some notification.
     * @param string $action ACL action string
     * @param array $items reservation items
     * @param bool  $global add global manager e-mail
     * @return array
     */
    public function getNotificationManagers($action, $items, $global = true)
    {
    	$config = AFactory::getConfig();
    	$receivers = array();
    	foreach ($items as $item) // search managers allowed to manage reservations of reserved subject
    		$receivers = array_merge($receivers, AUser::getActionReceivers($action, 'com_booking.subject.'.$item->subject));
        if ($global)
            $receivers = array_merge($receivers, $config->mailingManager); // combine with global manager
    	// cleanup
    	$receivers = array_unique($receivers);
    	$receivers = array_map('trim', $receivers);
    	$receivers = array_filter($receivers, 'strlen'); // remove empty
    	
    	return $receivers;
    }
}

?>