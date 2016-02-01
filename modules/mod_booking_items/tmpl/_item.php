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

defined('_JEXEC') or die;

?>

<<?php echo $item_heading; ?> class="newsflash-title<?php echo $params->get('moduleclass_sfx'); ?>">
	<a href="<?php echo $item->link; ?>" title="<?php echo JText::sprintf('MOD_BOOKING_ITEMS_BOOK_ITEM', $item->title); ?>"><?php echo $item->title; ?></a>
</<?php echo $item_heading; ?>>

<?php if ($params->get('show_image', 1) && $item->thumb) { ?>
	<a href="<?php echo $item->link; ?>" title="<?php echo JText::sprintf('MOD_BOOKING_ITEMS_BOOK_ITEM', $item->title); ?>"><img src="<?php echo $item->thumb; ?>" alt="" /></a>
<?php } ?>

<?php if ($params->get('show_desc', 1) && $item->introtext) { ?>
	<p><?php echo $item->introtext; ?></p>
<?php } ?>

<?php if ($params->get('show_price', 1) && $item->price) { ?>
	<strong><?php echo JText::sprintf('MOD_BOOKING_ITEMS_FROM', BookingHelper::displayPrice($item->price)); ?></strong>
<?php } ?>

<?php if ($params->get('show_bookit', 1)) { ?>
	<input type="submit" value="<?php echo JText::_('MOD_BOOKING_ITEMS_BOOKIT'); ?>" class="button btn btn-primary" name="<?php echo JText::_('MOD_BOOKING_ITEMS_BOOKIT'); ?>" onclick="window.location='<?php echo addslashes($item->link); ?>'" title="<?php echo JText::sprintf('MOD_BOOKING_ITEMS_BOOK_ITEM', $item->title); ?>" />
<?php } ?>