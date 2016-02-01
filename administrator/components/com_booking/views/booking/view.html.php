<?php

/**
 * View component administration control panel 
 * with buttons to open main parts of component.
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
//import needed JoomLIB helpers
AImporter::helper('booking', 'route');
//import needed models
AImporter::model('customer', 'reservations', 'reservationitems');
//import custom icons
AHtml::importIcons();
//defines constants
if (! defined('SESSION_PREFIX')) {
    if (IS_ADMIN) {
        define('SESSION_PREFIX', 'booking_control_panel_');
    }
}

class BookingViewBooking extends JViewLegacy
{
    /**
     * Array containig browse table reservations items to display.
     * 
     * @var array
     */
    var $items;
    
    /**
     * Standard Joomla! browse tables pagination object.
     * 
     * @var JPagination
     */
    var $pagination;

    /**
     * Prepare to display page.
     * 
     * @param string $tpl name of used template
     */
    function display($tpl = null)
    {
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $this->user = &JFactory::getUser();
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        
				$info = BookingHelper::getBookingInfo();
				$this->assignRef('info', $info);	
        
        $document->setTitle(COMPONENT_NAME);
        
        $modelReservations = new BookingModelReservations();
        
        $this->lists = array();
        
        $this->lists['limit'] = ARequest::getUserStateFromRequest('limit', $mainframe->getCfg('list_limit'), 'int');
        $this->lists['limitstart'] = ARequest::getUserStateFromRequest('limitstart', 0, 'int');
        
        $this->lists['order'] = ARequest::getUserStateFromRequest('filter_order', 'items-from', 'cmd');
        $this->lists['order_Dir'] = ARequest::getUserStateFromRequest('filter_order_Dir', 'ASC', 'word');
        
        $this->lists['from'] = AModel::getNow();
        
        $modelReservations->init($this->lists);
        
        $this->pagination = &$modelReservations->getPagination();
        $this->items = &$modelReservations->getData();
        
        if (count($this->items)) {        	
        	$modelReservationItems = new BookingModelReservationItems();
        	$modelReservationItems->init(array());
        	
        	foreach ($this->items as $item) {     		
        		$modelReservationItems->_lists['reservation_item-reservation_id'] = $item->id;
        		unset($modelReservationItems->_data);       		
        		$this->reservedItems[$item->id] = $modelReservationItems->getData();
        	}
        }
        
        $this->configRoute = $this->_getConfigRoute();
        
        parent::display($tpl);
    }
    
    private function _getConfigRoute()
    {
    	$configRoute = array();
    	
    	if (ISJ3) {
    		$uri = (string) JUri::getInstance();
    		$return = urlencode(base64_encode($uri));
    		
    		$configRoute['route'] = 'index.php?option=com_config&view=component&component=' . OPTION . '&return=' . $return;
    		$configRoute['params'] = array();
    	} else {
    		$configRoute['route'] = 'index.php?option=com_config&view=component&component=' . OPTION . '&tmpl=component';
    		$configRoute['params'] = array('rel' => "{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}", 'class' => 'modal');
    	}
    	
    	return $configRoute;
    }
}

?>