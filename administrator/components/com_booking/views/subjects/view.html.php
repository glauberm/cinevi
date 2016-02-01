<?php

/**
 * View subjects list.
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
jimport('joomla.application.pathway');

//import needed models
AImporter::model('subject', 'subjects', 'reservationtypes', 'reservations', 'reservationitems', 'prices');
//import needed JoomLIB helpers
AImporter::helper('route', 'booking', 'config', 'parameter', 'request', 'toolbar', 'utils');
//import needed assets
AImporter::js('view-subjects');
AImporter::css('view-subjects');
//import custom icons
AHtml::importIcons();
//import needed objects
AImporter::object('box', 'date', 'day', 'service');

//defines constants
if (! defined('SESSION_PREFIX')) {
    if (IS_ADMIN) {
        define('SESSION_PREFIX', 'booking_subjects_list_');
    } elseif (IS_SITE) {
        define('SESSION_PREFIX', 'booking_site_subjects_list_');
    }
}
if (! defined('SESSION_TESTER')) {
    define('SESSION_TESTER', 'booking_subjects_list_tester');
}

class BookingViewSubjects extends JViewLegacy
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
     * Object to working with templates.
     * 
     * @var ATemplateHelper
     */
    var $templateHelper;
    
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
    
    /**
     * Database operations support object.
     * 
     * @var BookingModelSubject
     */
    var $modelSubject;
    
    /**
     * Standard Joomla! object to working with component parameters.
     * 
     * @var $params JParameter
     */
    var $params;
    
    /**
     * Sign if ordering setting of browse table is turn on.
     * 
     * @var boolean
     */
    var $turnOnOrdering;
    
    /**
     * Sign if table is used to popup selecting subjects.
     * 
     * @var boolean
     */
    var $selectable;
    
    /**
     * If table is use to popup selecting - set the selecting type.
     * 
     * @var boolean
     */
    var $type;
    
    /**
     * Subject list parent
     * 
     * @var TableSubject
     */
    var $subject;
    
    /**
     * Subject list table total count
     * 
     * @var int
     */
    var $tableTotal;
    
    /**
     * All templates used by listened subjects
     * 
     * @var array
     */
    var $templates = array();
    
    /**
     * Filterables properties
     * 
     * @var array
     */
    var $filterables = array();
    
    /**
     * Subject table object
     * 
     * @var TableSubject
     */
    var $subjectTable;
    
    /**
     * All available access list levels.
     * 
     * @var array
     */
    var $access;

    /**
     * Calendars months.
	 *
     * @var array
     */
    var $months;

    /**
     * List of insensitive days in calendars.
     *
     * @var array
     */
    var $insensitiveDays;

    /**
     * Prepare to display page.
     * 
     * @param string $tpl name of used template
     */
    function display($tpl = null)
    {
        /* prepare Joomla! framework objects */
        
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $this->user = &JFactory::getUser();
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        
        /* prepare component framework objects */
        
        $config = &AFactory::getConfig();
        /* @var $config BookingConfig */
        $this->templateHelper = &AFactory::getTemplateHelper();
        
        /* prepare models */
        
        $this->modelSubjects = new BookingModelSubjects();
        $this->modelSubject = new BookingModelSubject();
        
        $this->lists = array();
        
                
            $this->lists['limit'] = 2;
            $this->lists['limitstart'] = 0;
        
        
        if (IS_ADMIN) {
            
            $this->lists['state'] = ARequest::getUserStateFromRequest('filter_state', '', 'string');
            $this->lists['featured'] = ARequest::getUserStateFromRequest('filter_featured', '', 'string');
            
            $this->lists['order'] = ARequest::getUserStateFromRequest('filter_order', 'ordering', 'cmd');
            $this->lists['order_Dir'] = ARequest::getUserStateFromRequest('filter_order_Dir', 'ASC', 'word');
            
            $this->lists['search'] = ARequest::getUserStateFromRequest('search', '', 'string');
            
            $this->lists['parent'] = ARequest::getUserStateFromRequest('filter_parent', 0, 'int');
            $this->lists['filter_parent'] = BookingHelper::getParentsSubjectFilter($this->lists['parent']);
            
            $this->lists['template'] = ARequest::getUserStateFromRequest('filter_template', 0, 'int');
            $this->lists['filter_template'] = $this->templateHelper->getSelectBox('filter_template', 'SELECT_TEMPLATE', $this->lists['template'], true);
            
            $document->setTitle(JText::_('LIST_OF_OBJECTS'));
        
        } elseif (IS_SITE) {
            $this->lists['access'] = &AModel::getAccess();
            
            // request from search module
            $this->lists['featured'] = $mainframe->getUserStateFromRequest('booking_search_featured', 'featured');
            $this->lists['category'] = $mainframe->getUserStateFromRequest('booking_search_category', 'category');
            $this->lists['date_from'] = $mainframe->getUserStateFromRequest('booking_search_date_from', 'date_from');
            $this->lists['date_from'] = $mainframe->getUserStateFromRequest('booking_search_date_from', 'date_from');	
            $this->lists['date_to'] = $mainframe->getUserStateFromRequest('booking_search_date_to', 'date_to');
            $this->lists['date_type'] = $mainframe->getUserStateFromRequest('booking_search_date_type', 'date_type');
            $this->lists['price_from'] = $mainframe->getUserStateFromRequest('booking_search_price_from', 'price_from');
    		$this->lists['price_to'] = $mainframe->getUserStateFromRequest('booking_search_price_to', 'price_to');	
    		$this->lists['template_area'] = $mainframe->getUserStateFromRequest('booking_search_template_area', 'template_area');
            $this->lists['required_capacity'] = $mainframe->getUserStateFromRequest('booking_search_required_capacity', 'required_capacity');
            if ($mainframe->getUserStateFromRequest('booking_search', 'booking_search')) { // search module active
            	$this->lists['parent'] = null;
            } else { // or display subject's branch with selected parent
            	$this->lists['parent'] = JRequest::getInt('id');
            	$this->modelSubject->setId($this->lists['parent']);
            	$this->subject = clone $this->modelSubject->getObject();
            	$subjectId = $this->subject ? $this->subject->id : 0;
            }
            
            /* page metadata and bread crumbs */
            
            if ($this->lists['parent']) {
                
                if ($config->parentsBookable == 2) {
                    $mainframe->enqueueMessage(JText::_('SUBITEM_UNBOOKABLE_ALONE'), 'notice');
                    return;
                }
                
                $pathway = &JPathway::getInstance('site');
                /* @var $pathway JPathwaySite */
                /* set bread crumbs */
                foreach (($parents = array_reverse($this->modelSubjects->loadSubjectParentsLine($this->subject->id))) as $parent)
                	/* @var $parents array subject parents line */
                	$pathway->addItem($parent->title, JRoute::_(ARoute::view(VIEW_SUBJECTS, $parent->id, $parent->alias)));
                $pathway->addItem($this->subject->title, JRoute::_(ARoute::view(VIEW_SUBJECTS, $this->subject->id, $this->subject->alias)));
            }
            
            if ($config->displayFilter) {
                /* prepare subjects filter */
                
                $this->modelSubjects->init($this->lists);
                $templates = $this->modelSubjects->getAvailableTemplates();
                //add tepmplate from search if is not already inluded
                if ($this->lists['template_area'] && !in_array($this->lists['template_area'],$templates))
                	$templates[] = $this->lists['template_area'];

                if(is_array($templates)){
	                foreach ($templates as $template) {
	                    /* @var $item TableSubject */
	                	$this->templates[$template] = &$this->templateHelper->getTemplateById($template);
	                    $properties = new AParameter('', null, $this->templates[$template]->parser);
	                    $params = $properties->loadParamsToFields();
	                    foreach ($params as $param)
	                        if ($param[PARAM_FILTERABLES] == 1) {
	                            
	                            /* set request parameters*/
	                            $param[PARAM_REQUESTNAME] = ARequest::getPropertyName(isset($subjectId) ? $subjectId : '', $template, $param[PARAM_NAME]);
	                            $param[PARAM_REQUESTVALUE] = ARequest::getUserStateFromRequest($param[PARAM_REQUESTNAME], '', 'string', true);
	                            
	                            /* set filter for get subjects list from database */
	                            $this->lists['properties'][$template][$param[PARAM_NAME]] = array('type' => $param[PARAM_TYPE] , 'value' => $param[PARAM_REQUESTVALUE], 'comparison' => JArrayHelper::getValue($param, 'comparison'));
	                            
	                            if ($param[PARAM_TYPE] == 'list' || $param[PARAM_TYPE] == 'radio') {
	                                /* set options if parameter is radio button or selectbox */
	                                $node = &$param[PARAM_NODE];
	                                /* @var $node JSimpleXMLElement */
	                                if(is_object($node))
	                                foreach ($node->children() as $option)
	                                    /* @var $option JSimpleXMLElement */
	                                    $param[PARAM_OPTIONS][] = array(($value = $option->attributes()->value) , ATemplate::translateParam($value));
	                            }
	                            $this->filterables[$template][] = $param;
	                        }
	                }
                }
            }
            if ($this->subject)
            	AHtml::setMetaData($this->subject);
        }
        
        $this->modelSubjects->init($this->lists);
        $this->pagination = &$this->modelSubjects->getPagination();
        $this->items = &$this->modelSubjects->getData();
        $this->params = &JComponentHelper::getParams(OPTION);
        $this->subjectTable = clone $this->modelSubject->_table;
        
        if (IS_ADMIN) {
            $this->turnOnOrdering = ($this->lists['order'] == 'ordering');
            $this->selectable = JRequest::getString('task') == 'element';
            $this->type = JRequest::getString('type');
            $this->input = JRequest::getString('input');
            $this->tableTotal = $this->modelSubjects->getTableTotal();
        } elseif (IS_SITE) {
        	if ($config->subjectsCalendar) { 
            	BookingHelper::importDHTMLXCalendar(); 
                $this->months = array();
                
            	for ($cal = $config->subjectsCalendarStart; $cal < $config->subjectsCalendarStart + $config->subjectsCalendarDeep; $cal ++) {
            		$month = new stdClass();
            		// months timestamp
            		$month->tmstmp = mktime(0, 0, 0, (JHtml::date('now', 'n') + $cal), 1, JHtml::date('now', 'Y'));
            		// previou month timestamp
            		$month->lstMon = date('Y, n, 1, 0, 0, 0, 0', strtotime('- 1 month', $month->tmstmp));
            		// first day of month
            		$month->dayFir = mktime(0, 0, 0, date('m', $month->tmstmp), 1, date('Y', $month->tmstmp));
            		// last day of month
            		$month->dayLas = mktime(0, 0, 0, date('m', strtotime('+ 1 month', $month->tmstmp)), 0, date('Y', strtotime('+ 1 month', $month->tmstmp)));
            		// monday (sunday) of first month week (usualy owns it previous month)
            		$month->preMon = date('Y-m-d 00:00:00', strtotime($config->firstDaySunday ? 'previous sunday' : 'previous monday', $month->dayFir));
    				// sunday (monday) of last month week (usualy owns it next month)
            		$month->nexSun = date('Y-m-d 23:59:59', strtotime($config->firstDaySunday ? 'next monday' : 'next sunday', $month->dayLas));
    				$this->months[] = $month;
            	}
            	if (count($this->months)) {
                    foreach ($this->items as $item) {
                        if (!$item->children || $config->parentsBookable) {
                            $this->subjectTable->bind($item);
                            $days = BookingHelper::getCalendar($this->subjectTable, reset($this->months)->preMon, end($this->months)->nexSun);
                            foreach ($days->calendar as $day) {
                                /* @var $day BookingDay */
                                if ($day->engaged || !empty(reset($day->boxes)->closed)) {
                                    $this->insensitiveDays[$item->id][] = '"' . $day->date . '"';
                                }
                            }
                        }
                    }
                }
        	}
        }
        
        parent::display($tpl);
    }
}