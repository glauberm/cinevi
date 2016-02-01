<?php

/**
 * Support for install component extensions such modules or plugins.
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

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

define('AINSTALLER_INSTALL', 1);
define('AINSTALLER_UNINSTALL', 2);

if (file_exists(($filepath = JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table.php')))
	include_once ($filepath);
if (file_exists(($filepath = JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'module.php')))
	include_once ($filepath);
if (file_exists(($filepath = JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'plugin.php')))
    include_once ($filepath);
if (file_exists(($filepath = JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'extension.php')))
    include_once ($filepath);

define('AINSTALLER_J16', class_exists('JTableExtension'));

class AInstaller
{

    /**
     * Proccess extensions installation.
     * 
     * @return void
     */
    function install()
    {
        if (($data = AInstaller::browsePackages(AINSTALLER_INSTALL)))
            AInstaller::setMsg('Installing', $data);
    }

    /**
     * Proccess extensions uninstallation.
     * 
     * @return void
     */
    function uninstall()
    {
        if (($data = AInstaller::browsePackages(AINSTALLER_UNINSTALL)))
            AInstaller::setMsg('Uninstalling', $data);
    }

    /**
     * Set result messages
     *
     * @param string $operation use Install or Uninstall
     * @param array $datas string title => extension name, string extType => extension type module/plugin , boolean outcome => success/unsuccess
     * @return void
     */
    function setMsg($operation, $datas)
    {
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        foreach ($datas as $data)
            if (is_array($data)) {
                if ($data['outcome']) {
               		if ($operation == 'Installing' && $data['extType'] == 'module')
                   		$mainframe->enqueueMessage(JText::sprintf('INSTALLING_MODULE_WAS_SUCCESSFUL', JText::_($data['title'])), 'message');
               		elseif ($operation == 'Installing' && $data['extType'] == 'plugin')
               			$mainframe->enqueueMessage(JText::sprintf('INSTALLING_PLUGIN_WAS_SUCCESSFUL', JText::_($data['title'])), 'message');
               		elseif ($operation == 'Uninstalling' && $data['extType'] == 'module')
               			$mainframe->enqueueMessage(JText::sprintf('UNINSTALLING_MODULE_WAS_SUCCESSFUL', JText::_($data['title'])), 'message');
               		else
               		$mainframe->enqueueMessage(JText::sprintf('UNINSTALLING_PLUGIN_WAS_SUCCESSFUL', JText::_($data['title'])), 'message');
                } else {
                	if ($operation == 'Installing' && $data['extType'] == 'module')
                   		$mainframe->enqueueMessage(JText::sprintf('INSTALLING_MODULE_WAS_UNSUCCESSFUL', JText::_($data['title'])), 'error');
                	elseif ($operation == 'Installing' && $data['extType'] == 'plugin')
                    	$mainframe->enqueueMessage(JText::sprintf('INSTALLING_PLUGIN_WAS_UNSUCCESSFUL', JText::_($data['title'])), 'error');
                	elseif ($operation == 'Uninstalling' && $data['extType'] == 'module')
                    	$mainframe->enqueueMessage(JText::sprintf('UNINSTALLING_MODULE_WAS_UNSUCCESSFUL', JText::_($data['title'])), 'error');
                	else
                		$mainframe->enqueueMessage(JText::sprintf('UNINSTALLING_PLUGIN_WAS_UNSUCCESSFUL', JText::_($data['title'])), 'error');
                }
            }
    }
    
    /**
     * @param bool $installfolder is it instalation, or update -> change folder
     * @return array of extension's folder
     */
    function getPackages($installfolder = true)
    {
    	//instalation
    	if($installfolder)
    		$rootfolder = dirname(__FILE__) . DS . '..' . DS;
    	//update - reinstall.php
    	else
    		$rootfolder = dirname(__FILE__) . DS . '..' . DS . 'admin' . DS;
    	
    	//Install payment methods
    	$folder = $rootfolder . 'extensions' . DS . 'payment_methods';
        if (JFolder::exists($folder)) {
            foreach (JFolder::folders($folder, '.', false, true) as $package) {
                $outcome[] = $package;
            }
        }
    
    	//install other plugins and modules
    	$folder = $rootfolder . 'extensions';
    	foreach (JFolder::folders($folder, '.', false, true, array('payment_methods')) as $package)
    		$outcome[] = $package;
    
    	return isset($outcome) ? $outcome : array();
    }

    /**
     * Browse all component extension and make selected operation.
     * 
     * @param int $type use constant AINSTALLER_INSTALL or AINSTALLER_UNINSTALL
     * @return array
     */
    function browsePackages($type)
    {
    	//get all extensions to install
    	$folders = self::getPackages();
    	 
    	//Install payment methods, plugins and modules
    	if (is_array($folders)) {
    		foreach ($folders as $package)
    			switch ($type) {
    				case AINSTALLER_INSTALL:
    					$outcome[] = &AInstaller::installPackage($package);
    					break;
    				case AINSTALLER_UNINSTALL:
    					$outcome[] = &AInstaller::uninstallPackage($package);
    					break;
    		}
    	}

        return isset($outcome) ? $outcome : array();
    }

    /**
     * Install concrete extension package.
     * 
     * @param string $package filepath to folder with extension
     * @param bool $override override already installed package
     * @return mixed false if unsuccess
     * or array with output data string title => extension name, string extType => extension type module/plugin , boolean outcome => success/unsuccess
     */
    function installPackage($package, $override = false)
    {
        $installer = new JInstaller();
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        
        //override existing
        if($override)
        	$installer->setOverwrite(true);
        
        if ($installer->install($package)) {
            if (is_object(($extension = &AInstaller::loadExtension($package)))) {
                $extType = $extension->extType;
                unset($extension->extType);
                $succes = $extension->store();
                $mainframe = &JFactory::getApplication();
                /* @var $mainframe JApplication */
                if (($manifest = &AInstaller::getManifest($package)) !== false) {
                    foreach (JFolder::files($package, '.', false, true) as $file)
                        if ($file != $manifest->source)
                            JFile::delete($file);
                    foreach (JFolder::folders($package, '.', false, true) as $folder)
                        JFolder::delete($folder);
                } else
                    JFolder::delete($package);
                return array('title' => AInstaller::getExtensionTitle($extension) , 'extType' => $extType , 'outcome' => true);
            }
        }
        return false;
    }

    /**
     * Unistall concrete package.
     * 
     * @param string $package filepath to folder with extension
     * @return mixed false if unsuccess
     * or array with output data string title => extension name, string extType => extension type module/plugin , boolean outcome => success/unsuccess
     */
    function uninstallPackage($package)
    {
        if (is_object(($extension = &AInstaller::loadExtension($package)))) {
            $installer = new JInstaller();
            $success = $installer->uninstall((string)$extension->extType, $extension->extension_id, $extension->client_id);
            return array('title' => AInstaller::getExtensionTitle($extension) , 'extType' => $extension->extType , 'outcome' => $success);
        }
        return false;
    }

    /**
     * Load extension from specify folder.
     * 
     * @param string $package filepath to folder with extension
     * @return mixed
     */
    function loadExtension($package)
    {
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        if (($manifest = &AInstaller::getManifest($package)) !== false) {
            $root = $manifest->parser;
            $element = $root->files;
            if (is_object($element)) {
                //$type = $root->attributes('type');
            	$type = $root['type'];
                foreach ($element->children() as $file)
                    if (($name = $file[$type])) {
                        if (class_exists(($classname = 'AInstaller' . ucfirst($type)))) {
                            $model = new $classname();
                            $extension = &$model->getTable();
                            $db->setQuery($model->getQuery($name, $root));
                            if (($id = (int) $db->loadResult())) {
                                $extension->load($id);
                                if (isset($extension->iscore) && (int) $extension->iscore != 0) {
                                    $extension->iscore = 0;
                                    $extension->store();
                                }
                            }
                            JFactory::getLanguage()->load($model->getLang($name, @$root['group']));
                            if (is_object(($setting = &$root->setting)))
                                foreach ($setting->attributes() as $name => $value)
                                    if (isset($extension->$name))
                                        $extension->$name = $value;
                            $model->extra($extension);
                            $extension->extType = $type;
                            
                            return $extension;
                        }
                    }
            }
        }
        return null;
    }

    /**
     * Get extension title from given data object.
     * 
     * @param mixed $extension
     * @return string
     */
    function getExtensionTitle($extension)
    {
        if (isset($extension->title))
            return $extension->title;
        elseif (isset($extension->name))
            return $extension->name;
    }

    /**
     * Get path to XML source and prepared object to parse XML data.
     * 
     * @param string $package filepath to folder with extension
     * @return stdClass string source => filepath to XML, JSimpleXML parser => object to parse XML 
     */
    function getManifest($package)
    {
        if (($source = reset(JFolder::files($package, '.xml$', false, true))) !== false) {
            $manifest = new stdClass();
            $manifest->source = $source;
            //$manifest->parser = &JFactory::getXMLParser('Simple');
            if ($manifest->parser = new SimpleXmlElement($source,null,true))
                return $manifest;
        }
        return false;
    }

    /**
     * Get Joomla! object table.
     * 
     * @return JTableExtension
     */
    function getTable()
    {
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        if (AINSTALLER_J16)
            return new JTableExtension($db);
        return null;
    }

    /**
     * Extra install operation.
     * 
     * @return void
     */
    function extra(&$extension)
    {}
}

