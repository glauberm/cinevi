<?php

/**
 * triggering event plugins
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class AEvent
{
	
	/**
	 * calls event on plugin
	 *
	 * @param string $event Name of vent in plugin
	 * @param mixed $params params for plugin
	 * @return array
	 */
	public static function payment($event,$params)
	{
		JPluginHelper::importPlugin('bookingpayment');
        $dispatcher = JDispatcher::getInstance();
        $results = $dispatcher->trigger($event,$params);
        
        return $results;
	}
	
	/**
	 * Returns data from plugin with name/alias $param
	 *
	 * @param string $event Name of vent in plugin
	 * @param mixed $params Plugin name/alias . Can be added other params for plugin.
	 * @return data
	 */
	public static function OnePayment($event,$params)
	{
		$results = self::payment($event,$params);
		return reset(array_filter($results, 'AEvent::isNotFalse'));
	}
	
	private static function isNotFalse($value) {
		return $value !== false;
	}
}

?>