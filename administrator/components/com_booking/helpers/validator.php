<?php

/**
 * Support for data validation.
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class AHelperValidator
{
    function isEmails ($text)
    {
        jimport('joomla.mail.helper');
        $text = JString::trim($text);
        if (! empty($text)) {
            $workEmail = str_replace(';', ',', $text);
            $parts = explode(',', $workEmail);
            foreach ($parts as $part) {
                $part = JString::trim($part);
                if (! empty($part) && ! JMailHelper::isEmailAddress($part)) {
                    return false;
                }
            }
        }
        return true;
    }
}

?>