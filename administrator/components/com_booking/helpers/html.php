<?php

/**
 * Support for generating html code
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

class AHtml
{

    /**
     * Get calendar html with cleaning date.
     * 
     * @param string $date       date in default MySQL format
     * @param string $fieldName  html field name
     * @param string $fieldId    html field id
     * @param string $formatDate format to date humanreadable displaying
     * @param string $formatCal  interval format for js calendar, there is a different in Joomla 1.6.x.
     * For displaying is used date format (e.q. Y-m-d).
     * Calendar use strftime format (e.q. %Y-%m-%d). 
     * @param string $customParams custom HTML field params (e.q. class="inputbox")                     
     * @return string HTML code
     */
    function getCalendar($date, $name, $id, $formatDate, $format, $attribs = '', $addTime = true, $offset = true,$dataFormat = "%Y-%m-%d %H:%M:%S")
    {
    	static $done;

		if ($done === null)
			$done = array();

		JHtml::_('behavior.calendar');
		

		// Only display the triggers once for each control.
		if (! in_array($id, $done)) {
			
			$done[] = $id;
			
			$id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
			
			// field where calendar writes date value
			$setting = array('inputField: "' . $id . '"');
			// field where calendar displays formated date value 
			$setting[] = 'displayArea: "' . $id . '_da"';
			// date format for input field
			$setting[] = 'ifFormat: "'.$dataFormat.'"';
			// date format for display area
			$setting[] = 'daFormat: "' . htmlspecialchars($format, ENT_QUOTES, 'UTF-8') . '"';
			// button to trige calendar
			$setting[] = 'button: "' . $id . '_img"';
			// text align
			$setting[] = 'align: "Tl"';
			$setting[] = 'singleClick: true';
			if ($addTime)
				// show time picker
				$setting[] = 'showsTime: true';
			if (ISJ16)
				// first day of week
				$setting[] = 'firstDay: ' . JFactory::getLanguage()->getFirstDay();
				
			$document = &JFactory::getDocument();
			/* @var $document JDocumentHTML */
			$document->addScriptDeclaration('window.addEvent(\'domready\', function() { Calendar.setup({ ' . implode(', ', $setting) . ' });});');
		}

		$code = '<span id="' . $id . '_da" class="calendar_da">' . htmlspecialchars($offset ? AHtml::date($date, $formatDate) : AHtml::date($date, $formatDate, 0), ENT_COMPAT, 'UTF-8') . '</span>';
		$code .= '<input type="hidden" id="' . $id . '" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($offset ? AHtml::date($date, ADATE_FORMAT_MYSQL_DATETIME) : AHtml::date($date, ADATE_FORMAT_MYSQL_DATETIME, 0), ENT_COMPAT, 'UTF-8') . '" />';
		$code .= '<img class="calendar" src="' . IMAGES . 'icon-16-calendar.png" alt="calendar" id="' . $id . '_img" title="' . htmlspecialchars(JText::_('SET_DATE'), ENT_QUOTES, 'UTF-8') . '"/>';
		$code .= '<img class="calendar_era" src="' . IMAGES . 'icon-16-calendar-erase.png" alt="erase" id="' . $id . '_era" title="' . htmlspecialchars(JText::_('ERASE_DATE'), ENT_QUOTES, 'UTF-8') . '" onclick="ACommon.resetCalendar(\'' . $id . '\')" />';
		return $code;
    }

    function getCustomcalendar($value, $name, $id, $format, $attribs = null, $returnjs = false)
    {
        JHTML::_('behavior.calendar');
        
        if (is_array($attribs))
            $attribs = JArrayHelper::toString($attribs);
        
        $js = 'Calendar.setup({' . PHP_EOL;
        $js .= '  inputField     :    \'' . $id . '\',' . PHP_EOL;
        $js .= '  ifFormat       :    \'' . $format . '\',' . PHP_EOL;
        $js .= '  button         :    \'' . ($button = $id . '_button') . '\',' . PHP_EOL;
        $js .= '  align          :    \'Tl\',' . PHP_EOL;
        $js .= '  singleClick    :    true,' . PHP_EOL;
        $js .= '  disableFunc    :    disallowDate,' . PHP_EOL;
        $js .= '  onSelect       :    onSelectDate,' . PHP_EOL;
        $js .= '  daFormat       :    \'%A, %B %d, %Y\',' . PHP_EOL;
        $js .= '  firstDay       :    ' . JFactory::getLanguage()->getFirstDay() . PHP_EOL;
        $js .= '});' . PHP_EOL;
        
        if ($returnjs)
        	return $js;
        
        ADocument::addDomreadyEvent($js);
        
        $code = '<input type="hidden" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" ' . $attribs . ' />';
        $code .= '<a class="calendarButton" id="' . $button . '">' . JText::_('SELECT_DATE') . '</a>';
        
        return $code;
    }

    /**
     * Get time picker gui selector.
     * 
     * @param string $value 
     * @param string $field
     * @return string HTML
     */
    function getTimePicker($value, $field, $withTzOffset = true, $params = '', $midnight = false)
    {
        static $id, $firstTime;
        if (is_null($id)) {
            $id = 1;
        } else {
            $id ++;
        }
        $firstTime = is_null($firstTime);
        $picker = 'timePicker' . $id;
        $toggler = 'timePickerToggler' . $id;
        $holder = 'timePickerHolder' . $id;
        if ($withTzOffset) {
            $time = AHtml::date($value, ATIME_FORMAT_CAL, null, false);
        } else {
            $time = AHtml::date($value, ATIME_FORMAT_CAL, 0, false);
        }
        if ($withTzOffset) {
            $hour = (int) AHtml::date($value, ISJ16 ? 'H' : '%H');
        } else {
            $hour = (int) AHtml::date($value, ISJ16 ? 'H' : '%H', 0);
        }
        if ($withTzOffset) {
            $minute = (int) AHtml::date($value, ISJ16 ? 'i' : '%M');
        } else {
            $minute = (int) AHtml::date($value, ISJ16 ? 'i' : '%M', 0);
        }
        $code = '<div class="timePickerDiv">';
        if ($midnight) {
        	$code .= '<div>';
        	$code .= '<input type="checkbox" class="timePickerMidnight"' . ($time == '00:00' ? ' checked="checked"' : '') . ' onclick="timePickerMidnight(this)" />';
        	$code .= '<span>' . JText::_('MIDNIGHT') . '</span>';
        	$code .= '</div>';
        }
        
        $code .= '<div class="picker"' . ($midnight && $time == '00:00' ? ' style="display: none"' : '') . '>';
        $code .= '<input type="text" name="' . $field . '" value="' . $time . '" id="' . $picker . '" size="5" ' . $params . ' style="width: auto" class="timePicker" />';
        $code .= '<img src="' . IMAGES . 'icon-16-clock.png" id="' . $toggler . '" alt="' . JText::_('OPEN_TIME_PICKER') . '" class="clock"/>';
        $code .= '<div id="' . $holder . '" class="time_picker_div"></div>';
        $code .= '</div>';
        $code .= '</div>';
        $document = &JFactory::getDocument();
        $document->addScriptDeclaration("
        	window.addEvent('domready', 
        		function() {
        			timePickers.push(
        				new TimePicker('$holder', '$picker', '$toggler', 
        					{
        						format24: true, 
        						imagesPath:\"" . TIME_PICKER_IMAGES . "\",
        						startTime: {
        							hour: $hour,
									minute: $minute
    							}
    						}
    					)
    				);
    			}
    		)");
        if ($firstTime) {
			$document->addScriptDeclaration("        		
        		function timePickerMidnight(e) {
					var input = document.id(e).getParent().getParent().getElement('input[class=timePicker]');
					var div = document.id(e).getParent().getParent().getElement('div[class=picker]');
        			if (e.checked) {
        				input.value = '00:00';
						div.hide();
					} else {
						input.value = '';
						div.show();
					}
        		}
    		");
        }
        return $code;
    }

    /**
     * Filter no real date data like 0000-00-00 or 0000-00-00 00:00:00 or null value or empty string. 
     * 
     * @param string $date date to clean
     * @return string real date/empty string
     */
    function cleanDate($date)
    {
        switch (($date = JString::trim($date))) {
            case '0000-00-00':
            case '0000-00-00 00:00:00':
            case '00:00:00':
            case '':
            case null:
            case NULL:
                return '';
            default:
                return $date;
        }
    }

    /**
     * Get formated date in locale, GMT0 or custom localization.
     * 
     * @param string $date   date in format to work with PHP strftime (Joomla 1.5.x) or date (Joomla 1.6.x) method. 
     * @param string $format string format for strftime/date (see above).
     * @param mixed  $offset time zone offset. 0/null/value - GMT0/offset from Joomla global config/custom offset 
     * @return string formated date
     */
    function date($date, $format, $offset = null, $cleanup = true, $midnight = false)
    {
        if (ISJ16) {
            if ($offset === 0)
                $offset = 'UTC';
            if ($offset === null) {
                $mainframe = &JFactory::getApplication();
                /* @var $mainframe JApplication */
                $offset = $mainframe->getCfg('offset');
            }
        }
        
        if (!ISJ16 && strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') //http://php.net/manual/en/function.strftime.php Exmaple #3 
    		$format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
        
    	if ($cleanup)
    		$date = AHtml::cleanDate($date);
    	
        switch ($date) {
            case '':
                return $midnight ? JText::_('MIDNIGHT') : '';
            default:
                try
            	{
            		$d = JHtml::date($date, $format, $offset);
            	}
            	catch(Exception $e)
            	{           		
            		ALog::addException($e,JLog::WARNING);
            		$d = '';
            	}
                return $d;
        }
    }

    /**
     * Get dropdown list by added data
     * 
     * @param string $field name
     * @param string $noSelectText default value label
     * @param array $items dropdown items
     * @param int $selected current item
     * @param boolean $autoSubmit autosubmit form on change dropdown list true/false
     * @param string $customParams custom dropdown params like style or class params
     * @param string name of param of items which may be used like value param in select box
     * @param 
     * @return string HTML code
     */
    function getFilterSelect($field, $noSelectText, $items, $selected, $autoSubmit = false, $customParams = '', $valueLabel = 'value', $textLabel = 'text')
    {
        $first = new stdClass();
        $first->$valueLabel = 0;
        $first->$textLabel = '&ndash; ' . JText::_($noSelectText) . ' &ndash;';
        array_unshift($items, $first);
        $customParams = array(trim($customParams));
        if ($autoSubmit) {
            $customParams[] = 'onchange="this.form.submit()"';
        }
        $customParams = implode(' ', $customParams);
        return JHTML::_('select.genericlist', $items, $field, $customParams, $valueLabel, $textLabel, $selected);
    }

    /**
     * Get control panel button.
     * 
     * @param string $link URL on page
     * @param string $image button image
     * @param string $text button label
     * @return string HTML code
     */
    function getCPanelButton($link, $image, $text, $localImage = false, $params = array(), $desc = '')
    {
        static $mainframe, $lang, $template;
        if (is_null($mainframe)) {
            $mainframe = &JFactory::getApplication();
            /* @var $mainframe JAdministrator */
            $lang = &JFactory::getLanguage();
            /* @var $lang JLanguage */
            $template = $mainframe->getTemplate();
        }
        $hparams = '';
        foreach ($params as $param => $value) {
            $hparams .= htmlspecialchars($param) . '="' . htmlspecialchars($value) . '" ';
        }
        $code = '<div class="icon">' . PHP_EOL;
        $code .= '	<a href="' . $link . '" ' . $hparams . ' title="' . htmlspecialchars($text).'::'. htmlspecialchars($desc) . '" class="hasTip">' . PHP_EOL;
        $path = ($localImage ? IMAGES : 'templates/' . $template . '/images/header/') . 'icon-48-' . $image . '.png';
        $code .= '<img src="' . $path . '" alt="' . JText::_($text) . '" />';
        $code .= '		<span>' . $text . '</span>' . PHP_EOL;
        $code .= '	</a>' . PHP_EOL;
        $code .= '</div>' . PHP_EOL;
        return $code;
    }

    /**
     * Get control panel button to open standard Joomla! configuration page in lightbox.
     * 
     * @return string HTML code
     */
    function getCPanelConfigButton()
    {
        $params = array('class' => 'modal' , 'rel' => '{handler: \'iframe\', size: {x: 800, y: 600}}');
        return AHtml::getCPanelButton(ARoute::config(), 'config', JText::_('CONFIGURATION'), false, $params);
    }

    /**
     * Get state item icon with tooltip label
     * 
     * @param stdClass $row item
     * @param int $i order number in lost 
     * @return string HTML code
     */
    function state(&$row, $i, $active = true)
    {
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $tzoffset = $mainframe->getCfg('offset');
        $now = &JFactory::getDate();
        /* @var $now JDate */
        $nowUTS = $now->toUnix();
        $template = &$mainframe->getTemplate();
        $nullDate = AModel::getNullDate();
        $publishUp = &JFactory::getDate($row->publish_up, $tzoffset);
        /* @var $publishUp JDate */
        $publishDown = &JFactory::getDate($row->publish_down, $tzoffset);
        /* @var $publishDown JDate */
        $publishUpUTS = $publishUp->toUnix();
        $publishDownUTS = $publishDown->toUnix();
        $submit = $row->state > - 1;
        switch ($row->state) {
            case 0:
                $className = 'aIconUnpublish';
                $alt = 'Unpublished';
                break;
            case 1:
                if ($nowUTS <= $publishUpUTS) {
                    $className = 'aIconPending';
                    $alt = 'Pending';
                } elseif ($nowUTS <= $publishDownUTS || $row->publish_down == $nullDate) {
                    $className = 'aIconPublished';
                    $alt = 'Published';
                } elseif ($nowUTS > $publishDownUTS) {
                    $className = 'aIconExpired';
                    $alt = 'Expired';
                }
                break;
            case - 1:
                $className = 'aIconArchived';
                $alt = 'Archived';
                break;
            case - 2:
                $className = 'aIconTrash';
                $alt = 'Trashed';
                break;
        }
        $times = '';
        $alt = htmlspecialchars(JText::_($alt), ENT_QUOTES);
        if (isset($row->publish_up) && $submit) {
            if ($row->publish_up == $nullDate)
                $times .= htmlspecialchars(JText::_('OBJECT_PUBLISH_UP_INFINITY'), ENT_QUOTES);
            else
                $times .= htmlspecialchars(JText::sprintf('OBJECT_PUBLISH_UP', AHtml::date($row->publish_up, ADATE_FORMAT_LONG)), ENT_QUOTES);
        }
        if (isset($row->publish_down) && $submit) {
            if ($row->publish_down == $nullDate)
                $times .= '<br/>' . htmlspecialchars(JText::_('OBJECT_PUBLISH_DOWN_INFINITY'), ENT_QUOTES);
            else
                $times .= '<br/>' . htmlspecialchars(JText::sprintf('OBJECT_PUBLISH_DOWN', AHtml::date($row->publish_down, ADATE_FORMAT_LONG)), ENT_QUOTES);
        }
        if ($submit) {
            $code = '<span class="editlinktip hasTip" title="' . htmlspecialchars(JText::_('PUBLISH_INFORMATION'), ENT_QUOTES) . '::' . $times . '">';
            if ($active) {
                $code .= '<a href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . ($row->state ? 'unpublish' : 'publish') . '\')" title="">';
                $code .= '<span class="aIcon ' . $className . ' aIconPointer">&nbsp;</span>';
                $code .= '</a>';
            } else
                $code .= '<span class="aIcon ' . $className . '" title="' . $alt . '">&nbsp;</span>';
            $code .= '</span>';
            return $code;
        }
        return '<span class="aIcon ' . $className . '" title="' . $alt . '">&nbsp;</span>';
    }

    function noActiveAccess(&$row, $i, $archived = NULL)
    {
        if (! $row->access) {
            $color = 'green';
        } else if ($row->access == 1) {
            $color = 'red';
        } else {
            $color = 'black';
        }
        $groupname = JText::_($row->groupname);
        if ($archived == - 1) {
            $href = $groupname;
        } else {
            $href = '<span style="color: ' . $color . ';">' . $groupname . '</span>';
        }
        return $href;
    }

    /**
     * Smart state indicator. Only active or trashed icon without clickable icon. 
     * 
     * @param stdClass $row
     * @return string HTML code
     */
    function enabled(&$row)
    {
        switch ($row->state) {
            case CUSTOMER_STATE_ACTIVE:
                switch ($row->block) {
                    case CUSTOMER_USER_STATE_ENABLED:
                        $className = 'aIconTick';
                        $title = 'Active';
                        break;
                    case CUSTOMER_USER_STATE_BLOCK:
                        $className = 'aIconUnpublish';
                        $title = 'Block';
                        break;
                }
                break;
            case CUSTOMER_STATE_DELETED:
                $className = 'aIconTrash';
                $title = 'Trashed';
                break;
        }
        return AHtml::stateTool($title, '', $className);
    }

    function stateTool($title, $text, $className, $i = null, $nextHop = null, $isChecked = false)
    {
        if ($isChecked) {
            $title = JText::_('ITEM_IS_CHECKED');
        } else {
            $title = JText::_($title);
            if (! is_null($i) && ! is_null($nextHop)) {
                $title .= '::' . JText::_($text);
            }
        }
        
        $code = '<span class="editlinktip hasTip aIcon ' . $className . '" title="' . $title . '"';
        if (! is_null($i) && ! is_null($nextHop) && ! $isChecked) {
            $code .= ' onclick="listItemTask(\'cb' . $i . '\',\'' . $nextHop . '\')" style="cursor: pointer" ';
        }
        $code .= '>&nbsp;</span>';
        return $code;
    }

    function importIcons()
    {
    	AImporter::cssIcon('new', 'icon-16-new.png');
    	AImporter::cssIcon('notice', 'icon-16-notice-note.png');
        AImporter::cssIcon('tick', 'icon-16-tick.png');
        AImporter::cssIcon('unpublish', 'icon-16-storno.png');
        AImporter::cssIcon('online', 'icon-16-online.png');
        AImporter::cssIcon('trash', 'icon-16-trash.png');
        AImporter::cssIcon('pending', 'icon-16-pending.png');
        AImporter::cssIcon('published', 'icon-16-publish.png');
        AImporter::cssIcon('expired', 'icon-16-unpublish.png');
        AImporter::cssIcon('archived', 'icon-16-disabled.png');
        AImporter::cssIcon('edit', 'icon-16-edit.png');
        AImporter::cssIcon('invoice', 'icon-16-invoice.png');
        AImporter::cssIcon('info', 'icon-16-info.png');
        AImporter::cssIcon('default', 'icon-16-default.png');
        AImporter::cssIcon('email', 'icon-16-email.png');
        AImporter::cssIcon('toolProfile', 'icon-32-card.png');
        AImporter::cssIcon('toolEdit', 'icon-32-edit.png');
        AImporter::cssIcon('toolReservations', 'icon-32-edittime.png');
        AImporter::cssIcon('toolSave', 'icon-32-save.png');
        AImporter::cssIcon('toolCancel', 'icon-32-cancel.png');
        AImporter::cssIcon('toolApply', 'icon-32-apply.png');
        AImporter::cssIcon('toolTrash', 'icon-32-delete.png');
        AImporter::cssIcon('toolRestore', 'icon-32-restore.png');
        AImporter::cssIcon('toolBack', 'icon-32-back.png');
        AImporter::cssIcon('toolPublish', 'icon-32-publish.png');
        AImporter::cssIcon('toolUnpublish', 'icon-32-unpublish.png');
        AImporter::cssIcon('toolPending', 'icon-32-query.png');
        AImporter::cssIcon('toolAdd', 'icon-32-add.png');
        AImporter::cssIcon('toolDelete', 'icon-32-trash.png');
        AImporter::cssIcon('toolInvoice', 'icon-32-invoice.png');
        AImporter::cssIcon('buy', 'icon-48-buy.png');
    }

    /**
     * @param reservation $reservation
     * @return string HTML code
     */
	function renderReservationPaymentStateIcon($reservation)
    {
    	$statuses = BookingHelper::getPaymentStatuses();
    	if (isset($statuses[$reservation->paid])) {
    		$status = $statuses[$reservation->paid];
    		return '<span class="editlinktip hasTip aIcon ' . $status['icon'] . '" title="' . htmlspecialchars(JText::_($status['label'])) . '">&nbsp;</span>';
    	}
    	return '';
    }
    
    /**
     * @param reservation $reservation
     * @return string HTML code
     */
    function renderReservationStateIcon($reservation)
    {
    	$ret = "";
    	switch ($reservation->state) {
    		case RESERVATION_PRERESERVED:
    			$ret = '<span class="editlinktip hasTip aIcon aIconNew" title="'.$this->escape(JText::_('RESERVATION_PRE_RESERVED')).'">&nbsp;</span>';
    			break;
    		case RESERVATION_ACTIVE:
    			if (isset($reservation->isExpired) && $reservation->isExpired)
    				$ret = '<span class="editlinktip hasTip aIcon aIconExpired" title="'.$this->escape(JText::_('EXPIRED')).'">&nbsp;</span>';
    			else
    				$ret = '<span class="editlinktip hasTip aIcon aIconTick" title="'.$this->escape(JText::_('RESERVED')).'">&nbsp;</span>';
    			break;
    		case RESERVATION_STORNED:
    			$ret = '<span class="editlinktip hasTip aIcon aIconUnpublish" title="'.$this->escape(JText::_('RESERVATION_CANCELLED')).'">&nbsp;</span>';
    			break;
    		case RESERVATION_TRASHED:
    			$ret = '<span class="editlinktip hasTip aIcon aIconTrash" title="'.$this->escape(JText::_('RESERVATION_TRASHED')).'">&nbsp;</span>';
    			break;
    		case RESERVATION_CONFLICTED:
    			$ret = '<span class="editlinktip hasTip aIcon aIconNotice" title="'.$this->escape(JText::_('CONFLICTED')).'">&nbsp;</span>';
    			break;
    	}
    	return $ret;
    }
    
	function showPaymentTooltip($reservation, $i)
    {
    	$statuses = BookingHelper::getPaymentStatuses();
    	if (isset($statuses[$reservation->paid])) {
    		$status = $statuses[$reservation->paid];
   			if (JTable::isCheckedOut(JFactory::getUser()->id, $reservation->checked_out))
  				return '<span class="editlinktip hasTip aIcon ' . $status['icon'] . '" title="' . htmlspecialchars($status['label']) . '">&nbsp;</span>';
   			return '<span class="editlinktip hasTip aIcon ' . $status['icon'] . '" title="' . htmlspecialchars($status['label']) . '::' . htmlspecialchars($status['title']) . '" onclick="listItemTask(\'cb' . $i . '\',\'' . $status['task'] . '\')" style="cursor: pointer">&nbsp;</span>';
    	}
    	return '';
    }
    
    /**
     * Render multiple list filter by added name, options and select values
     * 
     * @param string $name filter name, use for name and id param
     * @param array $options usable options
     * @param string $select select filter values from request
     * @return string HTML code
     */
    function renderMultipleFilter($name, $options, $select)
    {
        $code = '<select name="' . $name . '[]" id="' . $name . '" size="3" multiple="multiple" onchange="this.form.submit()" class="inputbox">';
        foreach ($options as $value => $label) {
            $code .= '<option value="' . htmlspecialchars($value) . '"';
            //$code .= in_array($value, $select) ? ' selected="selected" ' : '';
            $code .= ($value == $select) ? ' selected="selected" ' : '';
            $code .= '>' . JText::_($label) . '</option>';
        }
        $code .= '</select>';
        return $code;
    }

    /**
     * Get order tools for tree items list.
     * 
     * @param array $items ordered items
     * @param int $currentIndex index of current item in list
     * @param JPagination $pagination standard Joomla! pagination object to create order arrows
     * @param boolean $turnOnOrdering turn ordering on/off - true/false
     * @param int $itemsCount total list items count
     * @return string HTML code
     */
    function orderTree(&$items, $currentIndex, &$pagination, $turnOnOrdering, $itemsCount)
    {
        $currentItem = &$items[$currentIndex];
        $currentItemParent = $currentItem->parent;
        $inBranchWithPreview = false;
        for ($i = $currentIndex - 1; $i >= 0; $i --) {
            if ($currentItemParent == $items[$i]->parent) {
                $inBranchWithPreview = true;
                break;
            }
        }
        $inBranchWithNext = false;
        for ($i = $currentIndex + 1; $i < $itemsCount; $i ++) {
            if ($currentItemParent == $items[$i]->parent) {
                $inBranchWithNext = true;
                break;
            }
        }
        $code = '<span>' . $pagination->orderUpIcon($currentIndex, $inBranchWithPreview, 'orderup', 'Move Up', $turnOnOrdering) . '</span>';
        $code .= '<span>' . $pagination->orderDownIcon($currentIndex, $itemsCount, $inBranchWithNext, 'orderdown', 'Move Down', $turnOnOrdering) . '</span>';
        $code .= '<input type="text" name="order[]" size="1" value="' . $currentItem->ordering . '" ' . ($turnOnOrdering ? '' : 'disabled="disabled"') . ' class="input-mini" style="text-align: center" />';
        return $code;
    }

    /**
     * Get checkbox HTML
     * 
     * @param int $value if 1 checkbox is checked
     * @param string $field name, use for name and id param
     * @return string HTML
     */
    function getCheckbox($value, $field, $extraValue = null, $autoSubmit = false)
    {
        $code = '<input type="checkbox" class="inputCheckbox" name="' . $field . '" id="' . $field . '" value="' . (is_null($extraValue) ? 1 : $extraValue) . '" ' . ($value !== false ? 'checked="checked"' : '');
        $code .= ($autoSubmit ? ' onclick="document.adminForm.submit()" ' : '') . '/>' . PHP_EOL;
        return $code;
    }

    function getFilterCheckbox($field, $value, $extraValue, $image, $templateImage = false, $text = null, $color = null)
    {
    	//$text = $text['title'];
        $code = '<span class="cfilter" title="' . htmlspecialchars($text, ENT_QUOTES, ENCODING) . '">' . PHP_EOL;
        $code .= AHtml::getCheckbox($value, $field, $extraValue, true);
        if ($image) {
            $code .= '<img src="' . IMAGES . 'icon-16-' . $image . '.png" alt="" onclick="document.id(\'' . $field . '\').checked=!document.id(\'' . $field . '\').checked;document.adminForm.submit();" style="cursor: pointer;" />';
        } else {
            $code .= '<label for="' . $field . '" class="text" style="color: ' . $color . '">' . JText::_($text) . '</label>';
        }
        $code .= '</span>' . PHP_EOL;
        return $code;
    }

    /**
     * Set page title by JToolBarHelper object like "OBJECT_TITLE:[task]", 
     * where task take from request and OBJECT_TITLE and icon is given by function parameter.
     * 
     * @param string $title object title
     * @param string $icon image name
     */
    function title($title, $icon, $ctitle = COMPONENT_NAME)
    {
        JToolBarHelper::title($ctitle . ': ' . JText::_($title), $icon);
    }

    function getReadmore($text, $length = null)
    {
        $text = strip_tags($text);
        $text = JString::trim($text);
        if ($length && (mb_strlen($text, 'utf8') > $length)) {
            $text = JString::substr($text, 0, $length + 1);
            $last = JString::strrpos($text, ' ');
            if ($last) {
                $text = JString::substr($text, 0, $last);
                $run = true;
                while ($run) {
                    $slength = JString::strlen($text);
                    if ($slength == 0) {
                        break;
                    }
                    $last = JString::substr($text, $slength - 1, 1);
                    switch ($last) {
                        case '.':
                        case ',':
                        case '_':
                        case '-':
                            $text = JString::substr($text, 0, $slength - 1);
                            break;
                        default:
                            $run = false;
                            break;
                    }
                }
                $text .= ' ...';
            }
        }
        return $text;
    }

    /**
     * Make custom HTML tooltip.
     * 
     * @param string $header Header text displayed with icon
     * @param string $text Text displayed after open tooltip or on mouse icon over
     * @return string HTML code 
     */
    function info($header, $text)
    {
        $header = JString::trim(JText::_($header));
        $text = JString::trim(JText::_($text));
        
        if ($header && $text)
            $title = htmlspecialchars($header, ENT_QUOTES) . '::' . htmlspecialchars($text, ENT_QUOTES);
        else
            $title = htmlspecialchars($header . $text);
        
        $html = '<div class="topInfo editlinktip hasTip" title="' . $title . '" onclick="ACommon.info(this)">' . PHP_EOL;
        $html .= '  <span>' . $header . '</span>' . PHP_EOL;
        $html .= '  <p style="display: none">' . $text . '</p>' . PHP_EOL;
        $html .= '  <div class="clr"></div>' . PHP_EOL;
        $html .= '</div>' . PHP_EOL;
        
        return $html;
    }
    
    /**
     * @param array $arrayOfUsers user has to have fields: name, message
     * @return string HTML code with username and message
     */
function showUserInfo($arrayOfUsers, $calendar = 'monthly')
    {
    	$showmessage = AFactory::getConfig()->showNoteInCalendar;
    	$data = array();
    	if ($arrayOfUsers) {
	    	foreach($arrayOfUsers as $k => $user) {
               if (!empty($user['special'])) {
                    $data[$k] = '<strong>' . implode('<br/>', ($calendar != 'monthly' ? $user['special']['short'] : $user['special']['long'])) . '</strong>';
                } else {
                    $data[$k] = $user['name'];
                    if ($showmessage && $user['message']) {
                        $data[$k] .= ' (' . $user['message'] . ')';
                    }
                }
	    	}
    	}
        $data = implode('<br/>', $data);
    	return $data;
    }

    /**
     * Get months select for quick navigator. 
     *
     * @param string $name name of HTML select box
     * @param int $selectedMonth selected month from user request
     * @param int $selectedYear selected year from user request
     * @param int $deep set calendar available deepth 
     * @param string $attribs custom HTML tag params
     * @return string HTML
     */
    function getMonthsSelect($name, $selectedMonth, $selectedYear, $deep, $attribs = '')
    {
    	$arr = array();
    	
    	$date = JFactory::getDate();
    	
    	$m = $date->format('m');
    	$y = $date->format('Y');
    	
		for ($i = 0; $i <= $deep; $i ++) {
    		//$date = JFactory::getDate('first day of + ' . $i . ' month');
			$date = JFactory::getDate(gmmktime(0, 0, 0, ($m + $i), 1, $y));
			/* @var $date JDate */
			$arr[] = JHTML::_('select.option', (int) $date->format('m') . ',' . (int) $date->format('Y'), $date->format('F') . ' ' . $date->format('Y'));
		}
        return JHTML::_('select.genericlist', $arr, $name, $attribs, 'value', 'text', $selectedMonth . ',' . $selectedYear);
    }

    /**
     * Get week select for quick navigator. 
     *
     * @param string $name name of HTML select box
     * @param int $selectedWeek selected week from user request
     * @param int $selectedYear selected year from user request
     * @param int $deep set calendar available deepth 
     * @param string $attribs custom HTML tag params
     * @return string HTML
     */
	function getWeekSelect($name, $selectedWeek, $selectedYear, $deep, $attribs = '')
    {
    	$arr = array();
		for ($i = 0; $i <= $deep; $i ++) {
			$date = JFactory::getDate('+ ' . $i . ' week');
			$date->modify('this week');
			
			$week = (int) $date->format('W');
			$week1 = $date->format(ADATE_FORMAT_NICE_SHORT);
			//in php 5.2 retur null, in php 5.3 return DateTime Object
			$date->modify('+6 days');
			$week2 = $date->format(ADATE_FORMAT_NICE_SHORT);
			$year = (int) $date->format('Y');
			$text = $week .'/'.$year.' ('.$week1 . ' - ' . $week2.')';
			$arr[] = JHTML::_('select.option', $week . ',' . $year, $text);
		}
        return JHTML::_('select.genericlist', $arr, $name, $attribs, 'value', 'text', $selectedWeek . ',' . $selectedYear);
    }

    /**
     * Convert absolute path to real path from Joomla installation root.
     * 
     * @param string $abs
     * @return string
     */
    function abs2real($abs)
    {
        $abs = JURI::root() . JPath::clean(str_replace(JPATH_ROOT . DS, '', $abs));  	
    	//windows
    	$abs = str_replace('\\', '/', $abs);    	
        return $abs;
    }

    /**
     * Display label with compulsory sign and set javascript property with information about field is compulsory.
     * 
     * @param JDocument $document
     * @param mixed $config
     * @param string $field
     * @param string $label
     * @return string
     */
    function displayLabel($document, $config, $configField, $field, $label)
    {
        static $id;
        if (is_null($id))
            $id = 0;
        if ($config instanceof BookingConfig)    
        	$isCompulsory = $config->$configField == RS_COMPULSORY;
        else 
        	$isCompulsory = $config;    
        if ($isCompulsory)
            $document->addScriptDeclaration('rfields[' . $id ++ . '] = {name: "' . $field . '", msg: "' . addslashes(JText::sprintf('ADD_S', JText::_($label))) . '"}' . PHP_EOL);
        return '<label for="' . $field . '"' . ($isCompulsory ? ' class="required"' : '') . '>' . JText::_($label) . ': '.($isCompulsory ? '<span class="star"> *</span>':'').'</label>';	
    }
    
    /**
     * Show custom user field input.
     * @param array $field
     * @param array $fields
     * @return string
     */
    function getField($field, $fields)
    {
        $value = JString::trim(AUtils::getArrayValue($fields, $field['name'] . '.value'));
        $rawvalue = JString::trim(AUtils::getArrayValue($fields, $field['name'] . '.rawvalue'));
        $value = empty($rawvalue) ? $value : $rawvalue;
        
        if (JArrayHelper::getValue($field, 'type') == 'select') {
            $arr = array(JHtml::_('select.option', '', '- ' . JText::_('SELECT') . ' -'));
            foreach ($field['options'] as $option)
                $arr[] = JHtml::_('select.option', $option, $option);
            return JHtml::_('select.genericlist', $arr, $field['name'], 'autocomplete="off"', 'value', 'text', $value);
        } elseif (JArrayHelper::getValue($field, 'type') == 'radio') {
            return '<fieldset class="radio btn-group">
            		 	<input type="radio" name="' . $field['name'] . '" id="' . $field['name'] . '-yes" value="jyes" '. ($value == 'jyes' ? 'checked="checked"' : '') . ' autocomplete="off" />
                     	<label for="' . $field['name'] . '-yes">' . JText::_('JYES') . '</label>
                     	<input type="radio" name="' . $field['name'] . '" id="' . $field['name'] . '-no" value="jno"'. ($value == 'jno' ? 'checked="checked"' : '') . ' autocomplete="off" />
                     	<label for="' . $field['name'] . '-no">' . JText::_('JNO') . '</label>
                     </fieldset>';
        }
        return '<input class="text_area" type="text" name="' . $field['name'] . '" id="' . $field['name'] . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" />';
    }

    /**
     * Get payment method select dialog
     * 
     * @param array $payments
     * @param TableReservation $reservation
     */
    function getPaymentMethodSelect(&$payments, &$reservation, $idName='payment_method_id', $nameName='payment_method_name')
    {
        $options[] = JHTML::_('select.option', 0, JText::_('UNSELECT'), 'alias', 'title');
        $options = array_merge($options, $payments);
        $code = JHTML::_('select.genericlist', $options, $idName, 'onchange="var p = document.getElementById(\''.$nameName.'\'); if(this.value == \'0\') p.value = \'\'; else p.value = this.options[this.selectedIndex].innerHTML;"', 'alias', 'title', $reservation->payment_method_id);
        $code .= '<input type="hidden" name="'.$nameName.'" id="'.$nameName.'" value="' . $reservation->payment_method_name . '" />';
        return $code;
    }

    /**
     * Return all modules on given template position.
     * 
     * @param string $positions positions names
     * @return string HTML code of rendered modules
     */
    function renderModules($positions)
    {
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        $renderer = &$document->loadRenderer('module');
        /* @var $renderer JDocumentRendererModule */
        $code = '';
        foreach (func_get_args() as $position)
            foreach (JModuleHelper::getModules($position) as $module)
                $code .= $renderer->render($module);
        return $code;
    }

    /**
     * Render Joomla toolbar box in standard template format.
     * 
     * @return string HTML code of complete toolbar box
     */
    function renderToolbarBox()
    {
        $code = '<div id="toolbar-box">';
        $code .= '<div class="t"><div class="t"><div class="t"></div></div></div>';
        $code .= '<div class="m">' . AHtml::renderModules('toolbar', 'title') . '<div class="clr"></div></div>';
        $code .= '<div class="b"><div class="b"><div class="b"></div></div></div>';
        $code .= '</div><div class="clr"></div>';
        return $code;
    }

    /**
     * Display reservation interval.
     * 
     * @param TableReservation $reservation
     */
    function interval(&$reservation, $offset = 0)
    {
       	if (AHtml::date($reservation->from, ADATE_FORMAT_MYSQL_TIME, $offset) == '00:00:00' && AHtml::date($t = $reservation->to, ADATE_FORMAT_MYSQL_TIME, $offset) == '23:59:00') {
       		// full day reservation
			if (AHtml::date($reservation->from, ADATE_FORMAT_NORMAL, $offset) == AHtml::date($reservation->to, ADATE_FORMAT_NORMAL, $offset))
				// reservation begins and ends in the same day
               	return JText::sprintf('INTERVAL_DATE', AHtml::date($reservation->from, ADATE_FORMAT_NORMAL, $offset));
           	else
				return JText::sprintf('INTERVAL_FROM_TO', AHtml::date($reservation->from, ADATE_FORMAT_NORMAL, $offset), AHtml::date($reservation->to, ADATE_FORMAT_NORMAL, $offset));
       	} else
       		// partly day reservation (hourly or night booking)
       		if (AHtml::date($reservation->from, ADATE_FORMAT_NORMAL, $offset) == AHtml::date($reservation->to, ADATE_FORMAT_NORMAL, $offset))
       			// day begin and day end are the same
       			return JText::sprintf('INTERVAL_DAY_TIME_UP_DOWN', AHtml::date($reservation->from, ADATE_FORMAT_NORMAL, $offset), AHtml::date($reservation->from, ATIME_FORMAT_SHORT, $offset), AHtml::date($reservation->to, ATIME_FORMAT_SHORT, $offset));
       		// day begin and day end are different
          	return JText::sprintf('INTERVAL_FROM_TO_TIME_UP_DOWN', AHtml::date($reservation->from, ADATE_FORMAT_NORMAL, $offset), AHtml::date($reservation->from, ATIME_FORMAT_SHORT, $offset), AHtml::date($reservation->to, ADATE_FORMAT_NORMAL, $offset), AHtml::date($reservation->to, ATIME_FORMAT_SHORT, $offset));
    }
    
    /**
     * Display label for reservation interval.
     *
     * @param TableReservation $reservation
     */
    function intervalLabel(&$reservation, $offset = 0)
    {
    	if (AHtml::date($reservation->from, ADATE_FORMAT_MYSQL_TIME, $offset) == '00:00:00' && AHtml::date($t = $reservation->to, ADATE_FORMAT_MYSQL_TIME, $offset) == '23:59:00') {
    		// full day reservation
    		if (AHtml::date($reservation->from, ADATE_FORMAT_NORMAL, $offset) == AHtml::date($reservation->to, ADATE_FORMAT_NORMAL, $offset))
    			// reservation begins and ends in the same day
    			return  JText::_('DATE');
    		else
    			return  JText::_('FROM_TO');
    	} else
    		// partly day reservation (hourly or night booking)
    		if (AHtml::date($reservation->from, ADATE_FORMAT_NORMAL, $offset) == AHtml::date($reservation->to, ADATE_FORMAT_NORMAL, $offset))
    		// day begin and day end are the same
    		return JText::_('DAY');
    	// day begin and day end are different
    	return JText::_('FROM_TO');
    }

    /**
     * Convert format string for strftime method to date method.
     * 
     * http://phpxref.com/xref/egroupware/phpgwapi/inc/horde/Horde/Util.php.source.html#l796 fixed by little
     * 
     * 
     * @param  string format string for strftime
     * @param  convert date into strftime
     * @return string format string for date
     */
    function strftime2date($format, $viceversa = false)
    {
    	if ($viceversa)
    		return strtr($format, array('a' => '%p', 'A' => '%p', 'd' => '%d', 'D' => '%a', 'F' => '%B', 'h' => '%I', 'H' => '%H', 'g' => '%l', 'H' => '%H', 'i' => '%M', 'j' => '%e', 'l' => '%A', 'm' => '%m', 'M' => '%b', 'n' => '%m', 'r' => '%a, %e %b %Y %T %Z', 's' => '%S', 'T' => '%Z', 'w' => '%w', 'W' => '%V', 'y' => '%y', 'Y' => '%Y', 'z' => '%j', 'm/d/Y' => '%D', 'M' => '%h', ' ' => '%n', 'g:i a' => '%r', 'G:i' => '%R', ' ' => '%t', 'H:i:s' => '%T', '%' => '%%'));
    	else 
    		return strtr($format, array('%p' => 'a', '%p' => 'A', '%d' => 'd', '%a' => 'D', '%B' => 'F', '%I' => 'h', '%H' => 'H', '%l' => 'g', '%H' => 'H', '%M' => 'i', '%e' => 'j', '%A' => 'l', '%m' => 'm', '%b' => 'M', '%m' => 'n', '%a, %e %b %Y %T %Z' => 'r', '%S' => 's', '%Z' => 'T', '%w' => 'w', '%V' => 'W', '%y' => 'y', '%Y' => 'Y', '%j' => 'z', '%D' => 'm/d/Y', '%h' => 'M', '%n' => ' ', '%r' => 'g:i a', '%R' => 'G:i', '%t' => ' ', '%T' => 'H:i:s', '%%' => '%'));
    }
 
 
    /**
     * 
     * Convert date format for js calendar
     * Prepends % before characters.
     * http://docs.joomla.org/Calendar_form_field_type
     * 
     * @param string format for date
     * @return string format for JS calendar
     */
    function date2calendardate($format)
    {
    	//TODO JS calendar '%B' -> 'F' is it working? IMPORTNAT
    	$chars = array('d','D','j','l','N','S','w','z','W','F','m','M','n','t','L','o','Y','y','a','A','B','g','G','h','H','i','s','u','e','I','O','P','T','Z','c','r','U');
    	
    	return preg_replace('#['.implode($chars).']#','%\\0',$format);
    }

    /**
     * Set webpage metadata. Title, keywords and description.
     * 
     * @param stdClass $object object containing parameters title,keywords and description
     * @return void
     */
    function setMetaData(&$object)
    {
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $document->setTitle($object->title . ' - ' . $mainframe->getCfg('sitename'));
        if (($keywords = JString::trim($object->keywords)))
            $document->setMetaData('keywords', $keywords);
        if (($description = JString::trim($object->description)))
            $document->setDescription($description);
    }
    
    /**
     * http://php.net/manual/en/function.strftime.php Example #4
     */
    function getSupportedTimeFormats() {
    	
    	// Describe the formats.
		$strftimeFormats = array(
		    'A' => 'A full textual representation of the day',
		    'B' => 'Full month name, based on the locale',
		    'C' => 'Two digit representation of the century (year divided by 100, truncated to an integer)',
		    'D' => 'Same as "%m/%d/%y"',
		    'E' => '',
		    'F' => 'Same as "%Y-%m-%d"',
		    'G' => 'The full four-digit version of %g',
		    'H' => 'Two digit representation of the hour in 24-hour format',
		    'I' => 'Two digit representation of the hour in 12-hour format',
		    'J' => '',
		    'K' => '',
		    'L' => '',
		    'M' => 'Two digit representation of the minute',
		    'N' => '',
		    'O' => '',
		    'P' => 'lower-case "am" or "pm" based on the given time',
		    'Q' => '',
		    'R' => 'Same as "%H:%M"',
		    'S' => 'Two digit representation of the second',
		    'T' => 'Same as "%H:%M:%S"',
		    'U' => 'Week number of the given year, starting with the first Sunday as the first week',
		    'V' => 'ISO-8601:1988 week number of the given year, starting with the first week of the year with at least 4 weekdays, with Monday being the start of the week',
		    'W' => 'A numeric representation of the week of the year, starting with the first Monday as the first week',
		    'X' => 'Preferred time representation based on locale, without the date',
		    'Y' => 'Four digit representation for the year',
		    'Z' => 'The time zone offset/abbreviation option NOT given by %z (depends on operating system)',
		    'a' => 'An abbreviated textual representation of the day',
		    'b' => 'Abbreviated month name, based on the locale',
		    'c' => 'Preferred date and time stamp based on local',
		    'd' => 'Two-digit day of the month (with leading zeros)',
		    'e' => 'Day of the month, with a space preceding single digits',
		    'f' => '',
		    'g' => 'Two digit representation of the year going by ISO-8601:1988 standards (see %V)',
		    'h' => 'Abbreviated month name, based on the locale (an alias of %b)',
		    'i' => '',
		    'j' => 'Day of the year, 3 digits with leading zeros',
		    'k' => '',
		    'l' => 'Hour in 12-hour format, with a space preceeding single digits',
		    'm' => 'Two digit representation of the month',
		    'n' => 'A newline character ("\n")',
		    'o' => '',
		    'p' => 'UPPER-CASE "AM" or "PM" based on the given time',
		    'q' => '',
		    'r' => 'Same as "%I:%M:%S %p"',
		    's' => 'Unix Epoch Time timestamp',
		    't' => 'A Tab character ("\t")',
		    'u' => 'ISO-8601 numeric representation of the day of the week',
		    'v' => '',
		    'w' => 'Numeric representation of the day of the week',
		    'x' => 'Preferred date representation based on locale, without the time',
		    'y' => 'Two digit representation of the year',
		    'z' => 'Either the time zone offset from UTC or the abbreviation (depends on operating system)',
		    '%' => 'A literal percentage character ("%")',
		);
		
		// Results.
		$strftimeValues = array();
		
		// Evaluate the formats whilst suppressing any errors.
		foreach($strftimeFormats as $format => $description){
		    if (False !== ($value = @strftime("%{$format}"))){
		        $strftimeValues[$format] = $value;
		    }
		}
		
		// Find the longest value.
		$maxValueLength = 2 + max(array_map('strlen', $strftimeValues));
		
		$return = '';
		
		// Report known formats.
		foreach($strftimeValues as $format => $value){
		    $return.= "Known format   : '{$format}' = ". str_pad("'{$value}'", $maxValueLength). " ( {$strftimeFormats[$format]} )<br>\n";
		}
		
		// Report unknown formats.
		foreach(array_diff_key($strftimeFormats, $strftimeValues) as $format => $description){
		    $return.= "Unknown format : '{$format}'   ". str_pad(' ', $maxValueLength). ($description ? " ( {$description} )" : ''). "<br>\n";
		}
		
		return $return;
    }
    
    function locations($selective = null, $back = null, $delete = false, $container = true, $js = false)
    {
    	$config = AFactory::getConfig();
    	/* @var $config BookingConfig */	
    	$mainframe = JFactory::getApplication();
    	/* @var $mainframe JApplication */
    	
    	$pickupLocation = $mainframe->getUserStateFromRequest('com_booking.pickup_location', 'pickup_location', '', 'string');
    	$pickupLocationHour = $mainframe->getUserStateFromRequest('com_booking.pickup_location_hour', 'pickup_location_hour', '', 'string');
   		$pickupLocationMin = $mainframe->getUserStateFromRequest('com_booking.pickup_location_min', 'pickup_location_min', '', 'string');
    	
   		$dropoffLocation = $mainframe->getUserStateFromRequest('com_booking.dropoff_location', 'dropoff_location', '', 'string');
   		$dropoffLocationHour = $mainframe->getUserStateFromRequest('com_booking.dropoff_location_hour', 'dropoff_location_hour', '', 'string');
   		$dropoffLocationMin = $mainframe->getUserStateFromRequest('com_booking.dropoff_location_min', 'dropoff_location_min', '', 'string');
    	
    	$code = '';
    	
    	if ($selective === true) {
    		
    		$hours = array();
    		for ($hour = 0; $hour < 24; $hour++) {
    			$hour = str_pad($hour, 2, 0, STR_PAD_LEFT);
    			$hours[] = JHTML::_('select.option', $hour, $hour);
    		}
    			
    		$minutes = array();
    		for ($minute = 0; $minute < 60; $minute += 5) {
    			$minute = str_pad($minute, 2, 0, STR_PAD_LEFT);
    			$minutes[] = JHTML::_('select.option', $minute, $minute);
    		}
    		
    		$pickupLocations = array(JHTML::_('select.option', 0, JText::_('SELECT_PICKUP_LOCATION')));
    		foreach ($config->pickuplocations as $location)
    			$pickupLocations[] = JHTML::_('select.option', $location, $location);
    			
    		$dropoffLocations = array(JHTML::_('select.option', 0, JText::_('SELECT_DROPOFF_LOCATION')));
    		foreach ($config->dropofflocations as $location)
    			$dropoffLocations[] = JHTML::_('select.option', $location, $location);
    		
            if ($container) {
                $code = '<div id="locations">';
                $code .= '<div class="location">';
                $code .= '<label for="pickup_location" id="pickupLocationLabel">' . JText::_('PICKUP_LOCATION') . '</label>';
                $code .= '<label id="pickupLocationTimeLabel">' . JText::_('TIME') . '</label>';
                $code .= '<div class="wrap"></div>';    		
            } else {
                $code = '<div class="control-group ">';
            }
            $js = $js ? ' onchange="Calendars.showTotal()" ' : '';
    		$code .= JHtml::_('select.genericlist', $pickupLocations, 'pickup_location', 'class="input-medium"'.$js, 'value', 'text', $pickupLocation);
    		$code .= JHtml::_('select.genericlist', $hours, 'pickup_location_hour', 'class="input-mini"'.$js
                    , 'value', 'text', $pickupLocationHour);
			$code .= JHtml::_('select.genericlist', $minutes, 'pickup_location_min', 'class="input-mini"'.$js, 'value', 'text', $pickupLocationMin);
            if ($container) {
                $code .= '</div>';
                $code .= '<div class="location">';
                $code .= '<label for="dropoff_location" id="droppoffLocationLabel">' . JText::_('DROPOFF_LOCATION') . '</label>';
                $code .= '<label id="droppoffLocationTimeLabel">' . JText::_('TIME') . '</label>';
                $code .= '<div class="wrap"></div>';
            } else {
                $code .= '</div>';
                $code .= '<div class="control-group ">';
            }
    		$code .= JHtml::_('select.genericlist', $dropoffLocations, 'dropoff_location', 'class="input-medium"'.$js, 'value', 'text', $dropoffLocation);
    		$code .= JHtml::_('select.genericlist', $hours, 'dropoff_location_hour', 'class="input-mini"'.$js, 'value', 'text', $dropoffLocationHour);
			$code .= JHtml::_('select.genericlist', $minutes, 'dropoff_location_min', 'class="input-mini"'.$js, 'value', 'text', $dropoffLocationMin);
            if ($container) {
                $code .= '</div>';
            }
            $code .= '</div>';            
    	} elseif ($selective === false) {
    		if ($pickupLocation || $dropoffLocation) {
	    		$code = '<div id="locations">';
	    		if ($pickupLocation){
	    			$code .= '<div class="location">';
	    			$code .= '<span class="label">' . JText::_('PICKUP_LOCATION') . '</span>';
	    			$code .= '<span class="location">' . $pickupLocation . '</span>';
	    			$code .= '<span class="time">' . JText::sprintf('LOCATION_TIME', $pickupLocationHour, $pickupLocationMin) . '</span>';
	    			$code .= '<span class="back" onclick="window.location = \''.$back.'\'">' . JText::_('LOCATION_CHANGE') . '</span>';
	    			$code .= '<input type="hidden" name="pickup_location" value="' . htmlspecialchars($pickupLocation, ENT_QUOTES) . '" />';
	    			$code .= '<input type="hidden" name="pickup_location_hour" value="' . htmlspecialchars($pickupLocationHour, ENT_QUOTES) . '" />';
	    			$code .= '<input type="hidden" name="pickup_location_min" value="' . htmlspecialchars($pickupLocationMin, ENT_QUOTES) . '" />';
					$code .= '</div>';
	    		}
	    		if ($dropoffLocation) {
					$code .= '<div class="location">';
	    			$code .= '<span class="label">' . JText::_('DROPOFF_LOCATION') . '</span>';
	    			$code .= '<span class="location">' . $dropoffLocation . '</span>';
	    			$code .= '<span class="time">' . JText::sprintf('LOCATION_TIME', $dropoffLocationHour, $dropoffLocationMin) . '</span>';
	    			$code .= '<span class="back" onclick="window.location = \''.$back.'\'">' . JText::_('LOCATION_CHANGE') . '</span>';
	    			$code .= '<input type="hidden" name="dropoff_location" value="' . htmlspecialchars($dropoffLocation, ENT_QUOTES) . '" />';
	    			$code .= '<input type="hidden" name="dropoff_location_hour" value="' . htmlspecialchars($dropoffLocationHour, ENT_QUOTES) . '" />';
	    			$code .= '<input type="hidden" name="dropoff_location_min" value="' . htmlspecialchars($dropoffLocationMin, ENT_QUOTES) . '" />';
					$code .= '</div>';
	    		}
	    		$code .= '</div>';
    		}
    	} else {
    		$code['pickup_location'] = $pickupLocation;
    		$code['pickup_location_hour'] = $pickupLocationHour;
    		$code['pickup_location_min'] = $pickupLocationMin;
    		$code['dropoff_location'] = $dropoffLocation;
    		$code['dropoff_location_hour'] = $dropoffLocationHour;
    		$code['dropoff_location_min'] = $dropoffLocationMin;
    	}
    	
		return $code;
    }
    
    public function showRecurenceTimeframe($reservedItem)
    {
    	return JFactory::getDate($reservedItem->period_time_up)->format('H:i') . ' - ' . JFactory::getDate($reservedItem->period_time_down)->format('H:i');
    }
    
    /**
     * @param TableReservationItems $reservedItem
     * @return string
     */
    public function showRecurencePattern($reservedItem) 
    {
    	$weekdays = array();
    	$recurrence = $week = $weekday = $month = '';
    	
    	if ($reservedItem->period_monday)    $weekdays[] = JText::_('MONDAY');
    	if ($reservedItem->period_tuesday)   $weekdays[] = JText::_('Tuesday');
    	if ($reservedItem->period_wednesday) $weekdays[] = JText::_('Wednesday');
    	if ($reservedItem->period_thursday)  $weekdays[] = JText::_('Thursday');
    	if ($reservedItem->period_friday)    $weekdays[] = JText::_('Friday');
    	if ($reservedItem->period_saturday)  $weekdays[] = JText::_('Saturday');
    	if ($reservedItem->period_sunday)    $weekdays[] = JText::_('SUNDAY');
    	
    	if ($reservedItem->period_week == 1)     $week = JText::_('J1ST_WEEK');
    	elseif ($reservedItem->period_week == 2) $week = JText::_('J2ND_WEEK');
    	elseif ($reservedItem->period_week == 3) $week = JText::_('J3RD_WEEK');
    	elseif ($reservedItem->period_week == 4) $week = JText::_('J4TH_WEEK');
    	
    	if ($reservedItem->period_day == 1)     $weekday = JText::_('MONDAY');
    	elseif ($reservedItem->period_day == 2) $weekday = JText::_('Tuesday');
    	elseif ($reservedItem->period_day == 3) $weekday= JText::_('Wednesday');
    	elseif ($reservedItem->period_day == 4) $weekday= JText::_('Thursday');
    	elseif ($reservedItem->period_day == 5) $weekday = JText::_('Friday');
    	elseif ($reservedItem->period_day == 6) $weekday = JText::_('Saturday');
    	elseif ($reservedItem->period_day == 7) $weekday = JText::_('SUNDAY');
    	
    	if ($reservedItem->period_month == 1)      $month = JText::_('JANUARY');
    	elseif ($reservedItem->period_month == 2)  $month = JText::_('FEBRUARY');
    	elseif ($reservedItem->period_month == 3)  $month = JText::_('MARCH');
    	elseif ($reservedItem->period_month == 4)  $month = JText::_('APRIL');
    	elseif ($reservedItem->period_month == 5)  $month = JText::_('MAY');
    	elseif ($reservedItem->period_month == 6)  $month = JText::_('JUNE');
    	elseif ($reservedItem->period_month == 7)  $month = JText::_('JULY');
    	elseif ($reservedItem->period_month == 8)  $month = JText::_('AUGUST');
    	elseif ($reservedItem->period_month == 9)  $month = JText::_('SEPTEMBER');
    	elseif ($reservedItem->period_month == 10) $month = JText::_('OCTOBER');
    	elseif ($reservedItem->period_month == 11) $month = JText::_('NOVEMBER');
    	elseif ($reservedItem->period_month == 12) $month = JText::_('DECEMBER');
    	
    	if ($reservedItem->period_type == PERIOD_TYPE_DAILY)
    		return JText::sprintf('EVERY_S', implode(', ', $weekdays));
    		
    	elseif ($reservedItem->period_type == PERIOD_TYPE_WEEKLY)
    		return JText::sprintf('EVERY_D_WEEK_S', $reservedItem->period_recurrence, implode(', ', $weekdays));
    	
    	elseif ($reservedItem->period_type == PERIOD_TYPE_MONTHLY)
    		return JText::sprintf('EVERY_MONTH_S_S', $week, $weekday);
    	
    	elseif ($reservedItem->period_type == PERIOD_TYPE_YEARLY)
    		return JText::sprintf('EVERY_YEAR_S_S_S', $month, $week, $weekday);
    	
    	return '';
    }
    
    public function showRecurenceRange($reservedItem)
    {
    	if ($reservedItem->period_end == PERIOD_END_TYPE_NO)
    		return JText::sprintf('START_S_NO_END_DATE', AHtml::date($reservedItem->period_date_up, ADATE_FORMAT_NORMAL));
    	
    	elseif ($reservedItem->period_end == PERIOD_END_TYPE_AFTER)
    		return JText::sprintf('START_S_END_AFTER_D_OCCURRENCES', AHtml::date($reservedItem->period_date_up, ADATE_FORMAT_NORMAL), $reservedItem->period_occurrences);
    	
    	elseif ($reservedItem->period_end == PERIOD_END_TYPE_DATE)
    		return JText::sprintf('START_S_END_BY_S', AHtml::date($reservedItem->period_date_up, ADATE_FORMAT_NORMAL), AHtml::date($reservedItem->period_date_down, ADATE_FORMAT_NORMAL));
    	
    	return '';
    }
    
    /**
     * @param TableReservationItems $reservedItem
     * @return string
     */
    public function showRecurrenceTotal($reservedItem)
    {
		if ($reservedItem->period_end == PERIOD_END_TYPE_NO)
			return $reservedItem->period_total . ' ' . JText::_('PERIOD_FOR_NEXT_YEAR');
		return $reservedItem->period_total;    	
    }
    
    public function monthYearPicker($name = null, $value = null) 
    {
    	JHtml::script('components/com_booking/assets/libraries/month-year-picker/js/CalendarControl.js');
    	JHtml::stylesheet('components/com_booking/assets/libraries/month-year-picker/css/StyleCalender.css');
    	
    	if ($name) {
    		$name = htmlspecialchars($name);
    		$value = htmlspecialchars($value);
    	
    		return '<input type="text" id="'.$name.'" name="'.$name.'" value="'.$value.'" size="5" onchange="__doPostBack(\''.$name.'\')" autocomplete="off" class="input-mini" />
    			 <img align="absbottom" onclick="showCalendarControl(\''.$name.'\');" src="'.JUri::root(true).'/components/com_booking/assets/libraries/month-year-picker/images/datepicker.gif" />';
    	}
    }        
    
    public static function showSupplementsColumn($supplements, $rowDelimiter = '<br>') {
        $column = '';
        foreach ($supplements as $supplement) {
            $column .= htmlspecialchars($supplement->title);
            if ($supplement->type == SUPPLEMENT_TYPE_LIST && JString::trim($supplement->value)) {                                          $column .=  ': ' . htmlspecialchars($supplement->value);
            }
            if ($supplement->capacity > 0) {
                $column .=  '&nbsp;(' . $supplement->capacity . ')';
            }
            $column .=  $rowDelimiter;
        }
        return $column;
    }
    
    /**
     * Init tooltip text. With Joomla make jQuery tooltip compatible with old Mootools Tip.
     */
    public static function tooltip() {
        $selector = '.hasTip, .hasTooltip';
        $document = JFactory::getDocument();
        if (ISJ3) {
        
            $title = 'RNYTAIDXQZ';
            $text = 'NTSNLHHQIV';
            // mask of title format for jQuery tooltip
            $short = JHtml::_('tooltipText', $title, $title, 0, 0);
            $long = JHtml::_('tooltipText', $title, $text, 0, 0);
            
            JFactory::getDocument()->addScriptDeclaration("
// Init tooltip text. With Joomla make jQuery tooltip compatible with old Mootools tip.
jQuery(document).ready(function() {
    com_booking_tooltip();
});
function com_booking_tooltip() {
    var title = '$title';
    var text = '$text';
    var short = '$short';
    var long = '$long';
                        
    jQuery('$selector').each(function(i, e) {
        var j = jQuery(e);
        var p = j.attr('title').split('::', 2); // old Mootools Tip format
        // convert to new jQuery format
        if (p[0] != undefined && p[1] != undefined) {
            var tip = long.replace(title, p[0]).replace(text, p[1]);
        } else {    
            var tip = short.replace(title, p[0]);
        }
        j.attr('title', tip);
    });
}
");
            JHTML::_('bootstrap.tooltip', $selector); // Joomla 3
        } else {
            JHTML::_('behavior.tooltip', $selector); // Joomla 2.5
        }
    }
}
?>