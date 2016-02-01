<?php

/**
 * Extra fields parameter element.
 *
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  elements
 * @copyright	Copyright (C) 2012 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

require_once(JPath::clean(JPATH_ADMINISTRATOR.'/components/com_booking/helpers/document.php'));

class JFormFieldFields extends JFormField
{
	static $namevalue;
    static	$valuevalue;
    static	$nodevalue;
    static	$control_namevalue;

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'Fields';

    function getInput()
    {
    	$name = $this->name;
    	$value = $this->value;
    	$node = self::$nodevalue;
    	$control_name = self::$control_namevalue;
    	
        $mainframe = JFactory::getApplication();
        /* @var $mainframe JApplication */

        //$name = $control_name . '[' . $name . ']';
        $id = $control_name . $name;

        $mainframe->setUserState('com_booking.fields.name', $name);
        $mainframe->setUserState('com_booking.fields.id', $id);
        $mainframe->setUserState('com_booking.fields.value', $value);

        $rname = 'extra_fields' . rand(10, 1000);
        $url = JURI::base() . 'index.php?option=com_booking&view=fields&tmpl=component';
        
        ADocument::addDomreadyEvent('document.id("' . $rname . '").src = "' . $url . '";');
        
        return '
        	<input type="hidden" id="' . $id . '" name="' . $name . '" value="" />
        	<iframe src="' . $url . '" width="100%" height="400px" frameborder="0" id="' . $rname . '"></iframe>
		';
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