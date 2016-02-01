<?php

/**
 * View customers list.
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
AImporter::helper('route', 'booking', 'request');
//import needed assets
AImporter::js('view-customers');
//import custom icons
AHtml::importIcons();

//defines constants
if (! defined('SESSION_PREFIX')) {
    if (IS_ADMIN) {
        define('SESSION_PREFIX', 'booking_customers_list_');
    } elseif (IS_SITE) {
        define('SESSION_PREFIX', 'booking_site_customers_list_');
    }
}
if (! defined('SESSION_TESTER')) {
    if (IS_ADMIN) {
        define('SESSION_TESTER', 'booking_customers_list_tester');
    }
}

class BookingViewCustomers extends JViewLegacy
{
    /**
     * Array containing browse table filters properties.
     * 
     * @var array
     */
    var $lists;
    
    /**
     * Array containig browse table subjects items to display.
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
     * Standard Joomla! user object.
     * 
     * @var JUser
     */
    var $user;
    
    /**
     * Sign if table is used to popup selecting customers.
     * 
     * @var boolean
     */
    var $selectable;
    
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
    	if (!JFactory::getUser()->authorise('booking.view.customers', 'com_booking'))
    		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
    	 
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $this->user = &JFactory::getUser();
        
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        
        $document->setTitle(JText::_('CUSTOMERS_LIST'));
        
        $model = new BookingModelCustomers();
        
        $this->lists = array();
        
        $this->lists['limit'] = ARequest::getUserStateFromRequest('limit', $mainframe->getCfg('list_limit'), 'int');
        $this->lists['limitstart'] = ARequest::getUserStateFromRequest('limitstart', 0, 'int');
        
        $this->lists['order'] = ARequest::getUserStateFromRequest('filter_order', 'id', 'cmd');
        $this->lists['order_Dir'] = ARequest::getUserStateFromRequest('filter_order_Dir', 'DESC', 'word');
        
        $this->lists['search'] = ARequest::getUserStateFromRequest('filter_search', '', 'string');
        $this->lists['state'] = ARequest::getUserStateFromRequest('filter_state', '', 'string');
        
        $model->init($this->lists);
        
        $this->pagination = &$model->getPagination();
        
        $this->items = &$model->getData();
        
        $this->params = &JComponentHelper::getParams(OPTION);
        
        $this->selectable = JRequest::getCmd('task') == 'element';
        
        parent::display($tpl);
    }
}

?>