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
AImporter::helper('booking', 'config', 'controller', 'parameter', 'request', 'html', 'cache', 'smsservice', 'httphelper', 'utils');
//import next model
AImporter::model('customer', 'prices', 'reservation', 'reservations',  'reservationitem',  'reservationitems', 'reservationsupplements', 'reservationtypes', 'subject', 'supplements');


//import needed tables
AImporter::table('price', 'reservationsupplement', 'supplement');
//import needed objects
AImporter::object('box', 'date', 'day', 'interval', 'service');

class BookingControllerReservation extends AController
{
    
    /**
     * Main model
     * 
     * @var BookingModelReservation
     */
    var $_model;

    function __construct($config = array())
    {
        parent::__construct($config);
        if (IS_ADMIN) {
            $this->_model = $this->getModel('reservation');
        } elseif (IS_SITE) {
            $this->_model = new BookingModelReservation();
        }
        $this->_controllerName = CONTROLLER_RESERVATION;
    }
         
        
    /**
     * Display default view - reservation list.	
     */
    function display()
    {
    	//custom config for user who owns subject
    	if($id = ARequest::getCid())
    		$this->setGlobalConfigByUserFromReservation($id);
    	
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        
        if ($this->_doRedirect) {
            JRequest::setVar('view', 'reservations');
        }
        
        $modelCustomer = new BookingModelCustomer();
        $modelCustomer->setIdByUserId();
        
        $config = AFactory::getConfig();
        
        if ($modelCustomer->isCustomer()) {
            switch ($this->getTask()) {
                case 'storno':
                    $cids = ARequest::getCids();
                    if (count($cids)) {
                        if ($this->_model->stornoSafe($modelCustomer->getId(), $cids)) {
                          	            	            if ($this->_doRedirect) {
                	        	$mainframe->redirect(ARoute::convertUrl(JRoute::_(ARoute::view(VIEW_RESERVATIONS))), JText::_('SUCCESSFULLY_CANCELLED'), 'message');
                            }
                        } else {
                            if ($this->_doRedirect) {
                                $mainframe->redirect(ARoute::convertUrl(JRoute::_(ARoute::view(VIEW_RESERVATIONS))), JText::_('CANCELLATION_FAILED'), 'error');
                            }
                        }
                    } else {
                        if ($this->_doRedirect) {
                            $mainframe->redirect(ARoute::convertUrl(JRoute::_(ARoute::view(VIEW_RESERVATIONS))), JText::_('CHOOSE_RESERVATIONS_FROM_LIST_TO_CANCEL'), 'notice');
                        }
                    }
                case 'detail':
                    JRequest::setVar('view', 'reservation');
                    break;
                default:
                    parent::display();
                    return;
            }
        }
        
        if (IS_ADMIN || (IS_SITE && $modelCustomer->isAdmin())) {
            
            $id = ARequest::getCid();
            
            $task = $this->getTask();
            
            switch ($task) {
                case 'receive':
                case 'receiveDeposit':
                case 'unreceive':
                case 'storno':
                case 'trash':
                case 'active':
                case 'restore':
                case 'conflict':
                case 'prereserved':
                	$this->state($task, true, false);
                    $cids = ARequest::getCids();
                    foreach ($cids as $id) {
                        $this->changeStatusInfo($id, $task);
                    }
                	ARequest::redirectList($this->_controllerName);
                    break;
                case 'detail':
                    JRequest::setVar('view', 'reservation');
                    break;
            }
        }
        if ($this->_doRedirect) {
            parent::display();
        }
    }

