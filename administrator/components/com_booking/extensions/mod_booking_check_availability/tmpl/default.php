<?php
/**
 * @package		 ARTIO Booking
 * @subpackage  modules
 * @copyright	 Copyright (C) 2014 ARTIO s.r.o.. All rights reserved.
 * @author 		 ARTIO s.r.o., http://www.artio.net
 * @link         http://www.artio.net Official website
 */
defined('_JEXEC') or die('Restricted access');

?>
<div class="mod_booking_check_availability">
	<form name="bookingCheckAvailability" method="get" action="<?php echo JRoute::_('index.php'); ?>">
	    <div class="input-append">
	        <label for="booking_arrival_date_da">
	            <?php echo JText::_('MOD_BOOKING_CHECK_AVAILABILITY_ARRIVAL_DATE'); ?>:
	        </label>
	        <br/>
	        <div class="field">
		        <input type="text" title="" name="booking_arrival_date_da" id="booking_arrival_date_da" value="<?php echo JFactory::getDate()->format(ADATE_FORMAT_NORMAL); ?>" class="input-small" size="10"/>
		        <input type="hidden" name="pre_from" id="booking_arrival_date" value="<?php echo $nearest; ?>" />
		        <button type="button" class="btn button" id="booking_arrival_date_img">
		            <i class="icon-calendar"></i>
		        </button>
	        </div>
	    </div>
	    <button class="btn btn-success button" id="checkAvailability" disabled="disabled">
	        <?php echo JText::_('MOD_BOOKING_CHECK_AVAILABILITY_SEARCH'); ?>
	    </button>
	    <input type="hidden" name="pre_to" value="" />
	</form>
</div>

