<?php
/**
 * @copyright	  	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */
jimport('joomla.application.component.view');


$name = BookingHelper::formatName($this->reservation);
$address = BookingHelper::formatAddress($this->reservation);
?>
<div class="reservation-popup">
    <h1><?php echo JText::sprintf('RESERVATION_NUM', $this->reservation->id); ?></h1>
    <p>        
        <?php
        if ($name) {
            echo $name;
            ?><br/>
            <?php
        }
        if ($address) {
            echo $address;
            ?><br/>
            <?php
        }
        if ($this->reservation->telephone) {
            echo $this->reservation->telephone;
            ?><br/>
            <?php
        }
        if ($this->reservation->email) {
            ?>
            <a href="mailto:<?php echo $this->reservation->email; ?>"><?php echo $this->reservation->email; ?></a><br/>                        
        <?php }
        ?>
    </p>
    <?php if ($address) {
        ?>
        <div class="gmap">
            <?php
            require_once JPath::clean(JPATH_COMPONENT_SITE . '/assets/libraries/googlemaps/GoogleMapCurl.php');
            require_once JPath::clean(JPATH_COMPONENT_SITE . '/assets/libraries/googlemaps/JSMin.php');
            $googleMap = new GoogleMapCurlAPI();
            $googleMap->width = '550px';
            $googleMap->height = '400px';
            $googleMap->addMarkerByAddress(BookingHelper::formatAddress($this->reservation));
            JFactory::getDocument()->addCustomTag($googleMap->getHeaderJS() . $googleMap->getMapJS());
            echo $googleMap->printOnLoad() . $googleMap->printMap() . $googleMap->printSidebar();
            ?>                
        </div>        
    <?php } ?>
</div>