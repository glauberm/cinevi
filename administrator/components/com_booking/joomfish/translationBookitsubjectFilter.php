<?php

/**
 * Display filter for Booking objects.
 * Used in Booking reservation types and supplements translating.
 * Latest testing with JoomFISH 2.2.3.
 *
 * Usage:
 *
 *  <?xml version="1.0" ?>
 *  <joomfish type="contentelement">
 *    <translationfilters>
 * 	   <bookitsubject>subject</bookitsubject>
 *    </translationfilters>
 *  </joomfish>
 *
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

    /**
     * Compatibility with newer JoomFISH.
     */
    function __construct($contentElement)
    {
        $this->translationBookitsubjectFilter($contentElement);
        parent::__construct($contentElement);
    }

    /**
     * Compatibility with older JoomFISH.
     */
    function translationBookitsubjectFilter($contentElement)
    {
        $importer = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booking' . DS . 'helpers' . DS . 'importer.php';
        if (file_exists($importer)) {
            include_once($importer);
            AImporter::defines();
            AImporter::helper('booking', 'tree', 'html');
            AImporter::table('subject');
        } else {
            $mainframe =& JFactory::getApplication();
            /* @var $mainframe JAdministrator */
            $mainframe->enqueueMessage('Booking component propably not installed', 'notice');
        }
        $this->filterNullValue = 0;
        $this->filterType = $this->filterName;
        $this->filterField = $contentElement->getFilter($this->filterName);
        if (method_exists($this, 'translationFilter'))
            // compatibility with older JoomFISH.
            parent::translationFilter($contentElement);
    }

    /**
     * Compatibility with older JoomFISH.
     */
    function _createfilterHTML()
    {
        if (class_exists('BookingHelper') && $this->filterField) {
            $filter['title'] = JText::_('OBJECTS_FILTER');
            $filter['html'] = BookingHelper::getSubjectSelectBox($this->filter_value, $this->filterName . '_filter_value', true);
            return $filter;
        }
        return '';
    }

    /**
     * Compatibility with newer JoomFISH.
     */
    function createFilterHTML()
    {
        return $this->_createfilterHTML();
    }
}
?>