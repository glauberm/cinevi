<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage		controllers
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

AImporter::helper('controller');

class BookingControllerUser extends AController
{
	public function login()
	{
		ob_clean();
		if (JFactory::getApplication()->login(array('username' => JRequest::getString('username'), 'password' => JRequest::getString('password'))))
			die('OK');
		die('FAIL');
	}
}