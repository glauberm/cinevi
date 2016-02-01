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
AImporter::model('customer', 'occupancytypes', 'reservationtypes', 'reservations', 'reservationitems', 'prices', 'subject', 'subjects', 'supplements', 'template', 'reservation');
//import needed JoomLIB helpers
AImporter::helper('booking', 'config', 'document', 'image', 'parameter', 'string');
//import needed objects
AImporter::object('box', 'date', 'day', 'service');
if (IS_ADMIN) {
	BookingHelper::upgradeMootools125();
    //import needed Joomla! libraries
    jimport('joomla.html.pane');
    //import needed assets
    AImporter::js('validator', 'view-subject', 'view-images', 'view-files');
    AImporter::css('view-subject', 'template');
    //setup time picker component
    BookingHelper::importTimePicker();
    ADocument::setScriptJuri();
    ADocument::setCalendarHolder();
    AHtml::importIcons();
} elseif (IS_SITE) {
    //import needed Joomla! libraries
    jimport('joomla.application.pathway');
    //import needed assets
    AImporter::js('calendars');
    AImporter::js('supplements');
    AImporter::js('view-reservation-submitbutton');
    AImporter::joomlaJS();
    ADocument::setScriptJuri();
}

class BookingViewSubject extends JViewLegacy
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
        $user = JFactory::getUser();

        if (IS_SITE || $this->getLayout() == 'calendar') {
        	//defines constants
        	define('SESSION_PREFIX', 'booking_site_subject_' . ($id = JRequest::getInt('id')) . '_detail_');
        	define('SESSION_TESTER', 'booking_site_subject_tester');
        }
        
        $id = JRequest::getInt('id', ARequest::getCid());
        
        if (IS_SITE || $this->getLayout() == 'calendar') {
            if (JRequest::getString('pre_from') && JRequest::getString('pre_to')) { // pre-select interval from search result
                $from = JFactory::getDate(JRequest::getString('pre_from'));
                // pre-select first day of selected interval in the session 
                // month layout
                $mainframe->setUserState(('vsdmcy' . $id), $from->format('Y'));
                $mainframe->setUserState(('vsdmcm' . $id), $from->format('n'));
                // week layout
                $mainframe->setUserState(('vsdwcy' . $id), $from->format('Y'));
                $mainframe->setUserState(('vsdwcm' . $id), $from->format('W'));
                // day layout
                $mainframe->setUserState(('vsddcy' . $id), $from->format('Y'));
                $mainframe->setUserState(('vsddcm' . $id), $from->format('n'));
                $mainframe->setUserState(('vsddcd' . $id), $from->format('j'));                
                $to = JFactory::getDate(JRequest::getString('pre_to'));
                // pre-select last day of selected interval in the request 
                JRequest::setVar('year', $to->format('Y'));
                JRequest::setVar('month', $to->format('n'));
                JRequest::setVar('week', $to->format('W'));
                JRequest::setVar('day', $to->format('j'));
                JRequest::setVar('boxIds', array(1));
                JRequest::setVar('operation', CHECK_OP_NEXT);
            }
        }
        
        $modelSubject = new BookingModelSubject();
        $modelSupplements = new BookingModelSupplements();
        
        $modelSubject->setId($id);
        $subject = &$modelSubject->getObject();
        /* @var $subject TableSubject */
        
        if (IS_SITE) {
        	$subject->merge();
            if ($subject->parent && $config->parentsBookable == 2) {	
                $mainframe->enqueueMessage(JText::_('SUBITEM_UNBOOKABLE_ALONE'), 'notice');
                return;
            }
        }
        
        if (is_null($subject))
            $mainframe->redirect('index.php', 'Object not found or is unpublished', 'error');
            
    	if (JRequest::getString('page') == 'googlemaps') {
    		$this->assignRef('subject', $subject);
    		echo $this->loadTemplate('googlemaps');	
        	return;
        }
        
        $templateHelper = &AFactory::getTemplateHelper();
        $template = &$templateHelper->getTemplateById($subject->template);
        
        /*
        $params = json_decode($template->loadObjectParams($subject->id));
        $params = (array)$params;
        $param = null;
        foreach($params as $k=>$v)
        {
        	$param[(int)$k] = (string)$v;
        }
        $params = $param;
        
        $f = null;
        if(is_object($template->parser) && $template->parser->fields)
        	$f = $template->parser->fields->fieldset->field;
        
        $i=0;
        if($f)
        {
	        foreach($f as $k=>$v)
	        {
	        	//TODO xml node - correct indexing
	        	$name = (string)$v['name'];
				$template->parser->fields->fieldset->field[$i] = $params[$name];
				$i++;
	        }
        }
        */
        //var_dump($template->parser->asXML());
        
        $properties = new AParameter($template->loadObjectParams($subject->id), null, $template->parser);
        if ($this->getLayout() == 'form') {
            $this->_displayForm($tpl, $templateHelper, $subject);
            return;
        }
        
        if (IS_SITE) {
            $modelSubjects = new BookingModelSubjects();
            $pathway = &JPathway::getInstance('site');
            /* @var $pathway JPathwaySite */
            foreach (($parents = array_reverse($modelSubjects->loadSubjectParentsLine($subject->id))) as $parent)
                $pathway->addItem($parent->title, JRoute::_(ARoute::view(VIEW_SUBJECTS, $parent->id, $parent->alias)));
            $pathway->addItem($subject->title, JRoute::_(ARoute::view(VIEW_SUBJECTS, $subject->id, $subject->alias)));
            
            BookingHelper::setSubjectHits($subject->id, $modelSubject);
            
            $this->assignRef('parents', $parents);
        }
        
        if (IS_SITE || $this->getLayout() == 'calendar') {
        	$lists = array('from' => JString::trim(JRequest::getString('from')) , 'to' => JString::trim(JRequest::getString('to')) , 'operation' => JRequest::getInt('operation', CHECK_OP_IN));
        	
        	$this->assignRef('parents', $parents);
        	$this->assignRef('lists', $lists);
        	
        }
        
        $modelTemplate = new BookingModelTemplate();
        
        $templateTable = &$modelTemplate->_table;
        $templateTable->load($subject->template);
        $templateTable->display();
        
        $modelOccupancyTypes = new BookingModelOccupancyTypes();
        $modelOccupancyTypes->init(array('subject' => $subject->id));
        $occupancyTypes = &$modelOccupancyTypes->getData();
        
        $modelReservationTypes = new BookingModelReservationTypes();
        $modelReservationTypes->init(array('subject' => $subject->id));
        $reservationTypes = &$modelReservationTypes->getData();
        
        if (IS_SITE || $this->getLayout() == 'calendar') {
            $lists['rids'] = ARequest::getUserStateFromRequest('rids', ($defaultRids = BookingHelper::getIdsFromObjectList($reservationTypes)), 'array');
			$emptyRid = array_search('0', $lists['rids']);
            if ($emptyRid !== false) unset($lists['rids'][$emptyRid]);
            if ($subject->display_only_one_rtype && count($lists['rids']) > 1)
                $lists['rids'] = array(reset($lists['rids']));
            AHtml::setMetaData($subject);
        }
        
        $modelSupplements->init(array('subject' => $subject->id));
        $supplements = &$modelSupplements->getData();
        
        //getting logged customer
        $isAdmin = false;
        $customer = null;
        if(IS_SITE){
	        $modelCustomer = new BookingModelCustomer();
	        $modelCustomer->setIdByUserId();
	        $customer = $modelCustomer->getObject();
			/* @var $customer TableCustomer */	        
	        $isAdmin = $modelCustomer->isAdmin();
        }
        
        $mode = JRequest::getString('mode');
        $changedReservationItemId = JRequest::getInt('changed_reservation_item_id');
        if ($mode == 'change' && $user->authorise('booking.reservation.edit.item', 'com_booking')) {
            $modelReservation = new BookingModelReservation();
            $changeableItems = $modelReservation->getChangeableItems($changedReservationItemId, true);
            $this->assignRef('changeableItems', $changeableItems);
        }
        
        if ($isAdmin) {
        	$this->calendarnummonths = JFactory::getApplication()->getUserStateFromRequest('com_booking.calendarnummonths', 'calendarnummonths', $config->calendarNumMonths, 'int');
        	$this->calendarnumweeks = JFactory::getApplication()->getUserStateFromRequest('com_booking.calendarnumweeks', 'calendarnumweeks', 1, 'int');
        }
        
        $this->assignRef('subject', $subject);
        $this->assignRef('mode', $mode);
        $this->assignRef('changedReservationItemId', $changedReservationItemId);
        $this->assignRef('tmpl', JRequest::getString('tmpl'));
        $this->assignRef('properties', $properties);
        $this->assignRef('occupancyTypes', $occupancyTypes);
        $this->assignRef('reservationTypes', $reservationTypes);
        $this->assignRef('supplements', $supplements);
        $this->assignRef('customer', $customer);
        $this->assignRef('supplements', $supplements);
        $this->assignRef('templateTable', $templateTable);
        $this->assignRef('template', $template);
        $this->assignRef('isAdmin', $isAdmin);
        
        //get cancel time for object
        /*
        $modelPrices = new BookingModelPrices();
        $modelPrices->init(array('subject' => $subject->id));
        $prices = &$modelPrices->getData();
        //var_dump($prices);
        
        $this->assignRef('prices', $prices);
        */
        if($templateid = JRequest::getInt('templateid')) {
	        $db = JFactory::getDbo();
	        $query = 'SELECT `id` FROM `#__booking_subject` WHERE `template` ='.$templateid;
	        $db->setQuery($query);
	        $data = $db->loadObjectList();
	        //var_dump($data);
	        foreach($data as $object) {
	        	$modelSubject = new BookingModelSubject();
	        	$modelSubject->setId($object->id);
	        	$obj = $modelSubject->getObject();
	        	if(!$obj->id)
	        		continue;
	        	$this->day[$obj->id] = &BookingHelper::getWeekCalendar($obj, $this->setting, $isAdmin ? $this->calendarnumweeks * 7 : 'week', $isAdmin);
	        	$this->day[$obj->id]->label = $obj->title;
	        }
        }
        
        JFactory::getApplication()->setUserState('com_booking.object.last', JURI::getInstance()->toString());
        
        parent::display($tpl);
    }

    /**
     * Prepare to display page.
     * 
     * @param string $tpl name of used template
     * @param ATemplateHelper $templateHelper
     * @param TableSubject $subject
     */
    function _displayForm($tpl, &$templateHelper, &$subject)
    {
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        $templateHelper->importAssets();
        
        /* Prepare model objects */
        
        $modelOccupancyTypes = new BookingModelOccupancyTypes();
        $modelPrices = new BookingModelPrices();
        $modelReservationTypes = new BookingModelReservationTypes();
        $modelSupplements = new BookingModelSupplements();
        
        $subject->id ? $subject->clean() : $subject->init();
        
        $task = JRequest::getCmd('task');
        
        /* Prepare objects template */
        
        if (($taskAdd = $task == 'add'))
            $subject->template = JRequest::getInt('template');
        
        $subject->newTemplate = $subject->template == 0;
        
        $template = &$templateHelper->getTemplateById($subject->template);
        
        $templateTable = $modelPrices->getTable('template');
        /* @var $templateTable TableTemplate */
        $templateTable->load($subject->template);
        $templateTable->display();
        
        $params = $template->loadObjectParams($subject->id);

        $properties = new AParameter($params, null, $template->parser);
        

        /* Prepare page bookmarks */
        
        $tabsParams = array();
        
        if (! $taskAdd)
            $tabsParams['useCookie'] = true;
        
        /* Load objects occupancy types */
        
        $modelOccupancyTypes->init(array('subject' => $subject->id));
        $otypes = $modelOccupancyTypes->getData();

        /* Load objects reservation types */
        
        $modelReservationTypes->init(array('subject' => $subject->id));
        $rtypes = &$modelReservationTypes->getData();
        
        $emptyRtype = &$modelReservationTypes->getMainTable();
        /* @var $emptyRtype TableReservationType */
        $emptyRtype->init();
        
        array_unshift($rtypes, $emptyRtype);
        
        $rtcount = count($rtypes);
        for ($i = 0; $i < $rtcount; $i ++) {
            $rtype = &$rtypes[$i];
            /* @var $rtype TableReservationType */
            $rtype->fullTitle = JText::sprintf($rtype->type == RESERVATION_TYPE_HOURLY ? 'RESERVATION_TYPE_HOURLY_LABEL' : 'RESERVATION_TYPE_DAILY_LABEL', $rtype->title);
        }
        

        /* Load objects prices */
        
        $modelPrices->init(array('subject' => $subject->id));
        $prices = &$modelPrices->getData();
        
        $emptyPrice = &$modelPrices->getMainTable();
        /* @var $emptyPrice TablePrice */
        $emptyPrice->init();
        
        array_unshift($prices, $emptyPrice);
        

        /* Load objects supplements */
        
        $modelSupplements->init(array('subject' => $subject->id));
        $supplements = &$modelSupplements->getData();
        
        $emptySupplement = &$modelSupplements->getMainTable();
        /* @var $emptySupplement TableSupplement */
        $emptySupplement->init();
        
        array_unshift($supplements, $emptySupplement);
        

        JFilterOutput::objectHTMLSafe($subject, ENT_QUOTES, array('google_maps_code'));
        
        $params = &JComponentHelper::getParams(OPTION);
        /* @var $params JParameter */
        
        AController::setEditorProperties($subject);
        
        $calendars = &BookingHelper::loadCalendars();
        
        $config = &AFactory::getConfig();
        $templateHelper->importIconsToJS(AImage::getIPath($config->templatesIcons), AImage::getRIPath($config->templatesIcons));
        
        $this->assignRef('subject', $subject);
        $this->assignRef('properties', $properties);
        $this->assignRef('tabsParams', $tabsParams);
        $this->assignRef('otypes', $otypes);
        $this->assignRef('rtypes', $rtypes);
        $this->assignRef('prices', $prices);
        $this->assignRef('supplements', $supplements);
        $this->assignRef('templateHelper', $templateHelper);
        $this->assignRef('template', $template);
        $this->assignRef('templateTable', $templateTable);
        $this->assignRef('params', $params);
        $this->assignRef('calendars', $calendars);
        
        parent::display($tpl);
    }
}

?>