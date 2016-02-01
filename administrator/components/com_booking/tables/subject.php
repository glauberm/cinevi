<?php

/**
 * Booking subject.
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

class TableSubject extends JTable
{
    
    /**
     * Primary key
     *
     * @var int
     */
    var $id;
    
    /**
     * Subject parent ID - entity of same type
     *
     * @var int
     */
    var $parent;
    
    /**
     * Template type ID - specify subject template to create
     *
     * @var int
     */
    var $template;
    
    /**
     * Subject title name
     *
     * @var string
     */
    var $title;
    
    /**
     * Alias title, use for SEO alias
     * 
     * @var string
     */
    var $alias;
    
    /**
     * Subject info intro text
     *
     * @var string
     */
    var $introtext;
    
    /**
     * Subject info main text
     *
     * @var string
     */
    var $fulltext;
    
    /**
     * Max capacity of reservation. For example max. person who can reserving room.
     *
     * @var int
     */
    var $total_capacity;
    
    var $show_occupancy;
    /**
     * Maximum standard occupancy
     *
     * @var int
     */
    var $standard_occupancy_max;
    
    /**
     * Mimimum standard occupancy
     *
     * @var int
     */
    var $standard_occupancy_min;    
    
    /**
     * Maximum extra occupancy
     *
     * @var int
     */
    var $extra_occupancy_max;
    
    /**
     * Minimum extra occupancy
     *
     * @var int
     */
    var $extra_occupancy_min;
    
    /**
     * Subject state: 1 .. published, 0 .. unpublished, -1 .. archived, -2 .. deleted
     *
     * @var int
     */
    var $state;
    
    /**
     * Featured subject
     * @var int 1/0
     */
    var $featured;
    /**
     * Subject ordering
     *
     * @var int
     */
    var $ordering;
    
    /**
     * Time subject publish up, if empty publish up unlimited
     *
     * @var string (MySQL datetime)
     */
    var $publish_up;
    
    /**
     * Time subject publish down, if empty publish down unlimited
     *
     * @var string (MySQL datetime)
     */
    var $publish_down;
    
    /**
     * Usertype who can visited subject on frontend page: 0 .. all, 1 .. registered, 2 .. special
     *
     * @var int
     */
    var $access;
    
    /**
     * Subject visitors count
     *
     * @var int
     */
    var $hits;
    
    /**
     * If subject editing, ID user who editing
     *
     * @var int
     */
    var $checked_out;
    
    /**
     * Time of lock if some user editing subject
     *
     * @var string (MySQL datetime)
     */
    var $checked_out_time;
    
    /**
     * Main subject image
     * 
     * @var string
     */
    var $image;
    
    /**
     * Subject images gallery
     * 
     * @var string
     */
    var $images;
    
    /**
     * Subject related files
     * 
     * @var string
     */
    var $files;
    
    /**
     * Enable to book 13:00-15:00 interval, when 12:00-14:00 is booked.
     *
     * @var int
     */
    var $price_overlay;
    
    /**
     * Way to display reservation types in calendar. Allow display only one or more.
     * 
     * @var int
     */
    var $display_only_one_rtype;
    
    /**
     * Minimum reservation box length in minutes.
     * 
     * @var int
     */
    var $min_limit;
    
    /**
     * Type of reserving: exclusive/chain/overlap
     * 
     * @var int
     */
    var $reserving;
    /**
     * Use fixed calendar shedule length
     * 
     * @var int
     */
    var $use_fix_shedule;
    /**
     * Fixed shedule from
     * 
     * @var string
     */
    var $shedule_from;
    /**
     * Fixed shedule to
     * 
     * @var string
     */
    var $shedule_to;
    /**
     * Display capacity in calendar 0/1 ... off/on
     * 
     * @var int
     */
    var $display_capacity;
    /**
     * Reservation limit count reservations
     * 
     * @var int
     */
    var $rlimit_count;
    /**
     * Reservation limit count days
     * 
     * @var int
     */
    var $rlimit_days;
    /**
     * Reservation limit turn status 0/1 - OFF/ON
     * 
     * @var int
     */
    var $rlimit_set;
    /**
     * Use booking type per night (hotels)
     * 
     * @var int
     */
    var $night_booking;
    /**
     * Night booking first day from time
     * 
     * @var string
     */
    var $night_booking_from;
    /**
     * Night booking first day to time
     * 
     * @var string
     */
    var $night_booking_to;
    /**
     * Allow booking over more prices timeliness.
     * 
     * @var int 1/0 ... allow/disallow
     */
    var $book_over_timeliness;
    /**
     * Use single deposit for reservation
     * 
     * @var int
     */
    var $single_deposit;
    /**
     * Single deposit value type
     * @var int 1/0 ... value/percent
     */
    var $single_deposit_type;
    /**
     * Single deposit value include supplements value (with percent deposit only)
     * @var int 1/0 ... include/exclude
     */
    var $single_deposit_include_supplements;
    /**
     * Volume discount
     * 
     * @var array(count => int, value => float, type => int)
     */
    var $volume_discount;
    /**
     * Early booking discount
     * 
     * @var array(count => int, value => float, type => int)
     */
    var $early_booking_discount;
    /**
     * Member discount 
     * 
     * @var array(id => array(value => float, type => int))
     */
    var $member_discount;
    /**
     * Agent provision 
     * 
     * @var array(id => array(value => float, type => int))
     */    
    var $agent_provision;
    /**
     * Meta data keywords.
     * 
     * @var string
     */
    var $keywords;
    /**
     * Minimum capacity to start "event".
     * 
     * @var int
     */
    var $minimum_capacity;
    /**
     * Meta data description.
     * 
     * @var string
     */
    var $description;
    /**
     * Owner.
     *
     * @var int
     */
    var $user_id;
    /**
     * Store properties without extra table columns like Joomla paramter type.
     *
     * @var string
     */
    var $params;
    
    var $_extras;
    /**
     * Use google maps.
     * 
     * @var string off|address|code
     */
    var $google_maps;
    /**
     * Object address (with option address)
     *
     * @var string
     */
    var $google_maps_address;
    /**
     * Map width (with option address)
     *
     * @var int pixels
     */
    var $google_maps_width;
    /**
     * Map height (with option address)
     *
     * @var int pixels
     */
    var $google_maps_heigth;
    /**
     * Map zoom (with option address)
     *
     * @var int from 1 to 20
     */
    var $google_maps_zoom;
    /**
     * Map code (with option code)
     * 
     * @var string
     */
    var $google_maps_code;
    /**
     * Map displaying.
     *
     * @var string page|lightbox
     */
    var $google_maps_display;
    /**
     * Prices tax.
     * 
     * @var float
     */
    var $tax;
    
    /**
     * if 0 show contact form.
     *
     * @var int
     */
    var $show_contact_form;
    /**
     * email for contact form.
     *
     * @var string
     */
    var $contact_email;
    /**
     * Prices tax from low.
     * @var int
     */
    var $tax_rate_id;
    
    var $display_who_reserve;
    /**
     * minimal price for subject
     *
     * @var int
     */
    var $minprice;
        public function __construct(&$db)
    {
        parent::__construct('#__' . PREFIX . '_subject', 'id', $db);
        $this->_extras = array('price_overlay' => 'int','display_only_one_rtype' => 'int' , 'min_limit' => 'int' , 'image' => 'string' , 'images' => 'string', 'files' => 'string'  , 'reserving' => 'int' , 'use_fix_shedule' => 'int' , 'shedule_to' => 'time' , 'shedule_from' => 'time' , 'display_capacity' => 'int' , 'rlimit_count' => 'int' , 'rlimit_days' => 'int' , 'rlimit_set' => 'int' , 'night_booking' => 'int' , 'night_booking_from' => 'time' , 'night_booking_to' => 'time' , 'keywords' => 'string' , 'description' => 'string' , 'book_over_timeliness' => 'int' , 'single_deposit' => 'float' , 'single_deposit_type' => 'int' , 'single_deposit_include_supplements' => 'int' , 'volume_discount' => 'array', 'early_booking_discount' => 'array', 'member_discount' => 'collection', 'agent_provision' => 'collection', 'minimum_capacity' => 'int', 'google_maps' => 'string', 'google_maps_address' => 'string', 'google_maps_width' => 'int', 'google_maps_heigth' => 'int', 'google_maps_zoom' => 'string', 'google_maps_code' => 'string', 'google_maps_display' => 'string', 'tax' => 'string', 'show_contact_form' => 'int', 'contact_email' => 'string', 'tax_rate_id' => 'low', 'display_who_reserve' => 'string');
    }

    /**
     * Init empty object.
     */
    public function init()
    {
        $this->id = 0;
        $this->parent = 0;
        $this->template = 0;
        $this->reservation_type = 0;
        $this->title = '';
        $this->alias = '';
        $this->description = '';
        $this->total_capacity = 1;
        $this->minimum_capacity = 1;
        $this->show_occupancy = 0;
        $this->standard_occupancy_min = 1;
        $this->standard_occupancy_max = 1;
        $this->extra_occupancy_min = 0;
        $this->extra_occupancy_max = 1;        
        $this->state = SUBJECT_STATE_PUBLISHED;
        $this->featured = SUBJECT_NOFEATURED;
        $this->ordering = 0;
        $this->publish_up = '';
        $this->publish_down = '';
        $this->access = SUBJECT_ACCESS_PUBLIC;
        $this->hits = 0;
        $this->checked_out = 0;
        $this->checked_out_time = '';
                foreach ($this->_extras as $param => $type)
            $this->$param = '';
    }

    /**
     * Clean object params. Unreal data values like 0 replace by emty string.
     */
    public function clean()
    {

    }

    /**
     * Bind object from data source and clean values.
     * 
     * @param array $data source
     * @return boolean 
     */
    public function bind($data, $save = false)
    {
        $result = parent::bind($data, array('checked_out' , 'checked_out_time'));
        
        if ($save) {
            if (isset($data['text']))
                AController::setTextProperties($this, $data['text']);
            foreach ($this->_extras as $param => $type)
                if (! isset($data[$param]))
                    $this->$param = null;
        }
        
        $this->id = (int) $this->id;
        $this->parent = (int) $this->parent;
        $this->template = (int) $this->template;
        $this->title = JString::trim($this->title);
        $this->alias = JString::trim($this->alias);
        $this->total_capacity = (int) $this->total_capacity;
        $this->state = (int) $this->state;
        $this->featured = (int) $this->featured;
        $this->ordering = (int) $this->ordering;
        $this->publish_up = JString::trim($this->publish_up);
        $this->publish_down = JString::trim($this->publish_down);
        $this->access = (int) $this->access;
        $this->hits = (int) $this->hits;
        $this->user_id = (int) $this->user_id;
        
        if (! $save) {
            $params = new JRegistry($this->params);
            foreach ($this->_extras as $param => $type) {
                if ($type == 'int') {
                    if (($this->$param = (int) $params->get($param)) == 0)
                        $this->$param = '';
                } elseif ($type == 'float') {
                    if (($this->$param = (float) $params->get($param)) == 0.0)
                        $this->$param = '';
                } elseif ($type == 'array' || $type == 'collection') {
                	$registry = new JRegistry($params->get($param));
                    $this->$param = $registry->toArray();
                } else
                	$this->$param = JString::trim($params->get($param));
            }
        } else {
        	foreach ($this->_extras as $param => $type)
        		if ($type == 'array' || $type == 'collection') {
        			$this->$param = new JRegistry($type == 'array' ? json_decode($this->$param) : $this->$param);
        			$this->$param = $this->$param->toString();
        		}
        }
        
        if (isset(AFactory::getConfig()->taxRates[$this->tax_rate_id]))
        	$this->tax = AFactory::getConfig()->taxRates[$this->tax_rate_id][1];
        
        TableSubject::prepare($this);
        
        return $result;
    }
    
    public function merge()
    {
    	$config = AFactory::getConfig();
    	if ($this->display_who_reserve === '')
    		$this->display_who_reserve = $config->displayWhoReserve;
    }

    /**
     * Check object data before saving.
     * 
     * @return boolean
     */
    public function check()
    {
        if (($result = parent::check())) {
            if (! ($this->publish_up = AModel::datetime2save($this->publish_up)))
                $this->publish_up = AModel::getNullDate();
            if (! ($this->publish_down = AModel::datetime2save($this->publish_down)))
                $this->publish_down = AModel::getNullDate();
            if (! $this->alias)
                $this->alias = JFilterOutput::stringURLSafe($this->title);
        }
        return $result;
    }

    /**
     * Save object into database.
     * 
     * @return boolean
     */
    public function store($updateNulls = false)
    {
    	$this->publish_up = Amodel::datetime2save($this->publish_up, true);
    	$this->publish_down = Amodel::datetime2save($this->publish_down, true);
    	
        $params = new JRegistry();
        foreach ($this->_extras as $param => $type) {
            if ($type == 'int')
                $params->set($param, (int) $this->$param);
            elseif ($type == 'float')
                $params->set($param, (float) $this->$param);
            elseif ($type == 'time')
                $params->set($param, AModel::time2save($this->$param));
            elseif ($type == 'array' || $type == 'collection')
				$params->set($param, $this->$param);
           	else
             	$params->set($param, JString::trim($this->$param));
            $this->$param = null;
        }
        $this->params = $params->toString();
        return parent::store();
    }

    /**
     * @param TableSubject $subject
     */
    public function prepare(&$subject)
    {
        $subject->occupancy_types = JModelLegacy::getInstance('OccupancyTypes', 'BookingModel')->init(array('subject' => $subject->id))->getData();
    }
}

?>