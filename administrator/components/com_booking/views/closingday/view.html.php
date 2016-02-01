<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	views
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die;

class BookingViewClosingday extends JViewLegacy
{
	/**
	 * @var JForm
	 */
	public $form;
	public $item;
	public $state;

	/**
	 * (non-PHPdoc)
	 * @see JViewLegacy::display()
	 */
	public function display($tpl = null)
	{
		BookingHelper::importTimePicker();
		JFactory::getDocument()->addScript(JURI::root() . 'administrator/components/com_booking/assets/colorpicker/jscolor.js');
		JForm::addFormPath(JPath::clean(ADMIN_ROOT . '/models/forms'));
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		$this->subjects = $this->get('AffectedSubjects');
		$this->dateUp   = $this->form->getField('date_up');
		$this->dateDown = $this->form->getField('date_down');
		$this->timeUp   = $this->form->getField('time_up');
		$this->timeDown = $this->form->getField('time_down');
		if (IS_ADMIN)
		    $this->addToolbar();
		$this->params = new JRegistry();
		$menuitem = JFactory::getApplication()->getMenu()->getActive();
		if ($menuitem)
		    $this->params->merge($menuitem->params);
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		JToolBarHelper::title(JText::_('EDIT_CLOSING_DAY'), 'closingday');
		JToolBarHelper::apply('closingday.apply');
		JToolBarHelper::save('closingday.save');
		JToolBarHelper::cancel('closingday.cancel');
	}
}