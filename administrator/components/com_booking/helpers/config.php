<?php

/**
 * Component helper for configuration.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  helpers 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

if (!class_exists('Mobile_Detect'))
	require_once(JPATH_ROOT.DS.'components'.DS.'com_booking'.DS.'assets'.DS.'libraries'.DS.'mobile_detect'.DS.'Mobile_Detect.php');

class BookingConfig
{
    // Main
    var $parentsBookable;
    var $loginBeforeReserving;
    var $enableRegistration;
    var $displayWhoReserve;
    var $whoReserveShowType;
    var $displayMineOnly;
    var $showNoteInCalendar;
    var $moreReservations;
    var $confirmReservation;
    var $prefillReservation;
    var $redirectionAfterReservation;
    var $redirectionAfterReservationMenuItem;
    var $redirectionAfterReservationCustomUrl;
    var $redirectionBackReservation;
    var $customersUsergroup;
    var $templatesIcons;
    var $dateTypeJoomla;
    var $dateLong;
    var $dateNormal;
    var $dateDay;
    var $dateDayShort;
    var $time;
    var $jpgQuality;
    var $pngQuality;
    var $pngFilter;
    var $showOccupancyColumn;
    var $showSupplementsColumn;
    var $showNoteColumn;
    var $showRegistrationUnderLogin;
    var $allowCustomMessage;
    
    // Formating
    var $addressFormat;
    var $addressFormatCustom;
    
    // Design
    var $enableResponsive;
    
    // Prices
    var $usingPrices;
    var $choosePayAmount;
	var $mainCurrency;
    var $lastZero;    
    var $decimals;
	var $decimalsPoint;
    var $thousandSeparator;
    var $priceFormat;
    var $onlinePaymentExpirationTime;
    var $b2b;
    var $taxRates;
    var $showTotalPrice;
    var $showPaymentStatus;
    var $showUnitPrice;
    var $showDepositPrice;
    var $showPriceExcludingTax;
    var $showTax;
    var $useProvisions;
    
    // Calendars
    var $defaultCalendarPageBookable;
    var $firstDaySunday;
    var $quickNavigator;
    var $calendarFutureDays;
    var $disableUnbookableDays;
    var $calendarDeep;
    var $calendarDeepMonth;
    var $calendarNumMonths;
    var $calendarDeepWeek;
    var $calendarNumWeeks;
    var $calendarDeepDay;
    var $showFullWeek;
    var $weekStyle;
    var $timeIntervalStyle;
    var $daysInWeekLayout;
    var $bookCurrentDay;
    var $hideDaysNotBeginFixLimit;
    var $nightsStyle;
    var $hideNotCorrespondingDays;
    var $colorCalendarFieldReserved;
    var $colorCalendarFieldFree;
    var $colorCalendarUnavailable;
    var $colorCalendarBoxReserved;
    
    // Google
    var $googleClientID;
    var $googleClientSecret;
    var $googleDefaultcalendar;
    var $googleEventSummary;
    
    // Frontend - Objects List
    var $listStyle;
    var $showFlagFeatured;
    var $images;
    var $imagesCache;
    var $displayThumbs;
    var $thumbWidth;
    var $thumbHeight;
    var $displayReadmore;
    var $cropReadmore;
    var $readmoreLength;
    var $displayFilter;
    var $displayPagination;
    var $defaultPagination;
    var $displayPaginationSelector;
    var $displaySubjectsProperties;
    var $buttonBookit;
    var $subjectsCalendar;
    var $subjectsCalendarSkin;
    var $subjectsCalendarStart;
    var $subjectsCalendarDeep;
    var $subjectsWeek;
    var $subjectsWeekDeep;
    
    /* Frontend - Object Detail */
    
    var $multipleReservations;
    var $cartPopup;
    var $showCapacity;
    var $displaySubjectBack;
    var $displaySubjectTextPosition;
    var $priceLayout;
    var $showPermanentVolumeDiscount;
    var $showEarlyBookingDiscount;
    
    // Main image
    var $popupType;
    var $displayImage;
    var $subjectThumbWidth;
    var $subjectThumbHeight;
    
    // Gallery
    var $displayGallery;
    var $galleryPosition;
    var $galleryStyle;
    var $galleryThumbWidth;
    var $galleryThumbHeight;
    var $galleryPreviewWidth;
    var $galleryPreviewHeight;
    var $displayProperties;
    var $gallerySlideshowDuration;
    var $gallerySlideshowShift;

    // Mailing
    var $mailingManager;
    var $mailingManagerPhone;
    var $mailingSupplier;
    var $mailingRegistrationClient;
    var $mailingRegistrationManager;
    var $mailingReservationClient;
    var $mailingReservationManager;
    var $mailingReservationSupplier;
    var $mailingStatusClient;
    var $mailingStatusManager;
    var $mailingStatusSupplier;
    var $mailingCancelManager;
    var $mailingCancelSupplier;
    var $mailingChangeSubsubjectOld;
    var $mailingChangeSubsubjectNew;
    var $emailAutoNotificationsBatch;
    var $emailAutoNotificationsFrequency;
    
    // SMS
    var $smsUsername;
    var $smsApikey;
    var $smsUnicode;
    var $smsLocalCountry;
    var $smsLocalCountryCode;
    var $smsLocalNumberCode;
    var $smsLocalNumber;
    
    // Reservations setting
    var $rsTitleBefore;
    var $rsFirstname;
    var $rsMiddlename;
    var $rsSurname;
    var $rsTitleAfter;
    var $rsMoreNames;
    var $rsCompany;
    var $rsStreet;
    var $rsCity;
    var $rsCountry;
    var $rsZip;
    var $rsEmail;
    var $rsTelephone;
    var $rsFax;
    var $rsNote;
    var $rsExtra;
    var $rsCompanyId;
    var $rsVatId;
    var $fieldsPosition;
    
    // Locations
    var $locations;
    var $pickuplocations;
    var $dropofflocations;
    
    // follow up
    var $followupEnabled;
    var $followupSchedule;
    var $followupEmail;

    // period calendar
    var $showRecurrencePatternDaily;
    var $showRecurrencePatternWeekly;
    var $showRecurrencePatternMonthly;
    var $showRecurrencePatternYearly;
    var $showRangeOfRecurrenceNoEndDate;
    var $showRangeOfRecurrenceEndAfter;
    var $showRangeOfRecurrenceEndBy;
    var $multiTimeFrame;
    
    function __construct()
    {
        $this->init();
    }
    
    function getConfigForUser($id)
    {
    	AImporter::helper('user');
    	AUser::$id = $id;
    	$this->init();
    	echo $this->mainCurrency;
    	$default = clone $this;
    	AUser::$id = 0;
    	$this->init();
    	return $default;
    }
    
    function changeConfigForUser($id)
    {
    	AImporter::helper('user');
    	AUser::$id = $id;
    	$this->init();
    }

    function init()
    {
        $user = JFactory::getUser();
        /* @var $user JUser */
        $mainframe = JFactory::getApplication();
        /* @var $mainframe JApplication */
        
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_booking/models');
        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_booking/tables');
        
        $params = JComponentHelper::getParams(OPTION);
        if ($mainframe->isSite()) {
            $menu = $mainframe->getMenu();
            /* @var $menu JMenuSite */
            $active = $menu->getActive();
            if (is_object($active)) {
            	$activeParams = new JRegistry();
            	$activeParams->loadString($active->params);
            	$params->merge($activeParams);
            }
        }
        
        $this->parentsBookable = (int) $params->get('parents_bookable', 0);
        $this->images = $params->get('images', '/images/booking/');
        $this->imagesCache = $params->get('images_cache', 'cache/com_booking/');
        
        $this->templatesIcons = $params->get('templates_icons', '/components/com_booking/assets/images/icons/');
        
        // Design
        $detect = new Mobile_Detect;
        $mobile = ($detect->isMobile() ? true /*($detect->isTablet() ? 'tablet' : 'phone')*/ : false);
        $this->enableResponsive = $params->get('enable_responsive', 0) && $mobile;
        $this->enableJQuery = (int) $params->get('enable_jquery', 0);
        
        // Formating
        $this->addressFormat = (int) $params->get('address_format', 0);;
        $this->addressFormatCustom = JString::trim($params->get('address_format_custom'));
        
        // Prices
        $this->usingPrices = (int) $params->get('using_prices', 2);        
        $this->choosePayAmount = (int) $params->get('choose_pay_amount', 0);        
        $this->mainCurrency = $params->get('main_currency');
        $this->lastZero = (int) $params->get('last_zero', 0);
    	$this->decimals = (int) $params->get('decimals', 2);
		$this->decimalsPoint = $params->get('decimals_point', ',');
		$this->thousandSeparator = '';
    	if ($params->get('thousand_separator', 'space') == 'space')
			$this->thousandSeparator = ' ';
    	elseif ($params->get('thousand_separator', 'space') == 'comma')
    		$this->thousandSeparator = ',';
    	elseif ($params->get('thousand_separator', 'space') == 'point')
    		$this->thousandSeparator = '.';
    	elseif ($params->get('thousand_separator', 'space') == 'char')
    		$this->thousandSeparator = $params->get('thousand_separator_char');
    	$this->priceFormat = (int) $params->get('price_format', 2);
    	$this->onlinePaymentExpirationTime = abs($params->get('online_payment_expiration_time',15));
    	$this->b2b = $params->get('b2b_tax',0);
    	
    	$this->taxRates = array();
    	$taxrates = (array) $params->get('taxrates', array());
    	for ($i = 0; $i < count($taxrates); $i += 2)
    		$this->taxRates[] = array(JArrayHelper::getValue($taxrates, $i) . ' (' . JArrayHelper::getValue($taxrates, $i + 1) . '%)', JArrayHelper::getValue($taxrates, $i + 1));
    		
    	$this->showTotalPrice = (int) $params->get('show_total_price', 1);
    	$this->showPaymentStatus = (int) $params->get('show_payment_status', 1);
    	$this->showUnitPrice = (int) $params->get('show_unit_price', 1);
    	$this->showDepositPrice = (int) $params->get('show_deposit_price', 1);
    	$this->showPriceExcludingTax = (int) $params->get('show_price_excluding_tax', 1);
        $this->showTax = (int) $params->get('show_tax', 1);
        $this->useProvisions = (int) $params->get('use_provisions', 0);
    	
        if ($this->usingPrices == 0) {
        	$this->b2b = 0;
        	$this->showTotalPrice = 0;
        	$this->showPaymentStatus = 0;
        	$this->showUnitPrice = 0;
        	$this->showDepositPrice = 0;
        	$this->showPriceExcludingTax = 0;
        	$this->showTax = 0;
        } elseif ($this->usingPrices == 1) {
        	$this->showDepositPrice = 0;
        }
        
        // Calendars
        $this->defaultCalendarPageBookable = (int) $params->get('default_calendar_page_bookable', 0);
        $this->firstDaySunday = (int) $params->get('first_day', 0);
        $this->quickNavigator = (int) $params->get('quick_navigator', 1);
        $this->calendarFutureDays = $params->get('calendar_future_days', 0);
        $this->disableUnbookableDays = $params->get('disable_unbookable_days', 1);
        $this->calendarDeepMonth = (int) $params->get('calendar_deep_month', 5);
        $this->calendarNumMonths = (int) $params->get('calendar_num_months', 1);
        $this->calendarDeepWeek = (int) $params->get('calendar_deep_week', 20);
        $this->calendarNumWeeks = (int) $params->get('calendar_num_weeks', 1);
        $this->calendarDeepDay = (int) $params->get('calendar_deep_day', 100);
        $this->showFullWeek = (int) $params->get('show_full_week', 0);
        // responsive design for weekStyle is prepared only for old variation of layout (time in every table)
        $this->weekStyle = (int) ($mobile ? 1 : $params->get('week_style', 0));
        $this->timeIntervalStyle = (int) $params->get('time_interval_style', 0);
        $this->daysInWeekLayout = $params->get('days_in_week_layout', array(1, 2, 3, 4, 5, 6, 7, 0));
        $this->bookCurrentDay = (int) $params->get('book_current_day', 0);
        $this->hideDaysNotBeginFixLimit = (int) $params->get('hide_days_not_begin_fix_limit', 0);
        $this->nightsStyle = (int) $params->get('nights_style', 1);
        $this->hideNotCorrespondingDays = (int) $params->get('hide_not_corresponding_days', 0);
        $this->colorCalendarFieldReserved = $params->get('color_calendar_field_reserved', 0);
        $this->colorCalendarFieldFree = $params->get('color_calendar_field_free', 0);
        $this->colorCalendarUnavailable = $params->get('color_calendar_unavailable', 0);
        $this->colorCalendarBoxReserved = $params->get('color_calendar_box_reserved', 0);
        
        if ($this->colorCalendarFieldReserved && JString::strpos($this->colorCalendarFieldReserved, '#') !== 0)
        	 $this->colorCalendarFieldReserved = '#' . $this->colorCalendarFieldReserved; // color picker does not fill color code with #
        
        if ($this->colorCalendarFieldFree && JString::strpos($this->colorCalendarFieldFree, '#') !== 0)
        	 $this->colorCalendarFieldFree = '#' . $this->colorCalendarFieldFree; // color picker does not fill color code with #
        
        if ($this->colorCalendarUnavailable && JString::strpos($this->colorCalendarUnavailable, '#') !== 0)
        	$this->colorCalendarUnavailable = '#' . $this->colorCalendarUnavailable; // color picker does not fill color code with #
        
        if ($this->colorCalendarBoxReserved && JString::strpos($this->colorCalendarBoxReserved, '#') !== 0)
        	 $this->colorCalendarBoxReserved = '#' . $this->colorCalendarBoxReserved; // color picker does not fill color code with #
        
        // Google
        $this->googleClientID = JString::trim($params->get('google_client_id'));
        $this->googleClientSecret = JString::trim($params->get('google_client_secret'));
        $this->googleDefaultcalendar = JString::trim($params->get('google_default_calendar', 'primary'));
        $this->googleEventSummary = (int) $params->get('google_event_summary', 1);
        
        $this->dateTypeJoomla = $params->get('date_type') == 0;
        $this->dateLong = addslashes(JString::trim($params->get('date_long')));
        $this->dateNormal = addslashes(JString::trim($params->get('date_normal')));
        $this->dateDay = addslashes(JString::trim($params->get('date_day')));
        $this->dateDayShort = addslashes(JString::trim($params->get('date_day_short')));
        $this->time = addslashes(JString::trim($params->get('time')));
        $this->jpgQuality = (int) $params->get('jpg_quality', 85);
        $this->pngQuality = (int) $params->get('png_quality', 9);
        $this->pngFilter = (int) $params->get('png_filter');
        
        $this->multipleReservations = (int) $params->get('multiple_reservations', 1);
        $this->buttonPosition = (int) $params->get('button_position', 0);
        $this->cartPopup = (int) $params->get('cart_popup', 1);
        $this->showCapacity = (int) $params->get('show_capacity', 0);
        $this->popupType = JString::trim($params->get('popup'));
        $this->displayImage = (int) $params->get('display_image_subject_detail');
        $this->subjectThumbWidth = (int) $params->get('display_thumbs_subject_detail_width');
        $this->subjectThumbHeight = (int) $params->get('display_thumbs_subject_detail_height');
        
        $this->displayGallery = (int) $params->get('display_gallery_subject_detail');
        $this->galleryPosition = $params->get('display_gallery_subject_position', 'below');
        $this->galleryStyle = $params->get('display_gallery_subject_style', 'slideshow');
        $this->galleryThumbWidth = (int) $params->get('display_gallery_thumbs_subject_detail_width');
        $this->galleryThumbHeight = (int) $params->get('display_gallery_thumbs_subject_detail_height');
        $this->gallerySlideshowDuration = (int) $params->get('gallery_slideshow_duration', 500);
        $this->gallerySlideshowShift = (int) $params->get('gallery_slideshow_shift', 3);
        $this->galleryPreviewWidth = (int) $params->get('display_gallery_preview_subject_detail_width');
        $this->galleryPreviewHeight = (int) $params->get('display_gallery_preview_subject_detail_height');
        
        $this->enableRegistration = (int) $params->get('enable_registration',1);
        $this->displayWhoReserve = (int) $params->get('display_who_reserve',0);
        $this->whoReserveShowType = (int) $params->get('who_reserve_show_type',0);
        $this->displayMineOnly = (int) $params->get('display_mine_only',0);
        $this->showNoteInCalendar = (int) $params->get('show_note_in_calendar',0);
        $this->customersUsergroup = (int) $params->get('customers_usergroup', CUSTOMER_GID);
        $this->loginBeforeReserving = (int) $params->get('login_before_reserving', 0);
        $this->moreReservations = (int) $params->get('more_reservations',1);
        $this->confirmReservation = (int) $params->get('confirm_reservation',0);
        $this->prefillReservation = (int) $params->get('prefill_reservation',1);
        $this->redirectionAfterReservation = (int) $params->get('redirection_after_reservation', 0);
        $this->redirectionAfterReservationMenuItem = (int) $params->get('redirection_after_reservation_menu_item');
        $this->redirectionAfterReservationCustomUrl = JString::trim($params->get('redirection_after_reservation_custom_url'));
        $this->redirectionBackReservation = (int) $params->get('redirection_back_reservation', 2);
        $this->showOccupancyColumn = (int) $params->get('show_occupancy_column', 1);
        $this->showSupplementsColumn = (int) $params->get('show_supplements_column', 0);
        $this->showNoteColumn = (int) $params->get('show_note_column', 0);
        $this->showRegistrationUnderLogin = (int) $params->get('show_registration_under_login', 0);
        $this->allowCustomMessage = (int) $params->get('allow_custom_message', 1);
        
        $this->displaySubjectBack = (int) $params->get('display_subject_back', 1);
        $this->displaySubjectTextPosition = $params->get('display_subject_text_position', 'below_image');
        $this->priceLayout = $params->get('prices_layout', 'detailed_list');
        $this->showPermanentVolumeDiscount = (int) $params->get('show_permanent_volume_discount', 1);
        $this->showEarlyBookingDiscount = (int) $params->get('show_early_booking_discount', 1);
        
        $this->listStyle = (int) $params->get('list_style', 0);
        $this->showFlagFeatured = (int) $params->get('show_flag_featured', 1);
        $this->displayThumbs = (int) $params->get('display_thumbs_subjects_list');
        $this->thumbWidth = (int) $params->get('display_thumbs_subjects_list_width');
        $this->thumbHeight = (int) $params->get('display_thumbs_subjects_list_height');
        $this->displayReadmore = (int) $params->get('display_readmore_subjects_list');
        $this->cropReadmore = (int) $params->get('crop_readmore_subjects_list', 1);
        $this->displaySubjectsProperties = (int) $params->get('subjects_properties', 1);
        $this->readmoreLength = (int) $params->get('display_readmore_subjects_list_length');
        $this->displayProperties = (int) $params->get('display_properties_subject_detail');
        $this->displayFilter = (int) $params->get('subjects_list_filter', 1);
        $this->displayPagination = (int) $params->get('subjects_pagination', 1);
        $this->defaultPagination = (int) $params->get('subjects_pagination_start', 10);
        $this->displayPaginationSelector = (int) $params->get('subjects_pagination_selector', 1);
        $this->buttonBookit = (int) $params->get('button_bookit', 1);
        $this->subjectsCalendar = (int) $params->get('subjects_calendar', 0);
        $this->subjectsCalendarSkin = JString::trim($params->get('subjects_calendar_skin', 'dhx_skyblue'));
        $this->subjectsCalendarStart = (int) $params->get('subjects_calendar_start', 0);
        $this->subjectsCalendarDeep = (int) $params->get('subjects_calendar_deep', 3);
        $this->subjectsWeek = (int) $params->get('subjects_week', 0);
        $this->subjectsWeekDeep = (int) $params->get('subjects_week_deep', 7);
        
        // Mailing
        $this->mailingManager = explode(',', str_replace(';', ',', JString::trim($params->get('mailing_manager', ''))));
        $this->mailingSupplier = explode(',', str_replace(';', ',', JString::trim($params->get('mailing_supplier', ''))));
        $this->mailingManagerPhone = JString::trim($params->get('mailing_phone', ''));
        $this->mailingRegistrationClient = (int) $params->get('mailing_registration_client', 0);
    	$this->mailingRegistrationManager = (int) $params->get('mailing_registration_manager', 0);
    	$this->mailingReservationClient = (int) $params->get('mailing_reservation_client', 0);
    	$this->mailingReservationManager = (int) $params->get('mailing_reservation_manager', 0);
    	$this->mailingReservationSupplier = (int) $params->get('mailing_reservation_supplier', 0);
    	$this->mailingStatusClient = (int) $params->get('mailing_status_client', 0);
    	$this->mailingStatusManager = (int) $params->get('mailing_status_manager', 0);
    	$this->mailingStatusSupplier = (int) $params->get('mailing_status_supplier', 0);
    	$this->mailingCancelManager = (int) $params->get('mailing_cancel_manager', 0);
    	$this->mailingCancelSupplier = (int) $params->get('mailing_cancel_supplier', 0);
        $this->mailingChangeSubsubjectOld = (int) $params->get('mailing_change_subsubject_old', 0);
        $this->mailingChangeSubsubjectNew = (int) $params->get('mailing_change_subsubject_new', 0);
        
    	$this->emailAutoNotificationsBatch = (int) $params->get('email_auto_notifications_batch', 10);
    	$this->emailAutoNotificationsFrequency = (int) $params->get('email_auto_notifications_frequency', 10);
    	
    	// SMS
    	$this->smsUsername = $params->get('sms_username');
    	$this->smsApikey = $params->get('sms_apikey');
    	$this->smsUnicode = (int) $params->get('sms_unicode', 0);
    	$this->smsLocalCountry = $params->get('sms_local_country', '');
    	if ($this->smsLocalCountry) {
    		$parts = explode(',', $this->smsLocalCountry);
    		$this->smsLocalCountryCode = JArrayHelper::getValue($parts, 0);
    		$this->smsLocalNumberCode = JArrayHelper::getValue($parts, 1);
    	}
    	$this->smsLocalNumber = (int) $params->get('sms_local_number', 9);
    	
        // Reservations setting
        $this->rsTitleBefore = (int) $params->get('rs_title_before', 1);
        $this->rsFirstname = (int) $params->get('rs_firstname', 1);
        $this->rsMiddlename = (int) $params->get('rs_middlename', 1);
        $this->rsSurname = (int) $params->get('rs_surname', 1);
        $this->rsTitleAfter = (int) $params->get('rs_title_after', 1);
        $this->rsMoreNames = (int) $params->get('rs_more_names', 1);
        $this->rsCompany = (int) $params->get('rs_company', 1);
        $this->rsStreet = (int) $params->get('rs_street', 1);
        $this->rsCity = (int) $params->get('rs_city', 1);
        $this->rsCountry = (int) $params->get('rs_country', 1);
        $this->rsZip = (int) $params->get('rs_zip', 1);
        $this->rsEmail = (int) $params->get('rs_email', 1);
        $this->rsTelephone = (int) $params->get('rs_telephone', 1);
        $this->rsFax = (int) $params->get('rs_fax', 1);
        $this->rsNote = (int) $params->get('rs_note', 1);
        $this->rsCompanyId = (int) $params->get('rs_company_id', 1);
        $this->rsVatId = (int) $params->get('rs_vat_id', 1);
        $this->fieldsPosition = (int) $params->get('fields_position', 1);
        //FIXME notice is sometimes showing
        $this->rsExtra = unserialize($params->get('fields', ''));
        if (is_array($this->rsExtra))
        	foreach ($this->rsExtra as $i => $field) {
        		$this->rsExtra[$i]['name'] = JFilterOutput::stringURLSafe('fields ' . $field['title']);
        		if (isset($field['options'])) { // drop down list options
        		    $options = json_decode($field['options']);
        		    $options = str_replace(array("\r\n", "\n\r"), "\n", $options); // unit EOL
        		    $options = (array) explode("\n", $options); // split into rows
        		    $options = array_map('JString::trim', $options); // trim rows
        		    $this->rsExtra[$i]['options'] = array_filter($options, 'JString::strlen'); // delete empty rows
        		}
        	}
        
        // Reservation Terms 
        $this->terms_of_contract_accept = (int) $params->get('terms_of_contract_accept', 0);
        $this->terms_of_contract = JModelLegacy::getInstance('Article', 'BookingModel')->getItem(1);
        $this->terms_of_privacy_accept = (int) $params->get('terms_of_privacy_accept', 0);
        $this->terms_of_privacy = JModelLegacy::getInstance('Article', 'BookingModel')->getItem(2);
        
        // Locations
        $this->locations = (int) $params->get('locations', 0);
        $this->pickuplocations = $this->locations ? JModelLegacy::getInstance('Locations', 'BookingModel')->getPickUp() : array();
        $this->dropofflocations = $this->locations ? JModelLegacy::getInstance('Locations', 'BookingModel')->getDropOff() : array();
        
        $this->followupEnabled = (int) $params->get('followup_enabled', 0);
        $this->followupSchedule = JString::trim($params->get('followup_schedule', '1t0d0h10m'));
        $this->followupEmail = (int) $params->get('followup_email', 0);
        
        // period calendar
        $this->showRecurrencePatternDaily = (int) $params->get('show_recurrence_pattern_daily', 1);
        $this->showRecurrencePatternWeekly = (int) $params->get('show_recurrence_pattern_weekly', 1);
        $this->showRecurrencePatternMonthly = (int) $params->get('show_recurrence_pattern_monthly', 0);
        $this->showRecurrencePatternYearly = (int) $params->get('show_recurrence_pattern_yearly', 0);
        $this->showRangeOfRecurrenceNoEndDate = (int) $params->get('show_range_of_recurrence_no_end_date', 0);
        $this->showRangeOfRecurrenceEndAfter = (int) $params->get('show_range_of_recurrence_end_after', 1);
        $this->showRangeOfRecurrenceEndBy = (int) $params->get('show_range_of_recurrence_end_by', 1);
        $this->multiTimeFrame = (int) $params->get('multi_time_frame', 0);
    }
}

?>
