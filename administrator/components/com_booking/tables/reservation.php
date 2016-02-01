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

class TableReservation extends JTable
{
    /**
     * Primary key
     * 
     * @var int
     */
    var $id;
    /**
     * Customer ID
     * 
     * @var int
     */
    var $customer;
    /**
     * Creating date and time
     * 
     * @var string
     */
    var $created;
    /**
     * Creator user id
     * 
     * @var int
     */
    var $created_by;
    /**
     * Mofifing date and time
     * 
     * @var string
     */
    var $modified;
    /**
     * Modifier user id
     * 
     * @var string
     */
    var $modified_by;
    /**
     * Customer title before name
     * 
     * @var string
     */
    var $title_before;
    /**
     * Customer first name
     * 
     * @var string
     */
    var $firstname;
    /**
     * Customer middle name
     * 
     * @var string
     */
    var $middlename;
    /**
     * Customer surname
     * 
     * @var string
     */
    var $surname;
    /**
     * Customer title after name
     * 
     * @var string
     */
    var $title_after;
    /**
     * More customers
     * 
     * @var array 
     */
    var $more_names;
    /**
     * Customer company (optional)
     * 
     * @var string
     */
    var $company;
    /**
     * Company #
     * @var int
     */
    var $company_id;
    /**
     * Company Vat #
     * @var int
     */
    var $vat_id;
    /**
     * Customer address street
     * 
     * @var string
     */
    var $street;
    /**
     * Customer address city 
     * 
     * @var string
     */
    var $city;
    /**
     * Customer address country
     * 
     * @var string
     */
    var $country;
    /**
     * Customer address zip
     * 
     * @var string
     */
    var $zip;
    /**
     * Customer contact email.
     * 
     * @var unknown_type
     */
    var $email;
    /**
     * Customer contact telephone
     * 
     * @var string
     */
    var $telephone;
    /**
     * Customer contact fax
     * 
     * @var string
     */
    var $fax;
    /**
     * Rezervation state -1 .. trashed, 0 .. storned, 1 .. active 
     * 
     * @var int
     */
    var $state;
    /**
     * Reservation paid state 0 .. not receive, 1 .. receive
     * 
     * @var int 
     */
    var $paid;
    /**
     * Payment method identifier
     * 
     * @var string
     */
    var $payment_method_id;
    /**
     * Payment method title
     * 
     * @var string
     */
    var $payment_method_name;
    /**
     * Payment method info
     *
     * @var string
     */
    var $payment_method_info;
    /**
     * Customer wants to only pay deposit or whole price.
     * 
     * @var int 1/2 ... deposit  
     */
    var $payment_type;
    /**
     * Custom note text.
     * 
     * @var string
     */
    var $note;
    /**
     * User ID who item editing
     * 
     * @var int
     */
    var $checked_out;
    /**
     * Time checked
     * 
     * @var string MySQL Datetime
     */
    var $checked_out_time;

    /**
     * Extra fields values as serialize string
     * 
     * @var string
     */
    var $fields;
    /**
     * Time checked
     *
     * @var string MySQL Datetime
     */
    var $book_time;
    /**
     * Construct object.
     * 
     * @param JDatabaseMySQL $db database connector
     */
    public function __construct(& $db)
    {
        parent::__construct('#__' . PREFIX . '_reservation', 'id', $db);
    }

    /**
     * Init empty object
     * 
     */
    public function init()
    {
        $this->id = 0;
        $this->customer = 0;
        $this->title_before = '';
        $this->firstname = '';
        $this->middlename = '';
        $this->surname = '';
        $this->title_after = '';
        $this->more_names = array();
        $this->company = '';
        $this->street = '';
        $this->city = '';
        $this->country = '';
        $this->zip = '';
        $this->email = '';
        $this->telephone = '';
        $this->fax = '';
        $this->state = RESERVATION_ACTIVE;
        $this->paid = RESERVATION_PENDING;
        $this->checked_out = 0;
        $this->checked_out_time = '';
        $this->book_time = '';
        $this->payment_type = 1;
    }

