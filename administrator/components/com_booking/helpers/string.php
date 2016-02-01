<?php

/**
 * Support for strings.
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

class AString
{

    function getSafe($string)
    {
        $chTable = array('ä' => 'a' , 'Ä' => 'A' , 'á' => 'a' , 'Á' => 'A' , 'č' => 'c' , 'Č' => 'C' , 'ć' => 'c' , 'Ć' => 'C' , 'ď' => 'd' , 'Ď' => 'D' , 'ě' => 'e' , 'Ě' => 'E' , 'é' => 'e' , 'É' => 'E' , 'ë' => 'e' , 'Ë' => 'E' , 'í' => 'i' , 'Í' => 'I' , 'ľ' => 'l' , 'Ľ' => 'L' , 'ń' => 'n' , 'Ń' => 'N' , 'ň' => 'n' , 'Ň' => 'N' , 'ó' => 'o' , 'Ó' => 'O' , 'ö' => 'o' , 'Ö' => 'O' , 'ř' => 'r' , 'Ř' => 'R' , 'ŕ' => 'r' , 'Ŕ' => 'R' , 'š' => 's' , 'Š' => 'S' , 'ś' => 's' , 'Ś' => 'S' , 'ť' => 't' , 'Ť' => 'T' , 'ů' => 'u' , 'Ů' => 'U' , 'ú' => 'u' , 'Ú' => 'U' , 'ü' => 'u' , 'Ü' => 'U' , 'ý' => 'y' , 'Ý' => 'Y' , 'ž' => 'z' , 'Ž' => 'Z' , 'ź' => 'z' , 'Ź' => 'Z');
        $string = strtr($string, $chTable);
        $string = str_replace('-', ' ', $string);
        $string = preg_replace(array('/\s+/' , '/[^A-Za-z0-9\-]/'), array('-' , ''), $string);
        $string = JString::strtolower($string);
        $string = JString::trim($string);
        return $string;
    }
}

?>