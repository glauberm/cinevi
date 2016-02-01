<?php

/**
 * Component helper
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

class BookingHelper
{

		/**
     * Get Booking info from xml
     * 
     */
		function getBookingInfo()
		{
			static $info;
			if( !isset($info) ) {
				$info = array();
	      
				//$xml = JFactory::getXMLParser('Simple');
				//$xml = simplexml_load_file($data, $class)
	
				$xmlFile = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_booking' . DS . 'booking.xml';
				$xml = simplexml_load_file($xmlFile);
	
				if (file_exists($xmlFile)) {
					$element = & $xml->version;
					$info['version'] = $element ? $element : '';

					$element = & $xml->creationdate;
					$info['creationDate'] = $element ? $element : '';

					$element = & $xml->author;
					$info['author'] = $element ? $element : '';

					$element = & $xml->authoremail;
					$info['authorEmail'] = $element ? $element : '';

					$element = & $xml->authorurl;
					$info['authorUrl'] = $element ? $element : '';

					$element = & $xml->copyright;
					$info['copyright'] = $element ? $element : '';

					$element = & $xml->license;
					$info['license'] = $element ? $element : '';

					/*$element = & $xml->description;
					$info['description'] = $element ? $element : '';*/

					$element = & $xml->forum;
					$info['forum'] = $element ? $element : '';

					$element = & $xml->paidsupport;
					$info['paidsupport'] = $element ? $element : '';

					$element = & $xml->productpage;
					$info['productpage'] = $element ? $element : '';
					
					$element = & $xml->documentation;
					$info['documentation'] = $element ? $element : '';
					
					$element = & $xml->faq;
					$info['faq'] = $element ? $element : '';
					
					$element = & $xml->video;
					$info['video'] = $element ? $element : '';
				}
			}
	
			return $info;
		}
		
		/**
     * Set pages submenus.
     * 
     * @param $set
     */
    function setSubmenu($set)
    {
    	AImporter::helper('user');
    	 
    	JSubMenuHelper::addEntry(JText::_('CONTROL_PANEL'), ARoute::root(), $set == 0);
    	if (JFactory::getUser()->authorise('booking.reservations.manage', 'com_booking'))
    		JSubMenuHelper::addEntry(JText::_('RESERVATIONS'), ARoute::view(VIEW_RESERVATIONS), $set == 3);
    	if (JFactory::getUser()->authorise('booking.item.manage', 'com_booking')) {
    		JSubMenuHelper::addEntry(JText::_('BOOKABLE_ITEMS'), ARoute::view(VIEW_SUBJECTS), $set == 1);
    		JSubMenuHelper::addEntry(JText::_('TEMPLATES'), ARoute::view(VIEW_TEMPLATES), $set == 5);
    		JSubMenuHelper::addEntry(JText::_('ADMINS'), ARoute::view(VIEW_ADMINS), $set == 6);
    	}
    	if (JFactory::getUser()->authorise('booking.view.customers', 'com_booking'))
    		JSubMenuHelper::addEntry(JText::_('CUSTOMERS'), ARoute::view(VIEW_CUSTOMERS), $set == 2);
    }

    /**
     * Get subjects model class for support database operations
     * 
     * @return BookingModelSubjects
     */
    function getSubjectsModel()
    {
        if (! class_exists('BookingModelSubjects')) {
            AImporter::model('subjects');
        }
        return new BookingModelSubjects();
    }

    /**
     * Get selectbox to choose parent in editing form.
     * 
     * @param int $select selected parent
     * @param int $ignore parent id whitch mustnt't in list
     * @return string HTML code
     */
    function getParentsSubjectSelectBox($select, $ignore)
    {
        $model = BookingHelper::getSubjectsModel();
        $parents = $model->loadShortListByIds();
        $code = BookingHelper::getSubjectParentSelectBox('parent', 'ROOT', $parents, array($ignore), $select, false, ' class="fullWidth" size="10" ');
        $code = str_replace(array('<sup>' , '</sup>'), '', $code);
        return $code;
    }

    /**
     * Get selectbox to choose subject in tree format.
     * 
     * @param $select selected value
     * @param $field field name
     * @param $autoSubmit auto submit form on change
     * @return string HTML code
     */
    function getSubjectSelectBox($select, $field = 'subject', $autoSubmit = false, $parent = null)
    {
        $model = BookingHelper::getSubjectsModel();
        $lists = array('limit' => null , 'limitstart' => null , 'state' => null , 'access' => null , 'order' => 'ordering' , 'order_Dir' => 'ASC' , 'search' => null , 'parent' => $parent , 'template' => null);
        $model->init($lists);
        $fullList = $model->getFullList();
        $fullList = ATree::getListTree($fullList);
        foreach ($fullList as $node) {
            $node->disable = $parent && $node->id == $parent;
            $node->treename = str_replace(array('<sup>' , '</sup>'), '', $node->treename);
        }
        return AHtml::getFilterSelect($field, 'SELECT_OBJECT', $fullList, $select, $autoSubmit, '', 'id', 'treename');
    }

    /**
     * Get filter of subjects parents
     * 
     * @param int $select selected option
     * @param boolean $autoSubmit sign if is filter on list or edit form field
     * @return string HTML code
     */
    function getParentsSubjectFilter($select)
    {
        $model = BookingHelper::getSubjectsModel();
        $parents = $model->loadParents();
        $parents = $model->loadShortListByIds($parents);
        return BookingHelper::getSubjectParentSelectBox('filter_parent', 'SELECT_PARENT', $parents, array(), $select);
    }

    /**
     * Get selectbox contains parent to filter or choose in editing
     * 
     * @param string $name field name
     * @param string $noSelectText string of zero option
     * @param array $parents parent IDs
     * @param array $ignore ID of parents which mustnt't in list
     * @param int $select selected parent
     * @param boolean $autoSubmit add autosubmit form javascript
     * @param string $customParams additional params
     * @return string HTML code
     */
    function getSubjectParentSelectBox($name, $noSelectText, $parents, $ignore, $select, $autoSubmit = true, $customParams = '')
    {
        $mark1 = '&#160;';
        $mark2 = '_-_N-B-S-P_-_';
        foreach (($tree = ATree::getListTree($parents)) as $list)
            $list->treename = str_replace(array($mark1 , '<sup>' , '</sup>'), array($mark2 , '' , ''), $list->treename);
        return str_replace($mark2, $mark1, AHtml::getFilterSelect($name, $noSelectText, $tree, $select, $autoSubmit, $customParams, 'id', 'treename'));
    }

    /**
     * Get subject ordering selectbox to editing subject form
     * 
     * @param TableSubject $subject edited subject
     * @return string HTML code
     */
    function getSubjectOrderingSelectBox($subject)
    {
        $model = BookingHelper::getSubjectsModel();
        $query = $model->getLoadOrderingQuery($subject);
        return JHTML::_('list.ordering', $subject, $query);
    }

    /**
     * Import time picker library
     */
    function importTimePicker()
    {
        JHTML::script(TIMEPICKER_BASE.'nogray_time_picker.js');
        ADocument::addScriptPropertyDeclaration('timePickers', 'new Array()', false, false);
        ADocument::addScriptPropertyDeclaration('timePickerImages', TIME_PICKER_IMAGES);
        ADocument::addScriptPropertyDeclaration('dateFormat', ADATE_FORMAT_NORMAL_CAL);
        ADocument::addScriptPropertyDeclaration('timePickerToggler', IMAGES . 'icon-16-clock.png');
    }
    
    /**
     * Import base CSS and JS of DHTMLX calendar. Import selected or default skin.
     */
    function importDHTMLXCalendar()
    {
    	$config = AFactory::getConfig();
    	/* @var $config BookingConfig */
    	JHTML::script(DHTMLX_BASE.'dhtmlxcalendar.js'); JHTML::stylesheet(DHTMLX_BASE.'dhtmlxcalendar.css');
    	if ($config->subjectsCalendarSkin == 'dhx_web') JHTML::stylesheet(DHTMLX_SKIN.'dhtmlxcalendar_dhx_web.css');
    	elseif ($config->subjectsCalendarSkin == 'omega') JHTML::stylesheet(DHTMLX_SKIN.'dhtmlxcalendar_omega.css');
    	else JHTML::stylesheet(DHTMLX_SKIN.'dhtmlxcalendar_dhx_skyblue.css');
    }

    /**
     * Import SlimBox library.
     */
    function importSlimBox()
    {
    	self::upgradeMootools125();
    	
        JHTML::script(SLIMBOX_BASE . 'js/' . 'slimbox.js');
        JHTML::stylesheet(SLIMBOX_BASE . 'css/' . 'slimbox.css');
        //JHTML::_('behavior.mootools');
        JHTML::_('behavior.framework');
    }
    
    /**
     * Import ShadowBox library.
     */
    function importShadowBox()
    {
    	self::upgradeMootools125();
    	 
    	JHTML::script(SHADOWBOX_BASE . 'shadowbox.js');
    	JHTML::stylesheet(SHADOWBOX_BASE . 'shadowbox.css');
    	
    	$document = &JFactory::getDocument();
		$document->addScriptDeclaration('
			window.addEvent(\'domready\', function(){
				Shadowbox.init();
			});
		');
		
    	JHTML::_('behavior.framework');
    }
    
    /**
     * Upgrade included Squeezebox to newer version (for J!1.5)
     */
    function upgradeModal()
    {
    	static $imported;
    	if (!$imported) {
    		if (!ISJ16) {
    			BookingHelper::upgradeMootools125();
    			//remove old modal loaded
				AImporter::removeScripts('modal.js','modal-uncompressed.js');
				AImporter::removeStyleSheets('modal.css');
				//add upgraded one 
				JHTML::script(SQUEEZEBOX_BASE . 'js/' . 'modal.js'); 
				JHTML::stylesheet(SQUEEZEBOX_BASE . 'css/' . 'modal.css');
    		}
    	    $imported=true;
    	}
    }
    
    /**
     * Upgrades to Mootools 1.2.5, if J!1.5 and not using mtupgrade plugin
     * @params bool $more import Mootools more too
     */
    function upgradeMootools125($more = false)
    {
    	static $imported;
    	if (!$imported) {
    		if (!ISJ16 && !JPluginHelper::getPlugin('system','mtupgrade')){
    			//remove old mootools loaded
    			AImporter::removeScripts('mootools.js','mootools-uncompressed.js');
				//add mootools 1.2.5
    			JHTML::script(MOOTOOLS_BASE.'mootools125.js');
    			JFactory::getApplication()->set('MooToolsVersion', '1.2.5');
    		}
    		if ($more)
    			JHTML::script(MOOTOOLS_BASE.'mootoolsmore125.js');
    		$imported=true;
    	}
    }

    /**
     * Format person name.
     * 
     * @param TableCustomer  $person
     * @param boolean        $safe use HTML special chars to safe string, default false
     * @param boolean        $addCompany add Company Name, default false
     * @param boolean        $cblink make link to Community Builder user profile
     * @return string
     */
    function formatName(&$person, $safe = false, $addCompany = false, $cblink = false)
    {
        $parts = array();
        $person->title_before = JString::trim($person->title_before);
        $person->firstname = JString::trim($person->firstname);
        $person->middlename = JString::trim($person->middlename);
        $person->surname = JString::trim($person->surname);
        $person->title_after = JString::trim($person->title_after);
        if ($person->title_before) {
            $parts[] = $person->title_before;
        }
        if ($person->firstname) {
            $parts[] = $person->firstname;
        }
        if ($person->middlename) {
            $parts[] = $person->middlename;
        }
        if ($person->surname) {
            $parts[] = $person->surname . ($person->title_after ? ', ' : '');
        }
        if ($person->title_after) {
            $parts[] = $person->title_after;
        }
        if ($addCompany && $person->company) {
            $parts[] = '(' . $person->company . ')';
        }
        
        //if isn't any data, get basic from users table
        if(!$parts && $person->id){
    		AImporter::helper('user');
    		if (AUser::userExists($person->id)) { // prevent for Joomla error if user does not exist
        		$user = JFactory::getUser($person->id);
        		if($user->id){      	 
        			$parts[] = $user->name;
        		}
    		}
        }
        
        $name = JString::trim(implode(' ', $parts));
        if ($safe) {
            $name = htmlspecialchars($name, ENT_QUOTES, ENCODING);
        }
        
        if($cblink){
        	$name = '<a href="'.CommunityBuilder::userProfileUrl($person->user).'">'.$name.'</a>';
        }

        return $name;
    }

    /**
     * Format person adrress
     * 
     * @param TableCustomer $person
     * @return string HTML code
     */
    function formatAddress(&$person)
    {
    	$config = AFactory::getConfig();
        $parts = array();
        $person->city = JString::trim($person->city);
        $person->street = JString::trim($person->street);
        $person->zip = JString::trim($person->zip);
        $person->country = JString::trim($person->country);
        if ($config->addressFormat == 1) {
        	$address = $config->addressFormatCustom;
        	$address = str_ireplace('{city}', $person->city, $address);
        	$address = str_ireplace('{street}', $person->street, $address);
        	$address = str_ireplace('{zip}', $person->zip, $address);
        	$address = str_ireplace('{country}', $person->country, $address);
        	return $address;
        } else {
	        if ($person->country)
	            $parts[] = $person->country;
	        if ($person->city)
	            $parts[] = $person->city;
	        if ($person->street)
	            $parts[] = $person->street;
	        if ($person->zip)
	            $parts[] = $person->zip;
	        return JString::trim(implode(', ', $parts));
        }
    }

    /**
     * Get email link
     * 
     * @param TableCustomer $person
     * @param boolean $link display as link, default true
     * @return string HTML code
     */
    function getEmailLink(&$person, $link = true)
    {
        $person->email = JString::trim($person->email);
        if ($person->email) {
            return $link ? '<a href="mailto:' . $person->email . '" title="' . JText::_('SEND_EMAIL') . '">' . $person->email . '</a>' : $person->email;
        }
        return '';
    }

    function getIconEmail(&$person)
    {
        $email = JString::trim($person->email);
        if ($email) {
            return '<a href="mailto:' . $email . '" class="aIcon aIconEmail" title=""></a>';
        }
        return '';
    }

    /**
     * Get array of days in given date limit. For every day is generated list of time intervals in which can be subject reserved.
     * For every time is specified quantity of subject already reserved.
     * 
     * @param $subject     TableSubject Subject 
     * @param $dateStart   string       Date start of List
     * @param $dateEnd     string       Date end of List
     * @param $addCustomer boolean      add to Data Customer Name and Company, default false
     * @param $rids        array        selected reservation types
     * @param $setting     array        extra configuration
     * @return array
     */
    function getCalendar(&$subject, $dateStart, $dateEnd, $isAdmin = false)
    {
        if (! (int) $subject->total_capacity)
            // prevent for old versions where total capacity weren't compulsory
            $subject->total_capacity = 1;
        
        $jconfig = &JFactory::getConfig();    
        $config = &AFactory::getConfig();
        
        $tzoffset = BookingHelper::getTZOffset(true);
        
        
        //$config = JFactory::getConfig();
        $user = JFactory::getUser();
        $ttz = new DateTimeZone($user->getParam('timezone', $jconfig->get('offset')));
        
        //convert date end and start to full date interval with time zone offset 
        $dateStart = &BookingHelper::dateBeginDay($dateStart, $tzoffset);
        $dateEnd = &BookingHelper::dateEndDay($dateEnd, $tzoffset);
        
        $countDays = JFactory::getDate($dateEnd->dts,$ttz)->toUnix() - JFactory::getDate($dateStart->dts,$ttz)->toUnix();
        $countDays = $countDays ? round($countDays / DAY_LENGTH) : 1;
        
        //take needed models
        $modelReservationItems = new BookingModelReservationItems();
        $modelReservationTypes = &BookingModelReservationTypes::getObjectInstance();
        /* @var $modelReservationTypes BookingModelReservationTypes */
        $modelPrices = new BookingModelPrices();
        
        //take prices usable for this subject
        $modelPrices->init(array('subject' => $subject->id));
        $prices = &$modelPrices->getData(true);
        
        //take all reservations in this interval for this subject
        //$reservations = &$modelReservationItems->getSimpleData($subject->id, $dateStart->dts, $dateEnd->dts, $subject->display_who_reserve);
        //it gets user data always
        if ($user->authorise('booking.reservation.edit.item', 'com_booking') || $user->authorise('booking.reservation.edit.date', 'com_booking')) {
            $changedReservationItemId = JRequest::getInt('changed_reservation_item_id');
        } else {
            $changedReservationItemId = 0;
        }
        $reservations = &$modelReservationItems->getSimpleData($subject->id, $dateStart->dts, $dateEnd->dts, $changedReservationItemId);
        // items already in customer cart
        $cartIds = AUtils::getSubArray(JFactory::getApplication()->getUserState(OPTION . '.user_reservation_items'), 'boxIds');
        
        //take all usable reservation types for this subject
        $rtypesFilter['subject'] = $subject->id;
        $rtypesFilter['order'] = 'type';
        $rtypesFilter['order_Dir'] = 'DESC';
        
        unset($modelReservationTypes->_cache[$subject->id]); //! no cache, because for some reason on next fnc call it returns old data with old time_unit property
        $modelReservationTypes->init($rtypesFilter);
        $reservationTypes = $modelReservationTypes->getData();

        //get counts of data for optimalization
        $countReservationTypes = count($reservationTypes);
        $countPrices = count($prices);
        $countReservations = count($reservations);
        
        // get closing days
        $closingDaysModel = JModelLegacy::getInstance('Closingdays', 'BookingModel');
        /* @var $closingDaysModel BookingModelClosingdays */ 
        $closingDays = $closingDaysModel->getSubjectClosingDays($subject->id);
        
        if ($config->parentsBookable == 2) { // book one subject child as sub subject
            $child = JModelLegacy::getInstance('Subjects', 'BookingModel')->init(array('parent' => $subject->id, 'access' => AModel::getAccess()))->getData(); // get published child of the reserved subject
            foreach ($child as $kid) {
                $kid->closed = JModelLegacy::getInstance('Closingdays', 'BookingModel')->getSubjectClosingDays($kid->id); // sub subject closing days
            }
            $subject->total_capacity = count($child);
        }
        
        //set unix time stamp for reservations datetime from and to
        for ($reservationIndex = 0; $reservationIndex < $countReservations; $reservationIndex ++) {
            $reservation = $reservations[$reservationIndex];
            /* @var $reservation TableReservationItems */
            TableReservationItems::display($reservation);
            $reservation->fromUts = JFactory::getDate($reservation->from,$ttz)->toUnix();
            $reservation->toUts = JFactory::getDate($reservation->to,$ttz)->toUnix();
            $reservation->fields = unserialize($reservation->fields);
            $reservation->special = array();
            foreach ($config->rsExtra as $field) {
                if (!empty($field['special']) && !empty($reservation->fields[$field['name']]['title'])) {
                    $reservation->special['short'][] = $reservation->fields[$field['name']]['value']; 
                    $reservation->special['long'][]  = $reservation->fields[$field['name']]['value'] . ' (' . AHtml::date($reservation->from, ATIME_FORMAT_SHORT, 0) . ' - ' . AHtml::date($reservation->to, ATIME_FORMAT_SHORT, 0) . ')';
                }
            }
            $name = BookingHelper::formatName($reservation, false, true, CommunityBuilder::isInstalled());
            $reservation->canShow = ($user->authorise('booking.show.reservations', 'com_booking.subject.'.$reservation->subject) || $user->authorise('booking.show.reservations', 'com_booking.subject.'.$reservation->sub_subject) || $user->authorise('booking.show.reservations.popup', 'com_booking.subject.'.$reservation->subject) || $user->authorise('booking.show.reservations.popup', 'com_booking.subject.'.$reservation->sub_subject)) && $name;
            
            $reservation->canShowMine = ($isAdmin || (!$config->displayMineOnly || ($config->displayMineOnly && ($reservation->user == $user->id) && ($user->id != 0)))); 
            
            $reservation->interval = AHtml::interval($reservation);
            
            $capacity = $subject->display_capacity ? '('.$reservation->capacity.'x) ' : '';
            $data = array();
            $data['reservation_id'] = $reservation->reservation_id;
            $data['item'] = $reservation->subject;
            $data['name'] = $capacity . $name;
            $data['special'] = $reservation->special;
            $data['full'] = $reservation->interval . ' ' . $reservation->subject_title . ' (' . $name . ')';
            $data['message'] = JString::trim(htmlspecialchars($reservation->message.' '.$reservation->note, ENT_QUOTES, ENCODING));
            
            $reservation->data = $data;
        }

        $fix2num = array('mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6, 'sun' => 7);
        $today = date('N');
        
        //set cover interval for reservation types
        for ($reservationTypeIndex = 0; $reservationTypeIndex < $countReservationTypes; $reservationTypeIndex ++) {
            $reservationType = &$reservationTypes[$reservationTypeIndex];
            /* @var $reservationType TableReservationType */
            TableReservationType::display($reservationType);
            $reservationType->time_unit_orig = $reservationType->time_unit;
            if ($subject->min_limit)
                $reservationType->boxes = floor(($reservationType->time_unit + $reservationType->gap_time) / $subject->min_limit);
            else
                //$reservationType->boxes = $reservationType->time_unit + $reservationType->gap_time;
                $reservationType->boxes = 1;
            if ($reservationType->fix)
            	$reservationType->boxes = $reservationType->fix;
            if ($subject->min_limit && $reservationType->type == RESERVATION_TYPE_HOURLY)
                $reservationType->time_unit = $subject->min_limit;
            $reservationType->timeUnitFloat = $reservationType->time_unit / 60;
            $reservationType->gapTimeFloat = $reservationType->gap_time / 60;
            if ($reservationType->type == RESERVATION_TYPE_DAILY) {
                //reservation type cover full day
                $reservationType->interval = 24.0;
            } else {
            	$reservationType->interval = $reservationType->time_unit;
            	//standart gap time
            	if(!$reservationType->dynamic_gap_time)
            		$reservationType->interval += $reservationType->gap_time;
            	$reservationType->interval = $reservationType->interval / 60;
            }
            
            $reservationType->allowFixLimitFrom = null;
            
			if ($reservationType->book_fix_past && count($reservationType->fix_from) != 7 && $reservationType->fix) {
				
				/* allow reserve current fixed limit if has been started in the past */
				
				$fixUp = $fix2num[reset($reservationType->fix_from)]; // day of week when fix limit starts
				$fixDown = ($fixUp + $reservationType->fix - 1) % 7; // day of week when fix limit ends
				
				if ($today < $fixUp && $today <= $fixDown) // fix limit has been started in previous week and covers today
					$shift = $today + 7 - $fixUp;
				elseif ($today >= $fixUp && $today <= $fixDown) // fix limit has been started in current week and covers today
					$shift = $today - $fixUp;
				else // fix limit doesn't cover today
					$shift = null; 
				
				if ($shift !== null)
					$reservationType->allowFixLimitFrom = JFactory::getDate('-  ' . $shift . ' days')->format('Y-m-d');
			}
        }

        $pricesDays = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        
        //set cover interval for prices
        for ($priceIndex = 0; $priceIndex < $countPrices; $priceIndex ++) {
            $price = &$prices[$priceIndex];
            /* @var $price TablePrice */
            TablePrice::prepare($price, $subject);
            if ($price->time_range == TIME_RANGE_OVER_MIDNIGHT) { // time range over midnight EQ 8PM - 6AM
            	$price->break_up = $price->time_down; // break begins when price is down
            	$price->break_down = $price->time_up; // break ends when price is up
            	$price->time_up = $price->time_down = '00:00:00'; // price cover full day with middle break EQ 6AM - 8PM
            } else
            	$price->break_up = $price->break_down = null; // default without break
            if ($subject->single_deposit)
                $price->deposit = '';
            if (!empty($price->value) && !empty($price->deposit) && $price->deposit_type == DEPOSIT_TYPE_PERCENT)
            	$price->deposit = $price->value / 100 * $price->deposit;
            $dateUp = JFactory::getDate($price->date_up,$ttz);
            /* @var $dateUp JDate */    
            $price->dateUpUts = $dateUp->toUnix();
            $dateDown = JFactory::getDate($price->date_down,$ttz);
            /* @var $dateDown JDate */
            $price->dateDownUts = $dateDown->toUnix();
            $price->breakStartFloat = $price->breakStopFloat = null;
            if ($price->rtype == RESERVATION_TYPE_DAILY) {
                //price cover full day
                $price->limitStartFloat = 1.0;
                $price->limitStopFloat = 24.0;
            } else {
                $price->limitStartFloat = BookingHelper::timeToFloat($price->time_up, null);
                $price->limitStopFloat = BookingHelper::timeToFloat($price->time_down, null);
                if ($price->time_range == TIME_RANGE_OVER_MIDNIGHT) {
                	$price->breakStartFloat = BookingHelper::timeToFloat($price->break_up, null);
                	$price->breakStopFloat = BookingHelper::timeToFloat($price->break_down, null);
                }
             	if ($price->time_range == TIME_RANGE_OVER_WEEK) { // generate time range or each week day
             		$price->time_ranges = array();
             		foreach ($pricesDays as $pd => $priceDay) { // process each week day
             			if ($price->$priceDay) { // price is allowed for this day
							if ($pd > 0 && $price->$pricesDays[$pd - 1]) // price is allowed for the previous day as well
								$price->time_ranges[$priceDay]['limitStartFloat'] = 0.0; // price starts at morning - continues from the previous day
							else
								$price->time_ranges[$priceDay]['limitStartFloat'] = $price->limitStartFloat; // price starts at defined time up
							if ($pd < 6 && $price->$pricesDays[$pd + 1]) // price is allowed for the next day as well
								$price->time_ranges[$priceDay]['limitStopFloat'] = 24.0; // price ends at evening - continue to the next day
							else
								$price->time_ranges[$priceDay]['limitStopFloat'] = $price->limitStopFloat == 0.0 ? 24.0 : $price->limitStopFloat; // price ends at defined time down
             			} else {
             				$price->time_ranges[$priceDay]['limitStartFloat'] = null;
             				$price->time_ranges[$priceDay]['limitStopFloat'] = null;
             			}                        
             		}
             	}    
            }
            if ($price->limitStopFloat == 0.0) // price ends at midnight
            	$price->limitStopFloat = 24.0; // in database is stored 0 but we need 24
            //price to show
            $price->formatPrice = BookingHelper::displayPrice($price->value, $price->deposit, $subject->tax);
            $price->formatValue = BookingHelper::displayPrice($price->value, null, $subject->tax);
            $price->formatDeposit = BookingHelper::displayPrice($price->deposit, null, $subject->tax);
            
            $price->tailPiece = $price->tail_piece / 60;
            $price->headPiece = $price->head_piece / 60;
        }
        
        //days of calendar interval
        $days = array();
        
        // storage for used prices
        $usedPrices = array();
        
        //current unix time stamp in locale
        $current = &JFactory::getDate('now', $ttz);
        /* @var $current JDate */ 

        $time = $current->toUnix();
        $currentDay = &JFactory::getDate($current->format('Y-m-d 00:00:00'), $ttz);
        /* @var $currentDay JDate */        
        $timeDay = $currentDay->toUnix();
        
        $i = 0;
        //id for prices in calendar
        $id2 = array(RESERVATION_TYPE_HOURLY=>array(),RESERVATION_TYPE_DAILY=>array(),RESERVATION_TYPE_PERIOD=>array());
        
        if ($subject->use_fix_shedule) {
        	if ($subject->shedule_from === '00:00:00' && $subject->shedule_to === '00:00:00') {
        		$sheduleFrom = 0;
        		$sheduleTo = 24;
        	} else { 
            	$sheduleFrom = BookingHelper::timeToFloat($subject->shedule_from);
            	$sheduleTo = (BookingHelper::timeToFloat($subject->shedule_to) == 0.0 ? 24.0 : BookingHelper::timeToFloat($subject->shedule_to));
        	}
            $sheduleLimit = $subject->min_limit? $subject->min_limit / 60 : 1;
        }
        
        if (JRequest::getString('pre_from') && JRequest::getString('pre_to')) { // pre-select date from search result
            if ($subject->night_booking) {
                $preFrom = JFactory::getDate(JRequest::getString('pre_from'))->format('Y-m-d ' . JFactory::getDate($subject->night_booking_from)->format('H:i'));
                $preTo = JFactory::getDate(JRequest::getString('pre_to'));
                if (!$config->nightsStyle)
                    $preTo->modify('+1day');
                $preTo = $preTo->format('Y-m-d ' . JFactory::getDate($subject->night_booking_to)->format('H:i'));
            } else {
                $preFrom = JFactory::getDate(JRequest::getString('pre_from'))->format('Y-m-d H:i');
                $preTo = JFactory::getDate(JRequest::getString('pre_to'))->format('Y-m-d H:i');
            }
            $boxIds = array();
        } else {
            $preFrom = $preTo = $boxIds = null;
        }
        
        //for all days covers interval
        for ($dayOffset = 0; $dayOffset < $countDays; $dayOffset ++) {
            
            //set day date data
            $days[] = new BookingDay();
            $currentDay = end($days);
            /* @var $currentDay BookingDay */
            
    		$currentDay->jdate = JFactory::getDate(preg_replace('#\d{2}:\d{2}:\d{2}#', '', $dateStart->orig) . ' +' . $dayOffset . ' day', $ttz);
    		/* @var $date JDate */
    		$currentDay->up = $currentDay->jdate->format('Y-m-d 00:00:00', true);
    		$currentDay->down = $currentDay->jdate->format('Y-m-d 23:59:59', true);
        	$currentDay->date = $currentDay->jdate->format('Y-m-d', true);
            $currentDay->nextDate = JFactory::getDate(preg_replace('#\d{2}:\d{2}:\d{2}#', '', $dateStart->orig) . ' +' . ($dayOffset + 1) . ' day', $ttz);
    		/* @var $date JDate */
        	$currentDay->nextDate = $currentDay->nextDate->format('Y-m-d', true);
            
            $currentDay->Uts = $currentDay->jdate->toUnix();
            $currentDay->weekDayCode = $currentDay->jdate->format('N', true);
            $weekDayString = BookingHelper::dayCodeToString($currentDay->weekDayCode);
            $currentDay->weekDayString = $weekDayString;
            $weekType = $currentDay->jdate->format('W', true) % 2 ? WEEK_ODD : WEEK_EVEN;
            $currentDay->dayWeek = JString::strtolower($currentDay->jdate->format('D', true, false));

            // search reservations which cover the current day
            $currentReservations = array();
            foreach ($reservations as $reservation) {
                if ($reservation->from <= $currentDay->down && $reservation->to >= $currentDay->up) {
                    $currentReservations[] = $reservation;
                }
            }
            
            if ($subject->use_fix_shedule) {
                for ($dayIndex = $sheduleFrom; $dayIndex < $sheduleTo; $dayIndex += $sheduleLimit) {
                    $box = new BookingTimeBox();
                    $dayIndexKey = (string) $dayIndex;
                    $box->first = count($currentDay->boxes) === 0;
                    $box->fromFloat = $dayIndex;
                    $box->fromTime = BookingHelper::floatToTime($box->fromFloat);
                    $box->toFloat = $dayIndex + $sheduleLimit;
                    $box->toTime = BookingHelper::floatToTime($box->toFloat);
                    $box->fromDate = $currentDay->date . ' ' . $box->fromTime;
                    $box->fromUts = JFactory::getDate($box->fromDate,$ttz)->toUnix();
                    //TODO check, if it works correctly with localization
                    $box->fromDisplay = AHtml::date($box->fromDate, ADATE_FORMAT_LONG, 0);
                    //$box->fromDisplay = AHtml::date($box->fromDate, ADATE_FORMAT_LONG);
                    $box->toDate = $currentDay->date . ' ' . $box->toTime;
                    $box->toUts = JFactory::getDate($box->toDate,$ttz)->toUnix();
                    //TODO check, if it works correctly with localization
                    $box->toDisplay = AHtml::date($box->toDate, ADATE_FORMAT_LONG, 0);
                    //$box->toDisplay = AHtml::date($box->toDate, ADATE_FORMAT_LONG);
                    $box->rtype = RESERVATION_TYPE_HOURLY;
                    $currentDay->boxes[$dayIndexKey] = $box;
                }
            }
           
            //search reservation boxes for current day
            for ($reservationTypeIndex = 0; $reservationTypeIndex < $countReservationTypes; $reservationTypeIndex ++) {
                //search in all reservation types
                $reservationType = &$reservationTypes[$reservationTypeIndex];
                /* @var $reservationType TableReservationType */
                for ($priceIndex = 0; $priceIndex < $countPrices; $priceIndex ++) {
                    //search in all prices
                    $price = &$prices[$priceIndex];

                    /* @var $price TablePrice */
                    if ($price->week != WEEK_EVERY && $price->week != $weekType)
                    	continue;
                    if ($price->rezervation_type == $reservationType->id) {
                        //price is for reservation type
                        if ($price->$weekDayString && (! $price->dateUpUts || $price->dateUpUts <= $currentDay->Uts) && (! $price->dateDownUts || $price->dateDownUts >= $currentDay->Uts)) {
                        	
                        	$limitStartFloat = $price->limitStartFloat;
                        	$limitStopFloat = $price->limitStopFloat;
                        	
                        	if ($price->time_range == TIME_RANGE_OVER_WEEK) {
                        		$limitStartFloat = $price->time_ranges[$weekDayString]['limitStartFloat'];
                        		$limitStopFloat = $price->time_ranges[$weekDayString]['limitStopFloat'];
                        	}
                        	
                        	if ($price->headPiece)
                        		$limitStartFloat -= $reservationType->interval;
                        	
                        	if ($price->tailPiece)
                        		$limitStopFloat += $reservationType->interval;
                        	
                        	//price interval is for current day
                            $boxesLimitStop = $limitStopFloat - $reservationType->timeUnitFloat;
                            
                            //generate all boxes in price interval by reservation type interva;
                            for ($dayIndex = $limitStartFloat; $dayIndex <= $boxesLimitStop; $dayIndex = $dayIndex + $reservationType->interval) {
                                
                            	$priceFirst = $dayIndex == $limitStartFloat;
                            	$priceLast = $dayIndex == $boxesLimitStop;
                            	
                                $dayIndexKey = (string) $dayIndex;
                                
                                if (! isset($currentDay->boxes[$dayIndexKey])) {
                                    
                                    $box = new BookingTimeBox();
                                    
                                    $box->first = count($currentDay->boxes) === 0;
                                    $box->weekDay = $weekDayString;
                                    
                                    if ($reservationType->type == RESERVATION_TYPE_HOURLY) {
                                        //set datetime values 

                                        $box->fromFloat = $dayIndex;
                                        $box->fromTime = BookingHelper::floatToTime($box->fromFloat);
                                        
                                        $box->toFloat = $dayIndex + $reservationType->timeUnitFloat;
                                        $box->toTime = BookingHelper::floatToTime($box->toFloat);
                                    
                                    } else {
                                        //reservation type cover full day - box will be cover full day too
                                        if ($subject->night_booking) {
                                            $box->fromFloat = BookingHelper::timeToFloat($subject->night_booking_from, null);
                                            $box->toFloat = BookingHelper::timeToFloat($subject->night_booking_to, null);
                                            $box->fromTime = BookingHelper::floatToTime($box->fromFloat, null);
                                            $box->toTime = BookingHelper::floatToTime($box->toFloat, null);
                                        } else {
                                            $box->fromFloat = 0.0;
                                            $box->fromTime = '00:00';
                                            $box->toFloat = 24.0;
                                            $box->toTime = '23:59';
                                        }
                                    }
                                    $box->fromDate = $currentDay->date . ' ' . $box->fromTime;
                                    if ($subject->night_booking)
                                        $box->toDate = $currentDay->nextDate . ' ' . $box->toTime;
                                    else if($box->toTime == '24:00')
                                    	$box->toDate = $currentDay->nextDate . ' 00:00';
                                    else
                                    $box->toDate = $currentDay->date . ' ' . $box->toTime;
                                    
                                    $box->fromUts = JFactory::getDate($box->fromDate,$ttz)->toUnix();
                                    $box->toUts = JFactory::getDate($box->toDate,$ttz)->toUnix();
                                    
                                    if ($reservationType->type == RESERVATION_TYPE_DAILY && ! $subject->night_booking) {
                                    	//TODO check, if it works correctly with localization
                                        $box->fromDisplay = AHtml::date($box->fromDate, ADATE_FORMAT_NORMAL, 0);
                                        //$box->fromDisplay = AHtml::date($box->fromDate, ADATE_FORMAT_NORMAL);
                                        $box->toDisplay = AHtml::date($box->toDate, ADATE_FORMAT_NORMAL, 0);
                                        //$box->toDisplay = AHtml::date($box->toDate, ADATE_FORMAT_NORMAL);
                                    } else {
                                        $box->fromDisplay = AHtml::date($box->fromDate, ADATE_FORMAT_LONG, 0);
                                        //$box->fromDisplay = AHtml::date($box->fromDate, ADATE_FORMAT_LONG);
                                        $box->toDisplay = AHtml::date($box->toDate, ADATE_FORMAT_LONG, 0);
                                        //$box->toDisplay = AHtml::date($box->toDate, ADATE_FORMAT_LONG);
                                    }
                                    $box->rtype = $reservationType->type;
                                } else
                                    $box = $currentDay->boxes[$dayIndexKey];
                                
                                $service = new BookingService();
                                                            
                                //creating id for different prices in same day
                                $pricekey = $price->id;
                                $rt = $reservationType->type;
                                
                                //initialize id's and daybefore
                                if(!isset($daybefore[$rt]))
                                	$daybefore[$rt] = null;    
                                if(!array_key_exists($pricekey,$id2[$rt]))
                                	$id2[$rt][$pricekey] = 0;
                                
                                //get next day/hour for specific type
                                //compare only days
                                $daily = substr($daybefore[$rt], 0, 10) != substr($box->toDate, 0, 10);
                                
                                //compare full name including time
                                $hourly = $daybefore[$rt] != $box->toDate;
                                $nextday = ($rt == RESERVATION_TYPE_DAILY)? $daily : $hourly;
                                
                                //increment all keys for specific rtype, if is next day
                                if ($nextday || ($priceFirst /*&& $price->headPiece*/)) {
                                	foreach($id2[$rt] as $key => $val) {
                                		if (!isset($id2[$rt][$key]))
                                			$id2[$rt][$key] = 0;
                                		if ($pricekey == $key) {
											if ($priceFirst && $reservationType->type == RESERVATION_TYPE_HOURLY && $price->time_range == TIME_RANGE_ONE_DAY)
												// first box of day, increment for 2 to avoid link with the previous day
												$id2[$rt][$key] += 2; 
                               				else
                               				$id2[$rt][$key] += 1;
                                            break;
                                		}
                                	}
                                }
                                
                                //set actual box as before
                                $daybefore[$rt] = $box->toDate;
                                
                                //copy reservation type and price values into box
                                $service->i = $i ++;
                                $service->id = 'box-' . $reservationType->id . '-' . $price->id . '-' . $box->fromUts . '-' . $box->toUts; //absolute id of service
                                $service->idShort = 'box-' . $reservationType->id . '-' . $price->id . '-' . $id2[$rt][$pricekey]; //id used in HTML and JavaScript. unique and incrementing for each reservation type.
                                
                                $service->price = $price->value;
                                $service->deposit = $price->deposit;
                                $service->cancel_time = $price->cancel_time;
                                $service->color = $price->custom_color;
                                $service->formatPrice = $price->formatPrice;
                                $service->timeRange = $price->time_range;
                                $service->break = $price->time_range == TIME_RANGE_OVER_MIDNIGHT && $dayIndex >= $price->breakStartFloat && $dayIndex < $price->breakStopFloat;
                                $service->capacityUnit = $reservationType->capacity_unit; //capacityUnit != min capacity, but no of booked items
                                $service->rtype = $reservationType->type;
                                $service->rtypeId = $reservationType->id;
                                $service->boxes = $reservationType->boxes;
                                $service->min = $reservationType->min;
                                $service->max = $reservationType->max;
                                $service->fix = $reservationType->fix;
                                $service->fixFrom = $reservationType->fix_from;
                                $service->fixMultiply = $reservationType->fix_multiply;
                                $service->priceId = $price->id;
                                $service->priceIndex = $priceIndex;
                                $service->fromFloat = $box->fromFloat;
                                $service->fromTime = $box->fromTime;
                                $service->fromDate = $box->fromDate;
                                $service->fromUts = $box->fromUts;
                                $service->fromDisplay = $box->fromDisplay;
                                $service->toFloat = $box->toFloat;
                                $service->toTime = $box->toTime;
                                $service->toDate = $box->toDate;
                                $service->toUts = $box->toUts;
                                $service->toDisplay = $box->toDisplay;
                                $service->alreadyReserved = 0;
                                $service->canReserve = true;
                                $service->dayWeek = $currentDay->dayWeek;
                                
                                $service->notBeginsFixLimit = $config->hideDaysNotBeginFixLimit && !in_array($service->dayWeek, $service->fixFrom) && $service->fix;
                                $service->allowFixLimit = $reservationType->allowFixLimitFrom && $reservationType->allowFixLimitFrom <= $currentDay->date;
                                $service->item_id = $subject->id;
                                
                                if ($priceFirst && $price->headPiece) {
                                	$service->headPiece = $price->headPiece;
                                	$service->fromFloat += (($reservationType->time_unit - $price->head_piece) / 60);
                                	$service->fromTime = BookingHelper::floatToTime($service->fromFloat);
                                	$service->fromDate = $currentDay->date . ' ' . $service->fromTime;
                                	$service->fromUts = JFactory::getDate($service->fromDate)->toUnix();
                                } else
                                $service->headPiece = null;
                                
                                if ($priceLast && $price->tailPiece) {
                                	$service->tailPiece = $price->tailPiece;
                                	$service->toFloat -= (($reservationType->time_unit - $price->tail_piece) / 60);
                                	$service->toTime = BookingHelper::floatToTime($service->toFloat);
                                	$service->toDate = $currentDay->date . ' ' . $service->toTime;
                                	$service->toUts = JFactory::getDate($service->toDate)->toUnix();
                                } else
                                $service->tailPiece = null;
                                
                                if (! isset($usedPrices[$reservationType->id]))
                                    $usedPrices[$reservationType->id] = &$reservationType;
                                
                                if (! isset($usedPrices[$reservationType->id]->prices[$priceIndex]))
                                    $usedPrices[$reservationType->id]->prices[$priceIndex] = &$price;
            
                                if ($service->fromDate >= $preFrom && $service->toDate <= $preTo) // pre-select the box in the calendar
                                    $boxIds[] = $service->id;
                                
     //search if box is reserved
                                
                                foreach ($currentReservations as $reservation) {
                                    /* @var $reservation TableReservation */
                                    
                                    $from = $reservation->fromUts;
                                    $to = $reservation->toUts;
                                    //only for danamic gap time from object pricetype config
                                    //add gap time on the start and enf of reserved interval
                                    if($reservationType->dynamic_gap_time)
                                    {
	                                    $from -= ($reservationType->gap_time*60);
	                                    $to += ($reservationType->gap_time*60);
                                    }
                                    
                                 	$standard = ($reservation->rtype == $service->rtype && $to > $service->fromUts && $from < $service->toUts);
                                    $overlay = ($reservation->rtype == $service->rtype && ($from == $service->fromUts || $to == $service->toUts));
                                    //enable book 13-15 interval, when 12-14 is already booked.
                                    $reserved = $subject->price_overlay? $overlay : $standard;
                                    
                                    if ($reserved) {
                                    	
                                    	$service->capacityUnit += $reservation->capacity;
                                    	
                                    	//update info about number of full-day reservations for that day
                                    	if ($service->rtype==RESERVATION_TYPE_DAILY)
                                    		$currentDay->fullReserved += $reservation->capacity;
                                    	
                                    	//upate info about highest (peak) number of hourly reservations for that day
                                    	if ($service->rtype==RESERVATION_TYPE_HOURLY && $service->capacityUnit>$currentDay->maxHoursReserved)
                                    		$currentDay->maxHoursReserved = $service->capacityUnit;

                                        
                                        if ($reservation->canShow) {
                                            if ($reservation->canShowMine) 
                                                $box->customerName[$reservation->id] = $reservation->data;
                                            $currentDay->customerName[$reservation->id] = $reservation->data;
                                        }
                                    }
                                }
                                
                                if ($config->parentsBookable == 2) { // check if some sub subject is closed
                                    foreach ($child as $kid) {
                                        foreach ($kid->closed as $closed) {
                                            if ($closed->date_up <= $currentDay->date && $closed->date_down >= $currentDay->date && ($box->rtype == RESERVATION_TYPE_DAILY || ($closed->tUp <= $box->fromTime && $closed->tDown >= $box->toTime)) && $closed->{$currentDay->weekDayString}) {
                                                $service->capacityUnit ++; // descrease available quantity for closed sub subject
                                            }
                                        }
                                    }
                                }
                                
                                $box->services[] = $service;
                                
                                $currentDay->boxes[$dayIndexKey] = $box;
                            }
                        }
                    }
                }
            }
        }
        
        //now get information about number of already done reservations for every service      
        // and determine if service can be reserved
        foreach ($days as &$day) {
        	$day->engaged = true;
        	foreach ($day->boxes as &$box) {
        		$box->engaged = true;
        		foreach ($box->services as &$service) {
        			//already reserved for hour = all day reservations + that hour reservations
        			if ($service->rtype==RESERVATION_TYPE_HOURLY)
        				$service->alreadyReserved = $day->fullReserved + $service->capacityUnit;
        			//already reserved for whole day = max number of hour reservations + that whole day reservations
        			elseif ($service->rtype==RESERVATION_TYPE_DAILY)
        				$service->alreadyReserved = $day->maxHoursReserved + $service->capacityUnit;
        			else 
        				die ('Unknown reservation type variable');
        				
        			//determine if service can be reserved //box is in past - cannot reserve
        			
        			if ($isAdmin)
        				$inThePast = false;
        			elseif($config->bookCurrentDay && $service->rtype == RESERVATION_TYPE_DAILY)
        				$inThePast = $timeDay > $service->fromUts;
        			else
        			{
        				$future = self::intervalToSeconds($config->calendarFutureDays);
                        if ($config->disableUnbookableDays)
                            $inThePast = $time + $future > $service->fromUts;
                        else {
                            $inThePast = $time > $service->fromUts;
                            $service->beforeFuture = $time + $future > $service->fromUts;
                        }
        			}        			
                    
        			if (!$service->allowFixLimit && ($inThePast || $service->alreadyReserved>=$subject->total_capacity))
        				$service->canReserve = false;
        			else
        				$day->engaged = $box->engaged = false; // some service is bookable
        			
        			if ($service->break || in_array($service->id, $cartIds))
        				$service->canReserve = false;                    
        		}
        		
        		// check if box is covered by closing day
        		foreach ($closingDays as $closingDay) {
                    if ($closingDay->date_up <= $day->date && $closingDay->date_down >= $day->date && ($box->rtype == RESERVATION_TYPE_DAILY || ($closingDay->tUp <= $box->fromTime && $closingDay->tDown >= $box->toTime)) && $closingDay->{$day->weekDayString}) {
        				$box->closed = true;
						$box->closingDayTitle = $closingDay->title; // informations for calendar
        				$box->closignDayText  = $closingDay->text;
        				$box->closignDayColor = $closingDay->color;
        				$box->closignDayShow  = $closingDay->show;
        			}
        				
                }
        	}
        	// check if day is covered by closing day
        	foreach ($closingDays as $closingDay) {
        		if ($closingDay->up <= $day->up && $closingDay->down >= $day->down && $closingDay->{$day->weekDayString}) {
        			$day->closed = true;	
        			$day->closingDayTitle =	$closingDay->title; // informations for calendar
        			$day->closignDayText  = $closingDay->text;
        			$day->closignDayColor = $closingDay->color;
        			$day->closignDayShow  = $closingDay->show;
                }
            }
        }
        
        if ($subject->night_booking && !$config->nightsStyle) {
        	$a = true;
        	$p = false;
        	foreach ($days as $pDay) {
        		if ($p && $a && $pDay->engaged) {
        			foreach ($pDay->boxes as $pBox) {
        				$pBox->engaged = false;
        				foreach ($pBox->services as $pService)
        					$pService->noContinue = $pService->canReserve = true;
        			}
        			$pDay->engaged = $a = false;
        		} elseif (!$pDay->engaged)
        			$a = $p = true;
        	}
        }        
        
        // control if fixed limits in hourly reservation types are bookable
        foreach ($days as $i => &$day)
        	foreach ($day->boxes as $j => &$box) {
        		foreach ($box->services as &$service)
        			/* @var $service BookingService */
        			if ($service->canReserve && $service->fix > 1 && empty($service->controled) && $service->rtype == RESERVATION_TYPE_HOURLY) { // check non-controled bookable hourly service with fixed limit
        				$services = array($service); // list of services in fixed limit
        				$nbox = current($day->boxes); // pointer is aready on the next box
        				$nkeys = array_keys($day->boxes);
        				$nkey = array_search(key($day->boxes), $nkeys);
        				
        				do { // process next boxes after current box in fixed limit length
        					$next = false;
							if (!empty($nbox)) // there is next box
        						foreach ($nbox->services as $service2) // process next box services
        							if ($service2->priceId == $service->priceId && $service2->canReserve) { // next box bookable service is in the same fixed interval as current
        								$services[] = $service2; // store service for final check
        								$service2->controled = true; // no control service again
        								$next = true; // continue in next step
        							}
							$nbox = @$day->boxes[$nkeys[$nkey++]];  // move pointer to the next box. cannot use next()!
							
        				} while($next);  // there is no next box with service in fixed limit = stop controling
        				if ($service->timeRange != TIME_RANGE_ONE_DAY) { // the service is over midnight - control if can continue next day
        					$n = 1;
        					while (isset($days[$i + $n]) && end($services)->toFloat == 24) {
        						$l = -1; // start from the morning
        						do { // process next boxes in next day after current box in fixed limit length
        							$next = false;
        							if (!empty($days[$i + $n]->boxes[++$l])) // there is a next box in the next day
        								foreach ($days[$i + $n]->boxes[$l]->services as $service2) // process next box services
        									if ($service2->priceId == $service->priceId && $service2->canReserve) { // next box bookable service is in the same fixed interval as current
        										$services[] = $service2; // store service for final check
        										$service2->controled = true; // no control service again
        										$next = true; // continue in next step
        									}
        						} while($next);  // there is no next box with service in fixed limit = stop controling
        						if ($service->timeRange == TIME_RANGE_OVER_MIDNIGHT)
        							break;
        						$n ++;
        					}
        				}

        				if (count($services) <= $service->fix) // number of found services is not enough = found limit is unbookable
        					foreach ($services as $unreservable) // unset unbookable interval
        						$unreservable->canReserve = false;
        			}
        		// check box again if some service is bookable after check fixed interval
        		$box->engaged = true;
        		foreach ($box->services as &$service)
        			if ($service->canReserve)
        				$box->engaged = false;
	        }
        
        //counting over calendar - fix interval checking
        $c = new ServiceDayCounter();
        $last = false;
        foreach ($days as $dk=>&$day) {
        	$disable = null;
        	if(!$day->engaged)
        	{
        		foreach ($day->boxes as $bk=>&$box) {
        			foreach ($box->services as $sk=>&$service) {
        				//add new box type for counting fix days
        				$c->add($service);
        				if($service->fix > 1 && $service->rtype == RESERVATION_TYPE_DAILY)
        				{
        					//is set fix day for start booking?
        					if(count($service->fixFrom) !== 7 && $service->rtype == RESERVATION_TYPE_DAILY)
        					{
        						//when is fix day, start again
	        					if(in_array($service->dayWeek, $service->fixFrom))
	        					{
	        						$c->resetCount($service->id);
	        					}
	
	        					//decrease and check, if fix days counter is 0
	        					if($c->isCountZero($service->id))
	        					{
	        						//echo $service->dayWeek.'----<br>';
	        						$service->canReserve = false;
	        						$box->engaged = true;
	        						continue;
	        					}
        					}
        					/*else
        					{
        						if($c->isCountZero($service->id))
        						{
        							$service->canReserve = false;
        							$box->engaged = true;
        							$c->resetCount($service->id);
        							continue;
        						}
        					}*/
        				}

        				//end if price is reserved
        				if(!$service->canReserve)
        					continue;

        				//will count days only for days not for hours
        				if($service->rtype == RESERVATION_TYPE_HOURLY){
        					continue;
        				}
        				
        				//number of fixed services
        				$total = $service->fix;
        				$number = 0;
        				
        				//if exist previous day
        				if(array_key_exists($dk-1,$days))
        				{
        					if(!$days[$dk-1]->boxes)
        						continue;
        					//get yesterday servise
	        				$oldbox = $days[$dk-1]->boxes[$bk];
	        				$oldeservices = $oldbox->services;
	        				$exist = ($oldeservices != null && array_key_exists($sk,$oldeservices));
	        				
	        				//if don't exist or is reserved, start counting
	        				if(!$exist || ($exist && ($oldeservices[$sk]->canReserve == false) || ($days[$dk-1]->engaged == true)))
	        				{			
        						//find already booked service in fix interval. if there is any, it can't be booked and $number services must be disabled
		        				for($i=$total-1;$i>=0;$i--)
		        				{
		
		        					if(array_key_exists($dk+$i,$days))
		        					{
		        						$boxx = $days[$dk+$i]->boxes[$bk];
		        						$servic = $boxx->services;
		        						if($servic != null && array_key_exists($sk,$servic) && (($servic[$sk]->canReserve == false) || ($days[$dk+$i]->engaged == true)))
		        						{
		        							$number = $i;
		        							break;
		        						}
		        					}
		        					else
		        						break;
		        				}
	        				}
        				}

        				//disable all services in found interval
        				if($number>0)
        				{
        					for($i=0;$i<=$number;$i++)
        					{
        						$boxx = $days[$dk+$i]->boxes[$bk];
        						$servic = $boxx->services;
        						$servic[$sk]->canReserve = false;
        						$boxx->engaged = true;
        					}
        				}

        				// counting fix limit for services with specific starting day
        				if($service->fix > 1 && (count($service->fixFrom) != 7) && (!in_array($service->dayWeek, $service->fixFrom)) && !$last)
        				{
        					//$service->canReserve = false;
        					for($i=0;$i<=$service->fix;$i++)
        					{

        						if(array_key_exists($dk+$i,$days))
        						{
        							$boxx = $days[$dk+$i]->boxes[$bk];
        							$servic = $boxx->services;
        							if ($service->priceId == $servic[$sk]->priceId && $service->rtypeId == $servic[$sk]->rtypeId) // the same type only 
        								if(in_array($servic[$sk]->dayWeek, $service->fixFrom))
        								{
        									break;
        								}
        								else
        								{
        									$servic[$sk]->canReserve = false;
        									$boxx->engaged = true;
        								}
        						}
        						else
        							break;
        					}

        				}
        				//echo $service->fromDisplay.'- smazat:'.$number.' - <br>';
        			}
        		}

        	}

        	if($day->engaged)
        		$last = false;
        	else
        		$last = true;
        }
         
        $output = new stdClass();
        $output->prices = &$usedPrices;
        $output->calendar = &$days;        
        
        if (!empty($boxIds))
            JRequest::setVar ('boxIds', $boxIds);
        
        return $output;
    }
    
    /**
     * Get week calendar for selected subject.
     * 
     * @param TableSubject $subject
     * @param BookingCalendarSetting $setting
     * @return stdClass
     */
    function getWeekCalendar(&$subject, &$setting, $count = 'week', $isAdmin = false)
    {
        $mainframe = JFactory::getApplication();
        /* @var $mainframe JApplication */
        $current = JFactory::getDate();
        /* @var $current JDate */
        $config = &AFactory::getConfig();
        /* @var $config BookingConfig */
        
        $setting->defaultWeek = $setting->currentWeek = (int) $current->format('W');
        
        /* @var $currentWeek int current week without leading zero */
        $setting->defaultYear = $setting->currentYear = (int) $current->format('Y');
        /* @var $setting->currentYear int current year without leading zero */
        $setting->current = $current->format('Y-W');
        
        if ($config->defaultCalendarPageBookable) {
            $model = new BookingModelSubject();
            $nearest = JFactory::getDate($model->getNearestBooking($subject->id));
            if ($nearest->toUnix() > $current->toUnix()) {
                $setting->defaultWeek = (int) $nearest->format('W');
                $setting->defaultYear = (int) $nearest->format('Y');
            }
        }               
        
        $setting->week = $mainframe->getUserStateFromRequest('vsdwcm' . $subject->id, 'week', $setting->defaultWeek, 'int');
        /* @var $week int selected week from user request */
        $setting->year = $mainframe->getUserStateFromRequest('vsdwcy' . $subject->id, 'year', $setting->defaultYear, 'int');
        if (empty($setting->year)) {
            $setting->year = $setting->defaultYear;
        }
        /* @var $year int selected year from user request */
        
        //prevent failure of JFactory::getDate contructor
        if(!$setting->week)
        	$setting->week = $setting->defaultWeek? $setting->defaultWeek : 1;
        
        $selected = JFactory::getDate($setting->year . '-W' . str_pad($setting->week, 2, '0', STR_PAD_LEFT));
        /* @var $selected JDate */
        //in php 5.2 retur null, in php 5.3 return DateTime Object
        $selected->modify('+6 days');
        $setting->selected = $selected->format('Y-W');
        
        $lastAllow = JFactory::getDate('+ ' . $config->calendarDeepWeek . ' week');
        /* @var $lastAllow JDate */ 
        
        $setting->lastAllowYear = (int) $lastAllow->format('Y');
        /* @var $lastAllowYear int last allow year */
        $setting->lastAllowWeek = (int) $lastAllow->format('W');
        /* @var $lastAllowWeek int last allow week */
        $setting->lastAllow = $lastAllow->format('Y-W');
        
        if (!$isAdmin && ($setting->selected < $setting->current || $setting->selected > $setting->lastAllow)) {
            // request date no exists or is in past or over allowed interval - reset to current day
            $setting->week = $setting->currentWeek;
            $setting->year = $setting->currentYear;
        }
        
        $last = JFactory::getDate($setting->year . '-W' . str_pad($setting->week, 2, '0', STR_PAD_LEFT) . ' -1 week + 6days');
        /* @var $last JDate */ 
        
        $setting->previousWeek = (int) $last->format('W');
        /* @var $setting->previousWeek int previous week from actual */
        $setting->previousYear = (int) $last->format('Y');
        /* @var $previousYear int previous year from actual */
        
        $next = JFactory::getDate($setting->year . '-W' . str_pad($setting->week, 2, '0', STR_PAD_LEFT) . ' +1 week + 6days');
        /* @var $next JDate */ 
        
        $setting->nextWeek = (int) $next->format('W');
        /* @var $nextWeek int next week from actual */
        $setting->nextYear = (int) $next->format('Y');
        /* @var $nextYear int next year from actual */
        
        $setting->onCurrentWeek = $setting->current == $setting->selected;
        /* @var $onCurrentWeek boolean is set current week */
        $setting->lastAllowPage = $setting->lastAllow == $setting->selected;
        /* @var $lastAllowPage boolean is set last week from allowed interval */
        
        if (!$config->showFullWeek && $setting->onCurrentWeek && !$isAdmin) {
        	$monday = $current->format('d-m-Y H:i:s');
        } else {
        	$monday = JFactory::getDate($setting->year . '-W' . str_pad($setting->week, 2, '0', STR_PAD_LEFT) . '-' . ($config->firstDaySunday ? (7 . ' -1 week') : 1));
        	/* @var $monday JDate */
        	$monday = $monday->format('d-m-Y 00:00:00');
        }
        /* @var $monday int first day of week */
        if ($count === 'week')
        	$sunday = JFactory::getDate($setting->year . '-W' . str_pad($setting->week, 2, '0', STR_PAD_LEFT) . '-' . ($config->firstDaySunday ? 6 : 7));
        else 
        	$sunday = JFactory::getDate($monday . ' + ' . ($count - 1) . ' days');
        /* @var $sunday JDate */
        $sunday = $sunday->format('d-m-Y 23:59:59');
        /* @var $sunday int last day of week */
        
        $calendar = BookingHelper::getCalendar($subject, $monday, $sunday, $isAdmin);
        foreach ($calendar->calendar as $day) {
            usort($day->boxes, 'BookingHelper::weekcmp');
        }
        return $calendar;
    }
    
    public static function weekcmp($a, $b) {
        return strcmp($a->fromDate, $b->fromDate);
    }

    /**
     * Get monthly calendar for selected subject.
     * 
     * @param TableSubject $subject
     * @param BookingCalendarSetting $setting
     * @return stdClass
     */
    function getMonthlyCalendar(&$subject, &$setting, $calendarnummonths = 1, $isAdmin = false)
    {
        $mainframe = JFactory::getApplication();
        /* @var $mainframe JApplication */
        $current = JFactory::getDate();
        /* @var $current JDate */
        $config = &AFactory::getConfig();
        /* @var $config BookingConfig */
        
        $setting->currentDay = (int) $current->format('d');
        /* @var $currentDay int current day without leading zero */
        $setting->defaultMonth = $setting->currentMonth = (int) $current->format('m');
        /* @var $currentMonth int current month without leading zero */
        $setting->defaultYear = $setting->currentYear = (int) $current->format('Y');
        /* @var $currentYear int current year without leading zero */
        $setting->current = $current->format('Y-m');
        
        $setting->currentDate = $current->format('Y-m-d');
        /* @var $currentDate string current date with leading zeros */
        $setting->currentDayUTS = $current->toUnix();
        /* @var $currentDayUTS int unix timestamp of current date */

        $setting->monthNumber = 1;
        
        if ($config->defaultCalendarPageBookable) {
            $model = new BookingModelSubject();
            $nearest = JFactory::getDate($model->getNearestBooking($subject->id));
            if ($nearest->toUnix() > $current->toUnix()) {
                $setting->defaultMonth = (int) $nearest->format('m');
                $setting->defaultYear = (int) $nearest->format('Y');
            }
        }       
        
        //day is selected
        $boxIds = ARequest::getStringArray('boxIds', true);
        if (!empty($boxIds)) {
            $setting->month = JRequest::getInt('month');
            $setting->year = JRequest::getInt('year');
            if (is_null(($setting->lastMonth = $mainframe->getUserState('vsdmcm' . $subject->id))))
                $setting->lastMonth = $setting->month;
            if (is_null(($setting->lastYear = $mainframe->getUserState('vsdmcy' . $subject->id))))
                $setting->lastYear = $setting->year;
            if ($setting->lastYear > $setting->year || ($setting->lastYear == $setting->year && $setting->lastMonth > $setting->month)) {
                $setting->lastMonth = $setting->month;
                $setting->lastYear = $setting->year;
                JRequest::setVar('boxIds', '');
            }
            $setting->monthNumber = ($setting->lastYear - $setting->year) * 12 + $setting->month - $setting->lastMonth + 1;    
        } else {
            $setting->lastMonth = $setting->month = $mainframe->getUserStateFromRequest('vsdmcm' . $subject->id, 'month', $setting->defaultMonth, 'int');
            /* @var $month int selected month from user request */
            $mainframe->setUserState('vsdmcm' . $subject->id, $setting->lastMonth);
            $setting->lastYear = $setting->year = $mainframe->getUserStateFromRequest('vsdmcy' . $subject->id, 'year', $setting->defaultYear, 'int');
            /* @var $year int selected year from user request */
            $mainframe->setUserState('vsdmcy' . $subject->id, $setting->lastYear);
        }

    	if ($calendarnummonths > 1) {
    		if (JRequest::getString('boxIds'))
        		$lastMonth = JFactory::getDate($setting->year . '-' . $setting->month . '-1' . ' + ' . ($calendarnummonths - 1) . ' month');
        	else
        		$lastMonth = JFactory::getDate($setting->lastYear . '-' . $setting->lastMonth . '-1' . ' + ' . ($calendarnummonths - 1) . ' month');
    		$setting->month = (int) $lastMonth->format('m');
        	$setting->year =  $lastMonth->format('Y');	
        }
        
        $selected = JFactory::getDate($setting->lastYear . '-' . $setting->lastMonth . '-01');
        /* @var $selected JDate */
        $setting->selected = $selected->format('Y-m');
        
        $setting->dateExists = checkdate($setting->month, 1, $setting->year);
        /* @var $dateExists boolean request date exists */
        
        $lastAllow = JFactory::getDate('+' . $config->calendarDeepMonth . ' month');
        /* @var $lastAllow JDate */
        
        $setting->lastAllowYear = (int) $lastAllow->format('Y');
        /* @var $lastAllowYear int last allow year */
        $setting->lastAllowMonth = (int) $lastAllow->format('m');
        /* @var $lastAllowMonth int last allow month */
        $setting->lastAllow = $lastAllow->format('Y-m');
        /* @var $lastAllow string last allow year-month */
        
        if (!$isAdmin && (! $setting->dateExists || $setting->selected < $setting->current || $setting->selected > $setting->lastAllow)) {
            // request date no exists or is in past or over allowed interval - reset to current day
            $setting->lastMonth = $setting->month = $setting->currentMonth;
            $setting->lastYear = $setting->year = $setting->currentYear;
            $selected = JFactory::getDate($setting->year . '-' . $setting->month);
        	/* @var $selected JDate */
        	$setting->selected = $selected->format('Y-m');
        }
        
        $setting->monthName = $selected->format('F');
        
        $last = JFactory::getDate($setting->lastYear . '-' . $setting->lastMonth . '-01 -1 month');
        /* @var $last JDate */
        
        $setting->previousMonth = (int) $last->format('m');
        /* @var $previousMonth int previous month of actual date */
        $setting->previousYear = $last->format('Y');
        /* @var $previousYear int previous year of actual date */
        
        $near = JFactory::getDate($setting->lastYear . '-' . $setting->lastMonth . '-01 +1 month');
        
        $setting->nearMonth = (int) $near->format('m');
        /* @var $previousMonth int near month of actual date */
        $setting->nearYear = $near->format('Y');
        /* @var $previousYear int near year of actual date */                
        
        $next = JFactory::getDate($setting->year . '-' . $setting->month . '-01 +1 month');
        /* @var $next JDate */
        
        $setting->nextMonth = (int) $next->format('m');
        /* @var $nextMonth int next month of actual date */
        $setting->nextYear = $next->format('Y');
        /* @var $nextYear int next year of actual date */
        
        $setting->month = str_pad($setting->month, 2, '0', STR_PAD_LEFT);
        $setting->lastMonth = str_pad($setting->lastMonth, 2, '0', STR_PAD_LEFT);
        
        $firstDay = JFactory::getDate($setting->lastYear . '-' . $setting->lastMonth . '-01 00:00:00');
        /* @var $firstDay JDate */
        $setting->firstDay = $firstDay->format('Y-m-d 00:00:00');
        /* @var $firstDay string first month day date */
        $setting->firstDayUTS = $firstDay->toUnix();
        /* @var $firstDayUTS int first month day unix timestamp */
        
        //$lastDay = JFactory::getDate($setting->year . '-' . $setting->month . '-' . date('t', strtotime($setting->year . '-' . $setting->month . '-01')));
        $lastDay = JFactory::getDate(gmmktime(0, 0, 0, $setting->month + 1, 0, $setting->year));
        /* @var $lastDay JDate */
        $setting->lastDay = $lastDay->format('Y-m-d 23:59:59');
        /* @var $lastDay string last month day date */
        $setting->lastDayUTS = $lastDay->toUnix();
        /* @var $lastDay int last month day timestamp */
        
        $setting->firstWeekDayNumber = $config->firstDaySunday ? 7 : 1;
        /* @var $firstWeekDayNumber int number of first week day according to component configuration */
        $setting->firstDayOffset = $config->firstDaySunday ? 1 : 2;
        /* @var $firstDayOffset int first day offset according to component configuration */
        $setting->firstDayNumber = $firstDay->format('w');
        /* @var $firstDayNumber int week number of first month day */
        if ($setting->firstDayNumber == 0) $setting->firstDayNumber = 7;
        
        if ($setting->firstDayNumber != $setting->firstWeekDayNumber) {
            /* first month day isn't first week day - to head of month must connect part of previous month to complete calendar table */
            $setting->firstDay = JFactory::getDate(gmmktime(0, 0, 0, $setting->lastMonth, ($setting->firstDayOffset - $setting->firstDayNumber) , $setting->lastYear))->format('Y-m-d 00:00:00');
        }
        
        $setting->lastWeekDayNumber = $config->firstDaySunday ? 6 : 7;
        /* @var $lastWeekDayNumber int number of last week day according to component configuration */
        $setting->lastDayNumber = $lastDay->format('w');
        if ($setting->lastDayNumber == 0 && !$config->firstDaySunday) $setting->lastDayNumber = 7;
        
        /* @var $lastDayNumber int week number of last month day */
        
        if ($setting->lastDayNumber != $setting->lastWeekDayNumber) {
            /* last month day isnt't last week day - to tail of month must connect part of next month to complete calendar table */
            $setting->lastDay = JFactory::getDate(gmmktime(0, 0, 0, ($setting->month + 1), ($setting->lastWeekDayNumber - $setting->lastDayNumber), $setting->year))->format('Y-m-d 23:59:59');
        }
        
        $setting->week = (int) $firstDay->format('W');
        /* @var $week number of first week */
        
        $setting->onCurrentMonth = $setting->selected == $setting->current;
        /* @var $onCurrentMonth boolean user select current month */
        $setting->lastAllowPage = $setting->selected == $setting->lastAllow;
        /* @var $lastAllowPage boolean user select last allowed page */
        
        return BookingHelper::getCalendar($subject, $setting->firstDay, $setting->lastDay, $isAdmin);
    }

    /**
     * Get daily calendar for selected subject.
     * 
     * @param TableSubject $subject
     * @param BookingCalendarSetting $setting
     * @return stdClass
     */
    function getDailyCalendar(&$subject, &$setting, $isAdmin = false)
    {
        $mainframe = JFactory::getApplication();
        /* @var $mainframe JApplication */
        $current = JFactory::getDate();
        /* @var $current JDate */
        $config = AFactory::getConfig();
        /* @var $config BookingConfig */
        
        $setting->defaultDay = $setting->currentDay = (int) $current->format('d');
        /* @var $currentDay int current day without leading zero */
        $setting->defaultMonth = $setting->currentMonth = (int) $current->format('m');
        /* @var $currentMonth int current month without leading zero */
        $setting->defaultYear = $setting->currentYear = (int) $current->format('Y');
        /* @var $currentYear int current year without leading zero */
        
        $setting->currentDate = $current->format('Y-m-d');
        /* @var $currentDate string current date with leading zeros */
        $setting->currentUTS = $current->toUnix();
        /* @var $currentUTS int unix timestamp of current date */
        
        if ($config->defaultCalendarPageBookable) {
            $model = new BookingModelSubject();
            $nearest = JFactory::getDate($model->getNearestBooking($subject->id));
            if ($nearest->toUnix() > $current->toUnix()) {
                $setting->defaultDay = (int) $nearest->format('d');
                $setting->defaultMonth = (int) $nearest->format('m');
                $setting->defaultYear = (int) $nearest->format('Y');
            }
        }       
        
        $setting->day = $mainframe->getUserStateFromRequest('vsddcd' . $subject->id, 'day', $setting->defaultDay, 'int');
        /* @var $day int selected day from user request */
        $setting->month = $mainframe->getUserStateFromRequest('vsddcm' . $subject->id, 'month', $setting->defaultMonth, 'int');
        /* @var $month int selected month from user request */
        $setting->year = $mainframe->getUserStateFromRequest('vsddcy' . $subject->id, 'year', $setting->defaultYear, 'int');
        /* @var $year int selected year from user request */

        $lastAllow = JFactory::getDate('+ ' . $config->calendarDeepDay . 'day');
        /* @var $lastAllow JDate */
        
        $setting->lastAllow = $lastAllow->format('Y-m-d');
        $setting->lastAllowDateUTS = $lastAllow->toUnix();
        /* @var $lastAllowDateUTS int unix timestamp of last date in allowed interval */
        $setting->lastAllowDay = (int) $lastAllow->format('d');
        /* @var $lastAllowDay int last allow day number without leading zero */
        $setting->lastAllowMonth = (int) $lastAllow->format('m');
        /* @var $lastAllowMonth int last allow month without leading zero */
        $setting->lastAllowYear = (int) $lastAllow->format('Y');
        /* @var $lastAllowYear int last allow year */
        
        $setting->dateExists = checkdate($setting->month, $setting->day, $setting->year);
        /* @var $dateExists boolean request date exists */
        
        $request = JFactory::getDate($setting->year . '-' . str_pad($setting->month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($setting->day, 2, '0', STR_PAD_LEFT));
        /* @var $request JDate */
        $setting->requestDate = $request->format('Y-m-d');
        /* @var $requestDate string DB format of date from request date: 1.1.2011 to 2011-01-01 */
        $setting->requestUTS = $request->toUnix();
        /* @var $requestUTS int unix timestamp of request date */
        
        if (!$isAdmin && (! $setting->dateExists || $setting->requestUTS < $setting->currentUTS || $setting->requestUTS > $setting->lastAllowDateUTS)) {
            // request date no exists or is in past or over allowed interval - reset to current day
            $setting->day = $setting->currentDay;
            $setting->month = $setting->currentMonth;
            $setting->year = $setting->currentYear;
            $setting->requestDate = $setting->currentDate;
            $setting->requestUTS = $setting->currentUTS;
        }
        
        $last = JFactory::getDate($setting->requestDate . ' - 1 day');
        /* @var $last JDate */
        
        $setting->previousDay = (int) $last->format('d');
        /* @var $previousDay int previous day of actual date */
        $setting->previousMonth = (int) $last->format('m');
        /* @var $previousMonth int previous month of actual date */
        $setting->previousYear = (int) $last->format('Y');
        /* @var $previousYear int previous year of actual date */
        
        $next = JFactory::getDate($setting->requestDate . ' + 1 day');
        /* @var $next JDate */
        
        $setting->nextDay = (int) $next->format('d');
        /* @var $nextDay int next day of actual date */
        $setting->nextMonth = (int) $next->format('m');
        /* @var $nextMonth int next month of actual date */
        $setting->nextYear = (int) $next->format('Y');
        /* @var $nextYear int next year of actual date */
        
        $lastDay = JFactory::getDate($setting->nextYear . '-' . $setting->nextMonth . ' + 1 month last day');
        /* @var $lastDay JDate */
        
        $setting->lastDay = (int) $lastDay->format('Y-m-d');
        /* @var $lastDay string last day of next month */
        
        $setting->onCurrentDay = $setting->currentDate == $setting->requestDate;
        /* @var $onCurrentDay boolean request date is equal with current date */
        $setting->lastAllowPage = $setting->lastAllow == $setting->requestDate;
        /* @var $lastAllowPage boolean user set last allowed page */
        
        $calendar = BookingHelper::getCalendar($subject, $setting->requestDate, $setting->requestDate, $isAdmin);
        foreach ($calendar->calendar as $day) {
            usort($day->boxes, 'BookingHelper::weekcmp');
        }
        return $calendar;
    }

    /**
     * Get time zone offset from Joomla! configuration in seconds.
     * 
     * @return int
     */
    function getTZOffset($inSeconds = true)
    {
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $tzoffset = $mainframe->getCfg('offset');
        if (ISJ16) {
            $dateTimeZone = new DateTimeZone($tzoffset);
            $dateTime = new DateTime('now', $dateTimeZone);
            $tzoffset = $dateTimeZone->getOffset($dateTime);
            if (! $inSeconds)
                $tzoffset /= 60 / 60;
            else
                $inSeconds = false;
        }
        if ($inSeconds)
            $tzoffset *= 60 * 60;
        return $tzoffset;
    }

    /**
     * Convert date to begin datetime of this day with given time zone offset.
     * 
     * @param $date string
     * @param $tzoffset int
     * @return BookingDate
     */
    function dateBeginDay($date, $tzoffset = 0)
    {
        $date = BookingHelper::convertDate($date, 'Y-m-d 00:00:00', true);
        return $date;
    }

    /**
     * Convert date to end datetime of this day with given time zone offset.
     * 
     * @param $date string
     * @param $tzoffset int
     * @return BookingDate
     */
    function dateEndDay($date, $tzoffset = 0)
    {
        $date = BookingHelper::convertDate($date, 'Y-m-d 23:59:59', true);
        return $date;
    }

    /**
     * Convert date into given format with given time zone offset.
     * 
     * @param $date string date to convert
     * @param $format string datetime format
     * @param $tzoffset int time zone offset
     * @return BookingDate
     */
    function convertDate($date, $format = 'Y-m-d H:i:s', $tzoffset = false)
    {
        static $cache;
        $key = $date . $format . $tzoffset;
        if (! isset($cache[$key])) {
        	if ($tzoffset){
        		$mainframe = JFactory::getApplication();
        		/* @var $mainframe JApplication */
        		$jdate = JFactory::getDate($date, $mainframe->getCfg('config'));
        		/* @var $date JDate */ 
        	} else {
        		$jdate = JFactory::getDate($date);
        		/* @var $jdate JDate */
        	}
            $output = new BookingDate();
            $output->orig = $date;
            $output->uts = $jdate->toUnix();
            $output->dts = $jdate->format($format, $tzoffset);
            $cache[$key] = $output;
        }
        return $cache[$key];
    }

    /**
     * Get unix time stamp of date with given time zone offset.
     * 
     * @param $date string
     * @param $tzoffset int
     * @return int
     */
    function getUts($date, $tzoffset = 0)
    {
        $uts = JFactory::getDate($date)->toUnix() + $tzoffset;
        return $uts;
    }    
    
	/**
	 * Gets overall time interval from boxes Ids.
	 * 
	 * @param array $boxIds
	 */
    function getIntervalFromBoxIds($boxIds)
    {
    	$return = array();
    	$return['start'] = null;
    	$return['end'] = 0;
    	
    	if(is_array($boxIds)){
	    	foreach ($boxIds as $boxId)	{
	    		
	    		$box = explode('-',$boxId);
	    		$end = array_pop($box);
	    		$start = array_pop($box);
	    		if (!$return['start'] || $start < $return['start'])
	    			$return['start'] = $start - 86400;
	    		if ($end > $return['end'])
	    			$return['end'] = $end + 86400;
	    	}
    	}
    	
    	return $return;
    }
    
	/**
 	 * Get information about interval customer want to reserve 
     * @param TableSubject $subject
     * @param string $ctype
     * @param array $boxIds
     * @param array $supplements
     * @param int $capacity
     * @param TableReservationItems $item
     * @param array $occupancy
     * @return BookingInterval
     */
    function getReservedInterval(&$subject, $ctype, $boxIds, &$supplements, $capacity=1, &$item, $occupancy = null)
    {
        $user = JFactory::getUser();
    	$config = AFactory::getConfig();
        $interval = new BookingInterval();
        $interval->rtype = $ctype == CTYPE_MONTHLY ? RESERVATION_TYPE_DAILY : RESERVATION_TYPE_HOURLY;
        
        if ($ctype == CTYPE_PERIOD) {
        	
        	$interval->rtype = RESERVATION_TYPE_PERIOD;
        	
        	$rtype = JTable::getInstance('ReservationType', 'Table');
        	/* @var $rtype TableReservationType */
        	$price = JTable::getInstance('Price', 'Table');
        	/* @var $price TablePrice */
        	
        	// reservation type and price selected in timeframe
        	$rtype->load($item->period_rtype_id);
        	$price->load($item->period_price_id);
        	TablePrice::prepare($price, $subject);
    		
    		// compute occurrences #
    		$item->period_total = 0;
    		
    			$beginDay = JFactory::getDate($item->period_date_up);
    			$endDay = JFactory::getDate($item->period_date_down);
    			
    			if ($item->period_end == PERIOD_END_TYPE_NO) // if period end undefined then compute for next 365 days
    				$endDay = JFactory::getDate('+ 365 days');
    			
    			$beginDayUnix = $beginDay->toUnix();
    			$endDayUnix = $endDay->toUnix();
    			if ($item->period_end == PERIOD_END_TYPE_AFTER)
    				$endDayUnix = JFactory::getDate($price->date_down)->toUnix();
    			 
    			if ($item->period_type == PERIOD_TYPE_WEEKLY || $item->period_type == PERIOD_TYPE_DAILY) {
    				
    				$firstWeekDay = $config->firstDaySunday ? 7 : 1;
    				
    				for ($day = $beginDayUnix; $day < $endDayUnix; $day += 86400) { // loop all days in period

    					if ($item->period_end == PERIOD_END_TYPE_AFTER && $item->period_total == $item->period_occurrences)
    						break;
    					
    					$dayWeekNum = JFactory::getDate($day)->format('N'); // 1-7 
    					
    					if ($firstWeekDay == $dayWeekNum) // week begin
    						isset($week) ? $week++ : $week = 1; // increment week counter
    					
    					if (!isset($week)) // period begins in the middle of week
    						$week = 1;
    					
    					if ($week % $item->period_recurrence == 0) { // in week recurrence
							if ($dayWeekNum == 1 && $item->period_monday) {// check every week day
								$item->period_total++;
								$item->period[] = $day;				
							} elseif ($dayWeekNum == 2 && $item->period_tuesday) {
								$item->period_total++;
								$item->period[] = $day;
							} elseif ($dayWeekNum == 3 && $item->period_wednesday) {
								$item->period_total++;
								$item->period[] = $day;
							} elseif ($dayWeekNum == 4 && $item->period_thursday) {
								$item->period_total++;
								$item->period[] = $day;
							} elseif ($dayWeekNum == 5 && $item->period_friday) {
								$item->period_total++;
								$item->period[] = $day;
							} elseif ($dayWeekNum == 6 && $item->period_saturday) {
								$item->period_total++;
								$item->period[] = $day;
							} elseif ($dayWeekNum == 7 && $item->period_sunday) {
								$item->period_total++;
								$item->period[] = $day;
							}
    					}
    				}	
    			} elseif ($item->period_type == PERIOD_TYPE_MONTHLY) { 
    				
    				$weeks = array();
    				
    				$year = $beginDay->format('Y'); 
    				$month = $beginDay->format('m');
    				$first = JFactory::getDate("$year-$month-01"); // first day of period date up month 
    				
    				while ($first->toUnix() < $endDayUnix) { // check all months in period
    					$weeks[] = $first->format('W') + $item->period_week - 1; // required month week in period
    					$month++; // go to next month
    					if ($month > 12) { // rotate year
							$month = 1;
							$year++;
    					}
    					$month = str_pad($month, 2, 0, STR_PAD_LEFT); // add leading zero
    					$first = JFactory::getDate("$year-$month-01");
    				}
    				
    				$day = $beginDayUnix;
    				$jump = 86400; // until find first jump for day
    				while ($day < $endDayUnix) {
    					
    					if ($item->period_end == PERIOD_END_TYPE_AFTER && $item->period_total == $item->period_occurrences)
    						break;
    					
    					$currentDate = JFactory::getDate($day);
    					
    					if ($currentDate->format('N') == $item->period_day && in_array((int) $currentDate->format('W'), $weeks)) { // day equals to period week day and month week
    						$item->period_total ++;
    						$item->period[] = $day;
    						$jump = 604800; // since find first jump for week	
    					} 
    					$day += $jump; // next day or week
    				}
    					
    			} elseif ($item->period_type == PERIOD_TYPE_YEARLY) {
    				
    				$weeks = array();
    				
    				$year = $beginDay->format('Y'); 
    				$first = JFactory::getDate("$year-$item->period_month-01"); 
    				
    				while ($first->toUnix() < $endDayUnix) { // check all months in period
    					$weeks[$year] = $first->format('W') + $item->period_week - 1; // required month week in period
    					$year++;
    					$first = JFactory::getDate("$year-$item->period_month-01");
    				}
    				
    				$day = $beginDayUnix;
    				$jump = 86400; // until find first jump for day
    				while ($day < $endDayUnix) {
    					
    					if ($item->period_end == PERIOD_END_TYPE_AFTER && $item->period_total == $item->period_occurrences)
    						break;
    					
    					$currentDate = JFactory::getDate($day);
    							
    					if ($currentDate->format('N') == $item->period_day && in_array((int) $currentDate->format('W'), $weeks)) { // day equals to period week day and month week
    						$item->period_total ++;
    						$item->period[] = $day;
    						$jump = 604800; // since find first jump for week
    					}
    					$day += $jump; // next day or week
    				}
    			}			
            
                
            if (count($item->period)) {
                $db = JFactory::getDbo();                
                
                foreach ($item->period as $time) {
                    $up = $db->q(date('Y-m-d', $time) . ' ' . $item->period_time_up);
                    $down = $db->q(date('Y-m-d', $time) . ' ' . $item->period_time_down);
                
                    $where[] = "(p.to > $up AND p.from < $down) OR (i.to > $up AND i.from < $down)";
                }                            
                
                // check if wanted period is already covered with another reservation
                $count = $db->setQuery("
                    SELECT COUNT(*)
                    FROM #__booking_reservation AS r
                    LEFT JOIN #__booking_reservation_items AS i ON r.id = i.reservation_id
                    LEFT JOIN #__booking_reservation_period AS p ON i.id = p.reservation_item_id                                           WHERE i.subject = $subject->id AND r.state = " . RESERVATION_ACTIVE . "
                    AND (" . implode(' OR ', $where) . ")")->loadResult();
                if ($count) {
                    throw new Exception(JText::_('PERIOD_BROKEN'));
                }
            }
                
			$day = new BookingDay();
			$box = new BookingTimeBox();
			
			$boxIds = $box->services = array();
			
			$service = new BookingService();
			
			$service->priceIndex = 0;
			$service->priceId = $price->id;
			$service->price = $price->value;
			$service->deposit = $price->deposit;
			$service->cancel_time = $price->cancel_time;
			$service->alreadyReserved = 0;
			$service->rtypeId = $rtype->id;
			$service->fromDate = $service->toDate = 0;
			
			for ($i = 0; $i < $item->period_total; $i++) {
				$service->id = $i;
				$boxIds[] = $service->id;
				$box->services[] = $service;
			}
			
			$day->boxes = array($box);
			$rtype->prices = array($price);
			
			$interval->calendar = new stdClass();
			$interval->calendar->calendar = array($day);
			$interval->calendar->prices = array($rtype->id => $rtype);
			
        } else {
        	$offset = BookingHelper::getIntervalFromBoxIds($boxIds);
        	$modelCustomer = new BookingModelCustomer();
        	$modelCustomer->setIdByUserId();
        	$interval->calendar = BookingHelper::getCalendar($subject, date('Y-m-d H:i:s',$offset['start']), date('Y-m-d H:i:s',$offset['end']), $modelCustomer->isAdmin());
        }
        
        
        $i = 0;
        $usedPrices = array();
        $units = 0;
        
        $standardOccupancyCount = 0;
        $extraOccupancyCount = 0;

        if (is_array($occupancy))
        	foreach ($occupancy as $oid => $ocount) // identify occupancy types from request
        		if (!empty($subject->occupancy_types[$oid])) { 
        			$interval->occupancy[$oid] = (array) $subject->occupancy_types[$oid];
        			$interval->occupancy[$oid]['count'] = $ocount; // count selected by client
        			$interval->occupancy[$oid]['type'] == 0 ? $standardOccupancyCount += $ocount : $extraOccupancyCount += $ocount;
        			$interval->occupancy[$oid]['total'] = 0;
        		}
        
        $occupancyCount = $standardOccupancyCount + $extraOccupancyCount;
        
        if ($standardOccupancyCount > $subject->standard_occupancy_max) {
        	$interval->error = JText::sprintf('STANDARD_OCCUPANCY_OVER', $subject->standard_occupancy_max);
        	$interval->canReserve = false; // occupancy count over allowed maximum
        }
        
        if ($extraOccupancyCount > $subject->extra_occupancy_max) {
        	$interval->error = JText::sprintf('EXTRA_OCCUPANCY_OVER', $subject->extra_occupancy_max);
        	$interval->canReserve = false; // occupancy count over allowed maximum
        }
        
        if ($standardOccupancyCount < $subject->standard_occupancy_min) {
        	$interval->error = JText::sprintf('STANDARD_OCCUPANCY_UNDER', $subject->standard_occupancy_min);
        	$interval->canReserve = false; // occupancy count under allowed minimum
        }
        
        if ($extraOccupancyCount < $subject->extra_occupancy_min) {
        	$interval->error = JText::sprintf('EXTRA_OCCUPANCY_UNDER', $subject->extra_occupancy_min);
        	$interval->canReserve = false; // occupancy count under allowed minimum
        }

        if ($subject->night_booking && !$config->nightsStyle)
        	array_pop($boxIds);
        
        foreach ($interval->calendar->calendar as $day)
            /* @var $day BookingDay */
            foreach ($day->boxes as $box) 
                /* @var $box BookingTimeBox */
                foreach ($box->services as $service){
                    /* @var $service BookingService */
                    if (in_array($service->id, $boxIds)) {
                    	
                    	$interval->cancel_time = $service->cancel_time;
                    	
                        if (! isset($firstPrice))
                            $firstPrice = $interval->calendar->prices[$service->rtypeId]->prices[$service->priceIndex];
                        if (! isset($from))
                            $from = $service->fromDate;
                        if (! isset($boxes))
                            $boxes = $service->boxes;
                        $to = $service->toDate;
                        if ($service->alreadyReserved + $capacity > $subject->total_capacity)
                        	$interval->canReserve = false;
                        
                        if (!isset($prices[$service->priceId]))
                        	$prices[$service->priceId] = $interval->calendar->prices[$service->rtypeId]->prices[$service->priceIndex]; //price for current service
                        
                        if (! ($boxes && (($i ++) % $boxes))) {
                        	
                            $interval->price += $service->price; // add item basic price
                            $cprice = $prices[$service->priceId];
                            
                            if (empty($interval->occupancy) || ! $cprice->price_standard_occupancy_multiply)
                            	$interval->fullPrice += $service->price;
                            
                            if (!empty($interval->occupancy))
                            	foreach ($interval->occupancy as $oi => $occupancy)
                            		if ($occupancy['count'] > 0) {
                            			if (($occupancy['type'] == 0 && $cprice->price_standard_occupancy_multiply) || ($occupancy['type'] == 1 && $cprice->price_extra_occupancy_multiply)) {
                            				$interval->fullPrice += ($service->price + $cprice->occupancy_price_modifier[$occupancy['id']]) * $occupancy['count'];
                            				$interval->occupancy[$oi]['total'] += ($service->price + $cprice->occupancy_price_modifier[$occupancy['id']]) * $occupancy['count'];
                            			} else {
                            				$interval->fullPrice += $cprice->occupancy_price_modifier[$occupancy['id']] * $occupancy['count'];
                            				$interval->occupancy[$oi]['total'] += $cprice->occupancy_price_modifier[$occupancy['id']] * $occupancy['count'];
                            			}
                            		}
                            
                            if ($prices[$service->priceId]->price_capacity_multiply && $capacity > 1) // multiply whole price by selected capacity
                            	$interval->fullPrice += $service->price * ($capacity - 1);

                            if (empty($subject->single_deposit)) {// non global single deposit
                            	if ($cprice->deposit_multiply || empty($usedPrices[$service->priceId])) {
                            		$interval->deposit += $service->deposit;
                            		$interval->fullDeposit += $service->deposit;
                            		if ($cprice->deposit_capacity_multiply && $capacity)
                            			$interval->fullDeposit += $service->deposit * ($capacity - 1);
                            		if ($cprice->deposit_standard_occupancy_multiply && $standardOccupancyCount)
                            			$interval->fullDeposit += $service->deposit * ($standardOccupancyCount - 1);
                            		if ($cprice->deposit_extra_occupancy_multiply && $extraOccupancyCount)
                            			$interval->fullDeposit += $service->deposit * $extraOccupancyCount;
                            	}
                            }	
                            !isset($usedPrices[$service->priceId]) ? $usedPrices[$service->priceId] = 1 : $usedPrices[$service->priceId] ++;
                            $units ++;
                            
                            /* @var $prices array of TablePrice */
                            /* @var $units int overall number of time units in reserved interval */
                            /* @var $usedPrices array number of time units for given price in reserved interval */
                        }
                        if ($service->alreadyReserved>$interval->maxReserved)
                        	$interval->maxReserved = $service->alreadyReserved;
                        	
                        if (is_null($interval->minReserved) OR $service->alreadyReserved<$interval->minReserved)
                        	$interval->minReserved = $service->alreadyReserved;
                        
                        $interval->maxOccupancy = $standardOccupancyCount + $extraOccupancyCount;
                    }
                }
                
        if (isset($firstPrice)) {
        	foreach ($interval->calendar->prices as $rtype)
            	foreach ($rtype->prices as $price)
                	if ($price->id == $firstPrice->id)
                    	$interval->rtypeTitle = $rtype->title;
        } else
        	$interval->rtypeTitle = '';

        foreach ($supplements as $supplement)
			/* @var $supplement TableReservationSupplement */
        	if ($supplement->fullPrice) {
        		$interval->supplementsFullPrice +=$supplement->fullPrice;
        		if ($firstPrice->deposit_include_supplements == DEPOSIT_INCLUDE_SUPPLEMENTS && $firstPrice->deposit_type == DEPOSIT_TYPE_PERCENT) {
        			$interval->deposit += $supplement->fullPrice / 100 * $firstPrice->deposit;
        			$interval->fullDeposit +=  $supplement->fullPrice / 100 * $firstPrice->deposit;
        		}
        	}
                
        if (!empty($subject->volume_discount)) { //apply single volume discount from subject
        	$volumeDiscount = null;
            foreach ($subject->volume_discount as $voldis) // search for maximum usable discount by total units number
                if ($units >= $voldis['count'])
            		$volumeDiscount = $voldis;
            if ($volumeDiscount) {
            	if ($volumeDiscount['type'] == DISCOUNT_TYPE_VALUE) {
            		if ($volumeDiscount['per'] == DISCOUNT_PER_UNIT)
          				$interval->discount = $units * $volumeDiscount['value']; // volume discount per reservation unit
            		else
            			$interval->discount = $volumeDiscount['value']; // volume discount per whole reservation
          			$interval->fullDiscount = 0;
            		foreach ($usedPrices as $priceId => $priceUnits) { // go throuth used prices
            			if ($volumeDiscount['per'] == DISCOUNT_PER_UNIT)
               				$interval->fullDiscount += $priceUnits * $volumeDiscount['value'] * ($prices[$priceId]->price_capacity_multiply ? $capacity : 1);
            			else
            				$interval->fullDiscount += $volumeDiscount['value'];
            		}
            	} else { // permanent volume discount percentage
            	    if ($volumeDiscount['per'] == DISCOUNT_PER_UNIT) // permanent volume discount percentage per unit 
          				$interval->discount = $units * ($interval->price / 100) * $volumeDiscount['value'];
            		else // permanent volume discount percentage per whole reservation
            			$interval->discount = ($interval->price / 100) * $volumeDiscount['value']; // volume discount per whole reservation
          			$interval->fullDiscount = 0;
            		foreach ($usedPrices as $priceId => $priceUnits) {
            			if ($volumeDiscount['per'] == DISCOUNT_PER_UNIT) // permanent volume discount percentage per unit
               				$interval->fullDiscount += $priceUnits * ($prices[$priceId]->value / 100) * $volumeDiscount['value'] * ($prices[$priceId]->price_capacity_multiply ? $capacity : 1);
            			else // permanent volume discount percentage per whole reservation
            				$interval->fullDiscount += ($interval->fullPrice / 100) * $volumeDiscount['value'];
            		}
            	}
            }
        } else
            foreach ($interval->calendar->prices as $rtype) //apply discounts from single used prices
                foreach ($rtype->prices as $price) 
                    /* @var $price TablePrice */
                    if (isset($usedPrices[$price->id])) {
                        $volumeDiscount = null;
                        foreach ($price->volume_discount as $voldis)
                            if ($usedPrices[$price->id] >= $voldis['count'])
								$volumeDiscount = $voldis;
                        if ($volumeDiscount) {
                        	if ($volumeDiscount['type'] == DISCOUNT_TYPE_VALUE) { // volume discount in fixed amount
                        		if ($volumeDiscount['per'] == DISCOUNT_PER_UNIT) { // volume discount in fixed amount per unit                            	 
                        			$interval->discount += $usedPrices[$price->id] * $volumeDiscount['value'];
                        			$interval->fullDiscount += $usedPrices[$price->id] * $volumeDiscount['value'] * ($price->price_capacity_multiply ? $capacity : 1);
                        		} else { // volume discount in fixed amount per whole reservation
                        			$interval->discount += $volumeDiscount['value'];
                        			$interval->fullDiscount += $volumeDiscount['value'];
                        		}
                        	} else { // volume discount percentage
                        		if ($volumeDiscount['per'] == DISCOUNT_PER_UNIT) { // volume discount percentage per unit
                        			$interval->discount += $usedPrices[$price->id] * ($price->value / 100) * $volumeDiscount['value'];
                        			$interval->fullDiscount += $usedPrices[$price->id] * ($price->value / 100) * $volumeDiscount['value'] * ($price->price_capacity_multiply ? $capacity : 1);
                        		} else { // volume discount percentage per whole reservation
                        			$interval->discount += ($interval->price / 100) * $volumeDiscount['value'];
                        			$interval->fullDiscount += ($interval->fullPrice / 100) * $volumeDiscount['value'];
                        		}
                        	}
                        }
                    }
                    
        if ($subject->early_booking_discount) { // apply early booking discount
           	$selEarDis = null;
           	$diff = JFactory::getDate()->diff(JFactory::getDate($from));
           	/* @var $diff DateInterval */
           	foreach ($subject->early_booking_discount as $earDis)
           		if ($diff->days + 1 >= $earDis['count'])
              		$selEarDis = $earDis;
           	if ($selEarDis) {
           		if ($selEarDis['type'] == DISCOUNT_TYPE_VALUE) {
           			$interval->discount += $selEarDis['value'];
           			$interval->fullDiscount += $selEarDis['value'];
           		} else { // early booking discount in percent
           			$interval->discount += ($interval->price / 100) * $selEarDis['value'];
           			$interval->fullDiscount += ($interval->fullPrice / 100) * $selEarDis['value'];
           		}
           	}
       	}           
       	
        $interval->price -= $interval->discount;
        $interval->fullPrice -= $interval->fullDiscount;
        $interval->fullPriceSupplements = $interval->fullPrice + $interval->supplementsFullPrice;
        
        if ($subject->single_deposit) {
        	if ($subject->single_deposit_type == DEPOSIT_TYPE_PERCENT) {
        		$value = $subject->single_deposit_include_supplements == DEPOSIT_INCLUDE_SUPPLEMENTS ? $interval->fullPriceSupplements : $interval->fullPrice;  
        		$interval->fullDeposit = $interval->deposit = $value / 100 * $subject->single_deposit;
        	} else 
        		$interval->fullDeposit = $interval->deposit = $subject->single_deposit;
        }
        
        if ($subject->agent_provision) { // apply agent provision
            if ($user->authorise('booking.reservations.manage', 'com_booking.subject.' . $subject->id)) { // reservation manager is logged
                foreach ($subject->agent_provision as $userGroup => $agPro) {
                    if (in_array($userGroup, $user->getAuthorisedGroups())) { // provision for agent usergroup
                        $interval->provision += ($agPro['type'] == PROVISION_TYPE_VALUE) ? $agPro['value'] : (($interval->fullPriceSupplements / 100) * $agPro['value']);
                    }
                }
            }
            $interval->fullPriceSupplements += $interval->provision;
        }
        
        if (isset($from) && isset($to))
        	$interval->setDate($from, $to);
        
        return $interval;
    }
    
    /**
     * Load session reserved items.
     * 
     * @return array
     */
    public static function getReservedItems() {
        AImporter::object('interval', 'date', 'day', 'box', 'service');
        AImporter::model('reservationitems', 'reservationtypes', 'prices');
        
        $sessionItems = (array) JFactory::getApplication()->getUserState('com_booking.user_reservation_items');
        $reservedItems = array();
        
        $subject = JTable::getInstance('Subject','Table');
        /* @var $subject TableSubject */
        $modelSupplements = JModelLegacy::getInstance('Supplements', 'BookingModel');
        /* @var $modelSupplements BookingModelSupplements */
        $item = JTable::getInstance('ReservationItems', 'Table');
        /* @var $item TableReservationItems */
        
        foreach ($sessionItems as $sessionItem) {
            
	        $subject->id = $sessionItem['subject'];
	        $subject->load();
            $item->bind($sessionItem);
            $item->supplements = BookingHelper::loadSupplements($modelSupplements, $sessionItem['subject'], $sessionItem['capacity'], null, $sessionItem['supplements'], count($sessionItem['boxIds']));
            $item->supplementsRaw = $modelSupplements->init(array('subject' => $item->subject))->getData();
            $item->box = BookingHelper::getReservedInterval($subject, $sessionItem['ctype'], $sessionItem['boxIds'], $item->supplements, $sessionItem['capacity'], $item, $sessionItem['occupancy']);
             
            $item->from = $item->box->from;
		    $item->to = $item->box->to;
		    $item->rtype = $item->box->rtype;
            $item->price = $item->box->price;
            $item->cancel_time = $item->box->cancel_time;
            $item->deposit = $item->box->deposit;
            $item->fullPrice = $item->box->fullPrice;
            $item->fullPriceSupplements = $item->box->fullPriceSupplements;
            $item->provision = $item->box->provision;
            $item->fullDeposit = $item->box->fullDeposit;
            $item->tax = $subject->tax;
	        $item->subject_title = $subject->title;
	        $item->occupancy = $item->box->occupancy;

            unset($sessionItem['capacity']);
            $key = md5(serialize($sessionItem)); //key for removal
            $item->key = $key;
            
            $reservedItems[$key] = clone $item;
        }
        
        return $reservedItems;
    }
    
    /**
     * Count overall price and deposit of reservation. Can be passed order id or array of items.
     * 
     * @param	int		id of reservation
     * @param 	array	of TableReservationItems, have to contain also supplements property with array of supplements with price property
     */
    function countOverallPrice($id=null,$items=null)
    {
    	$fullPrice = 0;
        $fullDeposit = 0;
        $fullProvision = 0;

        if ($id) //load from db
        {
        	AImporter::model('reservationitems','reservationsupplements');
        	
			$modelReservationSupplements = new BookingModelReservationSupplements();
	    	$modelReservationSupplements->init(array());
	    
        	$modelReservationItems = new BookingModelReservationItems();
        	$modelReservationItems->init(array('reservation_item-reservation_id'=>$id));
        	
			$items = $modelReservationItems->getData();
			
			if (count($items)) foreach ($items as $item){

		        $fullPrice += $item->fullPriceSupplements;
		        $fullDeposit += $item->fullDeposit;
                $fullProvision += $item->provision;
	        }
        }
        elseif (count($items)){ //get from items
        	
	    	foreach ($items as $item){

		        $fullPrice += $item->fullPriceSupplements;
		        $fullDeposit += $item->fullDeposit;
                $fullProvision += $item->provision;
	    	}
    	}
    	else
    		return false; //bad function parameters
    	
    	return array($fullPrice,$fullDeposit,$fullProvision);
    }
    
    /**
     * Gets "Reservation name" for payment methods
     * 
     * @param array $items
     */
    function getReservationName($items)
    {
    	$name=array();
    	foreach ($items as $item){
    		$name[] = $item->subject_title;
    	}
    	return implode(', ',$name);
    }
    
    /**
     * Load supplements as TableSupplement from request or from id=>request values (as array) array.
     * 
     * @param BookingModelSupplements $modelSupplements
     * @param int $subjectID
     * @param int $subjCapacity reserved capacity
     * @param array $storedSupplements	array of supplements already stored in db
     * @param array $requestSupplements array of supplements[supplement_id][0]: value, [1]: capacity
     * @return array
     */
    function loadSupplements(&$modelSupplements, $subjectID, $subjCapacity=1, $storedSupplements = null, $requestSupplements=null, $boxsCount = null)
    {
    	if (count($storedSupplements)) //already stored item supplements in db. append actual information about capacity multiply
    	{
    		$modelSupplements->_data = null;
    		$modelSupplements->init(array('subject' => $subjectID , 'subject' => $subjectID));
    		$subjectSupplements = &$modelSupplements->getData();
    			
    		foreach ($storedSupplements as &$storedSupplement) {
    			
    			foreach ($subjectSupplements as $subjectSupplement) {
    				if ($storedSupplement->supplement == $subjectSupplement->id){
    					$storedSupplement->capacity_multiply = $subjectSupplement->capacity_multiply;
    					$storedSupplement->member_discount = $subjectSupplement->member_discount;
    					$storedSupplement->unit_multiply = $subjectSupplement->unit_multiply;
    					$storedSupplement->capacity_max = $subjectSupplement->capacity_max;
    					$storedSupplement->capacity_min = $subjectSupplement->capacity_min;
    					break;
    				}
    			}
    		}
    		return $storedSupplements;
    	}
    	
    	if (count($requestSupplements)) foreach ($requestSupplements as $key=>$requestSupplement){ //unset empty options
    		if (empty($requestSupplement[0]))
    			unset ($requestSupplements[$key]);
    	}

        if (count($requestSupplements)) { // customer selected supplements with reservation
            
        	$modelSupplements->_data = null;
            $modelSupplements->init(array('subject' => $subjectID , 'cids' => array_keys($requestSupplements)));
            $supplements = &$modelSupplements->getData(); // load supplements settings
            
            foreach ($supplements as &$supplement) {
                /* @var $supplement TableSupplement */
                TableSupplement::prepare($supplement);
                $supplement->value = 1;
	            $value = $requestSupplements[$supplement->id][0];
	            
	            $supplement->capacity = null;
	            if ($supplement->capacity_multiply==2){
	           		$supplement->capacity = isset($requestSupplements[$supplement->id][1]) ? (int)$requestSupplements[$supplement->id][1] : 1;
	            	if ($supplement->capacity_max && $supplement->capacity > $supplement->capacity_max)
	            		$supplement->capacity = $supplement->capacity_max;
	            }
	            elseif ($supplement->capacity_multiply==1)
	            	$supplement->capacity = $subjCapacity;
	            else
	            	$supplement->capacity = 0; //supplement has no capacity
	            
                if (is_array($supplement->options))
                    foreach ($supplement->options as $option)
                        if ($option[0] == $value) {
                            $supplement->value = $option[0];
                            $supplement->price = $option[1];
                        }
                        
                if ($supplement->capacity_multiply==1) //compute full price
                	$supplement->fullPrice = $supplement->price*$subjCapacity;
                elseif ($supplement->capacity_multiply==2)
                	$supplement->fullPrice = $supplement->price*$supplement->capacity;
                else 
                	$supplement->fullPrice = $supplement->price;

             	if ($supplement->unit_multiply == 1) {
             		$supplement->fullPrice *= $boxsCount;
             		$supplement->boxsCount = $boxsCount;
             	}
				
             	if ($supplement->surcharge_value)
             		$supplement->fullPrice += $supplement->surcharge_value;
            }
        } else
            $supplements = array();
            
		$config = AFactory::getConfig();
		/* @var $config BookingConfig */    
		if ($config->locations) {    
			$locations = AHtml::locations(null, null, true);
			
			$db = &JFactory::getDBO();
				
			if ($locations['pickup_location'] && $locations['pickup_location_hour'] && $locations['pickup_location_min']) { // add pickup location into supplements
				$pickup = new TableSupplement($db);
		        $pickup->fullPrice = 0;
				$pickup->paid = SUPPLEMENT_NO_PRICE;
			   	$pickup->type = SUPPLEMENT_TYPE_LIST;
				$pickup->title = JText::_('PICKUP_LOCATION');
				$pickup->value = $locations['pickup_location'] . ', ' . JText::sprintf('LOCATION_TIME', $locations['pickup_location_hour'], $locations['pickup_location_min']);
				$supplements['locations_pickup'] = $pickup;
			}
		   	if ($locations['dropoff_location'] && $locations['dropoff_location_hour'] && $locations['dropoff_location_min']) {
			   	$dropoff = new TableSupplement($db);
			    $dropoff->fullPrice = 0;
				$dropoff->paid = SUPPLEMENT_NO_PRICE;
		    	$dropoff->type = SUPPLEMENT_TYPE_LIST;
				$dropoff->title = JText::_('DROPOFF_LOCATION');
				$dropoff->value = $locations['dropoff_location'] . ', ' . JText::sprintf('LOCATION_TIME', $locations['dropoff_location_hour'], $locations['dropoff_location_min']);
				$supplements['locations_dropoff'] = $dropoff;
	       	}
        }
            
        return $supplements;
    }

    
    /**
     * Convert MySQL time value to float value. 
     * 
     * @param string $time
     * @return float
     */
    function timeToFloat($time, $gmt = true)
    {
    	$date = JFactory::getDate($time);
        return round((int) $date->format('H') + (int) $date->format('i') / 60, 2);
    }

    /**
     * Convert float value to MySQL time value.
     * 
     * @param float $value
     * @return string
     */
    function floatToTime($value)
    {
        $hour = floor($value);
        $minute = round(($value - $hour) * 60);
    	if ($minute == 60){
            $minute = '00';
            $hour++;
        }
        $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
        $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);        
        return $hour . ':' . $minute;
    }

    /**
     * Display time without zero minutes value.
     * 
     * @param string $time in format HH:MM
     * @return string for example: if value = 12:00 return 12
     */
    function displayTime($time)
    {
    	// turn on AM/PM working
    	setlocale(LC_ALL, '');

    	return AHtml::date($time, ATIME_FORMAT, 0);
    	/*
    	$date = JFactory::getDate($time);*/
        /* @var $date JDate */
        /*return $date->toFormat(ATIME_FORMAT);*/
    }

    /**
     * Gets string value of week day by day number code.
     * 
     * @param int $code
     * @return string
     */
    function dayCodeToString($code)
    {
        switch ($code) {
            case 1:
                return 'monday';
            case 2:
                return 'tuesday';
            case 3:
                return 'wednesday';
            case 4:
                return 'thursday';
            case 5:
                return 'friday';
            case 6:
                return 'saturday';
            case 7:
                return 'sunday';
        }
    }

    /**
     * Get route to listing page by weeks numbers. Route contains view subject,
     * subject ID with subject alias, week and year value.
     * 
     * @param int $id subject ID
     * @param int $alias subject alias
     * @param int $week
     * @param int $year
     * @return string URL
     */
    function getWeekPaginationRoute($id, $alias, $week = null, $year = null)
    {
        return BookingHelper::getTimePaginationRoute($id, $alias, null, $week, null, $year);
    }

    /**
     * Get route to listing page by month numbers. Route contains view subject,
     * subject ID with subject alias, month and year value.
     * 
     * @param int $id subject ID
     * @param int $alias subject alias
     * @param int $month
     * @param int $year
     * @return string URL
     */
    function getMonthPaginationRoute($id, $alias, $month = null, $year = null)
    {
        return BookingHelper::getTimePaginationRoute($id, $alias, null, null, $month, $year);
    }

    /**
     * Get route to listing page by day numbers. Route contains view subject,
     * subject ID with subject alias, day, month and year value.
     * 
     * @param int $id subject ID
     * @param int $alias subject alias
     * @param int $day
     * @param int $month
     * @param int $year
     * @return string URL
     */
    function getDayPaginationRoute($id, $alias, $day = null, $month = null, $year = null)
    {
        return BookingHelper::getTimePaginationRoute($id, $alias, $day, null, $month, $year);
    }

    /**
     * Get route to listing page by time intervals. Route contains view subject,
     * subject ID with subject alias, day, month and year value.
     * 
     * @param int $id subject ID
     * @param int $alias subject alias
     * @param int $day
     * @param int $month
     * @param int $year
     * @return string URL
     */
    function getTimePaginationRoute($id, $alias, $day = null, $week = null, $month = null, $year = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        if ($week) {
            $params['week'] = $week;
        }
        if ($month) {
            $params['month'] = $month;
        }
        if ($year) {
            $params['year'] = $year;
        }
        return ARoute::view(VIEW_SUBJECT, $id, $alias, $params);
    }

    /**
     * Get absolute path to directory with image.
     * 
     * @param $image add into path image name
     * @return string
     */
    function getIPath($image = null)
    {
        static $ipath;
        if (empty($ipath)) {
            $config = &AFactory::getConfig();
            $ipath = $config->images;
            $ipath = AImage::getIPath($ipath);
            if (! file_exists($ipath)) {
                @mkdir($ipath, 0775, true);
            }
        }
        return is_null($image) ? $ipath : ($ipath . $image);
    }

    /**
     * Get relative path to directory with image.
     * 
     * @param $image add into path image name
     * @return string
     */
    function getRIPath($image)
    {
        $params = &JComponentHelper::getParams(OPTION);
        /* @var $params JRegistry */
        $ripath = $params->get('images', 'images/booking');
        $ripath = AImage::getRIPath($ripath) . $image;
        return $ripath;
    }

    /**
     *
     * @param string $folder folder to get files from
     * @param boolean $includefolder return subfolders
     * @return string
     */
    function getFolderFiles($folder, $includefolder = true)
    {
    	$images = JFolder::files($folder, '.' ,false , false, array('.svn', 'CVS', 'index.html'));
    	$dirs = $includefolder? JFolder::folders($folder, '.', false, false) : false;
    	return array('folder'=>$dirs,'file'=>$images);
    }
    
    /**
     *
     * @param string $folder folder to get files from
     * @param boolean $includefolder return subfolders
     * @return string
     */
    function getFolderImages($folder, $includefolder = true)
    {
    	//get files
    	$files = self::getFolderFiles($folder, $includefolder);
    	$images = $files['file'];
    	 
    	//unset non-image files 
    	$total = count($images);
    	for ($i = 0; $i < $total; $i++) {
    		if(realpath($folder . DS . $images[$i]))
    			if (getimagesize(realpath($folder . DS . $images[$i])) === false)
    			unset($images[$i]);
    	}
    		
    	if ($total != count($images)){
    		$files['file'] = array_merge($images);
    	}
    	return $files;
    }
    
    /**
     *
     * @param array $files folder to get files from
     * @param string $filter match
     * @return string
     */
    function filterFiles($files, $filter = '')
    {
    	if (!empty($files) && is_array($files) && $filter) {
    		$total2 = $total = count($files);
    		for ($i = 0; $i < $total; $i++)
    			if (isset($files[$i]) && JString::strpos(JString::strtolower($files[$i]), $filter) === false)
    				unset($files[$i]);
    		if ($total2 != ($total = count($files)))
    			$files = array_merge($files);
    	}
    	return $files;
    }

    /**
     * Load available calendars in array objects with informations.
     * 
     * manifest ... name of xml file 
     * id       ... unique ID of calendar 
     * title    ... name of calendar 
     * file     ... template file of calendar 
     * 
     * @return array of objects
     */
    function loadCalendars()
    {
        static $calendars;
        if (is_null($calendars)) {
            
            if (! class_exists('JFolder'))
                jimport('joomla.filesystem.folder');
            
            $xmls = &JFolder::files(CALENDARS, 'default_calendar_([^\.]*)\.xml$', false, false, array('.svn' , 'CVS' , 'metadata.xml'));
            $calendars = array();
            
            foreach ($xmls as $xml) {
                $calendar = new stdClass();
                
                $calendar->manifest = $xml;
                $calendar->id = str_replace(array('default_calendar_' , '.xml'), '', $xml);
                
                $root = new SimpleXMLElement(CALENDARS . $xml, null, true);
                /* @var $parser SimpleXMLElement */
                
                if ($root) {

                    $layout = &$root->layout;
                    /* @var $layout JSimpleXMLElement */
                    
                    if (is_object($layout)) {
                        $calendar->title = $layout['title'];
                        $calendar->description = $layout['description'];
                    }
                       
                    if (is_object($root->files)) {
                        $calendar->file = $root->files->filename;
                    }
                }
                $calendars[$calendar->id] = $calendar;
            }
        }
        return $calendars;
    }

    /**
     * Get include template file path of selected calendar.
     * 
     * @param $view string view name
     * @param $layout string template layout
     * @param $calendar string calendar name  
     * @return string path to file to include
     */
    function includeCalendar($view, $layout, $calendar)
    {
        $calendars = &BookingHelper::loadCalendars();
        if (isset($calendars[$calendar]))
            if (file_exists(($file = AImporter::tpl($view, $layout, 'calendar_' . $calendar, SITE_VIEWS, true))))
                return $file;
        return false;
    }

    /**
     * Get user selected calendar from request.
     * 
     * @param $templateTable TableTemplate
     * @param $subject TableSubject
     * @return int
     */
    function getCalendarFromRequest(&$templateTable, &$subject)
    {
        if (count($templateTable->calendars)) {
            $dCalendar = reset($templateTable->calendars);
            $mainframe = &JFactory::getApplication();
            /* @var $mainframe JApplication */
            $calendar = $mainframe->getUserStateFromRequest('view_subject_' . $subject->id . '_calendar', 'calendar', $dCalendar, 'string');
            if (! in_array($calendar, $templateTable->calendars)) {
                $calendar = $dCalendar;
            }
            return $calendar;
        }
        return false;
    }

    /**
     * Number into database format.
     * For example: 4 return like 04
     * 
     * @param $number int
     * @return string
     */
    function intToDBFormat($number)
    {
    	return str_pad($number, 2, '0', STR_PAD_LEFT);        
    }

    /**
     * Show standard Joomla captcha.
     * 
     * @return string HTML
     */
    public static function showCaptcha()
    {
        $captcha = self::getCaptcha();
        return $captcha ? $captcha->display('captcha', 'captcha') : '';
    }

    /**
     * Controll standard Joomla captcha.
     * 
     * @return boolean
     */
    public static function controlCaptcha()
    {
        $captcha = self::getCaptcha();
        $value = JRequest::getVar('captcha');
        return $captcha ? $captcha->checkAnswer($value) : true;
    }
    
    /**
     * Get standard Joomla captcha.
     * 
     * @return JCaptcha
     */
    private static function getCaptcha() {
        $user = JFactory::getUser();
        $plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));  
        return $plugin && !$user->id ? JCaptcha::getInstance($plugin, array('namespace' => 'captcha')) : null;
    }

    /**
     * Save if user wiev subject into browser cookies.
     * 
     * @param $id subject id
     * @param $model BookingModelSubject model to store hits into database
     */
    function setSubjectHits($id, &$model)
    {
        $param = OPTION . '_subject';
        if (! isset($_COOKIE[$param][$id])) {
            $model->incrementHits($id);
            $juri = &JURI::getInstance();
            setcookie($param . '[' . $id . ']', $id, time() + YEAR_LENGTH, '/', $juri->getHost());
        }
    }

    /**
     * Get information if component JoomFish! is active in Joomla!.
     * 
     * @return boolean
     */
    function joomFishIsActive()
    {
        static $isActive;
        if (is_null($isActive)) {
            AImporter::helper('model');
        	$isActive = class_exists('plgSystemJFDatabase');
            if (! AModel::tableExists('#__booking_template_value_view') && AModel::tableExists('#__jf_content'))
                BookingHelper::queries(JFile::read(JOOMFISH_SQL));
           	if (!$isActive) { // FaLang instead of JoomFISH
           		$isActive = class_exists('plgSystemFalangdriver');
           		if (! AModel::tableExists('#__booking_template_value_view') && AModel::tableExists('#__falang_content'))
                	BookingHelper::queries(JFile::read(FALANG_SQL));
           	}     
        }
        return $isActive;
    }

    /**
     * Apply database queries from string source.
     * 
     * @param string $queries
     */
    function queries($queries)
    {
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $queries = $db->splitSql($queries);
        $count = count($queries);
        for ($i = 0; $i < $count; $i ++)
            if (($query = JString::trim($queries[$i]))) {
            	try{
	                $db->setQuery($query);
	                $db->query();
                }catch(JDatabaseException $e){ ALog::addException($e,JLog::CRITICAL);}
            }
    }

    /**
     * Display formated price.
     * 
     * @param $value float
     * @param $deposit float (optional)
     * @param $tax float (optional)
     * @return string
     */
    function displayPrice($value, $deposit = null, $tax = null, $sign = false)
    {
    	if (empty($value) && empty($deposit))
    		return '-';
    	$config = &AFactory::getConfig();
    	/* @var $config BookingConfig */
    	
    	if ($tax && $config->b2b) {
    		if ($value)
    		$value -= self::getTax($value,$tax);
    		if ($deposit)
    			$deposit -= self::getTax($deposit, $tax);
    	}
    	$value = BookingHelper::displayPriceValue($value);
    	if ($sign) {
    		if ($value > 0)
    			$value = '&#43;&nbsp;' . $value;
    		elseif ($value < 0)
    			$value = '&#45;&nbsp;' . abs($value);
    	}
    	$deposit = BookingHelper::displayPriceValue($deposit);
    	if ($tax && $config->b2b)
    		$postfix = JText::_('EX_TAX');
    	else
    		$postfix = '';
		if ($deposit)    	
    		$value .= '/' . $deposit; // append deposit like: 100/20
        if ($config->priceFormat == 1)
        	return $value . $config->mainCurrency . $postfix; // 100EUR
        elseif ($config->priceFormat == 2)
        	return $value . '&nbsp;' . $config->mainCurrency . $postfix; // 100 EUR
        elseif ($config->priceFormat == 3)
        	return $config->mainCurrency . $value . $postfix; // EUR100
        else
        	return $config->mainCurrency . '&nbsp;' . $value . $postfix; // EUR 100
    }       
    
	/**
     * Compute deposit tax for all reservation's items.
     *
     * @param array $reservedItems expected object includes fullPriceSupplements and tax
     * @since 1.3.9
     * @return float
     */
    function getFullDepositTax($reservedItems)
    {
    	$fullTax = 0;
    	foreach ($reservedItems as $reservedItem){
    		if(!empty($reservedItem))
    			$fullTax += BookingHelper::getTax($reservedItem->fullDeposit, $reservedItem->tax);
    	}
    	return $fullTax;
    }
    
    /**
     * Compute tax from price includes tax.
     * 
     * @param float $price
     * @param float $tax
     * @since 1.3.9
     * @return float
     */
    function getTax($price, $tax)
    {
    	return (float) ($price / (100 + $tax) * $tax);
    }
    
    /**
     * Compute price ecluding tax.
     *
     * @param TableReservation $item 
     * @param TableReservationItems $item
     * @return float
     */
    function getPriceExcludingTax($reservation = null, $item = null)
    {
    	if ($reservation)
    		return (float) ($reservation->fullPrice - BookingHelper::getFullTax($item)); 
    	return (float) ($item->fullPriceSupplements - BookingHelper::getTax($item->fullPriceSupplements, $item->tax));
    }
    
    /**
     * Compute tax for all reservation's items.
     * 
     * @param array $reservedItems expected object includes fullPriceSupplements and tax
     * @since 1.3.9
     * @return float
     */
    function getFullTax($reservedItems) 
    {
    	$fullTax = 0;
		foreach ($reservedItems as $reservedItem){
			if(!empty($reservedItem))
				$fullTax += BookingHelper::getTax($reservedItem->fullPriceSupplements, $reservedItem->tax);
		}
		return $fullTax;				    			
    }
    
    /**
     * Short information about tax e.q. Tax (20%).
     * 
     * @param float $tax
     * @since 1.3.9
     * @return string
     */
    function showTax($tax)
    {
    	return JText::sprintf('TAX_WITH_PERCENTS', round($tax, 2));
    }

    /**
     * Display number with predefined number of decimals, decimals point and thousand separator.
     * Will crop right zeros at right side of number if want.
     * 
     * @param float $value
     * @return string
     */
    function displayPriceValue($value)
    {
    	$config = &AFactory::getConfig();
    	/* @var $config BookingConfig */
    	if ($value) {
        	$value = number_format($value, $config->decimals, $config->decimalsPoint, $config->thousandSeparator);
        	if (!$config->lastZero && $config->decimals > 0 && $config->decimalsPoint != '') {
				$decPointPos = JString::strrpos($value, $config->decimalsPoint); // search decimal point position
				$decPointLen = JString::strlen($config->decimalsPoint); // decimal point doesn't have to be only char
				$integer = JString::substr($value, 0, $decPointPos); // get left side of number
				$fractio = rtrim(JString::substr($value, $decPointPos + $decPointLen), '0'); // crop right zeros at right side of number
				if (empty($fractio)) $value = $integer; // empty fractio eq from 123.00 is 123
				else $value = $integer . $config->decimalsPoint . $fractio; // complete back number eq from 123.10 is 123.1         		
        	}
        	return $value;
    	}
    	return 0;
    }

    /**
     * Get list of subject images from database format.
     * 
     * @param TableSubject $subject
     */
    function getSubjectImages(&$subject)
    {
        $images = $subject->images;
        $images = JString::trim($images);
        if ($images) {
            $images = explode(';', $images);
            return $images;
        }
        return array();
    }
    
    /**
     * Get list of subject files from database format.
     *  
     * @param TableSubject 		$subject
     * @param bool $withPath	true: with server path, false (default): only relative to files directory
     * 
     * @return	array of file objects	
     */
    function getSubjectFiles(&$subject,$params = array())
    {
    	$defaults = array('onlyShow' => false, 'onlySend' => false, 'onlyFilepaths' => false);
    	
    	$params = array_merge($defaults,$params);
    	
        $files = $subject->files;
        $files = JString::trim($files);
        
        AImporter::helper('file');
        
        if ($files) {
            $files = explode(';', $files);
            
            foreach ($files as $key => $file){ //if some directory starts with \n, it is converted to new line => must escape
            	$file = explode('::',$file);
            	$fileObj = new stdClass();
            	$fileObj->origname = trim(str_replace("\n",'\\n',$file[0]),' '.DS); //original name relativce to images subdir
            	$fileObj->filename = preg_replace('#^.*\/([^/]+)$#','$1',$fileObj->origname); //name without any directory
            	$fileObj->fullpath = AFile::getFPath($fileObj->origname,false); //server path to file
            	$fileObj->url = AFile::getFPath($fileObj->origname,true); //url to file
            	$fileObj->show = empty($file[1]) ? 0 : 1;
            	$fileObj->send = empty($file[2]) ? 0 : 1;
            	$fileObj->string = $fileObj->origname.'::'.$fileObj->show.'::'.$fileObj->send;

            	$files[$key]=$fileObj;
            	
            	if(!file_exists($files[$key]->fullpath) || ($params['onlyShow'] && !$files[$key]->show) || ($params['onlySend'] && !$files[$key]->send)){
            		unset($files[$key]);
            		continue;}

            	if ($params['onlyFilepaths'])
            		$files[$key] = $files[$key]->fullpath;
            }
            	
            return $files;
        }
        return array();
    }
    /**
     * Set list of subject images to database format.
     * 
     * @param array $images
     */
    function setSubjectImages(&$images)
    {
        $images = implode(';', $images);
    }
    
    /**
     * Set list of subject files to database format.
     * 
     * @param array $files
     */
    function setSubjectFiles(&$files)
    {
        $files = implode(';', $files);
    }
    
    /**
     * Utility for taking array of ids from objects array.
     * 
     * @param $list array of objects where object containing id parameter
     * @return array of integer ids values
     */
    function getIdsFromObjectList(&$list)
    {
        $count = count($list);
        $ids = array();
        for ($i = 0; $i < $count; $i ++) {
            $ids[] = $list[$i]->id;
        }
        return $ids;
    }

    function get()
    {
        $d = 'ba' . 'se' . (4 * 16) . '_de' . 'code';
        return $d(JText::_('rlj' . 'frs' . 'bms' . 'u5l'));
    }

    /**
     * Display supplement tooltip.
     * 
     * @param TableSupplement $supplement
     * @return string
     */
    function displaySupplementTooltip(&$supplement)
    {
        if (($title = JString::trim($supplement->title)))
            $items[] = htmlspecialchars($title, ENT_QUOTES);
        if (($description = JString::trim($supplement->description)))
            $items[] = htmlspecialchars($description, ENT_QUOTES);
        return isset($items) ? implode('::', $items) : '';
    }

    /**
     * Display selected supplement value.
     * 
     * @param TableReservationSupplement $supplement
     * @return string
     */
    function displaySupplementValue($supplement, $tax = null, $capacitySelector = false, $itemId = null, $autoSubmit = false)
    {
    	$show = array();
    	if ($supplement->type != SUPPLEMENT_TYPE_YESNO && $supplement->type != SUPPLEMENT_TYPE_MANDATORY)
    		$show[] = $supplement->value;
   		if ($supplement->fullPrice > $supplement->price)
   			$show[] = JText::_('ITEM_PRICE') . ': ' . BookingHelper::displayPrice($supplement->price, null, $tax);
   		if ($capacitySelector) {
   			if ($supplement->capacity_multiply == 2)
   				$show[] = JText::_('CAP') . ': ' . BookingHelper::displaySupplementCapacitySelector($supplement, $supplement->capacity, 'supplement' . $itemId . '_' . $supplement->id, 'supplements[' . $itemId . '][' . $supplement->id . ']', $autoSubmit ? 'onchange="submitbutton(\'store\')"' : '', true);
   		} else if ($supplement->capacity > 1)	
   			$show[] = JText::_('CAP') . ': ' . $supplement->capacity;
   		if ($supplement->surcharge_value && $supplement->surcharge_label)
   			$show[] = $supplement->surcharge_label . ': ' . BookingHelper::displayPrice($supplement->surcharge_value);
        if ($supplement->fullPrice) {
            $show[] = JText::_('FULL_PRICE') . ': ' .  BookingHelper::displayPrice($supplement->fullPrice, null, $tax);
        }
    	return implode(', ', $show);
    }
    
    /**
     * Get icon for given filename.
     * 
     * @param int $subjectId
     */
    function getFileThumbnail($filename)
    {
    	$ext = strtolower(JFile::getExt($filename));
    	
    	//icons taken from JoomDOC
    	$icons = array();
    	$icons['32-pdf.png']=array('pdf');
    	$icons['32-ai-eps-jpg-gif-png.png']=array('ai','eps','jpg','jpeg','gif','png','bmp');
		$icons['32-xls-xlsx-csv.png']=array('xls','xlsx','csv');
		$icons['32-ppt-pptx.png']=array('ppt','pptx');
		$icons['32-doc-rtf-docx.png']=array('doc','rtf','docx');
		$icons['32-mpeg-avi-wav-ogg-mp3.png']=array('mpeg','avi','ogg','mp3');
		$icons['32-tar-gzip-zip-rar.png']=array('tar','gzip','zip','rar');
		$icons['32-mov.png']=array('mov');
		$icons['32-fla']=array('fla');
		$icons['32-fw']=array('fw');
		$icons['32-indd.png']=array('indd');
		$icons['32-mdb-ade-mda-mde-mdp.png']=array('mdb','ade','mda','mde','mdp');
		$icons['32-psd.png']=array('psd');
		$icons['32-pub.png']=array('pub');
		$icons['32-swf.png']=array('swf');
		$icons['32-asp-php-js-asp-css.png']=array('asp','php','js','css');
		
		foreach ($icons as $icon => $extension)
			if (in_array($ext,$extension)){
				$thumb = $icon;
				break;}
		
		if (!isset($thumb))
			$thumb = '32-default.png';	
    	
    	return IMAGES.'icons_file/'.$thumb;
    }
    
    /**
     * Display supplement form field.
     * 
     * @param stdClass $supplement 
     * @param string $value selected supplement value
     * @param int $capacity selected supplement capacity
     * @param int $itemId reservation item id
     * @param string $params form field attributes aka onclick or onchange
     * @return string
     */
    function displaySupplementInput($supplement, $value, $capacity, $itemId, $params = '')
    {
    	TableSupplement::prepare($supplement);
    	
    	$name = 'supplements[' . $itemId . '][' . $supplement->id . ']';
    	$id = 'supplement' . $supplement->id;
    	
    	if ($supplement->type == SUPPLEMENT_TYPE_LIST) { // show supplement as drop down ?>
    	 
    		<select name="<?php echo $name; ?>[0]" id="<?php echo $id; ?>" <?php echo $params; ?>>
    			
    			<?php if ($supplement->empty == SUPPLEMENT_EMPTY_USE) { // show unselect option ?>
    				<option value="">- <?php echo JText::_('SELECT'); ?> -</option>
    			<?php }
    			
    			foreach ($supplement->options as $option) { ?>
    				<option value="<?php echo $this->escape($option[0]); ?>" <?php if ($option[0] == $value) { ?>selected="selected"<?php } ?>>
    					<?php echo $option[0]; if ($option[1]) { ?> (<?php echo BookingHelper::displayPrice($option[1]); ?>)<?php } ?>
    				</option>
    			<?php } ?>
    			
    		</select>
    	<?php } elseif ($supplement->type == SUPPLEMENT_TYPE_YESNO) { // show supplement as checkbox ?>
    	
    		<!-- zero value in request if checkbox is no checked -->
   			<input type="hidden" name="<?php echo $name; ?>[0]" value="0" />
   			<input type="checkbox" name="<?php echo $name; ?>[0]" value="1" id="<?php echo $id; ?>" <?php if ($value) { ?>checked="checked"<?php } ?> <?php echo $params; ?> />
   			
   			<?php if ($supplement->price) { ?>
   				<label>(<?php echo BookingHelper::displayPrice($supplement->price); ?>)</label>
   			<?php } ?>
   			
    	<?php } else { ?>
    		<input type="hidden" name="<?php echo $name; ?>[0]" value="1" />
    	<?php }
    	if ($supplement->capacity_multiply == 2) { // select supplement capacity 
			$id .= 'cap'; ?>
    		<label for="<?php echo $id; ?>" class="neutral"><?php echo JText::_('CAP'); ?>: </label> 
			<?php BookingHelper::displaySupplementCapacitySelector($supplement, $capacity, $id, $name, $params);     		
    	}
    }
    
    /**
     * Display capacity selector for supplement with maximal capacity defined.
     * If maximal capacity is equal or less then 100 it shows drop down list.
     * Drop down list starts at minimal capacity if defined (or 1).
     * Othewise it shows text field only.     
     * 
     * @param TableSupplement $supplement
     * @param int $capacity
     * @param string $id
     * @param string $name
     * @param string $params
     */
    function displaySupplementCapacitySelector($supplement, $capacity, $id, $name, $params = '', $return = false) {
		if ($return)
			ob_start();
		if ($supplement->capacity_max && $supplement->capacity_max <= 100) { // supplement has capacity limit - show drop down ?>
   			<select class="capacity input-mini" name="<?php echo $name; ?>[1]" id="<?php echo $id; ?>" <?php echo $params; ?>>
   				<?php $min = $supplement->capacity_min ? $supplement->capacity_min : 1;  // supplement has minimal capacity limit
   					for ($i = $min; $i <= $supplement->capacity_max; $i++) { ?>
   						<option value="<?php echo $i; ?>" <?php if ($i == $capacity) { ?>selected="selected"<?php } ?>><?php echo $i; ?></option>
   				<?php } ?>
   			</select>
   		<?php } else { // supplement has no capacity limit - show text field only 
			if ($supplement->capacity_min || $supplement->capacity_max) { // information about minimum/maximum capacity for supplement
				$params .= ' class="hasTip supplementquantity" title="';				
				if ($supplement->capacity_min) {
					$params .= htmlspecialchars(JText::_('MINIMAL')) . ': ' . $supplement->capacity_min;
					if (empty($capacity))
						$capacity = $supplement->capacity_min;	
				}
				if ($supplement->capacity_min && $supplement->capacity_max)
					$params .= ', ';
				if ($supplement->capacity_max)
					$params .= htmlspecialchars(JText::_('MAXIMAL')) . ': ' . $supplement->capacity_max;
				$params .= '"';
			} ?>
   			<input style="width: 50px" name="<?php echo $name; ?>[1]" id="<?php echo $id; ?>" value="<?php echo $capacity; ?>" <?php echo $params; ?> onblur="ACommon.toIntLimit(<?php echo $supplement->capacity_min; ?>, <?php echo $supplement->capacity_max; ?>, this)" />	   			
   		<?php }		
   		if ($return)
   			return ob_get_clean();
	}
    
    /**
     * Get invoice information from Book it! Invoice component
     * 
     * return array	
     * 1: 1 (available), 2 (unavailable), 3 (booking inoice not installed) 
     * 2: if success, link to get invoice, if false, reason of unavailability
     */
    function getInvoiceLink($reservationId)
    {
    	static $invoicesInstalled;
    	static $orderStatuses;
    	static $statusNames;
    	
    	if (!is_bool($invoicesInstalled))
    		$invoicesInstalled = file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_bookinginvoice'.DS.'helpers'.DS.'invoicehelper.php');
    		
    	if (!$invoicesInstalled)
    		return array(3,'Book it Invoice not installed');
    		
    	include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_bookinginvoice'.DS.'helpers'.DS.'invoicehelper.php');
    	
    	$invoiceNo = bookingInvHelper::getInvoiceNo($reservationId);
    	if (!$invoiceNo){
    		
    		if (empty($statusNames)){
	    		$invParams = bookingInvHelper::getParams();
	    		$orderStatuses = (array)$invParams->get('order_status');
	    		$statusNames = bookingInvGetter::getOrderStates();   
    		}

			foreach ($orderStatuses as &$orderStatus)
				$orderStatus = isset($statusNames[$orderStatus]) ? $statusNames[$orderStatus]->name : $orderStatus;
		        
			if (count($orderStatuses)==1)
				$sendStatuses = $orderStatuses[0];
			elseif (count($orderStatuses)>1)  {
				$sendStatuses = ' '.JText::_('OR').' '.array_pop($orderStatuses);
				$sendStatuses = implode(', ',$orderStatuses).$sendStatuses;
			}
			
			if (count($orderStatuses))
				$reason = JText::sprintf('ORDER_NEEDS_TO_GET_IN_STATE_S_FIRST',$sendStatuses);
			else
				$reason = JText::_('CANNOT_GET_INVOICE_NUMBER');

    		return array(2,$reason);
    	}
    	
    	$app = JFactory::getApplication();
    	$session = ($app->isSite() && ($session = JRequest::getVar('session'))) ? '&session='.$session : ''; //add session also for guest reservation
    	return array(1,($app->isSite() ? JURI::root(false) : '').'index.php?option=com_bookinginvoice&controller=invoices&task=pdf&cid='.$reservationId.$session);
    }
    
    /**
     * @param 0;null;<0;>0 $cancelTime
     * @param string $fromTime
     * @param bool $fromTimeIsReservationCreatedTime
     * @return bool $withoutLabel
     */
    function formatExpiration($cancelTime, $fromTime = null, $fromTimeIsReservationCreatedTime = false, $withoutLabel = false)
    {	
    	if($withoutLabel)
    	{
    		if($cancelTime === null)
    			$ret = null;
    		else if($cancelTime == 0)
    			$ret = time();
    		else if($cancelTime > 0)
    		{
    			if($fromTimeIsReservationCreatedTime)
    				$ret = BookingHelper::gmStrtotime($fromTime) + (int)$cancelTime;
    			else
    				$ret = time() + (int)$cancelTime;
    		}
    		else if($cancelTime < 0)
    		{
    			$ret = BookingHelper::gmStrtotime($fromTime) - abs((int)$cancelTime);
    		}
    	} else {
    		if($cancelTime === null)
    			$ret = JText::_("NO_EXPIRATION");
    		else if($cancelTime == 0)
    			$ret = JText::_("INSTANT_EXPIRATION_FOR_ONLINE_PAYMENT");
    		else if($cancelTime > 0)
    		{
    			if(($fromTime !== null) AND $fromTimeIsReservationCreatedTime)
    				$ret = AHtml::date(self::gmStrtotime($fromTime)+ $cancelTime, ADATE_FORMAT_LONG);
    			else
    				$ret = AHtml::date(time()+ $cancelTime, ADATE_FORMAT_LONG);
    		}
    		else if($cancelTime < 0)
    		{
    			$cancelTime = (int)abs($cancelTime);
    			if($fromTime !== null)
    			{
    				//ADATE_FORMAT_NORMAL,ATIME_FORMAT;
    				//TODO delete 0 from offset ?
    				$ret = AHtml::date(self::gmStrtotime($fromTime)-$cancelTime, ADATE_FORMAT_LONG, 0);
    			}
    			else
    			{
    				$ret = self::duration($cancelTime).' '.JText::_('BEFORE_BOOKED_EVENT');
    			}
    		}
    	}
    	return $ret;
    }
    
    function duration($secs)
    {
    	$vals = array('w' => (int) ($secs / 86400 / 7),
    			'd' => $secs / 86400 % 7,
    			'h' => $secs / 3600 % 24
    			//'m' => $secs / 60 % 60
    			//'s' => $secs % 60
    	);
    
    	$ret = array();
    
    	$added = false;
    	foreach ($vals as $k => $v) {
    		if ($v > 0 || $added) {
    			$added = true;
    			$ret[] = $v . $k;
    		}
    	}
    
    	return join(' ', $ret);
    }
    
    function formatFromCancelTime($secs)
    {
    	$secs = abs($secs);
    	
    	$r = $secs;
    	
    	if(($secs % (24*60*60)) == 0)
    	{
    		$r = ($secs / (24 * 60 * 60));
    	}
    	else if(($secs % (60*60)) == 0)
    	{
    		$r = ($secs / (60 * 60));
    	}

    	return $r;
    }
    
    function typeOfCancelTime($cancelTime)
    {
    	if($cancelTime === '')
    		$cancelTime = null;
    	
    	if($cancelTime === null)
    	{
    		$r = CANCEL_NONE;
    	}
    	else if($cancelTime == 0)
    	{
    		$r = CANCEL_IMMEDIATELY;
    	}
    	else if($cancelTime < 0)
    	{
    		$r = CANCEL_BEFORE;
    	}
    	else if($cancelTime > 0)
    	{
    		$r = CANCEL_AFTER;
    	}
    	else
    		$r = false;
    	 
    	return $r;
    }
    
    function formatOfCancelTime($cancelTime)
    {
    	$cancelTime = abs($cancelTime);

    	if($cancelTime < (24*60*60))
    	{
    		$r = EXPIRE_FORMAT_HOUR;
    	}
    	else if($cancelTime >= (24*60*60))
    	{
    		$r = EXPIRE_FORMAT_DAY;
    	}
    	else
    		$r = false;
    	
    	return $r;    	
    }
    
    function showReservationStateLabel($reservationState){
    	switch ($reservationState) {
    		case RESERVATION_PRERESERVED:
    			return JText::_('PRE_RESERVED');
    			break;
    		case RESERVATION_ACTIVE:
    			return JText::_('RESERVED');
    			break;
    		case RESERVATION_STORNED:
    			return JText::_('CANCELLED');
    			break;
    		case RESERVATION_TRASHED:
    			return JText::_('TRASHED');
    			break;
    		case RESERVATION_CONFLICTED:
    			return JText::_('CONFLICTED');
    			break;
    	}
    }
    
    /**
     * Show reservation payment status label
     * @param int $status
     * @return string
     */
    public static function showReservationPaymentStateLabel($status) {
		$statuses = BookingHelper::getPaymentStatuses();
		if (isset($statuses[$status]))
			return JText::_($statuses[$status]['label']);
		return '';
    }
    
    /**
     * Get payment status list
     * @return array
     */
    public static function getPaymentStatuses() {
        $deposit = AFactory::getConfig()->usingPrices == PRICES_WITH_DEPOSIT;
        
        $statuses[RESERVATION_PENDING] = array(
            'id' => RESERVATION_PENDING,
            'label' => JText::_('UNPAID'),
            'icon' => 'aIconExpired',
            'task' => ($deposit ? 'receiveDeposit' : 'receive'),
            'title' => ($deposit ? JText::_('CLICK_TO_MARK_AS_DEPOSIT_PAID') : JText::_('CLICK_TO_MARK_AS_PAID_IN_FULL')));
        
        $statuses[RESERVATION_ONLINE_PENDING] = array(
			'id' => RESERVATION_ONLINE_PENDING,
			'label' => JText::_('ONLINE_PENDING'),
			'icon' => 'aIconOnline',
			'task' => 'receive',
			'title' => JText::_('CLICK_TO_MARK_AS_PAID_IN_FULL'));
        
        if ($deposit) {
            $statuses[RESERVATION_RECEIVE_DEPOSIT] = array(
                'id' => RESERVATION_RECEIVE_DEPOSIT,
                'label' => JText::_('DEPOSIT_PAID'),
                'icon' => 'aIconPending',
                'task' => 'receive',
                'title' => JText::_('CLICK_TO_MARK_AS_PAID_IN_FULL'));
        }
        
        $statuses[RESERVATION_RECEIVE] = array(
			'id' => RESERVATION_RECEIVE,
			'label' => JText::_('PAID_IN_FULL'),
			'icon' => 'aIconPublished',
			'task' => 'unreceive',
			'title' => JText::_('CLICK_TO_MARK_AS_UNPAID'));		
        
        return $statuses;
	}
    
    static function gmStrtotime($date)
    {
    	//TODO replace strtotime with joomla getDate()
    	//JFactory::getDate($box->fromDate)->toUnix();
    	$tz = date_default_timezone_get();
    	date_default_timezone_set('UTC');
    	$ret = strtotime($date);
    	date_default_timezone_set($tz);
    	return $ret;
    }
    
    static function intervalToSeconds ($interval) {

		$interval 	= explode(',', $interval);
		
		$intervals 	= array();
		$intervals['minute'] = 0;
		$intervals['hour'] = 0;
		$intervals['day'] = 0;
		
		//get the values of intervals
		if (is_array($interval) && $interval) {
			foreach ($interval as $int) {
				$val = array();
				$val[0] = (int)$int;
				$val[1] = substr (trim($int), strlen($val[0]));
				if (!is_array($val) || count($val) != 2) continue;
		
				switch (strtolower(trim($val[1]))) {
					case 'i':
						$intervals['minute'] = (int)$val[0];
						break;
					case 'h':
						$intervals['hour'] = (int)$val[0];
						break;
					case 'd':
						$intervals['day'] = (int)$val[0];
						break;
		
				}
			}
}

		$format = ($intervals['minute'] * 60) + ($intervals['hour'] * 60*60) + ($intervals['day'] * 60*60*24);
		
		return $format;
	}
}


