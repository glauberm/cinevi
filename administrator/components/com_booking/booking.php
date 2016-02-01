<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

//Logs are in /logs/
//Include the JLog class.
jimport('joomla.log.log');

// throw exception when database error happens (instead of calling db->getErrorMsg())
JError::$legacy = false;

//if (function_exists('date_default_timezone_set'))
//date_default_timezone_set('GMT');

$tmpl = trim(JRequest::getVar('tmpl'));
if (empty($tmpl))
	JRequest::setVar('tmpl',null);

include_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'importer.php');
include_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
include_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'model.php');
include_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'log.php');

// set error logger and log it.
$error_lifetime = JFactory::getApplication()->getUserState('com_booking.errors.lifetime', 0, 'int');
$error_lifetime = ($error_lifetime > time())? 1 : 0; 
ALog::init($error_lifetime);

$language = &JFactory::getLanguage();
/* @var $language JLanguage */

$language->load('com_booking.common', JPATH_ADMINISTRATOR, null, true);
$language->load('com_booking', JPATH_SITE, null, true);

AImporter::defines();

JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');

if (ISJ3 && IS_ADMIN) {
	$document = & JFactory::getDocument();

	//css similar to j2.5
	$document->addStyleSheet('components/com_booking/assets/css/joomla3.css');
	
	//load jquery UI with jquery in noConflict() mode
	JHtml::_('jquery.ui');
}

//JHtml::_('behavior.mootools');
JHtml::_('behavior.framework');
JHtml::_('behavior.framework', true);
AImporter::helper('booking', 'factory', 'html', 'installer');
AInstallerJoomFish::init();

$config = AFactory::getConfig();

if ($config->enableResponsive && IS_SITE)
	AImporter::css('responsive');
else
AImporter::css('general');

AImporter::js('common', 'joomla.javascript', 'view-images');

AHtml::tooltip();

//register custom button classes
if(!ISJ3)
	JLoader::register('JButtonALink', dirname(__FILE__) . DS . 'helpers' . DS . 'toolbar' . DS . 'alink.php');
else{
	JLoader::register('JToolbarButtonALink', dirname(__FILE__) . DS . 'helpers' . DS . 'toolbar' . DS . 'alink.php');
	
	//override standard popup button with generating similar to Joomla 2.5
	JLoader::register('JToolbarButtonPopup', dirname(__FILE__) . DS . 'helpers' . DS . 'toolbar' . DS . 'apopup.php',true);
}

//Cancel expired reservations
JPluginHelper::importPlugin('booking');
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('OnDeleteExpired');

if (class_exists(($classname = AImporter::controller()))) {
    $controller = new $classname();
    /* @var $controller JController */
    $controller->execute(JRequest::getVar('task'));
    $controller->redirect();
}

?>