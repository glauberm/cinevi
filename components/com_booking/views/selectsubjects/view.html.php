<?php

/**
 * View subject detail.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

AImporter::importView("subject");


class BookingViewSelectsubjects extends BookingViewSubject
{
	function display($tpl = null)
	{
		if(! $templateid = JRequest::getInt('templateid'))
			$templateid = JRequest::getInt('id');
		//var_dump($templateid);
		if($templateid)
		{
			$db = JFactory::getDbo();
			$query = 'SELECT `id` FROM `#__booking_subject` WHERE `template` ='.$templateid;
			$db->setQuery($query);
			$data = $db->loadObjectList();
			$first = reset($data);
			//var_dump($first->id);
			if($first)
				JRequest::setVar('id',$first->id);
			
			JRequest::setVar('templateid',$templateid);
			$this->setting = new BookingCalendarSetting();
		}
		parent::display($tpl);
	}
}


?>