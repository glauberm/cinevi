<?php

/**
 * Support for create pagination. Modified standard Joomla! pagination object.
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @subpackage  helpers 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class BookingPagination extends JPagination
{
    
    var $minLimit = 5;
    var $maxLimit = 80;

    /**
     * Creates a dropdown box for selecting how many records to show per page.
     *
     * @return	string	The html for the limit # input box
     */
    public function getLimitBox()
    {
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        
        for ($i = $this->minLimit; $i <= $this->maxLimit; $i *= 2) {
            $limits[] = JHTML::_('select.option', $i);
            if ($i > $this->total)
                break;
        }
        
        if ($mainframe->isAdmin())
            return JHTML::_('select.genericlist', $limits, 'limit', 'class="input-mini inputbox" size="1" onchange="submitform();"', 'value', 'text', $this->limit);
        else
            return JHTML::_('select.genericlist', $limits, 'limit', 'class="input-mini inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', $this->limit);
    }
    
    /**
     * (non-PHPdoc)
     * @see JPagination::_list_footer()
     */
    protected function _list_footer($list)
    {
    	$config = AFactory::getConfig();
    	/* @var $config BookingConfig */
    	
		$html = "<div class=\"list-footer\">\n";

		if ($config->displayPaginationSelector)
			$html .= "\n<div class=\"limit\">" . JText::_('JGLOBAL_DISPLAY_NUM') . $list['limitfield'] . "</div>";
		$html .= $list['pageslinks'];
		$html .= "\n<div class=\"counter\">" . $list['pagescounter'] . "</div>";

		$html .= "\n<input type=\"hidden\" name=\"" . $list['prefix'] . "limitstart\" value=\"" . $list['limitstart'] . "\" />";
		$html .= "\n</div>";

		return $html;
    }
}

?>