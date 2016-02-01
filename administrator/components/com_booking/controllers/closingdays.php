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

class BookingControllerClosingdays extends JControllerAdmin
{
	/**
	 * (non-PHPdoc)
	 * @see JControllerLegacy::getModel()
	 */
	public function getModel($name = 'Closingday', $prefix = 'BookingModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see JControllerAdmin::delete()
	 */
	public function delete()
	{
		$cid = JRequest::getVar('cid', array(), '', 'array');
		if (!is_array($cid) || count($cid) < 1)
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		else {
			$model = $this->getModel();
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);
			if ($model->delete($cid))
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			else
				$this->setMessage($model->getError());
		}
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
}