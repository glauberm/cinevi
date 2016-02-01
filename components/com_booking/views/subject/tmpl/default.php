<?php

/**
 * Subject detail template.
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

/* @var $this BookingViewSubject */


JHTML::_('behavior.calendar');
AHtml::monthYearPicker();

$subject = &$this->subject;
/* @var $subject TableSubject */
$user = &JFactory::getUser();
/* @var $user JUser */
$customer = &$this->customer;
/* @var $customer TableCustomer */
$config = &AFactory::getConfig();

if ($config->enableResponsive && IS_SITE) {
	if ($config->enableJQuery == 1) {
		if (!ISJ3)
			JHTML::script(JQUERY_BASE.'jquery.min.js');
		else
			JHTML::_('jquery.framework');
	}
	JHTML::script('components/'.OPTION.'/assets/libraries/jquery/jquery.nicescroll.min.js');
	AImporter::js('responsive');
}

if ($config->popupType == 'shadowbox') {
	BookingHelper::importShadowbox();
} else {
	BookingHelper::importSlimBox();
}

$dispatcher	= &JDispatcher::getInstance();
/* @var $dispatcher JDispatcher */

$this->userCanReserve = ($user->authorise('booking.reservation.create', 'com_booking') && !$user->guest) || $this->isAdmin || ($user->guest && !$config->loginBeforeReserving);
/* @var $userCanReserve logged user can reserve objects */

ADocument::addDomreadyEvent('Calendars.onlyOnePrice = ' . ($subject->book_over_timeliness == BOOK_OVER_TIMELINESS_ALLOW ? 'false' : 'true') . ';');
ADocument::addDomreadyEvent('Calendars.cartPopup = ' . ($config->cartPopup ? 'true' : 'false') . ';');
ADocument::addDomreadyEvent('Calendars.highlightBoxes = ' . ($config->enableResponsive ? 'false' : 'true') . ';');
ADocument::addDomreadyEvent('Calendars.enabledResponsive = ' . ($config->enableResponsive ? 'true' : 'false') . ';');
ADocument::addDomreadyEvent('Calendars.nightBooking = ' . ($subject->night_booking && !$config->nightsStyle ? 'true' : 'false') . ';');
ADocument::addDomreadyEvent('Calendars.mode = "' . $this->mode . '";');
    ADocument::addDomreadyEvent('Calendars.init(' . ($config->multipleReservations ? 'true' : 'false') . ');');

if ($config->displaySubjectBack && $this->mode != 'change') {
	if (count($this->parents)) {
		$parent = reset($this->parents);
		$this->backurl = ARoute::view(VIEW_SUBJECTS, $parent->id, $parent->alias);
	} else
		$this->backurl = ARoute::view(VIEW_SUBJECTS);
?>
	<a href="<?php echo JRoute::_($this->backurl); ?>" title="" class="bookit-back button"><?php echo JText::_('BACK'); ?></a>
<?php }	?>

<?php if ($this->subject->show_contact_form == SUBJECT_SHOW_CALENDAR) { ?>
	<form name="bookSetting" id="bookSetting" method="post" action="<?php echo JRoute::_(ARoute::view(VIEW_SUBJECT, $subject->id, $subject->alias)); ?>#caltop">
<?php } else { ?>
	<div id="bookSetting">
<?php } ?>

	<h1 class="title">
        <?php if ($user->authorise('booking.reservation.edit.item', 'com_booking') && $this->mode == 'change' && !empty($this->changeableItems)) {
            echo JHtml::_('select.genericlist', $this->changeableItems, 'id', 'onchange="ViewReservation.reloadChangeItem(this.value)"', 'id', 'text', $this->subject->id);
        } else {
            echo $subject->title; 
        } ?>
    </h1>
    <?php $noOfImages = count(BookingHelper::getSubjectImages($this->subject)); ?>
    <div class="info<?php if (
    		(!$config->displayImage || !$subject->image) &&
    		($config->displaySubjectTextPosition != 'below_image') &&
			(!$config->displayGallery || $noOfImages < 1) && $this->mode != 'change'
    ) { ?> info-off<?php } ?>">
