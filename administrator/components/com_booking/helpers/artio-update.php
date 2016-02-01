<?php
/**
 * General helper to support ARTIO On-line Updates
 * 
 * @version		$Id$
 * @package		ARTIO General
 * @subpackage  helpers 
 * @copyright	Copyright (C) 2013 ARTIO s.r.o.. 
 * @author 		ARTIO, http://www.artio.net
 * @license     All rights reserved.
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class BookingUpdateHelper {

    /**
     * Update URL for ARTIO Updater by adding or updating the Download ID if set
     * 
     * @param string $componentName
     * @param string $downloadID
     * @return boolean
     */
    static function setUpdateLink($componentName, $downloadID)
    {
        $db =& JFactory::getDBO();
        
        // look for update record in DB        
        $query = $db->getQuery(true);
        $query->select('location')->from('#__update_sites')->where('name = '.$db->quote($componentName));
        $db->setQuery($query);
        $origLocation = $location = $db->loadResult();
        
        $location_match = array();
        // if some ID is already set, update or remove it
        if (preg_match("/(-([A-Za-z0-9]*)).xml/", $location, $location_match)) {
            // update existing download ID
            if (strlen($downloadID)) {
                $location = str_replace($location_match[0], '-' . $downloadID.'.xml', $location);
            // or remove it, if not set
            } else {
                $location = str_replace($location_match[0], '.xml', $location);
            }
        // if not set yet but just entered, attach it
        } else if (strlen($downloadID)) {
            $location = str_replace('.xml', '-'.$downloadID.'.xml', $location);        
        }
        
        // if location string has changed, update it in DB
        if ($location != $origLocation) {
            $query = "UPDATE #__update_sites SET location = " . $db->quote($location)." WHERE name = " . $db->quote($componentName);
            $db->setQuery($query);
            // write to DB
            if (!$db->query()) {
                $this->setError($db->stderr(true));
                return false;
            }
        }
        return true;
    }

}
?>
