<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	elements
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class JFormFieldBookingCalendar extends JFormField
{

	var $type = 'BookingCalendar';

	private $_timeselect;
	
	/**
	 * (non-PHPdoc)
	 * @see JFormField::getInput()
	 */
	protected function getInput()
	{
		$this->_timeselect = (string) $this->element['timeselect'] == 'true';
		
		$formatShow = $this->_timeselect ? ADATE_FORMAT_LONG : ADATE_FORMAT_NORMAL;
		$formatData = $this->_timeselect ? ADATE_FORMAT_LONG_CAL : ADATE_FORMAT_NORMAL_CAL;
		
		return AHtml::getCalendar($this->value, $this->name, $this->id, $formatShow, $formatData, '', $this->_timeselect);
	}
}