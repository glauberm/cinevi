<?php

/**
 * Create parameter table for template properties. 
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

if (! class_exists('JForm'))
    jimport('joomla.html.jform');
 
AImporter::helper('utils');

class AParameter extends JForm//JRegistry//JForm //JParameter
{
    
    /**
     * Image base 
     * 
     * @var string
     */
    var $images;

    /**
     * Construct object.
     * 
     * @param $data
     * @param $path
     */
    function __construct($data, $path = null, &$xml = null)
    {
        $this->images = JURI::root() . 'components/' . OPTION . '/assets/images/';
        
        parent::__construct('form');
        if ($xml) {
            $this->load($xml);
        }
        
        $j = new JRegistry();
        $j->loadString($data);
        foreach($j->toArray() as $k=>$v)
        {
        	$this->setValue($k,'params',$v);
        }
        //$this->bindLevel('params',$j);
        
    }

    
    /*
    function loadSetupXML(&$xml)
    {
        $result = false;
        if ($params = &$xml->attributes()) {
            $result = true;
            foreach ($params as $param) {
            	//var_dump($param);
                //$this->setXML($param);
            }
        }
        
        return $result;
    }
    */

    /**
     * load params from xml
     * @return array of params 
     */
    public function loadParamsToFields()
    {
    	$params = array();
    	//var_dump($this->xml->asXml());
/*
    	if(!is_null($this->xml))
    		if(!is_null($this->xml->fields->fieldset))
		    	foreach($this->xml->fields->fieldset->field as $x)
		    	{
		    		//load attributes 
		    		$attrs = (array)$x->attributes();
		    		$attrs = $attrs['@attributes'];
		    		
		    		$attrs['id'] = (string)$attrs['name'];
		    		$attrs['paramValue'] = (string)$x;
		    		$attrs['paramLabel'] = (string)$attrs['label'];
	
		    		//get group of current element
		    		$gr = $x->xpath('ancestor::fields[@name]/@name');
		    		$groups = array_map('strval', $gr ? $gr : array());
		    		$group = implode('.', $groups);
		    		 
		    		//render label and input form to the array
		    		$field = $this->loadField($x,$group,$attrs['paramValue']);
		    		$attrs['label'] = $field->label;
		    		$attrs['value'] = $field->input;
		    		$params[] = $attrs;
		    	}*/
    	
    	if(!is_null($this->xml))
    	if(!is_null($this->xml->fields->fieldset))
    		foreach($this->xml->fields->fieldset->field as $x)
    		{
    			 
    			//load attributes
    			$xmlattr = (array)$x->attributes();
    			$xmlattr = $xmlattr['@attributes'];
    			$xmlattr['node'] = $x;
    			//check if type is set, if not, set default type as text - it prevens error
    			if(!isset($xmlattr['type']) || !$xmlattr['type']){
    				ALog::add("unset type of property ".$xmlattr['name'],JLog::ERROR);
    				$xmlattr['type'] = "text";
    				$x['type'] = 'text';
    			}
    			
    			$id = (string)$xmlattr['name'];
    			$xmlattrs[$id] = $xmlattr;
    		}
    		foreach ($this->getFieldset() as $field)
    		{
    			//var_dump($this->xml);
    			$attrs = $xmlattrs[$field->fieldname];
    			$attrs['id'] = $field->fieldname;
    			$attrs['paramValue'] = $field->value;
    			$attrs['paramLabel'] = $field->title;
    	
    	
    			$attrs['label'] = $field->label;
    			$attrs['value'] = $field->input;
    			$params[] = $attrs;
    		}
    	return $params;
    }
    
    /**
     * Render properties table.
     * 
     * @param string $name type of params to render
     * @param string $group params group to render 
     */
    function render($name = 'params', $group = '_default')
    {
        //$params = $this->getParams($name, $group);
        $params = $this->loadParamsToFields();
        $config = &AFactory::getConfig();
        $ripath = AImage::getIPath($config->templatesIcons);
        $ids = array();
        $html = array();
        $html[] = '<table class="template">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>&nbsp;</th>';
        $html[] = '<th>&nbsp;</th>';
        $html[] = '<th><h3>' . JText::_('NAME') . '</h3></th>';
        $html[] = '<th><h3>' . JText::_('VALUE') . '</h3></th>';
        $html[] = '<th><h3>' . JText::_('ICON') . '</h3></th>';
        $html[] = '<th><h3>' . JText::_('TOOLS') . '</h3></th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody id="paramlist">';
        if (is_array($params)) {
            foreach ($params as $i => $param) {
                if (! is_null($param)) {
                	// prepare parameter properties
                	/*
                    $label = $param[0];
                    $value = $param[1];
                    $id = (int) $param[5];
                    $searchable = (int) $param[6];
                    $filterable = (int) $param[7];
                    $type = $param[8];
                    $paramValue = $param[9];
                    $icon = $param[10];
                    $objects = (int) $param[12];
                    $object = (int) $param[13];
                    */
                	
                	$label = $param['label'];
                	$value = $param['value'];
                    $comparison = JArrayHelper::getValue($param, 'comparison');
                	$id = (int) $param['id'];
                	$searchable = (int) $param['searchable'];
                	$filterable = (int) $param['filterable'];
                	$type = $param['type'];
                	$paramValue = $param['paramValue'];
                	$icon = $param['icon'];
                	$objects = (int) $param['objects'];
                	$object = (int) $param['object'];
                	
                    if ($type == 'radio') {
                        $value = '<input type="radio" class="inputRadio" name="params[' . $id . ']" value="" style="display: none" ' . (! $paramValue ? 'checked="checked"' : '') . '/>' . $value;
                    }
                    //generate checked checkbox
                    if ($type == 'checkbox') {
                    	$value = '<input type="hidden" name="params[' . $id . ']" value="0" /><input type="checkbox" name="params[' . $id . ']" value="1" ' . ($paramValue == '1' ? 'checked="checked"' : '') . ' />';
                    }
                    $value .= '<input type="hidden" name="param_' . $id . '_type" id="param_' . $id . '_type" value="' . $type . '" />';
                    $ids[] = $id;
                    $html[] = '<tr id="params' . $id . '-row">';
                    $html[] = '<td class="check">';
                    $html[] = '<input type="checkbox" class="inputCheckbox" name="cid[]" id="params' . $id . '-check" value="' . $id . '"/>';
                    $html[] = '</td>';
                    $html[] = '<td>';
                    $html[] = '<span class="drop-and-drag" title="'.JText::_('DROP_AND_DRAG').'"></span>';
                    $html[] = '<input type="hidden" name="params-ordering[' . $id . ']" value="' . $i . '" />';
                    $html[] = '</td>';
                    $html[] = '<td class="label">' . $label . '</td>';
                    $html[] = '<td id="params' . $id . '-value">' . ($type == 'list' && $filterable ? JHtml::_('select.genericlist', AUtils::getCmpTypes(), 'params-comparison[' . $id . ']', 'class="hasTip" title="' . htmlspecialchars(JText::_('COMPARISON')) . '::' . htmlspecialchars(JText::_('COMPARISON_TIP')) . '"', 'value', 'text', $comparison) : '') . $value . '</td>';                    
                    // icon cell
                    $html[] = '<td>';
                    $thumb = AImage::thumb($ripath . $icon, 30, 30);
                    if ($thumb)
                    	$html[] = '<img src="' . htmlspecialchars($thumb) . '" alt="" id="params' . $id . '-icons" />';
                    else
                    	$html[] = '<img src="' . $this->images . 'spacer.gif" alt="" id="params' . $id . '-icons" />';
                    $html[] = '<input type="hidden" name="params-icons-orig[]" id="params' . $id . '-icons-orig" value="' . htmlspecialchars($icon) . '" />';
                    $html[] = '</td>';
                    // tools cell
                    $html[] = '<td id="params' . $id . '-toolbar">';
                    // render tools
                    $html[] = $this->tool(true, 'config', null, 'ATemplate.config(' . $id . ')', JText::_('CONFIG'));
                    $html[] = $this->tool(true, 'trash', null, 'ATemplate.trash(' . $id . ',true)', JText::_('TRASH'));
                    $html[] = $this->tool($searchable != 0, 'search', $id, null, JText::_('SEARCHABLE'));
                    $html[] = $this->tool($filterable != 0, 'filter', $id, null, JText::_('FILTERABLE'));
                    $html[] = $this->tool($objects != 0, 'objects', $id, null, JText::_('SHOW_AT_ITEMS_LIST'));
                    $html[] = $this->tool($object != 0, 'object', $id, null, JText::_('SHOW_AT_ITEM_DETAIL'));
                    
                    $html[] = '</td>';
                    $html[] = '</tr>';
                }
            }
        }
        $html[] = '</tbody>';
        $html[] = '</table>';
        $html[] = '<div class="glossary">';
        $glossary = array('Glossary' => null , 'CONFIG' => 'config' , 'TRASH' => 'trash' , 'SEARCHABLE' => 'search' , 'FILTERABLE' => 'filter', 'SHOW_AT_ITEMS_LIST' => 'objects', 'SHOW_AT_ITEM_DETAIL' => 'object');
        foreach ($glossary as $label => $icon) {
            if ($icon) {
                $html[] = $this->tool(true, $icon);
                ADocument::addScriptPropertyDeclaration('TmpImg' . ucfirst($icon), $this->getToolImage($icon), true, false);
            }
            $html[] = '<span>' . JText::_($label) . ($icon ? '' : ':') . '</span>';
        }
        $html[] = '</div>';
        $max = count($ids) ? max($ids) : 0;
        ADocument::addScriptPropertyDeclaration('TmpId', $max, false, false);
        return implode(PHP_EOL, $html);
    }

    /**
     * Get toolbar image as only info icon or button with javascript onclick event function.
     * 
     * @param boolean $icon add image or empty div
     * @param string $name name of image and tool
     * @param int $id property ID 
     * @param string $function javascript event function
     * @return string HTML code
     */
    function tool($icon, $name, $id = null, $function = null, $title = '')
    {
        $image = $this->getToolImage($name);
        $id = $id ? (' id="icon-' . $name . '-' . $id . '" ') : '';
        if ($icon) {
            $uname = ucfirst($name);
            $function = $function ? (' onclick="' . $function . ';" ') : '';
            $class = $function ? 'tool' : 'icon';
            return '<img src="' . $image . '" alt="' . $uname . '"' . $function . ' class="' . $class . '"' . $id . ' title="' . htmlspecialchars($title) . '"/>';
        } else {
            return '<div class="emptyIcon"' . $id . '>&nbsp;</div>';
        }
    }

    /**
     * Get tool image full path.
     * 
     * @param string $name
     * @return string
     */
    function getToolImage($name)
    {
        return $this->images . 'icon-16-' . $name . '.png';
    }

    /**
     * Get main table toolbar table.
     * 
     * @return string HTML code
     */
    function toolbar()
    {
        $bar = &JToolBar::getInstance('template-properties');
        $bar->appendButton('ALink', 'new', 'New', 'ATemplate.add()');
        $bar->appendButton('ALink', 'delete', 'Delete', 'ATemplate.trash(\'all\',true)');
        return $bar->render();
    	
    	/*
    	return '<div class="btn-group pull-left">
				<button type="button" class="btn" title="' . JText::_('NEW') . '" onclick="ATemplate.add(); return false;"><span class="icon-new"></span>New</button>
				<button type="button" class="btn" title="' . JText::_('DELETE') . '" onclick="ATemplate.trash(\'all\',true); return false;"><span class="icon-delete"></span>Delete</button>
			</div>';
		*/
    }

    /**
     * Get toolbar button.
     * 
     * @param string $name tool name
     * @param string $function javascript onclick event function
     * @return array parts of HTML code
     */
    function button($name, $function)
    {
        $html = array();
        $html[] = '<td class="button">';
        $html[] = '<a class="toolbar" href="javascript:' . $function . '">';
        $html[] = '<span class="icon-32-' . $name . '" title="' . ucfirst($name) . '">&nbsp;</span>';
        $name = JString::ucfirst($name);
        $html[] = JText::_($name);
        $html[] = '</a>';
        $html[] = '</td>';
        return $html;
    }

    /**
     * Load param.
     * 
     * @param JSimpleXMLElement $node param node
     * @param string $control_name param name
     * @param string $group param group
     * @return array param values
     */
    function getParam(&$node, $control_name = 'params', $group = '_default')
    {
        $type = $node['type'];
        $type = str_replace('mos_', '', $type); // compatibility fix with J!1.0
        $value = $this->get($node['name'], $node['default'], $group);
        switch ($type) {
            case 'checkbox': // custom rendering for checkbox
                $param = &$this->renderCheckBox($node, $value, $control_name);
                break;
            case 'radio': // custom rendering for radio button
                $param = &$this->renderRadio($node, $value, $control_name);
                break;
            case 'textarea': // custom rendering for textarea
                $param = &$this->renderEditor($node, $value, $control_name);
                break;
            default: // use standard renderer for others
                $element = &$this->loadElement($type);
                if (is_object($element))
                	$param = &$element->render($node, $value, $control_name);
                break;
        }
        $param[] = $node['searchable']; // use param in global seaching
        $param[] = $node['filterable']; // use param in filtering object's list
        $param[] = $node['type']; // data type (checkbox, radio etc.)
        $param[] = $value; // parameter value
        $param[] = $node['icon']; // decorative icon image
        $param[] = $node; // full XML node
        $param[] = $node['objects']; // use param on object's list
        $param[] = $node['object']; // use param on object detail
        return $param;
    }

    /**
     * Render check box.
     * 
     * @param JSimpleXMLElement $node param node
     * @param mixed $value param value
     * @param string $control_name param name
     * @return array
     */
    function renderCheckBox(&$node, $value, $control_name)
    {
        $param = array();
        
        $name = $node['name'];
        $label = $node['label'];
        
        $nodeName = $control_name . '[' . $name . ']';
        $nodeId = $control_name . $name;
        
        $param[] = '<label id="' . $nodeId . '-lbl" for="' . $nodeId . '">' . $label . '</label>';
        $param[] = '<input type="hidden" name="' . $nodeName . '" value="0"/><input type="checkbox" class="inputCheckbox" name="' . $nodeName . '" id="' . $nodeId . '" value="1" ' . (((int) $value == 1) ? 'checked="checked"' : '') . '/>';
        $param[] = '';
        $param[] = $label;
        $param[] = $value;
        $param[] = $name;
        
        return $param;
    }

    /**
     * Render radio buttons list.
     * 
     * @param JSimpleXMLElement $node param node
     * @param mixed $value param value
     * @param string $control_name param name
     * @return array
     */
    function renderRadio(&$node, $value, $control_name)
    {
        static $id;
        if (is_null($id)) {
            $id = 0;
        }
        $param = array();
        
        $name = $node['name'];
        $label = $node['label'];
        
        $nodeName = $control_name . '[' . $name . ']';
        $nodeId = $control_name . $name;
        
        $param[] = '<label id="' . $nodeId . '-lbl">' . $label . '</label>';
        
        $options = &$node->children();
        $count = count($options);
        $values = '';
        for ($i = 0; $i < $count; $i ++) {
            /* @var $option JSimpleXMLElement */
            $option = &$options[$i];
            $optionValue = $option['value'];
            $id ++;
            $values .= '<input type="radio" class="inputRadio" name="' . $nodeName . '" id="radio' . $id . '" value="' . htmlspecialchars($optionValue) . '"';
            if ($value == $optionValue)
                $values .= ' checked="checked" ';
            $values .= '/><label for="radio' . $id . '" style="float: left">' . $optionValue . '</label>';
        }
        $param[] = $values;
        $param[] = '';
        $param[] = $label;
        $param[] = $value;
        $param[] = $name;
        return $param;
    }
    
    function renderEditor(&$node, $value, $control_name) 
    {
        $editor = &JFactory::getEditor();
    	/* @var $editor JEditor */
    	$param[] = '<label id="' . $control_name . $node['name'] . '-lbl">' . $node['label'] . '</label>';
        $param[] = $editor->display($control_name . '[' . $node['name'] . ']', $value, 500, 500, 50, 10, false, $control_name . $node['name']);
        $param[] = '';
        $param[] = $node['label'];
        $param[] = $value;
        $param[] = $node['name'];
        return $param;
    }
}

?>