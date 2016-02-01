<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

//import needed Joomla! libraries
jimport('joomla.application.component.view');

//import needed JoomLIB helpers
AImporter::helper('parameter', 'route');

//import needed assets
AImporter::js('view-upgrade');

//user custom config
AImporter::helper('user');

class BookingViewUpgrade extends JViewLegacy
{

    function display($tpl = null)
    {
    	if(AUser::onlyOwner())
    		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
    	
        if ($this->getLayout() == 'message') {
            
            $url = ARoute::view(VIEW_UPGRADE);
            $redir = JRequest::getVar('redirto', null, 'post');
            if (! is_null($redir)) {
                $url = 'index.php?option=' . OPTION . '&' . $redir;
            }
            
            JToolBarHelper::title(JText::_('CHECK_UPDATES'), 'upgrade');
            JToolBarHelper::back('Continue', $url);
            
            $this->assign('url', $url);
        }
        
        parent::display($tpl);
    }
}

?>