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

jimport('joomla.application.component.modellist');

class BookingModelClosingdays extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'a.id',
					'a.title'
			);
		}

		parent::__construct($config);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search'));
		parent::populateState('id', 'asc');
	}

	/**
	 * (non-PHPdoc)
	 * @see JModelList::getListQuery()
	 */
	protected function getListQuery()
	{
		$user   = JFactory::getUser();
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('id')->from('#__booking_subject');
		$sid = array(0);
		foreach ($db->setQuery($query)->loadColumn() as $id)
			if ($user->authorise('booking.closingdays.manage', 'com_booking.subject.'.$id))
				$sid[] = $id;
				
		$query	= $db->getQuery(true);

		$query->select('c.*, u.name AS editor')->from('#__booking_closingday AS c');
		$query->join('LEFT', '#__users AS u ON u.id = c.checked_out');
		$query->join('LEFT', '#__booking_closingday_subject AS cs ON cs.closingday_id = c.id');
		$query->join('LEFT', '#__booking_subject AS s ON s.id = cs.subject_id');

		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->quote('%'.JString::strtolower($search).'%');
			$query->where("(LOWER(c.title) LIKE $search OR LOWER(c.text) LIKE $search)");
		}
		
		$query->where('s.id IN ('.implode(', ', $sid) . ')');
        if (IS_SITE)
            $query->where('created_by = ' . $db->quote($user->get('id')));
		$query->group('c.id');
		$query->order($db->escape($this->state->get('list.ordering', 'c.title').' '.$this->state->get('list.direction', 'asc')));
		
		return $query;
	}
	
	/**
	 * Get all closing days which affected subject.
	 * @param int $subjectId
	 * @return array
	 */
	public function getSubjectClosingDays($subjectId)
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user   = JFactory::getUser();
		
		$query->select('c.title, c.text, c.color, c.show, c.date_up, c.date_down, c.monday, c.tuesday, c.wednesday, c.thursday, c.friday, c.saturday, c.sunday, c.time_up, c.time_down');
		$query->from('#__booking_closingday AS c');
		$query->join('', '#__booking_closingday_subject AS s ON s.closingday_id = c.id');
		$query->where('subject_id = ' . (int) $subjectId);
		
		$cDays = $db->setQuery($query)->loadObjectList();
		
		foreach ($cDays as $cDay) {
            $cDay->tUp = JHtml::date($cDay->time_up, 'H:i');
            $cDay->tDown = JHtml::date($cDay->time_down, 'H:i');
            $cDay->up = $cDay->date_up . ' ' . JHtml::date($cDay->time_up, 'H:i:s');
            if ($cDay->tDown == '00:00') {
                $cDay->tDown = '24:00';
                $cDay->down = $cDay->date_down . ' 24:00:00';
            } else {
                $cDay->down = $cDay->date_down . ' ' . JHtml::date($cDay->time_down, 'H:i:s');
            }
		}
			
		return $cDays;
	}
}