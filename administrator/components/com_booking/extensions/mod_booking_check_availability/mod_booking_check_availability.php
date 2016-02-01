<?php

/**
 * @package		 ARTIO Booking
 * @subpackage  modules
 * @copyright	 Copyright (C) 2014 ARTIO s.r.o.. All rights reserved.
 * @author 		 ARTIO s.r.o., http://www.artio.net
 * @link         http://www.artio.net Official website
 */
defined('_JEXEC') or die('Restricted access');

JFactory::getLanguage()->load('com_booking.common', JPATH_ADMINISTRATOR);

require_once JPATH_ROOT . '/administrator/components/com_booking/helpers/importer.php';
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

AImporter::defines();
AImporter::model('subject');
$model = new BookingModelSubject();

$nearest = $model->getNearestBooking(false);
$now = JFactory::getDate()->format('Y-m-01');
if (empty($nearest) || $nearest < $now) {
	$nearest = $now;
} 

require_once JModuleHelper::getLayoutPath('mod_booking_check_availability');
require_once 'tmpl/js.php';

$doc = JFactory::getDocument();
$doc->addStyleSheet('modules/mod_booking_check_availability/assets/css/general.css');
