<?php

/**
 * Scheduke parameter element.
 *
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	elements
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class JFormFieldSchedule extends JFormField
{

	var $type = 'Schedule';

	protected function getInput()
	{
		$match = array();
		if (preg_match('/^(\d+)t(\d+)d(\d+)h(\d+)m$/', $this->value, $match)) {
			$type = $match[1];
			$days = $match[2];
			$hours = $match[3];
			$minutes = $match[4];
		} else {
			$type = 1;
			$days = 0;
			$hours = 0;
			$minutes = 10;
		}
		$code = '<table>';
		$code .= '	<tr>';
		$code .= '		<td>';
		$code .= '			<select name="jelementscheduletype" id="jelementscheduletype" onchange="jelementschedule()" style="margin-bottom: 0">';
		$code .= '				<option value="1">' . JText::_('RESERVATION_DONE_AFTER') . '</option>';
		$code .= '			</select>';
		$code .= '		</td>';
		$code .= '		<td>';
		$code .= '			<input type="text" id="jelementscheduledays" name="jelementscheduledays" value="' . $days . '" size="1" onkeyup="jelementschedule()" autocomplete="off" style="width: auto" />';
		$code .= '		</td>';
		$code .= '		<td>';
		$code .= '			<label for="jelementscheduledays" style="min-width: 0">' . JText::_('DAYS') . '</label>';
		$code .= '		</td>';		
		$code .= '		<td>';
		$code .= '			<input type="text" id="jelementschedulehours" name="jelementschedulehours" value="' . $hours . '" size="1" maxlength="2" onkeyup="jelementschedule()" autocomplete="off" title="' . htmlspecialchars(JText::_('J0_23_HOURS')) . '" style="width: auto" />';
		$code .= '		</td>';		
		$code .= '		<td>';
		$code .= '			<label for="jelementschedulehours" style="min-width: 0">' . JText::_('HOURS') . '</label>';
		$code .= '		</td>';		
		$code .= '		<td>';		
		$code .= '			<input type="text" id="jelementscheduleminutes" name="jelementscheduleminutes" value="' . $minutes . '" size="1" maxlength="2" onkeyup="jelementschedule()" autocomplete="off" title="' . htmlspecialchars(JText::_('J0_59_MINUTES')) . '" style="width: auto" />';
		$code .= '		</td>';		
		$code .= '		<td>';
		$code .= '			<label for="jelementscheduleminutes" style="min-width: 0">' . JText::_('MINUTES') . '</label>';
		$code .= '		</td>';
		$code .= '	</tr>';
		$code .= '</table>';
		
		$code .= '<input type="hidden" id="jelementschedulevalue" name="' . $this->name . '" value="' . $this->value . '" />';
		$code .= '<p style="clear: both; margin: 0px; padding: 0px;">' . JText::_('CRON_URL') . ' ';
		$code .= '	<a href="' . JURI::root() . 'index.php?option=com_booking&amp;controller=reservation&amp;task=followup" target="_blank">';
		$code .= JURI::root() . 'index.php?option=com_booking&amp;controller=reservation&amp;task=followup';
		$code .= '	</a>';
		$code .= '</p>';
		$code .= '
			<script type="text/javascript">
				//<![CDATA[
				function jelementschedule() {
				
					document.id("jelementscheduledays").value = document.id("jelementscheduledays").value.toInt(); 
					document.id("jelementschedulehours").value = document.id("jelementschedulehours").value.toInt(); 
					document.id("jelementscheduleminutes").value = document.id("jelementscheduleminutes").value.toInt();
					
					if (document.id("jelementscheduledays").value == "NaN")
						document.id("jelementscheduledays").value = 0;
					if (document.id("jelementschedulehours").value == "NaN")
						document.id("jelementschedulehours").value = 0;
					if (document.id("jelementscheduleminutes").value == "NaN")
						document.id("jelementscheduleminutes").value = 0;
				
					if (document.id("jelementschedulehours").value > 23)
						document.id("jelementschedulehours").value = 23;
				
					if (document.id("jelementscheduleminutes").value > 59)
						document.id("jelementscheduleminutes").value = 59;
					
					document.id("jelementschedulevalue").value = document.id("jelementscheduletype").value + "t" + document.id("jelementscheduledays").value + "d" + document.id("jelementschedulehours").value + "h" + document.id("jelementscheduleminutes").value + "m";
				}
				//]]>
			</script>';
		return $code;
	}
}

?>