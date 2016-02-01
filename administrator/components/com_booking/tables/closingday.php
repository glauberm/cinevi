<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	tables
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */
defined('_JEXEC') or die('Restricted access');

class TableClosingday extends JTable {

    public function __construct(&$db) {
        parent::__construct('#__booking_closingday', 'id', $db);
    }

    public function load($keys = null, $reset = true) {
        $result = parent::load($keys, $reset);
        $this->time_up = JHtml::date($this->time_up, 'H:i:s');
        $this->time_down = JHtml::date($this->time_down, 'H:i:s');
        return $result;
    }

    public function store($updateNulls = false) {
        $config = JFactory::getConfig();
        $date = JFactory::getDate();
        $user = JFactory::getUser();
        if ($this->id) {
            $this->modified = $date->toSql();
            $this->modified_by = $user->get('id');
        } else {
            $this->created = $date->toSql();
            $this->created_by = $user->get('id');
        }
        $tz = new DateTimeZone($config->get('offset'));
        $this->time_up = JFactory::getDate($this->time_up, $tz)->format('H:i:s');
        $this->time_down = JFactory::getDate($this->time_down, $tz)->format('H:i:s');
        return parent::store($updateNulls);
    }

}

?>