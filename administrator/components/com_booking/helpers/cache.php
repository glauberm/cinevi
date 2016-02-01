<?php

/**
 * working with cache
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class ACache
{
	
	/**
	 * delete all page caching
	 */
	public static function cleanAll()
	{
		$conf = JFactory::getConfig();

		$options = array(
				'defaultgroup'	=> '',
				'storage' 		=> $conf->get('cache_handler', ''),
				'caching'		=> true,
				'cachebase'		=> (IS_ADMIN)? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache')
		);

		$cache = JCache::getInstance('', $options);
		if(!$cache->clean())
			ALog::add('ACache: can not clear cache');
	}
}

?>