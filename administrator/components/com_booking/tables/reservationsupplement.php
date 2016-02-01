<?php
/**
 * Reservation supplement table object.
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

class TableReservationSupplement extends JTable
{
    /**
     * Primary key
     * 
     * @var int
     */
    var $id;
    /**
     * ID of reservation ITEM.
     * 
     * @var int
     */
    var $reservation;
    /**
     * Supplement ID
     * 
     * @var int
     */
    var $supplement;
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
     * Supplement type
     * 
     * @var unknown_type
     */
    var $type;
    /**
     * Supplement selected value
     * 
     * @var string
     */
    var $value;
    /**
     * Supplement paid type
     * 
     * @var int
     */
    var $paid;
    /**
     * Supplement price
     * 
     * @var float
     */
    var $price;
    /**
     * Supplement full price
     * 
     * @var float
     */
    var $fullPrice;
    /**
     * Supplement capacity
     * 
     * @var int
     */
    var $capacity;
    
    var $boxsCount;
    
    /**
     * Construct object.
     * 
     * @param JDatabaseMySQL $db database connector
     */
    public function __construct(& $db)
    {
        parent::__construct('#__' . PREFIX . '_reservation_supplement', 'id', $db);
    }

}

?>