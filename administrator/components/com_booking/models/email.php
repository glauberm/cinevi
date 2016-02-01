<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage 	models
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class BookingModelEmail extends JModelAdmin
{
	/**
	 * (non-PHPdoc)
	 * @see JModelForm::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_booking.email', 'email', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
			return false;
		return $form;
	}

	/**
	 * (non-PHPdoc)
	 * @see JModelForm::loadFormData()
	 */
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_booking.edit.email.data', array());
		if (empty($data))
			$data = $this->getItem();
		return $data;
	}
}