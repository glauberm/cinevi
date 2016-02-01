<?php

/**
 * Subject available bookable supplement.
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

class TableSupplement extends JTable
{
    /**
     * Primary key
     * 
     * @var int
     */
    var $id;
    /**
     * Ordering value
     * 
     * @var int
     */
    var $ordering;
    /**
     * Subject owner - ID
     * 
     * @var int
     */
    var $subject;
    /**
     * Supplement title
     * 
     * @var string
     */
    var $title;
    /**
     * Supplement description
     * 
     * @var string
     */
    var $description;
    /**
     * Supplement type. 1 selectable, 2 yes/no
     * 
     * @var int
     */
    var $type;
    /**
     * If selectable type, list of select options
     * 
     * @var string
     */
    var $options;
    /**
     * For list type use empty option (no select anything)
     * 
     * @var int 1/0 use/unuse
     */
    var $empty;
    /**
     * Supplement is paid or only to select object property.
     * 
     * @var int 1/0 paid/unpaid
     */
    var $paid;
    /**
     * Supplement unit price
     * 
     * @var string
     */
    var $price;
    /**
     * Member discount list
     * @var array
     */
    var $member_discount;
    /**
     * If multiply supplement capacity by reserved capacity. 0: always 1 unit, 1: multiply by reserved capacity, 2: manual select capacity
     * 
     * @var int
     */
    var $capacity_multiply;
    /**
     * Maximal supplement capacity (only applies on $capacity_multiply option 2)
     * 
     * @var int
     */
    var $capacity_max;
    /**
     * Minimal allowed capacity of supplement
     * @var int
     */
    var $capacity_min;
    /**
     * Multiply price for count of reservation's units (hours or days)
     * 
     * @var int
     */
    var $unit_multiply;
    /**
     * Value of supplement surcharge (optional)
     * 
     * @var float
     */
    var $surcharge_value;
    /**
     * Label of supplement surcharge (optional)
     * 
     * @var string
     */
    var $surcharge_label;
    
    /**
     * Constructor - init table name, primary key and database connector.
     * 
     * @param JDatabaseMySQL $db instance of database connector
     */
    public function __construct(& $db)
    {
        parent::__construct('#__' . PREFIX . '_supplement', 'id', $db);
    }

    /**
     * Init empty object.
     */
    public function init()
    {
        $this->id = 0;
        $this->subject = 0;
        $this->title = '';
        $this->description = '';
        $this->type = SUPPLEMENT_TYPE_UNSELECT;
        $this->options = '';
        $this->empty = SUPPLEMENT_TYPE_UNSELECT;
        $this->paid = SUPPLEMENT_NO_PRICE;
        $this->price = '';
        $this->member_discount = array();
        $this->capacity_multiply = 1;
        $this->capacity_max = 0;
        $this->capacity_min = 0;
        $this->unit_multiply = 0;
        $this->surcharge_value = 0;
        $this->surcharge_label = '';
    }

    /**
     * Clean object data.
     */
    public function clean()
    {
        $this->id = (int) $this->id;
        $this->subject = (int) $this->subject;
        $this->title = JString::trim($this->title);
        $this->description = JString::trim($this->description);
        $this->type = (int) $this->type;
        $this->options = JString::trim($this->options);
        $this->empty = (int) $this->empty;
        $this->paid = (int) $this->paid;
        $this->price = JString::trim($this->price);
        $this->capacity_multiply = (int) $this->capacity_multiply;
        $this->capacity_max = (int) $this->capacity_max;
        $this->capacity_min = (int) $this->capacity_min;
        $this->unit_multiply = (int) $this->unit_multiply;
        $this->surcharge_value = (float) $this->surcharge_value;
        $this->surcharge_label = JString::trim($this->surcharge_label);
    }

    /**
     * Check before saving.
     */
    public function check()
    {
        $this->clean();
        if (($result = parent::check())) {
            $result = false;
            $result = $this->title ? true : $result;
            $result = $this->type ? true : $result;
            $result = $this->price ? true : $result;
        }
        return $result;
    }

    /**
     * Prepare object data to display.
     * 
     * @param TableSupplement $object
     * @return void
     */
    public function display(&$object)
    {
        $object->price = $object->paid == SUPPLEMENT_NO_PRICE ? '' : $object->price;
       	$registry = new JRegistry($object->member_discount);
    	$object->member_discount = $registry->toArray();
    }
    
    /**
     * (non-PHPdoc)
     * @see JTable::store()
     */
    public function store($updateNulls = false) 
    {
    	$registry = new JRegistry($this->member_discount);
    	$this->member_discount = $registry->toString();
		return parent::store($updateNulls);    	
    }

    /**
     * @param TableSupplement $supplement
     */
    public function prepare(&$supplement)
    {
    	$registry = new JRegistry($supplement->member_discount);
    	$supplement->member_discount = $registry->toArray();
    	
    	$userGroups = JFactory::getUser()->getAuthorisedGroups();
    	
        if ($supplement->type == SUPPLEMENT_TYPE_LIST) {
            $options = explode("\n", str_replace("\r\n", "\n", $supplement->options));
            if (is_array($options)) {
                foreach ($options as $i => $option)
                    if (! ($option = JString::trim($option)))
                        unset($options[$i]);
                    else
                        $options[$i] = array($option);
                if (count($options)) {
                    $options = array_merge($options);
                    if ($supplement->paid == SUPPLEMENT_MORE_PRICES) {
                        $prices = explode("\n", str_replace("\r\n", "\n", $supplement->price));
                        if (is_array($prices))
                            foreach ($prices as $i => $price)
                                if (JString::trim($price) == '')
                                    unset($prices[$i]);
                                else
                                    $prices[$i] = (float) $price;
                        else
                            $prices = array((float) $supplement->price);
                    } elseif ($supplement->paid == SUPPLEMENT_ONE_PRICE)
                        $prices = array((float) $supplement->price);
                    else
                        $prices = array(null);
                    $prices = array_merge($prices);
                    
                    foreach ($prices as &$price)
                    	foreach ($supplement->member_discount as $userGroup => $memDis) // apply member discount
                    		if (in_array($userGroup, $userGroups))
                    			$price -= ($memDis['type'] == DISCOUNT_TYPE_VALUE) ? $memDis['value'] : (($price / 100) * $memDis['value']);
                    
                    foreach ($options as $i => $option)
                        $options[$i][] = isset($prices[$i]) ? $prices[$i] : end($prices);
                }
            } else
                $options = null;
            $supplement->options = $options;
        }
		
		foreach ($supplement->member_discount as $userGroup => $memDis) // apply member discount
          	if (in_array($userGroup, $userGroups))
               	$supplement->price -= ($memDis['type'] == DISCOUNT_TYPE_VALUE) ? $memDis['value'] : (($supplement->price / 100) * $memDis['value']);
    }
}

?>