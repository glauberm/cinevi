<?php

/**
 * Aditional utilities.
 *
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @copyright	Copyright (C) 2012 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

class AUtils
{
    /**
     * Get value from array structure recursive into the deepth.
     * @param array $itm multi array, E.G. array('a' => array('a1' => 1, 'a2' => 2), 'b' => array('b1' => 1, 'b2' => 2))
     * @param array $key keys from every level separated by dot E.G. a.a1 or b.b2
     * @param array $def default value if nothing found
     * @return mixed last found level from array structure or default
     */
    function getArrayValue($itm, $key, $def = null)
    {
        foreach (explode('.', $key) as $key) {
            if (isset($itm[$key]))
                // go down to the next level
                $itm = $itm[$key];
            else
                // nothing found, return default
                return $def;
        }
        return $itm;
    }
    /**
     * Get value from object structure recursive into the deepth.
     * @param stdClass $itm multi object, E.G. object(a -> object(a1 -> 1, a2 -> 2), b -> object(b1 -> 1, b2 -> 2))
     * @param array $key keys from every level separated by dot E.G. a.a1 or b.b2
     * @param array $def default value if nothing found
     * @return mixed last found level from object structure or default
     */
    function getObjectValue($itm, $key, $def = null)
    {
        foreach (explode('.', $key) as $key) {
            if (isset($itm->$key))
                // go down to the next level
                $itm = $itm->$key;
            else
                // nothing found, return default
                return $def;
        }
        return $itm;
    }
    
    /**
     * Get phone number in local format with code. 
     * @param string $phone
     * @return string
     */
    function getLocalPhone($phone)
    {
    	$config = AFactory::getConfig();
    	if ($config->smsLocalNumberCode && $config->smsLocalNumber) {
    		$phone = str_replace(' ', '', $phone);
    		$phone = JString::ltrim($phone, '+0');
    		if (JString::strlen($phone) <= $config->smsLocalNumber)
    			$phone = $config->smsLocalNumberCode . $phone;
    	}
    	return $phone;
    }
    
    /**
     * Get sub-array of multidimensional array
     * @param array $array
     * @param string $key
     * @return array
     */
    public static function getSubArray($array, $key)
    {
        $subArray = array();
        if (is_array($array)) {
            foreach ($array as $item) {
                if (isset($item[$key]) && is_array($item[$key])) {
                    $subArray = array_merge($subArray, $item[$key]);
                }
            }
        }
        return $subArray;
    }
 
    public static function getCmpTypes()
    {
        return array(1 => '=', 2 => '>=', 3 => '<=');
    }
}
?>