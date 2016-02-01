<?php

/**
 * Defined component routes
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class ALog
{
	const DEFAULT_CATEGORY = 'booking';
	
	/**
	 * The global ALog instance.
	 * @var    ALog
	 */
	protected static $instance;
	
	/**
	 * Returns a reference to the a ALog object, only creating it if it doesn't already exist.
	 * Note: This is principally made available for testing and internal purposes.
	 *
	 * @param   ALog  $instance  The logging object instance to be used by the static methods.
	 * @return  void
	 */
	public static function setInstance($instance)
	{
		if (($instance instanceof ALog) || $instance === null)
		{
			self::$instance = & $instance;
		}
	}
	
	public static function init($debug = true)
	{
		// Automatically instantiate the singleton object if not already done.
		/*
		if (empty(self::$instance))
		{
			self::setInstance(new ALog);
		}
		*/
		
		$categories = array(self::DEFAULT_CATEGORY);
		
		JLog::addLogger(array('text_file' => 'booking_emergency.php'),JLog::EMERGENCY,$categories);
		
		if($debug)
		{		
			// log every type of exception ind different file.
			JLog::addLogger(array('text_file' => 'booking_alert.php'),JLog::ALERT,$categories);
			JLog::addLogger(array('text_file' => 'booking_critical.php'),JLog::CRITICAL,$categories);
			
			JLog::addLogger(array('text_file' => 'booking_error.php'),JLog::ERROR,$categories);
			
			//JLog::addLogger(array('text_file' => 'booking_debug.php'),JLog::DEBUG,$categories);
			//JLog::addLogger(array('text_file' => 'booking_info.php'),JLog::INFO,$categories);
			//JLog::addLogger(array('text_file' => 'booking_notice.php'),JLog::NOTICE,$categories);
			//JLog::addLogger(array('text_file' => 'booking_warning.php'),JLog::WARNING,$categories);
			
			//only for deprecated
			//JLog::addLogger(array('text_file' => 'booking_deprecated.php'),JLog::ALL,array('deprecated'));
		}
	}
	
	/**
	 * Method to add an exception message to the log with short debug.
	 *
	 * @param   Exception    $e
	 * @param   integer  $priority  Message priority.
	 * @param   int   $limit  number of showed
	 *
	 * @return  void
	 */
	public static function addException(Exception $e, $priority = JLog::ERROR, $limit = 2)
	{ 	
		static $logged;
		if( !isset($logged) )
			$logged = array();
		
    	//memory usage improve
    	if (version_compare(phpversion(), "5.4.0", ">="))
    		$deb = debug_backtrace(false, $limit);
    	else
    		$deb = debug_backtrace(false);
    	 
    	$message = $e->getMessage().' :|: ';
    	//create hash for error message and priority 
    	$hash = md5($message.$priority);
    	
    	//gal last n function calls from debug
    	for($i = $limit; $i > 0; $i--)
    	{
    		if($i != $limit)
    			$message .=	' -> ';
	    	if(array_key_exists($i,$deb))
	    		$message .= $deb[$i]['file'].':: '.$deb[$i]['line'].':'.$deb[$i]['function'].'()';
	    }
    	
	    //if message is new, log it
	    if(!array_key_exists($hash,$logged))
	    {
	    	$logged[$hash] = true;
	    	ALog::add($message, $priority);
	    }
    }
    
    /**
     * Method to add an entry to the log.
     *
     * @param   mixed    $entry     The JLogEntry object to add to the log or the message for a new JLogEntry object.
     * @param   integer  $priority  Message priority.
     * @param   string   $category  Type of entry
     * @param   string   $date      Date of entry (defaults to now if not specified or blank)
     *
     * @return  void
     */
    public static function add($entry, $priority = JLog::ERROR, $category = ALog::DEFAULT_CATEGORY, $date = null)
    {
    	JLog::add($entry, $priority, $category, $date);
    }
}

?>