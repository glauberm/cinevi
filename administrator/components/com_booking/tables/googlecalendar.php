<?php

/**
 * @version  	$Id$
 * @package   	ARTIO Booking
 * @subpackage	tables 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author   	ARTIO s.r.o., http://www.artio.net
 * @license   	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class TableGoogleCalendar extends JTable
{
    
    public function __construct(&$db)
    {
        parent::__construct('#__booking_google_calendar', 'id', $db);
    }
    
    public function bind($src, $ignore = array())
    {
    	$this->id = $src['id'];
    	$this->title = $src['summary'];
    	$this->modified = JFactory::getDate()->toSql();
    	
    	return $this;
    }
    
    public function store($updateNulls = false)
    {
    	$query = $this->getDbo()->getQuery(true);
    	
    	$query->insert('#__booking_google_calendar')
    		  ->columns('id, title, modified')
    		  ->values($query->quote($this->id) . ', ' . $query->quote($this->title) . ', ' . $query->quote($this->modified));
    	
    	$this->getDbo()->setQuery($query)->query();
    	
    	return $this;
    }
    
    public function truncate()
    {
    	$query = $this->getDbo()->getQuery(true);
    	$query->delete('#__booking_google_calendar');
    	$this->getDbo()->setQuery($query)->query();
    	
    	return $this;
    }
}