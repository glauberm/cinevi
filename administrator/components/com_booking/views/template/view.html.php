<?php

/**
 * View template edit page
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
AImporter::helper('booking', 'config', 'document', 'image', 'parameter');
//import needed assets
AImporter::css('view-subject', 'template');
AImporter::js('view-template');

class BookingViewTemplate extends JViewLegacy
{
    
    /**
     * Object to working with templates.
     * 
     * @var ATemplateHelper
     */
    var $templateHelper;
    
    /**
     * Edited template
     * 
     * @var ATemplate
     */
    var $template;
    
    /**
     * Template parameters object
     * 
     * @var AParameter
     */
    var $properties;
    
    /**
     * Template table object for database operations
     * 
     * @var TableTemplate
     */
    var $templateTable;
    
    /**
     * Usable system calendars.
     * 
     * @var array
     */
    var $calendars;

    /**
     * Prepare to display page.
     * 
     * @param string $tpl name of used template
     */
    function display($tpl = null)
    {
        $id = ARequest::getCid();
        
        $this->templateHelper = &AFactory::getTemplateHelper();
        $this->templateHelper->importAssets();
        $this->template = &$this->templateHelper->getTemplateById($id);
        
        $this->properties = new AParameter('', null, $this->template->parser);
        
        $model = &$this->getModel();
        /* @var $model BookingModelTemplate */
        $this->templateTable = &$model->_table;
        /* @var $templateTable TableTemplate */
        $this->templateTable->load($this->template->id);
        $this->templateTable->display();
        
        $this->calendars = &BookingHelper::loadCalendars();
        
        $config = &AFactory::getConfig();
        $this->templateHelper->importIconsToJS(AImage::getIPath($config->templatesIcons), AImage::getRIPath($config->templatesIcons));
        
        parent::display($tpl);
    }
}

?>