    /**
     * Send information (Email) to customer about change reservation status.
     * 
     * @param int $id reservation ID
     * @param string $task request parameter
     */
    function changeStatusInfo($id, $task)
    {
    	if (!$id){
    		JError::raiseWarning(400,'Id not set');
    		return false;
    	}
    	
        $config = &AFactory::getConfig();
        
        if ($config->mailingStatusClient || $config->mailingStatusManager || $config->mailingStatusSupplier) {
            
            switch ($task) {
                case 'receive':
                case 'active':
                    $status = JText::_('RESERVATION_ACCEPTED');
                    break;
                case 'receiveDeposit':
                    $status = JText::_('RESERVATION_DEPOSIT_RECEIVED');
                    break;
                case 'unreceive':
                    $status = JText::_('RESERVATION_DEPOSIT_NOT_RECEIVED');
                    break;
                case 'storno':
                    $status = JText::_('RESERVATION_CANCELLED');
                    break;
                case 'onlinePending':
                	$status = JText::_('ONLINE_PENDING');
                	break;
                case 'change':
                    $status = '';
                    break;
                default:
                    return;
            }
            
            $this->_model->setId($id);
            $reservation = &$this->_model->getObject();
            /* @var $reservation TableReservation */
            
            if (!$reservation->email)
            	return false;
            
            $modelSupplements = new BookingModelSupplements();
            $modelReservationSupplements = new BookingModelReservationSupplements();
			$modelReservationItems = new BookingModelReservationItems();
			
        	$modelReservationSupplements->init(array());
	        $modelReservationItems->init(array('reservation_item-reservation_id'=>$reservation->id));
	        
	        $reservedItemsDb=$modelReservationItems->getData();
	        $reservedItems=array();
        	if (count($reservedItemsDb)) foreach ($reservedItemsDb as $reservedItem) {
        		
	        	$newItem = JTable::getInstance('ReservationItems','Table');
	        	$newItem->id = $reservedItem->id;
	        	$newItem->load();

	        	unset($modelReservationSupplements->_data); //add suplements
        	 	$modelReservationSupplements->_lists['reservation'] = $reservedItem->id; 
        	 	$supplements = $modelReservationSupplements->getData();

        		$newItem->supplements = &BookingHelper::loadSupplements($modelSupplements, $newItem->subject, $reservedItem->capacity, $supplements);

        		$reservedItems[] = $newItem;
	        }
	        
	        //put email subject, body and attachments thorugh plugin (to add invoice)
            $mail = array();
            $email = JModelLegacy::getInstance('Email', 'BookingModel')->getItem($config->mailingStatusClient);
            $mail['subject'] = $this->_model->replaceEmailSubject($email->subject, $reservation);
            $mail['body'] = $this->_model->replaceEmailBody($email->body, $reservation, $reservedItems, $status);
            $mail['attachments'] = null;

            $paymentStates = array('unreceive'=>0,'receiveDeposit'=>1,'receive'=>2); //translate task into new payment state
			
            JPluginHelper::importPlugin('booking');
            $dispatcher = JDispatcher::getInstance(); 
            $dispatcher->trigger('OnChangeStatusCustomerMail', array($id,isset($paymentStates[$task]) ? $paymentStates[$task] : $task ,&$mail) ); //trigger plugin (for VM Invoice)
                        if ($config->mailingStatusClient)
            	if ($reservation->email && ($email->usage == NOTIFY_EMAIL || $email->usage == NOTIFY_ALL))
            		JFactory::getMailer()->sendMail(JFactory::getApplication()->getCfg('mailfrom'), JFactory::getApplication()->getCfg('fromname'), $reservation->email, $mail['subject'], $mail['body'], $email->mode, null, null, $mail['attachments']);
            
            if ($reservation->telephone && ($email->usage == NOTIFY_SMS || $email->usage == NOTIFY_ALL))
            	ASmsService::getInstance()->sendMessage($config->smsUsername, $config->smsApikey, AUtils::getLocalPhone($reservation->telephone), $this->_model->replaceEmailBody($email->sms, $reservation, $reservedItems, $status, true), $config->smsUnicode);
            
            if (is_array($mail['attachments']))
            	JFile::delete($mail['attachments']);
            
                    }
    }

    /**
     * Open editing form page
     */
    function editing()
    {
    	//custom config for user who owns subject
    	$this->setGlobalConfigByUserFromReservation(ARequest::getCid());
        parent::editing('reservation');
    }
    
    /**
     * Add item(s) to current reservation and continue.
     */
    function add_continue()
    {
    	$this->add_checkout(false);
    }
    
