<?php

/**
 * Support for custom manipulating with HTML document using standard Joomla! object JDocumentHTML. 
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

class ADocument
{

    /**
     * Add link to style sheet for Internet Explorer 7 with hack to ignore by others browsers.
     * 
     * @param string $url style sheet URL
     */
    function addIE7StyleSheet($url)
    {
        $document = &ADocument::getDocument();
        /* @var $document JDocument */
        $tag = '<!--[if IE 7]>' . "\n";
        $tag .= '<link href="' . $url . '" rel="stylesheet" type="text/css"/>' . "\n";
        $tag .= '<![endif]-->' . "\n";
        $document->addCustomTag($tag);
    }

    /**
     * Add link to style sheet for Internet Explorer 6 with hack to ignore by others browsers.
     * 
     * @param string $url style sheet URL
     */
    function addIE6StyleSheet($url)
    {
        $document = &ADocument::getDocument();
        /* @var $document JDocument */
        $tag = '<!--[if lte IE 6]>' . "\n";
        $tag .= '<link href="' . $url . '" rel="stylesheet" type="text/css"/>' . "\n";
        $tag .= '<![endif]-->' . "\n";
        $document->addCustomTag($tag);
    }

    /**
     * Add language constants into HTML head
     * 
     * @param array $languages key is name of param
     */
    function addLGScriptDeclaration($languages)
    {
        foreach ($languages as $name => $value) {
            ADocument::addScriptPropertyDeclaration($name, JText::_($value));
        }
    }

    /**
     * Get Joomla! object JDocument
     * 
     * @return JDocument
     */
    function getDocument()
    {
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        return $document;
    }

    /**
     * Add Javascript property into HTML page head.
     * 
     * @param string $name property name
     * @param mixed $value property value
     * @param boolean $quote add quotes
     * @param boolean $htmlentities convert value as htmlentities
     */
    function addScriptPropertyDeclaration($name, $value, $quote = true, $htmlentities = true)
    {
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        if ($htmlentities) {
            $value = str_replace(array('"' , "'"), array('&quot;' , '&#039;'), $value);
        }
        if ($quote) {
            $value = '"' . $value . '"';
        }
        $document->addScriptDeclaration('	var ' . $name . ' = ' . $value . ';');
    }

    /**
     * Add into HTML HEAD URL base javascript property with name 'juri'. 
     */
    function setScriptJuri()
    {
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        $document->addScriptDeclaration('var juri = "' . JRoute::_('index.php') . '";');
    }

    /**
     * Add into HTML HEAD relative URL to calendar holder image.
     */
    function setCalendarHolder()
    {
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $document->addScriptDeclaration('var calendarHolder = "' . IMAGES . 'icon-16-calendar.png' . '";');
        $document->addScriptDeclaration('var calendarEraser = "' . IMAGES . 'icon-16-calendar-erase.png' . '";');
    }

    /**
     * Add javascript event into page HTML head running on domready.
     * 
     * @param string $code event code
     */
    function addDomreadyEvent($code)
    {
        //JHTML::_('behavior.mootools');
        JHTML::_('behavior.framework');
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        $js = 'window.addEvent(\'domready\', function() {' . PHP_EOL;
        $js .= $code . PHP_EOL;
        $js .= '});' . PHP_EOL;
        $document->addScriptDeclaration($js);
    }

    /**
     * Set reservation box params as javascript object
     * 
     * @param BookingService $service
     * @param int $objectId
     * @param int $daysTotal
     * @param int $selectedDay used for merging neighbours dates(times) ex: 21-22
     */
    function setBoxParams(&$service, $objectId, $daysTotal = 0, $selectedDay = 0)
    {
    	static $i;
    	static $commands;
    	if ($i === null)
    		$i = 0;
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        $service->toEnd = $daysTotal - $selectedDay;
        $vars = get_object_vars($service);
        $vars['object'] = $objectId;
        $id = $vars['idShort'];
        foreach ($vars as $param => $value) {
            if (is_object($value))
            	$boxes[] = $param . ' : new Array(' . implode(', ', array_map('addslashes', get_object_vars($value))) . ')';
            elseif (is_array($value))
            	$boxes[] = $param . ' : new Array("' . implode('", "', array_map('addslashes', $value)) . '")';
			else
              $boxes[] = '"' . $param . '" : "' . addslashes($value) . '"';
        }
        $document->addScriptDeclaration($commands[] = 'Calendars.boxes[' . $i ++ . '] = {' . implode(', ', $boxes) . '};');
        ADocument::addDomreadyEvent($commands[] = "document.id('" . $id . "').addEvent('click',function(event){Calendars.setCheckBox(\"" . $id . "\");event.stopPropagation();});");
        ADocument::addDomreadyEvent($commands[] = "document.id('" . $id . "').addEvent('mouseover',function(){Calendars.highlightInterval(\"" . $id . "\");});");
        ADocument::addDomreadyEvent($commands[] = "document.id('" . $id . "').addEvent('mouseout',function(){Calendars.unhighlightInterval(\"" . $id . "\");});");
        $i++;
        return $commands;
    }
}

?>