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

class BookingModelClosingday extends JModelAdmin
{
	/**
	 * (non-PHPdoc)
	 * @see JModelForm::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
	    JForm::addFormPath(JPath::clean(ADMIN_ROOT.'/models/forms'));
		$form = $this->loadForm('com_booking.closingday', 'closingday', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_booking.edit.closingday.data', array());
		if (empty($data))
			$data = $this->getItem();
		return $data;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see JModelAdmin::save()
	 */
	public function save($data)
	{
		$success = parent::save($data);
		if ($success) {
			$id = $this->getState('closingday.id');
			$items = JRequest::getVar('item', array(), 'default', 'array');		
		
			$query = $this->getDbo()->getQuery(true);
			$query->delete('#__booking_closingday_subject')->where('closingday_id = ' . (int) $id);
			$this->getDbo()->setQuery($query)->query();		
		
			foreach ($items as $item) {
				$query = $this->getDbo()->getQuery(true);
				$query->insert('#__booking_closingday_subject')->columns('closingday_id, subject_id')->values((int)$id . ', ' . (int)$item);
				$this->getDbo()->setQuery($query)->query();
			}
			
			if (empty($items)) {
                $this->setError(JText::_('SELECT_AFFECTED_ITEMS'));
                $success = false;   
			}
		}
		
		return $success;
	}
	
	public function getAffectedSubjects()
	{
		$user = JFactory::getUser();
		$query = $this->getDbo()->getQuery(true);
		$query->select('subject_id')->from('#__booking_closingday_subject')->where('closingday_id = ' . (int) $this->getItem()->id);
		$affected = $this->getDbo()->setQuery($query)->loadColumn();
		
		$model = JModelLegacy::getInstance('Subjects', 'BookingModel');
		/* @var $model BookingModelSubjects */
		$subjects = $model->init(array('state' => SUBJECT_STATE_PUBLISHED))->getData(true);
		
		foreach ($subjects as $i => $subject) {
			if ($user->authorise('booking.closingdays.manage', 'com_booking.subject.'.$subject->id))
				$subject->affected = in_array($subject->id, $affected);
			else
				unset($subjects[$i]);
		}
		
		$subjects = array_merge($subjects);
		
		return $subjects;
	}
}