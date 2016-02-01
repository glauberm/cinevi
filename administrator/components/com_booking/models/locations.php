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

class BookingModelLocations extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'l.id',
					'l.title',
					'l.pick_up',
					'l.drop_off'
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
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string'));
		$this->setState('filter.pick_up', $this->getUserStateFromRequest($this->context.'.filter.pick_up', 'filter_pick_up', '', 'string'));
		$this->setState('filter.drop_off', $this->getUserStateFromRequest($this->context.'.filter.drop_off', 'filter_drop_off', '', 'string'));
		parent::populateState('id', 'asc');
	}

	/**
	 * (non-PHPdoc)
	 * @see JModelList::getListQuery()
	 */
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('l.id, l.title, l.pick_up, l.drop_off, l.checked_out, l.checked_out_time')->from('#__booking_location AS l');
		$query->select('u.name AS editor')->join('LEFT', '#__users AS u ON u.id = l.checked_out');

		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->quote('%'.JString::strtolower($search).'%');
			$query->where("LOWER(l.title) LIKE $search");				
		}
		
		$pick_up = $this->getState('filter.pick_up');
		if ($pick_up !== '')
			$query->where('l.pick_up = ' . (int) $pick_up);
		
		$drop_off = $this->getState('filter.drop_off');
		if ($drop_off !== '')
			$query->where('l.drop_off = ' . (int) $drop_off);
		
		$query->order($db->escape($this->state->get('list.ordering', 'l.title').' '.$this->state->get('list.direction', 'asc')));
		
		return $query;
	}
	
	public function getPickUp()
	{
		$query = $this->getDbo()->getQuery(true);
		$query->select('id, title')->from('#__booking_location')->where('pick_up = 1')->order('title');
		$items = $this->getDbo()->setQuery($query)->loadObjectList();
		$pickUp = array();
		foreach ($items as $item)
			$pickUp[] = $item->title;
		return $pickUp;
	}
	
	public function getDropOff()
	{
		$query = $this->getDbo()->getQuery(true);
		$query->select('id, title')->from('#__booking_location')->where('drop_off = 1')->order('title');
		$items = $this->getDbo()->setQuery($query)->loadObjectList();
		$dropOff = array();
		foreach ($items as $item)
			$dropOff[] = $item->title;
		return $dropOff;
	}
}