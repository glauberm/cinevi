<?php

/**
 * Booking template value.
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

class TableTemplate_value extends JTable
{
    /**
     * Unique primary key
     * 
     * @var int
     */
    var $id;
    /**
     * Text value
     * 
     * @var string
     */
    var $value;

    /**
     * Construct object
     * 
     * @param JDatabaseMySQL $db database connector
     */
    public function __construct(& $db)
    {
        parent::__construct('#__' . PREFIX . '_template_value', 'id', $db);
    }

    public function store($value = false)
    {
        $value = JString::trim($value);
        if (! is_numeric($value) && $value) {
            $value = $this->_db->Quote($value);
            $query = 'INSERT INTO `#__booking_template_value` (`value`) VALUES (' . $value . ') ';
            $query .= 'ON DUPLICATE KEY UPDATE `value` = ' . $value;
        	$this->_db->setQuery($query);
        	$this->_db->query();
        }
    }
}

?>