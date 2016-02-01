<?php

/**
 * Subject controller.
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

class BookingControllerSubject extends AController
{
    
    /**
     * Main model
     * 
     * @var BookingModelSubject
     */
    var $_model;

    function __construct($config = array())
    {
        parent::__construct($config);
        $this->_model = $this->getModel('subject');
        $this->_controllerName = CONTROLLER_SUBJECT;
    }

    /**
     * Display default view - subjects list	
     */
    function display()
    {
        switch ($this->getTask()) {
            case 'publish':
            case 'unpublish':
            case 'feature':
            case 'unfeature':
            case 'archive':
            case 'unarchive':
            case 'trash':
            case 'restore':
                $this->state($this->getTask());
                break;
        }
        JRequest::setVar('view', 'subjects');
        parent::display();
    }

    /**
     * Open editing form page
     */
    function editing()
    {
        parent::editing('subject');
    }

    /**
     * Cancel edit operation. Check in subject and redirect to subjects list. 
     */
    function cancel()
    {
        parent::cancel('SUBJECT_EDITING_CANCELED');
    }
    
    /**
     * Save items ordering 
     */
    function saveorder()
    {
        JRequest::checkToken() or jexit('Invalid Token');
        $cids = ARequest::getCids();
        $order = ARequest::getIntArray('order');
        if (ARequest::controlCids($cids, 'save order')) {
            $mainframe = &JFactory::getApplication();
            if ($this->_model->saveorder($cids, $order)) {
                $mainframe->enqueueMessage(JText::_('SUCCESSFULLY_SAVED_ORDER'), 'message');
            } else {
                $mainframe->enqueueMessage(JText::_('ORDER_SAVE_FAILED'), 'error');
            }
        }
        ARequest::redirectList(CONTROLLER_SUBJECT);
    }

    /**
     * Move item up in ordered list
     */
    function orderup()
    {
        $this->setOrder(- 1);
    }

    /**
     * Move item down in ordered list
     */
    function orderdown()
    {
        $this->setOrder(1);
    }

    /**
     * Set item order
     * 
     * @param int $direct move direction
     */
    function setOrder($direct)
    {
        JRequest::checkToken() or jexit('Invalid Token');
        $cid = ARequest::getCid();
        $mainframe = &JFactory::getApplication();
        if ($this->_model->move($cid, $direct)) {
            $mainframe->enqueueMessage(JText::_('SUCCESSFULLY_MOVED_ITEM'), 'message');
        } else {
            $mainframe->enqueueMessage(JText::_('ITEM_MOVE_FAILED'), 'error');
        }
        ARequest::redirectList(CONTROLLER_SUBJECT);
    }

    /**
     * Set item access to public
     */
    function accesspublic()
    {
        $this->setAccess(SUBJECT_ACCESS_PUBLIC);
    }

    /**
     * Set item access to registered
     */
    function accessregistered()
    {
        $this->setAccess(SUBJECT_ACCESS_REGISTERED);
    }

    /**
     * Set item access to special
     */
    function accessspecial()
    {
        $this->setAccess(SUBJECT_ACCESS_SPECIAL);
    }

    /**
     * Set item access
     * @param int $access access value
     */
    function setAccess($access)
    {
        JRequest::checkToken() or jexit('Invalid Token');
        $cid = ARequest::getCid();
        $mainframe = &JFactory::getApplication();
        if ($this->_model->setAccess($cid, $access)) {
            $mainframe->enqueueMessage(JText::_('SUCCESSFULLY_SET_ACCESS'), 'message');
        } else {
            $mainframe->enqueueMessage(JText::_('SET_ACCESS_FAILED'), 'error');
        }
        ARequest::redirectList(CONTROLLER_SUBJECT);
    }

    /**
     * Save subject and state on edit page.
     */
    function apply()
    {
        $this->save(true);
    }

    /**
     * Save subject.
     * 
     * @param boolean $apply true state on edit page, false return to browse list
     */
    function save($apply = false)
    {
        JRequest::checkToken() or jexit('Invalid Token');
        
        $mainframe = &JFactory::getApplication();
        
        $post = JRequest::get('post');
        
        $post['id'] = ARequest::getCid();
        $post['text'] = JRequest::getVar('text', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $post['pdf_ticket_template'] = JRequest::getVar('pdf_ticket_template', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $post['google_maps_code'] = JRequest::getVar('google_maps_code', '', 'post', 'string', JREQUEST_ALLOWRAW);
        
        $id = $this->_model->store($post);
        
        if ($id == -1) {
        	$mainframe->enqueueMessage(JText::_('OBJECTS_COUNT_IN_FREE_VERSION_IS_LIMITED_TO_2_BUY_FULL_VERSION'), 'notice');
        	ARequest::redirectMain();
        	return;
        }
        
        if ($id !== false) {
            $mainframe->enqueueMessage(JText::_('SUCCESSFULLY_SAVED'), 'message');
        } else {
            $mainframe->enqueueMessage(JText::_('SAVE_FAILED'), 'error');
        }
        if ($apply) {
            ARequest::redirectEdit(CONTROLLER_SUBJECT, $id);
        } else {
            ARequest::redirectList(CONTROLLER_SUBJECT);
        }
    
    }

    /**
     * Change subject template.
     */
    function changeTemplate()
    {
        $mainframe = &JFactory::getApplication();
        $id = ARequest::getCid();
        $template = JRequest::getInt('template');
        $result = $this->_model->changeTemplate($id, $template);
        switch ($result) {
            case 1:
                $mainframe->enqueueMessage(JText::_('SUCCESSFULLY_CHANGED'), 'message');
                break;
            case 0:
                $mainframe->enqueueMessage(JText::_('SUBJECT_ALREADY_HAVE_THIS_TEMPLATE'), 'notice');
                break;
            case - 1:
                $mainframe->enqueueMessage(JText::_('UNABLE_TO_CHANGE_TEMPLATE'), 'error');
                break;
        }
        ARequest::redirectEdit(CONTROLLER_SUBJECT, $id);
    }

    /**
     * Delete subject template.
     */
    function deleteTemplate()
    {
        $mainframe = &JFactory::getApplication();
        $id = ARequest::getCid();
        $result = $this->_model->deleteTemplate($id);
        if ($result) {
            $mainframe->enqueueMessage(JText::_('SUCCESSFULLY_DELETED_TEMPLATE'), 'message');
        } else {
            $mainframe->enqueueMessage(JText::_('TEMPLATE_DELETE_FAILED'), 'error');
        }
        ARequest::redirectEdit(CONTROLLER_SUBJECT, $id);
    }
    
    function copy() 
    {
    	$cid = ARequest::getCids();
    	if ($this->_model->copy($cid)) {
    		$mainframe = &JFactory::getApplication();
    		$mainframe->enqueueMessage(JText::sprintf('COPIED_OBJECTS', count($cid)), 'message');
    	}
    	ARequest::redirectList(CONTROLLER_SUBJECT);
    }
    
    function sendContactForm()
    {
    	if (IS_SITE) {
    		$from = JRequest::getVar('email','');
    		$fromname = JRequest::getVar('name','');
    		$text = JRequest::getVar('message','');
    		$datefrom = JRequest::getVar('date_from','');
    		$dateto = JRequest::getVar('date_to','');
    		 
    		$subject_id = JRequest::getInt('id','');
    		 
    		AImporter::model('subject');
    		$model = new BookingModelSubject();
    		$model->setId($subject_id);
    		$subject = $model->getObject();
    		
    		$body = "From: ".$datefrom."<br>To: ".$dateto."<br>Object: ".$subject->title."<br>".$text;
    		
    		if($subject->contact_email)
	    		$email = $subject->contact_email;
    		else{
    			$config = AFactory::getConfig();
    			$email = $config->mailingManager;
    		}
	    	
	    	$mainframe = &JFactory::getApplication();
	    	if($from && $fromname && $email && $body)
	    	{
	    		$sended = JFactory::getMailer()->sendMail($from, $fromname, $email, 'Booking request', $body, true);
	    		if($sended)
	    			$mainframe->enqueueMessage("Email sent", 'message');
	    		else
	    			$mainframe->enqueueMessage("Email send error", 'message');
	    	}
	    	else
	    		$mainframe->enqueueMessage("Bad input data", 'message');
    	}
    	$mainframe = &JFactory::getApplication();
        $mainframe->redirect(JRoute::_(ARoute::view(CONTROLLER_SUBJECT,$subject_id)));
    }
    
    function suggest()
    {
    	die(json_encode($this->_model->suggest(JRequest::getString('request'))));
    }    
    
    public function getMonthData() {
        $config = AFactory::getConfig();
        AImporter::model('customer', 'occupancytypes', 'reservationtypes', 'reservations', 'reservationitems', 'prices', 'subject', 'subjects', 'supplements', 'template');
        AImporter::helper('booking', 'config', 'document', 'image', 'parameter', 'string');
        AImporter::object('box', 'date', 'day', 'service');
        $setting = new BookingCalendarSetting();
        $subject = new BookingModelSubject();
        $subject->setId($id = (int) $subject->getNearestBooking(null, JRequest::getString('year'), JRequest::getString('month')));
        
        $route = JRoute::_(ARoute::view(VIEW_SUBJECT, $id), false);
        $juri = JUri::getInstance($route);
        $path = $juri->getPath();
        $query = $juri->getQuery(true);
        $days = BookingHelper::getMonthlyCalendar($subject->getObject(), $setting);
        $response = array();
        foreach ($days->calendar as $day) {
            $date = $day->date;
            $response[$date] = array(false);
            if (!$day->engaged){
                $response[$date] = false;
                foreach ($day->boxes as $box) {
                    if (!$box->closed) {
                        foreach ($box->services as $service) {
                            if ($service->allowFixLimit || ($service->rtype == RESERVATION_TYPE_DAILY && (($config->bookCurrentDay && $day->Uts >= strtotime($setting->currentDate)) || ($day->Uts > $setting->currentDayUTS)) && $service->canReserve)) {
                                if (!$service->notBeginsFixLimit) {
                                    if ($service->fix) {
                                        $date2 = JFactory::getDate($date);
                                        $date2->modify('+ 1 days');
                                        $value = $date2->format(ADATE_FORMAT_MYSQL_DATE);
                                    } else {
                                        $value = $service->toDate;
                                    }
                                    $response[$date] = array(true, $value, $path, $query);
                                }
                            }
                        }
                    }
                }
            }
        }
        ob_clean();
        die(json_encode($response));
    }
}

?>