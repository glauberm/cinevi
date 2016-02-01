<?php

/**
 * Subjects list model. Support for loading database data with apply filter.
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

//import needed Joomla! libraries
jimport('joomla.application.component.model');

//import needed JoomLIB helpers
AImporter::helper('image', 'model', 'request', 'tree', 'user', 'utils');

class BookingModelSubjects extends AModel
{
    
    /**
     * Main table
     * 
     * @var TableSubject
     */
    var $_table;

    function __construct()
    {
        parent::__construct();
        $this->_table = $this->getTable('subject');
    }

    /**
     * Get complet list data
     * 
     * @return array subjects objects
     */
    function getData($forceAdmin = false)
    {
        if (empty($this->_data)) {
            if (IS_ADMIN || $forceAdmin) {
                $fullList = $this->getFullList();
                $filterList = $this->getFilterList($forceAdmin);                
                            
				$this->_data = ATree::getListTree($fullList, $filterList, 0, 2);
				
            } elseif (IS_SITE) {
                $config = &AFactory::getConfig();
                /* @var $config BookingConfig */
                $query = $this->buildSimpleQuery();
                                
                $this->_data = $this->_getList($query, 0, 2);
                
                if (!empty($this->_lists['date_from']) && !empty($this->_lists['date_to'])) { // check if an item is book-able in required date
                    $items = array();
                    for (($i = $config->displayPagination ? $this->getState('limitstart') : 0); $i < count($this->_data); $i ++) {
                        $this->_table->load($this->_data[$i]->id);
                        $days = BookingHelper::getCalendar($this->_table, $this->_lists['date_from'], $this->_lists['date_to']);
                        foreach ($days->calendar as $day) {
                            /* @var $day BookingDay */
                            if ($day->engaged || !empty(reset($day->boxes)->closed)) {
                                continue 2; // an item is no available
                            }
                        }
                        $items[] = $this->_data[$i];
                        if ($config->displayPagination && count($items) == $this->getState('limit')) { // check for the current page only
                            break;
                        }
                    }
                    $this->_data = $items; // filtered item list
                }
            }
        }
        
        return $this->_data;
    }

    /**
     * Get total items count
     * 
     * @return int total count limited by filter
     */
    function getTotal()
    {
            
    	return 2;
    	
    }

    /**
     * Get full subjects list without apply filter
     * 
     * @return array subjects objects
     */
    function getFullList()
    {
        $query = $this->buildQuery(false);
            
        return $this->_getList($query, 0, 2);
        
    }

    /**
     * Get filtered subject list
     * 
     * @return array subjects objects 
     */
    function getFilterList($forceAdmin = false)
    {
        $query = $this->buildQuery(true, $forceAdmin);
            
        return $this->_getList($query, 0, 2);
        
    }

    /**
     * Get MySQL loading query for subjects list.
     * 
     * @return string complet MySQL query
     */
    function buildQuery($filter = true, $forceAdmin = false)
    {
        if ($filter) {
            $query = 'SELECT `subject`.`id` FROM `' . $this->_table->getTableName() . '` AS `subject` ';
            $query .= $this->buildContentWhere($forceAdmin);
        } else {
            $query = 'SELECT `subject`.*,`usergroup`.`' . (ISJ16 ? 'title' : 'name') . '` AS `groupname`, `editor`.`name` AS `editor` ';
            $query .= 'FROM `' . $this->_table->getTableName() . '` AS `subject` ';
            if (ISJ16)
                $query .= 'LEFT JOIN `#__viewlevels` AS `usergroup` ON `usergroup`.`id` = `subject`.`access` ';
            else
                $query .= 'LEFT JOIN `#__groups` AS `usergroup` ON `usergroup`.`id` = `subject`.`access` ';
            $query .= 'LEFT JOIN `#__users` AS `editor` ON `subject`.`checked_out` = `editor`.`id` ';
            $query .= $this->buildContentOrderBy();
        }
        return $query;
    }

    /**
     * Get query to load only currently published subjects.
     * 
     * @return string
     */
    function buildSimpleQuery()
    {
    	
        $config = AFactory::getConfig();
        $templateHelper = AFactory::getTemplateHelper();
        /* @var $templateHelper ATemplateHelper */

        $query = 'SELECT s.id, s.title, s.alias, s.featured, s.template, s.introtext, s.params, COUNT(k.id) AS children, s.total_capacity ';           
        $query .= 'FROM #__booking_subject AS s ';
        $query .= 'LEFT JOIN #__booking_subject AS k ON k.parent = s.id '; // check if item has children
           	
        if (!empty($this->_lists['price_from']) || !empty($this->_lists['price_to'])) { // search in required price range 
          	$query .= 'LEFT JOIN #__booking_price AS p ON p.subject = s.id ';	
        }
			
        if (!empty($this->_lists['properties'])) {
	        // filter by template properties
            foreach ($this->_lists['properties'] as $templateId => $properties) {
                $template = $templateHelper->getTemplateById($templateId);
                /* @var $template ATemplate */
                $id = 't' . $templateId; // alias of template database table
                foreach ($properties as $name => $param) { // check if properties has value to filter
                  	$value = JString::trim($param['value']);
                    if ($value) {
                        if ($param['type'] == 'text' || $param['type'] == 'textarea') // fulltext search
                            $where[] = $id . '.' . $name . ' LIKE ' . $this->_db->q('%' . $value . '%');
                        elseif ($param['type'] == 'list') {                                
                            $where[] = $id . '.' . $name . ' ' . JArrayHelper::getValue(AUtils::getCmpTypes(), $param['comparison'], '=') . ' ' . $this->_db->Quote($value);
                        } else {
                            $where[] = $id . '.' . $name . ' = ' . $this->_db->q($value);
                        }                            
                    }
                }
                if (!empty($where)) { // there is params to filter - join with template
                    $query .= ' LEFT JOIN ' . $template->getDBTableName() . ' AS ' . $id . ' ON ' . $id . '.id = s.id ';
                }
            }
        }
            
        $where[] = 's.state = ' . SUBJECT_STATE_PUBLISHED; // always only published items
        $where[] = 's.access IN (' . implode(',', $this->_lists['access']) . ')'; // logged user ACL
            
        if ($config->listStyle == 0) {
          	if (!empty($this->_lists['parent'])) { // search in parent branch
           		$where[] = 's.parent = ' . (int) $this->_lists['parent'];
            } else { // search in all bookable objects - ignore no-bookable parents
          		$where[] = 'k.id IS NULL'; // hasn't children
            }
        }	
            
		$UTC = $this->_db->q(AModel::getNow());            	
		$NULL = $this->_db->q(AModel::getNullDate());
            	
        $where[] = '(s.publish_up <= ' . $UTC . ' OR s.publish_up = ' . $NULL . ')';
        $where[] = '(s.publish_down >= ' . $UTC . ' OR s.publish_down = ' . $NULL . ')'; // always only published items
            
        if (!empty($this->_lists['template_area'])) { // search in specific template
          	$where[] = 's.template = ' . (int) $this->_lists['template_area']; // template shoud be positive integer
        }

        // search for price covered required price range
        if (!empty($this->_lists['price_from'])) {
          	$where[] = 'p.value >= ' . (float) $this->_lists['price_from'];
        }
        
        if (!empty($this->_lists['price_to'])) {
          	$where[] = 'p.value <= ' . (float) $this->_lists['price_to'];
        }
            	
        if (!empty($this->_lists['required_capacity'])) {
           	$where[] = 's.total_capacity >= ' . (int) $this->_lists['required_capacity']; // search for required capacity
        }
        
        if (!empty($this->_lists['featured'])) { // show featured items only
          	$where[] = 's.featured = ' . SUBJECT_FEATURED;
        }
        
        if (!empty($this->_lists['category'])) { // show required item category only
          	$where[] = 's.parent = ' . (int) $this->_lists['category'];
        }
            	
        $query .= ' WHERE ' . implode(' AND ', $where);
        $query .= ' GROUP BY s.id '; // prevent for duplicities provided by joins
            
        if (!empty($this->_lists['order_by'])) {
          	$query .= ' ORDER BY ' . $this->_db->quoteName($this->_lists['order_by']);
        } else {
          	$query .= ' ORDER BY s.featured DESC, s.ordering ASC ';
        }
        
        return $query;
    }

    /**
     * Get templates of all subjects with same parent a available on frontend.
     * 
     * @return array
     */
    function getAvailableTemplates()
    {
    	if ($this->_lists['parent'] !== '') {
        	$where[] = '`state` = ' . SUBJECT_STATE_PUBLISHED;
        	$where[] = '`access` IN (' . implode(',', $this->_lists['access']) . ')';
        	if ($this->_lists['parent'] !== null)
        		$where[] = '`parent` = ' . (int) $this->_lists['parent'];
        	$where[] = '(`publish_up` <= \'' . ($now = AModel::getNow()) . '\' OR `publish_up` = \'' . ($nullDate = AModel::getNullDate()) . '\')';
        	$where[] = '(`publish_down` >= \'' . $now . '\' OR `publish_down` = \'' . $nullDate . '\')';
        	$this->_db->setQuery('SELECT DISTINCT `template` FROM `' . $this->_table->getTableName() . '` WHERE ' . implode(' AND ', $where));
        	return $this->_db->loadAssocList('template','template');
    	}
    	return array();
    }

    
    /**
     * Get MySQL filter criteria for subjects list
     * 
     * @return string filter criteria in MySQL format
     */
    function buildContentWhere($forceAdmin = false)
    {
        $search = isset($this->_lists['search']) ? JString::trim($this->_lists['search']) : '';
        $parent = isset($this->_lists['parent']) ? (int) $this->_lists['parent'] : 0;
        $state = isset($this->_lists['state']) ? $this->_lists['state'] : '';
        $template = isset($this->_lists['template']) ? (int) $this->_lists['template'] : 0;
        $featured = isset($this->_lists['featured']) ? $this->_lists['featured'] : '';
        if ($search)
            $where[] = 'LOWER(`subject`.`title`) LIKE ' . $this->_db->Quote('%' . JString::strtolower($search) . '%');
        if ($parent) {
        	$childs = array();
            $this->loadChilds($parent, $childs);
            $childs[] = $parent;
            $where[] = '`subject`.`id` IN (' . implode(',', $childs) . ')';
        }
        if ($state !== '')
            $where[] = '`state` = ' . (int) $state;
        if ($template)
            $where[] = '`template` = ' . $template;
        if ($featured !== '')
        	$where[] = '`featured` = ' . (int) $featured;
        
        //ACL - user can see only own objects
        AImporter::helper('user');
        if($id = AUser::onlyOwner() && !$forceAdmin)
        	$where[] = '`subject`.`user_id` = '.$id;
        
        return isset($where) ? ' WHERE ' . implode(' AND ', $where) : '';
    }

    /**
     * Load parents subjects Ids
     * 
     * @return array unique IDs
     */
    function loadParents()
    {
        $query = 'SELECT DISTINCT `parent` FROM ' . $this->_table->getTableName() . ' WHERE `parent` <> 0';
                
        $this->_db->setQuery($query, 0, 2);
        
        return $this->_db->loadAssocList('parent','parent');
    }

    /**
     * Load short list (only ID,title and parent ID) by subjects IDs
     * 
     * @param array $ids subjects IDs if null load all subjects
     * @return array stdClass objects contains subjects data
     */
    function loadShortListByIds($ids = null, $ignore = null)
    {
        $where = array();
        if (is_array($ids) && count($ids)) {
            $where[] = '`id` IN (' . implode(',', $ids) . ')';
        }
        if (is_array($ignore) && count($ignore)) {
            $where[] = '`id` NOT IN (' . implode(',', $ignore) . ')';
        }
        if ($id = AUser::onlyOwner())
        	$where[] = '`user_id` = '.$id;
        
        $where = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';
        $query = 'SELECT `id`, `title`, `parent` FROM `' . $this->_table->getTableName() . '`' . $where;
                
        $this->_db->setQuery($query, 0, 2);
        
        return $this->_db->loadObjectList();
    }

    /**
     * Load IDs of parents childs recursive into deepth
     * 
     * @param int $parent parent ID
     * @param array $result array to saving IDs
     */
    function loadChilds($parent, &$result)
    {
        static $cache;
        static $list;
        if (is_null($list)) {
            $this->_db->setQuery('SELECT `id`,`parent` FROM `' . $this->_table->getTableName() . '` WHERE `parent` <> 0');
            $rows = &$this->_db->loadRowList();
            $list = array();
            $count = count($rows);
            for ($i = 0; $i < $count; $i ++) {
                $row = &$rows[$i];
                $list[(int) $row[0]] = (int) $row[1];
            }
            unset($rows);
        }
        if (! isset($cache[$parent])) {
            $childs = array();
            $parents = array($parent);
            while (true) {
                $count = count($parents);
                $nextParents = array();
                for ($i = 0; $i < $count; $i ++) {
                    $nextParents = array_merge($nextParents, array_keys($list, $parents[$i]));
                }
                if (count($nextParents)) {
                    $childs = array_merge($childs, $nextParents);
                    $parents = $nextParents;
                } else
                    break;
            }
            $cache[$parent] = $childs;
        }
        $result = $cache[$parent];
    }

    function loadSubjectParentsLine($id, $result = array())
    {
        $tableName = '`' . $this->_table->getTableName() . '`';
        $query = 'SELECT `parent`.`id`, `parent`.`title`, `parent`.`alias` ';
        $query .= 'FROM ' . $tableName . ' AS `child` ';
        $query .= 'LEFT JOIN ' . $tableName . ' AS `parent` ON `child`.`parent` = `parent`.`id` ';
        $query .= 'WHERE `child`.`id` = ' . (int) $id;
                
        $this->_db->setQuery($query, 0, 2);
        
        $parent = $this->_db->loadObject();
        if ($parent && $parent->id) {
            $result[] = $parent;
            return $this->loadSubjectParentsLine($parent->id, $result);
        }
        return $result;
    }

    /**
     * Get query to loading subject ordering branch by parent
     * 
     * @return string complet MySQL query 
     */
    function getLoadOrderingQuery($subject)
    {
        return 'SELECT `ordering` AS `value`, `title` AS `text` FROM `' . $this->_table->getTableName() . '` WHERE `parent` = ' . (int) $subject->parent . ' ORDER BY `ordering`';
    }
}