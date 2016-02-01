<?php

/**
 * Editor parameter element.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  elements
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class JElementEditor extends JFormField
{
	
	static $namevalue;
	static	$valuevalue;
	static	$nodevalue;
	static	$control_namevalue;
    
    var $_name = 'Editor';

    function getInput()
    {
    	$name = self::$namevalue;
    	$value = self::$valuevalue;
    	$node = self::$nodevalue;
    	$control_name = self::$control_namevalue;
    	
        $editor = &JFactory::getEditor();
        /* @var $editor JEditor */
        $code = $editor->display($control_name . '[' . $name . ']', $value, 800, 500, 1, 1);
        return $code;
    }
    
    function fetchElement($name, $value, &$node, $control_name)
    {
    	self::$namevalue = $name;
    	self::$valuevalue = $value;
    	self::$nodevalue = $node;
    	self::$control_namevalue = $control_name;
    	self::getInput();
    }
}

?>