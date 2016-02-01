<?php

/**
 * Config controller.
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
AImporter::helper('request', 'controller');

class BookingControllerConfig extends AController
{
    /**
     * Main model
     * 
     * @var BookingModelConfig
     */
    var $_model;

    function __construct($config = array())
    {
        parent::__construct($config);
        if (! class_exists('BookingModelConfig'))
            AImporter::model('config');
        $this->_model = new BookingModelConfig();
        $this->_controllerName = CONTROLLER_CONFIG;
    }

    /**
     * Save component configuration.
     * 
     * @param boolean $apply true/false ... (save and stay on edit page)/(save and go to controll panel)
     * @return void
     */
    function save($apply = false)
    {
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $this->_model->store(JRequest::get('post')) ? $mainframe->enqueueMessage(JText::_('SUCCESSFULLY_SAVED'), 'message') : $mainframe->enqueueMessage(JText::_('SAVE_FAILED'), 'error');
        $apply ? ARequest::redirectView(VIEW_CONFIG) : ARequest::redirectMain();
    }

    /**
     * Cancel edit operation.
     * 
     * @return void 
     */
    function cancel()
    {
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $mainframe->enqueueMessage(JText::_('CONFIGURATION_CANCELLED'), 'message');
        ARequest::redirectMain();
    }
}

?>