    /**
     * Clean object data.
     */
    public function clean()
    {

    }

    /**
     * Bind reservation.
     * 1. From reservation's database (edit reservation)
     * 2. From customer's database 	  (add  reservation)
     * 3. From request                (save reservation)
     * @return boolean
     */
    public function bind($data, $ignore = array())
    {
        if (($result = parent::bind($data))) {
        	// clean up binded data
            $this->id = $this->id > 0 ? (int) $this->id : null;
            $this->customer = (int) $this->customer;
            $this->title_before = JString::trim($this->title_before);
            $this->firstname = JString::trim($this->firstname);
            $this->middlename = JString::trim($this->middlename);
            $this->surname = JString::trim($this->surname);
            $this->title_after = JString::trim($this->title_after);
            $this->more_names = is_string($this->more_names) ? json_decode($this->more_names) : json_encode(array_filter((array) $this->more_names, 'JString::trim'));
            $this->company = JString::trim($this->company);
            $this->street = JString::trim($this->street);
            $this->city = JString::trim($this->city);
            $this->country = JString::trim($this->country);
            $this->zip = JString::trim($this->zip);
            $this->email = JString::trim($this->email);
            $this->telephone = JString::trim($this->telephone);
            $this->fax = JString::trim($this->fax);
            $this->state = (int) $this->state;
            $this->paid = (int) $this->paid;
        }
    	$config = AFactory::getConfig();
    	/* @var $config BookingConfig */
    	
    	if (is_object($data) && isset($data->fields)) // object from database 
    		$this->fields = !is_array($data->fields) ? unserialize($data->fields) : $data->fields;
    	elseif (is_array($data) && isset($data['fields'])) // array from database 
    		$this->fields = !is_array($data['fields']) ? unserialize($data['fields']) : $data['fields'];
    	else // new item
    		$this->fields = array();
        
        if (is_array($this->fields)) { // translate boolean custom field
            foreach ($this->fields as $fi => $field) {
                if (!empty($field['value']) && ($field['value'] == 'jyes' || $field['value'] == 'jno')) {       
                    $this->fields[$fi]['rawvalue'] = $field['value'];
                    $this->fields[$fi]['value'] = JText::_($field['value']);                    
                }
            }
        }

    	if (is_array($data) && is_array($config->rsExtra)) { // asociated array from request
    		foreach ($config->rsExtra as $field){
    			$this->$field['name'] = JArrayHelper::getValue($data, $field['name']);
            }
        }

        return $result;
    }
      
    public function store($updateNulls = false)
    {
    	$config = AFactory::getConfig();
    	$this->fields = array();
    	if (is_array($config->rsExtra))
    	foreach ($config->rsExtra as $field) { 
    		if (isset($this->$field['name'])) {
    			$this->fields[$field['name']]['title'] = $field['title'];
    			$this->fields[$field['name']]['value'] = $this->$field['name'];
    			unset($this->$field['name']);
    		}
    	}
    	$this->fields = serialize($this->fields);
    	if ($this->id) {
    		$this->modified = JFactory::getDate()->toSql();
    		$this->modified_by = JFactory::getUser()->get('id');
    	} else {
    		$this->created = JFactory::getDate()->toSql();
    		$this->created_by = JFactory::getUser()->get('id');
    		$this->payment_method_info = $this->_db->setQuery('SELECT info FROM #__booking_payment WHERE alias = ' . $this->_db->quote($this->payment_method_id))->loadResult();
    	}
    	return parent::store();
    }
    
    /**
     * Update customer ID in reservation
     *
     * @param int $reservationId
     * @param int $customerId
     * @return bool
     */
    public function updateCustomer($reservationId, $customerId)
    {
        return $this->_db->setQuery('UPDATE #__booking_reservation
                                      SET customer = ' . $this->_db->quote($customerId) . ' 
                                      WHERE id = ' . $this->_db->quote($reservationId))->query();
    }
}
?>