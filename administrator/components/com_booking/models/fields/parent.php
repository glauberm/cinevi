<?php

/**
 * @version	$Id$
 * @package	ARTIO Booking
 * @subpackage	models/fields
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license   	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('JPATH_BASE') or die;

JLoader::import('components.com_booking.defines', JPATH_ADMINISTRATOR);
JLoader::import('components.com_booking.helpers.importer', JPATH_ADMINISTRATOR);
JLoader::import('components.com_booking.helpers.booking', JPATH_ADMINISTRATOR);

class JFormFieldParent extends JFormField
{
	protected $type = 'Parent';

	protected function getInput()
	{
		$model = BookingHelper::getSubjectsModel();
        $parents = $model->loadParents();
        $parents = $model->loadShortListByIds($parents);
        return BookingHelper::getSubjectParentSelectBox($this->name, 'ALL_CATEGORIES', $parents, array(), $this->value, false);
	}
}