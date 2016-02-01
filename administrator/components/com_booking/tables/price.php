<?php

/**
 * Subject available price.
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

class TablePrice extends JTable
{
    
    /**
     * Primary key
     * 
     * @var int
     */
    var $id;
    
    /**
     * Subject owner - ID
     * 
     * @var int
     */
    var $subject;
    
    /**
     * Price value
     * 
     * @var int
     */
    var $value;
    
    /**
     * Price deposit value
     * 
     * @var int
     */
    var $deposit;
    
    /**
     * Price deposit type
     * @var int 1/2 ... value/percent
     */
    var $deposit_type;
    
    /**
     * Deposit is added to price multiply for each time unit or single.
     * 
     * @var int 1/0 ... multiply/single
     */
    var $deposit_multiply;
    
    /**
     * If multiply price by reserved capacity.
     * 
     * @var int 1/0 ... multiply/single
     */
    var $price_capacity_multiply;
    
    /**
     * Multiply price by reserved standard occupancy.
     *
     * @var int 1/0 ... multiply/single
     */
    var $price_standard_occupancy_multiply;
    
    /**
     * Multiply price by reserved extra occupancy.
     *
     * @var int 1/0 ... multiply/single
     */
    var $price_extra_occupancy_multiply;
    
    /**
     * If multiply deposit by reserved capacity.
     * 
     * @var int 1/0 ... multiply/single
     */
    var $deposit_capacity_multiply;
    
    /**
     * Multiply deposit by reserved standard occupancy
     *
     * @var int 1/0 ... yes/no
     */
    var $deposit_standard_occupancy_multiply;
    
    /**
     * Multiply deposit by reserved extra occupancy
     *
     * @var int 1/0 ... yes/no
     */
    var $deposit_extra_occupancy_multiply;
    
    /**
     * Deposit value include supplements value (with percent deposit only)
     * @var int 1/0 ... include/exclude
     */
    var $deposit_include_supplements;

    /**
     * Volume discount
     * 
     * @var array(count => int, value => float, type => int)
     */
    var $volume_discount;
    
    var $occupancy_price_modifier;
    /**
     * Price rezervation type
     * 
     * @var int
     */
    var $rezervation_type;
    
    /**
     * Start date validity - MySQL Date
     * 
     * @var string
     */
    var $date_up;
    
    /**
     * End date validity - MySQL Date
     * 
     * @var string
     */
    var $date_down;
    
    /**
     * Start time validity - MySQL Time
     * 
     * @var string
     */
    var $time_up;
    
    /**
     * End time validity - MySQL Time
     * 
     * @var string
     */
    var $time_down;
    
    /**
     * Price is valid on monday
     * 
     * @var boolean
     */
    var $monday;
    
    /**
     * Price is valid on tuesday
     * 
     * @var boolean
     */
    var $tuesday;
    
    /**
     * Price is valid on wednesday
     * 
     * @var boolean
     */
    var $wednesday;
    
    /**
     * Price is valid on thursday
     * 
     * @var boolean
     */
    var $thursday;
    
    /**
     * Price is valid on friday
     * 
     * @var boolean
     */
    var $friday;
    
    /**
     * Price is valid on saturday
     * 
     * @var boolean
     */
    var $saturday;
    
    /**
     * Price is valid on sunday
     * 
     * @var boolean
     */
    var $sunday;
    
    /**
     * In which week is price available.
     * 
     * @var int 0 - every, 1 - event, 2 - odd
     */
    var $week;
    
    /**
     * Time having to pay from order date
     *
     * @var int
     */
    var $cancel_time;

    /**
     * color for front-end calendar
     *
     * @var int
     */
    var $custom_color;
    
    /**
     * Time range:
     * 0 ... in one day EQ: from 8AM Monday to 3PM Monday
     * or
     * 1 ... over midnight EQ: from 8PM Friday to 4AM Saturday
     * or
     * 2 ... over week EQ: from 8AM Monday to 7PM Friday
     * @var int
     */
    var $time_range;
    
    var $head_piece;
    var $tail_piece;
    /**
     * Constructor - init table name, primary key and database connector.
     * 
     * @param JDatabaseMySQL $db instance of database connector
     */
    public function __construct(&$db)
    {
        parent::__construct('#__' . PREFIX . '_price', 'id', $db);
    }

    /**
     * Init empty object.
     */
    public function init()
    {
        $this->id = 0;
        $this->subject = 0;
        $this->value = '';
        $this->deposit = '';
        $this->deposit_type = DEPOSIT_TYPE_VALUE;
        $this->deposit_multiply = 0;
        $this->price_capacity_multiply = 1;
        $this->price_standard_occupancy_multiply = 1;
        $this->price_extra_occupancy_multiply = 0;
        $this->deposit_capacity_multiply = 1;
        $this->deposit_standard_occupancy_multiply = 1;
        $this->deposit_extra_occupancy_multiply = 0;
        $this->deposit_include_supplements = DEPOSIT_EXCLUDE_SUPPLEMENTS;
        $this->rezervation_type = 0;
        $this->volume_discount = array();
        $this->occupancy_price_modifier = array();
        $this->date_up = '';
        $this->date_down = '';
        $this->time_up = '';
        $this->time_down = '';
        $this->monday = 1;
        $this->tuesday = 1;
        $this->wednesday = 1;
        $this->thursday = 1;
        $this->friday = 1;
        $this->saturday = 1;
        $this->sunday = 1;
        $this->week = WEEK_EVERY;
        $this->cancel_time = '';
        $this->custom_color = '';
        $this->time_range = TIME_RANGE_ONE_DAY;
        $this->head_piece = '';
        $this->tail_piece = '';
    }

    /**
     * Clean object data.
     */
    public function clean()
    {
        $this->id = (int) $this->id;
        $this->subject = (int) $this->subject;
        $this->value = (float) $this->value;
        $this->deposit = (float) $this->deposit;
        $this->deposit_type = (int) $this->deposit_type;
        $this->deposit_multiply = (int) $this->deposit_multiply;
        $this->price_capacity_multiply = (int) $this->price_capacity_multiply;
        $this->price_standard_occupancy_multiply = (int) $this->price_standard_occupancy_multiply;
        $this->price_extra_occupancy_multiply = (int) $this->price_extra_occupancy_multiply;
        $this->deposit_capacity_multiply = (int) $this->deposit_capacity_multiply;
        $this->deposit_standard_occupancy_multiply = (int) $this->deposit_standard_occupancy_multiply;
        $this->deposit_extra_occupancy_multiply = (int) $this->deposit_extra_occupancy_multiply;
        $this->deposit_include_supplements = (int) $this->deposit_include_supplements;
        $this->rezervation_type = (int) $this->rezervation_type;
        $this->date_up = JString::trim((string) $this->date_up);
        $this->date_down = JString::trim((string) $this->date_down);
        $this->time_up = JString::trim((string) $this->time_up);
        $this->time_down = JString::trim((string) $this->time_down);
        $this->monday = (int) $this->monday;
        $this->tuesday = (int) $this->tuesday;
        $this->wednesday = (int) $this->wednesday;
        $this->thursday = (int) $this->thursday;
        $this->friday = (int) $this->friday;
        $this->saturday = (int) $this->saturday;
        $this->sunday = (int) $this->sunday;
        $this->week = (int) $this->week;
        $this->time_range = (int) $this->time_range;
        $this->head_piece = (int) $this->head_piece;
        $this->tail_piece = (int) $this->tail_piece;
    }

    /**
     * Check before saving.
     */
    public function check()
    {
        $this->clean();
        if (($result = parent::check())) {
            $result = false;
            $result = $this->value != 0.0 ? true : $result;
            $result = $this->rezervation_type != 0 ? true : $result;
            $this->date_up = AModel::date2save($this->date_up);
            $this->date_down = AModel::date2save($this->date_down);
            $this->time_up = AModel::time2save($this->time_up);
            $this->time_down = AModel::time2save($this->time_down);
            if ($this->custom_color)
            	$this->custom_color = '#'.$this->custom_color; // color picker does not append # before hexa code
        }
        return $result;
    }
    
    /**
     * Display object of Price with real data.
     * 
     * @param TablePrice $object  
     */
    public function display(&$object)
    {
        $object->deposit = (int) $object->deposit == 0 ? '' : $object->deposit;
        $object->occupancy_price_modifier = new JRegistry($object->occupancy_price_modifier);
        $object->occupancy_price_modifier = $object->occupancy_price_modifier->toArray();
        $object->volume_discount = new JRegistry($object->volume_discount);
        $object->volume_discount = (array) $object->volume_discount->toArray();
    }
    
    public function store($updateNulls = false)
    {
    	$this->occupancy_price_modifier = new JRegistry($this->occupancy_price_modifier);
    	$this->occupancy_price_modifier = $this->occupancy_price_modifier->toString();
    	$this->volume_discount = new JRegistry(json_decode($this->volume_discount));
    	$this->volume_discount = $this->volume_discount->toString();
    	return parent::store($updateNulls);
    }
    
    public function loadBySubject($id)
    {
    	$this->_db->setQuery('');
    	return $this->_db->loadResult();
    }

    /**
     * @param TablePrice $price
     * @param TableSubject $subject
     */
    public function prepare(&$price, &$subject)
    {
    	TablePrice::display($price);
    	$price->discounts = array();
    	foreach ($price->volume_discount as $voldis) {
    		if ($voldis['type'] == DISCOUNT_TYPE_PERCENT)
    			$price->discounts[$voldis['count']] = ($price->value / 100) * $voldis['value'];
    		else
    			$price->discounts[$voldis['count']] = $voldis['value'];
    	}
        ksort($price->discounts);
        
        $userGroups = JFactory::getUser()->getAuthorisedGroups();
        
        foreach ((array) $subject->member_discount as $userGroup => $memDis) // apply subject member discount
        	if (in_array($userGroup, $userGroups))
               	$price->value -= ($memDis['type'] == DISCOUNT_TYPE_VALUE) ? $memDis['value'] : (($price->value / 100) * $memDis['value']);
        
        $price->occupacyPrices = array();
        if (!empty($subject->occupancy_types)) {
            foreach ($subject->occupancy_types as $occ) {
                $price->occupacyPrices[$occ->id] = new stdClass();
                $price->occupacyPrices[$occ->id]->title = $occ->title;
                $price->occupacyPrices[$occ->id]->value = $price->value + JArrayHelper::getValue($price->occupancy_price_modifier, $occ->id);
            }
        }
    }
}

?>