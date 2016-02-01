<?php

/**
 * Extended search module.
 *
 * @package		ARTIO Booking
 * @subpackage  modules
 * @copyright	Copyright (C) 2012 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @link        http://www.artio.net Official website
 */

/* @var $params JRegistry */

defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
/* @var $app JApplication */
$doc = JFactory::getDocument();
/* @var $doc JDocument */

$version = '?version=' . str_replace('.', '', $manifest['version']);

$doc->addStyleSheet(JURI::root() . 'modules/mod_booking_search/assets/css/general.css' . $version);
$doc->addScript(JURI::root() . 'modules/mod_booking_search/assets/js/scripts.js' . $version);
$doc->addScriptDeclaration('var LGInvalidDateRange = "' . JText::_('INVALID_DATE_RANGE', true) . '";');

$url = 'index.php?option=com_booking&view=subjects';
$itemid = $params->get('itemid');
if ($itemid)
    $url .= '&Itemid=' . $itemid;

echo '<form name="bookingSearch" id="bookingSearch" method="post" action="' . JRoute::_($url) . '">';

if ($params->get('date_range', 1)) {
    $timeRange = $params->get('time_range', 0);
    $dateFormat = $timeRange ? ADATE_FORMAT_LONG : ADATE_FORMAT_NORMAL;
    $calsFormat = $timeRange ? ADATE_FORMAT_LONG_CAL : ADATE_FORMAT_NORMAL_CAL;
    echo '<div class="dateRange">';

    echo '<div class="row">';
    echo '<label for="bookingSearchDateFrom">' . JText::_('DATE_FROM') . '</label>';
    echo AHtml::getCalendar($app->getUserStateFromRequest('booking_search_date_from', 'date_from'), 'date_from', 'bookingSearchDateFrom', $dateFormat, $calsFormat, '', $timeRange, false,"%Y-%m-%d 00:00:00");
    echo '</div>';

    echo '<div class="row">';
    echo '<label for="bookingSearchDateTo">' . JText::_('DATE_TO') . '</label>';
    echo AHtml::getCalendar($app->getUserStateFromRequest('booking_search_date_to', 'date_to'), 'date_to', 'bookingSearchDateTo', $dateFormat, $calsFormat, '', $timeRange, false,"%Y-%m-%d 23:59:59");
    echo '</div>';

    echo '</div>';
    echo '<input type="hidden" name="date_type" value="' . ($timeRange ? 'datetime' : 'date') . '" />';
} else {
    echo '<input type="hidden" name="date_from" id="bookingSearchDateFrom" value="" />';
    echo '<input type="hidden" name="date_to" id="bookingSearchDateTo" value="" />';
}
if ($params->get('price_range', 0)) {
    echo '<div class="priceRange">';
    echo '<label for="bookingSearchPriceFrom">' . JText::_('PRICE_RANGE') . '</label>';
    echo '<input type="text" name="price_from" id="bookingSearchPriceFrom" value="' . htmlspecialchars($app->getUserStateFromRequest('booking_search_price_from', 'price_from')) . '" />';
    echo '<span class="dash">-</span>';
    echo '<input type="text" name="price_to" id="bookingSearchPriceTo" value="' . htmlspecialchars($app->getUserStateFromRequest('booking_search_price_to', 'price_to')) . '" />';
    echo '</div>';
} else {
    echo '<input type="hidden" name="price_from" id="bookingSearchPriceFrom" value="" />';
    echo '<input type="hidden" name="price_to" id="bookingSearchPriceTo" value="" />';
}
if ($params->get('template_area', 1)) {
    echo '<div class="templateArea">' . $stemplates . '</div>';
} else
    echo '<input type="hidden" name="template_area" id="template_area" value="" />';
if ($params->get('required_capacity', 0)) {
    echo '<div class="requiredCapacity">';
    echo '<label for="bookingSearchCapacity">' . JText::_('REQUIRED_CAPACITY') . '</label>';
    echo '<input type="text" name="required_capacity" id="bookingSearchCapacity" value="' . htmlspecialchars($app->getUserStateFromRequest('booking_search_required_capacity', 'required_capacity')) . '" />';
    echo '</div>';
} else
    echo '<input type="hidden" name="required_capacity" id="bookingSearchCapacity" value="" />';
if ($params->get('featured') === '2') {
	echo '<div class="property boolean">';
	echo '<label for="bookingFeatured">' . JText::_('Featured Only') . '</label>';
	echo '<input type="hidden" name="featured" value="0" />';
	echo '<input type="checkbox" name="featured" id="bookingFeatured" value="1"' . ($app->getUserStateFromRequest('booking_search_featured', 'featured') ? 'checked="checked"' : '') . ' />';
	echo '<div class="clr"></div>';
	echo '</div>';
} elseif ($params->get('featured') === '1') {
	echo '<input type="hidden" name="featured" value="1" />';
} else {
	echo '<input type="hidden" name="featured" value="0" />';
}

