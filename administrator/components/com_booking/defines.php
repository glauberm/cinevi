<?php

/**
 * Component Constant Configuration Defines.
 * 
 * @version		$Id$
 * @package		ARTIO Booking 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file'); // with Joomla 1.5 cannot be load 

define('ISJ16', version_compare(JVERSION, '1.6.0') >= 0);
define('ISJ25', version_compare(JVERSION, '2.5.0') >= 0);
//etickets are also using ISJ3
if(!defined('ISJ3'))
	define('ISJ3', version_compare(JVERSION, '3.0.0') >= 0);

//Set defines for component location
$mainframe = &JFactory::getApplication();
/* @var $mainframe JApplication */

define('IS_ADMIN', $mainframe->isAdmin());
define('IS_SITE', $mainframe->isSite());

//Display component name
define('COMPONENT_NAME', 'ARTIO Booking');
//Unique component option use for navigation in Joomla!
define('OPTION', 'com_booking');
define('NAME', 'booking');

//default component encoding
define('ENCODING', 'UTF-8');

define('ADMIN_ROOT', JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . OPTION);
define('SITE_ROOT', JPATH_ROOT . DS . 'components' . DS . OPTION);

define('MANIFEST', ADMIN_ROOT . DS . NAME . '.xml');
define('CONFIG', ADMIN_ROOT . DS . 'config.xml');

if (JFile::exists(MANIFEST)) {
	$manifest = JInstaller::parseXMLInstallFile(MANIFEST);
	define('ASSETS_VERSION', str_replace('.', '', $manifest['version']));
} 

define('PAYMENTS', SITE_ROOT . DS . 'views' . DS . 'payments');

define('CUSTOMER_GID', (ISJ16 ? 2 : 18));

//Component table prefix
define('PREFIX', 'booking');

include_once(ADMIN_ROOT . DS . 'helpers' . DS . 'importer.php');

AImporter::helper('factory', 'html');
$config = &AFactory::getConfig();

//overview: 
//in Book-It config is format stored in strftime format
//1.5 date - format uses strftime string (DATE_FORMAT_LC1=%A, %d %B %Y (strftime))
//1.6 date - format uses date string (DATE_FORMAT_LC1="l, d F Y")
//but bookit lang file overriden DATE_FORMAT_LC1 etc to is always strftime
//JS calendar (_CAL constant) uses strftime format and must have default value specified.

//default date formats
$aDateFormatNormal = $config->dateTypeJoomla ? JText::_('DATE_FORMAT_NORMAL') : ($config->dateNormal ? $config->dateNormal : JText::_('DATE_FORMAT_NORMAL'));
define('ADATE_FORMAT_NORMAL', ISJ16 ? AHtml::strftime2date($aDateFormatNormal) : $aDateFormatNormal);
define('ADATE_FORMAT_NORMAL_CAL', ISJ16 && $config->dateNormal ? AHtml::strftime2date($aDateFormatNormal, true) : $aDateFormatNormal);

$aDateFormatLong = $config->dateTypeJoomla ? JText::_('DATE_FORMAT_LONG') : ($config->dateLong ? $config->dateLong : JText::_('DATE_FORMAT_LONG'));
define('ADATE_FORMAT_LONG', ISJ16 ? AHtml::strftime2date($aDateFormatLong) : $aDateFormatLong);
define('ADATE_FORMAT_LONG_CAL', ISJ16 && $config->dateLong ? AHtml::strftime2date($aDateFormatLong, true) : $aDateFormatLong);

//used in calendars daily/weekly as header
$aDateFormatNice = $config->dateTypeJoomla ? JText::_('DATE_FORMAT_NICE') : $config->dateDay;
define('ADATE_FORMAT_NICE', ISJ16 ? AHtml::strftime2date($aDateFormatNice) : $aDateFormatNice);

