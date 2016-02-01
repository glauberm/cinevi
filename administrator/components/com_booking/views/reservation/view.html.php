<?php

/**
 * Edit reservation.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

//import needed Joomla! libraries
jimport('joomla.application.component.view');

//import needed models
AImporter::model('customer', 'reservation', 'reservationitems', 'reservationitem', 'reservations', 'reservationsupplements', 'subject', 'supplements');
//import needed JoomLIB helpers
AImporter::helper('config', 'route', 'booking', 'parameter', 'request','event');
//import needed element
AImporter::element('customer', 'subject');
//import needed assets
AImporter::joomlaJS();
AImporter::js('validator', 'view-reservation-submitbutton');
//import custom icons
AHtml::importIcons();
//import needed objects
AImporter::object('box', 'date', 'day', 'interval', 'service');
//import needed tables
AImporter::table('price');

class BookingViewReservation extends JViewLegacy
{

    /**
     * Prepare to display page.
     * 
     * @param string $tpl name of used template
     */
    function display($tpl = null)
    {
    	
		//define HTML tooltip labels
		define ('ITEM_PRICE_TIP','<span class="hasTip" title="'.JText::_('ITEM_PRICE').'::'.JText::_('ITEM_PRICE_INFO').'">'.JText::_('ITEM_PRICE').'</span>');
		define ('ITEM_DEPOSIT_TIP','<span class="hasTip" title="'.JText::_('ITEM_DEPOSIT').'::'.JText::_('ITEM_DEPOSIT_INFO').'">'.JText::_('ITEM_DEPOSIT').'</span>');
		define ('FULL_PRICE_TIP','<span class="hasTip" title="'.JText::_('FULL_PRICE').'::'.JText::_('FULL_PRICE_INFO').'">'.JText::_('FULL_PRICE').'</span>');
		define ('FULL_DEPOSIT_TIP','<span class="hasTip" title="'.JText::_('FULL_DEPOSIT').'::'.JText::_('FULL_DEPOSIT_INFO').'">'.JText::_('FULL_DEPOSIT').'</span>');
		define ('FULL_PRICE_SUPPLEMENTS_TIP','<span class="hasTip" title="'.JText::_('FULL_PRICE_SUPPLEMENTS').'::'.JText::_('FULL_PRICE_SUPPLEMENTS_INFO').'">'.JText::_('FULL_PRICE_SUPPLEMENTS').'</span>');

        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        $user = &JFactory::getUser();
        /* @var $user JUser */
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        $config = &AFactory::getConfig();
        
        $document->setTitle('Reservation');
        
        $modelReservation = new BookingModelReservation();
        $modelReservationItems = new BookingModelReservationItems();
       	$modelReservationItem = new BookingModelReservationItem();
        $modelCustomer = new BookingModelCustomer();
        $modelSubject = new BookingModelSubject();
                $modelSupplements = new BookingModelSupplements();
        $modelReservationSupplements = new BookingModelReservationSupplements();
        
        $isAdmin = $modelCustomer->isAdmin();
        
        $id = JRequest::getInt('id'); //id of subject
        $cid = ARequest::getCid(); //id of reservation

        //edit operation if layout is set to form
        $edit = $this->getLayout() == 'form';
        //get task operation
        $add = JRequest::getCmd('task')=='add';
        // update item list
        $this->ajaxForItems = JRequest::getInt('ajaxForItems');
        
        if ($add && IS_ADMIN) { // share frontend layout in administrator
        	$this->addTemplatePath(JPATH_COMPONENT_SITE.'/views/reservation/tmpl');
        	$this->setLayout('form_add');
        }
        	
        $refresh = JRequest::getCmd('task')=='refresh';
        //in previous relation detect error status
        $error = JRequest::getInt('error');
        //data from prevoius relation on detect error
        $data = JRequest::get('post');

        $reservedItems=array();
        $subjects = array();
        $expires = array();
        
        //if is on admin or is administrator on site take reservation ID from array cid 
        if (/*(IS_ADMIN || (IS_SITE && $isAdmin)) && */$cid>0) //get saved reservation
        	$modelReservation->setId($cid);
        
        $reservation = &$modelReservation->getObject();
        /* @var $reservation TableReservation */
        
        if (IS_ADMIN) {
            //take customer from reservation  
            $modelCustomer->setId($reservation->customer);
        } elseif (IS_SITE) {
            //take customer from logged user informations
            $modelCustomer->setIdByUserId();
        }
        //load from DB by ID
        $customer = &$modelCustomer->getObject();
		/* @var $customer TableCustomer */
        
        //get array of reserved items as TableReservationItems, their supplements and reserved subjects
        //(IS_ADMIN || (IS_SITE && $isAdmin)) && 
        if ($cid>0) { //admin or site - from db
	        
        	$modelReservationSupplements->init(array());
	        $modelReservationItems->init(array('reservation_item-reservation_id'=>$cid));
	        $reservedItemsDb=$modelReservationItems->getData();
	        
	        if (count($reservedItemsDb)) foreach ($reservedItemsDb as $reservedItem) {
	        	
	        	if ($isAdmin && !$user->authorise('booking.reservations.manage', 'com_booking.subject.'.$reservedItem->subject)) // check is manager can manage reservation
	        		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	        	
	        	$newItem = JTable::getInstance('ReservationItems','Table');
	        	$newItem->id = $reservedItem->id;
	        	$newItem->load();
	        	
	        	if (!isset($subjects[$reservedItem->subject])){ //create subject item
	        		$newSubject = JTable::getInstance('Subject','Table');
	        		$newSubject->id = $reservedItem->subject;
	        		$newSubject->load();
	        		$subjects[$reservedItem->subject] = $newSubject;
	        	}
	        	
	        	unset($modelReservationSupplements->_data); //add suplements
        	 	$modelReservationSupplements->_lists['reservation'] = $reservedItem->id; 
        		$newItem->supplements = &$modelReservationSupplements->getData();
        		$newItem->supplementsRaw = JModelLegacy::getInstance('Supplements', 'BookingModel')->init(array('subject' => $newItem->subject))->getData();
        		$reservedItems[] = $newItem;
	        }
        }
		elseif (IS_ADMIN || (IS_SITE && $edit && ($user->authorise('booking.reservation.create', 'com_booking') || $isAdmin || ($user->guest && !$config->loginBeforeReserving)))) {  //from site

			if (!$add && !$refresh) {
				//get reserved items from session (if any) - but only if editing
				$sessionItems = $mainframe->getUserState(OPTION.'.user_reservation_items');
	
				if (count($sessionItems)) foreach ($sessionItems as $sessionItem){
					
					$newItem = JTable::getInstance('ReservationItems','Table');
	
					foreach ($sessionItem as $key => $val)
						$newItem->$key = $val;
						
					unset($sessionItem['capacity']);
					$key = md5(serialize($sessionItem)); //key for removal
					$newItem->key = $key;
	
					if (!isset($subjects[$newItem->subject])){ //create subject item
		        		$newSubject = JTable::getInstance('Subject','Table');
		        		$newSubject->id = $newItem->subject;
		        		$newSubject->load();
		        		$subjects[$newItem->subject] = $newSubject;
		        	}
					
		        	$newItem->supplements = &BookingHelper::loadSupplements($modelSupplements, $newItem->subject, $newItem->capacity, null, $newItem->supplements, count($newItem->boxIds)); //load supplements table with supplements from session
		        	$newItem->supplementsRaw = JModelLegacy::getInstance('Supplements', 'BookingModel')->init(array('subject' => $newItem->subject))->getData();
                    try {
                        $newItem->box = &BookingHelper::getReservedInterval($subjects[$newItem->subject], $newItem->ctype, $newItem->boxIds, $newItem->supplements, $newItem->capacity, $newItem, $newItem->occupancy);
                    } catch(Exception $e) {
                        $mainframe->enqueueMessage($e->getMessage());
                        return;
                    }
		            $newItem->from = $newItem->box->from;
		            $newItem->to = $newItem->box->to;
		            $newItem->rtype = $newItem->box->rtype;
					$newItem->price = $newItem->box->price;
					$newItem->cancel_time = $newItem->box->cancel_time;
	                $newItem->deposit = $newItem->box->deposit;
	                $newItem->fullPrice = $newItem->box->fullPrice;
	                $newItem->fullPriceSupplements = $newItem->box->fullPriceSupplements;
                    $newItem->provision = $newItem->box->provision;
	                $newItem->fullDeposit = $newItem->box->fullDeposit;
	                $newItem->tax = $subjects[$newItem->subject]->tax;
	                $newItem->subject_title = $subjects[$newItem->subject]->title;
	                $newItem->occupancy = $newItem->box->occupancy;
					
					$reservedItems[$key] = $newItem;
					
					//+ from post? when changng quantity, dont save to session yet
				}
			}
			
			//append items from POST
			$boxIdsArray=array();
			
			if ($refresh) { //from this form (refreshing) - can be multiple
				
				$boxIdsArray = ARequest::getArray('boxIds');
				$postSubjects = ARequest::getArray('subject');
				$postCtypes = ARequest::getArray('ctype');
				$postSupplements = ARequest::getArray('supplements');
				$postCapacity = ARequest::getArray('capacity');
				$occupancy = ARequest::getArray('occupancy');
				$quantity = 1;
			}
			
			if ($add){ //from calendar (adding - in iframe)

				$capacity = JRequest::getInt('capacity', 1);
				$occupancy = ARequest::getArray('occupancy');
				
				if ($capacity > 1 && $occupancy) {
					$quantity = $capacity;
					$capacity = 1;
				} else
					$quantity = 1;
				
				foreach (ARequest::getArray('boxIds') as $boxIds) {
					$boxIdsArray[] = explode(',', $boxIds);
					$postCapacity[] = $capacity;
				}
				$postSubjects = ARequest::getArray('subject');
				$ctype = JRequest::getString('ctype');
				$requestSupplements = ARequest::getArray('supplements');
			}
			
			// pre-fill the reservation from the logged user
			$prefill = $config->prefillReservation;
			if ($isAdmin && $config->prefillReservation != 2)
			    $prefill = false;
			if ($prefill)
			    $reservation->email = $user->get('email');
			
			if (count($boxIdsArray)){
				for ($item = 0; $item < $quantity; $item ++)
					foreach ($boxIdsArray as $key => $boxIds) {
		            	$newItem = JTable::getInstance('ReservationItems','Table');
		            	$newItem->subject = $postSubjects[$key];
		                $ctype = $add ? $ctype : $postCtypes[$key];
		                $requestSupplements = $add ? $requestSupplements : (!empty($postSupplements[$key]) ? $postSupplements[$key] : array());
	
		                if (!isset($subjects[$newItem->subject])){
			                $newSubject = JTable::getInstance('Subject','Table');
			        		$newSubject->id = $newItem->subject;
			        		$newSubject->load();
			        		$subjects[$newItem->subject] = $newSubject;
		        		}
		        		
		        		$newSubject = $subjects[$newItem->subject];
		        		/* @var $newSubject TableSubject */
	
		        		$capacity = (($refresh || $add) && isset($postCapacity[$key])) ? $postCapacity[$key] : 1;
		        		
		                $supplements = &BookingHelper::loadSupplements($modelSupplements, $newItem->subject, $capacity, null, $requestSupplements, count($boxIds)); //load supplements table from request
		                
		                if ($ctype == CTYPE_PERIOD) {
		                	$newItem->period_rtype_id = JRequest::getInt('period_rtype_id');
		                	$newItem->period_price_id = JRequest::getInt('period_price_id');
		                	$newItem->period_time_up = JRequest::getString('period_time_up');
	        				$newItem->period_time_down = JRequest::getString('period_time_down');
		                	$newItem->period_type = JRequest::getInt('period_type');
		                	$newItem->period_recurrence = JRequest::getInt('period_recurrence');
		                	$newItem->period_monday = JRequest::getInt('period_monday');
		                	$newItem->period_tuesday = JRequest::getInt('period_tuesday');
		                	$newItem->period_wednesday = JRequest::getInt('period_wednesday');
		                	$newItem->period_thursday = JRequest::getInt('period_thursday');
		                	$newItem->period_friday = JRequest::getInt('period_friday');
		                	$newItem->period_saturday = JRequest::getInt('period_saturday');
		                	$newItem->period_sunday = JRequest::getInt('period_sunday');
		                	$newItem->period_month = JRequest::getInt('period_month');
		                	$newItem->period_week = JRequest::getInt('period_week');
		                	$newItem->period_day = JRequest::getInt('period_day');
		                	$newItem->period_date_up = JRequest::getString('period_date_up');
		                	$newItem->period_end = JRequest::getInt('period_end');
		                	$newItem->period_occurrences = JRequest::getInt('period_occurrences');
		                	$newItem->period_date_down = JRequest::getString('period_date_down');
		                }
	
		                //reserve from interval, in ID is subject ID
                        try {
                            $newItem->box = &BookingHelper::getReservedInterval($newSubject, $ctype, $boxIds, $supplements, $capacity, $newItem,  JArrayHelper::getValue($occupancy, $item, array(), 'array'));
                        } catch(Exception $e) {
                            $mainframe->enqueueMessage($e->getMessage());
                            return;
                        }
		                $newItem->from = $newItem->box->from;
		                $newItem->to = $newItem->box->to;
		                $newItem->rtype = $newItem->box->rtype;
						$newItem->price = $newItem->box->price;
						$newItem->cancel_time = $newItem->box->cancel_time;
		                $newItem->deposit = $newItem->box->deposit;
		                $newItem->fullPrice = $newItem->box->fullPrice;
		                $newItem->fullPriceSupplements = $newItem->box->fullPriceSupplements;
                        $newItem->provision = $newItem->box->provision;
		                $newItem->fullDeposit = $newItem->box->fullDeposit;
		                $newItem->capacity = $capacity;
		                $newItem->boxIds = $boxIds;
		                $newItem->ctype = $ctype;
		                $newItem->supplements = $supplements;
		                $newItem->supplementsRaw = JModelLegacy::getInstance('Supplements', 'BookingModel')->init(array('subject' => $newItem->subject))->getData();
		                $newItem->tax = $newSubject->tax;
		                $newItem->subject_title = $subjects[$newItem->subject]->title;
		                $newItem->occupancy = $newItem->box->occupancy;
		                //TODO try to do effectively
		                //take prices usable for this subject
		                /*
		                $modelPrices = new BookingModelPrices();
		                $modelPrices->init(array('subject' => $newItem->subject));
		                $prices = &$modelPrices->getData();
		                var_dump($prices);
		                $newItem->cancel_time = $newSubject->cancel_time;
		                */
		                
		                $itemKey = array();
		                $itemKey['boxIds'] = $boxIds;
		                $itemKey['supplements'] = $requestSupplements;
		                $itemKey['ctype'] = $ctype;
		                $itemKey['subject'] = $newItem->subject;
		                $itemKey['item'] = $item;
	    				$mkey = md5(serialize($itemKey)); //key must be without capacity		 
	    				
	    				if (isset($reservedItems[$mkey])) //already in session - can be deleted
	    					$newItem->key = $mkey;
	    				
		                //error if cannot reserved
		                $availableCapacity = $newSubject->total_capacity - $newItem->box->maxReserved;	                
		                if (!$newItem->box->canReserve OR $availableCapacity<$newItem->capacity)
		                    $mainframe->redirect(ARoute::view(VIEW_SUBJECT, $newSubject->id, $newSubject->alias), $newItem->box->error, 'error');
	
		                if ($add && isset($reservedItems[$mkey]))
		                	$mainframe->enqueueMessage('This exact item configuration was already in your reservation list. It was replaced by new one.');
		                
		                $reservedItems[$mkey] = $newItem;
					}
			
				$checkLimit = $modelReservationItems->canReserveInLimit($customer, $subjects, $reservedItems);
			
	        	if ($checkLimit!==true)
	        		$mainframe->redirect(ARoute::view(VIEW_SUBJECT, $checkLimit->id, $checkLimit->alias), JText::sprintf('CANNOT_RESERVE_IN_LIMIT_S_FOR_S_DAYS', $checkLimit->rlimit_count, $checkLimit->rlimit_days, $checkLimit->title), 'error');
			}
			
			//delete reservations with bigger capacity than maximum
			//init for finding collision in reservations
			$reservationItemCapacity = array();
			$allItems = $reservedItems;
			
			//go to all items and find other with same subject id and in same interval
			foreach($allItems as $ik=>$reservedItem)
			{
				//this is already checkend, so can be unsetted
				unset($allItems[$ik]);
				$subjectId = $reservedItem->subject;
				$from = $reservedItem->box->fromUts;
				$to = $reservedItem->box->toUts;
				$reservationItemCapacity[$ik] = 0;
				//get capacity
				foreach($allItems as $ikk=>$item)
				{
					if($item->subject == $subjectId && $item->box->fromUts < $to && $item->box->toUts > $from)
					{
						//var_dump($item->capacity);
						$reservationItemCapacity[$ik] = $reservationItemCapacity[$ik] + $item->capacity;
					}
				}
			}
			//var_dump($reservationItemCapacity);
			foreach($reservationItemCapacity as $k=>$cap)
			{
				//get total capacity of other same subject($cap)
				if($cap && array_key_exists($k,$reservedItems))
				{
					//get reservation
					$reservedItem = $reservedItems[$k];
					$subjectId = $reservedItem->subject;
					$from = $reservedItem->box->from;
					$to = $reservedItem->box->to;
					$modelReservationItems = new BookingModelReservationItems();
					//get reservations for interval and subject
					$reservations = $modelReservationItems->getSimpleData($subjectId, $from, $to);
					if (empty($reservations))
						continue;                   
					$totalCapacity = reset($reservations)->total_capacity;
					$capacity = $reservedItem->capacity + $cap;
					if($reservations)
					{
						foreach($reservations as $res)
						{
							$capacity += $res->capacity;
						}
					}                    
					//capacity from reservations and session is bigger than max capacity
					if($capacity > $totalCapacity)
					{
						$mainframe->enqueueMessage(JText::sprintf('OVER_QUANTITY', $reservedItems[$k]->subject_title), 'notice');
						
						//unset from form and session
						unset($sessionItems[$k],$reservedItems[$k]);
						$mainframe->setUserState(OPTION.'.user_reservation_items',$sessionItems);
						 
					}
				}
			}
		}

        //editing on site
        if (IS_SITE && $edit) {
            
            if ($isAdmin || ($reservation->id && $user->authorise('booking.reservations.manage', 'com_booking'))) {
                //user is admin, get special layout for admins and use reservation to editing loaded before
                $this->setLayout('form_admin');
            } elseif (JFactory::getUser()->authorise('booking.reservation.create', 'com_booking') || ($user->guest && !$config->loginBeforeReserving)) {
                $this->setLayout('form_customer');
                
                //bind data to reservation object
                if ($refresh) //direct refresh - from post
                	$source = $data;
                else {
                	$userState = $mainframe->getUserState(OPTION.'.user_reservation_info');
                	$source = !empty($userState) ? $userState : $customer; //from previous user state, if not stored from customer default
                }
            }
            
            if ($add)
            	$this->setLayout('form_add');
        }
        
        //if in previous detect error or refresh bind reservation with data from previous relation
        if ($error)
        	$source = $data;
        
        // pre-fill the reservation from the logged user
        if (isset($source) && $prefill)
        	$reservation->bind($source);
        	
        $reservation->clean();
        
        //make objects html safe
        if ($edit) {
            JFilterOutput::objectHTMLSafe($reservation);
            foreach ($reservedItems as $reservedItem)
            	JFilterOutput::objectHTMLSafe($reservedItem, ENT_QUOTES, 'occupancy');
            JFilterOutput::objectHTMLSafe($customer);
            JFilterOutput::objectHTMLSafe($user);
            array_map(array('JFilterOutput','objectHTMLSafe'),$subjects);
        }
        
        $params = &JComponentHelper::getParams(OPTION);
        /* @var $params JParameter */

        //controll if operation is hack 
        $hack=false;
        if (IS_SITE && !$isAdmin && !$edit && ($reservation->customer!=$customer->id || (!$customer->id && JRequest::getString('session') != md5(session_id())))) //displaying reservation
        	$hack=true;
        if (IS_SITE && !$isAdmin && $edit && !$user->authorise('booking.reservation.create', 'com_booking') && !($user->guest && !$config->loginBeforeReserving)) //editing/ading reservation
        	$hack=true;
        	
        if ($hack)
        	$mainframe->redirect('index.php'); //on hack go to homepage

        list($fullPrice,$fullDeposit,$fullProvision) = BookingHelper::countOverallPrice(null,$reservedItems);
    
        $reservation->subject_title = BookingHelper::getReservationName($reservedItems); //simulate subject name for payment methods
        //$reservation->price = $fullPrice;
        $reservation->fullPrice = $fullPrice;
        //$reservation->deposit = $fullDeposit; //for payment methods and backward comp.
        $reservation->fullDeposit = $fullDeposit;
        $reservation->fullProvision = $fullProvision;
        
        //finding item which must be paid immediately
        $onlyOnlinePayment = false;
        $expiremessage = 0;
        
        //select first usable expire time
        foreach($reservedItems as $reservedItem)
        {
        	if($reservedItem->cancel_time > 0)
        	{
	        	$expiremessage = BookingHelper::formatExpiration($reservedItem->cancel_time, $reservation->book_time, true);
	        	break;
        	}
        	else if($reservedItem->cancel_time < 0)
        	{
	        	$expiremessage = BookingHelper::formatExpiration($reservedItem->cancel_time, $reservedItem->from);
	        	break;
        	}
        }
        
        $date = null;
        $newdate = null;
        //find first deposit payment expiration
        foreach($reservedItems as $reservedItem)
        {	
        	if($reservedItem->cancel_time === 0)
        	{
        		$onlyOnlinePayment = true;
        		$expiremessage = BookingHelper::formatExpiration($reservedItem->cancel_time);
        		break;
        	}
        	else if($reservedItem->cancel_time > 0){
        		$newdate = BookingHelper::formatExpiration($reservedItem->cancel_time, $reservation->book_time, true,true);
        		$message = BookingHelper::formatExpiration($reservedItem->cancel_time, $reservation->book_time, true);
        	}
        	else if($reservedItem->cancel_time < 0)
        	{
        		$newdate = BookingHelper::formatExpiration($reservedItem->cancel_time, $reservedItem->from,false,true);
        		$message = BookingHelper::formatExpiration($reservedItem->cancel_time, $reservedItem->from);
        	}
        	
        	if($reservedItem->cancel_time !== null)
        	{
        		if(BookingHelper::gmStrtotime($newdate) < BookingHelper::gmStrtotime($date) )
        		{	$date = $newdate;
        			$expiremessage = $message;
        		}
        	}
        }
        
        //if is no expiration
        if($expiremessage === 0)
        	$expiremessage = BookingHelper::formatExpiration(null);
        
        $juri = JURI::getInstance();
        $juri->setVar('tmpl', 'component');
        $juri->setVar('layout', 'customer');
        $juri->setVar('print', '1');
        $this->printLink = $juri->toString();
        $juri->setVar('layout', 'voucher_print');
        $this->voucherLink = $juri->toString();        
        $juri->setVar('layout', 'voucher_pdf');
        $this->pdfLink = $juri->toString();
        
                $this->assignRef('depositExpires',$expiremessage);
        $this->assignRef('isAdmin', $isAdmin);
        $this->assignRef('modelReservation', $modelReservation);
        $this->assignRef('modelSubject', $modelSubject);
        $this->assignRef('reservation', $reservation);
        $this->assignRef('reservedItems', $reservedItems);
        $this->assignRef('customer', $customer);
        $this->assignRef('subjects', $subjects);
        $this->assignRef('user', $user);
        $this->assignRef('error', $error);
        $this->assignRef('params', $params);
        $this->assignRef('id', $id);

        if (isset($price))
            $this->assignRef('price', $price);
        
        parent::display($tpl);
    }
    
    /**
     * Get custom user field list assigend with item template in cart.
     * @return array
     */
    public function getCustomFields()
    {
        $fields = array();
        foreach ((array) AFactory::getConfig()->rsExtra as $field) { // custom user field list
            if (empty($field['template'])) {
                $fields[] = $field;
            } else { 
                foreach ($this->reservedItems as $item) { // cart item list
                    if (in_array($this->subjects[$item->subject]->template, $field['template'])) { // item template is assigned with custom user field
                        $fields[] = $field;
                        break;
                    }
                }
            }
        }
        return $fields;
    }
}

?>