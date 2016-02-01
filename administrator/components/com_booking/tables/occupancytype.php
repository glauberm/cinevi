<?php

/**
 * Occupancy type table.
 * 
 * @version	$Id$
 * @package	ARTIO Booking
 * @subpackage	tables 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license   	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class TableOccupancyType extends JTable
{
    /**
     * Primary key
     * @var int
     */
    var $id;
    /**
     * Reference to owner subject
     * @var int
     */
    var $subject;
    /**
     * Custom title
     * @var string
     */
    var $title;
    /**
     * Occupancy type
     * @var int 0/1 ... standard/extra
     */
    var $type;

    /**
     * @param JDatabase $db database connector
     */
    public function __construct(& $db)
    {
        parent::__construct('#__' . PREFIX . '_occupancy_type', 'id', $db);
    }
}