/**
 * Helper object for installing module.
 */


class AInstallerModule extends AInstaller
{

    /**
     * Get Joomla! object table.
     * 
     * @return JTableExtension
     */
    function getTable()
    {
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */

        return parent::getTable();
    }

    /**
     * Get SQL query to search installed extension database registration.
     * 
     * @param string $name
     * @return string SQL query
     */
    function getQuery($name)
    {
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        return 'SELECT `extension_id` FROM `#__extensions` WHERE `element` = ' . $db->Quote($name) . ' AND `type` = "module" LIMIT 1';
    }

    /**
     * Extra install operation.
     * 
     * @param JTableExtension $extension
     * @return void
     */
    function extra(&$extension)
    {
        if (AINSTALLER_J16) {
            if (isset($extension->enabled)) {
                $db = &JFactory::getDBO();
                /* @var $db JDatabaseMySQL */
                $db->setQuery('UPDATE `#__modules` SET `published` = ' . ((int) $extension->enabled) . ' WHERE `module` = ' . $db->Quote($extension->get('element')));
                $db->query();
            }
        }
    }
    
    function getLang($name)
    {
    	return (string) $name;
    }
}

/**
 * Helper object for installing plugin.
 */
class AInstallerPlugin extends AInstaller
{

    /**
     * Get Joomla! object table.
     * 
     * @return JTableExtension
     */
    function getTable()
    {
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        return parent::getTable();
    }

