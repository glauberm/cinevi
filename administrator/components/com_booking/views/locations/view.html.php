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

class BookingViewLocations extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * (non-PHPdoc)
	 * @see JViewLegacy::display()
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->addToolbar();
		BookingHelper::setSubmenu('');
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('LOCATIONS'), 'location');
		JToolbarHelper::addNew('location.add');
		JToolbarHelper::editList('location.edit');
		JToolbarHelper::divider();
		JToolbarHelper::publish('locations.pick_up_publish', 'Pick Up', true);
		JToolbarHelper::unpublish('locations.pick_up_unpublish', 'Not Pick Up', true);
		JToolbarHelper::publish('locations.drop_off_publish', 'Drop Off', true);
		JToolbarHelper::unpublish('locations.drop_off_unpublish', 'Not Drop Off', true);
		JToolbarHelper::divider();
		JToolbarHelper::deleteList('', 'locations.delete');
		JToolBarHelper::divider();
		if (JFactory::getUser()->authorise('core.admin', 'com_booking'))
			JToolBarHelper::preferences('com_booking');
	}
}
