<?php

/**
 * Support for import php classes, javascript or css sources.
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

class AImporter
{

    /**
     * Import file by absolute path from component root. 
     *
     * @param string $base last directory contain files: helpers, models, elements ...
     * @param array $names files names without path and extensions
     * @param string extension without dot: php, html, ini ...
     */
    function import($base, $names, $ext = 'php')
    {
        if (! is_array($names)) {
            $names = array($names);
        }
        $filePathMask = ADMIN_ROOT . DS . $base . DS . '%s.' . $ext;
        foreach ($names as $name) {
            AImporter::importFile(sprintf($filePathMask, $name));
        }
    }

    /**
     * Import file view.html.php from component view.
     * 
     * @param string $view view name, if empty use parameter from request
     */
    function importView($view = null)
    {
        AImporter::importFile(AImporter::getViewBase($view) . 'view.html.php');
    }

    /**
     * Import template file from component view.
     * 
     * @param string $view view name, if empty use parameter from request
     * @param string $tpl template name, if empty use default.php
     */
    function importTpl($view = null, $tpl = null)
    {
        AImporter::importFile(AImporter::getViewBase($view) . 'tmpl' . DS . ($tpl ? $tpl : 'default') . '.php');
    }

    /**
     * Get component view base directory.
     * 
     * @param string $view view name, if empty use parameter from request
     */
    function getViewBase($view = null)
    {
        return ADMIN_ROOT . DS . 'views' . DS . ($view ? $view : JRequest::getString('view')) . DS;
    }

    function importFile($filePath, $error = true)
    {
        if (file_exists($filePath)) {
            include_once ($filePath);
        } elseif ($error) {
            JError::raiseError(500, 'File ' . $filePath . ' not found');
        }
    }

    /**
     * Link js or css file into html head.
     *
     * @param string $type source type, available values are 'js' or 'css'
     * @param array $names files names without extension and path
     */
    function link($type, $names)
    {
        $absMask = SITE_ROOT . DS . 'assets' . DS . $type . DS . '%s.' . $type;
        $langMask = SITE_ROOT . DS . 'assets' . DS . 'language' . DS . '%s.php';
        $realMask = JURI::root() . 'components/' . OPTION . '/assets/' . $type . '/%s.' . $type . '?v=' . ASSETS_VERSION;
        foreach ($names as $name) {
            $abs = sprintf($absMask, $name);
            $real = sprintf($realMask, $name);
            if (file_exists($abs)) {
                $document = &JFactory::getDocument();
                switch ($type) {
                    case 'js':
                        $document->addScript($real);
                        AImporter::importFile(sprintf($langMask, $name), false);
                        break;
                    case 'css':
                        AImporter::helper('document');
                        $document->addStyleSheet($real);
                        $nameIE6 = $name . '-ie6';
                        $absIE6 = sprintf($absMask, $nameIE6);
                        $realIE6 = sprintf($realMask, $nameIE6);
                        if (file_exists($absIE6)) {
                            ADocument::addIE6StyleSheet($realIE6);
                        }
                        $nameIE7 = $name . '-ie7';
                        $absIE7 = sprintf($absMask, $nameIE7);
                        $realIE7 = sprintf($realMask, $nameIE7);
                        if (file_exists($absIE7)) {
                            ADocument::addIE7StyleSheet($realIE7);
                        }
                        break;
                }
            } else {
                JError::raiseError(500, 'File ' . $name . ' not found');
            }
        }
    }

    /**
     * Import component controller. Current or default according to client position.
     * @return boolean true if successfully imported
     */
    function controller($name = null)
    {
    	$match = array();
    	if (preg_match('/^([a-z_]+)\.([a-z_]+)$/', JRequest::getString('task'), $match)) {
    		JRequest::setVar('controller', $match[1]);
    		JRequest::setVar('task', $match[2]);
    	}
        $cname = is_null($name) ? JRequest::getString('controller', 'controller') : $name;
        $cname = $cname ? $cname : 'controller';
        AImporter::import('controllers', $cname);
        return AImporter::getControllerName($name);
    }

    function getControllerName($name = null)
    {
        $name = is_null($name) ? JRequest::getString('controller', '') : $name;
        return CONTROLLER . $name;
    }

    /**
     * Import component constants.
     */
    function defines()
    {
        AImporter::importFile(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booking' . DS . 'defines.php');
    }

    /**
     * Import component helper.
     *
     * @param mixed $name file name without extension and path
     */
    function helper($name)
    {
        $names = func_get_args();
        AImporter::import('helpers', $names);
    }

    /**
     * Import component model.
     *
     * @param mixed $name file name without extension and path.
     */
    function model($name)
    {
        $names = func_get_args();
        AImporter::import('models', $names);
    }

    /**
     * Import component table.
     *
     * @param mixed $name file name without extension and path.
     */
    function table($name)
    {
        $names = func_get_args();
        AImporter::import('tables', $names);
    }

    /**
     * Import component object.
     *
     * @param mixed $name file name without extension and path.
     */
    function object($name)
    {
        $names = func_get_args();
        AImporter::import('objects', $names);
    }

    /**
     * Import component element.
     *
     * @param mixed $name file name without extension and path.
     */
    function element($name)
    {
        $names = func_get_args();
        AImporter::import('elements', $names);
    }

    /**
     * Link js source into html head.
     *
     * @param mixed $name file name without extension and path
     */
    function js($name)
    {
        $names = func_get_args();
        AImporter::link('js', $names);
    }

    /**
     * Link css source into html head.
     *
     * @param mixed $name file name without extension and path
     */
    function css($name)
    {
        $names = func_get_args();
        AImporter::link('css', $names);
    }

    /**
     * Import style for CSS icon into HTML page head.
     * 
     * @param string $className
     * @param string $fileName
     */
    function cssIcon($className, $fileName)
    {
        $css = '.aIcon' . ucfirst($className) . ' {' . PHP_EOL;
        $css .= '    background: transparent url(' . IMAGES . $fileName . ') no-repeat scroll left center;' . PHP_EOL;
        $css .= '}' . PHP_EOL;
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        $document->addStyleDeclaration($css);
    }

    /**
     * Import CSS file from current admin Template.
     * 
     * @param string $name
     */
    function adminTemplateCss($template, $name)
    {
        $names = func_get_args();
        if (count($names)) {
            unset($names[0]);
        }
        if (is_null($template)) {
            $mainframe = &JFactory::getApplication();
            /* @var $mainframe JApplication */
            $template = $mainframe->getTemplate();
        }
        foreach ($names as $name) {
            JHTML::stylesheet($name . '.css', 'administrator/templates/' . $template . '/css/');
        }
    }

    /**
     * Import Joomla! main JS Library.
     */
    function joomlaJS()
    {
        $user = &JFactory::getUser();
        /* @var $user JUser */
        if (! $user->id && JFile::exists(JPATH_ROOT . DS . 'includes' . DS . 'js' . DS . 'joomla.javascript.js')) {
            $document = &JFactory::getDocument();
            /* @var $document JDocument */
            $document->addScript(JURI::root(true) . '/includes/js/joomla.javascript.js');
        }
    }

    /**
     * Remove specified scripts from header.
     */
    function removeScripts()
    {
    	$jsForRemove = func_get_args();
    	$document = &JFactory::getDocument();
    	$headerstuff = $document->getHeadData();
		foreach ($headerstuff['scripts'] as $key => $value)
			foreach ($jsForRemove as $js)
				if (strpos($key, $js) !== false) unset($headerstuff['scripts'][$key]);
		$document->setHeadData($headerstuff);
    }
    
    /**
     * Remove specified stylesheets from header.
     */
    function removeStyleSheets()
    {
    	$cssForRemove = func_get_args();
    	$document = &JFactory::getDocument();
    	$headerstuff = $document->getHeadData();
		foreach ($headerstuff['styleSheets'] as $key => $value)
			foreach ($cssForRemove as $js)
				if (strpos($key, $js) !== false) unset($headerstuff['styleSheets'][$key]);
		$document->setHeadData($headerstuff);
    }
    
    /**
     * Display template of another view.
     * 
     * @param string $view
     * @param string $tpl
     * @param string $layout
     * @param string $base use ADMIN_VIEWS/SITE_VIEWS constant
     * @param boolean $returnPath if true return full path, false include file
     * @return mixed string path or void
     */
    function tpl($view, $layout, $tpl, $base = ADMIN_VIEWS, $returnPath = false)
    {
        $name = ($layout ? ($layout . '_') : '') . $tpl . '.php';
        $path = $base . DS . $view . DS . 'tmpl' . DS . $name;
        if ($returnPath)
            return $path;
        include ($path);
    }

    /**
     * Get path to payment files.
     * 
     * @param string $alias
     * @return array
     */
    function payment($alias)
    {
    	//return array('button' => ($base = PAYMENTS . DS . JString::substr($alias, 0, JString::strrpos($alias, '_')) . DS) . 'button.php' , 'config' => $base . 'config.xml' , 'controller' => $base . 'controller.php' , 'icon' => $base . 'icon.png');
    	
    	//get params from plugin
    	return AEvent::OnePayment('onPluginFiles',$alias);
    }
}

?>