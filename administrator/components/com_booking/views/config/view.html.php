<?php
/**
 * View component configuration 
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
jimport('joomla.html.pane');

//import needed JoomLIB helpers
AImporter::helper('parameter', 'route');
//import needed assets
AImporter::js('view-config');

class BookingViewConfig extends JViewLegacy
{
    /**
     * Component configuration as Joomla params object JRegistry.
     * 
     * @var JRegistry
     */
    var $params;

    function display($tpl = null)
    {
        JRequest::setVar('hidemainmenu', 1);
        
        $this->params = &AParameter::loadComponentParams();

        if (is_null($this->params->get('customers_usergroup')))
            $this->params->set('customers_usergroup', CUSTOMER_GID);
        
        parent::display($tpl);
    }

}
?>