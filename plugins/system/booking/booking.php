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
class plgSystemBooking extends JPlugin
{

	public function onAfterDispatch()
	{
		if ($this->params->get('cron_enabled', 0) == 0)
			return;
		
		$log = JPath::clean(JFactory::getApplication()->getCfg('log_path') .'/plg_system_booking_cron');

		$current = JFactory::getDate();
		if (!JFile::exists($log)) {
			$latest = JFactory::getDate();
			JFile::write($log, $current->toSql());
		} else 
			$latest = JFactory::getDate(JFile::read($log));

		$latest->modify('+ ' . $this->params->get('cron_schedule', 60) . ' minutes');
		
		if ($latest->toSql() < $current->toSql()) {

			JLoader::import('components.com_booking.helpers.importer', JPATH_ADMINISTRATOR);
			JLoader::import('components.com_booking.defines', JPATH_ADMINISTRATOR);

			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_booking/models');
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_booking/tables');

			$modelNotifications = JModelLegacy::getInstance('Notifications', 'BookingModel');
			/* @var $modelNotifications BookingModelNotifications */

			$modelNotifications->notifyenqueue();
			$modelNotifications->notifysend();

			JFile::write($log, $current->toSql());
		}
	}
}