<?php 
	if ($config->galleryPosition == 'above' && $this->mode != 'change') echo $this->loadTemplate('gallery');

	if ((($config->displayImage && $subject->image) || ($subject->introtext || $subject->fulltext) && ($config->displaySubjectTextPosition == 'below_image')) && $this->mode != 'change') {
?>    
    <div class="head">
<?php 
	}
	
	$thumb = null;
	if ($config->displayImage && $subject->image && $this->mode != 'change') { 
		$ipath = BookingHelper::getIPath($subject->image);
		$thumb = AImage::thumb($ipath, $config->subjectThumbWidth, $config->subjectThumbHeight);
		$slide = AImage::thumb($ipath, $config->galleryPreviewWidth, $config->galleryPreviewHeight);
		if ($thumb) {
?>
			<a class="main-image" href="<?php echo $slide; ?>" title="" rel="<?php echo ($config->popupType == 'shadowbox') ? 'shadowbox' : 'lightbox-atomium'; ?>">
				<img src="<?php echo $thumb; ?>" alt="" class="subjectImage" />
			</a>
		  <div class="clearLeft"></div>
<?php 
		}
	} 
 
	if ($subject->introtext || $subject->fulltext) { 
		
		$fakeArticle = new stdClass();
		$fakeArticle->text = JString::trim($subject->introtext . ' ' . $subject->fulltext);
		$fakeParams = new JRegistry();
		$fakeLimitstart = 0;
		JPluginHelper::importPlugin('content');
		$results = $dispatcher->trigger('onPrepareContent', array (&$fakeArticle, &$fakeParams, $fakeLimitstart));
		$results = $dispatcher->trigger('onContentPrepare', array ('com_booking.subject', &$fakeArticle, &$fakeParams, $fakeLimitstart));
	}

	if (!empty($fakeArticle->text) && $config->displaySubjectTextPosition == 'below_image' && $this->mode != 'change') {
?>    
    	<div class="description"<?php if (!$thumb) echo " style=\"border-width: 0px;\""; ?>><div class="content"><?php echo $fakeArticle->text; ?></div></div>
<?php 
	} 

	if (($config->displayImage && $subject->image) || ($subject->introtext || $subject->fulltext) && ($config->displaySubjectTextPosition == 'below_image') && $this->mode != 'change') {
?>
	<div class="clear"></div>
    </div>
    <?php 
	}
	
    	if ($config->galleryPosition == 'below' && $this->mode != 'change') echo $this->loadTemplate('gallery');
    ?>
</div>

<a name="calendar"></a>

<?php
	if ($subject->google_maps_display == 'page' && $this->mode != 'change') { // place google map directly on page
		if ($this->subject->google_maps == 'address') { // search map through real address
			require_once JPATH_COMPONENT_SITE . '/assets/libraries/googlemaps/GoogleMapCurl.php';
			require_once JPATH_COMPONENT_SITE . '/assets/libraries/googlemaps/JSMin.php';
			$googleMap = new GoogleMapCurlAPI();
			$googleMap->width = $this->subject->google_maps_width . 'px';
			$googleMap->height = $this->subject->google_maps_heigth . 'px';
			$googleMap->zoom = $this->subject->google_maps_zoom; 
			$googleMap->addMarkerByAddress($this->subject->google_maps_address);
			$doc = JFactory::getDocument();
			/* @var $doc JDocumentHTML */
			$doc->addCustomTag($googleMap->getHeaderJS() . $googleMap->getMapJS()); // add map js into page head
			echo $googleMap->printOnLoad() . $googleMap->printMap() . $googleMap->printSidebar(); 
		} elseif ($this->subject->google_maps == 'code') // display predefined map
			echo $this->subject->google_maps_code;
	} elseif ($subject->google_maps_display == 'lightbox' && $this->mode != 'change') { // open map in lightbox
		if ($subject->google_maps == 'code') {
			$match = array();
			if (preg_match('/width="(\d+)" height="(\d+)"/', $subject->google_maps_code, $match)) { // parse map size from predefined code to set ligthbox
				$subject->google_maps_width = $match[1];
				$subject->google_maps_heigth = $match[2];
			}
		}
		$subject->google_maps_width += 40; // add border to predefined sizes
		$subject->google_maps_heigth += 60;
	?>
		<span id="googlemap">&nbsp;</span>
	<?php		
		// start lightbox	
		$js = ' 
			document.id("googlemap").addEvent("click", 
				function() { // open lightbox after click on togler
					SqueezeBox.$events["close"] = SqueezeBox.$events["open"] = []; // disable all events
					SqueezeBox.initialize(); // reset object	
					SqueezeBox.open("' . addslashes(JRoute::_('index.php?option=com_booking&view=subject&page=googlemaps&id=' . (int) $subject->id . '&tmpl=component')) . '", {handler: "iframe", size: {x: ' . (int) $subject->google_maps_width . ', y: ' . (int) $subject->google_maps_heigth . '}, iframeOptions: {name: "googleMap"}, iframePreload: false}); // open URL at iframe 
					SqueezeBox.asset.name = "googleMap";
				}
			);
		';
		ADocument::addDomreadyEvent($js);
	}

 	if ($config->displayProperties != DISPLAY_PROPERTIES_OFF && $this->mode != 'change') { ?>		
 	<div class="properties-block<?php if (
    		(!$config->displayImage || !$subject->image) &&
    		($config->displaySubjectTextPosition != 'below_image') &&
			(!$config->displayGallery || $noOfImages < 1)
    ) { ?> full-width<?php } ?>">		
		<h2 class="subjectSubtitle"><?php echo JText::_('PROPERTIES'); ?></h2>
