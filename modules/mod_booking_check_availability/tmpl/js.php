<?php

/**
 * @package		 ARTIO Booking
 * @subpackage  modules
 * @copyright	 Copyright (C) 2014 ARTIO s.r.o.. All rights reserved.
 * @author 		 ARTIO s.r.o., http://www.artio.net
 * @link         http://www.artio.net Official website
 */
defined('_JEXEC') or die('Restricted access');

$js = '
var MOD_BOOKING_CHECK_AVAILABILITY_FIRSTDAY = ' . (int) JFactory::getLanguage()->getFirstDay() . ';
var MOD_BOOKING_CHECK_AVAILABILITY_IFFORMAT = "' . addslashes(ADATE_FORMAT_MYSQL_DATE_CAL) . '";
var MOD_BOOKING_CHECK_AVAILABILITY_DAFORMAT = "' . addslashes(ADATE_FORMAT_NORMAL_CAL) . '";
var MOD_BOOKING_CHECK_AVAILABILITY_DATA = {};
var MOD_BOOKING_CHECK_AVAILABILITY_BASE = "' . addslashes(JRoute::_('index.php')) . '";
';
JHtml::_('behavior.calendar');
JFactory::getDocument()->addScriptDeclaration($js);
JHtml::_('script', 'modules/mod_booking_check_availability/assets/js/scripts.js');
