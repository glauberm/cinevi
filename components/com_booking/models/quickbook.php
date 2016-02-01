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

jimport('joomla.application.component.model');

class BookingModelQuickbook extends JModelLegacy {

	public function getParent()
	{
		$query = $this->getDbo()->getQuery(true);
		$query->select('title')->from('#__booking_subject')->where('id = ' . JRequest::getInt('id'));
		return $this->getDbo()->setQuery($query)->loadObject();
	}

	public function getChildren()
	{
		$query = $this->getDbo()->getQuery(true);
		$query->select('id, title');
		$query->from('#__booking_subject');
		$query->where('parent = ' . JRequest::getInt('id'));
		$query->where('state = '.SUBJECT_STATE_PUBLISHED);
		$query->where('(publish_up <= ' . $query->quote(JFactory::getDate()->toSql()). ' OR publish_up = ' . $query->quote($this->getDbo()->getNullDate()).')');
		$query->where('(publish_down >= ' . $query->quote(JFactory::getDate()->toSql()). ' OR publish_down = ' . $query->quote($this->getDbo()->getNullDate()).')');
		$query->order('ordering');
		return $this->getDbo()->setQuery($query)->loadObjectList();
	}
	
	public function getSubject()
	{
		$subject = JTable::getInstance('Subject', 'Table');
		/* @var $subject TableSubject */
		$subject->load(JRequest::getInt('id'));
		return $subject;
	}
}