<?php

/**
 * E-mail template table.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	tables 
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class TableArticle extends JTable
{
    
    public function __construct(&$db)
    {
        parent::__construct('#__booking_article', 'id', $db);
    }
}

?>