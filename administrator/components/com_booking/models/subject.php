<?php

/**
 * Subject model. Support for database operations.
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

//import next model
AImporter::model('occupancytypes', 'reservationtypes', 'prices', 'supplements', 'template');
//import needed JoomLIB helpers
AImporter::helper('template', 'model', 'user');

class BookingModelSubject extends AModel
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
     * Load object by given filter.
     * 
     * @return TableSubject  
     */
    function getObject()
    {
        if (IS_SITE) {
            $where[] = '`access` IN (' . implode(',', AModel::getAccess()) . ')';
            $where[] = '`id` = ' . $this->_id;
            $where[] = '(`publish_up` <= \'' . ($now = AModel::getNow()) . '\' OR `publish_up` = \'' . ($nullDate = AModel::getNullDate()) . '\')';
            $where[] = '(`publish_down` >= \'' . $now . '\' OR `publish_down` = \'0000-00-00 00:00:00\')';
            $where[] = '`state` = ' . SUBJECT_STATE_PUBLISHED;
            $query = 'SELECT * FROM `' . $this->_table->getTableName() . '` WHERE ' . implode(' AND ', $where);
            $this->_db->setQuery($query);
            $data = $this->_db->loadObject();
            if (!empty($data))
            	$this->_table->bind($data);
    		return $this->_table;
    	}
    	return parent::getObject();
    }
    
    /**
     * Load object id and the lowest price.
     *
     * @return stdClass
     */
    function getObjectMinPrice()
    {
    	if (IS_SITE) {
    		//choose the lowest posiible price for subject
    		$query = 'SELECT `a`.`id`, (`a`.`amount` * `a`.`minreservationprice`) as `minprice` FROM (SELECT `s`.`id`,`p`.`id` as `price`,min(`p`.`value`) as `minreservationprice`,`t`.`id` as `type`,
			(CASE WHEN (CASE WHEN `t`.`min` > `t`.`fix` THEN `t`.`min` ELSE `t`.`fix` end) > 0 THEN (CASE WHEN `t`.`min` > `t`.`fix` THEN `t`.`min` ELSE `t`.`fix` end) ELSE 1 END) as `amount`
			FROM `' . $this->_table->getTableName() . '` as `s`
			left join `#__booking_price` as `p` on `p`.`subject` = `s`.`id`
			left join `#__booking_reservation_type` as `t` on `p`.`rezervation_type` = `t`.`id`
			where `s`.`id` = '. (int)$this->_id.'
			group by `t`.`id`) as `a`
			order by `minprice`
			limit 0,1';
    
    		//$query = 'SELECT * FROM `' . $this->_table->getTableName() . '` WHERE ' . implode(' AND ', $where);
    		$this->_db->setQuery($query);
    		$this->_table->bind($this->_db->loadAssoc());
    		return $this->_table;
    	}
    	return parent::getObject();
    }

    /**
     * Publish selected subjects
     * 
     * @param $cids subjects IDs
     * @return boolean success sign
     */
    function publish($cids)
    {
        return $this->state('state', $cids, SUBJECT_STATE_PUBLISHED, SUBJECT_STATE_UNPUBLISHED);
    }
    
    /**
     * Unublish selected subjects
     * 
     * @param $cids subjects IDs
     * @return boolean success sign
     */
    function unpublish($cids)
    {
        return $this->state('state', $cids, SUBJECT_STATE_UNPUBLISHED, SUBJECT_STATE_PUBLISHED);
    }
    
    /**
     * Feature selected subjects
     * @param array $cids
     */
    function feature($cids)
    {
    	return $this->state('featured', $cids, SUBJECT_FEATURED, SUBJECT_NOFEATURED);
    }
    
    /**
     * Unfeature selected subjects
     * @param array $cids
     */
    function unfeature($cids)
    {
    	return $this->state('featured', $cids, SUBJECT_NOFEATURED, SUBJECT_FEATURED);
    }

    /**
     * Archive selected subjects
     * 
     * @param $cids subjects IDs
     * @return boolean success sign
     */
    function archive($cids)
    {
        return $this->state('state', $cids, SUBJECT_STATE_ARCHIVED, SUBJECT_STATE_PUBLISHED, SUBJECT_STATE_UNPUBLISHED);
    }

    /**
     * Unarchive selected subjects
     * 
     * @param $cids subjects IDs
     * @return boolean success sign
     */
    function unarchive($cids)
    {
        return $this->state('state', $cids, SUBJECT_STATE_UNPUBLISHED, SUBJECT_STATE_ARCHIVED);
    }

    /**
     * Trashed selected subjects
     * 
     * @param $cids subjects IDs
     * @return boolean success sign
     */
    function trash($cids)
    {
        return $this->state('state', $cids, SUBJECT_STATE_DELETED, SUBJECT_STATE_PUBLISHED, SUBJECT_STATE_UNPUBLISHED);
    }

    /**
     * Restore selected subjects
     * 
     * @param $cids subjects IDs
     * @return boolean success sign
     */
    function restore($cids)
    {
        return $this->state('state', $cids, SUBJECT_STATE_UNPUBLISHED, SUBJECT_STATE_DELETED);
    }

    /**
     * Delete objects signed as trashed.
     * 
     * @return boolean true if successfull
     */
    function emptyTrash()
    {
        // Load objects to delete
        $query = 'SELECT `id`, `parent`, `template` FROM `' . $this->_table->getTableName() . '` WHERE `state` = ' . SUBJECT_STATE_DELETED;
        $this->_db->setQuery($query);
        $subjectsToDelete = &$this->_db->loadObjectList();
        
        $prices = new BookingModelPrices();
        $reservationTypes = new BookingModelReservationTypes();
        $supplements = new BookingModelSupplements();
        $occupancyType = new BookingModelOccupancyTypes();
        
        // Set to objects childs as parent deleted object parent
        $count = count($subjectsToDelete);
        for ($i = 0; $i < $count; $i ++) {
            $subjectToDelete = &$subjectsToDelete[$i];
            
            $query = 'UPDATE `' . $this->_table->getTableName() . '` SET `parent` = ' . (int) $subjectToDelete->parent;
            $query .= ' WHERE `parent` = ' . (int) $subjectToDelete->id;
            
            $this->_db->setQuery($query);
            $this->_db->query();
            
            //delete prices
            $prices->emptyTrash('subject', $subjectToDelete->id);
            //delete reservation types
            $reservationTypes->emptyTrash('subject', $subjectToDelete->id);
            //delete supplements
            $supplements->emptyTrash('subject', $subjectToDelete->id);
            //delete occupancy types
            $occupancyType->emptyTrash('subject', $subjectToDelete->id);
        }
        
        // Delete objects template data
        $templateHelper = &AFactory::getTemplateHelper();
        $templateHelper->removeItems($subjectsToDelete);
        
        return parent::emptyTrash('state', SUBJECT_STATE_DELETED);
    }

    /**
     * Move item in ordered list
     * 
     * @param int $cid item ID
     * @param int $direction moving direction
     * @return boolean success sign
     */
    function move($cid, $direction)
    {
        if ($this->_table->load($cid)) {
            $this->_table->move($direction, ' parent = ' . (int) $this->_table->parent . ' AND state IN (' . SUBJECT_STATE_PUBLISHED . ',' . SUBJECT_STATE_UNPUBLISHED . ')');
        } else {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        return true;
    }

    /**
     * Set items ordering
     * 
     * @param int $cids items IDs
     * @param int $order order values
     * @return boolean success sign
     */
    function saveorder($cids, $order)
    {
        $branches = array();
        for ($i = 0; $i < count($cids); $i ++) {
            $this->_table->load((int) $cids[$i]);
            $branches[] = $this->_table->parent;
            if ($this->_table->ordering != $order[$i]) {
                $this->_table->ordering = $order[$i];
                if (! $this->_table->store()) {
                    $this->setError($this->_db->getErrorMsg());
                    return false;
                }
            }
        }
        $branches = array_unique($branches);
        foreach ($branches as $group) {
            $this->_table->reorder('parent = ' . (int) $group);
        }
        return true;
    }

    /**
     * Set item access
     * 
     * @param int $cid item ID
     * @param int $access access values
     * @return boolean success sign
     */
    function setAccess($cid, $access)
    {
        if ($this->_table->load($cid)) {
            $this->_table->access = $access;
            if (! $this->_table->store()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
            }
        } else {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        return true;
    }

    /**
     * Save subject and template.
     * 
     * @param array $data request data
     * @return mixed subject id if success, false in unsuccess
     */
    function store($data)
    {
        
        $query = 'SELECT `id` FROM `' . $this->_table->getTableName() . '`';
        $this->_db->setQuery($query);
        $ids = $this->_db->loadAssocList('id','id');
        $count = count($ids);
        $id = $data['id'];
        if ($count >= 2 && (($id != '0' && ! in_array($id, $ids)) || $id == '0')) { 
            return - 1;
        }
        
    	
        $id = (int) $data['id'];
        
        if ($id) {
            // Load old object data before saving new
            $this->_table->load($id);
            // Safe old object template
            $oldTemplate = $this->_table->template;
            // Safe object old parent
            $oldParent = $this->_table->parent;
            // Safe old parent of object new parent
            $parentOldParent = $this->getRoot($data['parent']);
        }
        else{
        	//new subject
        	$owner = JFactory::getUser();
        	$data['user_id'] = $owner->id;
        }
        
        if (! isset($data['images'])) {
            $data['images'] = array();
        }
        
        if (! isset($data['files'])) {
            $data['files'] = array();
        }
        if (! $this->_table->bind($data, true)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        
        BookingHelper::setSubjectImages($this->_table->images);
        BookingHelper::setSubjectFiles($this->_table->files);
         
        if (! $this->_table->id) {
            $where = 'parent = ' . (int) $this->_table->parent;
            $this->_table->ordering = $this->_table->getNextOrder($where);
        }
        
        if (! $this->_table->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        
        if (! $this->_table->store()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        
        $templateTask = $data['templateTask'];
        
        if ($templateTask == 'saveAsNew') {
            $this->_table->template = 0;
        }
        
        $resave = ($this->_table->template == 0);
        
        $templateHelper = &AFactory::getTemplateHelper();
        
        $template = &$templateHelper->getTemplateById($this->_table->template);
        
        switch ($templateTask) {
            case 'rename':
                $template->name = $data['template_rename'];
                break;
            case 'saveAsNew':
                $template->name = $data['new_template_name'];
                break;
        }
        $xml = $template->store($this->_table);
        
        if ($resave) {
            $this->_table->store();
        }
        
        // Object have new template. Remove old saved data.
        if ($id && $oldTemplate && $this->_table->template != $oldTemplate) {
            $template->removeItem($id, $oldTemplate);
        }
        
        // save objects occupancy types
        $omodel = new BookingModelOccupancyTypes();
        $omodel->store($this->_table->id, $data);
        
        // Save objects reservation types
        $rmodel = new BookingModelReservationTypes();
        $rmodel->store($this->_table->id, $data);
        
        // Save objects prices
        $pmodel = new BookingModelPrices();
        $pmodel->store($this->_table->id, $data);
        
        // Save objects supplements
        $smodel = new BookingModelSupplements();
        $smodel->store($this->_table->id, $data);
        
        // Save objects templates
        $tmodel = new BookingModelTemplate();
        if(!AUser::onlyOwner())
        	$tmodel->storeTable($data, $template->id, $xml);
        $tmodel->storeParams($template);
        
        // Subject was root and user set him as child and subject was moved in own tree.
        if ($id && $oldParent == 0 && $this->_table->parent > 0 && $parentOldParent == $id) {
            // Set all object childs as root
            $query = 'UPDATE `' . $this->_table->getTableName() . '` SET `parent` = 0 WHERE `parent` = ' . $id;
            $this->_db->setQuery($query);
            $this->_db->query();
        }
        
        // Save rules
        $rules = $data['jform']['rules'];
       	foreach ($rules as $rule => $usergroups) // process usergroups
       		foreach ($usergroups as $usergroup => $allowed) // process rules in usergroup
       			if ($allowed === '') // inherited or not set
       				unset($rules[$rule][$usergroup]); 
       	
       	$rules = new JAccessRules($rules); // generate string format from array
       	$asset = JTable::getInstance('asset');
       	/* @var $asset JTableAsset */
        
       	$asset->loadByName('com_booking.subject.'.$this->_table->id);
       	
       	$root = JTable::getInstance('asset'); // search ACL parent
       	/* @var $root JTableAsset */
       	
       	if ($this->_table->parent) // for child subject, parent subject will be ACL parent
       		$success = $root->loadByName('com_booking.subject.'.$this->_table->parent);
       	if (! $this->_table->parent || ! $success) // for root subject, global configuration will be ACL parent
       		$root->loadByName('com_booking');
       	
       	$asset->name = 'com_booking.subject.'.$this->_table->id;
       	$asset->title = $this->_table->title;
       	
       	$asset->setLocation($root->id, 'last-child'); // set as child of ACL parent
       	
       	$asset->rules = (string) $rules;
        
       	if (!$asset->check() || !$asset->store()) {
       		$this->setError($asset->getError());
       		return false;
       	}
       	
       	JFactory::getCache('com_booking_acl', '')->clean();
        
        return $this->_table->id;
    }

    /**
     * Get root parent of tree where is object by given ID member.
     * 
     * @param int $id object ID
     * @return int root parent ID
     */
    function getRoot($id)
    {
        $query = 'SELECT `id`, `parent` FROM `' . $this->_table->getTableName() . '` WHERE `id` = ' . $id;
        $this->_db->setQuery($query);
        $object = &$this->_db->loadObject();
        if (is_object($object) && $object->parent != 0)
            return $this->getRoot($object->parent);
        return $id;
    }

    /**
     * Change subject template.
     * 
     * @param int $id subject id
     * @param int $template template id
     * @return int 1 ... success, 0 ... subject already have this template, -1 ... unable to change
     */
    function changeTemplate($id, $template)
    {
        $template = (int) $template;
        $oldTemplate = $this->loadTemplate($id);
        if (is_null($oldTemplate)) {
            return - 1;
        } elseif ($oldTemplate == $template) {
            return 0;
        } else {
            $templateHelper = new ATemplateHelper();
            $templateObject = $templateHelper->getTemplateById($oldTemplate);
            if ($templateObject->removeItem($id, $oldTemplate)) {
                $query = 'UPDATE ' . $this->_table->getTableName() . ' SET template = ' . $template . ' WHERE id = ' . $id;
                $this->_db->setQuery($query);
                if ($this->_db->query()) {
                    return 1;
                }
            }
        }
        return - 1;
    }

    /**
     * Delete template. Set for all subject whoch have this template ID template to null.
     * 
     * @param int $id
     * @return boolean true if success
     */
    function deleteTemplate($id)
    {
        $currentTemplate = $this->loadTemplate($id);
        $templateHelper = new ATemplateHelper();
        $templateObject = $templateHelper->getTemplateById($currentTemplate);
        if ($templateObject->delete()) {
            $query = 'UPDATE ' . $this->_table->getTableName() . ' SET template = 0 WHERE template = ' . $currentTemplate;
            $this->_db->setQuery($query);
            return $this->_db->query();
        }
        return false;
    }

    /**
     * Load subject template by subject ID.
     * 
     * @param int $id subject ID
     * @return int template ID
     */
    function loadTemplate($id)
    {
        $id = (int) $id;
        $query = 'SELECT template FROM ' . $this->_table->getTableName() . ' WHERE id = ' . (int) $id;
        $this->_db->setQuery($query);
        return (int) $this->_db->loadResult();
    }

    /**
     * Return true if subject have childs subjects.
     * 
     * @param int $id subject ID
     * @return boolean
     */
    function haveChilds($id)
    {
        static $cache;
        if (is_null($cache)) {
            $query = 'SELECT `parent`, COUNT(`id`) FROM `' . $this->_table->getTableName() . '` GROUP BY `parent`';
            $this->_db->setQuery($query);
            $parents = &$this->_db->loadRowList();
            $cache = array();
            $count = count($parents);
            for ($i = 0; $i < $count; $i ++) {
                $parent = &$parents[$i];
                $cache[(int) $parent[0]] = (int) $parent[1];
            }
        }
        $haveChilds = isset($cache[$id]) && $cache[$id] > 0;
        return $haveChilds;
    }
    
    function getChildren()
    {
    	$query = $this->getDbo()->getQuery(true)->select('id')->from('#__booking_subject')->where('parent = ' . JRequest::getInt('id', ARequest::getCid()));
    	$cid = $this->getDbo()->setQuery($query)->loadColumn();
    	
    	$children = array();
    	
    	foreach ($cid as $id) {
    		$item = $this->getTable('subject');
    		$item->load($id);
    		/* @var $item TableSubject */

    		TableSubject::prepare($item);
    		$children[] = $item;
    		
    		$query = $this->getDbo()->getQuery(true)->select('*')->from('#__booking_supplement')->where('subject = ' . $id);
    		$item->supplements = $this->getDbo()->setQuery($query)->loadObjectList();
    		
    		$query = $this->getDbo()->getQuery(true)->select('id')->from('#__booking_reservation_type')->where('subject = ' . $id);
    		$item->rids = $this->getDbo()->setQuery($query)->loadColumn();
    	}
    	return $children;
    }       

    function incrementHits($id)
    {
        $query = 'UPDATE ' . $this->_table->getTableName() . ' SET `hits` = `hits` + 1 WHERE `id` = ' . (int) $id;
        $this->_db->setQuery($query);
        $this->_db->query();
    }
    
    function copy($subjectIds) 
    {
    	
    	$query = $this->getDbo()->getQuery(true)->select('COUNT(*)')->from('#__booking_subject');
    	if ($this->getDbo()->setQuery($query)->loadResult() > 1) {
    		JFactory::getApplication()->enqueueMessage(JText::_('THIS_IS_FREE_VERSION_WITH_OBJECTS_COUNT_LIMITED_TO_2'), 'notice');
    		return false;
    	}
    	
    	
    	$subject = $this->getTable('subject');
    	/* @var $subject TableSubject */
    	$rtype = $this->getTable('reservationtype');
    	/* @var $rtype TableReservationType */
    	$price = $this->getTable('price');
    	/* @var $price TablePrice */
    	$supplement = $this->getTable('supplement');
    	/* @var $supplement TableSupplement */
    	foreach ($subjectIds as $subjectId) {
    		$subject->load($subjectId);
    		$subject->id = 0; 
    		$subject->title .= ' ' . JText::_('COPY_MARK');
    		$subject->store();
    		$newSubjectId = $this->_db->insertid();
   			$this->_db->setQuery('SELECT `id` FROM `' . $rtype->getTableName() . '` WHERE `subject` = ' . $subjectId . ' ORDER BY `id` ASC');
   			foreach ($this->_db->loadAssocList('id','id') as $rtypeId) {
    			$rtype->load($rtypeId);
    			$rtype->id = 0;
   				$rtype->subject = $newSubjectId;
   				$rtype->store();
   				$newRtypeId = $this->_db->insertid();
    			$this->_db->setQuery('SELECT `id` FROM `' . $price->getTableName() . '` WHERE `rezervation_type` = ' . $rtypeId . ' ORDER BY `id` ASC');
    			if(is_array($list = $this->_db->loadAssocList('id','id')))
    				foreach ($list as $priceId) {
	    				$price->load($priceId);
	    				$price->id = 0;
	   					$price->subject = $newSubjectId;
	    				$price->rezervation_type = $newRtypeId;
	    				$price->store();
    				}
    		}
    		$this->_db->setQuery('SELECT `id` FROM `' . $supplement->getTableName() . '` WHERE `subject` = ' . $subjectId . ' ORDER BY `id` ASC');
    		foreach ($this->_db->loadAssocList('id','id') as $supplementId) {
    			$supplement->load($supplementId);
    			$supplement->id = 0;
    			$supplement->subject = $newSubjectId;
    			$supplement->store();
    		}
    	}
    	
    	return true;
    }
    
    public function getRTypes()
    {
    	if (empty($this->_rtypes)) {
    		$this->_db->setQuery('SELECT * FROM #__booking_reservation_type WHERE subject = ' . JRequest::getInt('id'));
    		$this->_rtypes = $this->_db->loadObjectList('id');
    		$this->_db->setQuery('SELECT * FROM #__booking_price WHERE subject = ' . JRequest::getInt('id'));
    		foreach ($this->_db->loadObjectList() as $price)
    			if (isset($this->_rtypes[$price->rezervation_type]))
					$this->_rtypes[$price->rezervation_type]->prices[] = $price;
    	}
    	return $this->_rtypes;    	
    }
    
    public function getRules()
    {
    	// prevent for wrong value of assets parent  
    	$db = $this->getDbo();
    	$query = $db->getQuery(true)->update('#__assets')->set('parent_id = 1')->where('name LIKE ' . $db->quote('%booking%'))->where('parent_id = 0');    	
    	$db->setQuery($query)->query();
    	
    	JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR.'/models/forms'); // set destination directory of xml maniest
    	$form = JForm::getInstance('com_booking.subject.rules', 'subject', array('control' => 'jform', 'load_data' => true)); // load xml manifest
    	/* @var $form JForm */
    	$query = $this->getDbo()->getQuery(true)->select('id')->from('#__assets')->where("name = 'com_booking.subject.".ARequest::getCid()."'"); // looking for id of row with rules for this subject
    	$this->getDbo()->setQuery($query);
    	$form->setValue('asset_id', null, $this->getDbo()->loadResult());
    	return $form->getInput('rules');
    }
        
    public function getGoogleCalendarList()
    {
    	$query = $this->getDbo()->getQuery(true);
    	
    	$query->select('id, title')
    	      ->from('#__booking_google_calendar')
    	      ->order('title');
    	
    	return $this->getDbo()->setQuery($query)->loadObjectList();
    }
    
    function suggest($request)
    {
    	$this->_db->setQuery('SELECT DISTINCT title FROM #__booking_subject WHERE LOWER(title) LIKE ' . $this->_db->quote('%' . JString::strtolower($request) . '%'));
    	return $this->_db->loadColumn();
    }    
    
    /**
     * Get full list of Joomla user groups.
     * @return array
     */
    public function getUserGroups() 
    {
        if (empty($this->userGroups)) {
            JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models');
            $this->userGroups = JModelLegacy::getInstance('Groups', 'UsersModel')->getItems();
        }
        return $this->userGroups;
    }
    
    /**
     * Get user groups which are allowed to manage reservations of currently managed bookable item
     * @return array
     */
    public function getAgents()
    {        
        $action = 'booking.reservations.manage';
        $root = 'com_booking';
        $item = 'com_booking.subject.' . ARequest::getCid();
        $agents = $this->getUserGroups();
        foreach ($agents as $k => $agent) {
            if (!(JAccess::checkGroup($agent->id, $action, $item) || JAccess::checkGroup($agent->id, $action, $root))) {
                unset ($agents[$k]);
            }
        }
        return $agents;                   
    }    
    
    public function getNearestBooking($id = null, $year = null, $month = null)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->from('#__booking_price AS p')->order('date_up');
        if ($id) {
            $query->select('date_up')->where('subject = '. (int) $id);
        } else {
            if ($id === null) {
                $query->select('subject');
            } elseif($id === false) {
                $query->select('date_up');                
            }
            $query->leftJoin('#__booking_subject AS s ON s.id = p.subject');
            $query->where('state = ' . SUBJECT_STATE_PUBLISHED);
            if ($year && $month) {
                $query->where('date_up < ' . $db->q($year . '-' . $month . '-32'))->where('date_down > ' . $db->q($year . '-' . $month . '-00'));
            }
        }
        
        return $db->setQuery($query)->loadResult();
    }
}

?>
