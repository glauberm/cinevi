<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	controllers
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class BookingControllerLocations extends JControllerAdmin
{
	/**
	 * (non-PHPdoc)
	 * @see JControllerLegacy::getModel()
	 */
	public function getModel($name = 'Location', $prefix = 'BookingModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function pick_up_publish() {
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$this->getModel()->pick_up_publish($cid);
		$this->setRedirect(JRoute::_('index.php?option=com_booking&view=locations', false));
	}
	
	public function pick_up_unpublish() {
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$this->getModel()->pick_up_unpublish($cid);
		$this->setRedirect(JRoute::_('index.php?option=com_booking&view=locations', false));
	}
	
	public function drop_off_publish() {
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$this->getModel()->drop_off_publish($cid);
		$this->setRedirect(JRoute::_('index.php?option=com_booking&view=locations', false));
	}
	
	public function drop_off_unpublish() {
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$this->getModel()->drop_off_unpublish($cid);
		$this->setRedirect(JRoute::_('index.php?option=com_booking&view=locations', false));
	}
}