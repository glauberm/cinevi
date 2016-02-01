<?php

/**
 * @package        ARTIO Booking
 * @subpackage		views
 * @copyright	  	Copyright (C) 2014 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */
defined('_JEXEC') or die;

class BookingViewOverview extends JViewLegacy {

    /**
     * Item parents which children have hourly reservation type.
     * @var array 
     */
    public $hourlyParents;

    /**
     * Current parent.
     * @var stdClass 
     */
    public $current;

    /**
     * Current parent children.
     * @var array 
     */
    public $children;

    /**
     * Schedule of hourly overview
     * @var array 
     */
    public $hourlySchedule;

    /**
     * Reserved reservations for current day and current family.
     * @var array 
     */
    public $hourlyReservations;

    /**
     * Update view via Ajax.
     * @var boolean 
     */
    public $ajax;

    /**
     * Main page header.
     * @var string 
     */
    public $h1;

    /**
     * Main page CSS class.
     * @var string 
     */
    public $css;

    /**
     * Show single week, otherwise show single day.
     * @var boolean 
     */
    public $singleWeek;

    /**
     * Days of current week.
     * @var array 
     */
    public $weekSchedule;

    public function display($tpl = null) {
        $app = JFactory::getApplication();

        $this->singleWeek = $this->getLayout() == 'week';
        $this->getModel()->setState('week', $this->singleWeek);
        $this->current = $this->get('HourlyCurrent');
        $this->hourlyParents = $this->get('HourlyParents');
        $this->navigator = $this->get('DayNavigator');
        $this->children = $this->get('Children');
        $this->hourlySchedule = $this->get('HourlySchedule');
        $this->hourlyReservations = $this->get('HourlyReservations');
        $this->params = $this->get('Params');

        if ($this->params->get('show_page_heading', 1)) {
            $active = $app->getMenu()->getActive();
            $this->h1 = $this->params->get('page_heading', ($active ? $active->title : JText::_('RESERVATIONS_OVERVIEW')));
        }
        $this->css = $this->params->get('pageclass_sfx');

        if ($this->singleWeek) {
            $this->weekSchedule = $this->get('WeekSchedule');
        }

        AImporter::css('overview');
        AImporter::js('overview');
        ISJ3 ? JHtml::_('bootstrap.tooltip') : JHtml::_('behavior.tooltip', '.hasTooltip');

        $this->ajax = $app->input->get('ajax');

        $juri = JURI::getInstance();
        $juri->setVar('layout', 'default');
        $this->dayRoute = JRoute::_('index.php?option=com_booking&view=overview&layout=default&Itemid=' . JRequest::getInt('Itemid') . '#bookingOverview');
        $juri->setVar('layout', 'week');
        $this->weekRoute = JRoute::_('index.php?option=com_booking&view=overview&layout=week&Itemid=' . JRequest::getInt('Itemid') . '#bookingOverview');

        parent::display($tpl);

        if ($this->ajax) {
            die();
        }
    }

}
