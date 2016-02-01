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

class BookingModelEmails extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'e.id',
					'e.subject'
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
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('e.id, e.subject, e.usage, e.checked_out, e.checked_out_time')->from('#__booking_email AS e');
		$query->select('u.name AS editor')->join('LEFT', '#__users AS u ON u.id = e.checked_out');

		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->quote('%'.JString::strtolower($search).'%');
			$query->where("(LOWER(e.subject) LIKE $search OR LOWER(e.body) LIKE $search)");				
		}

		$query->order($db->escape($this->state->get('list.ordering', 'e.subject').' '.$this->state->get('list.direction', 'asc')));
		
		return $query;
	}
}