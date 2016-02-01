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

function showUserInfo($arrayOfUser){
	$showmessage = AFactory::getConfig()->showNoteInCalendar;
	$data = "";
	foreach($arrayOfUser as $user)
	{
		$data .= $user['name'];
		if($showmessage && $user['message'])
		{
			$data .= ' ('.$user['message'].')';
		}
		$data .= '</br>';
	}
	return $data;
}



BookingHelper::importSlimBox();

$subject = &$this->subject;
/* @var $subject TableSubject */
$user = &JFactory::getUser();
/* @var $user JUser */
$customer = &$this->customer;
/* @var $customer TableCustomer */
$config = &AFactory::getConfig();

$dispatcher	= &JDispatcher::getInstance();
/* @var $dispatcher JDispatcher */

$this->userCanReserve = ($user->authorise('booking.reservation.create', 'com_booking') && !$user->guest) || $this->isAdmin || ($user->guest && !$config->loginBeforeReserving);
/* @var $userCanReserve logged user can reserve objects */

ADocument::addDomreadyEvent('Calendars.onlyOnePrice = ' . ($subject->book_over_timeliness == BOOK_OVER_TIMELINESS_ALLOW ? 'false' : 'true') . ';');

if ($config->displaySubjectBack) {
	if (count($this->parents)) {
		$parent = reset($this->parents);
		$this->backurl = ARoute::view(VIEW_SUBJECTS, $parent->id, $parent->alias);
	} else
		$this->backurl = ARoute::view(VIEW_SUBJECTS);
?>
	<a href="<?php echo JRoute::_($this->backurl); ?>" title="" class="bookit-back button"><?php echo JText::_('BACK'); ?></a>
<?php }	?>

<form name="bookSetting" id="bookSetting" method="post" action="<?php echo JRoute::_(ARoute::view(VIEW_SELECTSUBJECTS, $subject->id, $subject->alias)); ?>#caltop">

	<h1 class="title"><?php echo $this->template->name; ?></h1>
    <div class="info">

<?php 
/*
	if ($config->galleryPosition == 'above') echo $this->loadTemplate('gallery');
?>    
    <div class="head">
<?php 
	$thumb = null;
	if ($config->displayImage && $subject->image) { 
		$ipath = BookingHelper::getIPath($subject->image);
		$thumb = AImage::thumb($ipath, $config->subjectThumbWidth, $config->subjectThumbHeight);
		$slide = AImage::thumb($ipath, $config->galleryPreviewWidth, $config->galleryPreviewHeight);
		if ($thumb) {
?>
			<a href="<?php echo $slide; ?>" title="" rel="lightbox-atomium">
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
	}

	if (!empty($fakeArticle->text) && $config->displaySubjectTextPosition == 'below_image') {
?>    
    	<div class="description"<?php if (!$thumb) echo " style=\"border-width: 0px;\""; ?>><div class="content"><?php echo $fakeArticle->text; ?></div></div>
<?php 
	} 
?>
	<div class="clear"></div>
    </div>
    <?php 
    	if ($config->galleryPosition == 'below') echo $this->loadTemplate('gallery');
    	*/
    ?>
</div>

<a name="calendar"></a>

