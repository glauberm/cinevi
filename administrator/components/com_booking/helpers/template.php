<?php

/**
 * Support for manipulating with objects templates.
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

//import needed Joomla! libraries
jimport('joomla.filesystem.file');

AImporter::model('template');

class ATemplateHelper
{
    
    /**
     * Collection of templates
     * 
     * @var array
     */
    var $_templates;

    function __construct()
    {
        $this->loadTemplates();
    }

    /**
     * Get templates select
     * 
     * @param int $select select option
     * @param boolean $autoSubmit sign if select box is list filter or edit field
     * @return string HTML code
     */
    function getSelectBox($name, $noSelect, $select, $autoSubmit, $customParams = '')
    
    {
        $templates = $this->_templates;
        return AHtml::getFilterSelect($name, $noSelect, $templates, $select, $autoSubmit, $customParams, 'id', 'name');
    }

    /**
     * Load templates from XML. Create objects and save into object collection.
     */
    function loadTemplates()
    {
        $model = new BookingModelTemplate();
        $sources = &$model->loadList();
        $this->_templates = array();
        if(is_array($sources) && !empty($sources))
        {
	        foreach ($sources as $source) {
	            $template = new ATemplate();
	            $template->source = $source->xml;
	            $template->init();
	            $this->_templates[] = $template;
	        }
        }
    }

    /**
     * Info about count saved templates.
     * 
     * @return boolean true have more templates, false templates pool is empty
     */
    function haveTemplates()
    {
        $count = count($this->_templates);
        $haveTemplates = $count != 0;
        return $haveTemplates;
    }

    /**
     * Search in loaded templates by id
     * 
     * @param int $id
     * @return ATemplate null if not found
     */
    function getTemplateById($id)
    {
        $id = (int) $id;
        foreach ($this->_templates as $template) {
            if ((int) $template->id == $id) {
                return $template;
            }
        }
        return new ATemplate();
    }

    /**
     * Import template js source files
     */
    function importAssets()
    {
        $files = JFolder::files(SITE_ROOT . DS . 'assets' . DS . 'js', '.js$', false, false);
        foreach ($files as $file) {
            if (strpos($file, 'template') === 0) {
                AImporter::js(substr($file, 0, strlen($file) - 3));
            }
        }
    }

    function removeItems($itemsToDelete)
    {
        $templates = array();
        foreach ($itemsToDelete as $itemToDelete) {
            if (! isset($templates[$itemToDelete->template])) {
                $templates[$itemToDelete->template] = array();
            }
            $templates[$itemToDelete->template][] = $itemToDelete->id;
        }
        foreach ($templates as $templateId => $templateItems) {
            $templateObject = $this->getTemplateById($templateId);
            if (is_object($templateObject)) {
                $templateObject->removeItem($templateItems);
            }
        }
        return true;
    }

    /**
     * Import Templates Icons to Javascript Array and flush into HTML Head.
     * 
     * @param string $apath absolute path to icons
     * @param string $rpath relative patch to icons
     */
    function importIconsToJS($apath, $rpath)
    {
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        if (! is_dir($apath)) {
            JError::raiseNotice(100, JText::_('ICONS_FOLDER_NO_EXISTS'));
            $icons = array();
        } else {
            $icons = &JFolder::files($apath);
        }
        $count = count($icons);
        $thumbsIcons = array();
        $realIcons = array();
        for ($i = 0; $i < $count; $i ++) {
            $icon = &$icons[$i];
            $thumbsIcons[] = '"' . htmlspecialchars(AImage::thumb($apath . $icon, 30, 30)) . '"';
            $realIcons[] = '"' . htmlspecialchars($icon) . '"';
        }
        $document->addScriptDeclaration('	var TmpIconsThumbs = new Array(' . implode(',', $thumbsIcons) . ');');
        $document->addScriptDeclaration('	var TmpIconsReal = new Array(' . implode(',', $realIcons) . ');');
    }
}

/**
 * Object template with params loaded from XML source file
 * 
 */
class ATemplate
{
    
    /**
     * XML parser
     * 
     * @var SimpleXmlElement
     */
    var $parser;
    
    /**
     * XML source file
     * 
     * @var string
     */
    var $source;
    