<?php		
		$this->propertiesParams = $this->properties->loadParamsToFields();
		$this->displayProperties = $config->displayProperties;
		echo $this->loadTemplate('properties');
		echo $this->loadTemplate('files');
?>
	</div>
<?php		
	}
	
	if (!empty($fakeArticle->text) && $config->displaySubjectTextPosition == 'below_properties' && $this->mode != 'change') {
?>    
    	<div class="fulltext<?php if (
    		(!$config->displayImage || !$subject->image) &&
    		($config->displaySubjectTextPosition != 'below_image') &&
			(!$config->displayGallery || $noOfImages < 1)
    ) { ?> full-width<?php } ?>"><?php echo $fakeArticle->text; ?></div>
<?php 
	} 
	
	if (!$this->userCanReserve) {
?>
		<div class="mustLoginOrRegister">
			<strong>
<?php 				
				if ($user->guest && $config->loginBeforeReserving){
					if($config->enableRegistration)
						echo sprintf(JText::_('FOR_MAKE_RESERVATION_FIRST_LOGIN_OR_REGISTER'), JHtml::link(JRoute::_(ARoute::loginUser()), JText::_('LOGIN')), JHtml::link(JRoute::_(ARoute::edit(CONTROLLER_CUSTOMER, null, array('startSubjectId' => $this->subject->id))), JText::_('REGISTER')));
					else
						echo sprintf(JText::_('FOR_MAKE_RESERVATION_FIRST_LOGIN'), JHtml::link(JRoute::_(ARoute::loginUser()), JText::_('LOGIN')));					
				}
				else
					echo JText::_('YOU_USERGROUP_CANNOT_MAKE_RESERVATION');
?>
			</strong>
		</div>
<?php 
	}

    if ($this->mode != 'change')
        echo $this->loadTemplate('supplements');

	if($subject->show_contact_form == SUBJECT_SHOW_CALENDAR)
	{
		if ($this->calendar = BookingHelper::getCalendarFromRequest($this->templateTable, $subject)) {
		 	unset($this->templateTable->calendars[reset(array_keys($this->templateTable->calendars, $this->calendar))]); 
		 	$this->templateTable->calendars = &array_values($this->templateTable->calendars); 
		 	$pcount = count($this->templateTable->calendars); 
		 	if ($pcount && $this->mode != 'change') { 
				$scals = &BookingHelper::loadCalendars(); 
	?>
				<div class="calendarsSelect" id="calendarsSelect">
					<!--AJAX_calendarsSelect-->
					<strong><?php echo JText::_('SWITCH_TO'); ?></strong>
	<?php 
					for ($i = 0; $i < $pcount; $i++) {
					 	$cal = $this->templateTable->calendars[$i];
					 	if ($cal == 'weekly_multi') // TODO still back-end only, not front-end yet
					 		continue;
					 	$scal = $scals[$cal]; 
						$url = JRoute::_(ARoute::view(VIEW_SUBJECT, $subject->id, $subject->alias, array('calendar' => $scal->id))); 
	?>
						<span class="<?php echo $i == 0 ? 'first' : ''; ?><?php echo $i == ($pcount - 1) ? 'last' : ''; ?> <?php echo $scal->id; ?>">
							<a href="javascript:Calendars.requestNavigation('<?php echo $url; ?>')" title=""><?php echo JText::_($scal->title); ?></a>
						</span>
	<?php 
					} 
	?>
					<!--/AJAX_calendarsSelect-->
				</div>
	<?php 
		 	}
		 	
		 	echo $this->loadTemplate('calendar_' . $this->calendar);
		 	
		}
	}
	else
	{
		$javascript ="
 		SqueezeBox.assign($$('bookIt'), {
		size: {x: 300, y: 400},
		ajaxOptions: {
			method: 'get' // we use GET for requesting plain HTML (you can skip it, it is the default value)
			}
		});";
		//JFactory::getDocument()->addScriptDeclaration($javascript);
		//echo '<div class="bookit"><a class="checkButton bookitButton" id="bookIt" href="'.JRoute::_('index.php?view=subject&task=').'">'.JText::_('BOOK_IT').'</a></div>';
		echo $this->loadTemplate('contact_form');
	}
	/* @var $setting BookingCalendarSetting */
	if ($this->subject->show_contact_form == SUBJECT_SHOW_CALENDAR) {
?>
		<div id="formFoot">
			<!--AJAX_formFoot-->
				<input type="hidden" name="<?php echo SESSION_TESTER; ?>" value="1" />
				<input type="hidden" name="controller" value="" />
				<input type="hidden" name="view" value="subject" />
				<input type="hidden" name="task" value="display" />
                <input type="hidden" name="tmpl" value="<?php echo $this->tmpl; ?>" />
                <input type="hidden" name="mode" value="<?php echo $this->mode; ?>" />
                <input type="hidden" name="changed_reservation_item_id" value="<?php echo $this->changedReservationItemId; ?>" />
				<input type="hidden" name="operation" value="<?php echo $this->calendar == CTYPE_MONTHLY ? JRequest::getInt('operation') : ''; ?>" />
				<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>" />
				<?php if (isset($this->setting) && $this->setting) {
                                        if (!empty($this->setting->day)) { ?>
                                            <input type="hidden" name="day" value="<?php echo $this->setting->day; ?>" />
                                        <?php }
                                        if (!empty($this->setting->month)) { ?>
                                            <input type="hidden" name="month" value="<?php echo $this->setting->month; ?>" />
                                        <?php }
                                        if (!empty($this->setting->year)) { ?>
                                            <input type="hidden" name="year" value="<?php echo $this->setting->year; ?>" />
                                        <?php }
                                        if (!empty($this->setting->week)) { ?>
                                            <input type="hidden" name="week" value="<?php echo $this->setting->week; ?>" />
                                        <?php }
				} ?>
				<input type="hidden" name="lang" value="<?php echo JRequest::getString('lang'); ?>" />
				<input type="hidden" name="ctype" value="<?php echo $this->calendar; ?>" />
				<input type="hidden" name="subject[0]" value="<?php echo $this->subject->id; ?>" />
                <?php if (JRequest::getString('pre_from')) { ?>
                    <input type="hidden" name="boxIds[0]" value="<?php echo implode(',', (array) JRequest::getVar('boxIds')); ?>" />
                <?php } elseif ((! $config->multipleReservations || $this->calendar == CTYPE_PERIOD)) { ?>
					<input type="hidden" name="boxIds[0]" value="<?php echo $this->calendar == CTYPE_MONTHLY ? reset(ARequest::getStringArray('boxIds', true)) : ''; ?>" />
				<?php } ?>
			<!--/AJAX_formFoot-->
		</div>
	</form>
<?php } else { ?>
	</div>
<?php } 
$languages['LGSelectCheckIn'] = JText::sprintf('CLICK_IN_S_TO_SELECT_START_DATE_OF_YOUR_BOOKING', ($type = $this->calendar == 'monthly' ? JText::_('CALENDAR') : JText::_('SCHEDULE')));
$languages['LGSelectCheckOut'] = JText::sprintf('CLICK_IN_S_TO_SELECT_END_DATE_OF_YOUR_BOOKING', $type);
$languages['LGSelectCheckNext'] = JText::sprintf('IF_YOU_WANT_SELECT_ANOTHER_INTERVAL_CLICK_IN_S_TO_START_DATE_OF_YOUR_BOOKING', $type); 
ADocument::addLGScriptDeclaration($languages);
?>
