<?php

/**
 * Rezervation controller.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  controllers
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

//import needed JoomLIB helpers
AImporter::helper('booking', 'config', 'controller', 'parameter', 'request');
//import next model
AImporter::model('customer', 'prices', 'reservation', 'reservations',  'reservationitem',  'reservationitems', 'reservationsupplements', 'reservationtypes', 'subject', 'supplements');
//import needed tables
AImporter::table('price', 'reservationsupplement', 'supplement');
//import needed objects
AImporter::object('box', 'date', 'day', 'interval', 'service');

class BookingControllerExpiration extends AController
{
    
    /**
     * Main model
     * 
     * @var BookingModelExpiration
     */
    var $_model;

    function __construct($config = array())
    {
        parent::__construct($config);
        $this->_model = new BookingModelReservationItems();

        $this->_controllerName = CONTROLLER_EXPIRATION;
    }
    
    
    /**
     * run with CRON	
     */
    function display()
    {    
    	//JRequest::setVar('view', 'customer');
    	//parent::display();
    	$this->checkExpiration();
    }
    
    /**
     * Remove expired.
     */
    function checkExpiration()
    {
    	$this->_model->stornoExpired();
    }
        

}

?>