<?php

/**
 * @version	$Id$
 * @package	ARTIO Booking
 * @subpackage	elements
 * @copyright	Copyright (C) 2012 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license   	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class JFormFieldGoogleCalendarList extends JFormFieldList {
	
	var $type = 'GoogleCalendarList';
	
	/**
	 * (non-PHPdoc)
	 * @see JFormFieldList::getOptions()
	 */
	protected function getOptions()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('id, title')
		      ->from('#__booking_google_calendar')
		      ->order('title');
		
		$calendarList = $db->setQuery($query)->loadObjectList();
		
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('SELECT_GOOGLE_CALENDAR'));
		
		foreach ($calendarList as $calendar)
			$options[] = JHtml::_('select.option', $calendar->id, $calendar->title);
		
		return $options;
	}
}