<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage 	models
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class BookingModelGoogle extends JModelAdmin {

    const TOKEN = 'com_booking.google.token';
    const COMMAND = 'com_booking.google.command';
    const AUTHCLASS = 'Google_OAuth2';
    const AUTHURL = 'https://www.googleapis.com/auth/calendar';

    /**
     * @var Google_Client
     */
    private $_client;

    /**
     * @var Google_CalendarService
     */
    private $_service;

    /**
     * Import Google library, setup client and service.
     * @param array $config
     */
    public function __construct($config = array()) {
        parent::__construct($config);

        JLoader::import('components.com_booking.assets.libraries.googlecalendar.Google_Client', JPATH_ROOT);
        JLoader::import('components.com_booking.assets.libraries.googlecalendar.contrib.Google_CalendarService', JPATH_ROOT);
        JLoader::import('components.com_booking.assets.libraries.googlecalendar.auth.Google_OAuth2', JPATH_ROOT);

        $config = AFactory::getConfig();

        $this->_client = new Google_Client();
        $this->_service = new Google_CalendarService($this->_client);

        $this->_client->setApplicationName('ARTIO Booking');
        $this->_client->setClientId($config->googleClientID);
        $this->_client->setClientSecret($config->googleClientSecret);
        $this->_client->setRedirectUri(JURI::base() . 'index.php?option=com_booking&task=google.authenticate'); // url where google redirects after login
        $this->_client->setAuthClass(BookingModelGoogle::AUTHCLASS); // authentication type
        $this->_client->setScopes(array(BookingModelGoogle::AUTHURL)); // register used service
    }

    /**
     * Connect Google server. Use valid token or redirect to Google server for user login.
     * @return boolean
     */
    private function _connect() {
        if (JFactory::getApplication()->getUserState(BookingModelGoogle::TOKEN))
            $this->_client->setAccessToken(JFactory::getApplication()->getUserState(BookingModelGoogle::TOKEN));

        if ($this->_client->getAccessToken() && !$this->_client->isAccessTokenExpired()) // connect with valid token
            return true;
        else {
            JFactory::getApplication()->setUserState(BookingModelGoogle::COMMAND, JRequest::getCmd('controller') . '.' . JRequest::getCmd('task'));
            JFactory::getApplication()->redirect($this->_client->createAuthUrl()); // go to login page
        }
        return false;
    }

    /**
     * Synchronize reservation as Google Calendar Event.
     * Insert new event or update event. 
     */
    public function synchronizeReservations() {
        if ($this->_connect()) {

            $config = AFactory::getConfig();

            $query = $this->getDbo()->getQuery(true);

            // load non synchronized reservations or updated reservations
            $query->select('COALESCE(p.id,i.id) AS id, i.rtype, i.subject_title, COALESCE(p.from,i.from) AS `from`, COALESCE(p.to,i.to) AS `to`, COALESCE(p.google_calendar_id,i.google_calendar_id) AS google_calendar_id,
					        r.title_before, r.firstname, r.middlename, r.surname, r.title_after, r.company,
							r.street, r.city, r.country, r.zip, r.email, r.telephone,
                            r.state, r.modified AS rmodified,
                            g.id AS gid, g.modified AS gmodified,
					        s.google_calendar')
                    ->from('#__booking_reservation_items AS i')
                    ->join('', '#__booking_reservation AS r ON r.id = i.reservation_id')
                    ->leftJoin('#__booking_subject AS s ON i.subject = s.id')
                    ->leftJoin('#__booking_google_calendar AS g ON g.id = s.google_calendar')
                    ->leftJoin('#__booking_reservation_period AS p ON p.reservation_item_id = i.id')
                    ->having("(google_calendar_id = '' AND state = " . RESERVATION_ACTIVE . ") 
			      		    OR 
			      		   (google_calendar_id <> '' AND gid IS NOT NULL AND rmodified > gmodified)");

            $itemList = $this->getDbo()->setQuery($query)->loadObjectList();

            $insert = 0; // number of inserted events
            $update = 0; // number of updated events

            foreach ($itemList as $item) {
                try {
                    $event = new Google_Event();

                    // summary is showed in calendar box
                    $event->setSummary($config->googleEventSummary ? BookingHelper::formatName($item, false, true) : $item->subject_title);
                    // event detail text
                    $event->setDescription(
                            $item->subject_title . "\n" .
                            BookingHelper::formatName($item, false, true) . "\n" .
                            BookingHelper::formatAddress($item) . "\n" .
                            $item->email . "\n" .
                            $item->telephone
                    );

                    // reservation check in as event begin
                    $from = new Google_EventDateTime();
                    $from->setDateTime(JFactory::getDate($item->from, JFactory::getApplication()->getCfg('offset'))->toISO8601(true));
                    $from->setTimeZone(JFactory::getApplication()->getCfg('offset'));
                    $event->setStart($from);

                    // reservation check out as event end
                    $to = new Google_EventDateTime();
                    $to->setDateTime(JFactory::getDate($item->to, JFactory::getApplication()->getCfg('offset'))->toISO8601(true));
                    $to->setTimeZone(JFactory::getApplication()->getCfg('offset'));
                    $event->setEnd($to);

                    if (!empty($item->google_calendar_id)) { // update already synchronized reservation
                        $new = $this->_service->events->update($item->google_calendar ? $item->google_calendar : $config->googleDefaultcalendar, $item->google_calendar_id, $event);
                        $update++;
                    } else { // create new event for reservation
                        $new = $this->_service->events->insert($item->google_calendar ? $item->google_calendar : $config->googleDefaultcalendar, $event);
                        $insert++;
                    }

                    if (!empty($new['id']) && empty($item->google_calendar_id)) { // new event
                        $query = $this->getDbo()->getQuery(true);
                        // save event id in reservation item
                        $query->update($item->rtype == RESERVATION_TYPE_PERIOD ? '#__booking_reservation_period' : '#__booking_reservation_items')
                                ->set('google_calendar_id = ' . $query->quote($new['id']))
                                ->where('id = ' . $query->quote($item->id));

                        $this->getDbo()->setQuery($query)->query();
                    }
                } catch (Exception $e) {
                    JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                }
            }

            JFactory::getApplication()->enqueueMessage(JText::sprintf('INSERTED_GOOGLE_EVENTS', $insert));
            JFactory::getApplication()->enqueueMessage(JText::sprintf('UPDATED_GOOGLE_EVENTS', $update));
        }
    }

    /**
     * Delete event of non active reservation.
     */
    public function unsynchronizeReservations() {
        if ($this->_connect()) {
            $config = AFactory::getConfig();
            $query = $this->getDbo()->getQuery(true);

            $query->select('COALESCE(p.id,i.id) AS id, COALESCE(p.google_calendar_id,i.google_calendar_id) AS google_calendar_id, s.google_calendar, r.state, i.rtype')
                    ->from('#__booking_reservation_items AS i')
                    ->join('', '#__booking_reservation AS r ON r.id = i.reservation_id')
                    ->leftJoin('#__booking_subject AS s ON i.subject = s.id')
                    ->leftJoin('#__booking_reservation_period AS p ON p.reservation_item_id = i.id')
                    ->having("google_calendar_id <> ''") // synchronized reservation
                    ->having('state <> ' . RESERVATION_ACTIVE); // none active reservation

            $itemList = $this->getDbo()->setQuery($query)->loadObjectList();

            $sum = 0;

            foreach ($itemList as $item) {
                try {
                    $this->_service->events->delete($item->google_calendar ? $item->google_calendar : $config->googleDefaultcalendar, $item->google_calendar_id);

                    $query = $this->getDbo()->getQuery(true);
                    // remove event id from reservation item
                    $query->update($item->rtype == RESERVATION_TYPE_PERIOD ? '#__booking_reservation_period' : '#__booking_reservation_items')
                            ->set("google_calendar_id = ''")
                            ->where('id = ' . $query->quote($item->id));

                    $this->getDbo()->setQuery($query)->query();

                    $sum++;
                } catch (Exception $e) {
                    JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                }
            }

            JFactory::getApplication()->enqueueMessage(JText::sprintf('REMOVED_GOOGLE_EVENTS', $sum));
        }
    }

    /**
     * Authenticate client with code given by Google server and set access token.
     */
    public function authenticate() {
        if ($_GET['code']) {
            $this->_client->authenticate($_GET['code']);
            JFactory::getApplication()->setUserState(BookingModelGoogle::TOKEN, $this->_client->getAccessToken()); // save token into session
        }
    }

    /**
     * Load list of Google Calendars
     */
    public function loadCalendarList() {
        if ($this->_connect()) {
            $list = $this->_service->calendarList->listCalendarList();
            if (!empty($list['items'])) {
                $sum = 0;
                JTable::getInstance('GoogleCalendar', 'Table')->truncate();
                foreach ($list['items'] as $item)
                    if ($item['accessRole'] == 'owner') { // only own
                        JTable::getInstance('GoogleCalendar', 'Table')->bind($item)->store();
                        $sum ++;
                    }
                JFactory::getApplication()->enqueueMessage(JText::sprintf('LOADED_CALENDARS', $sum));
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see JModelForm::getForm()
     */
    public function getForm($data = array(), $loadData = true) {
        return null;
    }

}
