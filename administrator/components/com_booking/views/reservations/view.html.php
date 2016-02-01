<?php

/**
 * View reservations list.
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
AImporter::model('customer', 'reservations', 'reservationitems', 'reservationsupplements');
//import needed JoomLIB helpers
AImporter::helper('route', 'booking', 'request', 'toolbar');
//import needed assets
AImporter::js('view-reservations');
//import custom icons
AHtml::importIcons();

//defines constants
if (! defined('SESSION_PREFIX')) {
    if (IS_ADMIN) {
        define('SESSION_PREFIX', 'booking_reservations_list_');
    } elseif (IS_SITE) {
        define('SESSION_PREFIX', 'booking_site_reservations_list_');
    }
}

if (! defined('SESSION_TESTER')) {
    if (IS_ADMIN) {
        define('SESSION_TESTER', 'booking_reservations_list_tester');
    } elseif (IS_SITE) {
        define('SESSION_TESTER', 'booking_site_reservations_list_tester');
    }
}

class BookingViewReservations extends JViewLegacy
{
    /**
     * Array containing browse table filters properties.
     * 
     * @var array
     */
    var $lists;
    
    /**
     * Array containig browse table reservations items to display.
     * 
     * @var array
     */
    var $items;
    
    /**
     * Array containig reserved items (subjects) for given $items list.
     * 
     * @var array
     */
    var $reservedItems;
    
    /**
     * Standard Joomla! browse tables pagination object.
     * 
     * @var JPagination
     */
    var $pagination;
    
    /**
     * Standard Joomla! user object.
     * 
     * @var JUser
     */
    var $user;
    
    /**
     * Standard Joomla! object to working with component parameters.
     * 
     * @var $params JParameter
     */
    var $params;

    /**
     * Prepare to display page.
     * 
     * @param string $tpl name of used template
     */
    function display($tpl = null)
    {
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $config = AFactory::getConfig();
        
        $this->user = &JFactory::getUser();
        
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        
        $document->setTitle(JText::_('RESERVATION_LIST'));
        
        $modelReservations = new BookingModelReservations();
        $modelCustomer = new BookingModelCustomer();
        
        $this->lists = array();
        
        $this->lists['limit'] = ARequest::getUserStateFromRequest('limit', $mainframe->getCfg('list_limit'), 'int');
        
        $this->lists['order'] = ARequest::getUserStateFromRequest('filter_order', 'id', 'cmd');
        $this->lists['order_Dir'] = ARequest::getUserStateFromRequest('filter_order_Dir', 'DESC', 'word');
        
        $this->lists['reservation-surname'] = ARequest::getUserStateFromRequest('filter_reservation-surname', '', 'string');

        $this->lists['reservation-id'] = ARequest::getUserStateFromRequest('filter_reservation-id', '', 'string');
        
        if ($this->lists['reservation-id'] == 0) {
        	$this->lists['reservation-id'] = '';
        }
        
        if (IS_ADMIN) {
            
            $this->lists['limitstart'] = ARequest::getUserStateFromRequest('limitstart', 0, 'int');
            if ($mainframe->getUserState(OPTION.'.user_reservation_items'))
            	$mainframe->enqueueMessage('<a href="' . JRoute::_(ARoute::view(VIEW_RESERVATION,null, null, array('layout'=>'form'))).'">'.JText::_('CURRENT_RESERVATION').'</a>');
            
        } elseif (IS_SITE) {

        	$modelCustomer->setIdByUserId();

        	if (! ($modelCustomer->getId() || $modelCustomer->isAdmin())) {
        		// only cutomer or admin can see reservations
        		$mainframe->redirect('index.php', JText::_('UNABLE_DISPLAY_RESERVATIONS_MUST_BE_LOGGED_AS_CUSTOMER_OR_ADMIN'), 'notice');
        	}

        	//only for standard customer which is not global admin
        	if(!$modelCustomer->isAdmin())
        	{
        		// if customer logged filter only own reservations
        		$this->lists['customer-id'] = $modelCustomer->getId();
        		// customer cannot see trashed reservations
        		$this->lists['reservation-state'][RESERVATION_TRASHED] = false;
        	}
        	//only for managing own reservations
        	else
        		$this->lists['is_administrator'] = true;

        	$this->lists['limitstart'] = JRequest::getInt('limitstart');

        	// use extra layout for user or admin
        	if ($modelCustomer->getId() && !$modelCustomer->isAdmin() && $this->getLayout() != 'customer')
        		$mainframe->redirect(ARoute::convertUrl(JRoute::_(ARoute::viewlayout(VIEW_RESERVATIONS, 'customer'))));
        	elseif ($modelCustomer->isAdmin() && $this->getLayout() != 'admin')
        		$mainframe->redirect(ARoute::convertUrl(JRoute::_(ARoute::viewlayout(VIEW_RESERVATIONS, 'admin'))));
        }
        
        $this->lists['reservation_status'] = JFactory::getApplication()->getUserStateFromRequest('com_booking.reservation_status', 'filter_reservation_status', '', 'string');
        $this->lists['payment_status'] = JFactory::getApplication()->getUserStateFromRequest('com_booking.payment_status', 'filter_payment_status', '', 'string');
        
        $this->lists['items-subject_title'] = ARequest::getUserStateFromRequest('filter_items-subject_title', '', 'string');
        
        $this->lists['from'] = ARequest::getUserStateFromRequest('filter_from', '', 'string');
        $this->lists['to'] = ARequest::getUserStateFromRequest('filter_to', '', 'string');
        $this->lists['date_filtering'] = ARequest::getUserStateFromRequest('date_filtering', '', 1, 'int', false, false);
        
        $modelReservations->init($this->lists);
        
        $this->pagination = &$modelReservations->getPagination();
        $this->items = &$modelReservations->getData();
        
        $this->reservedItems = array();
        if (count($this->items)) {
        	
        	$now = time();
        	
        	$modelReservationItems = new BookingModelReservationItems();
            // show all reservation items
            $this->lists['limitstart'] = 0;
            $this->lists['limit'] = 1000;
        	$modelReservationItems->init($this->lists);
        	
            if ($this->getLayout() == 'csv' || $this->getLayout() == 'xls' || $config->showSupplementsColumn) {
                $modelReservationSupplements = new BookingModelReservationSupplements();
                $modelReservationSupplements->init(array());
            }
        	
        	foreach ($this->items as &$item) {
        		
        		$item->isExpired=false;
        		
        		$modelReservationItems->_lists['reservation_item-reservation_id']=$item->id;
        		$modelReservationItems->_lists['order'] = '';                
        		unset($modelReservationItems->_data);
        		$this->reservedItems[$item->id]=$modelReservationItems->getData();
                
                if ($this->getLayout() == 'csv' || $this->getLayout() == 'xls' || $config->showSupplementsColumn) {
                    foreach ($this->reservedItems[$item->id] as $reservedItem) {
                        $modelReservationSupplements->_lists['reservation']=$reservedItem->id;
                        unset($modelReservationSupplements->_data);
                        $this->reservedSupplements[$reservedItem->id]=$modelReservationSupplements->getData();
                    }
                }

        		foreach ($this->reservedItems[$item->id] as $reservedItem)
        			if ($reservedItem->rtype != RESERVATION_TYPE_PERIOD)
        				if ($now > strtotime($reservedItem->to))
        					$item->isExpired=true;
        			
        		if (IS_SITE) {
        			 $modelReservationItems->addTitleTranslation($this->reservedItems[$item->id]);
        		}
        	}
        }
        
        //finding item which must be paid immediately
        $onlyOnlinePayment = false;
        $expiremessage = 0;
        
        foreach($this->reservedItems as $key=>$reservedItems){
	        //select first usable expire time
	        foreach($reservedItems as $reservedItem)
	        {
	        	if(($reservedItem->cancel_time > 0) OR ($reservedItem->cancel_time < 0))
	        	{
	        		$expiremessage = BookingHelper::formatExpiration($reservedItem->cancel_time, $reservedItem->from);
	        		break;
	        	}
	        }
	        
	        //find first deposit payment expiration
	        foreach($reservedItems as $reservedItem)
	        {
	        	$date = BookingHelper::formatExpiration($reservedItem->cancel_time, $reservedItem->from);
	        	 
	        	if($reservedItem->cancel_time == 0)
	        	{
	        		$onlyOnlinePayment = true;
	        		$expiremessage = $date;
	        		break;
	        	}
	        	else if($reservedItem->cancel_time !== null)
	        	{
	        		if(BookingHelper::gmStrtotime($expiremessage) > BookingHelper::gmStrtotime($date) )
	        			$expiremessage = $date;
	        	}
	        }
	        
	        //if is no expiration
	        if($expiremessage === 0)
	        	$expiremessage = BookingHelper::formatExpiration(null);
	        
	        $expiremessages[$key] = $expiremessage;
        }

        $this->assignRef('depositExpires',$expiremessages);
        $this->params = &JComponentHelper::getParams(OPTION);
        
        $this->customerHomepage = null;
        if ($mainframe->isSite() && $mainframe->getParams()->get('show_customer_homepage_link', '0') === '1' && $mainframe->getParams()->get('customer_homepage_link_menu_item', '0') !== '0') 
        {
        	$menuItem = $mainframe->getMenu()->getItem($mainframe->getParams()->get('customer_homepage_link_menu_item'));
        	$this->customerHomepage = is_object($menuItem) ? JRoute::_($menuItem->link . '&Itemid=' . $menuItem->id) : null;
        }
        
        parent::display($tpl);
    }
    
}

?>