<?php

/**
 * Searching in published Booking objects and their templates properties values.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  plugins/search
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

$mainframe = &JFactory::getApplication();
$mainframe->registerEvent('onSearch', 'plgSearchBooking');
$mainframe->registerEvent('onSearchAreas', 'plgSearchBookingAreas');
/*
JPlugin::loadLanguage('plg_search_booking'); //fatal error in J16
*/
$language =& JFactory::getLanguage();
$language->load('plg_search_booking',JPATH_ADMINISTRATOR);

/**
 * For J!1.6
 */
class plgSearchBooking extends JPlugin
{
	function onContentSearchAreas()
	{
		return plgSearchBookingAreas();
	}
	
	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		return  plgSearchBooking($text, $phrase, $ordering, $areas);
	}
}

function plgSearchBookingAreas()
{
    return array('booking' => 'Booking');
}

function plgSearchBooking($text, $phrase = '', $ordering = '', $areas = null)
{
    // Test if searching is possible
    if (! ($text == JString::trim($text)))
        return array();
    
    if (is_array($areas) && ! array_intersect($areas, array_keys(plgSearchBookingAreas())))
        return array();
    
        $helpers = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booking' . DS . 'helpers' . DS;

     // Find Bookit importer    
    if (file_exists(($importer = ($helpers . 'importer.php'))))
        include_once ($importer);
    else
        return array();
    
    include_once ($helpers . 'html.php');
    include_once ($helpers . 'model.php');
    
     // Import needed Bookit framework    
    AImporter::defines();
    AImporter::helper('booking', 'installer', 'model', 'route', 'template');
    AImporter::model('subject');
    AImporter::table('template');
    AInstallerJoomFish::init();

    // Take needed Joomla! framework objects
    $mainframe = &JFactory::getApplication();
    /* @var $mainframe JApplication */
    $db = &JFactory::getDBO();
    /* @var $db JDatabaseMySQL */
    $user = &JFactory::getUser();
    /* @var $user JUser */
    $now = &JFactory::getDate();
    /* @var $now JDate current date */
    

    // Take current time
    $now = $db->Quote($now->toSQL());
    $nullDate = $db->Quote($db->getNullDate());
    
	$searchables = array();
    
    // Take searchable template params
    $templateHelper = &AFactory::getTemplateHelper();
    /* @var $templateHelper ATemplateHelper */
    foreach ($templateHelper->_templates as $template) {
        /* @var $template ATemplate */
        $properties = new AParameter('', null, $template->parser);
        /* @var $properties AParamter all template properties */
        $table = $template->getDBTableName();
        /* @var $table string template database table name */
        foreach ($properties->loadParamsToFields() as $param)
        	/* @var $param template parameter */
            if ($param[PARAM_SEARCHABLES] == 1) {
                $searchables[$table][] = $param;
                /* @var $searchables array searchables paramteters */
            }
    }
    
    $operator = $phrase == 'all' ? 'AND' : 'OR';
    /* @var $operator SQL query operator */
    
    // Prerare search keywords
    $keyword = plgSearchBookingKeywords($text);
    $keywords = plgSearchBookingKeywords($text, true);
    
    $jfActive = BookingHelper::joomFishIsActive();
    
    // Take keywords originals from JoomFISH translation
    if ($jfActive) {
        
        // Take current language ID
        $language = &JFactory::getLanguage();
        /* @var $language JLanguage */
        $db->setQuery('SELECT `id` FROM `#__languages` WHERE `code` = ' . $db->Quote($language->getTag()));
        
        // Take keywords originals
        $query = 'SELECT `val`.`value` FROM `#__booking_template_value` AS `val` ';
        $query .= 'LEFT JOIN `#__booking_template_value_view` AS `view` ON `val`.`id` = `view`.`id` ';
        $query .= 'WHERE `view`.`language` = ' . ($languageID = (int) $db->loadResult());
        
        if ($phrase == 'exact') {
            $db->setQuery($query . ' AND LOWER(`view`.`value`) LIKE ' . reset($keyword));
            $keyword = array_merge($keyword, plgSearchBookingKeywords($db->loadAssocList()));
        } else
            foreach ($keywords as $i => $word) {
                $db->setQuery($query . ' AND LOWER(`view`.`value`) LIKE ' . reset($word));
                $keywords[$i] = array_merge($word, plgSearchBookingKeywords($db->loadAssocList(), true, false));
            }
    }
    
    if ($jfActive)
        $query = 'SELECT `sbj`.`id`, COALESCE(`jfTitle`.`value`, `sbj`.`title`) AS `title`, `sbj`.`alias`, `sbj`.`template`, CONCAT(COALESCE(`jfIntrotext`.`value`, `sbj`.`introtext`)," ", COALESCE(`jfFulltext`.`value`, `sbj`.`fulltext`)) AS `text` ';
    else
        $query = 'SELECT `sbj`.`id`, `sbj`.`title`, `sbj`.`alias`, `sbj`.`template`, CONCAT(`sbj`.`introtext`, `sbj`.`fulltext`) AS `text` ';
    
    $query .= 'FROM `#__booking_subject` AS `sbj` ';
    
    $keywordWheres[] = '`sbj`.`title`';
    /* @var $keywordWheres string search SQL criteria to filter by given keywords */
    $keywordWheres[] = '`sbj`.`introtext`';
    $keywordWheres[] = '`sbj`.`fulltext`';
    
    if ($jfActive) {
        $keywordWheres[] = '`jfTitle`.`value`';
        $keywordWheres[] = '`jfIntrotext`.`value`';
        $keywordWheres[] = '`jfFulltext`.`value`';
    }
    
    $i = 0;
    
    foreach ($searchables as $table => $params) {
        $tid = '`tmpl' . ++ $i . '`';
        /* @var $tid string database table alias */
        foreach ($params as $param)
            $keywordWheres[] = $tid . '.`' . $param[PARAM_NAME] . '`';
        $query .= ' LEFT JOIN `' . $table . '` AS ' . $tid . ' ON ' . $tid . '.`id` = `sbj`.`id` ';
    }
    
    if ($jfActive) {
        $query .= ' LEFT JOIN `#__jf_content` AS `jfTitle` ON `jfTitle`.`language_id` = ' . $languageID . ' AND `jfTitle`.`reference_table`  = "booking_subject" AND `jfTitle`.`reference_field` = "title"  AND `jfTitle`.`reference_id` = `sbj`.`id` AND `jfTitle`.`published` = 1';
        $query .= ' LEFT JOIN `#__jf_content` AS `jfIntrotext` ON `jfIntrotext`.`language_id` = ' . $languageID . ' AND `jfIntrotext`.`reference_table`  = "booking_subject" AND `jfIntrotext`.`reference_field` = "introtext"  AND `jfIntrotext`.`reference_id` = `sbj`.`id` AND `jfIntrotext`.`published` = 1';
        $query .= ' LEFT JOIN `#__jf_content` AS `jfFulltext` ON `jfFulltext`.`language_id` = ' . $languageID . ' AND `jfFulltext`.`reference_table`  = "booking_subject" AND `jfFulltext`.`reference_field` = "fulltext"  AND `jfFulltext`.`reference_id` = `sbj`.`id` AND `jfFulltext`.`published` = 1';
    }
    
    $mainWheres[] = '`sbj`.`state` = ' . SUBJECT_STATE_PUBLISHED;
    /* @var $mainWheres array Criteria for object availables for logged user */
    $mainWheres[] = '`sbj`.`access` IN (' . implode(',', AModel::getAccess()) . ')';
    $mainWheres[] = '(`sbj`.`publish_up` <= ' . $now . ' OR `sbj`.`publish_up` = ' . $nullDate . ')';
    $mainWheres[] = '(`sbj`.`publish_down` >= ' . $now . ' OR `sbj`.`publish_down` = ' . $nullDate . ')';
    
    $wordsWheres = array();
    /* @var $wordsWheres array search SQL criteria for all words */
    
    if ($phrase == 'exact') {
        foreach ($keywordWheres as $keywordWhere)
            foreach ($keyword as $word)
                $wordsWheres[] = $keywordWhere . ' LIKE ' . $word;
        $mainWheres[] = '(' . implode(' OR ', $wordsWheres) . ')';
    } else {
        foreach ($keywords as $word) {
            $wordWheres = array();
            /* @var $wordWheres array search SQL criteria for one word */
            foreach ($keywordWheres as $keywordWhere) {
                $wordWhere = array();
                foreach ($word as $w)
                    $wordWhere[] = $keywordWhere . ' LIKE ' . $w;
                $wordWheres[] = '(' . implode(') OR (', $wordWhere) . ')';
            }
            $wordsWheres[] = implode(' OR ', $wordWheres);
        }
        $mainWheres[] = '(' . implode(') ' . $operator . ' (', $wordsWheres) . ')';
    }
    
    $query .= ' WHERE ' . implode(' AND ', $mainWheres) . ' ORDER BY ';
    
    switch ($ordering) {
        case 'oldest':
            $query .= '`sbj`.`publish_up` ASC';
            break;
        
        case 'popular':
            $query .= '`sbj`.`hits` DESC';
            break;
        
        case 'alpha':
            $query .= '`sbj`.`title` ASC';
            break;
        
        case 'newest':
        default:
            $query .= '`sbj`.`publish_up` DESC';
            break;
    }
    
    $db->setQuery($query);
    $rows = &$db->loadObjectList();
    
    foreach ($rows as $row) {
        $row->href = ARoute::view(VIEW_SUBJECT, $row->id, $row->alias);
        $row->browsernav = 2;
        if (($template = &$templateHelper->getTemplateById($row->template))) {
            /* @var $template ATemplate */
            $row->section = $template->name;
        }
        $row->created = '';
    }
    
    return $rows;
}

function plgSearchBookingKeywords($original, $parse = false, $intoMultiArray = true)
{
    $db = &JFactory::getDBO();
    /* @var $db JDatabaseMySQL */
    
    $keywords = array();
    
    if ($parse) {
        if (! is_array($original))
            $original = array($original);
        foreach ($original as $orig)
            foreach (explode(' ', $orig) as $word)
                if (($word = JString::trim($word)))
                    if ($intoMultiArray)
                        $keywords[][] = $db->Quote('%' . JString::strtolower($word) . '%');
                    else
                        $keywords[] = $db->Quote('%' . JString::strtolower($word) . '%');
    } else {
        if (is_array($original))
            foreach ($original as $word)
                $keywords[] = $db->Quote('%' . JString::strtolower($word) . '%');
        else
            $keywords[] = $db->Quote('%' . JString::strtolower($original) . '%');
    }
    
    return $keywords;
}

?>