class BookingCalendarSetting
{
    var $currentDay = '';
    var $currentWeek = '';
    var $currentMonth = '';
    var $currentYear = '';
    var $currentDate = '';
    var $currentDayUTS = '';
    var $onCurrentWeek = '';
    var $lastAllowYear = '';
    var $lastAllowWeek = '';
    var $previousWeek = '';
    var $previousYear = '';
    var $nextWeek = '';
    var $nextYear = '';
    var $day = '';
    var $month = '';
    var $week = '';
    var $year = '';
    var $dateExists = '';
    var $lastAllowMonth = '';
    var $previousMonth = '';
    var $nextMonth = '';
    var $firstDay = '';
    var $firstDayUTS = '';
    var $lastDay = '';
    var $lastDayUTS = '';
    var $firstWeekDayNumber = '';
    var $firstDayOffset = '';
    var $firstDayNumber = '';
    var $wPreviousMonth = '';
    var $lastDayPrevMonth = '';
    var $lastWeekDayNumber = '';
    var $lastDayNumber = '';
    var $onCurrentMonth = '';
    var $lastAllowPage = '';
}


//counting days for calendar
class ServiceDayCounter
{
	private $object = array();

	public function getBox($id)
	{
		$id = explode("-",$id);
		$id = $id[2];
		if(array_key_exists($id,$this->object))
			return $this->object[$id]['data'];
		else
			return false;
	}
	
