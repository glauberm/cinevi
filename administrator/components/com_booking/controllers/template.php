<?php

/**
 * Template controller.
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

class BookingControllerTemplate extends AController
{
    
    /**
     * Main model
     * 
     * @var BookingModelTemplate
     */
    var $_model;

    function __construct($config = array())
    {
        parent::__construct($config);
        
        $this->_model = $this->getModel('template');
        $this->_controllerName = CONTROLLER_TEMPLATE;
    }

    /**
     * Display default view - templates list	
     */
    function display()
    {
        JRequest::setVar('view', 'templates');
        
        parent::display();
    }

    /**
     * Open editing form page
     */
    function editing()
    {
        JRequest::setVar('hidemainmenu', 1);
        JRequest::setVar('layout', 'form');
        JRequest::setVar('view', 'template');
        
        parent::display();
    }

    /**
     * Cancel edit operation. Redirect to templates list. 
     */
    function cancel()
    {
        JRequest::checkToken() or jexit('Invalid Token');
        
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        
        $mainframe->enqueueMessage(JText::_('TEMPLATE_EDITING_CANCELED'));
        
        ARequest::redirectList($this->_controllerName);
    }

    /**
     * Save template and redirect to templates list or template editing page.
     * 
     * @param boolean $apply 
     */
    function save($apply = false)
    {
        JRequest::checkToken() or jexit('Invalid Token');
        
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        
        $id = ARequest::getCid();
        $copy = JRequest::getInt('copy');
        $name = JRequest::getString('name');
        $post = &JRequest::get('post');
        
        $templateId = $this->_model->store($id, $copy, $name, $post);
        
        $mainframe->enqueueMessage(JText::_('SUCCESSFULLY_SAVED'), 'message');
        
        if ($apply) {
            ARequest::redirectEdit($this->_controllerName, $templateId);
        } else {
            ARequest::redirectList($this->_controllerName);
        }
    }

    function trash()
    {
        JRequest::checkToken() or jexit('Invalid Token');
        
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        
        $cid = &ARequest::getCids();
        
        $templateHelper = &AFactory::getTemplateHelper();
        
        foreach ($cid as $id) {
            $template = &$templateHelper->getTemplateById($id);
            if ($template->haveItems()) {
                $mainframe->enqueueMessage(JText::sprintf('Unable delete template %s. Have saved objects.', $template->name), 'error');
            } elseif ($template->id) {
                $template->delete();
                $this->_model->delete($id);
            }
        }
        
        $mainframe->enqueueMessage(JText::_('TEMPLATES_DELETED'), 'message');
        
        ARequest::redirectList($this->_controllerName);
    }
}

?>