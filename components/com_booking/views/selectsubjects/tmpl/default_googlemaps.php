<?php

/**
 * Google map detail template.
 *
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 */

defined('_JEXEC') or die('Restricted access');

/* @var $this BookingViewSubject */

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
?>