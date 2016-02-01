<?php

defined('JPATH_BASE') or die();

if(!ISJ3){
	include_once (JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'html' . DS . 'toolbar' . DS . 'button' . DS . 'link.php');
	
	class JButtonALink extends JButtonLink
	{
	
	    function fetchId($type = 'Link', $name = 'back', $text = '', $url = null, $id = null)
	    {
	        if ($id)
	            return $id;
	        return parent::fetchId($type, $name, $text, $url);
	    }
	    
	    public function fetchButton($type = 'Link', $name = 'back', $text = '', $url = null, $id = null)
	    {
	    	$text = JText::_($text);
	    	$class = $this->fetchIconClass($name);
	    	$doTask = $this->_getCommand($url);
	    
	    	$html = "<a onclick=\"".$doTask."; return false;\">\n";
	    	$html .= "<span class=\"".$class."\">\n";
	    	$html .= "</span>\n";
	    	$html .= $text."\n";
	    	$html .= "</a>\n";
	    
	    	return $html;
	    }
	}
}
else
{
	jimport('joomla.html.toolbar');
	class JToolbarButtonALink extends JToolbarButtonLink
	{
	
		function fetchId($type = 'Link', $name = 'back', $text = '', $url = null, $id = null)
		{
			if ($id)
				return $id;
			return parent::fetchId($type, $name, $text, $url);
		}
		
		public function fetchButton($type = 'Link', $name = 'back', $text = '', $url = null, $id = null)
		{
			$text = JText::_($text);
			$class = $this->fetchIconClass($name);
			$doTask = $this->_getCommand($url);
			$id = $this->fetchId($type, $name, $text, $url);
		
			$html = "<button class=\"btn btn-small\" onclick=\"".$doTask."; return false;\" id=".$id.">\n";
			$html .= "<span class=\"".$class."\">\n";
			$html .= "</span>\n";
			$html .= $text."\n";
			$html .= "</button>\n";
		
			return $html;
		}
	}
}

?>