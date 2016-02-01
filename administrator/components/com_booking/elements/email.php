<?php

/**
 * @version	$Id$
 * @package	ARTIO Booking
 * @subpackage	elements
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license   	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class JFormFieldEmail extends JFormFieldList
{
	protected $type = 'Email';
	
    /**
     * (non-PHPdoc)
     * @see JFormFieldList::getOptions()
     */
    protected function getOptions()
    {
 		$db = JFactory::getDbo();
 		$query = $db->getQuery(true);
 		$options = array(JHtml::_('select.option', '', '- ' . JText::_('JNONE') . ' -'));

 		$query->select('id, subject, `usage`')->from('#__booking_email')->order('`subject`');
 		
 		foreach ($db->setQuery($query)->loadObjectList() as $email) {
 			if ($email->usage == 0)
 				$options[] = JHtml::_('select.option', $email->id, $email->subject . ' (' . JText::_('EMAIL_ONLY') . ')');
 			elseif ($email->usage == 1)
 				$options[] = JHtml::_('select.option', $email->id, $email->subject . ' (' . JText::_('SMS_ONLY') . ')');
 			elseif ($email->usage == 2)
 				$options[] = JHtml::_('select.option', $email->id, $email->subject . ' (' . JText::_('EMAIL_AND_SMS') . ')');
 		}
 		
 		return $options;
    }
}