echo '<input type="hidden" name="category" value="' . $params->get('category') . '" />';

if ($params->get('locations', 0)) {
    echo AHtml::locations(true);
}

if ($params->get('properties', 0)) {
    foreach ($searchables as $searchable) {
        echo '<div class="property ' . ($searchable[PARAM_TYPE] == 'checkbox' || $searchable[PARAM_TYPE] == 'radio' ? ' boolean' : '') . '">';
        echo '<label for="' . $searchable[PARAM_REQUESTNAME] . '">' . ATemplate::translateParam($searchable['node']['label']) . '</label>';
        if ($searchable[PARAM_TYPE] == 'list') {
            $options = array();
            $options[] = JHTML::_('select.option', '', JText::_('SELECT'));
            foreach ($searchable[PARAM_OPTIONS] as $option)
                $options[] = JHTML::_('select.option', $option[0], $option[1]);
            echo JHTML::_('select.genericlist', $options, $searchable[PARAM_REQUESTNAME], '', 'value', 'text', $searchable[PARAM_REQUESTVALUE]);
        } elseif ($searchable[PARAM_TYPE] == 'text') {
            echo '<input type="text" name="' . $searchable[PARAM_REQUESTNAME] . '" id="' . $searchable[PARAM_REQUESTNAME] . ' value="' . htmlspecialchars($searchable[PARAM_REQUESTVALUE], ENT_QUOTES) . '" />';
        } elseif ($searchable[PARAM_TYPE] == 'checkbox') {
            echo '<input class="checkbox" type="checkbox" name="' . $searchable[PARAM_REQUESTNAME] . '" id="' . $searchable[PARAM_REQUESTNAME] . '" value="1" ' . ($searchable[PARAM_REQUESTVALUE] == 1 ? 'checked="checked"' : '') . '/>';
        } elseif ($searchable[PARAM_TYPE] == 'radio') {
            foreach ($searchable[PARAM_OPTIONS] as $i => $option) {
                echo '<input type="radio" name="' . $searchable[PARAM_REQUESTNAME] . '" id="' . ($id = 'r' . $i . $searchable[PARAM_REQUESTNAME]) . '" value="' . htmlspecialchars($option[0]) . '" ' . ($searchable[PARAM_REQUESTVALUE] == $option[0] ? 'checked="checked"' : '') . ' />';
                echo '<label for="' . $id . '" class="option">' . $option[1] . '</label>';
            }
        } elseif ($searchable[PARAM_TYPE] == 'textarea') {
            echo '<textarea name="' . $searchable[PARAM_REQUESTNAME] . '" id="' . $searchable[PARAM_REQUESTNAME] . '" rows="5" cols="40">' . $searchable[PARAM_REQUESTVALUE] . '</textarea>';
        }
        echo '<div class="clr"></div>';
        echo '</div>';
    }
}
echo '<div class="toolbar">';

if ($params->get('submit_label'))
    echo '<label class="buttonLabel" id="bookingSearchSubmitLabel">' . $params->get('submit_label') . '</label>';
if ($params->get('login_label'))
    echo '<label class="buttonLabel" id="bookingSearchLoginLabel">' . $params->get('login_label') . '</label>';

if ($params->get('submit_label') || $params->get('login_label'))
    echo '<div class="wrap"></div>';

echo '<div class="button" id="bookingSearchSubmit">' . $params->get('submit', JText::_('SEARCH')) . '</div>';
if ($params->get('reset', 0))
    echo '<div class="button" id="bookingSearchReset">' . JText::_('RESET') . '</div>';

if ($params->get('login', 0)) {
    $mainframe = JFactory::getApplication();
    /* @var $mainframe JApplication */
    $menu = $mainframe->getMenu();
    /* @var $menu JMenuSite */
    $item = $menu->getItem($params->get('login_itemid'));
    if ($item)
        echo '<div class="button" id="bookingSearchLogin" rel="' . JRoute::_($item->link . '&Itemid=' . $item->id) . '">' . JText::_('LOGIN') . '</div>';
}

echo '<div class="wrap"></div>';
echo '</div>';
echo '<input type="hidden" name="booking_search" id="bookingSearchTogler" value="1" />';
echo '<input type="hidden" name="' . SESSION_TESTER . '" value="1"/>';
echo '</form>';
?>