    /**
     * Template name
     * 
     * @var string
     */
    var $name;
    
    /**
     * Unique ID use in database
     * 
     * @var int
     */
    var $id;
    
    var $params;

    function __construct()
    {
        $this->parser = '';
        $this->source = '';
        $this->name = '';
        $this->id = 0;
    }

    /**
     * Init object from XML source file
     */
    function init()
    {
        $app = JFactory::getApplication();
        /* @var $app JApplication */
        if($this->source)
        	$this->parser = new SimpleXMLElement($this->source);
        else
        	$this->parser = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><form name="" id=""><fields name="params"><fieldset></fieldset></fields></form>');
        
        $root = $this->parser;
        if (is_object($root)) {
            $this->name = (string)$root['name'];
            if ($app->isSite())
            	$this->name = $this->translateParam($this->name);
            $this->id = (int) $root['id'];
        }
    }

    /**
     * Get template table name
     * 
     * @param int id if is null use global id
     * @return string
     */
    function getDBTableName($id = null)
    {
        return TEMPLATES_DB_PREFIX . (is_null($id) ? $this->id : $id);
    }

    /**
     * Load saved template params for concrete object using this template. Add params into format: param=value.
     * 
     * @param int $id object ID
     * @return string
     */
    function loadObjectParams($id)
    {
        $params = new JRegistry();
        if ($this->tableExist()) {
            $db = &JFactory::getDBO();
            /* @var $db JDatabaseMySQL */
            $db->setQuery('SELECT * FROM `' . $this->getDBTableName() . '` WHERE `id` = ' . (int) $id);
            $result = $db->query();
            if ($result instanceof mysqli_result)
                $fields = mysqli_fetch_assoc($result);
            elseif (is_resource($result))
                $fields = mysql_fetch_assoc($result);
            if (is_array($fields)) {
                unset($fields['id']);
                $params->loadArray($fields);
            }
        }
        return $params->toString();
    }

    function translateParam($value)
    {
        if (BookingHelper::joomFishIsActive()) {
            static $translations;
            if (is_null($translations)) {
                $db = &JFactory::getDBO();
                /* @var $db JDatabaseMySQL */
                $language = &JFactory::getLanguage();
                /* @var $language JLanguage */
                $tag = $language->getTag();
                
                $query = 'SELECT `lang_id` FROM `#__languages` WHERE `lang_code` = \'' . $tag . '\'';

                // search for language ID by tag
                $db->setQuery($query);
                $lid = (int) $db->loadResult();
                
                // search for translations for current language
                $query = 'SELECT `value`.`value` AS `original`, COALESCE(`view`.`value`,`value`.`value`) AS `translation` ';
                $query .= 'FROM `#__booking_template_value` AS `value` ';
                $query .= 'LEFT JOIN `#__booking_template_value_view` AS `view` ';
                $query .= 'ON `view`.`id` = `value`.`id` AND `view`.`language` = ' . $lid;
                $db->setQuery($query);
                $data = &$db->loadObjectList();
                
                // convert to asociated array
                $translations = array();
                $count = count($data);
                for ($i = 0; $i < $count; $i ++) {
                    $item = &$data[$i];
                    $translations[$item->original] = $item->translation;
                }
                unset($data);
            }
            $value = isset($translations[(string)$value]) ? $translations[(string)$value] : $value;
        }
        return $value;
    }

    /**
     * Display param value. If param type is checkbox display text yes/no. Otherwise display translated param text value. 
     * 
     * @param array $param
     * @return string
     */
    function displayParamValue(&$param)
    {
        if ($param[PARAM_TYPE] == 'checkbox')
            return $param[PARAM_PARAMVALUE] == 1 ? JText::_('JYES') : JText::_('JNO');
        else
            return ATemplate::translateParam($param[PARAM_PARAMVALUE]);
    }

    /**
     * Save template from request.
     * 
     * @param stdClass $item object using template
     * @param array $data request params
     */
    function store(&$item = null, $copy = false)
    {
    	$db = &JFactory::getDBO();
    	//select old xml values
    	$query = 'SELECT xml FROM `#__booking_template` WHERE `id`='.$this->id;
    	$db->setQuery($query);
    	$oldXml = $db->loadResult();
        $xml = $this->storeData($item, $copy);
        
        if($oldXml)
        {
        	$this->updateParams($xml,$oldXml);
        }
        
        return $xml;
    }
    
