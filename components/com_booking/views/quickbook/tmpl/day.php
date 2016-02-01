<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	views
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

ob_clean();

$this->days = &BookingHelper::getDailyCalendar($this->subject, ($this->setting = new BookingCalendarSetting()), false);

$day = reset($this->days->calendar);
/* @var $day BookingDay */

$option = 20; // number 20 should be configurable by user
	
$j = 1;
foreach ($day->boxes as $i => $box) {
	if (($j % 20) == 1) {
		if ($j != 1) {
			echo '</ul>';
		}
		echo '<ul class="pagenav">';
	}
	/* @var $box BookingTimeBox */
	$service = reset($box->services);
	/* @var $service BookingService */
	if ($service->canReserve && !$box->closed) {
		echo '<li><a href="javascript:QuickBook.book(\''.$service->id.'\', \''.$service->idShort.'\');" id="'.$service->idShort.'">'.BookingHelper::displayTime($box->fromTime).'</a></li>';	
	} elseif ($box->closed) {
        $style = $box->closignDayColor ? 'style="background-color: #'.$box->closignDayColor.'"' : '';
		echo '<li class="hasTip" title="'.$this->escape($box->closingDayTitle).'::'.$this->escape($box->closignDayText).'"><a '.$style.'>'.BookingHelper::displayTime($box->fromTime).($box->closignDayShow ? '<br>'.$this->escape($box->closingDayTitle) : '').'</a></li>';
	} else {
		echo '<li><span>'.BookingHelper::displayTime($box->fromTime).'</span></li>'; 
	}
	$j++;
}
	
die();