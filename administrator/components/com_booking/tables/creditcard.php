<?php

/**
 * Database table manager for credit card.
 * Support for saving (insert/update) and next database
 * operations supported with parent JTable.
 *
 * @package		ARTIO Booking
 * @subpackage  payments
 * @copyright	Copyright (C) 2012 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class TableCreditCard extends JTable
{
    var $id = null;
    var $reservation_id = null;
    var $card_type = null;
    var $username = null;
    var $card_number = null;
    var $sec_code = null;
    var $exp_month = null;
    var $exp_year = null;
    var $pay_type = null;

    public function __construct($db)
    {
        parent::__construct('#__booking_creditcards', 'id', $db);
    }
}
?>
