<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	views
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

/* @var $this BookingViewClosingday */

defined('_JEXEC') or die;

?>
<style type="text/css">
    .com_booking_closing_days label {
        padding: 0 10px 0 5px;
    }
    .com_booking_closing_days table {
        margin: 0 0 10px;
    }
    .com_booking_closing_days table td {
        border: medium none;
        padding: 2px 0;
    }
    .com_booking_closing_days table tr {
        border: medium none;
    }
    .timePickerDiv div {
        height: 30px;
        width: 100px;
    }
    #week_days label {
        clear: none;
        display: inline-block;
        margin: 0;
        min-width: 0;
        padding: 0 15px 0 0;
    }
    #week_days input {
        margin: 0 5px 0 0;
    }
</style>
<form action="<?php echo JRoute::_('index.php?option=com_booking&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate com_booking_closing_days">
    <?php if (IS_SITE) { ?>
        <div class="btn-group pull-right hidden-phone formelm-buttons">
            <button type="button" onclick="Joomla.submitbutton('closingday.save')" class="btn btn-success">
                <?php echo JText::_('JSAVE') ?>
            </button>
            <button type="button" onclick="Joomla.submitbutton('closingday.apply')" class="btn btn-primary">
                <?php echo JText::_('JAPPLY') ?>
            </button>
            <button type="button" onclick="Joomla.submitbutton('closingday.cancel')" class="btn btn-danger">
                <?php echo JText::_('JCANCEL') ?>
            </button>    		
        </div>
        <div class="clr"></div>
        <?php
    }
    echo JHtml::_('tabs.start');
    echo JHtml::_('tabs.panel', JText::_('DETAILS'), 'com_booking_closing_days_details');
    ?>
    <table>
        <tr>
            <td><?php echo $this->form->getLabel('title'); ?></td>
            <td><?php echo $this->form->getInput('title'); ?></td>
        </tr>
        <?php if (IS_ADMIN || $this->params->get('edit_color')) { ?>
            <tr>
                <td><?php echo $this->form->getLabel('color'); ?></td>
                <td><?php echo $this->form->getInput('color'); ?></td>
            </tr>
            <?php
        }
        if (IS_ADMIN || $this->params->get('edit_show')) {
            ?>    				
            <tr>
                <td><?php echo $this->form->getLabel('show'); ?></td>
                <td><?php echo $this->form->getInput('show'); ?></td>
            </tr>
        <?php } ?>				
        <tr>
            <td><?php echo $this->form->getLabel('date_up'); ?></td>
            <td><?php echo $this->form->getInput('date_up'); ?></td>
        </tr>
        <tr>
            <td><?php echo $this->form->getLabel('date_down'); ?></td>
            <td><?php echo $this->form->getInput('date_down'); ?></td>
        </tr>
        <tr>
            <td><?php echo $this->form->getLabel('time_up'); ?></td>
            <td><?php echo $this->form->getInput('time_up'); ?></td>
        </tr>
        <tr>
            <td><?php echo $this->form->getLabel('time_down'); ?></td>
            <td><?php echo $this->form->getInput('time_down'); ?></td>
        </tr>
        <tr>
            <td><label><?php echo JText::_('CLOSED_WEEK_DAYS'); ?></label></td>
            <td id="week_days">
                <input type="hidden" value="0" name="jform[monday]" />
                <input type="hidden" value="0" name="jform[tuesday]" />
                <input type="hidden" value="0" name="jform[wednesday]" />
                <input type="hidden" value="0" name="jform[thursday]" />
                <input type="hidden" value="0" name="jform[friday]" />
                <input type="hidden" value="0" name="jform[saturday]" />
                <input type="hidden" value="0" name="jform[sunday]" />
                <?php 
                echo $this->form->getInput('monday');                 
                echo $this->form->getLabel('monday'); 
                echo $this->form->getInput('tuesday');
                echo $this->form->getLabel('tuesday'); 
                echo $this->form->getInput('wednesday'); 
                echo $this->form->getLabel('wednesday'); 
                echo $this->form->getInput('thursday'); 
                echo $this->form->getLabel('thursday'); 
                echo $this->form->getInput('friday'); 
                echo $this->form->getLabel('friday'); 
                echo $this->form->getInput('saturday');                 
                echo $this->form->getLabel('saturday');                                 
                echo $this->form->getInput('sunday');                 
                echo $this->form->getLabel('sunday');                                                 
                ?>
            </td>
        </tr>                
    </table>
    <div class="clr"></div>
    <?php echo $this->form->getLabel('text'); ?>
    <div class="clr"></div>
    <?php
    echo $this->form->getInput('text');
    $onlySubject = count($this->subjects) < 2;
    echo JHtml::_('tabs.panel', JText::_('AFFECTED_ITEMS'), 'com_booking_closing_days_items');
    ?>
    <table>
        <?php if (!$onlySubject) { ?>
            <tr>
                <td><input type="checkbox" onclick="Joomla.checkAll(this)" title="<?php echo JText::_('JGLOBAL_CHECK_ALL', true); ?>" value="" name="checkall-toggle" id="checkAll"></td>
                <td><label for="checkAll"><strong><?php echo JText::_('CHECK_ALL'); ?></strong></label></td>
            </tr>
            <?php
        }
        foreach ($this->subjects as $i => $subject) {
            ?>
            <tr>
                <td>
                    <input type="checkbox" name="item[]" id="cb<?php echo $i; ?>" value="<?php echo $subject->id; ?>" <?php if ($subject->affected || $onlySubject) { ?>checked="checked"<?php } if ($onlySubject) { ?>readonly="readonly"<?php } ?> />
                </td>
                <td>
                    <label for="cb<?php echo $i; ?>"><?php echo!$onlySubject ? $subject->treename : $subject->title; ?></label>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php echo JHtml::_('tabs.end'); ?>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="task" value="" /> 
    <input type="hidden" name="return" value="<?php echo JRequest::getCmd('return'); ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>
<div class="clr"></div>