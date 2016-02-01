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

class JFormFieldTimepicker extends JFormField
{

	var $type = 'TimePicker';
	
	/**
	 * (non-PHPdoc)
	 * @see JFormField::getInput()
	 */
	protected function getInput()
	{
	    if (IS_ADMIN)
		    return AHtml::getTimePicker($this->value, $this->name, false, '', true);
		else {
		    $i = $this->id;
		    $ih = 'h' . $this->id;
		    $im = 'm' . $this->id;
		    
		    $v = explode(':', $this->value);
		    $this->value = empty($this->value) ? '00:00:00' : $this->value; 
		    $vm = JArrayHelper::getValue($v, 0);
		    $vh = JArrayHelper::getValue($v, 1);
		    
		    $js = "onchange=\"document.id('$i').value = document.id('$ih').value + ':' + document.id('$im').value\" style=\"width: auto\"";
		    
		    for ($i = 0; $i < 24; $i ++)
		        $days[] = JHtml::_('select.option', str_pad($i, 2, 0, STR_PAD_LEFT));
		    $html = JHtml::_('select.genericlist', $days, 'h'.$this->name, $js, 'text', 'value', $vm, $ih);
		    
		    for ($i = 0; $i < 60; $i += 5)
		         $mins[] = JHtml::_('select.option', str_pad($i, 2, 0, STR_PAD_LEFT));
            
		    $html .= JHtml::_('select.genericlist', $mins, 'm'.$this->name, $js, 'text', 'value', $vh, $im);
            
		    $html .= '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '" />';
            
            return $html;
		}
	}
}