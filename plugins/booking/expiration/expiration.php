<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  plugins/search
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

class plgBookingExpiration extends JPlugin
{

	function OnDeleteExpired()
	{
		//get timestamp and interval from plugin config
		$timestamp = $this->params->get('expiration_check_timestamp', '0');
		$interval = $this->params->get('expiration_check_interval', '60');
		
		//only if it's time to check expiration
		if(($timestamp + $interval*60) < time()){
			$helpers = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booking' . DS . 'helpers' . DS;
		
		     // Find Bookit importer    
		    if (file_exists(($importer = ($helpers . 'importer.php'))))
		        include_once ($importer);
		    else
		        return false;
		
		    // Import needed Bookit framework
		    AImporter::defines();
		    AImporter::helper('booking','model','controller','html');
		    AImporter::model('reservationitems');
		    /*
		    AImporter::table('reservation','reservationitems','subject','customer');
		    AImporter::controller('reservation');
		    */
		    //cancel expired
		    $ri = new BookingModelReservationItems();
		    $ri->stornoExpired();	    
		    
		    $this->updateExpirationCheckTimestamp();
		    
		   	return true;
		}
		
		return false;
	}
	
	function updateExpirationCheckTimestamp(){
		//set time of last check
		$this->params->set('expiration_check_timestamp', time());
	
		// Get a new database query instance
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
	
		// Build the query
		$query->update('#__extensions AS ex');
		$query->set('ex.params = ' . $db->quote((string)$this->params));
		$query->where('ex.element = "expiration" AND ex.folder="booking"');
	
		// Execute the query
		$db->setQuery($query);
		$db->query();
	}
}

?>