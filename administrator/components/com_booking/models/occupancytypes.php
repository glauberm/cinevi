<?php

/**
 * Occupancy types list model.
 * 
 * @version	$Id$
 * @package	ARTIO Booking
 * @subpackage	models 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license   	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

define('OTYPES_PREFIX', 'otype-');

//import needed Joomla! libraries
jimport('joomla.application.component.model');
//import needed JoomLIB helpers
AImporter::helper('model');

class BookingModelOccupancyTypes extends AModel
{
    
    /**
     * @var TableOccupancyType
     */
    var $_table;

    public function __construct()
    {
        parent::__construct();
        $this->_table = $this->getTable('occupancytype');
    }
    
    public function getData()
    {
    	$query = $this->getDbo()->getQuery(true)->select('*')->from('#__booking_occupancy_type')->where('subject = ' . (int) $this->_lists['subject'])->order('type');
    	return $this->getDbo()->setQuery($query)->loadObjectList('id');
    }
    
    public function store($subject, $data)
    {
    	if (empty($data[OTYPES_PREFIX . 'title']))
    		$data[OTYPES_PREFIX . 'title'] = $data[OTYPES_PREFIX . 'type'] = array();
    	return parent::store($this->_db, $this->_table, array('subject' => $subject), OTYPES_PREFIX, $data);
    }
}