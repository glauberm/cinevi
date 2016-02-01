<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	elements
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class JFormFieldLocalLink extends JFormField
{

	public $type = 'LocalLink';
	

	/**
	 * (non-PHPdoc)
	 * @see JFormField::getInput()
	 */
	protected function getInput()
	{
		return '<label><a href="' . JUri::root() . $this->element['link'] . '" target="_blank">' . JUri::root() . $this->element['link'] . '</a></label>';
	}
}