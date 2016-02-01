<?php

/**
 * Upgrade model.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  models 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.installer.helper');

class BookingModelUpgrade extends JModelLegacy
{
	
    function getNewestVersion()
    {
    	$version = $this->post(NEWEST_VERSION);
    	$version = empty($version) ? false : $version->content;
        return $version === false ? '?.?.?' : JString::trim($version);
    }

    function getRegisteredInfo($artioDownloadID = null)
    {
        static $regInfo;
        if (is_null($regInfo)) {
            $regInfo = new stdClass();
            
            $params = JComponentHelper::getParams(OPTION);
            
            if (is_null($artioDownloadID)) {
                $artioDownloadID = JString::trim($params->get('artioDownloadId'));
            }
            
            if ($artioDownloadID) {
            	if (JString::strlen($artioDownloadID) != 32) {
            		JError::raiseNotice(100, JText::_('INVALID_DOWNLOAD_ID'));
            		return null;
            	}
                // Send the request to ARTIO server to check registration
                $data = array('download_id' => $artioDownloadID);
                $response = &$this->post(LICENSE, null, $data);
                
                if (($response === false) || ($response === null) || ($response->code != 200)) {
                	if ($response !== null) // there is another error message
                    	JError::raiseNotice(100, JText::_('ERROR_REG_CHECK_FAIL'));
                    JFactory::getApplication()->enqueueMessage(JText::sprintf('INFO_DOWNLOAD_AREA', $artioDownloadID));
                    return null;
                } else {
                    // Parse the response - get individual lines
                    $lines = explode("\n", $response->content);
                    
                    // Get the code
                    $pos = strpos($lines[0], ' ');
                    if($pos === false) {
                        JError::raiseNotice(100, JText::_('ERROR_REG_CHECK_FAIL'));
                        return null;
                    }
                    $regInfo->code = intval(substr($lines[0], 0, $pos));
                    
                    if(($regInfo->code == 10) || ($regInfo->code == 20)) {
                        // Download id found
                        if (count($lines) < 3) {
                            // Wrong response
                            JError::raiseNotice(100, JText::_('ERROR_REG_CHECK_FAIL'));
                            return null;
                        }
                        
                        // Parse the date
                        $date = JFactory::getDate(str_replace('.', '/', JString::trim($lines[1])));
                        $regInfo->date = $date->format(ADATE_FORMAT_NORMAL);
                        
                        // Parse the name
                        $regInfo->name = JString::trim($lines[2]);
                        
                        // Parse the company
                        $regInfo->company = isset($lines[3]) ? JString::trim($lines[3]) : '';
                        
                        // Is upgrade expired?
                        if ($regInfo->code == 20) {
                            JError::raiseNotice(100, JText::_('INFO_UPGRADE_LICENSE_EXPIRED'));
                        }
                    } else if ($regInfo->code == 30) {
                    	// Is upgrade inactive
                      	JError::raiseNotice(100, JText::_('INFO_UPGRADE_NOT_ACTIVE'));
                      	JFactory::getApplication()->enqueueMessage(JText::sprintf('INFO_ACTIVATE_UPGRADE', $artioDownloadID));
                     	$regInfo->date = JText::_('NOT_ACTIVATED_YET');
                     	$regInfo->name = JString::trim(JArrayHelper::getValue($lines, 2));
                     	$regInfo->company = JString::trim(JArrayHelper::getValue($lines, 3));
                    } else if ($regInfo->code == 40) {
                    	// Domain does not match
                      	JError::raiseNotice(100, JText::_('DOMAIN_DOES_NOT_MATCH'));
                      	return null;
                    } else if ($regInfo->code == 90) {
                        // Download id not found, do nothing
                        JError::raiseNotice(100, JText::_('ERROR_DOWNLOAD_ID_NOT_FOUND'));
                    } else {
                        // Wrong response
                        JError::raiseNotice(100, JText::_('ERROR_REG_CHECK_FAIL'));
                        return null;
                    }
                }
            } else {
                // Download ID not set
                JError::raiseNotice(100, JText::_('DOWNLOAD_ID_NOT_SET'));
                return null;
            }
        
        }
        
        return $regInfo;
    }

    function upgrade()
    {
        $fromServer = JRequest::getVar('fromserver');
        $extension = null;
        
        if (is_null($fromServer)) {
            $this->setState('message', JText::_('UPGRADE_SOURCE_NOT_GIVEN'));
            return false;
        }
        
        if($fromServer == 1) {
            $package = $this->_getPackageFromServer($extension);
        } else {
            $package = $this->_getPackageFromUpload();
        }

        // was the package unpacked?
        if (!$package) {
            $this->setState('message', 'Unable to find install package.');
            return false;
        }
        
        $data = JInstaller::parseXMLInstallFile(MANIFEST);
        
        // get current version
        $curVersion = $data['version'];
        
        if (empty($curVersion)) {
            $this->setState('message', JText::_('COULD_NOT_FIND_CURRENT_VERSION'));
            JFolder::delete($package['dir']);
            return false;
        }
        
        // create an array of upgrade files
        $upgradeDir = $package['dir'] . DS . 'upgrade';
        $upgradeFiles = JFolder::files($upgradeDir, '.php$');
        
        if (empty($upgradeFiles)) {
            $this->setState('message', JText::_('THIS_PACKAGE_DOES_NOT_CONTAIN_ANY_UPGRADE_INFORMATIONS'));
            JFolder::delete($package['dir']);
            return false;
        }

        // check if current version is upgradeable with downloaded package
        $reinstall = false;
        if (!in_array($curVersion . '.php', $upgradeFiles)) {
            // check if current version is being manually reinstalled with the same version package
            $xmlFile = $package['dir'] . DS . NAME . '.xml';
            $packVersion = $this->_getXmlText($xmlFile, 'version');
            if (version_compare($packVersion, $curVersion, '>=') && JFile::exists($upgradeDir . DS . 'reinstall.php')) {
                // initiate the reinstall
                $reinstall = true;
                $mainframe = JFactory::getApplication();
                $mainframe->enqueueMessage(JText::_('INFO_COMPONENT_REINSTALL'));
            }

            if (!$reinstall) {
                $this->setState('message', JText::_('ERROR_CANT_UPGRADE'));
                JFolder::delete($package['dir']);
                return false;
            }
        }

        natcasesort($upgradeFiles);

        // prepare arrays of upgrade operations and functions to manipulate them
        $this->_fileError = false;
        $this->_fileList = array();
        $this->_sqlList = array();
        $this->_scriptList = array();

        if (!$reinstall) {
            // load each upgrade file starting with current version in ascending order
            foreach ($upgradeFiles as $uFile) {
            	if (!preg_match("/^[0-9]+\.[0-9]+\.[0-9]+(\-beta\d+)?\.php$/i", $uFile)) {
            		continue;
            	}
                if (strnatcasecmp($uFile, $curVersion . ".php") >= 0) {
                    include_once($upgradeDir . DS . $uFile);
                }
            }
        } else {
            // create list of all files to upgrade
            include_once($upgradeDir . DS . 'reinstall.php');
        }
        
        if ($this->_fileError == false) {
            // set errors variable
            $errors = false;

            // first of all check if all the files are writeable
            // ONLY IF FTP IS DISABLED
            jimport('joomla.client.helper');
            $ftpOptions = JClientHelper::getCredentials('ftp');
            
            if($ftpOptions['enabled'] != 1) {
                
                foreach ($this->_fileList as $dest => $op) {
                    $file = JPath::clean(JPATH_ROOT . DS . $dest);

                    // check if source file is present in upgrade package
                    if ($op->operation == 'upgrade') {
                        $from = JPath::clean($package['dir'] . DS . $op->packagePath);
                        if(!JFile::exists($from)) {
                            JError::raiseWarning(100, JText::_('FILE_DOES_NOT_EXIST_IN_UPGRADE_PACKAGE') . ': ' . $op->packagePath);
                            $errors = true;
                        }
                    }
                    
                    if ((($op->operation == 'delete') && (JFile::exists($file))) || (($op->operation == 'upgrade') && (! JFile::exists($file)))) {
                        
                        // if the file is to be deleted or created, the file's directory must be writable
                        $dir = dirname($file);
                        if (!JFolder::exists($dir)) {
                            // we need to create the directory where the file is to be created
                            if(!JFolder::create($dir)) {
                                JError::raiseWarning(100, JText::_('DIRECTORY_COULD_NOT_BE_CREATED') . ': ' . $dir);
                                $errors = true;
                            }
                        }

                        if (!is_writable($dir)) {
                            if (!JPath::setPermissions($dir, '0755', '0777')) {
                                JError::raiseWarning(100, JText::_('DIRECTORY_NOT_WRITEABLE') . ': ' . $dir);
                                $errors = true;
                            }
                        }
                    } elseif ($op->operation == 'upgrade') {
    
                        // the file itself must be writeable
                        if (!is_writable($file)) {
                            if (!JPath::setPermissions($file, '0755', '0777')) {
                                JError::raiseWarning(100, JText::_('FILE_NOT_WRITEABLE') . ': ' . $file);
                                $errors = true;
                            }
                        }
                    }
                }
            }
            
            // If there are no errors, let's upgrade
            if (!$errors) {
                $db = JFactory::getDBO();

                // execute SQL queries
                foreach ($this->_sqlList as $sql) {
                    $db->setQuery($sql);
                    if(!$db->query()) {
                        JError::raiseWarning(100, JText::_('UNABLE_TO_EXECUTE_SQL_QUERY') . ': ' . $sql);
                        $errors = true;
                    }
                }

                // perform file operations
                foreach ($this->_fileList as $dest => $op) {
                    if ($op->operation == 'delete') {
                        $file = JPath::clean(JPATH_ROOT . DS . $dest);
                        if (JFile::exists($file)) {
                            $success = JFile::delete($file);
                            if (!$success) {
                                JError::raiseWarning(100, JText::_('COULD_NOT_DELETE_FILE_PLEASE_CHECK_THE_WRITE_PERMISSIONS_ON') . ' ' . $dest);
                                $errors = true;
                            }
                        }
                    } elseif ($op->operation == 'upgrade') {
                        $from = JPath::clean($package['dir'] . DS . $op->packagePath);
                        $to = JPath::clean(JPATH_ROOT . DS . $dest);
                        $destDir = dirname($to);

                        // create the destination directory if needed
                        if (!JFolder::exists($destDir)) {
                            JFolder::create($destDir);
                        }

                        $success = JFile::copy($from, $to);
                        if (!$success) {
                            JError::raiseWarning(100, JText::_('COULD_NOT_REWRITE_FILE_PLEASE_CHECK_THE_WRITE_PERMISSIONS_ON') . ' ' . $dest);
                            $errors = true;
                        }
                    }
                }

                // run scripts
                foreach ($this->_scriptList as $script) {
                    $file = JPath::clean($package['dir'] . DS . $script);
                    if(!JFile::exists($file)) {
                        JError::raiseWarning(100, JText::_('COULD_NOT_FIND_SCRIPT_FILE') . ': ' . $script);
                        $errors = true;
                    } else {
                        include($file);
                    }
                }
            }

            if (!$errors) {
                $this->setState('message', JText::_('COMPONENT_SUCCESSFULLY_UPGRADED'));
            } else {
                $this->setState('message', JText::_('ERROR_UPGRADE_PROBLEM'));
            }
        }

        JFolder::delete($package['dir']);
        return true;
    }

    // Adds a file operation to $fileList
    // $joomlaPath - destination file path (e.g. '/administrator/components/com_sef/admin.sef.php')
    // $operation - can be 'delete' or 'upgrade'
    // $packagePath - source file path in upgrade package if $operation is 'upgrade' (e.g. '/admin.sef.php')
    function _addFileOp($joomlaPath, $operation, $packagePath = '')
    {
        if (!in_array($operation, array('upgrade', 'delete'))) {
            $this->fileError = true;
            JError::raiseWarning(100, JText::_('INVALID_UPGRADE_OPERATION') . ': ' . $operation);
            return false;
        }

        // Do not check if file in package exists - it may be deleted in some future version during upgrade
        // It will be checked before running file operations
        $file = new stdClass();
        $file->operation = $operation;
        $file->packagePath = $packagePath;

        $this->_fileList[$joomlaPath] = $file;
    }

    function _addSQL($sql)
    {
        $this->_sqlList[] = $sql;
    }

    function _addScript($script)
    {
        $this->_scriptList[] = $script;
    }

    function _getPackageFromUpload()
    {
        // Get the uploaded file information
        $userfile = JRequest::getVar('install_package', null, 'files', 'array');

        // Make sure that file uploads are enabled in php
        if (!(bool) ini_get('file_uploads')) {
            JError::raiseWarning(100, JText::_('WARN_INSTALL_FILE'));
            return false;
        }

        // Make sure that zlib is loaded so that the package can be unpacked
        if (!extension_loaded('zlib')) {
            JError::raiseWarning(100, JText::_('WARN_INSTALL_ZLIB'));
            return false;
        }

        // If there is no uploaded file, we have a problem...
        if (!is_array($userfile)) {
            JError::raiseWarning(100, JText::_('NO_FILE_SELECTED'));
            return false;
        }

        // Check if there was a problem uploading the file.
        if ($userfile['error'] || $userfile['size'] < 1){
            JError::raiseWarning(100, JText::_('WARN_INSTALL_UPLOAD_ERROR'));
            return false;
        }

        // Build the appropriate paths
        /*
		$config = &JFactory::getConfig();
        $tmp_dest = $config->get('config.tmp_path') . DS . $userfile['name'];
        */
        $tmp_dest = JFactory::getApplication()->getCfg('tmp_path') . DS . $userfile['name'];
        $tmp_src = $userfile['tmp_name'];

        // Move uploaded file
        jimport('joomla.filesystem.file');
        $uploaded = JFile::upload($tmp_src, $tmp_dest);

        // Unpack the downloaded package file
        $package = JInstallerHelper::unpack($tmp_dest);

        // Delete the package file
        JFile::delete($tmp_dest);

        return $package;
    }

    function _getPackageFromServer($extension)
    {
        // Make sure that zlib is loaded so that the package can be unpacked
        if (!extension_loaded('zlib')) {
            JError::raiseWarning(100, JText::_('WARN_INSTALL_ZLIB'));
            return false;
        }

        // build the appropriate paths
        $params = JComponentHelper::getParams(OPTION);
        
        $artioUserName = JString::trim($params->get('artioUserName'));
        $artioPassword = JString::trim($params->get('artioPassword'));
        $artioDownloadId = JString::trim($params->get('artioDownloadId'));
        
        $config = &JFactory::getConfig();
        
        $tmp_dest = $config->get('tmp_path') . DS . 'com_booking2.zip';
        
        
        // Validate the upgrade on server
        $data = array();
        $data['username'] = $artioUserName;
        $data['password'] = $artioPassword;
        
        $data['download_id'] = $artioDownloadId;
        $data['file'] = 'com_booking2';
        
        $uri = parse_url(JURI::root());
        $url = $uri['host'] . $uri['path'];
        $url = JString::trim($url, '/');
        $data['site'] = $url;
        $data['ip'] = $_SERVER['SERVER_ADDR'];
        $lang = &JFactory::getLanguage();
        $data['lang'] = $lang->getTag();
        $data['cat'] = 'booking2';
        
        // Get the server response
        $response = &$this->post(UPGRADE, JURI::root(), $data);
        
        // Check the response
        if (($response === false) || ($response->code != 200)) {
            JError::raiseWarning(100, JText::_('CONNECTION_TO_SERVER_COULD_NOT_BE_ESTABLISHED'));
            return false;
        }
        
        // Response OK, check what we got
        if(strpos($response->header, 'Content-Type: application/zip') === false) {
            JError::raiseWarning(100, $response->content);
            return false;
        }
        
        // Seems we got the ZIP installation package, let's save it to disk
        if (!JFile::write($tmp_dest, $response->content)) {
            JError::raiseWarning(100, JText::_('UNABLE_TO_SAVE_INSTALLATION_FILE_IN_TEMP_DIRECTORY'));
            return false;
        }

        // Unpack the downloaded package file
        $package = JInstallerHelper::unpack($tmp_dest);

        // Delete the package file
        JFile::delete($tmp_dest);

        return $package;
    }

    function _getXmlText($file, $variable)
    {
        // try to find variable
        $value = null;
        if (JFile::exists($file)) {
            $xml = new SimpleXmlElement($file,null,true);
            
            if ($xml) {
                $element = (string)$xml->$variable;
                $value = $element ? $element : '';
            }
        }

        return $value;
    }

    function post($url, $referer = null, $data = null, $files = null, $options = array())
    {
        if (is_null($referer)) {
            $referer = JURI::root();
        }
        
        $purl = parse_url($url);
        if (! isset($purl['scheme']) || ($purl['scheme'] != 'http')) {
            return false;
        }
        
        $host = $purl['host'];
        $path = isset($purl['path']) ? $purl['path'] : '/';
        
        $errno = 0;
        $errstr = '';
        
        if (!function_exists('fsockopen')) {
        	static $once; // not duplicate message
        	if (empty($once)) {
        		JError::raiseWarning(100, JText::_('FSOCKOPEN_DISABLED'));
        		$once = true;
        	}
        	return null;
        }
        
        $fp = @fsockopen($host, 80, $errno, $errstr, 30);
        if ($fp === false) {
            return false;
        }
        
        $date = JFactory::getDate();
        /* @var $date JDate current locale date */
        $boundary = md5($date->toRFC822());
        
        jimport('joomla.environment.browser');
        $browser = &JBrowser::getInstance();
        /* @var $browser JBrowser */
        
        $head[] = "POST " . $path . " HTTP/1.1\r\n";
        $head[] = "Host: " . $host . "\r\n";
        $head[] = "Referer: " . $referer . "\r\n";
        $head[] = "User-Agent: " . $browser->getAgentString() . "\r\n";
        //$head[] = "Accept: " . $browser->get('accept') . "\r\n";
        $head[] = "Accept-Language:	en-us,en;q=0.5\r\n";
        $head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
        $head[] = "Connection: close\r\n";
        $head[] = "Content-Type: multipart/form-data; boundary=\"" . $boundary . "\"\r\n";
        
        $boundary = '--' . $boundary;
        
        $count = 0;
        
        $this->processData($data, $boundary, $fp, $count, 1);
        $this->processFiles($files, $boundary, $fp, $count, 1);
        
        $end = "\r\n\r\n" . $boundary . "--\r\n";
        
        $count += strlen($end);
        
        $head[] = "Content-Length: " . $count . "\r\n\r\n";
        
        foreach ($head as $row) {
            fputs($fp, $row);
        }
        
        $this->processData($data, $boundary, $fp, $count, 2);
        $this->processFiles($files, $boundary, $fp, $count, 2);
        
        fputs($fp, $end);
        
        $result = '';
        while (! feof($fp)) {
            $result .= fgets($fp, 128);
        }
        
        fclose($fp);
        
        $result = explode("\r\n\r\n", $result, 2);
        
        $header = isset($result[0]) ? $result[0] : '';
        $content = isset($result[1]) ? $result[1] : '';
        
        $response = new stdClass();
        $response->header = $header;
        $response->content = $content;
        
        if (strpos(strtolower($response->header), 'transfer-encoding: chunked') !== false) {
            $parsed = '';
            $left = $response->content;
            
            while (true) {
                $pos = strpos($left, "\r\n");
                if ($pos === false) {
                    return $response;
                }
                
                $chunksize = substr($left, 0, $pos);
                $pos += strlen("\r\n");
                $left = substr($left, $pos);
                
                $pos = strpos($chunksize, ';');
                if ($pos !== false) {
                    $chunksize = substr($chunksize, 0, $pos);
                }
                $chunksize = hexdec($chunksize);
                
                if ($chunksize == 0) {
                    break;
                }
                
                $parsed .= substr($left, 0, $chunksize);
                $left = substr($left, $chunksize + strlen("\r\n"));
            }
            
            $response->content = $parsed;
        }
        
        $headerLines = explode("\n", $response->header);
        $header1 = explode(' ', JString::trim($headerLines[0]));
        $code = intval($header1[1]);
        $response->code = $code;
        
        return $response;
    }

    function processData($data, $boundary, &$handle, &$count, $mode)
    {
        if (is_array($data)) {
            foreach ($data as $param => $value) {
                $this->process($boundary . "\r\n", $handle, $count, $mode);
                $this->process("Content-Disposition: form-data; name=\"" . $param . "\"\r\n\r\n", $handle, $count, $mode);
                $this->process($value . "\r\n", $handle, $count, $mode);
            
            }
        }
    }

    function processFiles($files, $boundary, &$handle, &$count, $mode)
    {
        if (is_array($files)) {
            foreach ($files as $filename => $filepath) {
                $this->process($boundary . "\r\n", $handle, $count, $mode);
                $this->process("Content-Disposition: form-data; name=\"file\"; filename=\"" . $filename . "\"\r\n", $handle, $count, $mode);
                $this->process("Content-Type: text/csv\r\n\r\n", $handle, $count, $mode);
                $fhandle = fopen($filepath, 'r');
                if ($fhandle !== false) {
                    while (! feof($fhandle)) {
                        $this->process(fread($fhandle, 1024), $handle, $count, $mode);
                    }
                }
            }
        }
    }

    function process($string, &$handle, &$count, $mode)
    {
        switch ($mode) {
            case 1:
                $count += strlen($string);
                break;
            case 2:
                fputs($handle, $string);
                break;
        }
    }
}
?>