    /**
     * Save template from request.
     *
     * @param stdClass $item object using template
     * @param array $data request params
     */
    function storeData(&$item = null, $copy = false)
    {
    	if ($copy) { // copy template
    		$from = $this->id;
    		$this->setNewId();
    		$to = $this->id;
    		$this->copyTable($from, $to);
    	}

    	if (! $this->tableExist()) { // create database table for template
    		$this->setNewId();
    		$this->createTable();
    		if (is_object($item)) {
    			$item->template = $this->id;
    		}
    	}
    
    	$this->loadParams();
    
    	$requestParams = &ARequest::getStringArray('params'); // existing parameters
        $paramsComparison = &ARequest::getStringArray('params-comparison'); // existing parameters comparison
    	$requestParamsOutput = &ARequest::getStringArray('params-output'); // new parameters
    	$requestParamsOrdering = &ARequest::getStringArray('params-ordering'); // parameters ordering

    	if(is_array($this->params))
	    	foreach ($this->params as $name => $existParam) { // proccess existing parameters
	    		if (! array_key_exists($name, $requestParams)) { // parameter was deleted
	    			unset($this->params[$name]);
	    			$this->dropColumn($name);
	    		} else { // update parameter value
	    			$this->params[$name]->value = $requestParams[$name];
                    $this->params[$name]->comparison = JArrayHelper::getValue($paramsComparison, $name);
	    		}
	    		if (array_key_exists($name, $requestParamsOutput)) { // existing parameter was modified
	    			$this->params[$name]->update($requestParamsOutput[$name]);
	    			for ($i = 0; $i < count($existParam->options); $i ++) { // proccess parameter options
	    				if (! isset($existParam->newOptions[$i])) { // parameter hasn't options
	    					$this->updateColumnValue($name, $existParam->options[$i], '');
	    				} elseif ($existParam->options[$i] != $existParam->newOptions[$i]) { // update parameter options
	    					$this->updateColumnValue($name, $existParam->options[$i], $existParam->newOptions[$i]);
	    				}
	    			}
	    			$this->params[$name]->options = $this->params[$name]->newOptions;
	    			unset($requestParamsOutput[$name]);
	    		}
	    	}

    	foreach ($requestParamsOutput as $name => $newParam) { // proccess new parameters
    		$this->params[$name] = new ATemplateParam();
    		$this->params[$name]->name = $name;
    		$this->params[$name]->value = isset($requestParams[$name]) ? $requestParams[$name] : '';
            $this->params[$name]->comparison = JArrayHelper::getValue($paramsComparison, $name);
    		$this->params[$name]->update($newParam);
    		$this->params[$name]->options = $this->params[$name]->newOptions;
    		$this->addColumn($name, $this->params[$name]->type);
    	}

    	$orderedParams = array(); // reorder parameters
    	asort($requestParamsOrdering);
    	if(is_array($requestParamsOrdering))
    	{
    		foreach ($requestParamsOrdering as $paramName => $orderingValue){
    			if(array_key_exists($paramName,$this->params) && ($this->params[$paramName] != null)){
    				$orderedParams[] = $this->params[$paramName];
    			}
    			else
    				null;
    		}
    	}
    	
    	$this->params = $orderedParams;
    	//var_dump($this->params);

    	if (is_object($item)) {
    		$this->saveItem($item->id);
    	}
    
    	$xml = $this->getXML();

    	return $xml;
    }
    