<?php
/*
	if ($subject->google_maps_display == 'page') { // place google map directly on page
		if ($this->subject->google_maps == 'address') { // search map through real address
			require_once JPATH_COMPONENT_SITE . '/assets/libraries/googlemaps/GoogleMapCurl.php';
			require_once JPATH_COMPONENT_SITE . '/assets/libraries/googlemaps/JSMin.php';
			$googleMap = new GoogleMapCurlAPI();
			$googleMap->width = $this->subject->google_maps_width . 'px';
			$googleMap->height = $this->subject->google_maps_heigth . 'px';
			$googleMap->zoom = $this->subject->google_maps_zoom; 
			$googleMap->addMarkerByAddress($this->subject->google_maps_address);
			$doc = JFactory::getDocument();

			$doc->addCustomTag($googleMap->getHeaderJS() . $googleMap->getMapJS()); // add map js into page head
			echo $googleMap->printOnLoad() . $googleMap->printMap() . $googleMap->printSidebar(); 
		} elseif ($this->subject->google_maps == 'code') // display predefined map
			echo $this->subject->google_maps_code;
	} elseif ($subject->google_maps_display == 'lightbox') { // open map in lightbox
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
					SqueezeBox.open("' . addslashes(JURI::root()) . 'index.php?option=com_booking&amp;view=subject&amp;page=googlemaps&amp;id=' . (int) $subject->id . '&amp;tmpl=component", {handler: "iframe", size: {x: ' . (int) $subject->google_maps_width . ', y: ' . (int) $subject->google_maps_heigth . '}, iframeOptions: {name: "googleMap"}, iframePreload: false}); // open URL at iframe 
					SqueezeBox.asset.name = "googleMap";
				}
			);
		';
		ADocument::addDomreadyEvent($js);
	}
*/
 	if ($config->displayProperties != DISPLAY_PROPERTIES_OFF) { ?>		
		<h2 class="subjectSubtitle"><?php echo JText::_('PROPERTIES'); ?></h2>
<?php		
		$this->propertiesParams = $this->properties->loadParamsToFields();
		$this->displayProperties = $config->displayProperties;
		echo $this->loadTemplate('properties');
		echo $this->loadTemplate('files');		
	}
	
	if (!empty($fakeArticle->text) && $config->displaySubjectTextPosition == 'below_properties') {
?>    
    	<div class="fulltext"><?php echo $fakeArticle->text; ?></div>
<?php 
	} 
	
	if (! $customer->id && ! $user->id && ! $config->unRegisteregCanReserve) {
		$loginRoute = '<a href="' . JRoute::_(ARoute::loginUser()) . '" title="' . JText::_('LOGIN_CUSTOMER') . '">' . JText::_('LOGIN') . '</a>';
		$registrationRoute = '<a href="' . JRoute::_(ARoute::edit(CONTROLLER_CUSTOMER, null, array('startSubjectId' => $this->subject->id))) . '" title="' . JText::_('CREATE_NEW_CUSTOMER_REGISTRATION') . '">' . JText::_('REGISTER') . '</a>'; 
?>
		<div class="mustLoginOrRegister">
			<strong>
				<?php echo sprintf(JText::_('FOR_MAKE_RESERVATION_FIRST_LOGIN_OR_REGISTER'), $loginRoute, $registrationRoute); ?>
			</strong>
		</div>
<?php 
	}
	
	echo $this->loadTemplate('supplements');

	if ($user->id) {
			if(!$customer->id && !$this->isAdmin) {
			// logged user isn't administrator, can become customer
?>
			<strong class="noCustomer"><?php echo JText::_('YOU_ARE_NO_REGISTER_AS_CUSTOMER'); ?></strong>
			<a class="becomeCustomer" href="<?php echo JRoute::_(ARoute::edit(CONTROLLER_CUSTOMER)); ?>" title=""><?php echo JText::_('BECOME_CUSTOMER'); ?></a>
<?php			
		}
	} 

	if($subject->show_contact_form == SUBJECT_SHOW_CALENDAR)
	{
		if ($this->calendar = CTYPE_WEEKLY /*BookingHelper::getCalendarFromRequest($this->templateTable, $subject)*/) {
		 	unset($this->templateTable->calendars[reset(array_keys($this->templateTable->calendars, $this->calendar))]); 
		 	$this->templateTable->calendars = &array_values($this->templateTable->calendars); 
		 	$pcount = count($this->templateTable->calendars); 
		 	/*
		 	if ($pcount) { 
				$scals = &BookingHelper::loadCalendars(); 
	?>
				<div class="calendarsSelect">
					<strong><?php echo JText::_('SWITCH_TO'); ?></strong>
	<?php 
					for ($i = 0; $i < $pcount; $i++) {
					 	$cal = $this->templateTable->calendars[$i]; 
					 	$scal = $scals[$cal]; 
						$url = JRoute::_(ARoute::view(VIEW_SUBJECT, $subject->id, $subject->alias, array('calendar' => $scal->id))); 
	?>
						<span class="<?php echo $i == 0 ? 'first' : ''; ?><?php echo $i == ($pcount - 1) ? 'last' : ''; ?> <?php echo $scal->id; ?>">
							<a href="<?php echo $url; ?>" title=""><?php echo JText::_($scal->title); ?></a>
						</span>
	<?php 
					} 
	?>
			</div>
	<?php 
		 	}*/
		 	
		 	echo $this->loadTemplate('calendar_week');
		 	
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
	if ($this->subject->show_contact_form == SUBJECT_SHOW_CALENDAR){
?>
	<input type="hidden" name="<?php echo SESSION_TESTER; ?>" value="1" />
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="view" value="subject" />
	<input type="hidden" name="task" value="display" />
	<input type="hidden" name="tmpl" value="" />
	<input type="hidden" name="operation" value="<?php echo $this->calendar == CTYPE_MONTHLY ? JRequest::getInt('operation') : ''; ?>" />
	<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>" />
	<?php if(isset($this->setting) && $this->setting){ ?>
	<input type="hidden" name="day" value="<?php echo $this->setting->day; ?>" />
	<input type="hidden" name="month" value="<?php echo $this->setting->month; ?>" />
	<input type="hidden" name="year" value="<?php echo $this->setting->year; ?>" />
	<input type="hidden" name="week" value="<?php echo $this->setting->week; ?>" />
	<?php } ?>
	<input type="hidden" name="lang" value="<?php echo JRequest::getString('lang'); ?>" />
	<input type="hidden" name="ctype" value="<?php echo $this->calendar; ?>" />
	<input type="hidden" name="subject[0]" value="<?php echo $this->subject->id; ?>" />
	<?php if (/*! $config->multipleReservations || $this->calendar == CTYPE_PERIOD*/ TRUE) { ?>
		<input type="hidden" name="boxIds[0]" value="<?php echo $this->calendar == CTYPE_MONTHLY ? JRequest::getString('boxIds') : ''; ?>" />
	<?php } ?>
	<?php }?>
</form>