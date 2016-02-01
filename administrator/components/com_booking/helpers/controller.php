<?php

/**
 * Support for components controllers.
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @subpackage  helpers 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

//import needed Joomla! libraries
jimport('joomla.application.component.controller');
//import needed JoomLIB helpers
AImporter::helper('booking');

class AController extends JControllerLegacy
{
    /**
     * String name of controller usable in request data.
     * 
     * @var string
     */
    var $_controllerName;
    /**
     * Sign if after satisfied task do redirect on another page.
     * 
     * @var boolean
     */
    var $_doRedirect;

    function __construct($config = array())
    {
        parent::__construct($config);
        $this->_doRedirect = true;
    }

    function execute($task)
    {
        parent::execute($task);
        
    
		echo BookingHelper::get();
		
    }
    
    /**
     * @param int $id reservation id
     */
    protected function setGlobalConfigByUserFromReservation($id)
    {
    	if(!$id)
    		return false;
    		
    	//custom config for user who owns subject
    	AImporter::model('reservationitems');
    	AImporter::helper('request');
    	$modelReservationItems = new BookingModelReservationItems();
    	$modelReservationItems->init(array('reservation_item-reservation_id'=>$id));
    	$reservedItemsDb=$modelReservationItems->getData();
    	$reservedItemDb = reset($reservedItemsDb);
    	if($reservedItemDb)
    		$this->setGlobalConfigByUserFromSubject($reservedItemDb->subject);
    	else
    		ALog::add('setGlobalConfigByUserFromReservation(): No user Argument is object, shoul be array',JLog::CRITICAL);
    }
    
    /**
     * @param int $id subject id
     */
    protected function setGlobalConfigByUserFromSubject($id)
    {
    	if(!$id)
    		return false;
    		
    	AImporter::helper('user');
    	AImporter::model('subject');
    	$model = new BookingModelSubject();
    	$model->setId($id);
    	if($user = $model->getObject()->user_id) {
    		$model = new BookingModelUserConfig();
    		if($data = $model->load('config',$user)) {
    			AUser::$id = $user;
    			AFactory::getConfig()->init();
    		}
    	}
    }

    /**
     * Add new object.
     */
    function add()
    {
        if (IS_SITE) {
            JRequest::setVar('view', 'reservation');
            JRequest::setVar('layout', 'form');
            $this->setGlobalConfigByUserFromSubject(JRequest::getInt('id'));
            parent::display();
        } elseif (IS_ADMIN) {
            JRequest::setVar('cid', null);
            $this->editing();
        }
    }

    /**
     * Edit existing object.
     */
    function edit()
    {
        $this->editing();
    }

    /**
     * Copy existing subject
     */
    function copy()
    {
        $this->editing();
    }

    /**
     * Open editing form page.
     * 
     * @param string $view name of view edit form
     */
    function editing($view)
    {
        JRequest::setVar('hidemainmenu', 1);
        JRequest::setVar('layout', 'form');
        JRequest::setVar('view', $view);
        $id = ARequest::getCid();
        $this->_model->setId($id);
        $this->_model->checkout();
        parent::display();
    }

    /**
     * Save object and state on edit page.
     */
    function apply()
    {
        $this->save(true);
    }

    /**
     * Save object.
     * 
     * @param boolean $apply true state on edit page, false return to browse list
     */
    function save($apply = false)
    {
        JRequest::checkToken() or jexit('Invalid Token');
        $post = JRequest::get('post');
        $post['id'] = ARequest::getCid();
        $mainframe = &JFactory::getApplication();
        $id = $this->_model->store($post);
        if ($id !== false) {
            $mainframe->enqueueMessage(JText::_('SUCCESSFULLY_SAVED'), 'message');
            if ($apply) {
                ARequest::redirectEdit($this->_controllerName, $id);
            } else {
                ARequest::redirectList($this->_controllerName);
            }
        }
    }

    /**
     * Cancel edit operation. Check in object and redirect to objects list. 
     */
    function cancel($msg)
    {
        $mainframe = &JFactory::getApplication();
        $id = ARequest::getCid();
        if ($id) {
            $this->_model->setId($id);
            $this->_model->checkin();
        }
        $mainframe->enqueueMessage(JText::_($msg));
        ARequest::redirectList($this->_controllerName);
    }

    /**
     * Set object state by choosen operation.
     * 
     * @param string $operation
     */
    function state($operation, $checkToken = true, $redirect = true)
    {
        if ($checkToken)
            JRequest::checkToken() or jexit('Invalid Token');
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        if (ARequest::controlCids(($cids = ARequest::getCids()), $operation)) {
            if (($success = $this->_model->$operation($cids)) && $this->_doRedirect)
                $mainframe->enqueueMessage(JText::_('SUCCESSFULLY_' . $operation), 'message');
            elseif (! $success && $this->_doRedirect)
                $mainframe->enqueueMessage(JText::_('FAILED_' . $operation), 'error');
        }       
        if ($this->_doRedirect && $redirect)
            ARequest::redirectList($this->_controllerName);
    }

    /**
     * Remove trashed objects.
     */
    function emptyTrash()
    {
        JRequest::checkToken() or jexit('Invalid Token');
        $mainframe = &JFactory::getApplication();
        if ($this->_model->emptyTrash()) {
            $mainframe->enqueueMessage(JText::_('SUCCESSFULLY_EMPTIED_TRASH'), 'message');
        } else {
            $mainframe->enqueueMessage(JText::_('EMPTY_TRASH_FAILED'), 'error');
        }
        ARequest::redirectList($this->_controllerName);
    }

    
    function setTextProperties(&$object, $text)
    {
        $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
        $tagPos = preg_match($pattern, $text);
        if ($tagPos == 0) {
            $object->introtext = $text;
        } else {
            list ($object->introtext, $object->fulltext) = preg_split($pattern, $text, 2);
        }
    }

    function setEditorProperties(&$object)
    {
        if (JString::strlen($object->fulltext) > 1) {
            $object->text = $object->introtext . '<hr id="system-readmore" />' . $object->fulltext;
        } else {
            $object->text = $object->introtext;
        }
    }
}

?>