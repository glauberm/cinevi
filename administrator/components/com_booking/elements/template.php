<?php

/**
 * Popup element to select subject.
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

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

JHTML::_('behavior.modal', 'a.modal');

jimport('joomla.html.parameter.element');

if (class_exists('JFormField')) {
	/**
 	 * @since Joomla 1.6
 	 */
	class JFormFieldTemplate extends JFormField {
		
		protected function getInput() {
			return JElementTemplate::fetchElement($this->name, $this->value, $this->element['use_type']);
		}
	}
}

/**
 * @since Joomla 1.5
 */
class JElementTemplate extends JFormField
{

	static $namevalue;
    static	$valuevalue;
    static	$nodevalue;
    static	$control_namevalue;
    /**
     * Display button to open popup window. 
     * 
     * @param string $name
     * @param mixed  $value
     * @param mixed  $node
     * @param string $control_name
     */
    function getInput()
    {
    	$name = self::$namevalue;
    	$value = self::$valuevalue;
    	$node = self::$nodevalue;
    	$control_name = self::$control_namevalue;

        if ($node instanceof JSimpleXMLElement) {
            $type = $node['use_type'];
        } else {
            $type = (int) $node;
        }
        
        if (! class_exists('BookingModelTemplate')) {
            if (! class_exists('AImporter')) {
                include_once (JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booking' . DS . 'helpers' . DS . 'importer.php');
                include_once (JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booking' . DS . 'helpers' . DS . 'html.php');
                include_once (JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booking' . DS . 'helpers' . DS . 'model.php');
                include_once (JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booking' . DS . 'helpers' . DS . 'route.php');
            }
            AImporter::defines();
            AImporter::model('template');
            AImporter::table('template');
        }

        $templateHelper = &AFactory::getTemplateHelper();
        $template = $templateHelper->getTemplateById($value);
        
        if ($control_name) {
            $fieldName = $control_name . '[' . $name . ']';
        } else {
            $fieldName = $name;
        }
        
        $id = 'template_id';
        $nameId='template_name';
        
        if (preg_match('#^items\[([^\]]+)\]\[[^\]]+\]$#',$name,$matches)) { //if more subject selects on page (array)
        	$id='items['.$matches[1].']['.$id.']';
        	$nameId='items['.$matches[1].']['.$nameId.']';
        }

        $html = '<div style="float: left; height: 19px; padding-top: 3px;">';
        $html .= '<input style="color: #000000;" size="40" type="text" id="'.$nameId.'" value="' . htmlspecialchars($template->name, ENT_QUOTES, ENCODING) . '" disabled="disabled" />';
        $html .= '</div>';
        $html .= '<div class="button2-left">';
        $html .= '<div class="blank">';
        $html .= '<a class="modal" title="' . JText::_('SELECT_A_TEMPLATES') . '"  href="' . ARoute::browse(CONTROLLER_TEMPLATE, true, '&type=' . $type.'&input='.$id) . '" rel="{handler: \'iframe\', size: {x: 1000, y: 600}}">' . JText::_('SELECT') . '</a>';
        $html .= '<a title="' . JText::_('RESET') . '"  href="#" onclick="document.getElementById(\'' . $id . '\').value=0;document.getElementById(\'' . $nameId . '\').value=\'\';">' . JText::_('RESET') . '</a>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<input type="hidden" id="'.$id.'" name="' . $fieldName . '" value="' . $value . '" />';
        return $html;
    }
    
    function fetchElement($name, $value, $node, $control_name = null)
    {
    	self::$namevalue = $name;
    	self::$valuevalue = $value;
    	self::$nodevalue = $node;
    	self::$control_namevalue = $control_name;
    	return self::getInput();
    }
}

?>