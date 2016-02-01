<?php

/**
 * Data object defines calendar day.
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

class BookingDay
{
    var $date;
    var $Uts;
    var $weekDayCode;
    var $weekDayString;
    var $boxes;
    /**
     * Number of reserved items during whole day by daily reservation types
     * @var int
     */
    var $fullReserved;
    /**
     * Maximal number of reserved items during that day by hour reservation type
     * @var int
     */
    var $maxHoursReserved;
    
    var $closed;
    var $closingDayTitle;
    var $closignDayText;
    /**
     * Hexa number
     * @var string
     */
    var $closignDayColor;
    /**
     * Show as tip or label
     * @var bool
     */
    var $closignDayShow;

    function __construct()
    {
        $this->date = '';
        $this->Uts = '';
        $this->weekDayCode = '';
        $this->weekDayString = '';
        $this->boxes = array();
        $this->maxHoursReserved = 0;
        $this->fullReserved = 0; 
        $this->closed = false;
        $this->closingDayTitle = '';
        $this->closignDayText = '';
        $this->closignDayColor = '';
        $this->closignDayShow = 0;
    }
}

?>