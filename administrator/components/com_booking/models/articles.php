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

class BookingModelArticles extends JModelList
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
	 * @see JModelList::getListQuery()
	 */
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id, a.title, a.checked_out, a.checked_out_time')->from('#__booking_article AS a');
		$query->select('u.name AS editor')->join('LEFT', '#__users AS u ON u.id = a.checked_out');

		$query->order($db->escape($this->state->get('list.ordering', 'a.title').' '.$this->state->get('list.direction', 'asc')));
		
		return $query;
	}
}