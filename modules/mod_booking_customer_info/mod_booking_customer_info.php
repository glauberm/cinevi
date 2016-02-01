<?php

/**
 * Display module with information about logged customer
 * and URLs to create new registration, login exists customer,
 * display customers profile and display customers reservations. 
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  modules
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

$helpers = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booking' . DS . 'helpers' . DS;

if (file_exists($helpers . 'importer.php')) {
    
    include_once ($helpers . 'importer.php');
    include_once ($helpers . 'html.php');
    include_once ($helpers . 'model.php');

    $language = JFactory::getLanguage();
    $language->load('com_booking.common', JPATH_ADMINISTRATOR);
    $language->load('com_booking');
    
    AImporter::defines();
    AImporter::helper('route');
    AImporter::model('customer');
    
    $moduleclassSfx = htmlspecialchars($params->get('moduleclass_sfx'));
    
    AHtml::tooltip();

    $reservedItems = BookingHelper::getReservedItems();
    if ($reservedItems) {
        list($fullPrice, $fullDeposit, $fullProvision) = BookingHelper::countOverallPrice(null, $reservedItems);
    }
    
    include (JModuleHelper::getLayoutPath('mod_booking_customer_info'));
}

?>