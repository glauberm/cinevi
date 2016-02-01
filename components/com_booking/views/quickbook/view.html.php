<?php 

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage		views
 * @copyright	  	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

jimport('joomla.application.component.view');



class BookingViewQuickbook extends JViewLegacy {

	public function display($tpl = null)
	{
		if ($this->getLayout() == 'date')
			$this->_date();
		elseif ($this->getLayout() == 'day')
			$this->_day();
		else
			$this->_default();
		parent::display();
	}

	private function _default()
	{
		JFactory::getApplication()->setUserState('com_booking.object.last', JURI::getInstance()->toString());
		$this->menu = JFactory::getApplication()->getMenu()->getActive();
		$this->parent = $this->get('parent');
		$this->children = $this->get('children');
	}

	private function _date()
	{
		AImporter::object('date', 'day', 'box', 'service');
		AImporter::model('reservationitems', 'reservationtypes', 'prices');
		$this->subject = $this->get('subject');
	}
	
	private function _day()
	{
		AImporter::object('date', 'day', 'box', 'service');
		AImporter::model('reservationitems', 'reservationtypes', 'prices');
		$this->subject = $this->get('subject');
	}
}