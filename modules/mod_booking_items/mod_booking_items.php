<?php
 
/**
 * @version	$Id$
 * @package   	ARTIO Booking
 * @subpackage	modules/mod_booking_items
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license  	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('_JEXEC') or die();

require_once(JPath::clean(JPATH_ROOT . '/modules/mod_booking_items/helper.php'));

modBookingItemsHelper::import();
$items = modBookingItemsHelper::getItems($params);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$item_heading = $params->get('item_heading', 'h4');

include (JModuleHelper::getLayoutPath('mod_booking_items'));