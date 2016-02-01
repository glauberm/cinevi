<?php

/**
 * Port between JFactory::getXMLParser('Simple') and SimpleXMLElement.
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class SimpleXmlParser
{
	private $parser = Object;
	
	public $document;
	function __construct($data = null, $options = null, $data_is_url = null, $ns = null, $is_prefix = null)
    {
    	if($data !== null)
    		$this->parser = new SimpleXMLElement($data, $options, $data_is_url, $ns, $is_prefix);
    	
    	$this->document = $this;  
    }
    
    function getElementByPath($path)
    {
    	return $this->parser->$path;
    }
    
    function loadFile($filename, $class_name = null, $options = null, $ns = null, $is_prefix = null)
    {
    	if($this->parser = simplexml_load_file($filename, $class_name, $options, $ns, $is_prefix))
    		return TRUE;
    	else
    		return false;
    }
    
    function loadString($data, $class_name = null, $options = null, $ns = null, $is_prefix = null)
    {
    	if($this->parser = simplexml_load_string($filename, $class_name, $options, $ns, $is_prefix))
    		return TRUE;
    	else
    		return false;
    }
    
    
    
}

?>