<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage 	models
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class BookingModelLocation extends JModelAdmin
{
	/**
	 * (non-PHPdoc)
	 * @see JModelForm::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_booking.location', 'location', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
			return false;
		return $form;
	}

	/**
	 * (non-PHPdoc)
	 * @see JModelForm::loadFormData()
	 */
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_booking.edit.location.data', array());
		if (empty($data))
			$data = $this->getItem();
		return $data;
	}
	
	public function pick_up_publish($cid) {
		$query = $this->getDbo()->getQuery(true);
		$query->update('#__booking_location')->set('pick_up = 1')->where('id IN (' . implode(', ', $cid) . ')');
		return $this->getDbo()->setQuery($query)->query();
	}
	
	public function pick_up_unpublish($cid) {
		$query = $this->getDbo()->getQuery(true);
		$query->update('#__booking_location')->set('pick_up = 0')->where('id IN (' . implode(', ', $cid) . ')');
		return $this->getDbo()->setQuery($query)->query();
	}
	
	public function drop_off_publish($cid) {
		$query = $this->getDbo()->getQuery(true);
		$query->update('#__booking_location')->set('drop_off = 1')->where('id IN (' . implode(', ', $cid) . ')');
		return $this->getDbo()->setQuery($query)->query();
	}
	
	public function drop_off_unpublish($cid) {
		$query = $this->getDbo()->getQuery(true);
		$query->update('#__booking_location')->set('drop_off = 0')->where('id IN (' . implode(', ', $cid) . ')');
		return $this->getDbo()->setQuery($query)->query();
	}
}