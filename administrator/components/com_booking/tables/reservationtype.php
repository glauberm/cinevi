<?php

/**
 * Reservation type
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

class TableReservationType extends JTable
{
    
    /**
     * Primary key
     *
     * @var int
     */
    var $id;
    
    /**
     * Owner
     * 
     * @var int
     */
    var $subject;
    
    /**
     * Reservation type name
     *
     * @var string
     */
    var $title;
    
    /**
     * Reservation type
     * 
     * @var int
     */
    var $type;
    
    /**
     * Info description
     *
     * @var string
     */
    var $description;
    
    /**
     * Min. reserving capacity
     *
     * @var int
     */
    var $capacity_unit;
    
    /**
     * Min. time reserving
     *
     * @var string (MySQL TIME)
     */
    var $time_unit;
    
    /**
     * Break between reservations. Fpr example: time for clean-up room for next quest.
     *
     * @var string (MySQL TIME)
     */
    var $gap_time;
    
    /**
     * Bool - gap time is added to the end of the reserved item to the calendar.
     *
     * @var int
     */
    var $dynamic_gap_time;
    
    /**
     * Price if this reserving is special
     *
     * @var float
     */
    var $special_offer;

    /**
     * Minimum allowed interval.
     * 
     * @var int
     */
    var $min;
    
    /**
     * Maximum allowed interval.
     * 
     * @var int
     */
    var $max;
    
    /**
     * Fixed limit of reservation units. EQ every reservation is only for 7 days.
     *
     * @var int
     */
    var $fix;
    
    /**
     * Fixed can start from concrete day of week only.
     * eq. fixed limit is 7 and can start only at Monday pr Tuesday
     * 
     * @var string array of day short code (any, mon, tue, wed, thu, fri, sat, sun)
     */
    var $fix_from;
    
    /**
     * Allow book fixed interval if starts in the past.
     * 
     * @var boolean
     */
    var $book_fix_past;
    
    /**
     * Allow book multiply fixed limit as a single reservation.
     * Otherwise every fixed limit is a single reservation although they are behind.
     * @var boolean
     */
    var $fix_multiply;
    
    public function __construct(& $db)
    {
        parent::__construct('#__' . PREFIX . '_reservation_type', 'id', $db);
    }

    /**
     * Init empty object
     * 
     */
    public function init()
    {
        $this->id = 0;
        $this->subject = 0;
        $this->title = '';
        $this->type = 0;
        $this->description = '';
        $this->capacity_unit = 0;
        $this->time_unit = '';
        $this->gap_time = '';
        $this->dynamic_gap_time = 0;
        $this->special_offer = 0;
        $this->min = 0;
        $this->max = 0;
        $this->fix = 0;
        $this->fix_from = serialize(array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'));
        $this->book_fix_past = 0;
        $this->fix_multiply = 0;
    }

    /**
     * Clean object data.
     */
    public function clean()
    {
        $this->id = (int) $this->id;
        $this->subject = (int) $this->subject;
        $this->title = JString::trim($this->title);
        $this->type = (int) $this->type;
        $this->description = JString::trim($this->description);
        $this->capacity_unit = (int) $this->capacity_unit;
        $this->time_unit = (int) $this->time_unit;
        $this->gap_time = (int) $this->gap_time;
        $this->dynamic_gap_time = (((string)$this->dynamic_gap_time)=='on')? 1 : 0;
        if($this->dynamic_gap_time)
        	$this->gap_time = $this->time_unit; 
        $this->special_offer = (int) $this->special_offer;
        $this->min = (int) $this->min;
        $this->max = (int) $this->max;
        $this->fix = (int) $this->fix;
        $this->fix_from = is_array($this->fix_from) ? serialize($this->fix_from) : serialize(array());
        $this->book_fix_past = (int) $this->book_fix_past;
        $this->fix_multiply = (int) $this->fix_multiply;
    }

    /**
     * Check object before saving.
     */
    public function check()
    {
        $this->clean();
        if (($result = parent::check())) {
            $result = false;
            $result = $this->title != '' ? true : $result;
            $result = $this->description != '' ? true : $result;
            $result = $this->capacity_unit != 0 ? true : $result;
            $result = $this->time_unit != '' ? true : $result;
            $result = $this->gap_time != '' ? true : $result;
        }
        return $result;
    }

    /**
     * Display object of Reservation Type with real data.
     * 
     * @param TableReservationType $object  
     */
    public function display(&$object)
    {
        $object->id = (int) $object->id;
        $object->subject = (int) $object->subject;
        $object->title = JString::trim($object->title);
        $object->type = (int) $object->type;
        $object->description = JString::trim($object->description);
        $object->capacity_unit = (int) $object->capacity_unit;
        $object->capacity_unit = $object->capacity_unit ? $object->capacity_unit : '';
        $object->time_unit = (int) $object->time_unit;
        $object->time_unit = $object->time_unit ? $object->time_unit : '';
        $object->gap_time = (int) $object->gap_time;
        $object->gap_time = $object->gap_time ? $object->gap_time : '';
        $object->dynamic_gap_time = (int) $object->dynamic_gap_time;
        $object->special_offer = (int) $object->special_offer;
        $object->min = $object->min == 0 ? '' : $object->min;
        $object->max = $object->max == 0 ? '' : $object->max;
        $object->fix = $object->fix == 0 ? '' : $object->fix;
        $object->fix_from = (array) @unserialize($object->fix_from);
        $object->book_fix_past = (int) $object->book_fix_past;
        $object->fix_multiply = (int) $object->fix_multiply;
    }
}

?>