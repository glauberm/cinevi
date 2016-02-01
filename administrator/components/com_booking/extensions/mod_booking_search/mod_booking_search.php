<?php

/**
 * Extended search module.
 *
 * @package		ARTIO Booking
 * @subpackage  modules
 * @copyright	Copyright (C) 2012 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @link        http://www.artio.net Official website
 */

/* @var $params JRegistry */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

if (!defined('SESSION_PREFIX'))
    define('SESSION_PREFIX', 'booking_search');

if (!defined('SESSION_TESTER'))
    define('SESSION_TESTER', 'booking_search');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

$helpers = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booking' . DS . 'helpers' . DS;

if (JFile::exists($helpers . 'importer.php')) {

    $lang = JFactory::getLanguage();
    /* @var $lang JLanguage */
    $lang->load('com_booking.common', JPATH_ADMINISTRATOR);

    include_once($helpers . 'importer.php');
    include_once($helpers . 'html.php');
    include_once($helpers . 'model.php');

    // start Booking core
    AImporter::defines();
    AImporter::helper('booking', 'factory', 'installer');
    AImporter::table('template', 'subject');
    AImporter::model('subjects');
    AImporter::js('common');
    //JHtml::_('behavior.mootools');
    JHtml::_('behavior.framework');
    //BookingHelper::upgradeMootools125();
    AInstallerJoomFish::init();

    $app = JFactory::getApplication();
    /* @var $app JApplication */

    $moduleclassSfx = htmlspecialchars($params->get('moduleclass_sfx'));

    $templateHelper = AFactory::getTemplateHelper();
    $stemplates = $templateHelper->getSelectBox('template_area', 'EVERYWHERE', $app->getUserStateFromRequest('booking_search_template_area', 'template_area'), false, 'class="input-medium"');

    $manifest = JInstaller::parseXMLInstallFile(dirname(__FILE__) . DS . 'mod_booking_search.xml');

    if ($params->get('properties')) {
        $searchables = array();
        $modelSubjects = new BookingModelSubjects();
        $modelSubjects->init(array('parent' => null, 'access' => AModel::getAccess()));
        $templates = $modelSubjects->getAvailableTemplates();
        $templateHelper = AFactory::getTemplateHelper();
        foreach ($templates as $atmpl) {
            /* @var $item TableSubject */
            $atmpl = $templateHelper->getTemplateById($atmpl);
            /* @var $atmpl ATemplate */
            $properties = new AParameter($atmpl->loadObjectParams($atmpl->id), null, $atmpl->parser);
            foreach ($properties->loadParamsToFields() as $param)
                if ($param[PARAM_SEARCHABLES] == 1) {
                    /* set request parameters*/
                    $param[PARAM_REQUESTNAME] = ARequest::getPropertyName('', $atmpl->id, $param[PARAM_NAME]);
                    $param[PARAM_REQUESTVALUE] = ARequest::getUserStateFromRequest($param[PARAM_REQUESTNAME], '', 'string', true);
                    if ($param[PARAM_TYPE] == 'list' || $param[PARAM_TYPE] == 'radio') {
                        /* set options if parameter is radio button or selectbox */
                        $node = $param[PARAM_NODE];
                        /* @var $node JSimpleXMLElement */
                        foreach ($node->children() as $option)
                            /* @var $option JSimpleXMLElement */
                            $param[PARAM_OPTIONS][] = array(($value = $option['value']), ATemplate::translateParam($value));
                    }
                    $searchables[] = $param;
                }
        }
    }

    $layout = JModuleHelper::getLayoutPath('mod_booking_search', $params->get('layout', 'default'));
    if (JFile::exists($layout))
        require_once $layout;
}
?>