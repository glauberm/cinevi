<?php

/**
 * Images upload and select browse window.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

//import needed Joomla! libraries
jimport('joomla.application.component.view');
jimport('joomla.filesystem.file');
jimport('joomla.html.pagination');

//import needed JoomLIB helpers
AImporter::helper('booking', 'image', 'model', 'request');
//import needed assets
AImporter::js('view-images');

AImporter::adminTemplateCss(null, 'general', 'icon');
AImporter::adminTemplateCss('system', 'system');

AHtml::importIcons();

define('SESSION_PREFIX', 'booking_images_');

class BookingViewImages extends JViewLegacy
{

    function display($tpl = null)
    {
        $task = JRequest::getCmd('task');
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        
        $ipath = BookingHelper::getIPath();
        $this->dir = $mainframe->getUserStateFromRequest('aimages_dir', 'dir', '', 'string');
        
        //todo should be moved to controller
        switch ($task) {
        	case 'upload':
        		$error = '';
        		$uimage = null;
        		if (AImage::upload(JPath::clean($ipath . DS . $this->dir . DS), 'image', $uimage, $error))
        			$this->assignRef('uimage', $uimage);
        		else
        			$mainframe->enqueueMessage('Unable upload image', 'error');
        		break;
        	case 'remove':
        		$removeImages = &ARequest::getArray('images');
        		$count = count($removeImages);
        		for ($i = 0; $i < $count; $i ++)
        			JFile::delete($ipath . DS . $this->dir . DS . JFile::getName($removeImages[$i]));
        		break;
        	case 'removefolder':
        		$removeFolder = JRequest::getVar('folder');
        		JFolder::delete(JPath::clean($ipath . DS . $removeFolder));
        		break;
        	case 'mkdir':
        		$newpath = JPath::clean($ipath . DS . $this->dir . DS . JRequest::getString('dirname'));
        		if (JFolder::create($newpath, 0775) === false)
        			$mainframe->enqueueMessage(JText::sprintf('Unable create directory', $newpath), 'error');
        		break;
        }
        
        //select images,folder and filter them
        $this->filter = ARequest::getUserStateFromRequest('filter', '', 'string');
        
        $this->testFilter = JString::trim($this->filter);
        $this->testFilter = JString::strtolower($this->testFilter);
        	
        //get images/folders from folder
        $files = BookingHelper::getFolderImages(JPath::clean($ipath . DS . $this->dir));
        	
        $this->images = $files['file'];
        $this->dirs = $files['folder'];
        	
        //count files in folder
        $this->totalOrigImage = count($this->images);
        $this->totalOrigDir = count($this->dirs);    	
        	
        //apply string filter
        if ($this->testFilter && ($this->totalOrigImage || $this->totalOrigDir)){
        	$this->images = BookingHelper::filterFiles($this->images, $this->filter);
        	$this->dirs = BookingHelper::filterFiles($this->dirs);
        }
         
        //count filtered images
        $this->totalImage = count($this->images);
        $this->totalDir = count($this->dirs);
        
        parent::display($tpl);
    }
}

?>