<?php

/**
 * Booking template.
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

class TableTemplate extends JTable
{
    /**
     * Unique primary key
     * 
     * @var int
     */
    var $id;
    /**
     * Usable calendars types. For example: daily,weekly,monthly.
     * 
     * @var array
     */
    var $calendars;
    /**
     * Template extra params out of XML
     * 
     * @var string
     */
    var $params;
    /**
     * Template XML source
     * 
     * @var string
     */
    var $xml;
    /**
     * For concrete calendar use shortest interval
     * 
     * @var array
     */
    var $shortestInterval;
    /**
     * number of months/week for one page
     *
     * @var int
     */
    var $numberOfMonths;

    public function __construct(&$db)
    {
        parent::__construct('#__' . PREFIX . '_template', 'id', $db);
    }

    public function bind($data, $save = false)
    {
        if ($save) {
            $this->calendars = isset($data['calendars']) ? $data['calendars'] : array();
            $this->shortestInterval = isset($data['shortest_interval']) ? $data['shortest_interval'] : array();
            
            $this->numberOfMonths = isset($data['num_months']) ? $data['num_months'] : '';
            
            if (($calendarDefault = JString::trim($data['calendar_default'])))
                array_unshift($this->calendars, $calendarDefault);
        } else
            parent::bind($data);
    }

    public function display()
    {
        $params = new JRegistry($this->params);
        
        $this->calendars = JString::trim($params->get('calendars'));
        $this->calendars = $this->calendars ? explode(',', $this->calendars) : array();
        $this->calendars = array_unique($this->calendars);
        
        $this->shortestInterval = JString::trim($params->get('shortest_interval'));
        $this->shortestInterval = $this->shortestInterval ? explode(',', $this->shortestInterval) : array();
        
        $this->numberOfMonths = JString::trim($params->get('num_months'));
    }

    public function store($updateNulls = false)
    {
        $params = new JRegistry('');
        
        $params->set('calendars', implode(',', $this->calendars));
        $params->set('shortest_interval', implode(',', $this->shortestInterval));
        $params->set('num_months', $this->numberOfMonths);
        
        $this->params = $this->_db->Quote($params->toString());
        
        $query = 'INSERT INTO `' . $this->getTableName() . '` (`id`,`params`,`xml`) VALUES (' . $this->id . ',';
        $query .= $this->params . ',' . ($xml = $this->_db->Quote($this->xml)) . ') ';
        $query .= 'ON DUPLICATE KEY UPDATE `params` = ' . $this->params . ', `xml` = ' . $xml;
        
        $this->_db->setQuery($query);
        
        return $this->_db->query();
    }
}

?>