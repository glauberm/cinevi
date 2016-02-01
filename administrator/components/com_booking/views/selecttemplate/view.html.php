<?php

/**
 * View templates list to select
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
//import admin template css
AImporter::adminTemplateCss(null, 'general', 'icon', 'rounded');
AImporter::adminTemplateCss('system', 'system');
//import needed assets
AImporter::js('view-selecttemplate');

class BookingViewSelecttemplate extends JViewLegacy
{
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

    function display($tpl = null)
    {
        $this->templateHelper = AFactory::getTemplateHelper();
        $this->templates = $this->templateHelper->_templates;
        
        parent::display($tpl);
    }
}

?>