	public function isCountZero($id)
	{
		$id = explode("-",$id);
		$id = $id[2];
		if(array_key_exists($id,$this->object))
		{
			//echo $id.': '.$this->object[$id]['count'].'<br>';
			if($this->object[$id]['count'] < 1)
			{
				$this->object[$id]['count'] = 0;
				return true;
			}
			else
			{
				$this->object[$id]['count'] = $this->object[$id]['count'] - 1;
				return false;
			}
		}
		else
			return false;
	}
	
	public function resetCount($id)
	{
		$id = explode("-",$id);
		$id = $id[2];
		if(array_key_exists($id,$this->object))
		{
			$this->object[$id]['count'] = $this->object[$id]['data']->fix;
			return true;

		}
		else
			return false;
	}
	
	private function setBox($box)
	{
		$id = explode("-",$box->id);
		$id = $id[2];
		if(!array_key_exists($id,$this->object))
		{
			$this->object[$id]['data'] = $box;
			$this->object[$id]['count'] = $box->fix;
		}
	}
	
	public function add($box)
	{
		$this->setBox($box);
	}
}

class CommunityBuilder
{
	static function userProfileUrl( $userId = null, $htmlSpecials = true, $tab = null, $format = 'html' ) {
	
		return 'index.php?option=com_comprofiler' . ( $userId ? '&task=userprofile&user=' . (int) $userId : '' ) . ( $tab ? '&tab=' . urlencode( $tab ) : '' ) ;
	}
	
	static function isInstalled(){
		$dir = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_comprofiler'.DS;
		if(is_dir($dir))
			return true;
		else
			return false;
	}
}
?>
