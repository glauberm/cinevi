<?php

/**
 * Improving of JButtonLink from Joomla 1.5 - does not generate unique button ID
 */

defined('JPATH_BASE') or die();

// import parent class
require_once(JPATH_LIBRARIES . DS . 'joomla' . DS . 'html' . DS . 'toolbar' . DS . 'button' . DS . 'link.php');

class JButtonBookingLink extends JButtonLink
{

	/**
	 * (non-PHPdoc)
	 * @see JButtonLink::fetchId()
	 */
	function fetchId($type, $name)
	{
		return $this->_parent->_name.'-'.$name;
	}

}