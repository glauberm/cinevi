<?php

/**
 * Popup element to select customer.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  elements
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal', 'a.modal');

class JElementCustomer extends JFormField
{

	static $idvalue;
    /**
     * Display button to open popup window. 
     * 
     * @param int $value
     */
    function getInput()
    {
    	$value = self::$idvalue;
        $customerModel = new BookingModelCustomer();
        $customerModel->setId($value);
        $customer = $customerModel->getObject();
        $html = '<span class="input-append">';
        $html .= '<input type="text" id="customer_name" value="' . BookingHelper::formatName($customer, true) . '" disabled="disabled" />';
        $html .= '<input type="button" class="btn" onclick="SqueezeBox.fromElement(this, {handler: \'iframe\', size: {x: 800, y: 600}, url: \'' . ARoute::browse(CONTROLLER_CUSTOMER, true) . '\'})" value="' . JText::_('SELECT') . '" title="' . JText::_('SELECT_A_CUSTOMER') . '">';
        $html .= '</span>';
        $html .= '<input type="hidden" id="customer_id" name="customer" value="' . $value . '" />';
        return $html;
    }
    
    function fetchElement($value)
    {
    	self::$idvalue = $value;
    	return self::getInput();
    }
}