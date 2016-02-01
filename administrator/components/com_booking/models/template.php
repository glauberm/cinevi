<?php

/**
 * Template model. Support for database operations.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  models
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

//import needed JoomLIB helpers
AImporter::helper('template', 'model');

class BookingModelTemplate extends AModel
{
    /**
     * Main table.
     * 
     * @var TableTemplate
     */
    var $_table;

    function __construct()
    {
        parent::__construct();
        $this->_table = &$this->getTable('template');
    }

    /**
     * Store template into XML source file and
     * store template data into database tables.
     * 
     * @param int $id template ID
     * @param boolean $copy template save as copy 
     * @param string $name template name 
     * @param array $data request data
     * @return mixed template ID on success, false on unsuccess
     */
    function store($id, $copy, $name, &$data)
    {
        $templateHelper = &AFactory::getTemplateHelper();
        $template = &$templateHelper->getTemplateById($id);
        
        $template->name = $name;
        
        $fake = null;
        
        $xml = $template->store($fake, $copy);
        
        if ($this->storeTable($data, $template->id, $xml)) {
            $this->storeParams($template);
            return $template->id;
        }
        return false;
    }

    /**
     * Store template params labels and string options into database table
     * to translate string values by JoomFISH.
     * 
     * @param ATemplate $template
     */
    function storeParams(&$template)
    {
        $valueTable = &$this->getTable('template_value');
        /* @var $valueTable TableTemplate_value */
        foreach ($template->params as $param) {
            /* @var $param ATemplateParam */
            $valueTable->store($param->label);
            switch ($param->type) {
            	case 'text':
            	case 'textarea':
            	case 'editor':
            		$valueTable->store($param->value);
            		break;
            }
            foreach ($param->options as $option) {
                $valueTable->store($option);
            }
        }
        $valueTable->store($template->name);
    }

    /**
     * Store template params such default and 
     * available calendars into database table.
     * 
     * @param array $data request data
     * @param int $id template ID
     * @param string $xml XML source code (must be valid XML)
     * @return int template ID
     */
    function storeTable(&$data, $id, $xml)
    {
        $this->_table->id = $id;
        $this->_table->xml = $xml;
        
        $this->_table->bind($data, true);
        $this->_table->store();
        
        return $this->_table->id;
    }

    /**
     * Load full list of availbale templates from database.
     * 
     * @return array
     */
    function loadList()
    {
        $list = array();
        $query = 'SELECT COUNT(`id`) FROM `' . $this->_table->getTableName() . '`';
        $this->_db->setQuery($query);
        $count = (int) $this->_db->loadResult();
        for ($i = 0; $i < $count; $i += 100) {
            $query = 'SELECT * FROM `' . $this->_table->getTableName() . '`';
            $part = &$this->_getList($query, $i, 100);
            if (is_array($part)) {
                $list = &array_merge($list, $part);
            }
        }
        return $list;
    }

    /**
     * Delete template by ID.
     * 
     * @param int $id 
     * @return boolean
     */
    function delete($id)
    {
        $query = 'DELETE FROM `' . $this->_table->getTableName() . '` WHERE `id` = ' . $id;
        
        $this->_db->setQuery($query);
        $success = $this->_db->query();
        
        //delete every label, which isn't in others template xml string
        $idToDoNotDell = false;
        
        //get name of current template
        $templateHelper = &AFactory::getTemplateHelper();
        $template = &$templateHelper->getTemplateById($id);      
        $label = $template->name;
        
        //select all collision id's
        $query = 'SELECT `id` FROM `#__booking_template` WHERE `xml` LIKE \'%="'.$label.'"%\' ';
        $this->_db->setQuery($query);
        $idToDoNotDell = $this->_db->loadAssocList();
         
        //if isn't collision, delete labels
        if(!$idToDoNotDell)
        {
        	$query = "DELETE FROM `#__booking_template_value` WHERE `value` = '".$label."'";
        	$this->_db->setQuery($query);
        	$this->_db->Query();
        }
        
        return $success;
    }
}

?>