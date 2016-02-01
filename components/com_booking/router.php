<?php

function BookingBuildRoute(&$query) {
    static $replaces;
    if (is_null($replaces))
        $replaces = BookingReplaces();

    $segments = array();

    $view = JArrayHelper::getValue($query, 'view');

    if (isset($query['view'])) {
        if (isset($replaces[$query['view']])) {
            $segments[] = $replaces[$query['view']];
        }
        unset($query['view']);
    }
    if (isset($query['id']) && ($view == 'subject' || $view == 'overview')) {
        $segments[] = $query['id'];
        unset($query['id']);
    }
    if (isset($query['layout'])) {
        $segments[] = $replaces[$query['layout']];
        unset($query['layout']);
    }

    return $segments;
}

function BookingParseRoute($segments) {
    $vars = array();
    $vars['view'] = BookingSearchReplace($segments[0]);

    if (isset($segments[1])) {
        switch ($vars['view']) {
            case 'reservations':
                $vars['layout'] = BookingSearchReplace($segments[1]);
                break;
            case 'reservation':
                $vars['layout'] = BookingSearchReplace($segments[1]);
                break;
            case 'quickbook':
                $vars['layout'] = BookingSearchReplace($segments[1]);
                break;
            case 'overview':
                $vars['layout'] = BookingSearchReplace($segments[1]);
                break;            
            case 'subject':
                BookingSearchReplace($segments[1]) ? $vars['layout'] = BookingSearchReplace($segments[1]) : $vars['id'] = (int) $segments[1];
                break;
            default:
                $vars['id'] = (int) $segments[1];
                break;
        }
        return $vars;
    } elseif (count($segments) == 0) { // menu link
        $active = JFactory::getApplication()->getMenu()->getActive();
        if (is_object($active)) {
            $juri = JURI::getInstance($active->link);
            return $juri->getQuery(true);
        }
        return $vars;
    }
    return $vars;
}

function BookingReplaces() {
    JFactory::getLanguage()->load('com_booking', JPATH_SITE);

    $replaces['subject'] = JText::_('SEO_SUBJECT');
    $replaces['subjects'] = JText::_('SEO_SUBJECTS');
    $replaces['selectsubjects'] = JText::_('SEO_SELECTSUBJECTS');
    $replaces['reservations'] = JText::_('SEO_RESERVATIONS');
    $replaces['reservation'] = JText::_('SEO_RESERVATION');
    $replaces['customer'] = JText::_('SEO_CUSTOMER');
    $replaces['admin'] = JText::_('SEO_ADMIN');
    $replaces['form'] = JText::_('SEO_FORM');
    $replaces['quickbook'] = JText::_('SEO_QUICKBOOK');
    $replaces['date'] = JText::_('SEO_DATE');
    $replaces['day'] = JText::_('SEO_DAY');
    $replaces['manager'] = JText::_('SEO_MANAGER');
    $replaces['closingdays'] = 'closingdays';
    $replaces['closingday'] = 'closingday';
    $replaces['popup'] = 'popup';
    $replaces['overview'] = 'reservationoverview';
    $replaces['week'] = 'week';
    $replaces['default'] = '';

    return $replaces;
}

function BookingSearchReplace($key) {
    static $replaces;

    if (is_null($replaces))
        $replaces = BookingReplaces();

    if (($value = array_search($key, $replaces)))
        return $value;

    return null;
}

?>