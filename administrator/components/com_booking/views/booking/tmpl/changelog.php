<?php

/**
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) 2012 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die;

JToolBarHelper::title(JText::_('CHANGELOG'), 'changelog');
JToolBarHelper::back();
JToolBarHelper::divider();
if (JFactory::getUser()->authorise('core.admin', 'com_booking'))
	JToolBarHelper::preferences('com_booking');

echo nl2br(str_replace(' ', '&nbsp;', JFile::read(JPATH_COMPONENT_ADMINISTRATOR . DS . 'changelog.txt')));

?>