    private function updateParams($newXml,$oldXml)
    {
    	//from xml to array of labels;
    	$labels1 = $this->parseXml($oldXml);
    	$labels2 = $this->parseXml($newXml);
    
    	//find which labels aren't in new data
    	$del = array();
    	if($labels2)
    	{
    		foreach($labels1 as $l)
    		{
    			if(!in_array($l,$labels2))
    				$del[] = $l;
    		}
    	}
    	else
    	{
    		$del = $labels1;
    	}
    
    	//var_dump($del);
    	//delete every label, which isn't in others template xml string
    	$idToDoNotDell = false;
    	$db = &JFactory::getDBO();
    	
    	if(is_array($del))
	    	foreach($del as $label)
	    	{
	    		//select all collision id's
	    		$query = 'SELECT `id` FROM `#__booking_template` WHERE `xml` LIKE \'%label="'.$label.'"%\' AND `id` <>'.$this->id;
	    		$db->setQuery($query);
	    		$idToDoNotDell = $db->loadAssocList();
	    		 
	    		//if isn't collision, delete labels
	    		if(!$idToDoNotDell)
	    		{
	    			$query = "DELETE FROM `#__booking_template_value` WHERE `value` = '".$label."'";
	    			$db->setQuery($query);
	    			$db->Query();
	    		}
	    	}
    	//var_dump($idToDoNotDell);
    }
    
    //from xml string to the array of labels
    private function parseXml($xml)
    {
    	$t = new ATemplate();
    	$t->source = $xml;
    	$t->init();
    	$properties = (array)$t->parser->attributes();
    	$templates = array($properties['@attributes']);
    	$labels = array();
    	foreach($templates as $params)
    	{
    		foreach($params as $param)
    		{
    			if(is_array($param))
	    			if(array_key_exists(11, $param)){
		    			$xmlArray = $param[11]->attributes();
		    			$labels[] = $xmlArray['label'];
	    			}
    		}
    	}
    	return $labels;
    }

    /**
     * Update template xml source.
     */
    function getXML()
    {
        //$root = $this->parser;
        $root = null;
        $attrs = array('name' => $this->name , 'id' => $this->id);
        if (!$root)
        	$root = new SimpleXMLElement('<form></form>'); 
        
        $attrs = array_change_key_case($attrs, CASE_LOWER);
        foreach($attrs as $key => $attr){
        	if(!$root[$key])
            	$root->addAttribute($key,$attr);
        	else
        		$root[$key] = $attr;
       	}

        if(is_array($this->params) && !empty($this->params))
        {
	        $params = $root->fields->fieldset;
	        if (! $params) {
	            $root->addChild('fields');
	            $root->fields->addAttribute('name','params');
	            $root->fields->addChild('fieldset');

	            $params = $root->fields->fieldset;
	        }
	        
	        //$params->_children = array();
	        foreach ($this->params as $param) {
	        	if($param){
	        	
	        	
		            $attributes = array();
		            $attributes['name'] = $param->name;
		            $attributes['type'] = $param->type;
		            $attributes['default'] = '';
		            $attributes['label'] = $param->label;
		            $attributes['description'] = '';
		            $attributes['searchable'] = $param->searchable;
		            $attributes['filterable'] = $param->filterable;
		            $attributes['objects'] = $param->objects;
		            $attributes['object'] = $param->object;
		            $attributes['icon'] = $param->icon;
                    $attributes['comparison'] = $param->comparison;
		            if ($param->type == 'radio')
		            	$attributes['class'] = 'btn-group';
		            $child = $params->addChild('field');
		            foreach($attributes as $key => $attr){
		            	if(!$child[$key])
		            		$child->addAttribute($key,$attr);
		            	else
		            		$child[$key] = $attr;
		            }
		            if (is_array($param->options)) {
		                foreach ($param->options as $option) {
		                    $options = $child->addChild('option');
		                    $options->addAttribute('value',$option);
		                }
		            }
	        	}
	        }
        }
        $xml = $root->asXML();
        /*$xml = '<?xml version="1.0" encoding="utf-8"?>' . $xml;
        */

        return $xml;
    }

    /**
     * Load template params from xml source.
     */
    function loadParams()
    {
    	$root = $this->parser;
        $params = $root ? @$root->fields->fieldset->field: null;
        //var_dump($params);

        if (is_object($params)) {
            foreach ($params as $param) {
                $object = new ATemplateParam();              
                $object->load($param);
                $this->params[$object->name] = $object;
            }
        }
    }

    /**
     * Check if template table exists
     * 
     * @return boolean
     */
    function tableExist()
    {
        AImporter::helper('model');
        $tableExists = AModel::tableExists($this->getDBTableName());
        return $tableExists;
    }

