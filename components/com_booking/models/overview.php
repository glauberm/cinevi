<?php

/**
 * @package        ARTIO Booking
 * @subpackage		models
 * @copyright	  	Copyright (C) 2014 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */
defined('_JEXEC') or die;

class BookingModelOverview extends JModelLegacy {

    private $hourlyParents;
    private $current;
    private $children;
    private $navigator;

    /**
     * Get item parents which children have hourly reservation type.
     * 
     * @return array
     */
    public function getHourlyParents() {
        if (empty($this->hourlyParents)) {
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $date = JFactory::getDate();
            $user = JFactory::getUser();

            $now = $db->q($date->toSql());
            $null = $db->q($db->getNullDate());
            $access = $user->getAuthorisedViewLevels();

            $query->select('s.id, s.title, s.alias')
                    ->from('#__booking_subject AS s')
                    ->leftJoin('#__booking_subject AS c ON s.id = c.parent')
                    ->where('c.id IS NOT NULL')
                    ->where('s.state = ' . SUBJECT_STATE_PUBLISHED)
                    ->where('s.access IN (' . implode(', ', $access) . ')')
                    ->where('(s.publish_up <= ' . $now . ' OR s.publish_up = ' . $null . ')')
                    ->where('(s.publish_down >= ' . $now . ' OR s.publish_down = ' . $null . ')')
                    ->where('c.state = ' . SUBJECT_STATE_PUBLISHED)
                    ->where('c.access IN (' . implode(', ', $access) . ')')
                    ->where('(c.publish_up <= ' . $now . ' OR c.publish_up = ' . $null . ')')
                    ->where('(c.publish_down >= ' . $now . ' OR c.publish_down = ' . $null . ')')
                    ->order('s.ordering')
                    ->group('s.id');

            $this->hourlyParents = $db->setQuery($query)->loadObjectList();
        }
        return $this->hourlyParents;
    }

    /**
     * Get current parent for hourly overview.
     * 
     * @return stdClass
     */
    public function getHourlyCurrent() {
        $app = JFactory::getApplication();

        $this->getHourlyParents();

        $default = reset(JArrayHelper::getColumn($this->hourlyParents, 'id'));

        $id = $app->getUserStateFromRequest('com_booking.overview.id', 'id', $default, 'int');

        foreach ($this->hourlyParents as $i => $candidate) {
            if ($candidate->id == $id) {
                unset($this->hourlyParents[$i]);
                $this->current = $candidate;
            }
        }

        if (empty($this->current)) { // system has no parent items
            $this->current = new stdClass();
            $this->current->id = $this->current->title = $this->current->alias = null; // root
        }

        return $this->current;
    }

    /**
     * Get data for day navigator.
     * 
     * @return stdClass
     */
    public function getDayNavigator() {
        if (empty($this->navigator)) {
            $app = JFactory::getApplication();
            $params = $this->getParams();
            $config = AFactory::getConfig();

            $this->navigator = new stdClass();

            $date = JFactory::getDate($app->getUserStateFromRequest('com_booking.overview.date', 'date', 'now', 'string'));

            $this->navigator->currentDate = $date->format('Y-m-d');
            $this->navigator->currentWeek = $date->format('W');
            $this->navigator->currentDay = $date->format(ADATE_FORMAT_NORMAL);

            $this->navigator->prevDay = $this->modify($date->toUnix(), '- 1 day', 'Y-m-d');
            $this->navigator->nextDay = $this->modify($date->toUnix(), '+ 1 day', 'Y-m-d');

            $this->navigator->lastWeekDay = $this->modify($date->toUnix(), ($config->firstDaySunday ? 'this week saturday' : 'this week sunday'), 'Y-m-d');
            $this->navigator->firstWeekDay = $this->modify($date->toUnix(), ($config->firstDaySunday ? 'this week sunday' : 'this week monday'), 'Y-m-d');

            $this->navigator->prevWeek = $this->modify($date->toUnix(), '- 1 week', 'Y-m-d');
            $this->navigator->nextWeek = $this->modify($date->toUnix(), '+ 1 week', 'Y-m-d');

            $this->navigator->prevMonth = $this->modify($date->toUnix(), '- 1 month', 'Y-m-01');
            $this->navigator->nextMonth = $this->modify($date->toUnix(), '+ 1 month', 'Y-m-01');
        }
        return $this->navigator;
    }

    private function modify($date, $modify, $format) {
        $date = JFactory::getDate($date);
        $date->modify($modify);
        return $date->format($format);
    }

    /**
     * Get current parent children.
     * 
     * @return array
     */
    public function getChildren() {
        if (empty($this->children)) {
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $date = JFactory::getDate();
            $user = JFactory::getUser();

            $now = $db->q($date->toSql());
            $null = $db->q($db->getNullDate());
            $access = $user->getAuthorisedViewLevels();

            $query->select('s.id, s.title, s.alias')
                    ->from('#__booking_subject AS s')
                    ->where('s.state = ' . SUBJECT_STATE_PUBLISHED)
                    ->where('s.access IN (' . implode(', ', $access) . ')')
                    ->where('(s.publish_up <= ' . $now . ' OR s.publish_up = ' . $null . ')')
                    ->where('(s.publish_down >= ' . $now . ' OR s.publish_down = ' . $null . ')')
                    ->where('s.parent = ' . (int) $this->current->id)
                    ->group('s.id');

            $this->children = $db->setQuery($query)->loadObjectList();
        }
        return $this->children;
    }

