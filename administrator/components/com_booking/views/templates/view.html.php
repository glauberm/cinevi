<?php

/**
 * View templates list.
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
jimport('joomla.html.pagination');
//import needed JoomLIB helpers
AImporter::helper('booking', 'request', 'route', 'user');

AImporter::js('view-templates');
//defines constants
if (! defined('SESSION_PREFIX')) {
    if (IS_ADMIN) {
        define('SESSION_PREFIX', 'booking_templates_list_');
    }
}

class BookingViewTemplates extends JViewLegacy
{
    /**
     * Array containing browse table filters properties.
     * 
     * @var array
     */
    var $lists;
    
    /**
     * Object to working with templates.
     * 
     * @var ATemplateHelper
     */
    var $templateHelper;
    
    /**
     * List of usable templates
     * @var array
     */
    var $templates;
    
    /**
     * Standard Joomla! user object.
     * 
     * @var JUser
     */
    var $user;
    
    /**
     * Standard Joomla! browse tables pagination object.
     * 
     * @var JPagination
     */
    var $pagination;

    function display($tpl = null)
    {
    	if(AUser::onlyOwner())
    		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
    	 
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        
        $this->user = &JFactory::getUser();
        
        $this->templateHelper = &AFactory::getTemplateHelper();
        $this->templates = $this->templateHelper->_templates;
        
        $this->lists['search'] = ARequest::getUserStateFromRequest('search', '', 'string');
        $this->lists['search'] = JString::trim($this->lists['search']);
        $this->lists['search'] = JString::strtolower($this->lists['search']);
        
        $this->lists['order'] = ARequest::getUserStateFromRequest('filter_order', 'id', 'cmd');
        $this->lists['order_Dir'] = ARequest::getUserStateFromRequest('filter_order_Dir', 'DESC', 'word');
        
        $this->lists['limit'] = ARequest::getUserStateFromRequest('limit', $mainframe->getCfg('list_limit'), 'int');
        $this->lists['limitstart'] = ARequest::getUserStateFromRequest('limitstart', 0, 'int');
        
    	if ($this->lists['search']) {
    		$total = 0;
    		foreach ($this->templates as $i => $template) {
				$title = JString::strtolower($template->name);
				$pos = JString::strpos(JString::strtolower($template->name), $this->lists['search']);
				if ($pos === false)
					unset($this->templates[$i]);				
				else
				  	$total++;  	
    		}
		} else 
			$total = count($this->templates);
        
        $this->pagination = new JPagination($total, $this->lists['limitstart'], $this->lists['limit']);
        
        if (IS_ADMIN) {
        	$this->selectable = JRequest::getString('task') == 'element';
        	$this->type = JRequest::getString('type');
        	$this->input = JRequest::getString('input');
        }
        
        parent::display($tpl);
    }
}

?>