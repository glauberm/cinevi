<?php

/**
 * Default component controller.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

//import needed Joomla! libraries
jimport('joomla.application.component.controller');
//import needed JoomLIB helpers
AImporter::helper('controller','request');

class BookingController extends AController
{

    function display()
    {
    	if(IS_SITE) //custom config for user who owns subject
    	{
	    	if(JRequest::getString('view') == "reservation" && JRequest::getString('layout') == "form")
	    	{
	    		$mainframe = &JFactory::getApplication();
	    		$sessionItems = $mainframe->getUserState(OPTION.'.user_reservation_items');
	    		if (is_array($sessionItems) && count($sessionItems)) {
	    			$item = reset($sessionItems);
	    			$this->setGlobalConfigByUserFromSubject($item['subject']);
	    		}
	    	}
	    	if(JRequest::getString('view') == "reservation" && JRequest::getString('layout') == "")
	    	{
    			$this->setGlobalConfigByUserFromReservation(ARequest::getCid());
	    	}
	    	else if(JRequest::getString('view') == "subject" && JRequest::getString('layout') == "")
	    	{
	    		$this->setGlobalConfigByUserFromSubject(JRequest::getInt('id'));
	    	}
    	}
    	
        $helpController = JRequest::getString('help_controller');
        if ($helpController) {
            $classname = AImporter::controller($helpController);
            if (class_exists($classname)) {
                $controller = new $classname();
                $controller->_doRedirect = false;
                $controller->execute(JRequest::getVar('task'));
            }
        }
        parent::display();
    }

    function sampleData()
    {
        AImporter::helper('image', 'route', 'template');
        
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $template = new ATemplate();
        
        if (! file_exists(SAMPLE_SQL))
            $mainframe->redirect(ARoute::root(), JText::sprintf('SAMPLE_SOURCE_S_DONT_EXIST', SAMPLE_SQL), 'error');
        
        $queries = JFile::read(SAMPLE_SQL);
        
        $template->setNewId();
        $lastTemplateID = $template->id;
        
        $db->setQuery('SELECT MAX(`id`) FROM `#__booking_subject`');
        $lastSubjectID = $lid = (int) $db->loadResult();
        
        $db->setQuery('SELECT MAX(`id`) FROM `#__booking_reservation_type`');
        $lastRTypeID = (int) $db->loadResult();
        
        $templatesMask = array();
        $subjectsMask = array();
        $rtypesMask = array();
        
        if (preg_match_all("#('%T[1-9][0-9]*T%')#isU", $queries, $templatesMask, PREG_PATTERN_ORDER))
            $templatesMask = array_merge(array_unique(reset($templatesMask)));
        
        if (preg_match_all("#('%B[1-9][0-9]*B%')#isU", $queries, $subjectsMask, PREG_PATTERN_ORDER))
            $subjectsMask = array_unique(reset($subjectsMask));
        
        if (preg_match_all("#('%R[1-9][0-9]*R%')#isU", $queries, $rtypesMask, PREG_PATTERN_ORDER))
            $rtypesMask = array_unique(reset($rtypesMask));
        
        foreach ($templatesMask as $i => $templateMask) {
            $queries = str_replace($template->getDBTableName($i + 1), $template->getDBTableName($lastTemplateID), $queries);
            $queries = str_replace($templateMask, ($lastTemplateID ++), $queries);
        }
        
        foreach ($subjectsMask as $subjectMask)
            $queries = str_replace($subjectMask, ++ $lastSubjectID, $queries);
        
        foreach ($rtypesMask as $rtypeMask)
            $queries = str_replace($rtypeMask, ++ $lastRTypeID, $queries);
        
        // Fill default dates
        $queries = str_replace('%DB%', date('Y-m-d', time() - (60 * 60 * 24)), $queries);
        $queries = str_replace('%DE%', date('Y-m-d', time() + (60 * 60 * 24 * 365)), $queries);
        
        $path = BookingHelper::getIPath();
        
        foreach (JFolder::files(IMAGES_SAMPLE) as $image) {
            $replace = '';
            do {
                $target = $path . ($new = ($replace ++) . $image);
            } while (file_exists($target));
            JFile::copy(IMAGES_SAMPLE . DS . $image, $target);
            $queries = str_replace($image, $new, $queries);
        }
        
        if (ISJ16)
            $queries .= 'UPDATE `#__booking_subject` SET `access` = (SELECT `id` FROM `#__viewlevels` ORDER BY `id` ASC LIMIT 0,1) WHERE `id` > ' . (int) $lid . ';';
        
        BookingHelper::queries($queries);
        
        $mainframe->redirect(ARoute::root(), JText::_('SAMPLE_DATA_INSTALLED_SUCCESSFULLY'));
    }
}

?>