<?php 

/**
 * Monthly calendar to show all reservations.
 * 
 * @package		ARTIO Booking
 * @subpackage  	views
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

/* @var $this BookingViewSubject */

$this->userCanReserve = false;
$languages['LGSelectCheckIn'] = $languages['LGSelectCheckOut'] = $languages['LGSelectCheckNext'] = '';
ADocument::addLGScriptDeclaration($languages);

?>
<form name="bookSetting" id="bookSetting" method="post" action="<?php echo JRoute::_(ARoute::view(VIEW_SUBJECT, $subject->id, $subject->alias, array('layout' => 'manager'))); ?>">
    <?php echo $this->loadTemplate('calendar_monthly'); ?>
	<div id="formFoot">
		<!--AJAX_formFoot-->
		<input type="hidden" name="<?php echo SESSION_TESTER; ?>" value="1" />
		<input type="hidden" name="controller" value="" />
		<input type="hidden" name="view" value="subject" />
		<input type="hidden" name="task" value="display" />
		<input type="hidden" name="tmpl" value="" />
		<input type="hidden" name="operation" value="" />
		<input type="hidden" name="selectCheckInDay" id="selectCheckInDay" value="" />
		<input type="hidden" name="selectCheckOutDay" id="selectCheckOutDay" value="" />
		<input type="hidden" name="checkInfo" id="checkInfo" value="" />
		<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>" />
		<?php if (!empty($this->setting)) { ?>
			<input type="hidden" name="month" value="<?php echo $this->setting->month; ?>" />
			<input type="hidden" name="year" value="<?php echo $this->setting->year; ?>" />
		<?php } ?>
		<input type="hidden" name="lang" value="<?php echo JRequest::getString('lang'); ?>" />
		<input type="hidden" name="ctype" value="monthly" />
		<input type="hidden" name="subject[0]" value="<?php echo $this->subject->id; ?>" />
		<!--/AJAX_formFoot-->
	</div>
</form>