<?php

/**
 * Support for create JoomLIB objects.
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

class AFactory
{

    /**
     * Create template helper object
     * 
     * @return ATemplateHelper
     */
    function getTemplateHelper()
    {
        static $instance;
        if (empty($instance)) {
            AImporter::helper('template');
            $instance = new ATemplateHelper();
        }
        return $instance;
    }

    /**
     * Create config helper object
     * 
     * @return BookingConfig
     */
    function getConfig()
    {
        static $instance;
        if (empty($instance)) {
            AImporter::helper('config', 'parameter');
            $instance = new BookingConfig();
        }
        return $instance;
    }
}

?>