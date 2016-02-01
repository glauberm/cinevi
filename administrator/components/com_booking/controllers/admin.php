<?php

/**
 * Admins controller.
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
AImporter::helper('booking', 'controller', 'parameter', 'request');
//import needed tables
AImporter::table('admin');

class BookingControllerAdmin extends AController
{
    
    /**
     * Main model
     * 
     * @var BookingModelAdmin
     */
    var $_model;

    function __construct($config = array())
    {
        parent::__construct($config);
        $this->_model = $this->getModel('admin');
        $this->_controllerName = CONTROLLER_ADMIN;
    }

    /**
     * Display default view - admins list	
     */
    function display()
    {
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        
        JRequest::setVar('view', 'admins');
        $task = $this->getTask();
        switch ($task) {
            case 'setAsAdmin':
            case 'setAsNoAdmin':
                $this->_redirect = true;
                $this->state($task);
                break;
        }
        parent::display();
    }
}

?>