    /**
     * Add item(s) to current reservation and go to checkout / reload page.
     */
    function add_checkout($toCheckout=true){
    	$config = AFactory::getConfig();
    	$mainframe =& JFactory::getApplication();
    	$post = JRequest::get('post');
    	$sessionItems = $mainframe->getUserState(OPTION.'.user_reservation_items');
    	
    	//reset reservations if can booked only one item
    	if($sessionItems && !($config->moreReservations)){
    		$sessionItems = array();
    		$mainframe->enqueueMessage(JText::_('ONE_ITEM_ONLY'));
    	}
    	
    	if (!$sessionItems)
    		$sessionItems = array();
        
        $capacity = JRequest::getInt('capacity', 1);
        $quantity = JRequest::getVar('occupancy') && !$config->cartPopup ? $capacity : 1;    	
        if ($quantity > 1) {
            $capacity = 1;
        }        
        for ($item = 0; $item < $quantity; $item++) {
            foreach ($post['boxIds'] as $id => $boxIds) {
                $quantityKey = $quantity > 1 ? $item : $id;
        
                $newItem = array();
                $newItem['boxIds']=$config->cartPopup ? $boxIds : (is_string($boxIds) ? explode(',',$boxIds) : $boxIds);
                if ($config->cartPopup)
                $newItem['supplements']=isset($post['supplements'][$id]) ? $post['supplements'][$id] : array();
                else
                    $newItem['supplements']=isset($post['supplements']) ? $post['supplements'] : array();
                $newItem['ctype']=$config->cartPopup ? $post['ctype'][$id] : $post['ctype'];
                $newItem['subject']=$post['subject'][$id];
    		$newItem['occupancy']=JArrayHelper::getValue(JRequest::getVar('occupancy', array(), 'default', 'array'), $quantityKey, array(), 'array');
            
                $newItem['item']=$quantityKey;

                if (!$config->cartPopup)
                    $newItem['capacity'] = $capacity;
                else
                $newItem['capacity']=isset($post['capacity'][$id]) ? $post['capacity'][$id] : 1;

                if ($newItem['ctype'] == CTYPE_PERIOD) {
                    $newItem['period_rtype_id']=$post['period_rtype_id'][$id];
                    $newItem['period_price_id']=$post['period_price_id'][$id];
                    $newItem['period_time_up']=$post['period_time_up'][$id];
                    $newItem['period_time_down']=$post['period_time_down'][$id];
                    $newItem['period_type']=$post['period_type'][$id];
                    $newItem['period_recurrence']=$post['period_recurrence'][$id];
                    $newItem['period_monday']=$post['period_monday'][$id];
                    $newItem['period_tuesday']=$post['period_tuesday'][$id];
                    $newItem['period_wednesday']=$post['period_wednesday'][$id];
                    $newItem['period_thursday']=$post['period_thursday'][$id];
                    $newItem['period_friday']=$post['period_friday'][$id];
                    $newItem['period_saturday']=$post['period_saturday'][$id];
                    $newItem['period_sunday']=$post['period_sunday'][$id];
                    $newItem['period_month']=$post['period_month'][$id];
                    $newItem['period_week']=$post['period_week'][$id];
                    $newItem['period_day']=$post['period_day'][$id];
                    $newItem['period_date_up']=$post['period_date_up'][$id];
                    $newItem['period_end']=$post['period_end'][$id];
                    $newItem['period_occurrences']=$post['period_occurrences'][$id];
                    $newItem['period_date_down']=$post['period_date_down'][$id];
                }

                $md5source = $newItem;
                unset($md5source['capacity']); // key must be without capacity
                $key = md5(serialize($md5source)); 
                
                $sessionItems[$key] = $newItem;

            }
        }
    	$mainframe->setUserState(OPTION.'.user_reservation_items',$sessionItems);
    	$mainframe->enqueueMessage(JText::_('ITEM_ADDED'));
    	
        //store system messages to session
        $messageQueue = $mainframe->getMessageQueue();
    	if (count($messageQueue)) {
			$session = JFactory::getSession();
			$session->set('application.queue', $messageQueue);
		}

    	//redirect parent / this page
    	echo '<script>'."\n";
    	echo 'if (window.parent)'."\n";
    	if ($toCheckout) 
    		echo "window.parent.location.href='".JRoute::_(ARoute::view(VIEW_RESERVATION,null, null, array('layout'=>'form')), false)."';\n";
    	else {
    		echo "{
    				var e = window.parent.document.bookSetting.getElementsByTagName('input'); // all reservation form inputs
    				for (var i = 0; i < e.length; i++) // process each
    					if (e[i].getAttribute('name').match(/^boxIds\[\d+\]$/)) // hidden input with name boxIds[]
    						e[i].value = ''; // reset field
    				";
    		echo "window.parent.document.bookSetting.submit();}\n";  //re-submit parent form
    	}
    	echo 'else '."\n";
    	if ($toCheckout) 
    		echo "document.location.href='".JRoute::_(ARoute::view(VIEW_RESERVATION,null, null, array('layout'=>'form')), false)."';\n";
    	else
    		echo "document.location.href='".JRoute::_(ARoute::view(VIEW_SUBJECT,$newItem['subject']), false)."';\n";
    		
    	echo '</script>';
    	
    	$mainframe->close();
    }
    
    /**
     * Remove item from current reservation (from session).
     */
    function remove_item() {
    	
    	$mainframe =& JFactory::getApplication();
    	
    	$sessionItems = $mainframe->getUserState(OPTION.'.user_reservation_items');
    	if ($sessionItems) foreach ($sessionItems as $key => $sessionItem) {
    		if (JRequest::getVar('key')==$key){
    			unset($sessionItems[$key]);
    			$mainframe->enqueueMessage(JText::_('ITEM_REMOVED'));}
    	}

    	$mainframe->setUserState(OPTION.'.user_reservation_items',$sessionItems);
    	$mainframe->redirect(ARoute::view(VIEW_RESERVATION,null, null, array('layout'=>'form')));
    }
    
    /**
     * Remove item from existing reservation (from database). Ajax action.
     */
    function remove_item_db() {
    	$ret = $this->getModel('reservationitem')->removeitem(JRequest::getString('id'), JRequest::getInt('rid'));
    	ob_clean();
    	die($ret ? '1' : '0');
    }
    