//used in monthly calendars in day box
$aDateFormatNiceShort = $config->dateTypeJoomla ? JText::_('DATE_FORMAT_NICE_SHORT') : $config->dateDayShort;
define('ADATE_FORMAT_NICE_SHORT', ISJ16 ? AHtml::strftime2date($aDateFormatNiceShort) : $aDateFormatNiceShort);
define('ADATE_FORMAT_NICE_SHORT_RESPONSIVE', ISJ16 ? AHtml::strftime2date(JText::_('DATE_FORMAT_NICE_SHORT_RESPONSIVE')) : JText::_('DATE_FORMAT_NICE_SHORT_RESPONSIVE'));
define('ADATE_FORMAT_NICE_SHORT_RESPONSIVE2', ISJ16 ? AHtml::strftime2date(JText::_('DATE_FORMAT_NICE_SHORT_RESPONSIVE2')) : JText::_('DATE_FORMAT_NICE_SHORT_RESPONSIVE2'));

//time format
$aTimeFormatShort = $config->dateTypeJoomla ? '%H:%M' : ($config->time ? JText::_($config->time) : '%H:%M');
define('ATIME_FORMAT', ISJ16 ? AHtml::strftime2date($aTimeFormatShort) : $aTimeFormatShort);
define('ATIME_FORMAT_SHORT', ISJ16 ? AHtml::strftime2date($aTimeFormatShort) : $aTimeFormatShort);
define('ATIME_FORMAT_CAL', ISJ16 ? AHtml::strftime2date('%H:%M') : '%H:%M'); //for time picket 


// MYSQL date formats - internal no display
define('ADATE_FORMAT_MYSQL_DATE', ISJ16 ? 'Y-m-d' : '%Y-%m-%d');
define('ADATE_FORMAT_MYSQL_DATE_CAL', '%Y-%m-%d');
define('ADATE_FORMAT_MYSQL_DATETIME_CAL', '%Y-%m-%d %H:%M:%S');
define('ADATE_FORMAT_MYSQL_TIME', ISJ16 ? 'H:i:s' : '%H:%M:%S');
define('ADATE_FORMAT_MYSQL_DATETIME', ISJ16 ? 'Y-m-d H:i:s' : '%Y-%m-%d %H:%M:%S');

//Name of default controller
define('CONTROLLER', 'BookingController');
//Define IDs for component controllers
define('CONTROLLER_SUBJECT', 'subject');
define('CONTROLLER_CUSTOMER', 'customer');
define('CONTROLLER_RESERVATION', 'reservation');
define('CONTROLLER_PAYMENT', 'payment');
define('CONTROLLER_CONFIG', 'config');
define('CONTROLLER_TEMPLATE', 'template');
define('CONTROLLER_ADMIN', 'admin');
define('CONTROLLER_UPGRADE', 'upgrade');
define('CONTROLLER_EXPIRATION', 'expiration');
define('CONTROLLER_ERRORS', 'errors');

define('TEMPLATES_DB_PREFIX', '#__booking_template_');

define('IMAGES', JURI::root() . 'components/' . OPTION . '/assets/images/');
define('IMAGES_SAMPLE', SITE_ROOT . DS . 'assets' . DS . 'images' . DS . 'sample');

define('CACHE_IMAGES_DEPTH', 5);

define('ADMIN_SET_IMAGES_WIDTH', 80);

define('TIME_PICKER_IMAGES', JURI::root() . 'components/' . OPTION . '/assets/libraries/nogray_timepicker/time_picker_files/images');

//Defines for Joomla! user types
define('JUSER_REGISTERED', 'Registered');
define('JUSER_AUTHOR', 'Author');
define('JUSER_EDITOR', 'Editor');
define('JUSER_PUBLISHER', 'Publisher');

define('JUSER_MANAGER', 'Manager');
define('JUSER_ADMINISTRATOR', 'Administrator');
define('JUSER_SUPER_ADMINISTRATOR', 'Super Users');

//Defines for subjects
define('SUBJECT_STATE_PUBLISHED', 1);
define('SUBJECT_STATE_UNPUBLISHED', 0);
define('SUBJECT_STATE_ARCHIVED', - 1);
define('SUBJECT_STATE_DELETED', - 2);

