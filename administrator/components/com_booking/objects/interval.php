<?php

/**
 * Data object defines calendar days interval.
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

class BookingInterval
{
    var $canReserve = null;
    var $calendar = null;
    var $price = null;
    var $cancel_time = null;
    var $fullPrice = null;
    var $fullPriceSupplements = null;
    var $discount = null;
    var $fullDiscount = null;
    /**
     * Full price of supplements
     * @var float
     */
    var $supplementsFullPrice = null;
    var $deposit = null;
    var $fullDeposit = null;
    var $from = null;
    var $to = null;
    var $fromUts = null;
    var $toUts = null;
    var $rtype = null;
    /**
     * Maximal number of subject reservations during that interval. (highest limit for new reservation)
     * @var int
     */
    var $maxReserved = null;
    var $occupancy = null;
    /**
     * Minimal number of subject reservations during that interval.
     * @var int
     */
    var $minReserved = null;
    var $error = '';
    var $provision = null;
    
    function __construct()
    {
        $this->canReserve = true;
        $this->calendar = array();
        $this->price = 0;
        $this->cancel_time = '';
        $this->fullPrice = 0;
        $this->fullPriceSupplements = 0;
        $this->deposit = 0;
        $this->fullDeposit = 0;
        $this->discount = 0;
        $this->fullDiscount = 0;
        $this->supplementsFullPrice = 0;
        $this->maxReserved = 0;
        $this->minReserved = null; //null != 0 !!! must be initially null, because 0 means no reservation
        $this->occupancy = array();
        $this->error = '';
        $this->provision = 0;
    }

    function setDate($from, $to)
    {
        $from = BookingHelper::convertDate($from);
        $to = BookingHelper::convertDate($to);
        $this->from = $from->dts;
        $this->fromUts = $from->uts;
        $this->to = $to->dts;
        $this->toUts = $to->uts;
    }
}

?>