<?php

/**
 * Data object defines calendar day time box.
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @subpackage  objects
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class BookingTimeBox
{
    var $fromFloat = '';
    var $fromTime = '';
    var $fromDate = '';
    var $fromUts = '';
    var $fromDisplay = '';
    var $toFloat = '';
    var $toTime = '';
    var $toDate = '';
    var $toUts = '';
    var $toDisplay = '';
    var $services = array();
    var $rtype = '';
    var $price = '';
    //var $cancel_time = '';
    var $deposit = '';
    var $haveDailyService = '';
    var $closed = false;
    var $closignDayTitle = '';
    var $closignDayText  = '';
}

?>