define('SUBJECT_NOFEATURED', 0);
define('SUBJECT_FEATURED', 1);

define('SUBJECT_SHOW_CALENDAR', 0);
define('SUBJECT_SHOW_CONTACT_FORM', 1);

JLoader::register('AModel', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_booking' . DS . 'helpers' . DS . 'model.php');

foreach (AModel::getAccesList() as $id => $title)
    define('SUBJECT_ACCESS_' . JString::strtoupper($title), $id);

define('SUBJECT_BOOKABLE', 1);
define('SUBJECT_PARENT', 2);

//Defines for customers
define('CUSTOMER_STATE_ACTIVE', 1);
define('CUSTOMER_STATE_DELETED', 0);
define('CUSTOMER_STATE_BLOCK', - 1);

define('CUSTOMER_USER_STATE_BLOCK', 1);
define('CUSTOMER_USER_STATE_ENABLED', 0);

define('CUSTOMER_SENDEMAIL', 0);

define('RESERVATION_RECEIVE', 2);
define('RESERVATION_RECEIVE_DEPOSIT', 1);
define('RESERVATION_PENDING', 0);
define('RESERVATION_ONLINE_PENDING', 3);

//Defines for reservations
define('RESERVATION_PRERESERVED', 2);
define('RESERVATION_ACTIVE', 1);
define('RESERVATION_STORNED', 0);
define('RESERVATION_TRASHED', - 1);
define('RESERVATION_CONFLICTED', - 2);

//Defines reservation types
define('RESERVATION_TYPE_HOURLY', 1);
define('RESERVATION_TYPE_DAILY', 2);
define('RESERVATION_TYPE_PERIOD', 3);

//Defines for payments
define('PAYMENT_UNPUBLISHED', 0);
define('PAYMENT_PUBLISHED', 1);
define('PAYMENT_TRASHED', 2);

//Defines for supplements
define('SUPPLEMENT_TYPE_UNSELECT', 0);
define('SUPPLEMENT_TYPE_LIST', 1);
define('SUPPLEMENT_TYPE_YESNO', 2);
define('SUPPLEMENT_TYPE_MANDATORY', 3);
define('SUPPLEMENT_NO_PRICE', 0);
define('SUPPLEMENT_ONE_PRICE', 1);
define('SUPPLEMENT_MORE_PRICES', 2);
define('SUPPLEMENT_EMPTY_UNUSE', 0);
define('SUPPLEMENT_EMPTY_USE', 1);
define('SUPPLEMENT_UNIT_MULTIPLY', 1);

//Defines for frontend views
define('VIEW_SUBJECTS', 'subjects');
define('VIEW_SUBJECT', 'subject');
define('VIEW_SELECTSUBJECTS', 'selectsubjects');
define('VIEW_CUSTOMER', 'customer');
define('VIEW_CUSTOMERS', 'customers');
define('VIEW_RESERVATIONS', 'reservations');
define('VIEW_RESERVATION', 'reservation');
define('VIEW_PAYMENTS', 'payments');
define('VIEW_PAYMENT', 'payment');
define('VIEW_CONFIG', 'config');
define('VIEW_SELECT_TEMPLATE', 'selecttemplate');
define('VIEW_TEMPLATES', 'templates');
define('VIEW_ADMINS', 'admins');
define('VIEW_IMAGES', 'images');
define('VIEW_FILES', 'files');
define('VIEW_UPGRADE', 'upgrade');
define('VIEW_EMAILS', 'emails');
define('VIEW_ARTICLES', 'articles');
define('VIEW_LOCATIONS', 'locations');
define('VIEW_NOTIFICATIONS', 'notifications');
define('VIEW_CLOSINGDAYS', 'closingdays');
define('VIEW_ERRORS', 'errors');

define('SYSTEM_READMORE', '<hr id="system-readmore" />');

define('CALENDARS', SITE_ROOT . DS . 'views' . DS . 'subject' . DS . 'tmpl' . DS);

define('NEWEST_VERSION', 'http://www.artio.cz/updates/joomla/booking2/version');
define('LICENSE', 'http://www.artio.net/license-check');
define('UPGRADE', 'http://www.artio.net/joomla-auto-upgrade');

define('UPLOAD_IMAGE_CLOSE_SET', 1);
define('UPLOAD_IMAGE_LEAVE_OPEN', 2);

define('AIMAGES_TYPE_ONE', 1);
define('AIMAGES_TYPE_MORE', 2);

define('AFILES_TYPE_ONE', 1);
define('AFILES_TYPE_MORE', 2);

define('CHECK_OP_IN', 1);
define('CHECK_OP_OUT', 2);
define('CHECK_OP_NEXT', 3);

define('DAY_LENGTH', 24 * 60 * 60);
define('YEAR_LENGTH', 365 * 24 * 60 * 60);

define('JOOMFISH_SQL', ADMIN_ROOT . DS . 'sql' . DS . 'install.mysql.joomfish.utf8.sql');
define('FALANG_SQL', ADMIN_ROOT . DS . 'sql' . DS . 'install.mysql.falang.utf8.sql');
define('SAMPLE_SQL', ADMIN_ROOT . DS . 'sql' . DS . 'install.mysql.sample.utf8.sql');

define('ADMIN_VIEWS', ADMIN_ROOT . DS . 'views');
define('SITE_VIEWS', SITE_ROOT . DS . 'views');

define('SLIMBOX_BASE', JURI::root() . 'components/' . OPTION . '/assets/libraries/slimbox18/'); //needs MooTools 1.3 - repair for Joomla 3.0 which doesn't work with slimbox1.7.1a
define('SHADOWBOX_BASE', JURI::root() . 'components/' . OPTION . '/assets/libraries/shadowbox303/'); // new library as option instead of slimbox
define('SQUEEZEBOX_BASE', JURI::root() . 'components/' . OPTION . '/assets/libraries/squeezebox/'); //needs MooTools 1.2
define('JQUERY_BASE', JURI::root() . 'components/' . OPTION . '/assets/libraries/jquery/');
define('MOOTOOLS_BASE', JURI::root() . 'components/' . OPTION . '/assets/libraries/mootools/');  

define('DHTMLX_BASE', JURI::root() . 'components/' . OPTION . '/assets/libraries/dhmtlx/calendar/');
define('DHTMLX_SKIN', JURI::root() . 'components/' . OPTION . '/assets/libraries/dhmtlx/calendar/skins/');

define('TIMEPICKER_BASE', JURI::root() . 'components/' . OPTION . '/assets/libraries/nogray_timepicker/');

define('PLAIN_TEXT', 'plain_text');

/*
define('PARAM_LABEL', 3);
define('PARAM_VALUE', 4);
define('PARAM_NAME', 5);
define('PARAM_SEARCHABLES', 6);
define('PARAM_FILTERABLES', 7);
define('PARAM_TYPE', 8);
define('PARAM_ICON', 10);
define('PARAM_NODE', 11);
define('PARAM_OBJECTS', 12);
define('PARAM_OBJECT', 13);
define('PARAM_DISPLAY', 100);
*/
define('PARAM_LABEL', 'label');
define('PARAM_PARAMLABEL', 'paramLabel');
define('PARAM_VALUE', 'value');
define('PARAM_PARAMVALUE', 'paramValue');
define('PARAM_NAME', 'name');
define('PARAM_SEARCHABLES', 'searchable');
define('PARAM_FILTERABLES', 'filterable');
define('PARAM_TYPE', 'type');
define('PARAM_ICON', 'icon');
define('PARAM_NODE', 'node');
define('PARAM_OBJECTS', 'objects');
define('PARAM_OBJECT', 'object');
define('PARAM_DISPLAY', 'display');

define('PARAM_REQUESTNAME', 'requestname');
define('PARAM_REQUESTVALUE', 'requestvalue');
define('PARAM_OPTIONS', 'options');

define('DISPLAY_PROPERTIES_OFF', 0);
define('DISPLAY_PROPERTIES_TABLE', 1);
define('DISPLAY_PROPERTIES_ICON', 2);
define('DISPLAY_PROPERTIES_TEXTS', 3);

define('RESERVING_EXCLUSIVE', 1);
define('RESERVING_CHAIN', 2);
define('RESERVING_OVERLAP', 3);

define('CTYPE_WEEKLY', 'weekly');
define('CTYPE_WEEKLY_MULTI', 'weekly_multi');
define('CTYPE_DAILY', 'daily');
define('CTYPE_MONTHLY', 'monthly');
define('CTYPE_PERIOD', 'period');

define('BOOK_OVER_TIMELINESS_DISALLOW', 0);
define('BOOK_OVER_TIMELINESS_ALLOW', 1);

define('DEPOSIT_MULTIPLY_ALLOW', 1);
define('DEPOSIT_MULTIPLY_DISALLOW', 0);
define('DEPOSIT_TYPE_VALUE', 0);
define('DEPOSIT_TYPE_PERCENT', 1);
define('DEPOSIT_EXCLUDE_SUPPLEMENTS', 0);
define('DEPOSIT_INCLUDE_SUPPLEMENTS', 1);

// reservation property compulsory
define('RS_COMPULSORY', 2);

define('WEEK_EVERY', 0);
define('WEEK_EVEN', 1);
define('WEEK_ODD', 2);

define('CANCEL_NONE', 0);
define('CANCEL_IMMEDIATELY', 1);
define('CANCEL_BEFORE', 2);
define('CANCEL_AFTER', 3);

define('EXPIRE_FORMAT_HOUR', 0);
define('EXPIRE_FORMAT_DAY', 1);

define('PERIOD_TYPE_DAILY', 1);
define('PERIOD_TYPE_WEEKLY', 2);
define('PERIOD_TYPE_MONTHLY', 3);
define('PERIOD_TYPE_YEARLY', 4);

define('PERIOD_END_TYPE_NO', 1); // no period end
define('PERIOD_END_TYPE_AFTER', 2); // after occurences number
define('PERIOD_END_TYPE_DATE', 3); // in date

define('PRICES_NONE', 0);
define('PRICES_WITHOUT_DEPOSIT', 1);
define('PRICES_WITH_DEPOSIT', 2);

define('TIME_RANGE_ONE_DAY', 0);
define('TIME_RANGE_OVER_MIDNIGHT', 1);
define('TIME_RANGE_OVER_WEEK', 2);

define('NOTIFY_EMAIL', 0);
define('NOTIFY_SMS', 1);
define('NOTIFY_ALL', 2);

define('REDIRECTION_AFTER_RESERVATION_THANKYOU_PAGE', 0);
define('REDIRECTION_AFTER_RESERVATION_LATEST_SUBJECT', 1);
define('REDIRECTION_AFTER_RESERVATION_SUBJECT_LIST', 2);
define('REDIRECTION_AFTER_RESERVATION_RESERVATION_LIST', 3);
define('REDIRECTION_AFTER_RESERVATION_HOMEPAGE', 4);
define('REDIRECTION_AFTER_RESERVATION_MENU_ITEM', 5);
define('REDIRECTION_AFTER_RESERVATION_CUSTOM_URL', 6);

define('REDIRECTION_BACK_RESERVATION_LATEST_SUBJECT', 0);
define('REDIRECTION_BACK_RESERVATION_SUBJECT_LIST', 1);
define('REDIRECTION_BACK_RESERVATION_RESERVATION_LIST', 2);
define('REDIRECTION_BACK_RESERVATION_HOMEPAGE', 3);

define('PAYMENT_PAY_BOTH', 1);
define('PAYMENT_PAY_DEPOSIT', 2);
define('PAYMENT_PAY_FULL', 3);

define('DISCOUNT_TYPE_VALUE', 0);
define('DISCOUNT_TYPE_PERCENT', 1);

define('DISCOUNT_PER_UNIT', 0);
define('DISCOUNT_PER_RESERVATION', 1);

define('PROVISION_TYPE_VALUE', 0);
define('PROVISION_TYPE_PERCENT', 1);
?>