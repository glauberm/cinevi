<?php

/**
 * Upgrade controller.
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
AImporter::helper('controller', 'parameter');

class BookingControllerUpgrade extends AController
{
    
    /**
     * Main model
     * 
     * @var BookingModelUpgrade
     */
    var $_model;

    function __construct($config = array())
    {
        parent::__construct($config);
        
        $this->_model = &$this->getModel('upgrade');
        $this->_controllerName = CONTROLLER_UPGRADE;
    }

    /**
     * Start component upgrade from remote server.
     */
    function doUpgrade()
    {
        JRequest::checkToken() or jexit('Invalid Token');
        
        $result = $this->_model->upgrade();
        $this->_model->setState('result', $result);
        
        JRequest::setVar('message', $this->_model->getState('message'));
        JRequest::setVar('view', 'upgrade');
        JRequest::setVar('layout', 'message');
        
        parent::display();
    }
}

?>