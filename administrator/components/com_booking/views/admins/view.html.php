<?php

/**
 * View admins list.
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
AImporter::helper('route', 'booking', 'request', 'user');
//import custom icons
AHtml::importIcons();

//defines constants
if (! defined('SESSION_PREFIX')) {
    define('SESSION_PREFIX', 'booking_admins_list_');
}
if (! defined('SESSION_TESTER')) {
    define('SESSION_TESTER', 'booking_admins_list_tester');
}

class BookingViewAdmins extends JViewLegacy
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
     * Joomla! Core User Groups.
     * 
     * @var array
     */
    var $usertypes;

    /**
     * Prepare to display page.
     * 
     * @param string $tpl name of used template
     */
    function display($tpl = null)
    {
    	if(AUser::onlyOwner())
    		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
    	 
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        
        $document->setTitle(JText::_('LIST_OF_ADMINS'));
        
        $model = new BookingModelAdmins();
        
        $this->lists = array();
        
        $this->lists['limit'] = ARequest::getUserStateFromRequest('limit', $mainframe->getCfg('list_limit'), 'int');
        $this->lists['limitstart'] = ARequest::getUserStateFromRequest('limitstart', 0, 'int');
        
        $this->lists['order'] = ARequest::getUserStateFromRequest('filter_order', 'username', 'cmd');
        $this->lists['order_Dir'] = ARequest::getUserStateFromRequest('filter_order_Dir', 'ASC', 'word');
        
        $this->lists['search'] = ARequest::getUserStateFromRequest('filter_search', '', 'string');
        $this->lists['global_manager'] = ARequest::getUserStateFromRequest('filter_global_manager', '', 'string');
        
        $this->lists['usertype'] = array();
        
        $usertypes = &AUser::getUserGroups();
        $this->lists['usertype'] = array();
        $count = count($usertypes);
        for ($i = 0; $i < $count; $i ++) {
            $usertype = $usertypes[$i]['title'];
            $safeName = JFilterOutput::stringURLSafe($usertype);
            $param = 'usertype_' . $safeName;
            $this->lists['usertype'][$usertype] = ARequest::getUserStateFromRequest($param, $usertype, 'int', true);
            $this->usertypes[$param] = $usertype;
        }
        unset($usertypes);
        
        $model->init($this->lists);
        
        $this->items = &$model->getData();
        $this->pagination = &$model->getPagination();
        
        parent::display($tpl);
    }
}

?>