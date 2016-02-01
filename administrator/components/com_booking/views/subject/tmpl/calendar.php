<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	models
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

/* @var $this BookingViewSubject */

AImporter::js('calendars', 'supplements');
$config = AFactory::getConfig();
/* @var $config BookingConfig */

JToolBarHelper::title(JText::_('CALENDAR') . ': ' . $this->subject->title);
JToolbarHelper::back('JTOOLBAR_BACK', ARoute::view(VIEW_SUBJECTS));

$this->userCanReserve = true;
$this->calendar = BookingHelper::getCalendarFromRequest($this->templateTable, $this->subject);

$this->addTemplatePath(JPATH_COMPONENT_SITE.'/views/subject/tmpl');
$this->setLayout('default'); 
$calendars = BookingHelper::loadCalendars(); 

ADocument::addDomreadyEvent('Calendars.onlyOnePrice = ' . ($this->subject->book_over_timeliness == BOOK_OVER_TIMELINESS_ALLOW ? 'false' : 'true') . ';');
ADocument::addDomreadyEvent('Calendars.cartPopup = ' . ($config->cartPopup ? 'true' : 'false') . ';');
ADocument::addDomreadyEvent('Calendars.highlightBoxes = ' . ($config->enableResponsive ? 'false' : 'true') . ';');
ADocument::addDomreadyEvent('Calendars.enabledResponsive = ' . ($config->enableResponsive ? 'true' : 'false') . ';');
ADocument::addDomreadyEvent('Calendars.nightBooking = ' . ($this->subject->night_booking && !$config->nightsStyle ? 'true' : 'false') . ';');
ADocument::addDomreadyEvent('Calendars.init(' . ($config->multipleReservations ? 'true' : 'false') . ');');

?>
<form id="bookSetting" action="index.php#bookSetting" method="post" name="bookSetting">
	<label for="calendar"><?php echo JText::_('CALENDAR'); ?>: </label>
	<select name="calendar" id="calendar" onchange="this.form.submit()" autocomplete="off"> 
		<?php foreach($this->templateTable->calendars as $calendar) {
			  	if ($calendar != 'weekly_multi' || $this->getModel()->haveChilds($this->subject->id)) { // parent item has multi view only ?>
					<option value="<?php echo $calendar; ?>"<?php if ($this->calendar == $calendar) { ?> selected="selected"<?php } ?>><?php echo JText::_($calendars[$calendar]->title); ?></option>
			<?php }
		} ?>
	</select>
	<?php echo $this->loadTemplate('supplements');
		echo $this->loadTemplate('calendar_' . $this->calendar); ?>
	<input type="hidden" name="option" value="com_booking" />
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tmpl" value="" />
	<input type="hidden" name="layout" value="calendar" />
	<input type="hidden" name="view" value="subject" />
	<input type="hidden" name="Itemid" value="" />
	<input type="hidden" name="id" value="<?php echo $this->subject->id; ?>" />
	<input type="hidden" name="operation" value="<?php echo $this->calendar == CTYPE_MONTHLY ? JRequest::getInt('operation') : ''; ?>" />
	<input type="hidden" name="day" value="<?php echo $this->setting->day; ?>" />
	<input type="hidden" name="month" value="<?php echo $this->setting->month; ?>" />
	<input type="hidden" name="year" value="<?php echo $this->setting->year; ?>" />
	<input type="hidden" name="week" value="<?php echo $this->setting->week; ?>" />
	<input type="hidden" name="lang" value="<?php echo JRequest::getString('lang'); ?>" />
	<input type="hidden" name="ctype" value="<?php echo $this->calendar; ?>" />
	<input type="hidden" name="subject[0]" value="<?php echo $this->subject->id; ?>" />
	<?php if (! $config->multipleReservations || $this->calendar == CTYPE_PERIOD) { ?>
		<input type="hidden" name="boxIds[0]" value="<?php echo $this->calendar == CTYPE_MONTHLY ? JRequest::getString('boxIds') : ''; ?>" />
	<?php } ?>
</form>