    /**
     * Save current reservation into user state.
     */
    function store()
    {
    	$post = JRequest::get('post');
    	$mainframe =& JFactory::getApplication();
		$reservedItems = array();

		//store itemns from post to session
    	foreach ($post['boxIds'] as $id => $boxIds) {
    		
    		$newItem = array();
    		$newItem['boxIds']=$boxIds;
    		$newItem['supplements']=isset($post['supplements'][$id]) ? $post['supplements'][$id] : array();
    		$newItem['ctype']=$post['ctype'][$id];
    		$newItem['subject']=$post['subject'][$id];
    		$newItem['occupancy']=$post['occupancy'][$id];
    		
    		$key = md5(serialize($newItem)); //key must be without capacity
			
    		$newItem['capacity']=isset($post['capacity'][$id]) ? $post['capacity'][$id] : 1;

    		if ($newItem['ctype'] == CTYPE_PERIOD) {
    			$newItem['period_rtype_id']=$post['period_rtype_id'][$id];
    			$newItem['period_price_id']=$post['period_price_id'][$id];
    			$newItem['period_time_up']=$post['period_time_up'][$id];
    			$newItem['period_time_down']=$post['period_time_down'][$id];
    			$newItem['period_type']=$post['period_type'][$id];
    			$newItem['period_recurrence']=$post['period_recurrence'][$id];
    			$newItem['period_monday']=$post['period_monday'][$id];
    			$newItem['period_tuesday']=$post['period_tuesday'][$id];
    			$newItem['period_wednesday']=$post['period_wednesday'][$id];
    			$newItem['period_thursday']=$post['period_thursday'][$id];
    			$newItem['period_friday']=$post['period_friday'][$id];
    			$newItem['period_saturday']=$post['period_saturday'][$id];
    			$newItem['period_sunday']=$post['period_sunday'][$id];
    			$newItem['period_month']=$post['period_month'][$id];
    			$newItem['period_week']=$post['period_week'][$id];
    			$newItem['period_day']=$post['period_day'][$id];
    			$newItem['period_date_up']=$post['period_date_up'][$id];
    			$newItem['period_end']=$post['period_end'][$id];
    			$newItem['period_occurrences']=$post['period_occurrences'][$id];
    			$newItem['period_date_down']=$post['period_date_down'][$id];
    		}
    		
    		$reservedItems[$key] = $newItem;
    	}
    	
    	$mainframe->setUserState(OPTION.'.user_reservation_items',$reservedItems);

    	//store customer info to session
    	$customer  = array();
    	$reservationTable = JTable::getInstance('Reservation','Table');
    	$params = get_object_vars($reservationTable);
    	unset($params['id'],$params['customer'],$params['state'],$params['paid'],$params['checked_out'],$params['checked_out_time']);
    	foreach ($params as $param => $val)
    		if (isset($post[$param]))
    			$customer[$param] = $post[$param];
    			
    	$mainframe->setUserState(OPTION.'.user_reservation_info',$customer);
    	$mainframe->enqueueMessage(JText::_('RESERVATION_SAVED'));
     	$mainframe->redirect(ARoute::view(VIEW_RESERVATION,null, null, array('layout'=>'form')));
    }
        
    /**
     * Empty session with current reservation.
     */
    function erase()
    {
    	$mainframe =& JFactory::getApplication();
    	$mainframe->setUserState(OPTION.'.user_reservation_items',null);
    	$mainframe->setUserState(OPTION.'.user_reservation_info',null);
    	$mainframe->enqueueMessage(JText::_('RESERVATION_ERASED'));
    	if (JFactory::getApplication()->getUserState('com_booking.object.last'))
    		$this->setRedirect(JFactory::getApplication()->getUserState('com_booking.object.last'));
    	else
    		ARequest::redirectView(VIEW_SUBJECTS);
    }
    
