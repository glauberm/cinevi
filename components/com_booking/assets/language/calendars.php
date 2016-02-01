<?php

/**
 * Language constants for site calendars
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  assets 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

$languages['LGSelectCheckInAndCheckOut'] = 'SELECT_CHECK_IN_AND_CHECK_OUT';
$languages['LGSelectReservation'] = 'SELECT_RESERVATION';
$languages['LGNoContinuousInterval'] = 'YOUR_SELECTED_INTERVAL_IS_NO_CONTINUOUS';
$languages['LGSelectRealInterval'] = 'SELECT_REAL_CHECK_INTERVAL';
$languages['LGFixedLimitError'] = 'FIXED_LIMIT_ERROR';
$languages['LGMinimumLimitUnderflow'] = 'MINIMUM_LIMIT_UNDERFLOW';
$languages['LGMaximumLimitOverflow'] = 'MAXIMUM_LIMIT_OVERFLOW';
$languages['LGFixFromMon'] = 'FIX_FROM_MON';
$languages['LGFixFromTue'] = 'FIX_FROM_TUE';
$languages['LGFixFromWed'] = 'FIX_FROM_WED';
$languages['LGFixFromThu'] = 'FIX_FROM_THU';
$languages['LGFixFromFri'] = 'FIX_FROM_FRI';
$languages['LGFixFromSat'] = 'FIX_FROM_SAT';
$languages['LGFixFromSun'] = 'FIX_FROM_SUN';
$languages['LGUnBookAbleInterval'] = 'UNBOOKABLE_INTERVAL';
$languages['LGSelectOccupancy'] = 'SELECT_OCCUPANCY';
$languages['LGSelectNightInterval'] = 'SELECT_NIGHT_BOOKING';

$document = &JFactory::getDocument();
/* @var $document JDocument */

$document->addScriptDeclaration('	var CheckOpIn = ' . CHECK_OP_IN . ';');
$document->addScriptDeclaration('	var CheckOpOut = ' . CHECK_OP_OUT . ';');
$document->addScriptDeclaration('	var CheckOpNext = ' . CHECK_OP_NEXT . ';');
$document->addScriptDeclaration('	var ReservationDaily = ' . RESERVATION_TYPE_DAILY . ';');
$document->addScriptDeclaration('	var ReservationHourly = ' . RESERVATION_TYPE_HOURLY . ';');

AImporter::helper('document');
ADocument::addLGScriptDeclaration($languages);

?>