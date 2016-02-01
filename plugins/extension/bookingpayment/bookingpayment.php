<?php

/**
 * @version	$Id$
 * @package	ARTIO Booking
 * @subpackage	plugins/system
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license   	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Joomla system plug-in. Dispatched always when Joomla core starts.
*/
class plgExtensionBookingpayment extends JPlugin
{

	function onExtensionAfterInstall($installer,$eid)
	{
		//only for booking payment plugin
		if($installer->manifest['type'] == 'plugin' && $installer->manifest['group'] == 'bookingpayment')
		{
			$this->installPluginToBooking($this->getPluginAlias($installer));
		}
		return true;
	}
	
	private function getPluginAlias($installer)
	{
		$result = array();
		foreach($installer->manifest->files->filename as $file)
		{
			if((string)$file['plugin'])
			{
				$result['alias'] = (string)$file['plugin'];
				break;
			}
		}
		
		$result['title'] = (string)$installer->manifest->name;
		return $result;
	}
	
	private function installPluginToBooking($data)
	{
		$payment = new stdClass();
		$payment->alias = $data['alias'];
		//if label is translated or use alias
		$payment->title = ($data['title'] != strtoupper($data['title']))? $data['title'] : ucfirst($payment->alias);

		//select if payment is already installed
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
		->select('a.id')
		->from('#__booking_payment AS a')
		->where("a.alias LIKE '".$payment->alias."'");

		$db->setQuery($query);
		$results = $db->loadObjectList();
		
		
		if(empty($results))
		{
			try {
				// Insert the payment if not installed
				$result = JFactory::getDbo()->insertObject('#__booking_payment', $payment);
			} catch (Exception $e) {
				JLog::add('bookingpayment plugin: payment '.$payment->alias.' can\'t be installed.'.$e->getMessage(),Jlog::ALERT);
			}
		}
		else
			JLog::add('bookingpayment plugin: payment '.$payment->alias.' is already installed',Jlog::INFO);
	}
}