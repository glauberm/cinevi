<?php

/**
 * @version	$Id$
 * @package	ARTIO Booking
 * @subpackage	controllers
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license   	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

AImporter::helper('controller');

class BookingControllerGoogle extends AController
{
    
	/**
	 * @var BookingModelGoogle
	 */
	var $_model;
	
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->_model = $this->getModel('google');
		$this->_controllerName = CONTROLLER_ADMIN;
	}
	
	public function synchronizeevents()
	{
		$this->_model->synchronizeReservations();
		$this->_model->unsynchronizeReservations();
		JFactory::getApplication()->redirect('index.php?option=com_booking');
	}
	
	public function loadcalendars()
	{
		$this->_model->loadCalendarList();
		JFactory::getApplication()->redirect('index.php?option=com_booking');
	}
	
	public function authenticate()
	{
		$this->_model->authenticate();
		JFactory::getApplication()->redirect('index.php?option=com_booking&task=' . JFactory::getApplication()->getUserState(BookingModelGoogle::COMMAND));
	}
	
}