    /**
     * Check if template have saved items.
     * 
     * @return boolean
     */
    function haveItems()
    {
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        $query = 'SELECT COUNT(*) FROM `' . $this->getDBTableName($this->id) . '`';
        $db->setQuery($query);
        $count = (int) $db->loadResult();
        return $count != 0;
    }

    /**
     * Get new template ID
     * 
     * @return int
     */
    function setNewId()
    {
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        $tmpPrefix = str_replace('#__', $db->getPrefix(), $this->getDBTableName(''));
        $query = 'SHOW TABLES LIKE ' . $db->Quote($tmpPrefix . '%');
        $db->setQuery($query);
        $tables = $db->loadAssocList();
        $existsIds = array();
        if($tables)
	        foreach ($tables as $table) {
	            $existsIds[] = (int) str_replace($tmpPrefix, '', reset($table));
	        }
        $this->id = count($existsIds) ? (max($existsIds) + 1) : 1;
        //$this->source = TEMPLATES_SOURCE . DS . 'template_' . $this->id . '.xml';
    }

    /**
     * Create new template table with primary ID column
     */
    function createTable()
    {
        $db = &JFactory::getDBO();
        $query = 'CREATE TABLE IF NOT EXISTS ' . $this->getDBTableName() . ' ( id int(11) NOT NULL auto_increment, PRIMARY KEY  (id) ) ENGINE=MyISAM DEFAULT CHARSET=utf8';
        $db->setQuery($query);
        $db->query();
    }

    function copyTable($from, $to)
    {
        $db = &JFactory::getDBO();
        $query = 'CREATE TABLE `' . $this->getDBTableName($to) . '` LIKE `' . $this->getDBTableName($from) . '`';
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Add column into existing table
     * 
     * @param string $name column name
     * @param string $type data type
     */
    function addColumn($name, $type)
    {
        switch ($type) {
            case 'textarea':
                $field = 'TEXT';
                break;
            case 'checkbox':
                $field = 'TINYINT(4)';
                break;
            default:
                $field = 'VARCHAR(255)';
                break;
        }
        $db = &JFactory::getDBO();
        $query = 'ALTER TABLE ' . $this->getDBTableName() . ' ADD `' . $name . '` ' . $field . ' NOT NULL';
        
        try {
        	$db->setQuery($query);
        	$db->query();
        } catch (JDatabaseException $e) { //can be added existing field - ignore it
        	ALog::addException($e,JLog::INFO);
        }
    }

    /**
     * Add column index
     * 
     * @param string $name column name
     */
    function addIndex($name)
    {
        $db = &JFactory::getDBO();
        $query = 'ALTER TABLE ' . $this->getDBTableName() . ' ADD INDEX (`' . $name . '`)';
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Drop table column
     * 
     * @param string $name column name
     */
    function dropColumn($name)
    {
        if ($this->tableExist() && $name) {
            $db = &JFactory::getDBO();
            $query = 'ALTER TABLE ' . $this->getDBTableName() . ' DROP `' . $name . '`';
            $db->setQuery($query);
            $db->query();
        }
    }

    /**
     * Update column value
     * 
     * @param string $name column name
     * @param string $oldValue old value
     * @param string $newValue new value
     */
    function updateColumnValue($name, $oldValue, $newValue)
    {
        if ($name && $oldValue && $newValue) {
            $db = &JFactory::getDBO();
            $query = 'UPDATE ' . $this->getDBTableName() . ' SET `' . $name . '` = ' . $db->Quote($newValue) . ' WHERE `' . $name . '` = ' . $db->Quote($oldValue);
            $db->setQuery($query);
            $db->query();
        }
    }

    /**
     * Save template item
     * 
     * @param int $id
     */
    function saveItem($id)
    {
        if (count($this->params)) {
            $db = JFactory::getDBO(); 
            // make SQL manually, with Joomla 2.5 don't work numbered keys
            $columns = array('`id`');
            $inserts = array($id);
           	foreach ($this->params as $param) {
            	/* @var $param ATemplateParam */
           		$val = $db->quote($param->value);
           		
                $columns[] = '`' . $param->name . '`'; 
                $inserts[] = $val; 
                $updates[] = '`' . $param->name .'` = ' . $val;
          	}
          	$query = 'INSERT INTO `' . $this->getDBTableName(). '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $inserts) . ') ';
           	$query .= 'ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);
            $db->setQuery($query);
            return $db->query();
        }
    }

    /**
     * Remove item from template table.
     * 
     * @param int $id item id
     * @param int $template template id
     * @return boolean true if success
     */
    function removeItem($id, $template = null)
    {
        if ($this->tableExist()) {
            $db = &JFactory::getDBO();
            $query = 'DELETE FROM ' . $this->getDBTableName($template) . ' WHERE id ' . (is_array($id) && count($id) ? 'IN (' . implode(',', $id) . ')' : '= ' . (int) $id);
            $db->setQuery($query);
            return $db->query();
        }
        return true;
    }

    /**
     * Delete template: delete database table and XML source file.
     * 
     * @return true if successfull
     */
    function delete()
    {
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        
        $query = 'DROP TABLE IF EXISTS ' . $this->getDBTableName();
        
        $db->setQuery($query);
        $success = $db->query();
        
        return $success;
    }
}

/**
 * Template param (object property). 
 */
class ATemplateParam
{
    
