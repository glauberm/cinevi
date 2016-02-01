<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking - Joofish content elements 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class translationBookitsubjectFilter extends translationFilter
{
    var $filterName = 'bookitsubject';

    function translationBookitsubjectFilter($contentElement)
    {
        $importer = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booking' . DS . 'helpers' . DS . 'importer.php';
        if (file_exists($importer)) {
            include_once ($importer);
            AImporter::defines();
            AImporter::helper('booking', 'tree', 'html');
            AImporter::table('subject');
        } else {
            $mainframe = &JFactory::getApplication();
            /* @var $mainframe JAdministrator */
            $mainframe->enqueueMessage('Booking component propably not installed', 'notice');
        }
        $this->filterNullValue = 0;
        $this->filterType = $this->filterName;
        $this->filterField = $contentElement->getFilter($this->filterName);
        parent::translationFilter($contentElement);
    }

    function _createfilterHTML()
    {
        if (class_exists('BookingHelper') && $this->filterField) {
            $filter['title'] = JText::_('OBJECTS_FILTER');
            $filter['html'] = BookingHelper::getSubjectSelectBox($this->filter_value, $this->filterName . '_filter_value', true);
            $filter['html'] = str_replace(array('&lt;sup&gt;', '&lt;/sup&gt;'), '', $filter['html']);
            return $filter;
        }
        return '';
    }
}

?>