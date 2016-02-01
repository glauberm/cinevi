<?php 

/**
 * Book it dialog template.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

JHTML::_('behavior.modal');

defined('_JEXEC') or die('Restricted access');

$config = AFactory::getConfig();

if ($this->userCanReserve) {
	
	ADocument::addDomreadyEvent('Calendars.init();');
	
	$languages['LGSelectCheckIn'] = $start = JText::sprintf('CLICK_IN_S_TO_SELECT_START_DATE_OF_YOUR_BOOKING', ($type = $this->calendar == 'monthly' ? JText::_('CALENDAR') : JText::_('SCHEDULE')));
	$languages['LGSelectCheckOut'] = $end = JText::sprintf('CLICK_IN_S_TO_SELECT_END_DATE_OF_YOUR_BOOKING', $type);
	$languages['LGSelectCheckNext'] = $next = JText::sprintf('IF_YOU_WANT_SELECT_ANOTHER_INTERVAL_CLICK_IN_S_TO_START_DATE_OF_YOUR_BOOKING', $type); 
	ADocument::addLGScriptDeclaration($languages);
	
	?>
	
	<!-- Book it section -->
	
		<div class="bookInterval">
			<a id="calendar"></a>
			<h2><?php echo JText::sprintf('BOOK_THIS_S_NOW', $this->template->name); ?></h2>
			<div class="buttons">
        	<div class="checkInfo checkInfoMessage" id="checkInfo"><?php echo $start; ?></div>
  			<div class="checkTools">
  				<a class="checkButton checkButtonActive" id="selectCheckInDay" href="javascript:Calendars.setOperation(<?php echo CHECK_OP_IN; ?>)">
  					<?php echo JText::_('SELECT_CHECK_IN'); ?></a>
  				<a class="checkButton checkButtonUnactive" id="selectCheckOutDay" href="javascript:Calendars.setOperation(<?php echo CHECK_OP_OUT; ?>)">
  					<?php echo JText::_('SELECT_CHECK_OUT'); ?></a>
  				<a class="checkButton resetButton" id="reset" href="javascript:Calendars.reset()">
  					<?php echo JText::_('RESET'); ?></a>
  				<div class="cleaner"></div>
  			</div>
			</div>
			<?php 
				if ($config->locations) {
					echo AHtml::locations($config->locations == 2, $this->backurl); 
				}
			?>
			<div class="fromTo">
  			<div class="cal">
  				<label for="iFrom"><?php echo JText::_('CHECK_IN'); ?>: </label>
  				<input type="text" name="iFrom" id="iFrom" value="" disabled="disabled" size="16" />
  			</div>
  			<div class="cal">
  				<label for="fTo"><?php echo JText::_('CHECK_OUT'); ?>: </label>
  				<input type="text" name="iTo" id="fTo" value="" disabled="disabled" size="16" />
  			</div>
			</div>
			<div class="cleaner"></div>
			<div class="bookit">
				<a class="checkButton bookitButton" id="bookIt" href="javascript:Calendars.bookIt()">
					<?php echo JText::_('BOOK_IT'); ?></a>
			</div>
		</div>

<?php 
}
?>