    /**
     * Save new or existing reservation.
     * 
     * @param boolean $apply if true after save redirect on editing reservation page, if false redirect to reservations list.
     */
    function save($apply = false)
    {
        $modelCustomer = new BookingModelCustomer();
        $modelSubject = new BookingModelSubject();
        $modelReservationItem = new BookingModelReservationItem();
        $modelReservationItems = new BookingModelReservationItems();
        $modelSupplements = new BookingModelSupplements();
        $modelReservationSupplements = new BookingModelReservationSupplements();
                
        $config = &AFactory::getConfig();

        $modelCustomer->setIdByUserId();
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
            
        	//check for errors
        	$err=false;
        	if ($modelCustomer->isCustomer() && !BookingHelper::controlCaptcha()){ //captcha invalid
        		$mainframe->enqueueMessage(JText::_('CAPTCHA_INVALID'), 'error');
        		$err=true;}
        	if ($modelCustomer->isCustomer() && $config->terms_of_contract_accept && !JRequest::getInt('accept_terms_of_contract')){
        		$mainframe->enqueueMessage(JText::_('ACCEPT_TERMS_OF_CONTRACT'), 'error');
        		$err=true;}
            if ($modelCustomer->isCustomer() && $config->terms_of_privacy_accept && !JRequest::getInt('accept_terms_of_privacy')){
        		$mainframe->enqueueMessage(JText::_('ACCEPT_TERMS_OF_PRIVACY'), 'error');
        		$err=true;}
        	
            if ($err) 
            {
                JRequest::setVar('view', VIEW_RESERVATION);
                JRequest::setVar('layout', 'form');
                JRequest::setVar('task', 'refresh');
                JRequest::setVar('error', true);
                parent::display();
                return;
            }
            
			//get items
			$subjects = array();
			$reservedItems=array();
			
			$boxIdsArray = ARequest::getArray('boxIds');
			$postSubjects = ARequest::getIntArray('subject');
			$postCtypes = ARequest::getStringArray('ctype');
			$postSupplements = ARequest::getArray('supplements', false);
			$postCapacity = ARequest::getIntArray('capacity');
            $postMoreNames = ARequest::getArray('more_names');
			$postOccupancy = ARequest::getArray('occupancy');
			$postMessages = ARequest::getArray('message');
			
			$post_period_rtype_id = ARequest::getIntArray('period_rtype_id');
			$post_period_price_id = ARequest::getIntArray('period_price_id');
			$post_period_time_up = ARequest::getStringArray('period_time_up');
			$post_period_time_down = ARequest::getStringArray('period_time_down');
			$post_period_type = ARequest::getIntArray('period_type');
			$post_period_recurrence = ARequest::getIntArray('period_recurrence');
			$post_period_monday = ARequest::getIntArray('period_monday');
			$post_period_tuesday = ARequest::getIntArray('period_tuesday');
			$post_period_wednesday = ARequest::getIntArray('period_wednesday');
			$post_period_thursday = ARequest::getIntArray('period_thursday');
			$post_period_friday = ARequest::getIntArray('period_friday');
			$post_period_saturday = ARequest::getIntArray('period_saturday');
			$post_period_sunday = ARequest::getIntArray('period_sunday');
			$post_period_month = ARequest::getIntArray('period_month');
			$post_period_week = ARequest::getIntArray('period_week');
			$post_period_day = ARequest::getIntArray('period_day');
			$post_period_date_up = ARequest::getStringArray('period_date_up');
			$post_period_end = ARequest::getIntArray('period_end');
			$post_period_occurrences = ARequest::getIntArray('period_occurrences');
			$post_period_date_down = ARequest::getStringArray('period_date_down');
			
        	$customer = &$modelCustomer->getObject();
        	/* @var $customer TableCustomer */
        	
        	        	
        	//check and prepare items firstly
			if (count($boxIdsArray)) foreach ($boxIdsArray as $key => $boxIds){
				
            	$newItem = JTable::getInstance('ReservationItems','Table');
            	/* $newItem->reservation_id = $id; */
            	$newItem->subject = $postSubjects[$key];
            	$newItem->message = empty($postMessages[$key]) ? '' : $postMessages[$key];
            	$newItem->capacity = isset($postCapacity[$key]) ? $postCapacity[$key] : 1;
                $newItem->more_names = JArrayHelper::getValue($postMoreNames, $key, array(), 'array');
            	$newItem->occupancy = JArrayHelper::getValue($postOccupancy, $key, array(), 'array');

                if (!isset($subjects[$newItem->subject])){
                	/*
	                $newSubject = JTable::getInstance('Subject','Table');
	        		$newSubject->id = $newItem->subject ;
	        		$newSubject->load();
	        		$newSubject->bind($newSubject); //bind to itself, because we need files array from params
	        		*/
	        		$modelSubject->setId($newItem->subject);
	        		$subjects[$newItem->subject] = $modelSubject->getObject();
        		}
        		
        		$subject = $subjects[$newItem->subject];
        		/* @var $subject TableSubject */

				$requestSupplements = !empty($postSupplements[$key]) ? $postSupplements[$key] : array();
                $newItem->supplements = &BookingHelper::loadSupplements($modelSupplements, $newItem->subject, $newItem->capacity, null ,$requestSupplements, count($boxIds)); //load supplements table from request

                if ($postCtypes[$key] == CTYPE_PERIOD) {
                	$newItem->period_rtype_id = $post_period_rtype_id[$key];
                	$newItem->period_price_id = $post_period_price_id[$key];
                	$newItem->period_time_up = $post_period_time_up[$key];
                	$newItem->period_time_down = $post_period_time_down[$key];
                	$newItem->period_type = $post_period_type[$key];
                	$newItem->period_recurrence = $post_period_recurrence[$key];
                	$newItem->period_monday = $post_period_monday[$key];
                	$newItem->period_tuesday = $post_period_tuesday[$key];
                	$newItem->period_wednesday = $post_period_wednesday[$key];
                	$newItem->period_thursday = $post_period_thursday[$key];
                	$newItem->period_friday = $post_period_friday[$key];
                	$newItem->period_saturday = $post_period_saturday[$key];
                	$newItem->period_sunday = $post_period_sunday[$key];
                	$newItem->period_month = $post_period_month[$key];
                	$newItem->period_week = $post_period_week[$key];
                	$newItem->period_day = $post_period_day[$key];
                	$newItem->period_date_up = $post_period_date_up[$key];
                	$newItem->period_end = $post_period_end[$key];
                	$newItem->period_occurrences = $post_period_occurrences[$key];
                	$newItem->period_date_down = $post_period_date_down[$key];
                }
                
                if (ARequest::getCid()) { // resave existing reservation
                	$newItem->id = JArrayHelper::getValue(ARequest::getArray('id'), $key);
                	$newItem->from = JArrayHelper::getValue(ARequest::getArray('from'), $key);
                	$newItem->to = JArrayHelper::getValue(ARequest::getArray('to'), $key);
                	$newItem->rtype = JArrayHelper::getValue(ARequest::getArray('rtype'), $key);
                	$newItem->price = JArrayHelper::getValue(ARequest::getArray('price'), $key);
                	$newItem->deposit = JArrayHelper::getValue(ARequest::getArray('deposit'), $key);
                	$newItem->fullPrice = JArrayHelper::getValue(ARequest::getArray('fullPrice'), $key);
                	$newItem->fullPriceSupplements = JArrayHelper::getValue(ARequest::getArray('fullPriceSupplements'), $key);
                    $newItem->provision = JArrayHelper::getValue(ARequest::getArray('provision'), $key);
                	$newItem->fullDeposit = JArrayHelper::getValue(ARequest::getArray('fullDeposit'), $key);
                	$newItem->subject_title = JArrayHelper::getValue(ARequest::getArray('subject_title'), $key);
                	$newItem->sub_subject = JArrayHelper::getValue(ARequest::getArray('sub_subject'), $key);
                	$newItem->tax = JArrayHelper::getValue(ARequest::getArray('tax'), $key);
                	$newItem->occupancy = JArrayHelper::getValue(ARequest::getArray('occupancy'), $key);
                } else {	
                	//reserve from interval, in ID is subject ID
                	$box = &BookingHelper::getReservedInterval($subject, $postCtypes[$key], $boxIds, $newItem->supplements, $newItem->capacity, $newItem, $newItem->occupancy);
		                
	            	if (is_object($box)) {
	            		$newItem->id = null;
	                	$newItem->from = $box->from;
	                	$newItem->to = $box->to;
	                	$newItem->rtype = $box->rtype;
	                	$newItem->price = $box->price;
	                
	                		                		$onlinePaymentExpirationTime = $config->onlinePaymentExpirationTime;
	                
	                	//if expiration for online payment, get confgiruation in minutes and save in seconds
	                	$newItem->cancel_time = empty($box->cancel_time) ? $onlinePaymentExpirationTime * 60 : $box->cancel_time; 
	                	$newItem->deposit = $box->deposit;
	                	$newItem->fullPrice = $box->fullPrice;
	                	$newItem->fullPriceSupplements = $box->fullPriceSupplements;
                        $newItem->provision = $box->provision;
	                	$newItem->fullDeposit = $box->fullDeposit;
	                	$newItem->subject_title = $subject->title;
	                	$newItem->sub_subject = JArrayHelper::getValue(ARequest::getArray('sub_subject'), $key);
	                	$newItem->tax = $subject->tax;
	                	$newItem->occupancy = $box->occupancy;
	                	$availableCapacity = $subject->total_capacity-$box->maxReserved;
	                	if (!$box->canReserve OR $availableCapacity<$newItem->capacity) {
	                		$mainframe->redirect(ARoute::view(VIEW_SUBJECT, $subject->id, $subject->alias), 'Subject cannot reserved', 'error');
	                	}
	            	}
                }
                		
                $reservedItems[] = $newItem;
			}
			
			$checkLimit = $modelReservationItems->canReserveInLimit($customer, $subjects, $reservedItems);
			
	       	if ($checkLimit!==true)
	        	$mainframe->redirect(ARoute::view(VIEW_SUBJECT, $checkLimit->id, $checkLimit->alias), JText::sprintf('CANNOT_RESERVE_IN_LIMIT_S_FOR_S_DAYS', $checkLimit->rlimit_count, $checkLimit->rlimit_days, $checkLimit->title), 'error');
			     
			//store reservation
            $post = &JRequest::get('post');
            if ($modelCustomer->isCustomer())
            	$post['id'] = 0; //customer  only adding
            else 
            	$post['id'] = ARequest::getCid();
            if ($modelCustomer->isCustomer())
        		$post['customer'] = $modelCustomer->getId() ? $modelCustomer->getId() : 0; //anynomyous = 0
        	
        	//if every reservation must be confirmed by admin, state is pre-reserved
        	//if deposit must be paid immediately (online payment) set resevation as storned
        	//Reservation will be activated after confirmation of payment - ?controller=reservation&paid=receive&task=payment&type=paypal
        	
            if (!$modelCustomer->isAdmin()) {
	       		if ($config->confirmReservation)
	       			$post['state'] = RESERVATION_PRERESERVED; // manager should confirm reservation
	       		else if ($box->cancel_time === '0')
	       			$post['state'] = RESERVATION_STORNED; 
	       		else
		    		$post['state'] = RESERVATION_ACTIVE;
			    $post['paid'] = RESERVATION_PENDING; // reservation unpaid in default
            }      
            $id = $this->_model->store($post);

            $user = JFactory::getUser();
            
            if ($id) {
            	
            	if ($user->guest || (!$modelCustomer->getId() && !JRequest::getInt('customer'))) { // guest or non customer - create new account
            		$customer = JTable::getInstance('customer', 'table');
            		/* @var $customer TableCustomer */
            		$customer->bind($post);
					$customer->id = null;
					$customer->user = $modelCustomer->isAdmin() ? 0 : $user->get('id'); // do not assign new customer account witl logged administrator - when admin creates reservations
					$customer->state = CUSTOMER_STATE_ACTIVE;
					$customer->store();
					if (!$user->guest)
						$this->_model->_table->updateCustomer($id, $customer->id);
            	}
            	
            	//var_dump($reservedItems);
            	//store items & get files to send customer
            	$filesSend = null;
            	foreach ($reservedItems as &$reservedItem){
            		$reservedItem->reservation_id = $id;
            		
                	if (!($reservedItem->id = $modelReservationItem->store((array)$reservedItem)))
                		$mainframe->enqueueMessage(JText::_('SUBJECT_NOT_STORED').': <br>'.nl2br(print_r($reservedItem,true)),'warning');
                	elseif ($config->mailingReservationClient) {
                		
                		$files = BookingHelper::getSubjectFiles($subjects[$reservedItem->subject],array('onlySend'=>true,'onlyFilepaths'=>true));
                		$filesSend = array_unique(array_merge((array)$filesSend,$files));
                	}
            	}

            	$mainframe->setUserState(OPTION.'.user_reservation_items',null);
    			$mainframe->setUserState(OPTION.'.user_reservation_info',null);
    	
                if ($config->mailingReservationClient || $config->mailingReservationManager) {
                    $reservation = &$this->_model->_table;
                    /* @var $reservation TableReservation */
                    
                    JPluginHelper::importPlugin('booking');
                    $dispatcher = JDispatcher::getInstance();
                    
                    //put customer email through plugin
                    $mail = array();
                    $email = JModelLegacy::getInstance('Email', 'BookingModel')->getItem($config->mailingReservationClient);
                    $mail['subject'] = $this->_model->replaceEmailSubject($email->subject, $reservation);
                    $mail['body'] = $this->_model->replaceEmailBody($email->body, $reservation, $reservedItems);
                    $mail['attachments'] = $filesSend;
					$dispatcher->trigger('OnReservationCustomerMail', array($id,$reservation->paid,&$mail) ); 
                    
                    if ($config->mailingReservationClient && ($modelCustomer->isCustomer() || JRequest::getInt('notify_customer'))) {
                    	if ($reservation->email && ($email->usage == NOTIFY_EMAIL || $email->usage == NOTIFY_ALL))
                    	JFactory::getMailer()->sendMail(JFactory::getApplication()->getCfg('mailfrom'), JFactory::getApplication()->getCfg('fromname'), $reservation->email, $mail['subject'], $mail['body'], $email->mode, null, null, $mail['attachments']);
                    
                    	if ($reservation->telephone && ($email->usage == NOTIFY_SMS || $email->usage == NOTIFY_ALL))
                    		ASmsService::getInstance()->sendMessage($config->smsUsername, $config->smsApikey, AUtils::getLocalPhone($reservation->telephone), $this->_model->replaceEmailBody($email->sms, $reservation, $reservedItems, '', true), $config->smsUnicode);
                    }
                    
                    //put administrator email through plugin
                    $mail = array();
                    $email = JModelLegacy::getInstance('Email', 'BookingModel')->getItem($config->mailingReservationManager);
                    $mail['subject'] = $this->_model->replaceEmailSubject($email->subject, $reservation);
                    $mail['body'] = $this->_model->replaceEmailBody($email->body, $reservation, $reservedItems);
                    $mail['attachments'] = null;
					$dispatcher->trigger('OnReservationAdministratorMail', array($id,$reservation->paid,&$mail) ); 
                        
                    if ($config->mailingReservationManager && $modelCustomer->isCustomer()) { // notify managers after customer reservation
                    	$managers = AUser::getNotificationManagers('booking.mailing.new.reservation', $reservedItems);
                    		
                   		if (!empty($managers) && ($email->usage == NOTIFY_EMAIL || $email->usage == NOTIFY_ALL))
                    		JFactory::getMailer()->sendMail(JFactory::getApplication()->getCfg('mailfrom'), JFactory::getApplication()->getCfg('fromname'), $managers, $mail['subject'], $mail['body'], $email->mode, null, null, $mail['attachments']);
                   		
                   		if ($config->mailingManagerPhone && ($email->usage == NOTIFY_SMS || $email->usage == NOTIFY_ALL))
                   			ASmsService::getInstance()->sendMessage($config->smsUsername, $config->smsApikey, AUtils::getLocalPhone($config->mailingManagerPhone), $this->_model->replaceEmailBody($email->sms, $reservation, $reservedItems, '', true), $config->smsUnicode);
                    }
                    
                    if ($config->mailingSupplier && $config->mailingReservationSupplier && $modelCustomer->isCustomer()) { // notify supplier after customer reservation
                    	$email = JModelLegacy::getInstance('Email', 'BookingModel')->getItem($config->mailingReservationSupplier);
                    	if ($email->usage == NOTIFY_EMAIL || $email->usage == NOTIFY_ALL)
                    		JFactory::getMailer()->sendMail(JFactory::getApplication()->getCfg('mailfrom'), JFactory::getApplication()->getCfg('fromname'), $config->mailingSupplier, $this->_model->replaceEmailSubject($email->subject, $reservation), $this->_model->replaceEmailBody($email->body, $reservation, $reservedItems), $email->mode);                    	 
                    }
                 }
                 
                 //clear cache after saving reservation
                 try{
                 	ACache::cleanAll();
                 }catch(Exception $e){ ALog::addException($e); }
                 
                 $mainframe->setUserState(('com_booking.pay' . $id), true); // prompt to online payment                
                 
                 if ($modelCustomer->isAdmin()) {
                 	if ($apply)
                 		ARequest::redirectEdit($this->_controllerName, $id);
                 	else
                 		ARequest::redirectList($this->_controllerName);
                 } elseif ($config->redirectionAfterReservation == REDIRECTION_AFTER_RESERVATION_THANKYOU_PAGE)
                		$mainframe->redirect($this->getReservationRoute($id), JText::_('THANK_RESERVATION'), 'message');
                 elseif ($config->redirectionAfterReservation != REDIRECTION_AFTER_RESERVATION_THANKYOU_PAGE)
                 	$mainframe->redirect($this->getReservationRoute($id), JText::sprintf('ACCEPT_RESERVATION', $id), 'message');
            
            } else {
            	
            	JError::raiseWarning(JText::_('RESERVATION_NOT_STORED'),'error');

            	JRequest::setVar('view','reservation');
    			JRequest::setVar('layout','form');
    			parent::display();
            }
    }

