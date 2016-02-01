<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	elements
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class JFormFieldTerm extends JFormFieldRadio
{

	public $type = 'Term';
	
	/**
	 * (non-PHPdoc)
	 * @see JFormField::getInput()
	 */
	protected function getInput()
	{
		static $once;
		if ($once === null) {
            JHtml::_('behavior.modal');
			JFactory::getDocument()->addScript(JURI::base() . 'components/com_booking/assets/colorpicker/jscolor.js?r=' . (JDEBUG ? uniqid() : '224'));
			JFactory::getDocument()->addScript(JURI::root() . 'components/com_booking/assets/js/view-config.js?r=' . (JDEBUG ? uniqid() : '224'));

			$guestUsergroup = intval(JComponentHelper::getParams('com_users')->get('guest_usergroup'));

			JFactory::getDocument()->addScriptDeclaration("
				window.addEvent('domready', function() { 
					ViewConfig.setEvents(true); 
					var userGroups = document.id('jformcustomers_usergroup');
					if (userGroups)
						for (var i = 0; i < userGroups.options.length; i++)
							if (userGroups.options[i].value == $guestUsergroup)
								userGroups.options[i].disabled = true;
				});
			");
			JFactory::getDocument()->addStyleDeclaration('div.control-label > label, li > label { width: 150px; } li > fieldset > label { width: auto; }');
			$once = true;
		}
		return parent::getInput() . 
			'<fieldset class="radio btn-group" style="padding-top: 5px">
				<a style="padding-top: 3px; display: inline-block" class="' . (string) $this->element['link_class'] . ' btn" rel="'.(string) $this->element['rel'] . '" href="' . JRoute::_((string) $this->element['link']) . '">' . 
					JText::_((string) $this->element['linktext']) . 
				'</a>
			</fieldset>';
	}
}
