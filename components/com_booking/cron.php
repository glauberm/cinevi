<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', realpath(dirname(__FILE__) . '/../../'));

require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

JFactory::getApplication('site');

JFactory::getLanguage()->load('com_booking.common', JPATH_ADMINISTRATOR);
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_booking/models');
JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_booking/tables');
require_once JPATH_ADMINISTRATOR . '/components/com_booking/defines.php';

$model = JModelLegacy::getInstance('Notifications', 'BookingModel');
/* @var $model BookingModelNotifications */
$model->notifyenqueue();
$model->notifysend();