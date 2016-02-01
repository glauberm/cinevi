<?php

/**
 * @package		ARTIO Booking
 * @subpackage  models
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */
defined('_JEXEC') or die();

class JFormRuleDownloadid extends JFormRule {

    public function test(\SimpleXMLElement $element, $value, $group = null, \JRegistry $input = null, \JForm $form = null) {
        $value = JString::trim($value);
        if ($value && !preg_match('/^[a-z0-9]{32}$/', $value)) {
            JFactory::getLanguage()->load('com_booking', JPATH_ADMINISTRATOR);
            return false;
        }
        $xml = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_booking/booking.xml');
        $server = (string) $xml->updateservers->server;
        $name = (string) $xml->updateservers->server['name'];
        $location = str_replace('.xml', '-' . $value . '.xml', $server);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__update_sites')
                ->set('location = ' . $db->q($location))
                ->where('name = ' . $db->q($name));
        $db->setQuery($query)->execute();
        return true;
    }

}
