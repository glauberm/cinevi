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

class JFormFieldDateformat extends JFormField {
	
	protected $type = 'dateformat';
	
	protected function getInput() {
	
		$input = '';
		// sample dates
		$now = JFactory::getDate('2012-01-01 00:00:00');
		$now2 = JFactory::getDate('2012-12-31 23:59:59');
		$now3 = JFactory::getDate('2012-01-01 01:01:01');
		$now4 = JFactory::getDate('2012-12-31 12:59:59');
		
		$formats = '<option value=""></option>'; // available date element list
		
		if ($this->element['year'] == 'true') { // year element list
			$formats .= '<option value="year" disabled="disabled" style="background-color: #ddd">' . JText::_('YEAR') . '</option>'; // list header
			$formats .= '<option value="y" title="' . htmlspecialchars(JText::_('TWO_DIGITS_YEAR_NUMBER')) . '">' . $now->format('y') . '</option>';
			$formats .= '<option value="Y" title="' . htmlspecialchars(JText::_('FOUR_DIGITS_YEAR_NUMBER')) . '">' . $now->format('Y') . '</option>';
		}
		
		if ($this->element['month'] == 'true') { // month element list
			$formats .= '<option value="month" disabled="disabled" style="background-color: #ddd">' . JText::_('MONTH') . '</option>'; // list header
			$formats .= '<option value="n" title="' . htmlspecialchars(JText::_('MONTH_NUMBER_WITHOUT_LEADING_ZERO')) . '">' . $now->format('n') . ' - ' . $now2->format('n') . '</option>';
			$formats .= '<option value="m" title="' . htmlspecialchars(JText::_('MONTH_NUMBER_WITH_LEADING_ZERO')) . '">' . $now->format('m') . ' - ' . $now2->format('m') . '</option>';
			$formats .= '<option value="M" title="' . htmlspecialchars(JText::_('THREE_LETTERS_MONTH_NAME')) . '">' . $now->format('M') . ' - ' . $now2->format('M') . '</option>';
			$formats .= '<option value="F" title="' . htmlspecialchars(JText::_('FULL_MONTH_NAME')) . '">' . $now->format('F') . ' - ' . $now2->format('F') . '</option>';
		}
		
		if ($this->element['day'] == 'true') { // day element list
			$formats .= '<option value="day" disabled="disabled" style="background-color: #ddd">' . JText::_('DAY') . '</option>'; // list header
			$formats .= '<option value="j" title="' . htmlspecialchars(JText::_('DAY_NUMBER_WITH_LEADING_ZERO')) . '">' . $now->format('j') . ' - ' . $now2->format('j') . '</option>';
			$formats .= '<option value="d" title="' . htmlspecialchars(JText::_('DAY_NUMBER_WITHOUT_LEADING_ZERO')) . '">' . $now->format('d') . ' - ' . $now2->format('d') . '</option>';
			$formats .= '<option value="D" title="' . htmlspecialchars(JText::_('THREE_LETTERS_DAY_NAME')) . '">' . $now->format('D') . ' - ' . $now2->format('D') . '</option>';
			$formats .= '<option value="l" title="' . htmlspecialchars(JText::_('FULL_DAY_NAME')) . '">' . $now->format('l') . ' - ' . $now2->format('l') . '</option>';
		}
		
		if ($this->element['meridiem'] == 'true') { // meridiem element list
			$formats .= '<option value="meridiem" disabled="disabled" style="background-color: #ddd">' . JText::_('MERIDIEM') . '</option>'; // list header
			$formats .= '<option value="a" title="' . htmlspecialchars(JText::_('LOWER_CASE_MERIDIEM')) . '">' . $now->format('a') . '</option>';
			$formats .= '<option value="A" title="' . htmlspecialchars(JText::_('UPPER_CASE_MERIDIEM')) . '">' . $now->format('A') . '</option>';			
		}
		
		if ($this->element['hour'] == 'true') { // hour element list
			$formats .= '<option value="hour" disabled="disabled" style="background-color: #ddd">' . JText::_('HOUR') . '</option>'; // list header
			$formats .= '<option value="g" title="' . htmlspecialchars(JText::_('HOUR_12_WITHOUT_LEADING_ZERO')) . '">' . $now3->format('g') . ' - ' . $now4->format('g') . '</option>';
			$formats .= '<option value="h" title="' . htmlspecialchars(JText::_('HOUR_12_WITH_LEADING_ZERO')) . '">' . $now3->format('h') . ' - ' . $now4->format('h') . '</option>';
			$formats .= '<option value="G" title="' . htmlspecialchars(JText::_('HOUR_24_WITHOUT_LEADING_ZERO')) . '">' . $now->format('G') . ' - ' . $now2->format('G') . '</option>';
			$formats .= '<option value="H" title="' . htmlspecialchars(JText::_('HOUR_24_WITH_LEADING_ZERO')) . '">' . $now->format('H') . ' - ' . $now2->format('H') . '</option>';
		}

		if ($this->element['minute'] == 'true') { // minute element list
			$formats .= '<option value="minute" disabled="disabled" style="background-color: #ddd">' . JText::_('MINUTE') . '</option>'; // list header
			$formats .= '<option value="i" title="' . htmlspecialchars(JText::_('MINUTE_NUMBER_WITH_LEADING_ZERO')) . '">' . $now->format('i') . ' - ' . $now2->format('i') . '</option>';
		}
		
		for ($i = 0; $i < $this->element['items']; $i++) {
            $value = isset($this->value[$i]) ? $this->value[$i] : '';
            $func = "ViewConfig.buildDateFormat('" . $this->id . "')";
            $events = 'onchange="' . $func . '" onclick="' . $func . '" onblur="' . $func . '"';
            
			if ($i % 2) // every even input is only letter separator
				$input .= '<input type="text" name="' . $this->name . '_' . $i . '" id="' . $this->id . '_' . $i . '" size="1" maxlength="1" value="' . htmlspecialchars($value) . '" style="width: 10px;padding: 2px;" title="' . htmlspecialchars(JText::_('CUSTOM_ONLY_LETTER_SEPARATOR')) . ' ' . $events . ' />';
			else { // every odd input is date format element list
				$input .= '<select name="' . $this->name . '_' . $i . '" id="' . $this->id . '_' . $i . '" style="width: 75px;" class="chzn-done" ' . $events . '>';                
				$input .= str_replace('value="' . htmlspecialchars($value) . '"', 'value="' . htmlspecialchars($value) . '" selected="selected"', $formats); // preselect selected option
				$input .= '</select>';
			}
		}
		
		$input .= '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . htmlspecialchars($this->value) . '" />'; // hidden field to save format		
		
		return $input;
	}	
}