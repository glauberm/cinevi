<?php

/**
 * View subject detail page or page with edit form.
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

jimport('joomla.application.component.view');

//import needed models
AImporter::model('errors');
//import needed JoomLIB helpers
AImporter::helper('booking', 'config', 'document', 'image', 'parameter', 'string');
//import needed objects
AImporter::object('box', 'date', 'day', 'service');

    //import needed Joomla! libraries
    jimport('joomla.html.pane');
    //import needed assets
    AImporter::js('validator', 'view-images', 'view-files');

    ADocument::setScriptJuri();
    AHtml::importIcons();


class BookingViewErrors extends JViewLegacy
{

    /**
     * Prepare to display page.
     * 
     * @param string $tpl name of used template
     */
    function display($tpl = null)
    {
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        $config = AFactory::getConfig();
        /* @var $config BookingConfig */
        
        //get all files from /logs/
        $this->files = $this->get('allErrors');
        
        //to enable debug mode, lifetime must be greather than time()
        $this->debugActive = JFactory::getApplication()->getUserState('com_booking.errors.lifetime', 0);
        
        parent::display($tpl);
    }
}

?>