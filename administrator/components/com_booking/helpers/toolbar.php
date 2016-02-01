<?php

/**
 * Component custom toolbar.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  helpers 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class BookingToolbar
{

    /**
     * New subject button with select box to choose template. Add custom button to JToolBar instance.
     */
    function newSubject ()
    {
        $templateHelper = AFactory::getTemplateHelper();
        $html = '<div class="toolbarTitle">';
        $html .= '<a class="toolbar" onclick="javascript: submitbutton(\'add\')" href="#">';
        $html .= '<span class="icon-32-new" title="' . JText::_('NEW') . '">';
        $html .= '</span>';
        $html .= JText::_('NEW');
        $html .= '</a>';
        $html .= '<div class="clr"></div>';
        $html .= '</div>';
        $html .= '<div class="toolbarSelect">';
        $html .= $templateHelper->getSelectBox('template', 'new template', 0, false, ' onchange="ListSubjects.setTemplate(this);" ');
        $html .= '</div>';
        JToolBar::getInstance('toolbar')->appendButton('Custom', $html);
    }
}
?>