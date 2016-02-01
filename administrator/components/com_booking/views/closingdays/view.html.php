<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	views
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die;

class BookingViewClosingdays extends JViewLegacy
{
	public $items;
	public $pagination;
	public $state;

	/**
	 * (non-PHPdoc)
	 * @see JViewLegacy::display()
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->user         = JFactory::getUser();
		
		if (IS_ADMIN) {
		    $this->addToolbar();
		    BookingHelper::setSubmenu('');
	    }
	    $this->params = new JRegistry();
	    
	    $menuitem = JFactory::getApplication()->getMenu()->getActive();
	    if ($menuitem)
	        $this->params->merge($menuitem->params);
	    
	    $document = JFactory::getDocument();
	    if ($this->params->get('menu-meta_description'))
	        $document->setDescription($this->params->get('menu-meta_description'));
	    if ($this->params->get('menu-meta_keywords'))
	        $document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));	        
	        
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('CLOSING_DAYS'), 'closingday');
		JToolbarHelper::addNew('closingday.add');
		JToolbarHelper::editList('closingday.edit');
		JToolbarHelper::deleteList('', 'closingdays.delete');
		JToolBarHelper::divider();
		if (JFactory::getUser()->authorise('core.admin', 'com_booking'))
			JToolBarHelper::preferences('com_booking');
	}
}