    /**
     * Param name
     * 
     * @var string
     */
    var $name;
    
    /**
     * Pram data type: list,radio or text
     * 
     * @var string
     */
    var $type;
    
    /**
     * Param label
     * 
     * @var string
     */
    var $label;
    
    /**
     * Sign param searchable
     * 
     * @var int
     */
    var $searchable;
    
    /**
     * Sign param is filterable
     * 
     * @var int
     */
    var $filterable;
    
    /**
     * Sign if param is diplayed on object's list
     * 
     * @var int
     */
    var $objects;
    
    /**
     * Sign if param is diplayed on object detail
     * 
     * @var int
     */
    var $object;
    
    /**
     * Icon filename
     * 
     * @var string
     */
    var $icon;
    
    /**
     * Options for list or radio param type
     * 
     * @var array
     */
    var $options;
    
    /**
     * Tmp options memory
     * 
     * @var array
     */
    var $newOptions;
    
    /**
     * Param value
     * 
     * @var mixed
     */
    var $value;
    var $comparison;

    /**
     * Load param attributes
     * 
     * @param SimpleXMLElement $param
     */
    function load(&$param)
    {
    	$object = $param;
    	$param = $param->attributes();

        $this->name = (string)$param['name'];
        $this->type = (string)$param['type'];
        $this->label = (string)$param['label'];
        $this->searchable = (string)$param['searchable'];
        $this->filterable = (string)$param['filterable'];
        $this->objects = (string)$param['objects'];
        $this->object = (string)$param['object'];
        $this->icon = (string)$param['icon'];
        $this->comparison = (string)$param['comparison'];
        $this->loadMulti($object);
    }

    /**
     * Load param multi attributes
     * 
     * @param SimpleXMLElement $param
     */
    function loadMulti(&$param)
    {
        $this->options = array();
        if ($param->option) {
            foreach ($param->option as $option) {
                $this->options[] = (string)$option['value'];
            }
        }
    }

    /**
     * Update by new values from request
     * 
     * @param string $data
     */
    function update($data)
    {
        $parts = explode('|', $data);
        $this->label = isset($parts[0]) ? $parts[0] : '';
        $this->searchable = isset($parts[1]) ? (int) $parts[1] : 0;
        $this->filterable = isset($parts[2]) ? (int) $parts[2] : 0;
        $this->objects = isset($parts[3]) ? (int) $parts[3] : 0;
        $this->object = isset($parts[4]) ? (int) $parts[4] : 0;
        $this->icon = (isset($parts[5]) && $parts[5] != '0') ? $parts[5] : '';
        $this->type = isset($parts[6]) ? $parts[6] : '';
        $this->newOptions = array();
        for ($i = 7; $i < count($parts); $i ++) {
            $parts[$i] = JString::trim($parts[$i]);
            if ($parts[$i]) {
                $this->newOptions[] = JString::trim($parts[$i]);
            }
        }
    }
}

?>