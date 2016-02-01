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

class BookingModelArticle extends JModelAdmin
{
	/**
	 * (non-PHPdoc)
	 * @see JModelForm::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_booking.article', 'article', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_booking.edit.article.data', array());
		if (empty($data))
			$data = $this->getItem();
		return $data;
	}

	/**
	 * (non-PHPdoc)
	 * @see JModelAdmin::getItem()
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		if ($pk == 1 && !$item) {
			$query = $this->getDbo()->getQuery(true);
			$query->insert('#__booking_article')->columns('id, title')->values("1, 'Terms of Contract'");
			$this->getDbo()->setQuery($query)->query();
			$item = parent::getItem($pk);
		}
		if ($pk == 2 && !$item) {
			$query = $this->getDbo()->getQuery(true);
			$query->insert('#__booking_article')->columns('id, title')->values("2, 'Terms of Privacy'");
			$this->getDbo()->setQuery($query)->query();
			$item = parent::getItem($pk);
		}
		return $item;
	}
}