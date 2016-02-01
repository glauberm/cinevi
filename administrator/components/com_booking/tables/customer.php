<?php

/**
 * Customer table with main person properties.
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

AImporter::helper('utils');

class TableCustomer extends JTable
{
    
    /**
     * Primary key
     * 
     * @var int
     */
    var $id;
    
    /**
     * Joomla user with assigned to - ID
     * 
     * @var int
     */
    var $user;
    
    /**
     * Person title before name
     * 
     * @var string
     */
    var $title_before;
    
    /**
     * Person firstname
     * 
     * @var string
     */
    var $firstname;
    
    /**
     * Person middlename
     * 
     * @var string
     */
    var $middlename;
    
    /**
     * Person surname
     * 
     * @var string
     */
    var $surname;
    
    /**
     * Person title after name
     * 
     * @var string
     */
    var $title_after;
    
    /**
     * Company name (optional)
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
     * Adrress street
     * 
     * @var string
     */
    var $street;
    
    /**
     * Adrress city
     * 
     * @var string
     */
    var $city;
    
    /**
     * Adrress country
     * 
     * @var string
     */
    var $country;
    
    /**
     * Adrress zip code
     * 
     * @var string
     */
    var $zip;
    
    /**
     * Telephone numbers(s) - separately by comma
     *  
     * @var string
     */
    var $telephone;
    
    /**
     * Fax number(s) - separately by comma
     * 
     * @var string
     */
    var $fax;
    
    /**
     * Customer state, 1 ... active, 0 ... trashed
     * 
     * @var int
     */
    var $state;
    /**
     * ID of user who now editing object.
     * 
     * @var int
     */
    var $checked_out;
    
    /**
     * Date and time start checkin - MySQL datetime
     * 
     * @var string
     */
    var $checked_out_time;

    /**
     * Extra fields values as serialize string
     * 
     * @var string
     */
    var $fields;
    /**
     * Construct object.
     * 
     * @param JDatabaseMySQL $db database connector
     */
    public function __construct(& $db)
    {
        parent::__construct('#__' . PREFIX . '_customer', 'id', $db);
    }

    public function bind($data, $ignore = array()) {
    	parent::bind($data);
    	
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
        
    	if (is_array($data)) { // asociated array from request
    		foreach ($config->rsExtra as $field) {
                $this->fields[$field['name']]['value'] = $this->$field['name'] = JArrayHelper::getValue($data, $field['name']);            
            }
        }
    	
    	return true;
    }
    
    /**
     * Init empty object.
     */
    public function init()
    {
        $this->id = 0;
        $this->user = 0;
        $this->title_before = '';
        $this->firstname = '';
        $this->middlename = '';
        $this->surname = '';
        $this->title_after = '';
        $this->company = '';
        $this->street = '';
        $this->city = '';
        $this->zip = '';
        $this->telephone = '';
        $this->fax = '';
        $this->state = CUSTOMER_STATE_ACTIVE;
    }
    
	public function store($updateNulls = false)
    {
    	$config = AFactory::getConfig();
    	$this->fields = array();	
    	foreach ($config->rsExtra as $field) { 
    		if (isset($this->$field['name'])) {
    			$this->fields[$field['name']]['title'] = $field['title'];
    			$this->fields[$field['name']]['value'] = $this->$field['name'];
    			unset($this->$field['name']);
    		}
    	}
    	$this->fields = serialize($this->fields);
    	return parent::store();
    }
}

?>