    /**
     * Get URL to display reservation on frontend.
     * 
     * @param int $reservationId
     * @param string $sessionIdMd5
     * @return string
     */
    function getReservationRoute($reservationId, $sessionIdMd5 = null)
    {
	  	return  ARoute::redirectionAfterReservation(true, $reservationId, $sessionIdMd5);
    }

    /**
     * Cancel edit operation. Check in reservation and redirect to reservations list. 
     */
    function cancel()
    {
        parent::cancel('RESERVATION_EDITING_CANCELED');
    }
    
    /**
     * Serve reservation total price to the AJAX call.
     */
    function gettotal()
    {
    	$modelSupplements = JModelLegacy::getInstance('Supplements', 'BookingModel');
    	/* @var $modelSupplements BookingModelSupplements */
    	$newItem = JTable::getInstance('ReservationItems','Table');
    	/* @var $newItem TableReservationItems */
    	$subject = JTable::getInstance('Subject', 'Table');
    	/* @var $subject TableSubject */
    	
    	$postSubjects = ARequest::getIntArray('subject'); // list of reservation item
    	$boxIdsArray = ARequest::getArray('boxIds'); // selected reservation interval
    	$requestSupplements = ARequest::getArray('supplements'); // list of supplement
    	$ctype = JRequest::getString('ctype'); // calendar type
    	$occupancy = ARequest::getArray('occupancy'); // occupancy
    	$capacity = JRequest::getInt('capacity', 1);
    	
    	$response = array('total' => '', 'error' => '', 'status' => 'OK');
    	$fullPriceSupplements = 0;
    	$fullDeposit = 0;
    	
    	if ($capacity > 1 && count($occupancy)) {
    		$quantity = $capacity;
    		$capacity = 1;
    	} else
    		$quantity = 1;
    	
    	for ($item = 0; $item < $quantity; $item ++)
    		foreach ($boxIdsArray as $key => $boxIds) {
    			$boxIds = explode(',', $boxIds);
			    		
    			$subject->load($postSubjects[$key]);
    			$supplements = BookingHelper::loadSupplements($modelSupplements, $subject->id, $capacity, null, $requestSupplements, count($boxIds));
    			$box = BookingHelper::getReservedInterval($subject, $ctype, $boxIds, $supplements, $capacity, $newItem, JArrayHelper::getValue($occupancy, $item, array(), 'array'));    	
    			if (!$box->canReserve) {
    				$response['status'] = 'FAIL';
    				$response['error'] = $box->error;
    			}
    			$fullPriceSupplements += $box->fullPriceSupplements;
    			$fullDeposit += $box->fullDeposit; 
    		}
    	
    	if ($response['status'] == 'OK' && !empty($fullPriceSupplements)) {
    		$response['total'] = JText::_('TOTAL_PRICE') . ': ' . BookingHelper::displayPrice($fullPriceSupplements, null, $subject->tax); // get total price formated with label
    		if ($fullDeposit)
    			$response['total'] .= ', ' . JText::_('Deposit') . ': ' . BookingHelper::displayPrice($fullDeposit, null, $subject->tax);
    	}
    	
    	ob_clean(); // prevent for some errors in string response
    	die(json_encode($response));
    }
        /**
     * Export reservation list into CSV file and serve to user
     */
    function export()
    {
    	JRequest::setVar('view', 'reservations');
    	JRequest::setVar('layout', JRequest::getString('type', 'csv'));
    	parent::display();
    }
    
