<?php

/**
 * View page to display and editing extra fileds of customer registration.
 *
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) 2012 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

AImporter::js('view-fields');
AImporter::css('view-fields');



if (ISJ3)
    JHtml::_('formbehavior.chosen', 'select');

class BookingViewFields extends JViewLegacy
{
	/**
	 * Name of paramerer in component configuration.
	 * @var string
	 */
    var $name;
    /**
	 * ID of paramerer in component configuration.
	 * @var string
	 */
    var $fid;
    /**
     * ID of selected field to edit.
     * @var int
     */
    var $id;
    /**
     * Array od IDs of selected fields to remove.
     * @var array
     */
    var $cid;
    /**
     * Completed array of fields as serialize string.
     * @var string
     */
    var $value;
    /**
     * Field to edit.
     * @var array
     */
    var $field;
    /**
     * Array of fields to display.
     * @var array
     */
    var $fields;

    function display($tpl = null)
    {
        $mainframe = JFactory::getApplication();
        /* @var $mainframe JApplication */

        $this->templates = AFactory::getTemplateHelper()->_templates; 

        $this->name = $mainframe->getUserState('com_booking.fields.name');
        $this->fid = $mainframe->getUserState('com_booking.fields.id');
        $this->value = unserialize($mainframe->getUserState('com_booking.fields.value'));

        if (!is_array($this->value)) // first time
            $this->value = array();

        // editing or removing fields    
        $this->cid = JRequest::getVar('cid', array(JRequest::getInt('id', -1)), 'default', 'array');
        $this->id = reset($this->cid);

        if (JRequest::getString('op') == 'add') { // new field
            $this->field = array('title' => '', 'required' => '', 'template' => array(), 'type' => 'text', 'options' => '', 'special' => 0);
        }

        if (JRequest::getString('op') == 'edit') { // edit existing field
            $this->field = $this->value[$this->id];
            if (!isset($this->field['template']))
                $this->field['template'] = array();
            if (!isset($this->field['type']))
                $this->field['type'] = 'text';
            if (!isset($this->field['options']))
                $this->field['options'] = '';      
            else          
              $this->field['options'] = json_decode($this->field['options']);
            if (!isset($this->field['special']))
                $this->field['special'] = 0;      
        }

        if (JRequest::getString('op') == 'save') { // save field
            $data = array('title' => JRequest::getString('title'), 'required' => JRequest::getInt('required'), 'template' => JRequest::getVar('template', array(), 'default', 'array'), 'type' => JRequest::getString('type'), 'options' => json_encode(JRequest::getString('options')), 'special' => JRequest::getInt('special'));
            $this->id > -1 ? $this->value[$this->id] = $data : $this->value[] = $data;
        }

        if (JRequest::getString('op') == 'remove') { // remove fields
            foreach ($this->cid as $id) {
                unset($this->value[$id]);
            }
            $this->value = array_merge($this->value); // reindexing
        }

        $this->types[] = JHtml::_('select.option', 'text', JText::_('FIELD_STRING'));
        $this->types[] = JHtml::_('select.option', 'radio', JText::_('FIELD_RADIO'));
        $this->types[] = JHtml::_('select.option', 'select', JText::_('FIELD_SELECT'));
        
        $this->fields = $this->value;
        $this->value = serialize($this->value); // back to dataabse format
        $mainframe->setUserState('com_booking.fields.value', $this->value);

        parent::display($tpl);
    }
}
?>