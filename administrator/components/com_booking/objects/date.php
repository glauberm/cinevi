<?php

/**
 * Data object defines date.
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @subpackage  objects
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class BookingDate
{
	var $orig;
    var $uts;
    var $dts;

    function __construct()
    {
    	$this->orig = '';
        $this->uts = '';
        $this->dts = '';
    }
}

?>