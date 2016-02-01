<?php

/**
 * Data object defines calendar day time box item.
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

class BookingService
{
    var $id = '';
    var $price = '';
    var $cancel_time = '';
    var $deposit = '';
    var $color = '';
    /*
    var $depositMultiply = '';
    var $priceCapacityMultiply = '';
    var $depositCapacityMultiply = '';
    */
    var $formatPrice = '';
    var $capacityUnit = '';
    var $rtype = '';
    var $boxes = '';
    var $priceId = '';
    var $priceIndex = '';
    var $fromFloat = '';
    var $fromDisplay = '';
    var $fromTime = '';
    var $fromDate = '';
    var $fromUts = '';
    var $toFloat = '';
    var $toDisplay = '';
    var $toTime = '';
    var $toDate = '';
    var $toUts = '';
    var $canReserve = '';
    var $rtypeId = '';
    /**
     * Number of already reserved items during that service.
     * @var int
     */
    var $alreadyReserved = '';
    /**
     * Minimum reservation limit allowed in reservation type 
     * @var int
     */
    var $min = '';
    /**
     * Maxinum reservation limit allowed in reservation type 
     * @var int
     */
    var $max = '';
    /**
     * Fixed length of reservation. Replaces max/min.
     * @var int
     */
    var $fix;
    /**
     * Fixed limit from concrete day of week, eq 7 days from monday.
     * @var string
     */
    var $fixFrom;
    /**
     * Short code of day od week.
     * @var string mon, tue, wed etc.
     */
    var $dayWeek;
    var $timeRange;
    /**
     * Service not begins fixed limit
     * @var int
     */
    var $notBeginsFixLimit;
    var $headPiece;
    var $tailPiece;
    var $noContinue = false;
    var $beforeFuture = false;
    var $inCart = false;
}

?>