    /**
     * Get schedule of hourly overview.
     * 
     * @return array
     */
    public function getHourlySchedule() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $date = JFactory::getDate();
        $user = JFactory::getUser();
        $params = $this->getParams();

        if ($params->get('schedule_type', 2) == 1) {
            $now = $db->q($date->toSql());
            $null = $db->q($db->getNullDate());
            $access = $user->getAuthorisedViewLevels();

            $query->select('MIN(p.time_up) AS up, MAX(p.time_down) AS down')
                    ->from('#__booking_subject AS s')
                    ->leftJoin('#__booking_price AS p ON s.id = p.subject')
                    ->leftJoin('#__booking_reservation_type AS t ON t.id = p.rezervation_type')
                    ->where('s.state = ' . SUBJECT_STATE_PUBLISHED)
                    ->where('s.access IN (' . implode(', ', $access) . ')')
                    ->where('(s.publish_up <= ' . $now . ' OR s.publish_up = ' . $null . ')')
                    ->where('(s.publish_down >= ' . $now . ' OR s.publish_down = ' . $null . ')')
                    ->where('s.parent = ' . (int) $this->current->id)
                    ->where('t.type = ' . RESERVATION_TYPE_HOURLY);

            $limit = $db->setQuery($query)->loadObject();
            if (!$limit) {
                $limit = JArrayHelper::toObject(array('up' => '00:00:00', 'down' => '24:00:00'));
            }
            if ($limit->down == '00:00:00') {
                $limit->down = '24:00:00';
            }
            $limit->up = floor(str_replace(':', '.', $limit->up));
            $limit->down = ceil(str_replace(':', '.', $limit->down));
        } else {
            $begin = $params->get('manually_schedule_begin', 0);
            $end = $params->get('manually_schedule_end', 23);
            $limit = array('up' => $begin, 'down' => $end);
            $limit = JArrayHelper::toObject($limit);
        }

        $schedule = array();

        for ($hour = $limit->up; $hour <= $limit->down; $hour++) {
            $date->setTime($hour, 0, 0);
            $schedule[] = $date->format(ATIME_FORMAT);
        }

        return $schedule;
    }

    /**
     * Get days of the current week.
     * 
     * @return array
     */
    public function getWeekSchedule() {
        $navigator = $this->getDayNavigator();
        $days = array();

        $begin = JFactory::getDate($navigator->firstWeekDay);
        $end = JFactory::getDate($navigator->lastWeekDay);

        for ($day = $begin; $day->toUnix() <= $end->toUnix(); null) {
            $days[] = $day->format('Y-m-d');
            $day->modify('+ 1 day');
        }

        return $days;
    }

    /**
     * Get reserved reservations for current day and current family.
     * 
     * @return array
     */
    public function getHourlyReservations() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $navigator = $this->getDayNavigator();
        $params = $this->getParams();
        $singleWeek = $this->getState('week');
        $config = AFactory::getConfig();

        if (!$singleWeek) {
            $date = JFactory::getDate($navigator->currentDate);
            $up = $db->q($date->format('Y-m-d 00:00:00'));
            $down = $db->q($date->format('Y-m-d 23:59:59'));
        } else {
            $up = $db->q(JFactory::getDate($navigator->firstWeekDay)->format('Y-m-d 00:00:00'));
            $down = $db->q(JFactory::getDate($navigator->lastWeekDay)->format('Y-m-d 23:59:59'));
        }

        $ids = JArrayHelper::getColumn($this->children, 'id');

        if (empty($ids)) {
            return array();
        }

        $query->select('i.subject AS id, COALESCE(p.from, i.from) AS up, COALESCE(p.to, i.to) AS down, r.title_before, r.firstname, r.middlename, r.surname, r.title_after, r.id AS rid, i.message, r.note, i.subject')
                ->from('#__booking_reservation_items AS i')
                ->leftJoin('#__booking_reservation AS r ON i.reservation_id = r.id')
                ->leftJoin('#__booking_reservation_period AS p ON p.reservation_item_id = i.id')
                ->where('i.subject IN (' . implode(', ', $ids) . ')');
        if ($config->confirmReservation < 2) {
            $query->where('r.state IN (' . RESERVATION_ACTIVE . ',' . RESERVATION_PRERESERVED . ')');
        } else {
            $query->where('r.state = ' . RESERVATION_ACTIVE);
        }
        $query->order('up')
                ->having('up <=' . $down)
                ->having('down >= ' . $up);

        $reservations = $db->setQuery($query)->loadObjectList();
        $grouped = array();

        foreach ($reservations as $reservation) {
            $reservation->upDate = JString::substr($reservation->up, 0, 10);
            $reservation->downDate = JString::substr($reservation->down, 0, 10);
            if (!isset($grouped[$reservation->id])) {
                $grouped[$reservation->id] = array();
            }
            $grouped[$reservation->id][] = $reservation;
        }

        return $grouped;
    }

    /**
     * Get current menu item configuration.
     * 
     * @return JRegistry
     */
    public function getParams() {
        $app = JFactory::getApplication();
        $active = $app->getMenu()->getActive();
        if ($active) {
            return $active->params;
        }
        return new JRegistry();
    }

}
