<?php

/**
 * @version	$Id$
 * @package	ARTIO Booking
 * @subpackage	plugins/system
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license   	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Joomla system plug-in. Dispatched always when Joomla core starts.
*/
class plgSystemDismoot extends JPlugin
{

	function onBeforeRender()
	{
		if($this->isBackend())
			return false;
		
		$doc = JFactory::getDocument();
		
		//only for total disabling mootools
		if (!$this->params->get('only_unlink', 1))
		{
			// Function used to replace window.addEvent()
			$doc->addScriptDeclaration("function none() { return; }");
		
			// Disable css stylesheets
			unset($doc->_styleSheets[JURI::root(true) . '/media/system/css/modal.css']);
		}

		// Disable loading mootools javascript
		$path = JURI::root(true);
		$moot_files = array(
				'/media/system/js/mootools-core.js',
				'/media/system/js/mootools-more.js',
				'/media/system/js/core.js',
				'/media/system/js/caption.js',
				'/media/system/js/modal.js',
				'/media/system/js/mootools.js',
				'/plugins/system/mtupgrade/mootools.js',
				'/media/system/js/mootools-core-uncompresed.js',
				'/media/system/js/core-uncompresed.js',
				'/media/system/js/caption-uncompresed.js'
				);
		
		foreach($moot_files as $file)
		{
			unset($doc->_scripts[$path . $file]);
		}
		
		//special option of normal doesn't work
		if($this->params->get('brutalforce_unlink', 0))
		{
			//brutalforce deleting all scripts including 'mootools' or 'caption' in path/filename
			$search = array('mootools', 'caption.js');
			foreach($doc->_scripts as $key => $script) {
				foreach($search as $findme) {
					if(stristr($key, $findme) !== false) {
						unset($doc->_scripts[$key]);
					}
				}
			}
		}

		return true;
	}
	
	function onAfterRender()
	{
		if($this->isBackend())
			return false;
		
		//only for total disabling mootools
		if (!$this->params->get('only_unlink', 1))
		{
			//generated html
			$body = JResponse::getBody();
	
			// Remove JCaption JS calls
			$pattern     = "/(new JCaption\()(.*)(\);)/isU";
			$replacement = '';
			$body        = preg_replace($pattern, $replacement, $body);
	
			//deactive mootool's addEvent
			$pattern = "/(window.addEvent\()(.*)(,)/isU";
			$body    = preg_replace($pattern, 'none(', $body);
			
			JResponse::setBody($body);
		}

		return true;
	}
	
	private function isBackend()
	{
		return JFactory::getApplication()->isAdmin()? true : false;
	}
}