    /**
     * Get SQL query to search installed extension database registration.
     * 
     * @param string $name
     * @return string SQL query
     */
    function getQuery($name, $root)
    {
        $db = &JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        $group = $root['group'];
        return 'SELECT `extension_id` FROM `#__extensions` WHERE `element` = ' . $db->Quote($name) . ' AND `folder` = ' . $db->Quote($group) . ' AND `type` = "plugin" LIMIT 1';
    }
    
    function getLang($name, $group)
    {
    	return 'plg_' . $group . '_' . $name;
    }
}

class AInstallerJoomFish
{

    function install()
    {
        AInstallerJoomFish::init('install');
    }

    function uninstall()
    {
        AInstallerJoomFish::init('uninstall');
    }

    function init($operation = 'install')
    {
        if (! class_exists('JFile')) jimport('joomla.filesystem.file');
        $mainframe = JFactory::getApplication();
        /* @var $mainframe JApplication */
        if (JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_joomfish/contentelements')) { // Joom!Fish available
        	$target = JPATH_ADMINISTRATOR . '/components/com_joomfish/contentelements';
        	$master = 'Joom!Fish'; 
        	$source = JPATH_COMPONENT_ADMINISTRATOR . '/joomfish';
        } elseif (JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_falang/contentelements')) { // FaLang available
        	$target = JPATH_ADMINISTRATOR . '/components/com_falang/contentelements';
        	$master = 'FaLang'; 
        	$source = JPATH_COMPONENT_ADMINISTRATOR . '/falang';
        } else return; // any translation master
        if (!JFolder::exists($source)) return;
       	if (IS_ADMIN && ! is_writable($target)) return $mainframe->enqueueMessage(JText::sprintf('ERR_WRITE_CONTENTELEMENTS', $target, $operation, $master), 'error');
       	if (IS_ADMIN && ! is_readable($source)) return $mainframe->enqueueMessage(JText::sprintf('ERR_READ_CONTENTELEMENTS', $source, $operation, $master), 'error');
      	if (! is_writable($target) || ! is_readable($source)) return;
      	if ($operation == 'install') foreach (JFolder::files($source) as $file) if (!JFile::exists($target . '/' . $file)) JFile::copy($source . '/' . $file, $target . '/' . $file);
       	elseif ($operation == 'uninstall') foreach (JFolder::files($source) as $file) if (JFile::exists($target . '/' . $file)) JFile::delete($target . '/' . $file);
    }
}

?>