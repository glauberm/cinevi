<?php

/**
 * Reservation table object.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  tables 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class TableReservationItems extends JTable
{
    /**
     * Primary key
     * 
     * @var int
     */
    var $id;
    /**
     * ID of reservation item.
     * 
     * @var int
     */
    var $reservation_id;
    /**
     * Use Reservation type constant (daily,hourly ...)
     * 
     * @var int
     */
    var $rtype;
    /**
     * Rezerved subject ID
     * 
     * @var int
     */
    var $subject;
    /**
     * Reserved subject title
     * 
     * @var string
     */
    var $subject_title;
    /**
     * Reserved sub subject ID
     * 
     * @var int
     */
    var $sub_subject;
    /**
     * Reserved sub subject title
     * 
     * @var string
     */    
    var $sub_subject_title;
    /**
     * Rezervation valid from
     * 
     * @var string MySQL datetime
     */
    var $from;
    /**
     * Rezervation valid to
     * 
     * @var string MySQL Datetime
     */
    var $to;
    /**
     * Rezervation capacity
     * 
     * @var int
     */
    var $capacity;
    
    /**
     * Next customers per capacity or occupancy
     * 
     * @var string 
     */
    var $more_names;
    
    /**
     * Rezervation occupancy
     *
     * @var int
     */
    var $occupancy;
    
    /**
     * Price for single capacity in timeline.
     * 
     * @var float
     */
    var $price;
    /**
     * Price deposit
     * 
     * @var float
     */
    var $deposit;
    /**
     * Full price multiplied by capacity (if set so)
     * 
     * @var float
     */
    var $fullPrice;
    /**
     *  Full reservation price with supplements.
     * 
     * @var float
     */
    var $fullPriceSupplements;
    /**
     * Agent provision
     * 
     * @var float
     */
    var $provision;
    /**
     *  Full deposit multiplied by capacity (if set so)
     * 
     * @var float
     */
    var $fullDeposit;
    /**
     * Tax value in percent.
     * 
     * @var float
     */
    var $tax;
    /**
     * Time having to pay from order date
     *
     * @var int
     */
    var $cancel_time;
    /**
     * Message to calendar
     *
     * @var string
     */
    var $message;
    /**
     * Time up in period timeframe
     * @var time
     */
    var $period_time_up;
    /**
     * Time down in period timeframe
     * @var time
     */
    var $period_time_down;
    /**
     * Daily, Weekly, Monthly, Yearly.
     * Value of constants: PERIOD_TYPE_DAILY, PERIOD_TYPE_WEEKLY, PERIOD_TYPE_MONTHLY, PERIOD_TYPE_YEARLY 
     * @var int
     */
    var $period_type;
    /**
     * Recurrence in period type
     * EQ recurrence every # week
     * @var int
     */
    var $period_recurrence;
    /**
     * Period is valid for Monday
     * @var bool
     */
    var $period_monday;
    /**
     * Period is valid for Tuesday
     * @var bool
     */
    var $period_tuesday;
    /**
     * Period is valid for Wednesday
     * @var bool
     */
    var $period_wednesday;
    /**
     * Period is valid for Thursday
     * @var bool
     */
    var $period_thursday;
    /**
     * Period is valid for Friday
     * @var bool
     */
    var $period_friday;
    /**
     * Period is valid for Saturday
     * @var bool
     */
    var $period_saturday;
    /**
     * Period is valid for Sunday
     * @var bool
     */
    var $period_sunday; 
    /**
     * Recurrence in one year month (month number 1-12) 
     * @var int
     */
    var $period_month;
    /**
     * Recurrence in one month week (week number 1-4)
     * @var int
     */
    var $period_week;
    /**
     * Recurrence in one week day (day number 1-7)
     * @var int
     */
    var $period_day;
    /**
     * Period end type (no end, end after # occurrences, end in date)
     * Value of constants: PERIOD_END_TYPE_NO, PERIOD_END_TYPE_AFTER, PERIOD_END_TYPE_DATE 
     * @var int
     */   
    var $period_end;
    /**
     * Number of occurrences to end period (with period end after occurrences #)
     * @var int
     */
    var $period_occurrences;
    /**
     * Date up of period
     * @var date
     */
    var $period_date_up;
    /**
     * Date down of period (with period end in date)
     * @var date
     */
    var $period_date_down;
    /**
     * Total number of occurrences computed according settings.
     * @var int
     */
    var $period_total;
    
    var $period;
    
    /**
     * Construct object.
     * 
     * @param JDatabaseMySQL $db database connector
     */
    public function __construct(& $db)
    {
        parent::__construct('#__' . PREFIX . '_reservation_items', 'id', $db);
    }

    /**
     * Init empty object
     * 
     */
    public function init()
    {
        $this->id = 0;
        $this->reservation_id = 0;
        $this->rtype = 0;
        $this->subject = 0;
        $this->subject_title = '';
        $this->fax = '';
        $this->from = '';
        $this->to = '';
        $this->capacity = 0;
        $this->occupancy = array();
        $this->price = 0;
        $this->deposit = 0;
        $this->fullPrice = 0;
        $this->fullDeposit = 0;
        $this->cancel_time = '';
        $this->message = '';
    }

    /**
     * Clean object data.
     */
    public function clean()
    {
        $this->capacity = $this->capacity ? $this->capacity : '';
        $this->price = ($this->price != 0.0 && $this->price != 0) ? $this->price : '';
        $this->deposit = ($this->deposit != 0.0 && $this->deposit != 0) ? $this->deposit : '';
    }

    /**
     * Reservation full price + supplements prices (if set, multiplied by capacity).
     * 
     * @param array $supplements
     * @return void
     */
    /*
    public function setFullPriceSupplements(&$supplements)
    {
    	$this->fullPriceSupplements = $this->fullPrice;
    	
        foreach ($supplements as $supplement){
        	$this->fullPriceSupplements += $supplement->price * ($supplement->capacity_multiply ? $this->capacity : 1);
        }
    }
    */

    public function bind($data, $ignore = array())
    {
        if (($result = parent::bind($data))) {
        	$this->id = (int) $this->id;
            $this->reservation_id = (int) $this->reservation_id;
            $this->rtype = (int) $this->rtype;
            $this->subject = (int) $this->subject;
            $this->subject_title = JString::trim($this->subject_title);
            $this->from = JString::trim($this->from);
            $this->to = JString::trim($this->to);
            $this->capacity = (int) $this->capacity;
            $this->more_names = is_string($this->more_names) ? json_decode($this->more_names) : json_encode($this->more_names);
            $this->price = (float) $this->price;
            $this->deposit = (float) $this->deposit;
            $this->fullPrice = (float) $this->fullPrice;
            $this->fullDeposit = (float) $this->fullDeposit;
            $this->message = JString::trim($this->message);
        }
        return $result;
    }
    
    public function store($updateNulls = false) {    	
    	if (isset($this->period)) {
    		$period = $this->period;
    		unset($this->period); // we save it into another table
    	}
    	$this->occupancy = new JRegistry($this->occupancy);
    	$this->occupancy = $this->occupancy->toString();
    	parent::store($updateNulls);
    	if (isset($period)) {
    		$reservationperiod = JTable::getInstance('ReservationPeriod', 'Table');
    		/* @var $reservationperiod TableReservationPeriod */
    		$reservationperiod->reservation_item_id = $this->id;
    		foreach ($period as $item) {
    			$reservationperiod->id = null;
    			$reservationperiod->from = date('Y-m-d', $item) . ' ' . $this->period_time_up;
    			$reservationperiod->to = date('Y-m-d', $item) . ' ' . $this->period_time_down;
    			$reservationperiod->store();
    		}
    	}
    	if (!isset($this->period)) // prepare for the next item
    		$this->period = array();
    	return true;
    }

    /**
     * Check object data before saving.
     * 
     * @return boolean
     */
    public function check()
    {
        if (($result = parent::check())) {
            $this->from = AModel::datetime2save($this->from);
            $this->to = AModel::datetime2save($this->to);
        }
        
        return $result;
    }
    
    /**
     * @param TableReservationItems $object
     */
    public function display(&$object)
    {
    	$occupancy = new JRegistry($object->occupancy);
    	$object->occupancy = $occupancy->toArray();
    }
    
    /**
     * Bind reservation item from booking interval and subject
     * 
     * @param TableReservationItems $item
     * @param BookingInterval $box
     * @param TableSubject $subject
     */
    public static function bindFromBox(&$item, &$box, &$subject) {
        $item->from = $box->from;
        $item->to = $box->to;
        $item->rtype = $box->rtype;
        $item->price = $box->price;
		$item->cancel_time = $box->cancel_time;
        $item->deposit = $box->deposit;
        $item->fullPrice = $box->fullPrice;
        $item->fullPriceSupplements = $box->fullPriceSupplements;
        $item->provision = $box->provision;
        $item->fullDeposit = $box->fullDeposit;
        $item->tax = $subject->tax;
        $item->subject = $subject->id;
        $item->subject_title = $subject->title;
        $item->occupancy = $box->occupancy;
    }
}
?>