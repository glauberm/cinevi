<?php

/**
 * Print Manager Voucher
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addScriptDeclaration("
window.addEvent('domready', function() {
    window.print();
});
");
$document->addStyleDeclaration('
tr {
    border: medium none;
}
td {
    border: medium none;
    padding: 3px 10px 3px 0;
}
td:first-child {
    text-align: right;
}
td:empty {
    padding: 0;
}    
');
include(dirname(__FILE__) . '/voucher.php');
