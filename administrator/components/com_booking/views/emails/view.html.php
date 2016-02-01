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

class BookingViewEmails extends JViewLegacy
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
		JToolBarHelper::title(JText::_('EMAIL_TEMPLATES'), 'email');
		JToolbarHelper::addNew('email.add');
		JToolbarHelper::editList('email.edit');
		JToolbarHelper::deleteList('', 'emails.delete');
		JToolBarHelper::divider();
		if (JFactory::getUser()->authorise('core.admin', 'com_booking'))
			JToolBarHelper::preferences('com_booking');
	}
}