    /**
     * Get list of available items to change in reservation
     */
    public function getChangeableItems() {
        $id = JRequest::getInt('id');
        $user = JFactory::getUser();
        
        if (!$user->authorise('booking.reservation.edit.item', 'com_booking')) {
            $response['code'] = 0;
            $response['html'] = JText::_('YOU_ARE_NOT_ALLOWED_TO_CHANGE_ITEM');
        } else {
            $items = $this->_model->getChangeableItems($id);
            $response = array();
            if ($items) {
                $response['code'] = 1;
                array_unshift($items, array('value' => 0, 'text' => ('&ndash; ' . JText::_('SELECT_ITEM') . ' &ndash;')));
                $response['html'] = JHtml::_('select.genericlist', $items, ('subject[' . $id . ']'), 'onchange="ViewReservation.selectChangeItem(' . $id . ')"');
            } else {
                $response['code'] = 0;
                $response['html'] = JText::_('NOT_FOUND_ANY_ITEM_TO_CHANGE');
            }
        }
        die(json_encode($response));
    }

    /**
     * Do changing reservation item.
     */
    public function changeItem() {
        $id = JRequest::getInt('changed_reservation_item_id');
        $data = JRequest::getString('data');
        $user = JFactory::getUser();
        
        if (!$user->authorise('booking.reservation.edit.item', 'com_booking')) {
            $response['code'] = 0;
            $response['html'] = JText::_('YOU_ARE_NOT_ALLOWED_TO_CHANGE_ITEM');
        } else {
            if ($rid = $this->_model->changeItem($id, $data)) {
                $response['code'] = 1;
                $response['html'] = JText::_('ITEM_SUCCESSFULLY_CHANGED');
                $this->changeStatusInfo($rid, 'change');
                $app = JFactory::getApplication();
                $app->enqueueMessage(JText::_('ITEM_SUCCESSFULLY_CHANGED'));
                JFactory::getSession()->set('application.queue', $app->getMessageQueue());
            } else {
                $response['code'] = 0;
                $response['html'] = JText::_('UNABLE_TO_CHANGE_ITEM');
            }
        }
        
        die(json_encode($response));
    }
}

?>
